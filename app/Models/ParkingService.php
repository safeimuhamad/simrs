<?php

class ParkingService
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function checkIn($data)
    {
        $plate = strtoupper(trim($data['plate_number'] ?? ''));
        $member = (new ParkingMember())->findActiveByPlate($plate);
        $vehicleType = (new ParkingVehicleType())->find($data['vehicle_type_id']);
        $visit = !empty($data['patient_visit_id']) ? (new PatientVisit())->withRelations($data['patient_visit_id']) : null;
        $entryTime = str_replace('T', ' ', (string) ($data['entry_time'] ?: date('Y-m-d H:i:s')));

        $ticketId = (new ParkingTicket())->create([
            'ticket_no' => SimrsNumber::make('parking_tickets', 'ticket_no', 'PRK'),
            'qr_token' => bin2hex(random_bytes(16)),
            'plate_number' => $plate,
            'vehicle_type_id' => $data['vehicle_type_id'],
            'area_id' => $data['area_id'] ?: null,
            'entry_gate_id' => $data['entry_gate_id'] ?: null,
            'patient_visit_id' => ($data['patient_visit_id'] ?? null) ?: null,
            'patient_id' => $visit['patient_id'] ?? (($data['patient_id'] ?? null) ?: null),
            'member_id' => $member['id'] ?? null,
            'entry_time' => $entryTime,
            'payment_status' => (!empty($member) || !empty($vehicleType['is_free'])) ? 'waived' : 'unpaid',
            'source' => $data['source'] ?? 'manual',
            'notes' => $data['notes'] ?? '',
            'created_by' => $_SESSION['user_id'] ?? null,
        ]);

        if (!empty($data['entry_gate_id'])) {
            (new ParkingGateService())->openGate($data['entry_gate_id'], $ticketId, 'open_entry');
        }

        activity_log('Parking - Check In', 'create', 'Kendaraan masuk: ' . $plate, $ticketId, $plate);
        return $ticketId;
    }

    public function calculate($ticketId, $exitTime = null, $lostTicket = false)
    {
        $ticket = (new ParkingTicket())->detail($ticketId);
        if (!$ticket) {
            throw new RuntimeException('Tiket parkir tidak ditemukan.');
        }

        $exitTime = $exitTime ?: date('Y-m-d H:i:s');
        $duration = max(0, (int) ceil((strtotime($exitTime) - strtotime($ticket['entry_time'])) / 60));
        $vehicleType = (new ParkingVehicleType())->find($ticket['vehicle_type_id']);
        $rate = (new ParkingRate())->activeByVehicleType($ticket['vehicle_type_id']);

        if (!empty($vehicleType['is_free']) || !empty($ticket['member_id'])) {
            return ['duration_minutes' => $duration, 'calculated_amount' => 0, 'discount_amount' => 0, 'payable_amount' => 0];
        }

        if (!$rate) {
            return ['duration_minutes' => $duration, 'calculated_amount' => 0, 'discount_amount' => 0, 'payable_amount' => 0];
        }

        if ($duration <= (int) $rate['grace_minutes']) {
            $amount = 0;
        } elseif ($lostTicket) {
            $amount = (float) $rate['lost_ticket_fee'];
        } elseif ($rate['rate_type'] === 'flat') {
            $amount = (float) $rate['initial_rate'];
        } else {
            $remainingMinutes = max(0, $duration - (int) $rate['initial_minutes']);
            $nextHours = (int) ceil($remainingMinutes / 60);
            $amount = (float) $rate['initial_rate'] + ($nextHours * (float) $rate['next_hour_rate']);

            if ($rate['rate_type'] === 'progressive') {
                $amount += max(0, $nextHours - 1) * (float) $rate['progressive_rate'];
            }

            if ((float) $rate['max_daily_rate'] > 0) {
                $days = max(1, (int) ceil($duration / 1440));
                $amount = min($amount, $days * (float) $rate['max_daily_rate']);
            }
        }

        $discount = $this->validationDiscount($ticketId, $amount, $rate);

        return [
            'duration_minutes' => $duration,
            'calculated_amount' => $amount,
            'discount_amount' => $discount,
            'payable_amount' => max(0, $amount - $discount),
        ];
    }

    public function checkout($ticketId, $data)
    {
        $exitTime = str_replace('T', ' ', (string) ($data['exit_time'] ?: date('Y-m-d H:i:s')));
        $lostTicket = !empty($data['lost_ticket']);
        $calc = $this->calculate($ticketId, $exitTime, $lostTicket);
        $status = $lostTicket ? 'lost_ticket' : ($calc['payable_amount'] > 0 ? 'unpaid' : 'paid');
        $paymentStatus = $calc['payable_amount'] > 0 ? 'unpaid' : 'waived';

        (new ParkingTicket())->update($ticketId, [
            'exit_gate_id' => $data['exit_gate_id'] ?: null,
            'exit_time' => $exitTime,
            'duration_minutes' => $calc['duration_minutes'],
            'calculated_amount' => $calc['calculated_amount'],
            'discount_amount' => $calc['discount_amount'],
            'payable_amount' => $calc['payable_amount'],
            'status' => $status,
            'payment_status' => $paymentStatus,
            'checked_out_by' => $_SESSION['user_id'] ?? null,
        ]);

        if (!empty($data['exit_gate_id'])) {
            (new ParkingGateService())->openGate($data['exit_gate_id'], $ticketId, 'open_exit');
        }

        if ($calc['payable_amount'] <= 0) {
            $this->recordPayment($ticketId, ['amount' => 0, 'payment_method' => 'waived', 'status' => 'waived', 'notes' => 'Gratis/validasi/member']);
        }

        activity_log('Parking - Check Out', 'checkout', 'Kendaraan keluar tiket #' . $ticketId, $ticketId);
        return $calc;
    }

    public function recordPayment($ticketId, $data)
    {
        $ticket = (new ParkingTicket())->find($ticketId);
        if (!$ticket) {
            throw new RuntimeException('Tiket parkir tidak ditemukan.');
        }

        $amount = (float) ($data['amount'] ?? $ticket['payable_amount']);
        $method = $data['payment_method'] ?? 'cash';
        $status = $data['status'] ?? 'paid';

        $paymentId = (new ParkingPayment())->create([
            'payment_no' => SimrsNumber::make('parking_payments', 'payment_no', 'PPAY'),
            'ticket_id' => $ticketId,
            'patient_billing_id' => $data['patient_billing_id'] ?? null,
            'bank_account_id' => ($data['bank_account_id'] ?? null) ?: null,
            'payment_date' => $data['payment_date'] ?? date('Y-m-d'),
            'payment_method' => $method,
            'amount' => $amount,
            'status' => $status,
            'reference_no' => $data['reference_no'] ?? '',
            'notes' => $data['notes'] ?? '',
            'created_by' => $_SESSION['user_id'] ?? null,
        ]);

        if ($method === 'patient_billing' && !empty($ticket['patient_visit_id'])) {
            $billingModel = new PatientBilling();
            $billingId = $billingModel->ensureForVisit($ticket['patient_visit_id']);
            $billingModel->addItem($billingId, [
                'reference_type' => 'parking',
                'reference_id' => $ticketId,
                'item_name' => 'Parkir - ' . $ticket['plate_number'],
                'quantity' => 1,
                'unit_price' => $amount,
            ]);
            $billingModel->recalculate($billingId, 'unpaid');
            (new ParkingPayment())->update($paymentId, ['patient_billing_id' => $billingId]);
        }

        if (!empty($data['bank_account_id']) && $amount > 0 && class_exists('BankTransaction')) {
            (new BankTransaction())->create([
                'bank_account_id' => $data['bank_account_id'],
                'transaction_date' => $data['payment_date'] ?? date('Y-m-d'),
                'transaction_type' => 'in',
                'reference_type' => 'parking_payment',
                'reference_id' => $paymentId,
                'description' => 'Pendapatan parkir ' . $ticket['ticket_no'],
                'amount' => $amount,
            ]);
            (new BankAccount())->increaseBalance($data['bank_account_id'], $amount);
        }

        (new ParkingTicket())->update($ticketId, ['status' => 'paid', 'payment_status' => $status === 'waived' ? 'waived' : 'paid']);
        activity_log('Parking - Payment', 'payment', 'Pembayaran parkir dicatat', $paymentId, $ticket['ticket_no']);
        return $paymentId;
    }

    private function validationDiscount($ticketId, $amount, $rate)
    {
        $validations = (new ParkingValidation())->byTicket($ticketId);
        if (empty($validations)) {
            return 0;
        }

        $validation = $validations[0];
        return match ($validation['discount_type']) {
            'free' => $amount,
            'percent' => $amount * ((float) $validation['discount_value'] / 100),
            'amount' => (float) $validation['discount_value'],
            'special_rate' => max(0, $amount - (float) $validation['discount_value']),
            default => 0,
        };
    }
}

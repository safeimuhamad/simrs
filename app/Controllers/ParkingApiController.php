<?php

class ParkingApiController extends Controller
{
    public function lprEntry()
    {
        $this->requirePostJson();
        $data = $this->requestData();
        $plate = strtoupper(trim($data['plate_number'] ?? ''));

        if ($plate === '') {
            $this->json(['success' => false, 'message' => 'plate_number wajib diisi.'], 422);
        }

        $ticketModel = new ParkingTicket();
        $existing = $ticketModel->activeByPlate($plate);
        if ($existing) {
            $this->json([
                'success' => true,
                'message' => 'Kendaraan masih memiliki tiket aktif.',
                'action' => 'existing_ticket',
                'gate_opened' => false,
                'ticket' => $this->ticketPayload($existing),
            ]);
        }

        $defaults = $this->defaults((int) ($data['gate_id'] ?? 0), ['entry', 'both']);
        $ticketId = (new ParkingService())->checkIn([
            'plate_number' => $plate,
            'vehicle_type_id' => $data['vehicle_type_id'] ?? ($defaults['vehicleType']['id'] ?? null),
            'area_id' => $data['area_id'] ?? ($defaults['area']['id'] ?? null),
            'entry_gate_id' => $data['gate_id'] ?? ($defaults['gate']['id'] ?? null),
            'entry_time' => $data['entry_time'] ?? date('Y-m-d H:i:s'),
            'source' => 'lpr',
            'notes' => $this->apiNotes($data),
        ]);

        $ticket = $ticketModel->detail($ticketId);
        $this->json([
            'success' => true,
            'message' => 'Tiket parkir dibuat dan gate masuk dibuka.',
            'action' => 'entry_created',
            'gate_opened' => !empty($ticket['entry_gate_id']),
            'ticket' => $this->ticketPayload($ticket),
            'kiosk_url' => url('parking-kiosk-entry', ['ticket_id' => $ticketId]),
        ], 201);
    }

    public function lprExit()
    {
        $this->requirePostJson();
        $data = $this->requestData();
        $ticket = $this->resolveTicket($data);

        if (!$ticket) {
            $this->json(['success' => false, 'message' => 'Tiket aktif tidak ditemukan untuk kendaraan ini.'], 404);
        }

        $gateId = $data['gate_id'] ?? ($this->defaults(0, ['exit', 'both'])['gate']['id'] ?? null);

        if (empty($ticket['exit_time'])) {
            (new ParkingService())->checkout($ticket['id'], [
                'exit_gate_id' => null,
                'exit_time' => $data['exit_time'] ?? date('Y-m-d H:i:s'),
                'lost_ticket' => !empty($data['lost_ticket']),
            ]);
            $ticket = (new ParkingTicket())->detail($ticket['id']);
        }

        $paymentMethod = $this->normalizePaymentMethod($data['payment_method'] ?? '');
        $autoPay = array_key_exists('auto_pay', $data)
            ? filter_var($data['auto_pay'], FILTER_VALIDATE_BOOLEAN)
            : false;
        $shouldPay = $autoPay || $paymentMethod !== '';
        $isPaid = in_array($ticket['payment_status'] ?? '', ['paid', 'waived'], true);

        if (!$isPaid && $shouldPay) {
            (new ParkingService())->recordPayment($ticket['id'], [
                'payment_method' => $paymentMethod ?: 'qris',
                'amount' => (float) ($ticket['payable_amount'] ?? 0),
                'reference_no' => $data['reference_no'] ?? ('LPR-' . date('YmdHis')),
                'notes' => 'Pembayaran dari API LPR exit',
            ]);
            $ticket = (new ParkingTicket())->detail($ticket['id']);
            $isPaid = true;
        }

        $gateOpened = false;
        if ($isPaid && $gateId) {
            (new ParkingTicket())->update($ticket['id'], ['exit_gate_id' => $gateId]);
            (new ParkingGateService())->openGate($gateId, $ticket['id'], 'open_exit');
            $ticket = (new ParkingTicket())->detail($ticket['id']);
            $gateOpened = true;
        }

        $this->json([
            'success' => true,
            'message' => $isPaid ? 'Tiket lunas dan gate keluar dibuka.' : 'Tiket ditemukan, pembayaran diperlukan.',
            'action' => $isPaid ? 'exit_opened' : 'payment_required',
            'gate_opened' => $gateOpened,
            'ticket' => $this->ticketPayload($ticket),
            'payment_required' => !$isPaid,
            'kiosk_url' => url('parking-kiosk-exit', ['ticket_id' => $ticket['id']]),
        ]);
    }

    public function ticket()
    {
        $this->guardApiToken();
        $data = $this->requestData();
        $ticket = $this->resolveTicket($data);

        if (!$ticket) {
            $this->json(['success' => false, 'message' => 'Tiket tidak ditemukan.'], 404);
        }

        $this->json([
            'success' => true,
            'ticket' => $this->ticketPayload($ticket),
        ]);
    }

    private function resolveTicket(array $data)
    {
        $ticketModel = new ParkingTicket();

        if (!empty($data['ticket_id'])) {
            return $ticketModel->detail($data['ticket_id']);
        }

        if (!empty($data['ticket_no'])) {
            return $ticketModel->kioskLookup($data['ticket_no']);
        }

        if (!empty($data['qr_token'])) {
            $ticket = $ticketModel->byToken($data['qr_token']);
            return $ticket ? $ticketModel->detail($ticket['id']) : null;
        }

        if (!empty($data['plate_number'])) {
            return $ticketModel->activeByPlate($data['plate_number']) ?: $ticketModel->kioskLookup($data['plate_number']);
        }

        return null;
    }

    private function defaults($gateId = 0, array $gateTypes = ['entry', 'both'])
    {
        $areas = (new ParkingArea())->active();
        $types = (new ParkingVehicleType())->active();
        $gates = (new ParkingGate())->active();
        $gate = null;

        foreach ($gates as $candidate) {
            if ($gateId && (int) $candidate['id'] === $gateId) {
                $gate = $candidate;
                break;
            }
        }

        if (!$gate) {
            foreach ($gates as $candidate) {
                if (in_array($candidate['gate_type'] ?? '', $gateTypes, true)) {
                    $gate = $candidate;
                    break;
                }
            }
        }

        return [
            'area' => $areas[0] ?? null,
            'gate' => $gate ?: ($gates[0] ?? null),
            'vehicleType' => $this->preferredVehicleType($types),
        ];
    }

    private function preferredVehicleType(array $types)
    {
        foreach ($types as $type) {
            if (strtolower($type['category'] ?? '') === 'car' || str_contains(strtolower($type['name'] ?? ''), 'mobil')) {
                return $type;
            }
        }

        return $types[0] ?? null;
    }

    private function ticketPayload(array $ticket)
    {
        return [
            'id' => (int) $ticket['id'],
            'ticket_no' => $ticket['ticket_no'] ?? null,
            'qr_token' => $ticket['qr_token'] ?? null,
            'plate_number' => $ticket['plate_number'] ?? null,
            'vehicle_type' => $ticket['vehicle_type_name'] ?? null,
            'area' => $ticket['area_name'] ?? null,
            'entry_gate' => $ticket['entry_gate_name'] ?? null,
            'exit_gate' => $ticket['exit_gate_name'] ?? null,
            'entry_time' => $ticket['entry_time'] ?? null,
            'exit_time' => $ticket['exit_time'] ?? null,
            'duration_minutes' => (int) ($ticket['duration_minutes'] ?? 0),
            'payable_amount' => (float) ($ticket['payable_amount'] ?? 0),
            'status' => $ticket['status'] ?? null,
            'payment_status' => $ticket['payment_status'] ?? null,
        ];
    }

    private function apiNotes(array $data)
    {
        $parts = ['API LPR'];
        if (isset($data['confidence'])) {
            $parts[] = 'confidence=' . $data['confidence'];
        }
        if (!empty($data['image_url'])) {
            $parts[] = 'image=' . $data['image_url'];
        }

        return implode('; ', $parts);
    }

    private function normalizePaymentMethod($method)
    {
        $method = strtolower(trim((string) $method));
        return match ($method) {
            'cash', 'qris', 'debit', 'transfer', 'member', 'patient_billing', 'waived', 'other' => $method,
            'emoney', 'e-money', 'e_money' => 'other',
            default => '',
        };
    }

    private function requestData()
    {
        $raw = file_get_contents('php://input');
        $json = json_decode($raw ?: '', true);
        return is_array($json) ? array_merge($_GET, $_POST, $json) : array_merge($_GET, $_POST);
    }

    private function requirePostJson()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            $this->json(['success' => false, 'message' => 'Method wajib POST.'], 405);
        }

        $this->guardApiToken();
    }

    private function guardApiToken()
    {
        $expected = trim((string) getenv('PARKING_API_TOKEN'));
        if ($expected === '') {
            return;
        }

        $provided = $_SERVER['HTTP_X_PARKING_API_KEY'] ?? '';
        $auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if ($provided === '' && str_starts_with($auth, 'Bearer ')) {
            $provided = substr($auth, 7);
        }

        if (!hash_equals($expected, trim((string) $provided))) {
            $this->json(['success' => false, 'message' => 'API token tidak valid.'], 401);
        }
    }

    private function json(array $payload, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }
}

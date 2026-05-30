<?php

class ParkingKioskController extends Controller
{
    public function entry()
    {
        $ticket = !empty($_GET['ticket_id']) ? (new ParkingTicket())->detail($_GET['ticket_id']) : null;
        $defaults = $this->defaults();

        $this->frontView('parking/kiosk/entry', [
            'title' => 'Kiosk Parkir Masuk',
            'ticket' => $ticket,
            'area' => $defaults['area'],
            'gate' => $defaults['entryGate'],
            'vehicleType' => $defaults['vehicleType'],
            'plate' => strtoupper($_GET['plate'] ?? ($ticket['plate_number'] ?? 'B 1234 ABC')),
        ]);
    }

    public function storeEntry()
    {
        $defaults = $this->defaults();
        $ticketId = (new ParkingService())->checkIn([
            'plate_number' => $_POST['plate_number'] ?? 'B 1234 ABC',
            'vehicle_type_id' => $_POST['vehicle_type_id'] ?? ($defaults['vehicleType']['id'] ?? null),
            'area_id' => $_POST['area_id'] ?? ($defaults['area']['id'] ?? null),
            'entry_gate_id' => $_POST['entry_gate_id'] ?? ($defaults['entryGate']['id'] ?? null),
            'entry_time' => date('Y-m-d H:i:s'),
            'source' => 'lpr',
            'notes' => 'Check-in dari kiosk parkir masuk',
        ]);

        $this->redirect('parking-kiosk-entry', ['ticket_id' => $ticketId]);
    }

    public function exit()
    {
        $keyword = trim($_GET['ticket'] ?? ($_GET['plate'] ?? ''));
        $ticket = null;

        if (!empty($_GET['ticket_id'])) {
            $ticket = (new ParkingTicket())->detail($_GET['ticket_id']);
        } elseif ($keyword !== '') {
            $ticket = (new ParkingTicket())->kioskLookup($keyword);
        } else {
            $ticket = (new ParkingTicket())->latestKioskTicket();
        }

        $defaults = $this->defaults();
        $calc = $ticket ? (new ParkingService())->calculate($ticket['id'], date('Y-m-d H:i:s'), false) : null;

        $this->frontView('parking/kiosk/exit', [
            'title' => 'Kiosk Parkir Keluar',
            'ticket' => $ticket,
            'calc' => $calc,
            'gate' => $defaults['exitGate'],
            'keyword' => $keyword,
            'paid' => !empty($_GET['paid']),
        ]);
    }

    public function payExit()
    {
        $ticketId = $_POST['ticket_id'] ?? null;
        $exitGateId = $_POST['exit_gate_id'] ?? null;
        $method = $_POST['payment_method'] ?? 'qris';
        $ticket = (new ParkingTicket())->detail($ticketId);

        if (!$ticket) {
            $this->redirect('parking-kiosk-exit');
        }

        if (empty($ticket['exit_time'])) {
            (new ParkingService())->checkout($ticketId, [
                'exit_gate_id' => null,
                'exit_time' => date('Y-m-d H:i:s'),
                'lost_ticket' => false,
            ]);
            $ticket = (new ParkingTicket())->detail($ticketId);
        }

        if (($ticket['payment_status'] ?? '') !== 'paid' && ($ticket['payment_status'] ?? '') !== 'waived') {
            (new ParkingService())->recordPayment($ticketId, [
                'payment_method' => $method === 'e_money' ? 'other' : $method,
                'amount' => (float) ($ticket['payable_amount'] ?? 0),
                'reference_no' => strtoupper($method) . '-' . date('His'),
                'notes' => 'Pembayaran dari kiosk parkir keluar' . ($method === 'e_money' ? ' - E-Money' : ''),
            ]);
        }

        if ($exitGateId) {
            (new ParkingTicket())->update($ticketId, ['exit_gate_id' => $exitGateId]);
            (new ParkingGateService())->openGate($exitGateId, $ticketId, 'open_exit');
        }

        $this->redirect('parking-kiosk-exit', ['ticket_id' => $ticketId, 'paid' => 1]);
    }

    private function defaults()
    {
        $areas = (new ParkingArea())->active();
        $gates = (new ParkingGate())->active();
        $types = (new ParkingVehicleType())->active();

        return [
            'area' => $areas[0] ?? null,
            'entryGate' => $this->firstGate($gates, ['entry', 'both']),
            'exitGate' => $this->firstGate($gates, ['exit', 'both']),
            'vehicleType' => $this->preferredVehicleType($types),
        ];
    }

    private function firstGate(array $gates, array $types)
    {
        foreach ($gates as $gate) {
            if (in_array($gate['gate_type'] ?? '', $types, true)) {
                return $gate;
            }
        }

        return $gates[0] ?? null;
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
}

<?php

class ParkingTicketController extends Controller
{
    public function index()
    {
        $this->guard('parking_ticket.manage');
        $search = trim($_GET['search'] ?? '');
        $tickets = (new ParkingTicket())->recent($search);
        $pagination = paginateRows($tickets, 'parking-tickets', $search, 10);
        $this->view('parking/tickets/index', array_merge([
            'title' => 'Tiket Parkir',
            'tickets' => $pagination['rows'],
            'search' => $search,
        ], $pagination));
    }

    public function show()
    {
        $this->guard('parking_ticket.manage');
        $ticket = (new ParkingTicket())->detail($_GET['id'] ?? null);
        if (!$ticket) {
            $this->redirect('parking-tickets');
        }
        $this->view('parking/tickets/show', [
            'title' => 'Detail Tiket Parkir',
            'ticket' => $ticket,
            'exitGates' => $this->gates(['exit','both']),
            'bankAccounts' => class_exists('BankAccount') ? (new BankAccount())->getActive() : [],
        ]);
    }

    public function checkin()
    {
        $this->guard('parking_checkin.manage');
        $this->view('parking/checkin', [
            'title' => 'Check-in Kendaraan',
            'areas' => (new ParkingArea())->active(),
            'entryGates' => $this->gates(['entry','both']),
            'vehicleTypes' => (new ParkingVehicleType())->active(),
            'visits' => (new PatientVisit())->list('', date('Y-m-d')),
        ]);
    }

    public function storeCheckin()
    {
        $this->guard('parking_checkin.manage');
        $ticketId = (new ParkingService())->checkIn([
            'plate_number' => $_POST['plate_number'] ?? '',
            'vehicle_type_id' => $_POST['vehicle_type_id'] ?? null,
            'area_id' => $_POST['area_id'] ?? null,
            'entry_gate_id' => $_POST['entry_gate_id'] ?? null,
            'patient_visit_id' => $_POST['patient_visit_id'] ?? null,
            'patient_id' => $_POST['patient_id'] ?? null,
            'entry_time' => $_POST['entry_time'] ?? date('Y-m-d H:i:s'),
            'source' => $_POST['source'] ?? 'manual',
            'notes' => $_POST['notes'] ?? '',
        ]);
        $this->redirect('parking-tickets-show', ['id' => $ticketId]);
    }

    public function checkout()
    {
        $this->guard('parking_checkout.manage');
        $search = trim($_GET['search'] ?? '');
        $tickets = (new ParkingTicket())->openTickets($search);
        $pagination = paginateRows($tickets, 'parking-checkout', $search, 10);
        $this->view('parking/checkout', array_merge([
            'title' => 'Check-out Kendaraan',
            'tickets' => $pagination['rows'],
            'search' => $search,
        ], $pagination));
    }

    public function processCheckout()
    {
        $this->guard('parking_checkout.manage');
        (new ParkingService())->checkout($_POST['ticket_id'] ?? null, [
            'exit_gate_id' => $_POST['exit_gate_id'] ?? null,
            'exit_time' => $_POST['exit_time'] ?? date('Y-m-d H:i:s'),
            'lost_ticket' => !empty($_POST['lost_ticket']),
        ]);
        $this->redirect('parking-tickets-show', ['id' => $_POST['ticket_id'] ?? null]);
    }

    public function pay()
    {
        $this->guard('parking_payment.manage');
        (new ParkingService())->recordPayment($_POST['ticket_id'] ?? null, [
            'bank_account_id' => $_POST['bank_account_id'] ?? null,
            'payment_date' => $_POST['payment_date'] ?? date('Y-m-d'),
            'payment_method' => $_POST['payment_method'] ?? 'cash',
            'amount' => (float) str_replace('.', '', $_POST['amount'] ?? 0),
            'reference_no' => $_POST['reference_no'] ?? '',
            'notes' => $_POST['notes'] ?? '',
        ]);
        $_SESSION['success'] = 'Pembayaran parkir berhasil dicatat.';
        $this->redirect('parking-tickets-show', ['id' => $_POST['ticket_id'] ?? null]);
    }

    public function validations()
    {
        $this->guard('parking_validation.manage');
        $tickets = (new ParkingTicket())->openTickets(trim($_GET['search'] ?? ''));
        $pagination = paginateRows($tickets, 'parking-validations', trim($_GET['search'] ?? ''), 10);
        $this->view('parking/validations/index', array_merge([
            'title' => 'Validasi Parkir Pasien',
            'tickets' => $pagination['rows'],
            'visits' => (new PatientVisit())->list('', date('Y-m-d')),
            'search' => trim($_GET['search'] ?? ''),
        ], $pagination));
    }

    public function storeValidation()
    {
        $this->guard('parking_validation.manage');
        $visit = null;
        if (!empty($_POST['patient_visit_id'])) {
            $visit = (new PatientVisit())->withRelations($_POST['patient_visit_id']);
        }
        $id = (new ParkingValidation())->create([
            'ticket_id' => $_POST['ticket_id'] ?? null,
            'patient_visit_id' => $_POST['patient_visit_id'] ?: null,
            'patient_id' => $visit['patient_id'] ?? null,
            'validation_type' => $_POST['validation_type'] ?? 'outpatient',
            'discount_type' => $_POST['discount_type'] ?? 'free',
            'discount_value' => (float) ($_POST['discount_value'] ?? 0),
            'validated_by' => $_SESSION['user_id'] ?? null,
            'validated_at' => date('Y-m-d H:i:s'),
            'notes' => $_POST['notes'] ?? '',
        ]);
        activity_log('Parking - Validasi', 'create', 'Validasi parkir pasien dibuat', $id);
        $this->redirect('parking-validations');
    }

    public function openGate()
    {
        $this->guard('parking_ticket.manage');
        (new ParkingGateService())->openGate($_GET['gate_id'] ?? null, $_GET['ticket_id'] ?? null, $_GET['action'] ?? 'open_manual');
        $_SESSION['success'] = 'Gate dibuka dalam mode simulasi.';
        $this->redirect('parking-tickets-show', ['id' => $_GET['ticket_id'] ?? null]);
    }

    private function gates($types)
    {
        $all = (new ParkingGate())->active();
        return array_values(array_filter($all, fn($gate) => in_array($gate['gate_type'], $types, true)));
    }

    private function guard($permission)
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        requirePermission($permission);
    }
}

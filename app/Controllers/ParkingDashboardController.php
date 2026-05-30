<?php

class ParkingDashboardController extends Controller
{
    public function index()
    {
        $this->guard('parking_dashboard.view');
        $report = new ParkingReport();

        if (role_name() === 'parking_operator') {
            $this->view('parking/operator-dashboard', [
                'title' => 'Dashboard Operator',
                'stats' => $report->operatorDashboard(),
            ]);
            return;
        }

        $this->view('parking/dashboard', [
            'title' => 'Dashboard Parking',
            'stats' => $report->dashboard(),
        ]);
    }

    public function transactions()
    {
        $this->guard('parking_report.view');
        $from = $_GET['from'] ?? date('Y-m-01');
        $to = $_GET['to'] ?? date('Y-m-d');
        $rows = (new ParkingPayment())->report($from, $to);
        $pagination = paginateRows($rows, 'parking-reports-transactions', trim($_GET['search'] ?? ''), 10);
        $this->view('parking/reports/transactions', array_merge([
            'title' => 'Laporan Transaksi Parkir',
            'rows' => $pagination['rows'],
            'from' => $from,
            'to' => $to,
        ], $pagination));
    }

    public function occupancy()
    {
        $this->guard('parking_report.view');
        $rows = (new ParkingReport())->occupancy();
        $pagination = paginateRows($rows, 'parking-reports-occupancy', trim($_GET['search'] ?? ''), 10);
        $this->view('parking/reports/occupancy', array_merge([
            'title' => 'Laporan Okupansi Parkir',
            'rows' => $pagination['rows'],
        ], $pagination));
    }

    public function gateQueue()
    {
        $this->guard('parking_ticket.manage');
        $search = trim($_GET['search'] ?? '');
        $rows = (new ParkingReport())->gateQueue($search);
        $pagination = paginateRows($rows, 'parking-gate-queue', $search, 10);
        $this->view('parking/reports/gate-queue', array_merge([
            'title' => 'Antrean Gate',
            'rows' => $pagination['rows'],
            'search' => $search,
        ], $pagination));
    }

    public function income()
    {
        $this->guard('parking_report.view');
        $from = $_GET['from'] ?? date('Y-m-01');
        $to = $_GET['to'] ?? date('Y-m-d');
        $rows = (new ParkingReport())->income($from, $to);
        $pagination = paginateRows($rows, 'parking-reports-income', trim($_GET['search'] ?? ''), 10);
        $this->view('parking/reports/income', array_merge([
            'title' => 'Laporan Pendapatan Parkir',
            'rows' => $pagination['rows'],
            'from' => $from,
            'to' => $to,
        ], $pagination));
    }

    public function duration()
    {
        $this->guard('parking_report.view');
        $rows = (new ParkingReport())->duration();
        $pagination = paginateRows($rows, 'parking-reports-duration', trim($_GET['search'] ?? ''), 10);
        $this->view('parking/reports/duration', array_merge([
            'title' => 'Laporan Durasi Parkir',
            'rows' => $pagination['rows'],
        ], $pagination));
    }

    public function lostTicket()
    {
        $this->guard('parking_report.view');
        $search = trim($_GET['search'] ?? '');
        $rows = (new ParkingReport())->lostTickets($search);
        $pagination = paginateRows($rows, 'parking-reports-lost-ticket', $search, 10);
        $this->view('parking/reports/lost-ticket', array_merge([
            'title' => 'Laporan Ticket Lost',
            'rows' => $pagination['rows'],
            'search' => $search,
        ], $pagination));
    }

    private function guard($permission)
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        requirePermission($permission);
    }
}

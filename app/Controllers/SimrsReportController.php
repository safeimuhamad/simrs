<?php

class SimrsReportController extends Controller
{
    public function dashboard()
    {
        $this->guard('simrs_dashboard.view');

        $roleDashboard = roleDashboardRoute();
        if ($roleDashboard !== 'simrs-dashboard') {
            $this->redirect($roleDashboard);
        }

        $this->view('simrs/dashboard/index', [
            'title' => 'Dashboard SIMRS',
            'stats' => (new SimrsReport())->dashboard(),
        ]);
    }

    public function visits()
    {
        $this->guard('simrs_report.view');
        $from = $_GET['from'] ?? date('Y-m-01');
        $to = $_GET['to'] ?? date('Y-m-d');
        $rows = (new SimrsReport())->visits($from, $to);
        $pagination = paginateRows($rows, 'simrs-reports-visits', trim($_GET['search'] ?? ''), 10);
        $this->view('simrs/reports/visits', array_merge([
            'title' => 'Laporan Kunjungan',
            'rows' => $pagination['rows'],
            'from' => $from,
            'to' => $to,
        ], $pagination));
    }

    public function income()
    {
        $this->guard('simrs_report.view');
        $from = $_GET['from'] ?? date('Y-m-01');
        $to = $_GET['to'] ?? date('Y-m-d');
        $rows = (new SimrsReport())->income($from, $to);
        $pagination = paginateRows($rows, 'simrs-reports-income', trim($_GET['search'] ?? ''), 10);
        $this->view('simrs/reports/income', array_merge([
            'title' => 'Laporan Pendapatan',
            'rows' => $pagination['rows'],
            'from' => $from,
            'to' => $to,
        ], $pagination));
    }

    public function pharmacy()
    {
        $this->guard('simrs_report.view');
        $from = $_GET['from'] ?? date('Y-m-01');
        $to = $_GET['to'] ?? date('Y-m-d');
        $rows = (new SimrsReport())->pharmacy($from, $to);
        $pagination = paginateRows($rows, 'simrs-reports-pharmacy', trim($_GET['search'] ?? ''), 10);
        $this->view('simrs/reports/pharmacy', array_merge([
            'title' => 'Laporan Farmasi',
            'rows' => $pagination['rows'],
            'from' => $from,
            'to' => $to,
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

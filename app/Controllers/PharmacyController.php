<?php

class PharmacyController extends Controller
{
    public function index()
    {
        $this->guard();
        $prescriptions = (new Prescription())->pendingForPharmacy();
        $pagination = paginateRows($prescriptions, 'simrs-pharmacy', trim($_GET['search'] ?? ''), 10);
        $this->view('simrs/pharmacy/index', array_merge([
            'title' => 'Farmasi',
            'prescriptions' => $pagination['rows'],
            'lowStock' => (new Pharmacy())->lowStock(),
        ], $pagination));
    }

    public function show()
    {
        $this->guard();
        $rx = (new Prescription())->withDetails($_GET['id'] ?? null);
        if (!$rx) {
            $this->redirect('simrs-pharmacy');
        }
        $this->view('simrs/pharmacy/show', ['title' => 'Detail Resep Farmasi', 'rx' => $rx]);
    }

    public function status()
    {
        $this->guard();
        $id = $_GET['id'] ?? null;
        $status = $_GET['status'] ?? 'verified';
        $rx = (new Prescription())->find($id);

        if ($rx && in_array($status, ['verified','prepared','cancelled'], true)) {
            $data = ['status' => $status];
            if ($status === 'verified') {
                $data['verified_by'] = $_SESSION['user_id'] ?? null;
                $data['verified_at'] = date('Y-m-d H:i:s');
            }
            (new Prescription())->update($id, $data);
            activity_log('SIMRS - Farmasi', 'status', 'Status resep berubah ke ' . $status, $id, $rx['prescription_no']);
        }

        $this->redirect('simrs-pharmacy');
    }

    public function dispense()
    {
        $this->guard();
        try {
            (new Pharmacy())->dispense($_POST['prescription_id'] ?? null, $_SESSION['user_id'] ?? null);
            activity_log('SIMRS - Farmasi', 'dispense', 'Obat diserahkan', $_POST['prescription_id'] ?? null);
            $_SESSION['success'] = 'Obat berhasil diserahkan dan stok berkurang.';
        } catch (Throwable $e) {
            $_SESSION['error'] = $e->getMessage();
        }
        $this->redirect('simrs-pharmacy');
    }

    private function guard()
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        requirePermission('simrs_pharmacy.manage');
    }
}

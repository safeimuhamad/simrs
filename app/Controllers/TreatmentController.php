<?php

class TreatmentController extends Controller
{
    public function store()
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }

        requirePermission('simrs_medical_record.manage');

        $visit = (new PatientVisit())->withRelations($_POST['visit_id'] ?? null);
        if (!$visit) {
            $this->redirect('simrs-outpatient');
        }

        $billingModel = new PatientBilling();
        $billingId = $billingModel->ensureForVisit($visit['id']);
        $billingModel->addItem($billingId, [
            'reference_type' => 'treatment',
            'reference_id' => null,
            'item_name' => trim($_POST['item_name'] ?? 'Tindakan medis'),
            'quantity' => (float) ($_POST['quantity'] ?? 1),
            'unit_price' => (float) str_replace('.', '', $_POST['unit_price'] ?? 0),
        ]);
        $billingModel->recalculate($billingId, 'unpaid');

        activity_log('SIMRS - Tindakan', 'create', 'Tindakan rawat jalan ditambahkan ke billing', $visit['id'], $visit['visit_no']);
        $_SESSION['success'] = 'Tindakan berhasil ditambahkan ke billing.';

        $this->redirect('simrs-outpatient-examine', ['visit_id' => $visit['id']]);
    }
}

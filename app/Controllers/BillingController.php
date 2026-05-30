<?php

class BillingController extends Controller
{
    public function index()
    {
        $this->guard();
        $billings = (new PatientBilling())->unpaid();
        $pagination = paginateRows($billings, 'simrs-billing', trim($_GET['search'] ?? ''), 10);
        $this->view('simrs/billing/index', array_merge([
            'title' => 'Billing Pasien',
            'billings' => $pagination['rows'],
        ], $pagination));
    }

    public function show()
    {
        $this->guard();
        $billing = (new PatientBilling())->detail($_GET['id'] ?? null);
        if (!$billing) {
            $this->redirect('simrs-billing');
        }
        $this->view('simrs/billing/show', ['title' => 'Detail Billing Pasien', 'billing' => $billing]);
    }

    public function addItem()
    {
        $this->guard();
        $billingId = $_POST['billing_id'] ?? null;
        (new PatientBilling())->addItem($billingId, [
            'reference_type' => 'manual',
            'reference_id' => null,
            'item_name' => trim($_POST['item_name'] ?? ''),
            'quantity' => (float) ($_POST['quantity'] ?? 1),
            'unit_price' => (float) str_replace('.', '', $_POST['unit_price'] ?? 0),
        ]);
        (new PatientBilling())->recalculate($billingId, 'unpaid');
        activity_log('SIMRS - Billing', 'add_item', 'Item billing ditambahkan', $billingId);
        $this->redirect('simrs-billing-show', ['id' => $billingId]);
    }

    private function guard()
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        requirePermission('simrs_billing.manage');
    }
}

<?php

class CashierController extends Controller
{
    public function index()
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        requirePermission('simrs_cashier.manage');
        $billings = (new PatientBilling())->unpaid();
        $pagination = paginateRows($billings, 'simrs-cashier', trim($_GET['search'] ?? ''), 10);
        $this->view('simrs/cashier/index', array_merge([
            'title' => 'Kasir Pasien',
            'billings' => $pagination['rows'],
        ], $pagination));
    }

    public function pay()
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        requirePermission('simrs_cashier.manage');

        try {
            $paymentId = (new PatientPayment())->receive($_POST['billing_id'] ?? null, [
                'bank_account_id' => $_POST['bank_account_id'] ?? null,
                'payment_date' => $_POST['payment_date'] ?? date('Y-m-d'),
                'payment_method' => $_POST['payment_method'] ?? 'cash',
                'amount' => (float) str_replace('.', '', $_POST['amount'] ?? 0),
                'reference_no' => trim($_POST['reference_no'] ?? ''),
                'notes' => trim($_POST['notes'] ?? ''),
            ]);
            activity_log('SIMRS - Kasir', 'payment', 'Pembayaran pasien diterima', $paymentId);
            $_SESSION['success'] = 'Pembayaran berhasil diterima.';
        } catch (Throwable $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        $this->redirect('simrs-cashier');
    }
}

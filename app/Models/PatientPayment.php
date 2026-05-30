<?php

class PatientPayment extends SimrsModel
{
    protected $table = 'patient_payments';
    protected $fillable = ['payment_no','billing_id','visit_id','patient_id','bank_account_id','payment_date','payment_method','amount','reference_no','notes','created_by'];

    public function byBilling($billingId)
    {
        $stmt = $this->db->prepare("
            SELECT pp.*, ba.account_name, ba.bank_name
            FROM patient_payments pp
            LEFT JOIN bank_accounts ba ON ba.id = pp.bank_account_id
            WHERE pp.billing_id = ?
            ORDER BY pp.payment_date ASC, pp.id ASC
        ");
        $stmt->execute([$billingId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function receive($billingId, $data)
    {
        $billingModel = new PatientBilling();
        $billing = $billingModel->find($billingId);

        if (!$billing) {
            throw new RuntimeException('Billing tidak ditemukan.');
        }

        $this->db->beginTransaction();

        try {
            $paymentId = $this->create([
                'payment_no' => SimrsNumber::make('patient_payments', 'payment_no', 'PAY'),
                'billing_id' => $billingId,
                'visit_id' => $billing['visit_id'],
                'patient_id' => $billing['patient_id'],
                'bank_account_id' => $data['bank_account_id'] ?: null,
                'payment_date' => $data['payment_date'] ?: date('Y-m-d'),
                'payment_method' => $data['payment_method'] ?: 'cash',
                'amount' => (float) $data['amount'],
                'reference_no' => $data['reference_no'] ?? '',
                'notes' => $data['notes'] ?? '',
                'created_by' => $_SESSION['user_id'] ?? null,
            ]);

            $status = $billingModel->updatePaid($billingId, (float) $data['amount']);

            if ($status === 'paid') {
                (new PatientVisit())->changeStatus($billing['visit_id'], 'completed');
            }

            $this->db->commit();
            return $paymentId;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
}

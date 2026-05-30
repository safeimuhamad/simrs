<?php

class PatientBilling extends SimrsModel
{
    protected $table = 'patient_billing';
    protected $fillable = ['billing_no','visit_id','patient_id','billing_date','subtotal','discount_amount','tax_amount','grand_total','paid_amount','status','notes','created_by'];

    public function ensureForVisit($visitId)
    {
        $stmt = $this->db->prepare("SELECT id FROM patient_billing WHERE visit_id = ? LIMIT 1");
        $stmt->execute([$visitId]);
        $existing = $stmt->fetchColumn();

        if ($existing) {
            return (int) $existing;
        }

        $visit = (new PatientVisit())->withRelations($visitId);

        return $this->create([
            'billing_no' => SimrsNumber::make('patient_billing', 'billing_no', 'BILL'),
            'visit_id' => $visitId,
            'patient_id' => $visit['patient_id'],
            'billing_date' => date('Y-m-d'),
            'status' => 'draft',
            'created_by' => $_SESSION['user_id'] ?? null,
        ]);
    }

    public function addItem($billingId, $data)
    {
        $quantity = (float) ($data['quantity'] ?? 1);
        $unitPrice = (float) ($data['unit_price'] ?? 0);
        $stmt = $this->db->prepare("
            INSERT INTO patient_billing_items
            (billing_id, reference_type, reference_id, item_name, quantity, unit_price, total_price)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $billingId,
            $data['reference_type'] ?? null,
            $data['reference_id'] ?? null,
            $data['item_name'],
            $quantity,
            $unitPrice,
            $quantity * $unitPrice
        ]);
    }

    public function recalculate($billingId, $status = null)
    {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(total_price), 0) FROM patient_billing_items WHERE billing_id = ?");
        $stmt->execute([$billingId]);
        $subtotal = (float) $stmt->fetchColumn();

        $billing = $this->find($billingId);
        $paid = (float) ($billing['paid_amount'] ?? 0);
        $newStatus = $status ?: $this->paymentStatus($paid, $subtotal);

        $stmt = $this->db->prepare("
            UPDATE patient_billing
            SET subtotal = ?, grand_total = ?, status = ?
            WHERE id = ?
        ");

        return $stmt->execute([$subtotal, $subtotal, $newStatus, $billingId]);
    }

    public function detail($id)
    {
        $stmt = $this->db->prepare("
            SELECT b.*, p.name AS patient_name, p.medical_record_no, v.visit_no
            FROM patient_billing b
            LEFT JOIN patients p ON p.id = b.patient_id
            LEFT JOIN patient_visits v ON v.id = b.visit_id
            WHERE b.id = ?
        ");
        $stmt->execute([$id]);
        $billing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($billing) {
            $billing['items'] = $this->items($id);
            $billing['payments'] = (new PatientPayment())->byBilling($id);
        }

        return $billing;
    }

    public function byVisit($visitId)
    {
        $stmt = $this->db->prepare("SELECT * FROM patient_billing WHERE visit_id = ? LIMIT 1");
        $stmt->execute([$visitId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function items($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM patient_billing_items WHERE billing_id = ? ORDER BY id ASC");
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function unpaid()
    {
        $stmt = $this->db->query("
            SELECT b.*, p.name AS patient_name, p.medical_record_no, v.visit_no
            FROM patient_billing b
            LEFT JOIN patients p ON p.id = b.patient_id
            LEFT JOIN patient_visits v ON v.id = b.visit_id
            WHERE b.status IN ('unpaid','partial')
            ORDER BY b.id ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updatePaid($billingId, $paidAmount)
    {
        $billing = $this->find($billingId);
        $totalPaid = (float) ($billing['paid_amount'] ?? 0) + (float) $paidAmount;
        $status = $this->paymentStatus($totalPaid, (float) ($billing['grand_total'] ?? 0));

        $stmt = $this->db->prepare("UPDATE patient_billing SET paid_amount = ?, status = ? WHERE id = ?");
        $stmt->execute([$totalPaid, $status, $billingId]);

        if ($status === 'paid') {
            (new PatientVisit())->changeStatus($billing['visit_id'], 'paid');
        }

        return $status;
    }

    private function paymentStatus($paid, $total)
    {
        if ($total <= 0) {
            return 'draft';
        }

        if ($paid <= 0) {
            return 'unpaid';
        }

        return $paid >= $total ? 'paid' : 'partial';
    }
}

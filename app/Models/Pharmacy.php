<?php

class Pharmacy
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function medicines()
    {
        $stmt = $this->db->query("
            SELECT *
            FROM medical_items
            WHERE status = 'active'
            ORDER BY name ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function lowStock()
    {
        $stmt = $this->db->query("
            SELECT *
            FROM medical_items
            WHERE status = 'active'
              AND current_stock <= minimum_stock
            ORDER BY current_stock ASC
            LIMIT 20
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function dispense($prescriptionId, $userId = null)
    {
        $rxModel = new Prescription();
        $visitModel = new PatientVisit();
        $billingModel = new PatientBilling();
        $rx = $rxModel->withDetails($prescriptionId);

        if (!$rx) {
            throw new RuntimeException('Resep tidak ditemukan.');
        }

        foreach ($rx['items'] as $item) {
            if (!empty($item['product_id']) && (float) $item['current_stock'] < (float) $item['quantity']) {
                throw new RuntimeException('Stok tidak cukup untuk ' . $item['item_name']);
            }
        }

        $this->db->beginTransaction();

        try {
            $total = 0;
            foreach ($rx['items'] as $item) {
                $lineTotal = (float) $item['quantity'] * (float) $item['price'];
                $total += $lineTotal;

                if (!empty($item['product_id'])) {
                    (new MedicalItem())->decreaseStock(
                        $item['product_id'],
                        (float) $item['quantity'],
                        'prescription',
                        $prescriptionId,
                        'Serah obat resep ' . $rx['prescription_no'],
                        $userId
                    );
                }
            }

            $stmt = $this->db->prepare("
                INSERT INTO pharmacy_transactions
                (transaction_no, prescription_id, visit_id, patient_id, status, total_amount, created_by)
                VALUES (?, ?, ?, ?, 'dispensed', ?, ?)
            ");
            $stmt->execute([
                SimrsNumber::make('pharmacy_transactions', 'transaction_no', 'PHR'),
                $prescriptionId,
                $rx['visit_id'],
                $rx['patient_id'],
                $total,
                $userId
            ]);

            $this->db->prepare("
                UPDATE prescriptions
                SET status = 'dispensed', dispensed_by = ?, dispensed_at = NOW()
                WHERE id = ?
            ")->execute([$userId, $prescriptionId]);

            $billingId = $billingModel->ensureForVisit($rx['visit_id']);
            foreach ($rx['items'] as $item) {
                $billingModel->addItem($billingId, [
                    'reference_type' => 'prescription_item',
                    'reference_id' => $item['id'],
                    'item_name' => 'Obat - ' . $item['item_name'],
                    'quantity' => (float) $item['quantity'],
                    'unit_price' => (float) $item['price'],
                ]);
            }
            $billingModel->recalculate($billingId, 'unpaid');
            $visitModel->changeStatus($rx['visit_id'], 'billing');

            $this->db->commit();
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    private function product($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM medical_items WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

<?php

class Prescription extends SimrsModel
{
    protected $table = 'prescriptions';
    protected $fillable = ['prescription_no','visit_id','patient_id','doctor_id','status','notes','verified_by','dispensed_by','verified_at','dispensed_at'];

    public function byVisit($visitId)
    {
        $stmt = $this->db->prepare("SELECT * FROM prescriptions WHERE visit_id = ? ORDER BY id DESC");
        $stmt->execute([$visitId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function withDetails($id)
    {
        $stmt = $this->db->prepare("
            SELECT rx.*, p.name AS patient_name, p.medical_record_no, d.name AS doctor_name, v.visit_no
            FROM prescriptions rx
            LEFT JOIN patients p ON p.id = rx.patient_id
            LEFT JOIN doctors d ON d.id = rx.doctor_id
            LEFT JOIN patient_visits v ON v.id = rx.visit_id
            WHERE rx.id = ?
        ");
        $stmt->execute([$id]);
        $prescription = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($prescription) {
            $prescription['items'] = $this->items($id);
        }

        return $prescription;
    }

    public function items($id)
    {
        $stmt = $this->db->prepare("
            SELECT pi.*, mi.current_stock, mi.minimum_stock
            FROM prescription_items pi
            LEFT JOIN medical_items mi ON mi.id = pi.product_id
            WHERE pi.prescription_id = ?
            ORDER BY pi.id ASC
        ");
        $stmt->execute([$id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createWithItems($data, $items)
    {
        $this->db->beginTransaction();

        try {
            $id = $this->create($data);
            $stmt = $this->db->prepare("
                INSERT INTO prescription_items
                (prescription_id, product_id, item_name, dosage, frequency, duration, quantity, unit_name, price, notes)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($items as $item) {
                if (trim($item['item_name'] ?? '') === '') {
                    continue;
                }

                $stmt->execute([
                    $id,
                    $item['product_id'] ?: null,
                    $item['item_name'],
                    $item['dosage'] ?? '',
                    $item['frequency'] ?? '',
                    $item['duration'] ?? '',
                    (float) ($item['quantity'] ?? 1),
                    $item['unit_name'] ?? 'unit',
                    (float) ($item['price'] ?? 0),
                    $item['notes'] ?? '',
                ]);
            }

            $this->db->commit();
            return $id;
        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function pendingForPharmacy()
    {
        $stmt = $this->db->query("
            SELECT rx.*, p.name AS patient_name, p.medical_record_no, d.name AS doctor_name, v.visit_no
            FROM prescriptions rx
            LEFT JOIN patients p ON p.id = rx.patient_id
            LEFT JOIN doctors d ON d.id = rx.doctor_id
            LEFT JOIN patient_visits v ON v.id = rx.visit_id
            WHERE rx.status IN ('pending','verified','prepared')
            ORDER BY rx.id ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

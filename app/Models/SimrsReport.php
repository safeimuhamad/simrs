<?php

class SimrsReport
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function dashboard()
    {
        $beds = $this->bedOccupancy();

        return [
            'patients_today' => $this->count("SELECT COUNT(DISTINCT patient_id) FROM patient_visits WHERE visit_date = CURDATE()"),
            'visits_today' => $this->count("SELECT COUNT(*) FROM patient_visits WHERE visit_date = CURDATE()"),
            'active_queue' => $this->count("SELECT COUNT(*) FROM visit_queue WHERE queue_date = CURDATE() AND status IN ('waiting','called','in_service')"),
            'completed_today' => $this->count("SELECT COUNT(*) FROM patient_visits WHERE visit_date = CURDATE() AND status = 'completed'"),
            'pending_prescriptions' => $this->count("SELECT COUNT(*) FROM prescriptions WHERE status IN ('pending','verified','prepared')"),
            'unpaid_billing' => $this->count("SELECT COUNT(*) FROM patient_billing WHERE status IN ('unpaid','partial')"),
            'income_today' => $this->sum("SELECT COALESCE(SUM(amount),0) FROM patient_payments WHERE payment_date = CURDATE()"),
            'critical_stock' => $this->count("SELECT COUNT(*) FROM medical_items WHERE status='active' AND current_stock <= minimum_stock"),
            'lab_pending' => $this->count("SELECT COUNT(*) FROM lab_orders WHERE status IN ('ordered','sample_taken')"),
            'inpatient_count' => $beds['occupied'],
            'bed_occupancy' => $beds,
            'visits_by_poly' => $this->visitsByPoly(),
            'visit_trend' => $this->visitTrend(),
            'recent_patients' => $this->recentPatients(),
            'income_trend' => $this->incomeTrend(),
            'notifications' => $this->notifications(),
        ];
    }

    public function visits($from, $to)
    {
        $stmt = $this->db->prepare("
            SELECT v.visit_date, v.status, p.name AS patient_name, p.medical_record_no, c.name AS polyclinic_name, d.name AS doctor_name
            FROM patient_visits v
            LEFT JOIN patients p ON p.id = v.patient_id
            LEFT JOIN polyclinics c ON c.id = v.polyclinic_id
            LEFT JOIN doctors d ON d.id = v.doctor_id
            WHERE v.visit_date BETWEEN ? AND ?
            ORDER BY v.visit_date DESC, v.id DESC
        ");
        $stmt->execute([$from, $to]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function income($from, $to)
    {
        $stmt = $this->db->prepare("
            SELECT pp.payment_date, b.billing_no, p.name AS patient_name, pp.payment_method, pp.amount
            FROM patient_payments pp
            LEFT JOIN patient_billing b ON b.id = pp.billing_id
            LEFT JOIN patients p ON p.id = pp.patient_id
            WHERE pp.payment_date BETWEEN ? AND ?
            ORDER BY pp.payment_date DESC, pp.id DESC
        ");
        $stmt->execute([$from, $to]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function pharmacy($from, $to)
    {
        $stmt = $this->db->prepare("
            SELECT pt.created_at, rx.prescription_no, p.name AS patient_name, pt.status, pt.total_amount
            FROM pharmacy_transactions pt
            LEFT JOIN prescriptions rx ON rx.id = pt.prescription_id
            LEFT JOIN patients p ON p.id = pt.patient_id
            WHERE DATE(pt.created_at) BETWEEN ? AND ?
            ORDER BY pt.created_at DESC
        ");
        $stmt->execute([$from, $to]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function visitsByPoly()
    {
        $stmt = $this->db->query("
            SELECT c.name, COUNT(v.id) AS total
            FROM polyclinics c
            LEFT JOIN patient_visits v ON v.polyclinic_id = c.id AND v.visit_date = CURDATE()
            GROUP BY c.id, c.name
            ORDER BY c.name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function visitTrend()
    {
        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $dates[$date] = [
                'label' => date('d M', strtotime($date)),
                'outpatient' => 0,
                'inpatient' => 0,
                'lab' => 0,
            ];
        }

        $stmt = $this->db->query("
            SELECT visit_date, COUNT(*) AS total
            FROM patient_visits
            WHERE visit_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND CURDATE()
            GROUP BY visit_date
        ");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (isset($dates[$row['visit_date']])) {
                $dates[$row['visit_date']]['outpatient'] = (int) $row['total'];
            }
        }

        $stmt = $this->db->query("
            SELECT admission_date, COUNT(*) AS total
            FROM inpatient_admissions
            WHERE admission_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND CURDATE()
            GROUP BY admission_date
        ");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (isset($dates[$row['admission_date']])) {
                $dates[$row['admission_date']]['inpatient'] = (int) $row['total'];
            }
        }

        $stmt = $this->db->query("
            SELECT order_date, COUNT(*) AS total
            FROM lab_orders
            WHERE order_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND CURDATE()
            GROUP BY order_date
        ");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (isset($dates[$row['order_date']])) {
                $dates[$row['order_date']]['lab'] = (int) $row['total'];
            }
        }

        return array_values($dates);
    }

    private function recentPatients()
    {
        $stmt = $this->db->query("
            SELECT v.status, p.medical_record_no, p.name AS patient_name, c.name AS polyclinic_name, d.name AS doctor_name
            FROM patient_visits v
            LEFT JOIN patients p ON p.id = v.patient_id
            LEFT JOIN polyclinics c ON c.id = v.polyclinic_id
            LEFT JOIN doctors d ON d.id = v.doctor_id
            ORDER BY v.visit_date DESC, v.id DESC
            LIMIT 5
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function incomeTrend()
    {
        $dates = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $dates[$date] = [
                'label' => date('d M', strtotime($date)),
                'amount' => 0,
            ];
        }

        $stmt = $this->db->query("
            SELECT payment_date, COALESCE(SUM(amount),0) AS total
            FROM patient_payments
            WHERE payment_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND CURDATE()
            GROUP BY payment_date
        ");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            if (isset($dates[$row['payment_date']])) {
                $dates[$row['payment_date']]['amount'] = (float) $row['total'];
            }
        }

        return array_values($dates);
    }

    private function bedOccupancy()
    {
        $total = $this->count("SELECT COUNT(*) FROM inpatient_beds WHERE status != 'inactive'");
        $occupied = $this->count("SELECT COUNT(*) FROM inpatient_beds WHERE status = 'occupied'");
        $available = max(0, $total - $occupied);

        $stmt = $this->db->query("
            SELECT class_name, COUNT(*) AS total, SUM(status = 'occupied') AS occupied
            FROM inpatient_beds
            WHERE status != 'inactive'
            GROUP BY class_name
            ORDER BY class_name ASC
        ");

        return [
            'total' => $total,
            'occupied' => $occupied,
            'available' => $available,
            'percent' => $total > 0 ? round(($occupied / $total) * 100) : 0,
            'classes' => $stmt->fetchAll(PDO::FETCH_ASSOC),
        ];
    }

    private function notifications()
    {
        return [
            [
                'title' => 'Resep Belum Diproses',
                'text' => $this->count("SELECT COUNT(*) FROM prescriptions WHERE status IN ('pending','verified','prepared')") . ' resep menunggu validasi farmasi',
                'icon' => 'medication',
                'bg' => '#fee2e2',
            ],
            [
                'title' => 'Antrean Aktif',
                'text' => $this->count("SELECT COUNT(*) FROM visit_queue WHERE queue_date = CURDATE() AND status IN ('waiting','called','in_service')") . ' pasien dalam antrean poli',
                'icon' => 'groups',
                'bg' => '#fff7ed',
            ],
            [
                'title' => 'Stok Obat Menipis',
                'text' => $this->count("SELECT COUNT(*) FROM medical_items WHERE status='active' AND current_stock <= minimum_stock") . ' item perlu restock',
                'icon' => 'inventory',
                'bg' => '#eff6ff',
            ],
            [
                'title' => 'Hasil Lab Pending',
                'text' => $this->count("SELECT COUNT(*) FROM lab_orders WHERE status IN ('ordered','sample_taken')") . ' order belum selesai',
                'icon' => 'science',
                'bg' => '#ecfdf5',
            ],
            [
                'title' => 'Tagihan Belum Dibayar',
                'text' => $this->count("SELECT COUNT(*) FROM patient_billing WHERE status IN ('unpaid','partial')") . ' tagihan pasien belum lunas',
                'icon' => 'receipt_long',
                'bg' => '#f3e8ff',
            ],
        ];
    }

    private function count($sql)
    {
        return (int) $this->db->query($sql)->fetchColumn();
    }

    private function sum($sql)
    {
        return (float) $this->db->query($sql)->fetchColumn();
    }
}

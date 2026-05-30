<?php

class RoleDashboard
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function registration(): array
    {
        return [
            'cards' => [
                ['label' => 'Pendaftaran Hari Ini', 'value' => $this->count("SELECT COUNT(*) FROM patient_visits WHERE visit_date = CURDATE()"), 'hint' => 'Kunjungan dibuat hari ini', 'icon' => 'app_registration', 'tone' => 'blue'],
                ['label' => 'Pasien Baru', 'value' => $this->count("SELECT COUNT(*) FROM patients WHERE DATE(created_at) = CURDATE()"), 'hint' => 'Master pasien baru', 'icon' => 'person_add', 'tone' => 'green'],
                ['label' => 'Antrean Menunggu', 'value' => $this->count("SELECT COUNT(*) FROM visit_queue WHERE queue_date = CURDATE() AND status IN ('waiting','called')"), 'hint' => 'Menunggu poli', 'icon' => 'groups', 'tone' => 'orange'],
                ['label' => 'Kunjungan Selesai', 'value' => $this->count("SELECT COUNT(*) FROM patient_visits WHERE visit_date = CURDATE() AND status IN ('paid','completed')"), 'hint' => 'Selesai layanan', 'icon' => 'task_alt', 'tone' => 'teal'],
            ],
            'sections' => [
                $this->visitSection('Pendaftaran Terbaru', "v.visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)", 10),
                $this->patientSection(),
            ],
            'quick' => [
                ['Pendaftaran Baru', 'simrs-registration-create', 'person_add'],
                ['Data Pasien', 'simrs-patients', 'personal_injury'],
                ['Antrean Poli', 'simrs-queue', 'groups'],
            ],
        ];
    }

    public function doctor(): array
    {
        $doctorId = $this->doctorIdForCurrentUser();
        $doctorFilter = $doctorId ? ' AND v.doctor_id = ' . (int) $doctorId : '';
        $rxFilter = $doctorId ? ' AND doctor_id = ' . (int) $doctorId : '';

        return [
            'cards' => [
                ['label' => 'Pasien Menunggu', 'value' => $this->count("SELECT COUNT(*) FROM patient_visits v WHERE v.visit_date = CURDATE() AND v.status = 'waiting'{$doctorFilter}"), 'hint' => 'Siap dipanggil', 'icon' => 'groups', 'tone' => 'orange'],
                ['label' => 'Dalam Pemeriksaan', 'value' => $this->count("SELECT COUNT(*) FROM patient_visits v WHERE v.visit_date = CURDATE() AND v.status = 'in_consultation'{$doctorFilter}"), 'hint' => 'Sedang ditangani', 'icon' => 'stethoscope', 'tone' => 'blue'],
                ['label' => 'Selesai Hari Ini', 'value' => $this->count("SELECT COUNT(*) FROM patient_visits v WHERE v.visit_date = CURDATE() AND v.status IN ('pharmacy','billing','paid','completed'){$doctorFilter}"), 'hint' => 'Sudah diperiksa', 'icon' => 'task_alt', 'tone' => 'green'],
                ['label' => 'Resep Dibuat', 'value' => $this->count("SELECT COUNT(*) FROM prescriptions WHERE DATE(created_at) = CURDATE(){$rxFilter}"), 'hint' => 'Resep hari ini', 'icon' => 'medication', 'tone' => 'purple'],
            ],
            'sections' => [
                $this->visitSection('Antrean Pemeriksaan Saya', "v.visit_date = CURDATE() AND v.status IN ('waiting','in_consultation','pharmacy','billing')" . ($doctorId ? " AND v.doctor_id = {$doctorId}" : ''), 12),
                $this->orderSection('Order Penunjang Terbaru', $doctorId),
            ],
            'quick' => [
                ['Rawat Jalan', 'simrs-outpatient', 'stethoscope'],
                ['Antrean', 'simrs-queue', 'groups'],
                ['Rekam Medis', 'simrs-outpatient', 'clinical_notes'],
            ],
        ];
    }

    public function nurse(): array
    {
        return [
            'cards' => [
                ['label' => 'Antrean Aktif', 'value' => $this->count("SELECT COUNT(*) FROM visit_queue WHERE queue_date = CURDATE() AND status IN ('waiting','called','in_service')"), 'hint' => 'Poli hari ini', 'icon' => 'groups', 'tone' => 'blue'],
                ['label' => 'Menunggu Dipanggil', 'value' => $this->count("SELECT COUNT(*) FROM visit_queue WHERE queue_date = CURDATE() AND status = 'waiting'"), 'hint' => 'Belum masuk layanan', 'icon' => 'schedule', 'tone' => 'orange'],
                ['label' => 'Dalam Layanan', 'value' => $this->count("SELECT COUNT(*) FROM visit_queue WHERE queue_date = CURDATE() AND status = 'in_service'"), 'hint' => 'Sedang dilayani', 'icon' => 'stethoscope', 'tone' => 'green'],
                ['label' => 'Order Lab Pending', 'value' => $this->count("SELECT COUNT(*) FROM lab_orders WHERE status IN ('ordered','sample_taken')"), 'hint' => 'Perlu tindak lanjut', 'icon' => 'science', 'tone' => 'purple'],
            ],
            'sections' => [
                $this->queueSection(),
                $this->visitSection('Rawat Jalan Aktif', "v.visit_date = CURDATE() AND v.status IN ('registered','waiting','in_consultation')", 10),
            ],
            'quick' => [
                ['Antrean', 'simrs-queue', 'groups'],
                ['Rawat Jalan', 'simrs-outpatient', 'stethoscope'],
                ['Laboratorium', 'simrs-laboratory', 'science'],
            ],
        ];
    }

    public function pharmacy(): array
    {
        return [
            'cards' => [
                ['label' => 'Resep Pending', 'value' => $this->count("SELECT COUNT(*) FROM prescriptions WHERE status = 'pending'"), 'hint' => 'Belum diverifikasi', 'icon' => 'medication', 'tone' => 'orange'],
                ['label' => 'Resep Disiapkan', 'value' => $this->count("SELECT COUNT(*) FROM prescriptions WHERE status IN ('verified','prepared')"), 'hint' => 'Dalam proses farmasi', 'icon' => 'inventory_2', 'tone' => 'blue'],
                ['label' => 'Serah Obat Hari Ini', 'value' => $this->count("SELECT COUNT(*) FROM prescriptions WHERE status = 'dispensed' AND DATE(dispensed_at) = CURDATE()"), 'hint' => 'Sudah diserahkan', 'icon' => 'task_alt', 'tone' => 'green'],
                ['label' => 'Stok Kritis', 'value' => $this->count("SELECT COUNT(*) FROM medical_items WHERE status='active' AND current_stock <= minimum_stock"), 'hint' => 'Di bawah minimum', 'icon' => 'warning', 'tone' => 'red'],
            ],
            'sections' => [
                $this->prescriptionSection(),
                $this->lowStockSection(),
            ],
            'quick' => [
                ['Farmasi', 'simrs-pharmacy', 'medication'],
                ['Inventori Farmasi', 'simrs-medical-items', 'inventory_2'],
            ],
        ];
    }

    public function cashier(): array
    {
        return [
            'cards' => [
                ['label' => 'Tagihan Belum Bayar', 'value' => $this->count("SELECT COUNT(*) FROM patient_billing WHERE status IN ('unpaid','partial')"), 'hint' => 'Perlu pembayaran', 'icon' => 'receipt_long', 'tone' => 'orange'],
                ['label' => 'Pembayaran Hari Ini', 'value' => $this->money($this->sum("SELECT COALESCE(SUM(amount),0) FROM patient_payments WHERE payment_date = CURDATE()")), 'hint' => 'Kasir pasien', 'icon' => 'point_of_sale', 'tone' => 'green'],
                ['label' => 'Transaksi Hari Ini', 'value' => $this->count("SELECT COUNT(*) FROM patient_payments WHERE payment_date = CURDATE()"), 'hint' => 'Jumlah pembayaran', 'icon' => 'payments', 'tone' => 'blue'],
                ['label' => 'Partial', 'value' => $this->count("SELECT COUNT(*) FROM patient_billing WHERE status = 'partial'"), 'hint' => 'Belum lunas penuh', 'icon' => 'pending_actions', 'tone' => 'purple'],
            ],
            'sections' => [
                $this->billingSection('Tagihan Aktif', "b.status IN ('unpaid','partial')", 12),
                $this->paymentSection(),
            ],
            'quick' => [
                ['Kasir', 'simrs-cashier', 'point_of_sale'],
                ['Billing', 'simrs-billing', 'receipt_long'],
            ],
        ];
    }

    public function finance(): array
    {
        return [
            'cards' => [
                ['label' => 'Pendapatan Pasien', 'value' => $this->money($this->sum("SELECT COALESCE(SUM(amount),0) FROM patient_payments WHERE payment_date = CURDATE()")), 'hint' => 'Hari ini', 'icon' => 'account_balance_wallet', 'tone' => 'green'],
                ['label' => 'Pendapatan Parkir', 'value' => $this->money($this->sum("SELECT COALESCE(SUM(amount),0) FROM parking_payments WHERE payment_date = CURDATE() AND status='paid'")), 'hint' => 'Hari ini', 'icon' => 'local_parking', 'tone' => 'teal'],
                ['label' => 'Piutang Pasien', 'value' => $this->money($this->sum("SELECT COALESCE(SUM(grand_total - paid_amount),0) FROM patient_billing WHERE status IN ('unpaid','partial')")), 'hint' => 'Belum dibayar', 'icon' => 'receipt_long', 'tone' => 'orange'],
                ['label' => 'Pendapatan Bulan Ini', 'value' => $this->money($this->sum("SELECT COALESCE(SUM(amount),0) FROM patient_payments WHERE payment_date BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND CURDATE()")), 'hint' => 'Pembayaran pasien', 'icon' => 'query_stats', 'tone' => 'blue'],
            ],
            'sections' => [
                $this->paymentSection(),
                $this->billingSection('Tagihan Belum Lunas', "b.status IN ('unpaid','partial')", 10),
            ],
            'quick' => [
                ['Laporan Pendapatan', 'simrs-reports-income', 'query_stats'],
                ['Billing', 'simrs-billing', 'receipt_long'],
                ['Parking Finance', 'parking-reports-income', 'local_parking'],
            ],
        ];
    }

    public function management(): array
    {
        $report = (new SimrsReport())->dashboard();
        return [
            'cards' => [
                ['label' => 'Kunjungan Hari Ini', 'value' => (int) $report['visits_today'], 'hint' => 'Total semua layanan', 'icon' => 'groups', 'tone' => 'blue'],
                ['label' => 'Antrean Aktif', 'value' => (int) $report['active_queue'], 'hint' => 'Sedang berjalan', 'icon' => 'schedule', 'tone' => 'orange'],
                ['label' => 'Pendapatan Hari Ini', 'value' => $this->money($report['income_today']), 'hint' => 'Pembayaran pasien', 'icon' => 'account_balance_wallet', 'tone' => 'green'],
                ['label' => 'BOR Rawat Inap', 'value' => (int) ($report['bed_occupancy']['percent'] ?? 0) . '%', 'hint' => 'Bed terisi', 'icon' => 'hotel', 'tone' => 'purple'],
            ],
            'sections' => [
                $this->polySection($report['visits_by_poly'] ?? []),
                $this->visitSection('Kunjungan Terbaru', "v.visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)", 10),
            ],
            'quick' => [
                ['Laporan Kunjungan', 'simrs-reports-visits', 'bar_chart'],
                ['Laporan Keuangan', 'simrs-reports-income', 'query_stats'],
                ['Laporan Farmasi', 'simrs-reports-pharmacy', 'medication'],
            ],
        ];
    }

    private function visitSection(string $title, string $where, int $limit): array
    {
        $stmt = $this->db->query("
            SELECT v.visit_no, v.visit_date, v.status, p.name AS patient_name, p.medical_record_no, c.name AS polyclinic_name, d.name AS doctor_name
            FROM patient_visits v
            LEFT JOIN patients p ON p.id = v.patient_id
            LEFT JOIN polyclinics c ON c.id = v.polyclinic_id
            LEFT JOIN doctors d ON d.id = v.doctor_id
            WHERE {$where}
            ORDER BY v.visit_date DESC, v.id DESC
            LIMIT {$limit}
        ");

        return [
            'title' => $title,
            'columns' => ['Kunjungan', 'Pasien', 'Poli', 'Dokter', 'Status'],
            'rows' => array_map(fn($row) => [
                htmlspecialchars($row['visit_no'] ?? '-'),
                htmlspecialchars($row['patient_name'] ?? '-') . '<br><small class="text-body">' . htmlspecialchars($row['medical_record_no'] ?? '-') . '</small>',
                htmlspecialchars($row['polyclinic_name'] ?? '-'),
                htmlspecialchars($row['doctor_name'] ?? '-'),
                simrsStatusBadge($row['status'] ?? ''),
            ], $stmt->fetchAll(PDO::FETCH_ASSOC)),
        ];
    }

    private function patientSection(): array
    {
        $stmt = $this->db->query("
            SELECT medical_record_no, name, gender, phone, created_at
            FROM patients
            ORDER BY id DESC
            LIMIT 8
        ");

        return [
            'title' => 'Pasien Terbaru',
            'columns' => ['No. RM', 'Nama', 'Gender', 'Telepon', 'Dibuat'],
            'rows' => array_map(fn($row) => [
                htmlspecialchars($row['medical_record_no'] ?? '-'),
                htmlspecialchars($row['name'] ?? '-'),
                htmlspecialchars($row['gender'] ?? '-'),
                htmlspecialchars($row['phone'] ?? '-'),
                htmlspecialchars(date('d/m/Y H:i', strtotime($row['created_at'] ?? 'now'))),
            ], $stmt->fetchAll(PDO::FETCH_ASSOC)),
        ];
    }

    private function queueSection(): array
    {
        $stmt = $this->db->query("
            SELECT q.queue_no, q.status AS queue_status, p.name AS patient_name, c.name AS polyclinic_name, d.name AS doctor_name
            FROM visit_queue q
            LEFT JOIN patient_visits v ON v.id = q.visit_id
            LEFT JOIN patients p ON p.id = v.patient_id
            LEFT JOIN polyclinics c ON c.id = q.polyclinic_id
            LEFT JOIN doctors d ON d.id = q.doctor_id
            WHERE q.queue_date = CURDATE()
            ORDER BY q.id DESC
            LIMIT 12
        ");

        return [
            'title' => 'Antrean Poli Hari Ini',
            'columns' => ['Nomor', 'Pasien', 'Poli', 'Dokter', 'Status'],
            'rows' => array_map(fn($row) => [
                htmlspecialchars($row['queue_no'] ?? '-'),
                htmlspecialchars($row['patient_name'] ?? '-'),
                htmlspecialchars($row['polyclinic_name'] ?? '-'),
                htmlspecialchars($row['doctor_name'] ?? '-'),
                simrsStatusBadge($row['queue_status'] ?? '', 'queue'),
            ], $stmt->fetchAll(PDO::FETCH_ASSOC)),
        ];
    }

    private function prescriptionSection(): array
    {
        $stmt = $this->db->query("
            SELECT rx.prescription_no, rx.status, p.name AS patient_name, p.medical_record_no, v.visit_no, d.name AS doctor_name
            FROM prescriptions rx
            LEFT JOIN patients p ON p.id = rx.patient_id
            LEFT JOIN patient_visits v ON v.id = rx.visit_id
            LEFT JOIN doctors d ON d.id = rx.doctor_id
            ORDER BY rx.created_at DESC
            LIMIT 10
        ");

        return [
            'title' => 'Resep Terbaru',
            'columns' => ['No. Resep', 'Pasien', 'Kunjungan', 'Dokter', 'Status'],
            'rows' => array_map(fn($row) => [
                htmlspecialchars($row['prescription_no'] ?? '-'),
                htmlspecialchars($row['patient_name'] ?? '-') . '<br><small class="text-body">' . htmlspecialchars($row['medical_record_no'] ?? '-') . '</small>',
                htmlspecialchars($row['visit_no'] ?? '-'),
                htmlspecialchars($row['doctor_name'] ?? '-'),
                simrsStatusBadge($row['status'] ?? '', 'prescription'),
            ], $stmt->fetchAll(PDO::FETCH_ASSOC)),
        ];
    }

    private function lowStockSection(): array
    {
        $rows = (new Pharmacy())->lowStock();
        return [
            'title' => '20 Stok Obat Kritis',
            'columns' => ['SKU', 'Nama Obat', 'Kategori', 'Stok', 'Minimum'],
            'rows' => array_map(fn($row) => [
                htmlspecialchars($row['sku'] ?? '-'),
                htmlspecialchars($row['name'] ?? '-'),
                htmlspecialchars($row['category'] ?? '-'),
                '<span class="default-badge bg-danger bg-opacity-10 text-danger">' . number_format((float) ($row['current_stock'] ?? 0), 0, ',', '.') . ' ' . htmlspecialchars($row['unit_name'] ?? '') . '</span>',
                htmlspecialchars(number_format((float) ($row['minimum_stock'] ?? 0), 0, ',', '.') . ' ' . ($row['unit_name'] ?? '')),
            ], $rows),
        ];
    }

    private function billingSection(string $title, string $where, int $limit): array
    {
        $stmt = $this->db->query("
            SELECT b.billing_no, b.billing_date, b.grand_total, b.paid_amount, b.status, p.name AS patient_name, p.medical_record_no
            FROM patient_billing b
            LEFT JOIN patients p ON p.id = b.patient_id
            WHERE {$where}
            ORDER BY b.id DESC
            LIMIT {$limit}
        ");

        return [
            'title' => $title,
            'columns' => ['Billing', 'Pasien', 'Tanggal', 'Total', 'Status'],
            'rows' => array_map(fn($row) => [
                htmlspecialchars($row['billing_no'] ?? '-'),
                htmlspecialchars($row['patient_name'] ?? '-') . '<br><small class="text-body">' . htmlspecialchars($row['medical_record_no'] ?? '-') . '</small>',
                htmlspecialchars(date('d/m/Y', strtotime($row['billing_date'] ?? 'now'))),
                htmlspecialchars($this->money($row['grand_total'] ?? 0)),
                simrsStatusBadge($row['status'] ?? '', 'billing'),
            ], $stmt->fetchAll(PDO::FETCH_ASSOC)),
        ];
    }

    private function paymentSection(): array
    {
        $stmt = $this->db->query("
            SELECT pp.payment_no, pp.payment_date, pp.payment_method, pp.amount, p.name AS patient_name
            FROM patient_payments pp
            LEFT JOIN patients p ON p.id = pp.patient_id
            ORDER BY pp.payment_date DESC, pp.id DESC
            LIMIT 10
        ");

        return [
            'title' => 'Pembayaran Terbaru',
            'columns' => ['No. Bayar', 'Pasien', 'Tanggal', 'Metode', 'Jumlah'],
            'rows' => array_map(fn($row) => [
                htmlspecialchars($row['payment_no'] ?? '-'),
                htmlspecialchars($row['patient_name'] ?? '-'),
                htmlspecialchars(date('d/m/Y', strtotime($row['payment_date'] ?? 'now'))),
                simrsStatusBadge($row['payment_method'] ?? '', 'payment'),
                htmlspecialchars($this->money($row['amount'] ?? 0)),
            ], $stmt->fetchAll(PDO::FETCH_ASSOC)),
        ];
    }

    private function orderSection(string $title, ?int $doctorId): array
    {
        $filter = $doctorId ? ' AND doctor_id = ' . (int) $doctorId : '';
        $stmt = $this->db->query("
            SELECT order_no, 'Laboratorium' AS layanan, status, order_date FROM lab_orders WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY){$filter}
            UNION ALL
            SELECT order_no, 'Radiologi' AS layanan, status, order_date FROM radiology_orders WHERE order_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY){$filter}
            ORDER BY order_date DESC
            LIMIT 10
        ");

        return [
            'title' => $title,
            'columns' => ['Order', 'Layanan', 'Tanggal', 'Status'],
            'rows' => array_map(fn($row) => [
                htmlspecialchars($row['order_no'] ?? '-'),
                htmlspecialchars($row['layanan'] ?? '-'),
                htmlspecialchars(date('d/m/Y', strtotime($row['order_date'] ?? 'now'))),
                simrsStatusBadge($row['status'] ?? '', 'order'),
            ], $stmt->fetchAll(PDO::FETCH_ASSOC)),
        ];
    }

    private function polySection(array $rows): array
    {
        return [
            'title' => 'Kunjungan per Poli Hari Ini',
            'columns' => ['Poli', 'Total Kunjungan'],
            'rows' => array_map(fn($row) => [
                htmlspecialchars($row['name'] ?? '-'),
                htmlspecialchars((string) (int) ($row['total'] ?? 0)),
            ], $rows),
        ];
    }

    private function doctorIdForCurrentUser(): ?int
    {
        $stmt = $this->db->prepare("SELECT id FROM doctors WHERE user_id = ? LIMIT 1");
        $stmt->execute([$_SESSION['user_id'] ?? 0]);
        $id = $stmt->fetchColumn();
        return $id ? (int) $id : null;
    }

    private function count(string $sql): int
    {
        return (int) $this->db->query($sql)->fetchColumn();
    }

    private function sum(string $sql): float
    {
        return (float) $this->db->query($sql)->fetchColumn();
    }

    private function money($value): string
    {
        return 'Rp ' . number_format((float) $value, 0, ',', '.');
    }
}

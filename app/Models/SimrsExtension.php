<?php

class SimrsExtension
{
    private PDO $db;

    private const COMMON_FIELDS = [
        'record_no',
        'patient_id',
        'visit_id',
        'medical_record_id',
        'billing_id',
        'reference_no',
        'module_type',
        'subject',
        'record_date',
        'status',
        'priority',
        'amount',
        'location',
        'assigned_to',
        'notes',
        'payload_json',
        'interoperability_status',
    ];

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public static function modules(): array
    {
        return [
            'simrs-emergency' => ['table' => 'emergency_cases', 'permission' => 'simrs_emergency.manage', 'title' => 'Emergency / IGD', 'subtitle' => 'Triase, kasus IGD, status stabilisasi, dan rujukan internal.', 'prefix' => 'IGD', 'icon' => 'emergency', 'group' => 'clinical', 'types' => ['Triase merah', 'Triase kuning', 'Triase hijau', 'Observasi IGD'], 'statuses' => ['waiting', 'triage', 'treatment', 'observation', 'admitted', 'referred', 'completed', 'cancelled']],
            'simrs-bed-management' => ['table' => 'bed_management_details', 'permission' => 'simrs_bed.manage', 'title' => 'Bed Management Detail', 'subtitle' => 'Ketersediaan bed, alokasi pasien, housekeeping, dan status kamar.', 'prefix' => 'BED', 'icon' => 'bed', 'group' => 'clinical', 'types' => ['Kelas 1', 'Kelas 2', 'Kelas 3', 'VIP', 'ICU', 'NICU'], 'statuses' => ['available', 'reserved', 'occupied', 'cleaning', 'maintenance', 'blocked']],
            'simrs-nurse-station' => ['table' => 'nurse_station_tasks', 'permission' => 'simrs_nurse_station.manage', 'title' => 'Nurse Station', 'subtitle' => 'Tugas perawat, handover, observasi, dan instruksi klinis.', 'prefix' => 'NS', 'icon' => 'clinical_notes', 'group' => 'clinical', 'types' => ['Handover', 'Observasi', 'Medication', 'Edukasi pasien', 'Nursing care'], 'statuses' => ['open', 'in_progress', 'done', 'verified', 'cancelled']],
            'simrs-vital-signs' => ['table' => 'vital_sign_records', 'permission' => 'simrs_vital_sign.manage', 'title' => 'Vital Sign', 'subtitle' => 'Tanda vital pasien yang terhubung ke kunjungan dan EMR.', 'prefix' => 'VIT', 'icon' => 'monitor_heart', 'group' => 'clinical', 'types' => ['Tekanan darah', 'Nadi', 'Suhu', 'Respirasi', 'SpO2', 'Skor nyeri'], 'statuses' => ['draft', 'recorded', 'reviewed', 'corrected']],
            'simrs-operating-room' => ['table' => 'operating_room_schedules', 'permission' => 'simrs_operating_room.manage', 'title' => 'Operating Room / OK', 'subtitle' => 'Jadwal operasi, tim OK, kamar operasi, dan status tindakan.', 'prefix' => 'OK', 'icon' => 'medical_services', 'group' => 'clinical', 'types' => ['Elektif', 'Cito', 'Minor surgery', 'Mayor surgery'], 'statuses' => ['scheduled', 'preparation', 'in_surgery', 'recovery', 'completed', 'cancelled']],
            'simrs-icu-nicu' => ['table' => 'intensive_care_records', 'permission' => 'simrs_icu.manage', 'title' => 'ICU / NICU', 'subtitle' => 'Monitoring intensif pasien ICU/NICU dan kebutuhan ventilator.', 'prefix' => 'ICU', 'icon' => 'health_and_safety', 'group' => 'clinical', 'types' => ['ICU', 'NICU', 'PICU', 'HCU'], 'statuses' => ['admitted', 'monitoring', 'step_down', 'transferred', 'discharged']],
            'simrs-bpjs-bridging' => ['table' => 'bpjs_bridge_logs', 'permission' => 'simrs_bpjs.manage', 'title' => 'BPJS Bridging Readiness', 'subtitle' => 'Log kesiapan VClaim, antrean online, SEP, rujukan, dan Aplicares.', 'prefix' => 'BPJS', 'icon' => 'hub', 'group' => 'integration', 'types' => ['VClaim SEP', 'Antrean Online', 'Aplicares', 'Rujukan', 'Klaim'], 'statuses' => ['draft', 'queued', 'sent', 'success', 'failed', 'retry']],
            'simrs-satusehat' => ['table' => 'satusehat_exchange_logs', 'permission' => 'simrs_satusehat.manage', 'title' => 'SATUSEHAT Readiness', 'subtitle' => 'Persiapan payload HL7 FHIR untuk Patient, Encounter, Condition, Observation.', 'prefix' => 'FHIR', 'icon' => 'sync_alt', 'group' => 'integration', 'types' => ['Patient', 'Encounter', 'Condition', 'Observation', 'MedicationRequest', 'DiagnosticReport'], 'statuses' => ['draft', 'mapped', 'queued', 'sent', 'accepted', 'failed']],
            'simrs-insurance-claims' => ['table' => 'insurance_claims', 'permission' => 'simrs_insurance_claim.manage', 'title' => 'Insurance Claim', 'subtitle' => 'Klaim asuransi pasien, dokumen pendukung, status submit, dan pembayaran.', 'prefix' => 'CLM', 'icon' => 'verified_user', 'group' => 'finance', 'types' => ['BPJS', 'Asuransi swasta', 'Perusahaan', 'Mandiri reimbursement'], 'statuses' => ['draft', 'submitted', 'verified', 'approved', 'rejected', 'paid', 'cancelled']],
            'simrs-payment-gateway-logs' => ['table' => 'payment_gateway_logs', 'permission' => 'simrs_payment_gateway.manage', 'title' => 'Payment Gateway Log', 'subtitle' => 'Log QRIS, e-money, VA, kartu, dan callback pembayaran.', 'prefix' => 'PG', 'icon' => 'payments', 'group' => 'finance', 'types' => ['QRIS', 'Virtual Account', 'E-Money', 'Debit Card', 'Credit Card', 'Callback'], 'statuses' => ['pending', 'paid', 'expired', 'failed', 'refunded', 'reconciled']],
            'simrs-assets' => ['table' => 'hospital_assets', 'permission' => 'simrs_asset.manage', 'title' => 'Asset Management RS', 'subtitle' => 'Aset medis/nonmedis, lokasi, penanggung jawab, dan jadwal maintenance.', 'prefix' => 'AST', 'icon' => 'inventory', 'group' => 'operation', 'types' => ['Alat medis', 'IT', 'Furniture', 'Ambulance equipment', 'Facility'], 'statuses' => ['active', 'maintenance', 'broken', 'retired', 'lost']],
            'simrs-ambulance' => ['table' => 'ambulance_trips', 'permission' => 'simrs_ambulance.manage', 'title' => 'Ambulance Management', 'subtitle' => 'Permintaan ambulance, driver, tujuan, biaya, dan status perjalanan.', 'prefix' => 'AMB', 'icon' => 'ambulance', 'group' => 'operation', 'types' => ['Emergency pickup', 'Transfer pasien', 'Jenazah', 'Standby event', 'Operasional'], 'statuses' => ['requested', 'assigned', 'departed', 'arrived', 'completed', 'cancelled']],
            'simrs-employee-shifts' => ['table' => 'employee_shifts', 'permission' => 'simrs_hris.manage', 'title' => 'HRIS / Shift Pegawai', 'subtitle' => 'Jadwal shift pegawai medis dan nonmedis.', 'prefix' => 'SFT', 'icon' => 'badge', 'group' => 'operation', 'types' => ['Pagi', 'Siang', 'Malam', 'On call', 'Libur'], 'statuses' => ['scheduled', 'checked_in', 'checked_out', 'absent', 'changed']],
            'simrs-stock-opnames' => ['table' => 'stock_opnames', 'permission' => 'simrs_stock_opname.manage', 'title' => 'Stock Opname Obat', 'subtitle' => 'Stock opname obat/alkes terhubung ke inventori farmasi.', 'prefix' => 'SO', 'icon' => 'fact_check', 'group' => 'pharmacy', 'types' => ['Obat', 'Alkes', 'BMHP', 'Narkotika', 'Psikotropika'], 'statuses' => ['draft', 'counting', 'variance', 'approved', 'posted', 'cancelled']],
            'simrs-pharmacy-purchase-requests' => ['table' => 'pharmacy_purchase_requests', 'permission' => 'simrs_pharmacy_pr.manage', 'title' => 'Purchase Request Obat / Alkes', 'subtitle' => 'Permintaan pembelian obat/alkes dari farmasi ke purchasing.', 'prefix' => 'PRF', 'icon' => 'shopping_cart', 'group' => 'pharmacy', 'types' => ['Obat', 'Alkes', 'BMHP', 'Reagen', 'Emergency stock'], 'statuses' => ['draft', 'submitted', 'approved', 'ordered', 'received', 'cancelled']],
            'simrs-pharmacy-vendors' => ['table' => 'pharmacy_vendors', 'permission' => 'simrs_pharmacy_vendor.manage', 'title' => 'Supplier / Vendor Farmasi', 'subtitle' => 'Master vendor farmasi, izin, kontak, kategori, dan status aktif.', 'prefix' => 'VFR', 'icon' => 'local_shipping', 'group' => 'pharmacy', 'types' => ['Distributor obat', 'Alkes', 'Reagen lab', 'BMHP', 'Konsinyasi'], 'statuses' => ['active', 'inactive', 'blocked', 'under_review']],
            'simrs-lab-results' => ['table' => 'lab_result_details', 'permission' => 'simrs_lab_result.manage', 'title' => 'Lab Result Detail', 'subtitle' => 'Detail hasil lab, nilai rujukan, validasi, dan lampiran hasil.', 'prefix' => 'LBR', 'icon' => 'biotech', 'group' => 'diagnostic', 'types' => ['Hematologi', 'Kimia klinik', 'Imunologi', 'Mikrobiologi', 'Urinalisa'], 'statuses' => ['draft', 'validated', 'released', 'revised', 'cancelled']],
            'simrs-radiology-results' => ['table' => 'radiology_result_details', 'permission' => 'simrs_radiology_result.manage', 'title' => 'Radiology Result Detail', 'subtitle' => 'Hasil radiologi, ekspertise, DICOM reference, dan validasi dokter.', 'prefix' => 'RADR', 'icon' => 'image_search', 'group' => 'diagnostic', 'types' => ['X-Ray', 'USG', 'CT Scan', 'MRI', 'Dental panoramic'], 'statuses' => ['draft', 'reviewed', 'released', 'revised', 'cancelled']],
            'simrs-medical-documents' => ['table' => 'medical_documents', 'permission' => 'simrs_medical_document.manage', 'title' => 'Medical Document / Informed Consent', 'subtitle' => 'Dokumen medis, informed consent, upload, dan tanda tangan pasien.', 'prefix' => 'DOC', 'icon' => 'description', 'group' => 'document', 'types' => ['Informed consent', 'Resume medis', 'Surat kontrol', 'Surat rujukan', 'Lampiran klaim'], 'statuses' => ['draft', 'signed', 'uploaded', 'verified', 'archived', 'cancelled']],
            'simrs-notifications' => ['table' => 'notification_reminders', 'permission' => 'simrs_notification.manage', 'title' => 'Notification / Reminder', 'subtitle' => 'Reminder kontrol, jadwal operasi, obat, pembayaran, dan follow-up pasien.', 'prefix' => 'NTF', 'icon' => 'notifications', 'group' => 'document', 'types' => ['Kontrol', 'Jadwal dokter', 'Pembayaran', 'Obat', 'Operasi', 'Follow-up'], 'statuses' => ['scheduled', 'sent', 'delivered', 'read', 'failed', 'cancelled']],
        ];
    }

    public static function config(string $route): ?array
    {
        $modules = self::modules();
        if (!isset($modules[$route])) {
            return null;
        }

        return ['route' => $route] + $modules[$route];
    }

    public function paginate(array $module, string $search, int $limit, int $offset): array
    {
        [$where, $params] = $this->where($search);
        $table = $module['table'];
        $stmt = $this->db->prepare("
            SELECT e.*, p.name AS patient_name, p.medical_record_no, v.visit_no, b.billing_no
            FROM {$table} e
            LEFT JOIN patients p ON p.id = e.patient_id
            LEFT JOIN patient_visits v ON v.id = e.visit_id
            LEFT JOIN patient_billing b ON b.id = e.billing_id
            {$where}
            ORDER BY e.id DESC
            LIMIT ? OFFSET ?
        ");
        $index = 1;
        foreach ($params as $param) {
            $stmt->bindValue($index++, $param);
        }
        $stmt->bindValue($index++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($index, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count(array $module, string $search): int
    {
        [$where, $params] = $this->where($search);
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$module['table']} e LEFT JOIN patients p ON p.id = e.patient_id LEFT JOIN patient_visits v ON v.id = e.visit_id {$where}");
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function find(array $module, $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT e.*, p.name AS patient_name, p.medical_record_no, v.visit_no, b.billing_no
            FROM {$module['table']} e
            LEFT JOIN patients p ON p.id = e.patient_id
            LEFT JOIN patient_visits v ON v.id = e.visit_id
            LEFT JOIN patient_billing b ON b.id = e.billing_id
            WHERE e.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function create(array $module, array $data): int
    {
        $data = $this->filter($data);
        if (empty($data['record_no'])) {
            $data['record_no'] = SimrsNumber::make($module['table'], 'record_no', $module['prefix']);
        }
        $data['created_by'] = $_SESSION['user_id'] ?? null;
        $columns = array_keys($data);
        $stmt = $this->db->prepare("INSERT INTO {$module['table']} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', array_fill(0, count($columns), '?')) . ")");
        $stmt->execute(array_values($data));

        return (int) $this->db->lastInsertId();
    }

    public function update(array $module, $id, array $data): bool
    {
        $data = $this->filter($data);
        $data['updated_by'] = $_SESSION['user_id'] ?? null;
        $sets = implode(', ', array_map(fn($column) => "{$column} = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $id;

        return $this->db->prepare("UPDATE {$module['table']} SET {$sets} WHERE id = ?")->execute($values);
    }

    public function patients(): array
    {
        return $this->db->query("SELECT id, medical_record_no, name FROM patients ORDER BY name ASC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function visits(): array
    {
        return $this->db->query("
            SELECT v.id, v.visit_no, p.name AS patient_name, c.name AS polyclinic_name
            FROM patient_visits v
            LEFT JOIN patients p ON p.id = v.patient_id
            LEFT JOIN polyclinics c ON c.id = v.polyclinic_id
            ORDER BY v.id DESC
            LIMIT 500
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function billings(): array
    {
        return $this->db->query("SELECT id, billing_no, total_amount, status FROM patient_billing ORDER BY id DESC LIMIT 500")->fetchAll(PDO::FETCH_ASSOC);
    }

    private function filter(array $data): array
    {
        $filtered = [];
        foreach (array_merge(self::COMMON_FIELDS, ['created_by', 'updated_by']) as $field) {
            if (array_key_exists($field, $data)) {
                $filtered[$field] = $data[$field] === '' ? null : $data[$field];
            }
        }
        return $filtered;
    }

    private function where(string $search): array
    {
        $clauses = [];
        $params = [];
        $status = trim((string) ($_GET['status'] ?? ''));
        $type = trim((string) ($_GET['module_type'] ?? ''));

        if ($search !== '') {
            $like = '%' . $search . '%';
            $clauses[] = "(e.record_no LIKE ? OR e.reference_no LIKE ? OR e.module_type LIKE ? OR e.subject LIKE ? OR e.location LIKE ? OR e.assigned_to LIKE ? OR e.notes LIKE ? OR p.name LIKE ? OR p.medical_record_no LIKE ? OR v.visit_no LIKE ?)";
            $params = array_merge($params, array_fill(0, 10, $like));
        }
        if ($status !== '') {
            $clauses[] = 'e.status = ?';
            $params[] = $status;
        }
        if ($type !== '') {
            $clauses[] = 'e.module_type = ?';
            $params[] = $type;
        }

        return [$clauses ? 'WHERE ' . implode(' AND ', $clauses) : '', $params];
    }
}

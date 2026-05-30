-- SIMRS Phase 2 Extensions
-- Modul tambahan tanpa mengubah modul SIMRS/Parking yang sudah berjalan.
-- Tabel dibuat konsisten agar mudah dipakai untuk BPJS/SATUSEHAT readiness dan integrasi billing/EMR.

DROP TABLE IF EXISTS _simrs_extension_template;
CREATE TABLE _simrs_extension_template (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    record_no VARCHAR(50) NOT NULL,
    patient_id BIGINT UNSIGNED NULL,
    visit_id BIGINT UNSIGNED NULL,
    medical_record_id BIGINT UNSIGNED NULL,
    billing_id BIGINT UNSIGNED NULL,
    reference_no VARCHAR(100) NULL,
    module_type VARCHAR(100) NULL,
    subject VARCHAR(255) NOT NULL,
    record_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) NOT NULL DEFAULT 'draft',
    priority VARCHAR(30) NOT NULL DEFAULT 'normal',
    amount DECIMAL(18,2) NULL DEFAULT 0,
    location VARCHAR(150) NULL,
    assigned_to VARCHAR(150) NULL,
    notes TEXT NULL,
    payload_json JSON NULL,
    interoperability_status VARCHAR(50) NOT NULL DEFAULT 'not_ready',
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_record_no (record_no),
    KEY idx_patient (patient_id),
    KEY idx_visit (visit_id),
    KEY idx_billing (billing_id),
    KEY idx_status (status),
    KEY idx_date (record_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS emergency_cases LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS bed_management_details LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS nurse_station_tasks LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS vital_sign_records LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS operating_room_schedules LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS intensive_care_records LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS bpjs_bridge_logs LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS satusehat_exchange_logs LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS insurance_claims LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS payment_gateway_logs LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS hospital_assets LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS ambulance_trips LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS employee_shifts LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS stock_opnames LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS pharmacy_purchase_requests LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS pharmacy_vendors LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS lab_result_details LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS radiology_result_details LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS medical_documents LIKE _simrs_extension_template;
CREATE TABLE IF NOT EXISTS notification_reminders LIKE _simrs_extension_template;

DROP TABLE IF EXISTS _simrs_extension_template;

INSERT INTO permissions (module, action_name, permission_key)
SELECT module, action_name, permission_key
FROM (
    SELECT 'simrs_emergency' module, 'manage' action_name, 'simrs_emergency.manage' permission_key UNION ALL
    SELECT 'simrs_bed', 'manage', 'simrs_bed.manage' UNION ALL
    SELECT 'simrs_nurse_station', 'manage', 'simrs_nurse_station.manage' UNION ALL
    SELECT 'simrs_vital_sign', 'manage', 'simrs_vital_sign.manage' UNION ALL
    SELECT 'simrs_operating_room', 'manage', 'simrs_operating_room.manage' UNION ALL
    SELECT 'simrs_icu', 'manage', 'simrs_icu.manage' UNION ALL
    SELECT 'simrs_bpjs', 'manage', 'simrs_bpjs.manage' UNION ALL
    SELECT 'simrs_satusehat', 'manage', 'simrs_satusehat.manage' UNION ALL
    SELECT 'simrs_insurance_claim', 'manage', 'simrs_insurance_claim.manage' UNION ALL
    SELECT 'simrs_payment_gateway', 'manage', 'simrs_payment_gateway.manage' UNION ALL
    SELECT 'simrs_asset', 'manage', 'simrs_asset.manage' UNION ALL
    SELECT 'simrs_ambulance', 'manage', 'simrs_ambulance.manage' UNION ALL
    SELECT 'simrs_hris', 'manage', 'simrs_hris.manage' UNION ALL
    SELECT 'simrs_stock_opname', 'manage', 'simrs_stock_opname.manage' UNION ALL
    SELECT 'simrs_pharmacy_pr', 'manage', 'simrs_pharmacy_pr.manage' UNION ALL
    SELECT 'simrs_pharmacy_vendor', 'manage', 'simrs_pharmacy_vendor.manage' UNION ALL
    SELECT 'simrs_lab_result', 'manage', 'simrs_lab_result.manage' UNION ALL
    SELECT 'simrs_radiology_result', 'manage', 'simrs_radiology_result.manage' UNION ALL
    SELECT 'simrs_medical_document', 'manage', 'simrs_medical_document.manage' UNION ALL
    SELECT 'simrs_notification', 'manage', 'simrs_notification.manage'
) p
WHERE NOT EXISTS (SELECT 1 FROM permissions existing WHERE existing.permission_key = p.permission_key);

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN (
    'simrs_emergency.manage','simrs_bed.manage','simrs_nurse_station.manage','simrs_vital_sign.manage',
    'simrs_operating_room.manage','simrs_icu.manage','simrs_bpjs.manage','simrs_satusehat.manage',
    'simrs_insurance_claim.manage','simrs_payment_gateway.manage','simrs_asset.manage','simrs_ambulance.manage',
    'simrs_hris.manage','simrs_stock_opname.manage','simrs_pharmacy_pr.manage','simrs_pharmacy_vendor.manage',
    'simrs_lab_result.manage','simrs_radiology_result.manage','simrs_medical_document.manage','simrs_notification.manage'
)
WHERE r.name IN ('super_admin','admin_rs');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('simrs_emergency.manage','simrs_bed.manage','simrs_nurse_station.manage','simrs_vital_sign.manage','simrs_icu.manage','simrs_medical_document.manage','simrs_notification.manage')
WHERE r.name = 'perawat';

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('simrs_emergency.manage','simrs_vital_sign.manage','simrs_operating_room.manage','simrs_icu.manage','simrs_lab_result.manage','simrs_radiology_result.manage','simrs_medical_document.manage','simrs_notification.manage')
WHERE r.name = 'dokter';

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('simrs_stock_opname.manage','simrs_pharmacy_pr.manage','simrs_pharmacy_vendor.manage')
WHERE r.name = 'farmasi';

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('simrs_insurance_claim.manage','simrs_payment_gateway.manage')
WHERE r.name IN ('kasir','finance');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('simrs_bpjs.manage','simrs_satusehat.manage','simrs_insurance_claim.manage','simrs_asset.manage','simrs_ambulance.manage','simrs_hris.manage','simrs_notification.manage')
WHERE r.name IN ('manajemen','management');

SET @patient1 = (SELECT id FROM patients ORDER BY id LIMIT 1);
SET @patient2 = (SELECT id FROM patients ORDER BY id LIMIT 1 OFFSET 1);
SET @visit1 = (SELECT id FROM patient_visits ORDER BY id DESC LIMIT 1);
SET @visit2 = (SELECT id FROM patient_visits ORDER BY id DESC LIMIT 1 OFFSET 1);
SET @billing1 = (SELECT id FROM patient_billing ORDER BY id DESC LIMIT 1);
SET @user1 = (SELECT id FROM users WHERE email = 'superadmin@simrs.test' LIMIT 1);

INSERT IGNORE INTO emergency_cases (record_no, patient_id, visit_id, module_type, subject, status, priority, location, assigned_to, notes, payload_json, interoperability_status, created_by)
VALUES
('IGD-20260529-0001', @patient1, @visit1, 'Triase kuning', 'Nyeri dada dan sesak napas', 'treatment', 'urgent', 'IGD Bed 03', 'dr. Budi Santoso', 'EKG dan observasi tanda vital, siap dibuat Encounter FHIR.', JSON_OBJECT('resourceType','Encounter','class','EMER'), 'mapped', @user1),
('IGD-20260529-0002', @patient2, @visit2, 'Observasi IGD', 'Demam tinggi dan dehidrasi', 'observation', 'high', 'IGD Observasi 01', 'Perawat IGD', 'Cairan IV dan monitoring suhu.', JSON_OBJECT('triage','yellow'), 'not_ready', @user1);

INSERT IGNORE INTO bed_management_details (record_no, patient_id, visit_id, module_type, subject, status, priority, location, assigned_to, notes, created_by)
VALUES
('BED-20260529-0001', @patient1, @visit1, 'ICU', 'Reservasi ICU Bed 02', 'reserved', 'critical', 'ICU Bed 02', 'Nurse Station ICU', 'Menunggu konfirmasi DPJP.', @user1),
('BED-20260529-0002', NULL, NULL, 'Kelas 2', 'Bed Kelas 2-12 siap digunakan', 'available', 'normal', 'Lantai 3 Kamar 312', 'Housekeeping', 'Sudah dibersihkan.', @user1);

INSERT IGNORE INTO nurse_station_tasks (record_no, patient_id, visit_id, module_type, subject, status, priority, location, assigned_to, notes, created_by)
VALUES
('NS-20260529-0001', @patient1, @visit1, 'Observasi', 'Observasi tanda vital tiap 2 jam', 'in_progress', 'high', 'Rawat Jalan / Observasi', 'Perawat Sinta', 'Catat tekanan darah, nadi, suhu, SpO2.', @user1),
('NS-20260529-0002', @patient2, @visit2, 'Edukasi pasien', 'Edukasi obat pulang', 'open', 'normal', 'Poli Anak', 'Perawat Poli', 'Edukasi jadwal minum obat.', @user1);

INSERT IGNORE INTO vital_sign_records (record_no, patient_id, visit_id, module_type, subject, status, priority, location, assigned_to, notes, payload_json, interoperability_status, created_by)
VALUES
('VIT-20260529-0001', @patient1, @visit1, 'Tekanan darah', 'TD 150/95, Nadi 104, SpO2 96%', 'recorded', 'high', 'IGD Bed 03', 'Perawat Sinta', 'Perlu review dokter.', JSON_OBJECT('systolic',150,'diastolic',95,'pulse',104,'spo2',96), 'mapped', @user1),
('VIT-20260529-0002', @patient2, @visit2, 'Suhu', 'Suhu 38.8 C', 'recorded', 'normal', 'Poli Anak', 'Perawat Poli', 'Observasi demam.', JSON_OBJECT('temperature',38.8), 'mapped', @user1);

INSERT IGNORE INTO operating_room_schedules (record_no, patient_id, visit_id, module_type, subject, status, priority, location, assigned_to, notes, created_by)
VALUES
('OK-20260529-0001', @patient1, @visit1, 'Cito', 'Debridement luka infeksi', 'scheduled', 'urgent', 'OK 1', 'Tim Bedah A', 'Persiapan anestesi dan informed consent.', @user1);

INSERT IGNORE INTO intensive_care_records (record_no, patient_id, visit_id, module_type, subject, status, priority, location, assigned_to, notes, created_by)
VALUES
('ICU-20260529-0001', @patient1, @visit1, 'ICU', 'Monitoring pasca tindakan', 'monitoring', 'critical', 'ICU Bed 02', 'dr. Intensivist', 'Pantau ventilator dan balance cairan.', @user1);

INSERT IGNORE INTO bpjs_bridge_logs (record_no, patient_id, visit_id, reference_no, module_type, subject, status, priority, notes, payload_json, interoperability_status, created_by)
VALUES
('BPJS-20260529-0001', @patient1, @visit1, 'SEP-DRAFT-0001', 'VClaim SEP', 'Draft SEP rawat jalan', 'draft', 'normal', 'Struktur disiapkan untuk VClaim, belum call API.', JSON_OBJECT('bridging','VClaim','endpoint','SEP'), 'mapped', @user1);

INSERT IGNORE INTO satusehat_exchange_logs (record_no, patient_id, visit_id, reference_no, module_type, subject, status, priority, notes, payload_json, interoperability_status, created_by)
VALUES
('FHIR-20260529-0001', @patient1, @visit1, 'Encounter-DRAFT-0001', 'Encounter', 'Draft export Encounter kunjungan', 'mapped', 'normal', 'Payload HL7 FHIR disiapkan, belum dikirim.', JSON_OBJECT('resourceType','Encounter','status','in-progress'), 'mapped', @user1);

INSERT IGNORE INTO insurance_claims (record_no, patient_id, visit_id, billing_id, reference_no, module_type, subject, status, amount, priority, notes, created_by)
VALUES
('CLM-20260529-0001', @patient1, @visit1, @billing1, 'CLAIM-DRAFT-0001', 'Asuransi swasta', 'Klaim rawat jalan perusahaan', 'submitted', 750000, 'normal', 'Dokumen billing dan resume medis perlu diverifikasi.', @user1);

INSERT IGNORE INTO payment_gateway_logs (record_no, patient_id, visit_id, billing_id, reference_no, module_type, subject, status, amount, priority, payload_json, created_by)
VALUES
('PG-20260529-0001', @patient1, @visit1, @billing1, 'QRIS-SIMRS-0001', 'QRIS', 'Callback pembayaran QRIS billing pasien', 'paid', 350000, 'normal', JSON_OBJECT('channel','qris','callback_status','PAID'), @user1);

INSERT IGNORE INTO hospital_assets (record_no, module_type, subject, status, priority, location, assigned_to, notes, created_by)
VALUES
('AST-20260529-0001', 'Alat medis', 'Patient monitor ICU PM-ICU-002', 'active', 'normal', 'ICU Bed 02', 'Teknisi Elektromedis', 'Kalibrasi berikutnya 2026-07-01.', @user1),
('AST-20260529-0002', 'IT', 'Printer gelang pasien admisi', 'maintenance', 'high', 'Pendaftaran', 'IT Support', 'Roller printer perlu diganti.', @user1);

INSERT IGNORE INTO ambulance_trips (record_no, patient_id, visit_id, module_type, subject, status, priority, location, assigned_to, amount, notes, created_by)
VALUES
('AMB-20260529-0001', @patient2, @visit2, 'Transfer pasien', 'Transfer pasien ke RS rujukan', 'assigned', 'urgent', 'IGD ke RS Rujukan', 'Driver Andi', 250000, 'Ambulance 01 siap berangkat.', @user1);

INSERT IGNORE INTO employee_shifts (record_no, module_type, subject, status, priority, location, assigned_to, notes, created_by)
VALUES
('SFT-20260529-0001', 'Pagi', 'Shift perawat IGD 07:00-14:00', 'scheduled', 'normal', 'IGD', 'Perawat Sinta', 'Jadwal reguler.', @user1),
('SFT-20260529-0002', 'Malam', 'Shift farmasi 21:00-07:00', 'scheduled', 'normal', 'Farmasi', 'Apt. Sari Lestari', 'Jadwal malam.', @user1);

INSERT IGNORE INTO stock_opnames (record_no, reference_no, module_type, subject, status, priority, location, assigned_to, notes, payload_json, created_by)
VALUES
('SO-20260529-0001', 'SKU-PCT-500', 'Obat', 'Stock opname Paracetamol 500 mg', 'counting', 'normal', 'Gudang Farmasi', 'Apt. Sari Lestari', 'Hitung fisik dan cocokkan stok sistem.', JSON_OBJECT('system_stock',12,'minimum_stock',50), @user1);

INSERT IGNORE INTO pharmacy_purchase_requests (record_no, reference_no, module_type, subject, status, priority, amount, location, assigned_to, notes, payload_json, created_by)
VALUES
('PRF-20260529-0001', 'SKU-PCT-500', 'Emergency stock', 'PR Paracetamol 500 mg 500 strip', 'submitted', 'high', 2500000, 'Gudang Farmasi', 'Purchasing Farmasi', 'Dibuat dari stok kritis.', JSON_OBJECT('item','Paracetamol 500 mg','qty',500), @user1);

INSERT IGNORE INTO pharmacy_vendors (record_no, reference_no, module_type, subject, status, priority, location, assigned_to, notes, created_by)
VALUES
('VFR-20260529-0001', 'PBF-001', 'Distributor obat', 'PT Sehat Farma Distribusi', 'active', 'normal', 'Jakarta', 'Procurement', 'PBF aktif, lead time 2 hari.', @user1);

INSERT IGNORE INTO lab_result_details (record_no, patient_id, visit_id, reference_no, module_type, subject, status, priority, assigned_to, notes, payload_json, interoperability_status, created_by)
VALUES
('LBR-20260529-0001', @patient1, @visit1, 'LAB-DRAFT-0001', 'Hematologi', 'Hb 13.2, Leukosit 9800, Trombosit 250000', 'validated', 'normal', 'Analis Lab', 'Hasil siap dirilis ke dokter.', JSON_OBJECT('resourceType','DiagnosticReport','category','LAB'), 'mapped', @user1);

INSERT IGNORE INTO radiology_result_details (record_no, patient_id, visit_id, reference_no, module_type, subject, status, priority, assigned_to, notes, payload_json, interoperability_status, created_by)
VALUES
('RADR-20260529-0001', @patient1, @visit1, 'RAD-DRAFT-0001', 'X-Ray', 'Thorax PA: tidak tampak infiltrat aktif', 'reviewed', 'normal', 'dr. Radiologi', 'Ekspertise menunggu rilis final.', JSON_OBJECT('resourceType','DiagnosticReport','category','RAD'), 'mapped', @user1);

INSERT IGNORE INTO medical_documents (record_no, patient_id, visit_id, reference_no, module_type, subject, status, priority, assigned_to, notes, created_by)
VALUES
('DOC-20260529-0001', @patient1, @visit1, 'CONSENT-OK-0001', 'Informed consent', 'Persetujuan tindakan debridement', 'signed', 'high', 'Petugas OK', 'Sudah ditandatangani pasien/keluarga.', @user1);

INSERT IGNORE INTO notification_reminders (record_no, patient_id, visit_id, reference_no, module_type, subject, status, priority, assigned_to, notes, created_by)
VALUES
('NTF-20260529-0001', @patient1, @visit1, 'WA-DRAFT-0001', 'Kontrol', 'Reminder kontrol 7 hari setelah kunjungan', 'scheduled', 'normal', 'System Reminder', 'Siap dikirim via WhatsApp/SMS gateway nanti.', @user1);

INSERT INTO activity_logs (user_id, module, action, reference_id, reference_number, description, ip_address, user_agent, created_at)
SELECT @user1, 'SIMRS Phase 2', 'migration', NULL, 'SIMRS-PHASE2', 'Migration SIMRS phase 2 extension modules dijalankan.', '127.0.0.1', 'SQL migration', NOW()
WHERE @user1 IS NOT NULL;

CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medical_record_no VARCHAR(50) NOT NULL UNIQUE,
    nik VARCHAR(30) NULL,
    name VARCHAR(150) NOT NULL,
    gender ENUM('male','female','other') NULL,
    birth_date DATE NULL,
    birth_place VARCHAR(100) NULL,
    phone VARCHAR(50) NULL,
    email VARCHAR(150) NULL,
    address TEXT NULL,
    blood_type VARCHAR(5) NULL,
    allergy_notes TEXT NULL,
    emergency_contact_name VARCHAR(150) NULL,
    emergency_contact_phone VARCHAR(50) NULL,
    insurance_type ENUM('umum','asuransi','bpjs') DEFAULT 'umum',
    insurance_no VARCHAR(100) NULL,
    fhir_patient_id VARCHAR(100) NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    created_by INT NULL,
    updated_by INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_patients_name (name),
    KEY idx_patients_nik (nik),
    KEY idx_patients_fhir (fhir_patient_id)
);

CREATE TABLE IF NOT EXISTS doctors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    doctor_code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    specialist VARCHAR(150) NULL,
    sip_no VARCHAR(100) NULL,
    phone VARCHAR(50) NULL,
    email VARCHAR(150) NULL,
    fhir_practitioner_id VARCHAR(100) NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_doctors_user (user_id),
    KEY idx_doctors_fhir (fhir_practitioner_id)
);

CREATE TABLE IF NOT EXISTS polyclinics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    clinic_code VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    queue_prefix VARCHAR(5) NOT NULL DEFAULT 'A',
    location VARCHAR(150) NULL,
    fhir_location_id VARCHAR(100) NULL,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS doctor_schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,
    polyclinic_id INT NOT NULL,
    day_of_week TINYINT NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    quota INT DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_schedule_doctor_day (doctor_id, day_of_week),
    KEY idx_schedule_poly_day (polyclinic_id, day_of_week)
);

CREATE TABLE IF NOT EXISTS patient_visits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_no VARCHAR(50) NOT NULL UNIQUE,
    patient_id INT NOT NULL,
    doctor_id INT NULL,
    polyclinic_id INT NOT NULL,
    schedule_id INT NULL,
    visit_date DATE NOT NULL,
    visit_time TIME NULL,
    payment_type ENUM('umum','asuransi','bpjs') DEFAULT 'umum',
    chief_complaint TEXT NULL,
    status ENUM('registered','waiting','in_consultation','pharmacy','billing','paid','completed','cancelled') DEFAULT 'registered',
    fhir_encounter_id VARCHAR(100) NULL,
    created_by INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_visit_patient (patient_id),
    KEY idx_visit_doctor_date (doctor_id, visit_date),
    KEY idx_visit_poly_date (polyclinic_id, visit_date),
    KEY idx_visit_status (status),
    KEY idx_visit_fhir (fhir_encounter_id)
);

CREATE TABLE IF NOT EXISTS visit_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_id INT NOT NULL,
    polyclinic_id INT NOT NULL,
    doctor_id INT NULL,
    queue_no VARCHAR(20) NOT NULL,
    queue_date DATE NOT NULL,
    status ENUM('waiting','called','in_service','done','cancelled') DEFAULT 'waiting',
    called_at DATETIME NULL,
    served_at DATETIME NULL,
    finished_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_queue_visit (visit_id),
    KEY idx_queue_poly_date (polyclinic_id, queue_date),
    KEY idx_queue_status (status)
);

CREATE TABLE IF NOT EXISTS medical_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_id INT NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NULL,
    subjective TEXT NULL,
    objective TEXT NULL,
    assessment TEXT NULL,
    plan TEXT NULL,
    vital_bp VARCHAR(30) NULL,
    vital_pulse VARCHAR(30) NULL,
    vital_temperature VARCHAR(30) NULL,
    vital_respiration VARCHAR(30) NULL,
    vital_weight VARCHAR(30) NULL,
    notes TEXT NULL,
    fhir_observation_payload LONGTEXT NULL,
    created_by INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_medical_record_visit (visit_id),
    KEY idx_mr_patient (patient_id)
);

CREATE TABLE IF NOT EXISTS diagnoses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_id INT NOT NULL,
    medical_record_id INT NULL,
    diagnosis_code VARCHAR(50) NULL,
    diagnosis_name VARCHAR(255) NOT NULL,
    diagnosis_type ENUM('primary','secondary') DEFAULT 'primary',
    notes TEXT NULL,
    fhir_condition_id VARCHAR(100) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY idx_diag_visit (visit_id),
    KEY idx_diag_code (diagnosis_code)
);

CREATE TABLE IF NOT EXISTS prescriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prescription_no VARCHAR(50) NOT NULL UNIQUE,
    visit_id INT NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NULL,
    status ENUM('pending','verified','prepared','dispensed','cancelled') DEFAULT 'pending',
    notes TEXT NULL,
    verified_by INT NULL,
    dispensed_by INT NULL,
    verified_at DATETIME NULL,
    dispensed_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_prescription_visit (visit_id),
    KEY idx_prescription_status (status)
);

CREATE TABLE IF NOT EXISTS prescription_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prescription_id INT NOT NULL,
    product_id INT NULL,
    item_name VARCHAR(150) NOT NULL,
    dosage VARCHAR(100) NULL,
    frequency VARCHAR(100) NULL,
    duration VARCHAR(100) NULL,
    quantity DECIMAL(12,2) DEFAULT 1,
    unit_name VARCHAR(50) DEFAULT 'unit',
    price DECIMAL(15,2) DEFAULT 0,
    notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY idx_rx_item_prescription (prescription_id),
    KEY idx_rx_item_product (product_id)
);

CREATE TABLE IF NOT EXISTS pharmacy_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_no VARCHAR(50) NOT NULL UNIQUE,
    prescription_id INT NOT NULL,
    visit_id INT NOT NULL,
    patient_id INT NOT NULL,
    status ENUM('verified','prepared','dispensed','cancelled') DEFAULT 'verified',
    total_amount DECIMAL(15,2) DEFAULT 0,
    notes TEXT NULL,
    created_by INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_pharmacy_rx (prescription_id),
    KEY idx_pharmacy_visit (visit_id)
);

CREATE TABLE IF NOT EXISTS patient_billing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    billing_no VARCHAR(50) NOT NULL UNIQUE,
    visit_id INT NOT NULL,
    patient_id INT NOT NULL,
    billing_date DATE NOT NULL,
    subtotal DECIMAL(15,2) DEFAULT 0,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    grand_total DECIMAL(15,2) DEFAULT 0,
    paid_amount DECIMAL(15,2) DEFAULT 0,
    status ENUM('draft','unpaid','partial','paid','cancelled') DEFAULT 'draft',
    notes TEXT NULL,
    created_by INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_billing_visit (visit_id),
    KEY idx_billing_patient (patient_id),
    KEY idx_billing_status (status)
);

CREATE TABLE IF NOT EXISTS patient_billing_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    billing_id INT NOT NULL,
    reference_type VARCHAR(50) NULL,
    reference_id INT NULL,
    item_name VARCHAR(150) NOT NULL,
    quantity DECIMAL(12,2) DEFAULT 1,
    unit_price DECIMAL(15,2) DEFAULT 0,
    total_price DECIMAL(15,2) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY idx_billing_item_billing (billing_id)
);

CREATE TABLE IF NOT EXISTS patient_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payment_no VARCHAR(50) NOT NULL UNIQUE,
    billing_id INT NOT NULL,
    visit_id INT NOT NULL,
    patient_id INT NOT NULL,
    bank_account_id INT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('cash','transfer','card','insurance','other') DEFAULT 'cash',
    amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    reference_no VARCHAR(100) NULL,
    notes TEXT NULL,
    created_by INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY idx_patient_payment_billing (billing_id),
    KEY idx_patient_payment_date (payment_date)
);

CREATE TABLE IF NOT EXISTS lab_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_no VARCHAR(50) NOT NULL UNIQUE,
    visit_id INT NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NULL,
    order_date DATE NOT NULL,
    tests TEXT NULL,
    status ENUM('ordered','sample_taken','result_ready','cancelled') DEFAULT 'ordered',
    result_notes TEXT NULL,
    fhir_service_request_id VARCHAR(100) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_lab_visit (visit_id)
);

CREATE TABLE IF NOT EXISTS radiology_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_no VARCHAR(50) NOT NULL UNIQUE,
    visit_id INT NOT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NULL,
    order_date DATE NOT NULL,
    examination TEXT NULL,
    status ENUM('ordered','scheduled','result_ready','cancelled') DEFAULT 'ordered',
    result_notes TEXT NULL,
    fhir_service_request_id VARCHAR(100) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_rad_visit (visit_id)
);

CREATE TABLE IF NOT EXISTS medical_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(80) NULL,
    name VARCHAR(180) NOT NULL,
    category ENUM('obat','alkes','bmhp','lainnya') NOT NULL DEFAULT 'obat',
    item_type ENUM('medicine','supply','service') NOT NULL DEFAULT 'medicine',
    unit_name VARCHAR(40) NOT NULL DEFAULT 'pcs',
    current_stock DECIMAL(14,2) NOT NULL DEFAULT 0,
    minimum_stock DECIMAL(14,2) NOT NULL DEFAULT 0,
    unit_price DECIMAL(14,2) NOT NULL DEFAULT 0,
    description TEXT NULL,
    status ENUM('active','inactive') NOT NULL DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_medical_items_status (status),
    KEY idx_medical_items_category (category),
    KEY idx_medical_items_sku (sku)
);

CREATE TABLE IF NOT EXISTS medical_stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    medical_item_id INT NOT NULL,
    movement_type ENUM('in','out','adjustment') NOT NULL,
    reference_type VARCHAR(80) NULL,
    reference_id INT NULL,
    quantity DECIMAL(14,2) NOT NULL DEFAULT 0,
    stock_before DECIMAL(14,2) NOT NULL DEFAULT 0,
    stock_after DECIMAL(14,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_by INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY idx_medical_stock_item (medical_item_id),
    KEY idx_medical_stock_reference (reference_type, reference_id)
);

INSERT INTO roles (name, description, status)
SELECT role_name, role_description, 'active'
FROM (
    SELECT 'super_admin' role_name, 'Akses penuh sistem' role_description UNION ALL
    SELECT 'admin_rs', 'Administrator SIMRS' UNION ALL
    SELECT 'pendaftaran', 'Petugas pendaftaran pasien' UNION ALL
    SELECT 'dokter', 'Dokter pemeriksa pasien' UNION ALL
    SELECT 'perawat', 'Perawat poli dan antrean' UNION ALL
    SELECT 'farmasi', 'Petugas farmasi' UNION ALL
    SELECT 'kasir', 'Petugas kasir pasien' UNION ALL
    SELECT 'finance', 'Keuangan rumah sakit' UNION ALL
    SELECT 'manajemen', 'Manajemen dan laporan'
) r
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE roles.name = r.role_name);

INSERT INTO permissions (module, action_name, permission_key)
SELECT module_name, action_label, permission_key
FROM (
    SELECT 'simrs_dashboard' module_name, 'view' action_label, 'simrs_dashboard.view' permission_key UNION ALL
    SELECT 'simrs_patient', 'view', 'simrs_patient.view' UNION ALL
    SELECT 'simrs_patient', 'create', 'simrs_patient.create' UNION ALL
    SELECT 'simrs_patient', 'edit', 'simrs_patient.edit' UNION ALL
    SELECT 'simrs_doctor', 'manage', 'simrs_doctor.manage' UNION ALL
    SELECT 'simrs_polyclinic', 'manage', 'simrs_polyclinic.manage' UNION ALL
    SELECT 'simrs_schedule', 'manage', 'simrs_schedule.manage' UNION ALL
    SELECT 'simrs_registration', 'manage', 'simrs_registration.manage' UNION ALL
    SELECT 'simrs_queue', 'manage', 'simrs_queue.manage' UNION ALL
    SELECT 'simrs_outpatient', 'manage', 'simrs_outpatient.manage' UNION ALL
    SELECT 'simrs_medical_record', 'manage', 'simrs_medical_record.manage' UNION ALL
    SELECT 'simrs_prescription', 'manage', 'simrs_prescription.manage' UNION ALL
    SELECT 'simrs_pharmacy', 'manage', 'simrs_pharmacy.manage' UNION ALL
    SELECT 'simrs_billing', 'manage', 'simrs_billing.manage' UNION ALL
    SELECT 'simrs_cashier', 'manage', 'simrs_cashier.manage' UNION ALL
    SELECT 'simrs_report', 'view', 'simrs_report.view'
) p
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE permissions.permission_key = p.permission_key);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key LIKE 'simrs_%'
WHERE r.name IN ('super_admin','admin_rs')
AND NOT EXISTS (
    SELECT 1 FROM role_permissions rp WHERE rp.role_id = r.id AND rp.permission_id = p.id
);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('simrs_dashboard.view','simrs_patient.view','simrs_patient.create','simrs_registration.manage','simrs_queue.manage')
WHERE r.name = 'pendaftaran'
AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id = r.id AND rp.permission_id = p.id);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('simrs_dashboard.view','simrs_queue.manage','simrs_outpatient.manage','simrs_medical_record.manage','simrs_prescription.manage','simrs_patient.view')
WHERE r.name IN ('dokter','perawat')
AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id = r.id AND rp.permission_id = p.id);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('simrs_dashboard.view','simrs_pharmacy.manage')
WHERE r.name = 'farmasi'
AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id = r.id AND rp.permission_id = p.id);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('simrs_dashboard.view','simrs_billing.manage','simrs_cashier.manage')
WHERE r.name = 'kasir'
AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id = r.id AND rp.permission_id = p.id);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('simrs_dashboard.view','simrs_billing.manage','simrs_cashier.manage','simrs_report.view')
WHERE r.name = 'finance'
AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id = r.id AND rp.permission_id = p.id);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('simrs_dashboard.view','simrs_report.view')
WHERE r.name = 'manajemen'
AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id = r.id AND rp.permission_id = p.id);

INSERT INTO polyclinics (clinic_code, name, queue_prefix, location, status)
SELECT 'UMU', 'Poli Umum', 'A', 'Lantai 1', 'active'
WHERE NOT EXISTS (SELECT 1 FROM polyclinics WHERE clinic_code = 'UMU');

INSERT INTO polyclinics (clinic_code, name, queue_prefix, location, status)
SELECT 'ANA', 'Poli Anak', 'B', 'Lantai 1', 'active'
WHERE NOT EXISTS (SELECT 1 FROM polyclinics WHERE clinic_code = 'ANA');

INSERT INTO polyclinics (clinic_code, name, queue_prefix, location, status)
SELECT 'PDL', 'Poli Penyakit Dalam', 'C', 'Lantai 2', 'active'
WHERE NOT EXISTS (SELECT 1 FROM polyclinics WHERE clinic_code = 'PDL');

INSERT INTO medical_items
(name, category, item_type, unit_name, sku, current_stock, minimum_stock, unit_price, description, status, created_at)
SELECT 'Paracetamol 500 mg', 'obat', 'medicine', 'tablet', 'OBT-PCT-500', 250, 50, 1500, 'Seed SIMRS MVP - obat umum', 'active', NOW()
WHERE NOT EXISTS (SELECT 1 FROM medical_items WHERE sku = 'OBT-PCT-500');

INSERT INTO medical_items
(name, category, item_type, unit_name, sku, current_stock, minimum_stock, unit_price, description, status, created_at)
SELECT 'Amoxicillin 500 mg', 'obat', 'medicine', 'kapsul', 'OBT-AMX-500', 100, 30, 3500, 'Seed SIMRS MVP - antibiotik contoh', 'active', NOW()
WHERE NOT EXISTS (SELECT 1 FROM medical_items WHERE sku = 'OBT-AMX-500');

INSERT INTO medical_items
(name, category, item_type, unit_name, sku, current_stock, minimum_stock, unit_price, description, status, created_at)
SELECT 'Masker Medis', 'alkes', 'supply', 'pcs', 'ALK-MSK-001', 500, 100, 1000, 'Seed SIMRS MVP - alkes', 'active', NOW()
WHERE NOT EXISTS (SELECT 1 FROM medical_items WHERE sku = 'ALK-MSK-001');

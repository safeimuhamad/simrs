CREATE TABLE IF NOT EXISTS parking_areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    area_code VARCHAR(30) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    location VARCHAR(150) NULL,
    capacity INT NOT NULL DEFAULT 0,
    reserved_capacity INT NOT NULL DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS parking_gates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    gate_code VARCHAR(30) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    gate_type ENUM('entry','exit','both') DEFAULT 'both',
    area_id INT NULL,
    device_type ENUM('manual','http_api','relay','tcp_ip','serial','qr_scanner','lpr_camera') DEFAULT 'manual',
    device_endpoint VARCHAR(255) NULL,
    status ENUM('active','inactive','maintenance') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_parking_gate_area (area_id)
);

CREATE TABLE IF NOT EXISTS parking_vehicle_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_code VARCHAR(30) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    category ENUM('motor','mobil','ambulance','doctor_employee','vendor','vip','operational','other') DEFAULT 'mobil',
    is_free TINYINT(1) NOT NULL DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS parking_rates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rate_code VARCHAR(30) NOT NULL UNIQUE,
    name VARCHAR(150) NOT NULL,
    vehicle_type_id INT NOT NULL,
    rate_type ENUM('flat','hourly','progressive','daily_max') DEFAULT 'hourly',
    initial_minutes INT NOT NULL DEFAULT 60,
    initial_rate DECIMAL(15,2) NOT NULL DEFAULT 0,
    next_hour_rate DECIMAL(15,2) NOT NULL DEFAULT 0,
    progressive_rate DECIMAL(15,2) NOT NULL DEFAULT 0,
    max_daily_rate DECIMAL(15,2) NOT NULL DEFAULT 0,
    grace_minutes INT NOT NULL DEFAULT 15,
    lost_ticket_fee DECIMAL(15,2) NOT NULL DEFAULT 0,
    inpatient_special_rate DECIMAL(15,2) NOT NULL DEFAULT 0,
    status ENUM('active','inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_parking_rate_vehicle (vehicle_type_id)
);

CREATE TABLE IF NOT EXISTS parking_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_no VARCHAR(50) NOT NULL UNIQUE,
    qr_token VARCHAR(100) NOT NULL UNIQUE,
    plate_number VARCHAR(30) NOT NULL,
    vehicle_type_id INT NOT NULL,
    area_id INT NULL,
    entry_gate_id INT NULL,
    exit_gate_id INT NULL,
    patient_visit_id INT NULL,
    patient_id INT NULL,
    member_id INT NULL,
    entry_time DATETIME NOT NULL,
    exit_time DATETIME NULL,
    duration_minutes INT NOT NULL DEFAULT 0,
    calculated_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    payable_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    status ENUM('active','unpaid','paid','lost_ticket','cancelled') DEFAULT 'active',
    payment_status ENUM('unpaid','paid','waived','refunded') DEFAULT 'unpaid',
    source ENUM('manual','lpr','qr','member') DEFAULT 'manual',
    notes TEXT NULL,
    created_by INT NULL,
    checked_out_by INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_parking_ticket_plate (plate_number),
    KEY idx_parking_ticket_status (status),
    KEY idx_parking_ticket_entry (entry_time),
    KEY idx_parking_ticket_visit (patient_visit_id)
);

CREATE TABLE IF NOT EXISTS parking_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payment_no VARCHAR(50) NOT NULL UNIQUE,
    ticket_id INT NOT NULL,
    patient_billing_id INT NULL,
    bank_account_id INT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('cash','transfer','debit','qris','member','patient_billing','waived','other') DEFAULT 'cash',
    amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    status ENUM('unpaid','paid','waived','refunded') DEFAULT 'paid',
    reference_no VARCHAR(100) NULL,
    notes TEXT NULL,
    created_by INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY idx_parking_payment_ticket (ticket_id),
    KEY idx_parking_payment_date (payment_date)
);

CREATE TABLE IF NOT EXISTS parking_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_no VARCHAR(50) NOT NULL UNIQUE,
    member_type ENUM('doctor','employee','vendor','vip','operational') DEFAULT 'employee',
    user_id INT NULL,
    employee_id INT NULL,
    doctor_id INT NULL,
    name VARCHAR(150) NOT NULL,
    plate_number VARCHAR(30) NOT NULL,
    vehicle_type_id INT NULL,
    valid_from DATE NULL,
    valid_to DATE NULL,
    status ENUM('active','inactive','expired') DEFAULT 'active',
    notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_parking_member_plate (plate_number)
);

CREATE TABLE IF NOT EXISTS parking_validations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    patient_visit_id INT NULL,
    patient_id INT NULL,
    validation_type ENUM('outpatient','inpatient','emergency','manual') DEFAULT 'outpatient',
    discount_type ENUM('free','percent','amount','special_rate') DEFAULT 'free',
    discount_value DECIMAL(15,2) NOT NULL DEFAULT 0,
    validated_by INT NULL,
    validated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    notes TEXT NULL,
    KEY idx_parking_validation_ticket (ticket_id),
    KEY idx_parking_validation_visit (patient_visit_id)
);

CREATE TABLE IF NOT EXISTS parking_gate_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NULL,
    gate_id INT NOT NULL,
    action ENUM('open_entry','close_entry','open_exit','close_exit','open_manual','close_manual','deny') NOT NULL,
    response_status ENUM('success','failed','simulated') DEFAULT 'simulated',
    response_message TEXT NULL,
    created_by INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY idx_parking_gate_log_ticket (ticket_id),
    KEY idx_parking_gate_log_gate (gate_id)
);

INSERT INTO roles (name, description, status)
SELECT role_name, role_desc, 'active'
FROM (
    SELECT 'parking_admin' role_name, 'Administrator Parking Management' role_desc UNION ALL
    SELECT 'parking_operator', 'Operator gate/check-in/check-out parkir' UNION ALL
    SELECT 'cashier', 'Kasir RS/Parkir' UNION ALL
    SELECT 'management', 'Manajemen RS'
) r
WHERE NOT EXISTS (SELECT 1 FROM roles WHERE roles.name = r.role_name);

INSERT INTO permissions (module, action_name, permission_key)
SELECT module_name, action_label, permission_key
FROM (
    SELECT 'parking_dashboard' module_name, 'view' action_label, 'parking_dashboard.view' permission_key UNION ALL
    SELECT 'parking_master', 'manage', 'parking_master.manage' UNION ALL
    SELECT 'parking_ticket', 'manage', 'parking_ticket.manage' UNION ALL
    SELECT 'parking_checkin', 'manage', 'parking_checkin.manage' UNION ALL
    SELECT 'parking_checkout', 'manage', 'parking_checkout.manage' UNION ALL
    SELECT 'parking_payment', 'manage', 'parking_payment.manage' UNION ALL
    SELECT 'parking_validation', 'manage', 'parking_validation.manage' UNION ALL
    SELECT 'parking_report', 'view', 'parking_report.view'
) p
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE permissions.permission_key = p.permission_key);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key LIKE 'parking_%'
WHERE r.name IN ('super_admin','parking_admin')
AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id = r.id AND rp.permission_id = p.id);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('parking_dashboard.view','parking_ticket.manage','parking_checkin.manage','parking_checkout.manage','parking_validation.manage')
WHERE r.name = 'parking_operator'
AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id = r.id AND rp.permission_id = p.id);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key = 'parking_report.view'
WHERE r.name = 'parking_operator'
AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id = r.id AND rp.permission_id = p.id);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('parking_dashboard.view','parking_payment.manage','parking_checkout.manage','parking_report.view')
WHERE r.name IN ('cashier','kasir','finance')
AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id = r.id AND rp.permission_id = p.id);

INSERT INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('parking_dashboard.view','parking_report.view')
WHERE r.name IN ('management','manajemen')
AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id = r.id AND rp.permission_id = p.id);

INSERT INTO parking_areas (area_code, name, location, capacity, reserved_capacity, status)
SELECT 'A', 'Area Parkir Utama', 'Depan lobby', 80, 5, 'active'
WHERE NOT EXISTS (SELECT 1 FROM parking_areas WHERE area_code = 'A');

INSERT INTO parking_areas (area_code, name, location, capacity, reserved_capacity, status)
SELECT 'B', 'Area Parkir Pegawai', 'Sisi timur', 40, 20, 'active'
WHERE NOT EXISTS (SELECT 1 FROM parking_areas WHERE area_code = 'B');

INSERT INTO parking_gates (gate_code, name, gate_type, area_id, device_type, status)
SELECT 'IN-01', 'Gate Masuk Utama', 'entry', id, 'manual', 'active' FROM parking_areas WHERE area_code = 'A'
AND NOT EXISTS (SELECT 1 FROM parking_gates WHERE gate_code = 'IN-01');

INSERT INTO parking_gates (gate_code, name, gate_type, area_id, device_type, status)
SELECT 'OUT-01', 'Gate Keluar Utama', 'exit', id, 'manual', 'active' FROM parking_areas WHERE area_code = 'A'
AND NOT EXISTS (SELECT 1 FROM parking_gates WHERE gate_code = 'OUT-01');

INSERT INTO parking_vehicle_types (type_code, name, category, is_free, status)
SELECT 'MTR', 'Motor', 'motor', 0, 'active'
WHERE NOT EXISTS (SELECT 1 FROM parking_vehicle_types WHERE type_code = 'MTR');

INSERT INTO parking_vehicle_types (type_code, name, category, is_free, status)
SELECT 'MBL', 'Mobil', 'mobil', 0, 'active'
WHERE NOT EXISTS (SELECT 1 FROM parking_vehicle_types WHERE type_code = 'MBL');

INSERT INTO parking_vehicle_types (type_code, name, category, is_free, status)
SELECT 'AMB', 'Ambulance', 'ambulance', 1, 'active'
WHERE NOT EXISTS (SELECT 1 FROM parking_vehicle_types WHERE type_code = 'AMB');

INSERT INTO parking_vehicle_types (type_code, name, category, is_free, status)
SELECT 'EMP', 'Kendaraan Dokter/Pegawai', 'doctor_employee', 1, 'active'
WHERE NOT EXISTS (SELECT 1 FROM parking_vehicle_types WHERE type_code = 'EMP');

INSERT INTO parking_vehicle_types (type_code, name, category, is_free, status)
SELECT 'VND', 'Vendor', 'vendor', 0, 'active'
WHERE NOT EXISTS (SELECT 1 FROM parking_vehicle_types WHERE type_code = 'VND');

INSERT INTO parking_vehicle_types (type_code, name, category, is_free, status)
SELECT 'VIP', 'VIP', 'vip', 0, 'active'
WHERE NOT EXISTS (SELECT 1 FROM parking_vehicle_types WHERE type_code = 'VIP');

INSERT INTO parking_rates (rate_code, name, vehicle_type_id, rate_type, initial_minutes, initial_rate, next_hour_rate, max_daily_rate, grace_minutes, lost_ticket_fee, inpatient_special_rate, status)
SELECT 'RATE-MTR', 'Tarif Motor', id, 'hourly', 60, 2000, 1000, 10000, 15, 25000, 5000, 'active'
FROM parking_vehicle_types WHERE type_code = 'MTR'
AND NOT EXISTS (SELECT 1 FROM parking_rates WHERE rate_code = 'RATE-MTR');

INSERT INTO parking_rates (rate_code, name, vehicle_type_id, rate_type, initial_minutes, initial_rate, next_hour_rate, max_daily_rate, grace_minutes, lost_ticket_fee, inpatient_special_rate, status)
SELECT 'RATE-MBL', 'Tarif Mobil', id, 'hourly', 60, 5000, 3000, 30000, 15, 50000, 15000, 'active'
FROM parking_vehicle_types WHERE type_code = 'MBL'
AND NOT EXISTS (SELECT 1 FROM parking_rates WHERE rate_code = 'RATE-MBL');

INSERT INTO parking_rates (rate_code, name, vehicle_type_id, rate_type, initial_minutes, initial_rate, next_hour_rate, max_daily_rate, grace_minutes, lost_ticket_fee, inpatient_special_rate, status)
SELECT 'RATE-VND', 'Tarif Vendor', id, 'daily_max', 60, 7000, 4000, 40000, 10, 75000, 0, 'active'
FROM parking_vehicle_types WHERE type_code = 'VND'
AND NOT EXISTS (SELECT 1 FROM parking_rates WHERE rate_code = 'RATE-VND');

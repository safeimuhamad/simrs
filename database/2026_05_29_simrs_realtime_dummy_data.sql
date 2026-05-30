-- Data pendukung SIMRS realtime untuk dashboard, rawat inap, laboratorium, billing, dan role access.

CREATE TABLE IF NOT EXISTS inpatient_beds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bed_no VARCHAR(30) NOT NULL UNIQUE,
    room_name VARCHAR(100) NOT NULL,
    class_name VARCHAR(50) NOT NULL,
    status ENUM('available','occupied','maintenance','inactive') NOT NULL DEFAULT 'available',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS inpatient_admissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admission_no VARCHAR(50) NOT NULL UNIQUE,
    visit_id INT NULL,
    patient_id INT NOT NULL,
    doctor_id INT NULL,
    bed_id INT NOT NULL,
    admission_date DATE NOT NULL,
    discharge_date DATE NULL,
    status ENUM('active','discharged','cancelled') NOT NULL DEFAULT 'active',
    notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_inpatient_patient (patient_id),
    KEY idx_inpatient_status (status),
    KEY idx_inpatient_date (admission_date)
);

INSERT INTO permissions (module, action_name, permission_key)
SELECT module_name, action_label, permission_key
FROM (
    SELECT 'simrs_inpatient' module_name, 'manage' action_label, 'simrs_inpatient.manage' permission_key UNION ALL
    SELECT 'simrs_lab', 'manage', 'simrs_lab.manage' UNION ALL
    SELECT 'simrs_radiology', 'manage', 'simrs_radiology.manage'
) p
WHERE NOT EXISTS (SELECT 1 FROM permissions WHERE permissions.permission_key = p.permission_key);

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key IN ('simrs_inpatient.manage','simrs_lab.manage','simrs_radiology.manage')
WHERE r.name IN ('super_admin','admin_rs','dokter','perawat','manajemen');

INSERT IGNORE INTO role_permissions (role_id, permission_id)
SELECT r.id, p.id
FROM roles r
JOIN permissions p ON p.permission_key = 'simrs_lab.manage'
WHERE r.name IN ('farmasi');

INSERT INTO inpatient_beds (bed_no, room_name, class_name, status)
SELECT 'A-101-1', 'Ruang Anggrek 101', 'Kelas 1', 'occupied'
WHERE NOT EXISTS (SELECT 1 FROM inpatient_beds WHERE bed_no = 'A-101-1');

INSERT INTO inpatient_beds (bed_no, room_name, class_name, status)
SELECT 'A-101-2', 'Ruang Anggrek 101', 'Kelas 1', 'available'
WHERE NOT EXISTS (SELECT 1 FROM inpatient_beds WHERE bed_no = 'A-101-2');

INSERT INTO inpatient_beds (bed_no, room_name, class_name, status)
SELECT 'M-201-1', 'Ruang Melati 201', 'Kelas 2', 'occupied'
WHERE NOT EXISTS (SELECT 1 FROM inpatient_beds WHERE bed_no = 'M-201-1');

INSERT INTO inpatient_beds (bed_no, room_name, class_name, status)
SELECT 'M-201-2', 'Ruang Melati 201', 'Kelas 2', 'occupied'
WHERE NOT EXISTS (SELECT 1 FROM inpatient_beds WHERE bed_no = 'M-201-2');

INSERT INTO inpatient_beds (bed_no, room_name, class_name, status)
SELECT 'ICU-01', 'ICU', 'ICU', 'occupied'
WHERE NOT EXISTS (SELECT 1 FROM inpatient_beds WHERE bed_no = 'ICU-01');

INSERT INTO inpatient_beds (bed_no, room_name, class_name, status)
SELECT 'ICU-02', 'ICU', 'ICU', 'available'
WHERE NOT EXISTS (SELECT 1 FROM inpatient_beds WHERE bed_no = 'ICU-02');

INSERT INTO patient_visits (visit_no, patient_id, doctor_id, polyclinic_id, visit_date, visit_time, payment_type, chief_complaint, status, created_by)
SELECT 'VIS-DASH-004', p.id, d.id, c.id, CURDATE(), '10:15:00', 'umum', 'Kontrol tekanan darah', 'pharmacy', u.id
FROM patients p JOIN doctors d ON d.doctor_code='DR-001' JOIN polyclinics c ON c.clinic_code='UMU' LEFT JOIN users u ON u.email='pendaftaran@simrs.test'
WHERE p.medical_record_no='RM00012348'
AND NOT EXISTS (SELECT 1 FROM patient_visits WHERE visit_no='VIS-DASH-004');

INSERT INTO patient_visits (visit_no, patient_id, doctor_id, polyclinic_id, visit_date, visit_time, payment_type, chief_complaint, status, created_by)
SELECT 'VIS-DASH-005', p.id, d.id, c.id, CURDATE(), '10:40:00', 'asuransi', 'Nyeri ulu hati', 'billing', u.id
FROM patients p JOIN doctors d ON d.doctor_code='DR-004' JOIN polyclinics c ON c.clinic_code='KBD' LEFT JOIN users u ON u.email='pendaftaran@simrs.test'
WHERE p.medical_record_no='RM00012349'
AND NOT EXISTS (SELECT 1 FROM patient_visits WHERE visit_no='VIS-DASH-005');

INSERT INTO patient_visits (visit_no, patient_id, doctor_id, polyclinic_id, visit_date, visit_time, payment_type, chief_complaint, status, created_by)
SELECT CONCAT('VIS-HIST-', n.seq), p.id, d.id, c.id, DATE_SUB(CURDATE(), INTERVAL n.day_back DAY), MAKETIME(8 + (n.seq % 4), 10, 0), 'umum', 'Kunjungan kontrol rutin', 'completed', u.id
FROM (
    SELECT 1 seq, 1 day_back UNION ALL SELECT 2, 1 UNION ALL SELECT 3, 2 UNION ALL SELECT 4, 3 UNION ALL
    SELECT 5, 4 UNION ALL SELECT 6, 5 UNION ALL SELECT 7, 6
) n
JOIN patients p ON p.medical_record_no = ELT(((n.seq - 1) % 5) + 1, 'RM00012345','RM00012346','RM00012347','RM00012348','RM00012349')
JOIN doctors d ON d.doctor_code = ELT(((n.seq - 1) % 4) + 1, 'DR-001','DR-002','DR-003','DR-004')
JOIN polyclinics c ON c.clinic_code = ELT(((n.seq - 1) % 4) + 1, 'UMU','ANA','GIG','KBD')
LEFT JOIN users u ON u.email='pendaftaran@simrs.test'
WHERE NOT EXISTS (SELECT 1 FROM patient_visits WHERE visit_no = CONCAT('VIS-HIST-', n.seq));

INSERT INTO visit_queue (visit_id, polyclinic_id, doctor_id, queue_date, queue_no, status)
SELECT v.id, v.polyclinic_id, v.doctor_id, CURDATE(), 'E001', 'waiting'
FROM patient_visits v WHERE v.visit_no='VIS-DASH-005'
AND NOT EXISTS (SELECT 1 FROM visit_queue WHERE visit_id = v.id);

INSERT INTO medical_records (visit_id, patient_id, doctor_id, subjective, objective, assessment, plan, vital_bp, vital_pulse, vital_temperature, vital_respiration, vital_weight, created_by)
SELECT v.id, v.patient_id, v.doctor_id, 'Demam sejak 2 hari, batuk ringan.', 'Tenggorokan hiperemis, tidak sesak.', 'ISPA ringan.', 'Terapi simptomatik dan kontrol bila memburuk.', '120/80', '86', '37.8', '20', '68', u.id
FROM patient_visits v LEFT JOIN users u ON u.email='dokter@simrs.test'
WHERE v.visit_no='VIS-DUMMY-001'
AND NOT EXISTS (SELECT 1 FROM medical_records WHERE visit_id = v.id);

INSERT INTO diagnoses (visit_id, medical_record_id, diagnosis_code, diagnosis_name, diagnosis_type, notes)
SELECT mr.visit_id, mr.id, 'J06.9', 'Acute upper respiratory infection, unspecified', 'primary', 'ICD manual dummy'
FROM medical_records mr
JOIN patient_visits v ON v.id = mr.visit_id
WHERE v.visit_no='VIS-DUMMY-001'
AND NOT EXISTS (SELECT 1 FROM diagnoses WHERE visit_id = mr.visit_id AND diagnosis_code='J06.9');

INSERT INTO medical_items (sku, name, category, item_type, unit_name, current_stock, minimum_stock, unit_price, description, status, created_at)
SELECT 'OBT-PCT-500', 'Paracetamol 500mg', 'obat', 'medicine', 'tablet', 120, 30, 1500, 'Obat demam dan nyeri', 'active', NOW()
WHERE NOT EXISTS (SELECT 1 FROM medical_items WHERE sku='OBT-PCT-500');

INSERT INTO medical_items (sku, name, category, item_type, unit_name, current_stock, minimum_stock, unit_price, description, status, created_at)
SELECT 'OBT-AMX-500', 'Amoxicillin 500mg', 'obat', 'medicine', 'kapsul', 18, 25, 2500, 'Antibiotik', 'active', NOW()
WHERE NOT EXISTS (SELECT 1 FROM medical_items WHERE sku='OBT-AMX-500');

INSERT INTO prescriptions (prescription_no, visit_id, patient_id, doctor_id, status, notes)
SELECT 'RX-DUMMY-001', v.id, v.patient_id, v.doctor_id, 'pending', 'Resep dari pemeriksaan rawat jalan'
FROM patient_visits v WHERE v.visit_no='VIS-DUMMY-001'
AND NOT EXISTS (SELECT 1 FROM prescriptions WHERE prescription_no='RX-DUMMY-001');

INSERT INTO prescription_items (prescription_id, product_id, item_name, dosage, frequency, duration, quantity, unit_name, price)
SELECT rx.id, mi.id, mi.name, '500mg', '3x sehari', '3 hari', 9, mi.unit_name, mi.unit_price
FROM prescriptions rx JOIN medical_items mi ON mi.sku='OBT-PCT-500'
WHERE rx.prescription_no='RX-DUMMY-001'
AND NOT EXISTS (SELECT 1 FROM prescription_items WHERE prescription_id=rx.id AND product_id=mi.id);

INSERT INTO pharmacy_transactions (transaction_no, prescription_id, visit_id, patient_id, status, total_amount, notes, created_by)
SELECT 'PH-DUMMY-001', rx.id, rx.visit_id, rx.patient_id, 'verified', 13500, 'Menunggu persiapan obat', u.id
FROM prescriptions rx LEFT JOIN users u ON u.email='farmasi@simrs.test'
WHERE rx.prescription_no='RX-DUMMY-001'
AND NOT EXISTS (SELECT 1 FROM pharmacy_transactions WHERE transaction_no='PH-DUMMY-001');

INSERT INTO lab_orders (order_no, visit_id, patient_id, doctor_id, order_date, tests, status, result_notes)
SELECT 'LAB-DUMMY-001', v.id, v.patient_id, v.doctor_id, CURDATE(), 'Darah Lengkap, Gula Darah Sewaktu', 'sample_taken', 'Sampel sudah diterima lab'
FROM patient_visits v WHERE v.visit_no='VIS-DASH-005'
AND NOT EXISTS (SELECT 1 FROM lab_orders WHERE order_no='LAB-DUMMY-001');

INSERT INTO lab_orders (order_no, visit_id, patient_id, doctor_id, order_date, tests, status, result_notes)
SELECT 'LAB-DUMMY-002', v.id, v.patient_id, v.doctor_id, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'Urinalisis', 'result_ready', 'Hasil dalam batas normal'
FROM patient_visits v WHERE v.visit_no='VIS-HIST-1'
AND NOT EXISTS (SELECT 1 FROM lab_orders WHERE order_no='LAB-DUMMY-002');

INSERT INTO radiology_orders (order_no, visit_id, patient_id, doctor_id, order_date, examination, status, result_notes)
SELECT 'RAD-DUMMY-001', v.id, v.patient_id, v.doctor_id, CURDATE(), 'Thorax PA', 'ordered', 'Menunggu pemeriksaan radiologi'
FROM patient_visits v WHERE v.visit_no='VIS-DASH-004'
AND NOT EXISTS (SELECT 1 FROM radiology_orders WHERE order_no='RAD-DUMMY-001');

INSERT INTO radiology_orders (order_no, visit_id, patient_id, doctor_id, order_date, examination, status, result_notes)
SELECT 'RAD-DUMMY-002', v.id, v.patient_id, v.doctor_id, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 'USG Abdomen', 'result_ready', 'Tidak tampak kelainan akut'
FROM patient_visits v WHERE v.visit_no='VIS-HIST-2'
AND NOT EXISTS (SELECT 1 FROM radiology_orders WHERE order_no='RAD-DUMMY-002');

INSERT INTO inpatient_admissions (admission_no, visit_id, patient_id, doctor_id, bed_id, admission_date, status, notes)
SELECT 'ADM-DUMMY-001', v.id, v.patient_id, v.doctor_id, b.id, CURDATE(), 'active', 'Observasi demam dan hidrasi'
FROM patient_visits v JOIN inpatient_beds b ON b.bed_no='A-101-1'
WHERE v.visit_no='VIS-DUMMY-002'
AND NOT EXISTS (SELECT 1 FROM inpatient_admissions WHERE admission_no='ADM-DUMMY-001');

INSERT INTO inpatient_admissions (admission_no, visit_id, patient_id, doctor_id, bed_id, admission_date, status, notes)
SELECT 'ADM-DUMMY-002', v.id, v.patient_id, v.doctor_id, b.id, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 'active', 'Perawatan lanjutan'
FROM patient_visits v JOIN inpatient_beds b ON b.bed_no='M-201-1'
WHERE v.visit_no='VIS-DASH-004'
AND NOT EXISTS (SELECT 1 FROM inpatient_admissions WHERE admission_no='ADM-DUMMY-002');

INSERT INTO patient_billing (billing_no, visit_id, patient_id, billing_date, subtotal, grand_total, paid_amount, status, notes, created_by)
SELECT 'BILL-DUMMY-001', v.id, v.patient_id, CURDATE(), 185000, 185000, 185000, 'paid', 'Billing dummy rawat jalan', u.id
FROM patient_visits v LEFT JOIN users u ON u.email='kasir@simrs.test'
WHERE v.visit_no='VIS-DUMMY-001'
AND NOT EXISTS (SELECT 1 FROM patient_billing WHERE billing_no='BILL-DUMMY-001');

INSERT INTO patient_billing (billing_no, visit_id, patient_id, billing_date, subtotal, grand_total, paid_amount, status, notes, created_by)
SELECT 'BILL-DUMMY-002', v.id, v.patient_id, CURDATE(), 275000, 275000, 0, 'unpaid', 'Billing menunggu pembayaran', u.id
FROM patient_visits v LEFT JOIN users u ON u.email='kasir@simrs.test'
WHERE v.visit_no='VIS-DASH-005'
AND NOT EXISTS (SELECT 1 FROM patient_billing WHERE billing_no='BILL-DUMMY-002');

INSERT INTO patient_billing_items (billing_id, reference_type, reference_id, item_name, quantity, unit_price, total_price)
SELECT b.id, 'consultation', v.id, 'Jasa konsultasi dokter', 1, 150000, 150000
FROM patient_billing b JOIN patient_visits v ON v.id=b.visit_id
WHERE b.billing_no='BILL-DUMMY-001'
AND NOT EXISTS (SELECT 1 FROM patient_billing_items WHERE billing_id=b.id AND item_name='Jasa konsultasi dokter');

INSERT INTO patient_billing_items (billing_id, reference_type, reference_id, item_name, quantity, unit_price, total_price)
SELECT b.id, 'laboratory', lo.id, 'Pemeriksaan laboratorium', 1, 125000, 125000
FROM patient_billing b JOIN lab_orders lo ON lo.order_no='LAB-DUMMY-001'
WHERE b.billing_no='BILL-DUMMY-002'
AND NOT EXISTS (SELECT 1 FROM patient_billing_items WHERE billing_id=b.id AND item_name='Pemeriksaan laboratorium');

INSERT INTO patient_payments (payment_no, billing_id, visit_id, patient_id, bank_account_id, payment_date, payment_method, amount, reference_no, notes, created_by)
SELECT 'PAY-DUMMY-001', b.id, b.visit_id, b.patient_id, ba.id, CURDATE(), 'cash', 185000, 'CASH-DUMMY-001', 'Pembayaran kasir dummy', u.id
FROM patient_billing b
LEFT JOIN bank_accounts ba ON ba.account_code='RS-CASH-001'
LEFT JOIN users u ON u.email='kasir@simrs.test'
WHERE b.billing_no='BILL-DUMMY-001'
AND NOT EXISTS (SELECT 1 FROM patient_payments WHERE payment_no='PAY-DUMMY-001');

INSERT INTO patient_payments (payment_no, billing_id, visit_id, patient_id, bank_account_id, payment_date, payment_method, amount, reference_no, notes, created_by)
SELECT CONCAT('PAY-HIST-', n.seq), b.id, b.visit_id, b.patient_id, ba.id, DATE_SUB(CURDATE(), INTERVAL n.day_back DAY), 'transfer', n.amount, CONCAT('TRF-HIST-', n.seq), 'Dummy pendapatan historis SIMRS', u.id
FROM (
    SELECT 1 seq, 1 day_back, 225000 amount UNION ALL SELECT 2, 2, 175000 UNION ALL SELECT 3, 3, 310000 UNION ALL
    SELECT 4, 4, 145000 UNION ALL SELECT 5, 5, 260000 UNION ALL SELECT 6, 6, 190000
) n
JOIN patient_billing b ON b.billing_no='BILL-DUMMY-001'
LEFT JOIN bank_accounts ba ON ba.account_code='RS-BCA-001'
LEFT JOIN users u ON u.email='kasir@simrs.test'
WHERE NOT EXISTS (SELECT 1 FROM patient_payments WHERE payment_no=CONCAT('PAY-HIST-', n.seq));

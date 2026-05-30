SET @simrs_password = '$2y$10$yQ0R2.biOboJ4P753Q80iOW83/evGcX8Izva3I9kP.GBfNVxc5ywW';

INSERT INTO users (name, username, email, password, role_id, data_scope, role, status, created_at)
SELECT 'Super Admin SIMRS', 'superadmin@simrs.test', 'superadmin@simrs.test', @simrs_password, r.id, 'all', 'super_admin', 'active', NOW()
FROM roles r WHERE r.name = 'super_admin'
AND NOT EXISTS (SELECT 1 FROM users WHERE email = 'superadmin@simrs.test');

INSERT INTO users (name, username, email, password, role_id, data_scope, role, status, created_at)
SELECT 'Admin RS', 'admin.rs@simrs.test', 'admin.rs@simrs.test', @simrs_password, r.id, 'all', 'admin_rs', 'active', NOW()
FROM roles r WHERE r.name = 'admin_rs'
AND NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin.rs@simrs.test');

INSERT INTO users (name, username, email, password, role_id, data_scope, role, status, created_at)
SELECT 'Petugas Pendaftaran', 'pendaftaran@simrs.test', 'pendaftaran@simrs.test', @simrs_password, r.id, 'all', 'pendaftaran', 'active', NOW()
FROM roles r WHERE r.name = 'pendaftaran'
AND NOT EXISTS (SELECT 1 FROM users WHERE email = 'pendaftaran@simrs.test');

INSERT INTO users (name, username, email, password, role_id, data_scope, role, status, created_at)
SELECT 'dr. Budi Santoso', 'dokter@simrs.test', 'dokter@simrs.test', @simrs_password, r.id, 'own', 'dokter', 'active', NOW()
FROM roles r WHERE r.name = 'dokter'
AND NOT EXISTS (SELECT 1 FROM users WHERE email = 'dokter@simrs.test');

INSERT INTO users (name, username, email, password, role_id, data_scope, role, status, created_at)
SELECT 'Ns. Rina Pratiwi', 'perawat@simrs.test', 'perawat@simrs.test', @simrs_password, r.id, 'department', 'perawat', 'active', NOW()
FROM roles r WHERE r.name = 'perawat'
AND NOT EXISTS (SELECT 1 FROM users WHERE email = 'perawat@simrs.test');

INSERT INTO users (name, username, email, password, role_id, data_scope, role, status, created_at)
SELECT 'Apt. Sari Lestari', 'farmasi@simrs.test', 'farmasi@simrs.test', @simrs_password, r.id, 'department', 'farmasi', 'active', NOW()
FROM roles r WHERE r.name = 'farmasi'
AND NOT EXISTS (SELECT 1 FROM users WHERE email = 'farmasi@simrs.test');

INSERT INTO users (name, username, email, password, role_id, data_scope, role, status, created_at)
SELECT 'Kasir SIMRS', 'kasir@simrs.test', 'kasir@simrs.test', @simrs_password, r.id, 'department', 'kasir', 'active', NOW()
FROM roles r WHERE r.name = 'kasir'
AND NOT EXISTS (SELECT 1 FROM users WHERE email = 'kasir@simrs.test');

INSERT INTO users (name, username, email, password, role_id, data_scope, role, status, created_at)
SELECT 'Finance SIMRS', 'finance@simrs.test', 'finance@simrs.test', @simrs_password, r.id, 'all', 'finance', 'active', NOW()
FROM roles r WHERE r.name = 'finance'
AND NOT EXISTS (SELECT 1 FROM users WHERE email = 'finance@simrs.test');

INSERT INTO users (name, username, email, password, role_id, data_scope, role, status, created_at)
SELECT 'Manajemen RS', 'manajemen@simrs.test', 'manajemen@simrs.test', @simrs_password, r.id, 'all', 'manajemen', 'active', NOW()
FROM roles r WHERE r.name = 'manajemen'
AND NOT EXISTS (SELECT 1 FROM users WHERE email = 'manajemen@simrs.test');

INSERT INTO users (name, username, email, password, role_id, data_scope, role, status, created_at)
SELECT 'Parking Admin', 'parking.admin@simrs.test', 'parking.admin@simrs.test', @simrs_password, r.id, 'all', 'parking_admin', 'active', NOW()
FROM roles r WHERE r.name = 'parking_admin'
AND NOT EXISTS (SELECT 1 FROM users WHERE email = 'parking.admin@simrs.test');

INSERT INTO users (name, username, email, password, role_id, data_scope, role, status, created_at)
SELECT 'Operator Parkir', 'parking.operator@simrs.test', 'parking.operator@simrs.test', @simrs_password, r.id, 'department', 'parking_operator', 'active', NOW()
FROM roles r WHERE r.name = 'parking_operator'
AND NOT EXISTS (SELECT 1 FROM users WHERE email = 'parking.operator@simrs.test');

INSERT INTO doctors (doctor_code, name, specialist, phone, email, user_id, status)
SELECT 'DR-001', 'dr. Budi Santoso', 'Dokter Umum', '081234500001', 'dokter@simrs.test', u.id, 'active'
FROM users u WHERE u.email = 'dokter@simrs.test'
AND NOT EXISTS (SELECT 1 FROM doctors WHERE doctor_code = 'DR-001');

INSERT INTO doctors (doctor_code, name, specialist, phone, email, status)
SELECT 'DR-002', 'dr. Anisa Putri', 'Spesialis Anak', '081234500002', 'anisa@simrs.test', 'active'
WHERE NOT EXISTS (SELECT 1 FROM doctors WHERE doctor_code = 'DR-002');

INSERT INTO doctors (doctor_code, name, specialist, phone, email, status)
SELECT 'DR-003', 'drg. Dimas Aditya', 'Dokter Gigi', '081234500003', 'dimas@simrs.test', 'active'
WHERE NOT EXISTS (SELECT 1 FROM doctors WHERE doctor_code = 'DR-003');

INSERT INTO doctors (doctor_code, name, specialist, phone, email, status)
SELECT 'DR-004', 'dr. Lina Wati', 'Spesialis Kandungan', '081234500004', 'lina@simrs.test', 'active'
WHERE NOT EXISTS (SELECT 1 FROM doctors WHERE doctor_code = 'DR-004');

INSERT INTO polyclinics (clinic_code, name, queue_prefix, location, status)
SELECT 'GIG', 'Poli Gigi', 'D', 'Lantai 1', 'active'
WHERE NOT EXISTS (SELECT 1 FROM polyclinics WHERE clinic_code = 'GIG');

INSERT INTO polyclinics (clinic_code, name, queue_prefix, location, status)
SELECT 'KBD', 'Poli Kebidanan', 'E', 'Lantai 2', 'active'
WHERE NOT EXISTS (SELECT 1 FROM polyclinics WHERE clinic_code = 'KBD');

INSERT INTO patients (medical_record_no, nik, name, gender, birth_date, phone, address, insurance_type, status)
SELECT 'RM00012345', '3171000000000001', 'Andi Pratama', 'male', '1988-03-12', '081100000001', 'Jakarta', 'umum', 'active'
WHERE NOT EXISTS (SELECT 1 FROM patients WHERE medical_record_no = 'RM00012345');

INSERT INTO patients (medical_record_no, nik, name, gender, birth_date, phone, address, insurance_type, status)
SELECT 'RM00012346', '3171000000000002', 'Siti Aminah', 'female', '1992-07-20', '081100000002', 'Jakarta', 'bpjs', 'active'
WHERE NOT EXISTS (SELECT 1 FROM patients WHERE medical_record_no = 'RM00012346');

INSERT INTO patients (medical_record_no, nik, name, gender, birth_date, phone, address, insurance_type, status)
SELECT 'RM00012347', '3171000000000003', 'Bambang Setiawan', 'male', '1979-01-08', '081100000003', 'Depok', 'umum', 'active'
WHERE NOT EXISTS (SELECT 1 FROM patients WHERE medical_record_no = 'RM00012347');

INSERT INTO patients (medical_record_no, nik, name, gender, birth_date, phone, address, insurance_type, status)
SELECT 'RM00012348', '3171000000000004', 'Rina Marlina', 'female', '1995-10-18', '081100000004', 'Bekasi', 'asuransi', 'active'
WHERE NOT EXISTS (SELECT 1 FROM patients WHERE medical_record_no = 'RM00012348');

INSERT INTO patients (medical_record_no, nik, name, gender, birth_date, phone, address, insurance_type, status)
SELECT 'RM00012349', '3171000000000005', 'Joko Widodo', 'male', '1965-06-21', '081100000005', 'Bogor', 'umum', 'active'
WHERE NOT EXISTS (SELECT 1 FROM patients WHERE medical_record_no = 'RM00012349');

INSERT INTO doctor_schedules (doctor_id, polyclinic_id, day_of_week, start_time, end_time, quota, status)
SELECT d.id, p.id, 5, '08:00:00', '12:00:00', 30, 'active'
FROM doctors d JOIN polyclinics p ON p.clinic_code = 'UMU'
WHERE d.doctor_code = 'DR-001'
AND NOT EXISTS (SELECT 1 FROM doctor_schedules WHERE doctor_id = d.id AND polyclinic_id = p.id AND day_of_week = 5);

INSERT INTO patient_visits (visit_no, patient_id, doctor_id, polyclinic_id, visit_date, visit_time, payment_type, chief_complaint, status, created_by)
SELECT 'VIS-DUMMY-001', p.id, d.id, c.id, CURDATE(), '08:15:00', 'umum', 'Demam dan batuk', 'completed', u.id
FROM patients p JOIN doctors d ON d.doctor_code='DR-001' JOIN polyclinics c ON c.clinic_code='UMU' LEFT JOIN users u ON u.email='pendaftaran@simrs.test'
WHERE p.medical_record_no='RM00012345'
AND NOT EXISTS (SELECT 1 FROM patient_visits WHERE visit_no='VIS-DUMMY-001');

INSERT INTO patient_visits (visit_no, patient_id, doctor_id, polyclinic_id, visit_date, visit_time, payment_type, chief_complaint, status, created_by)
SELECT 'VIS-DUMMY-002', p.id, d.id, c.id, CURDATE(), '08:30:00', 'bpjs', 'Kontrol anak', 'in_consultation', u.id
FROM patients p JOIN doctors d ON d.doctor_code='DR-002' JOIN polyclinics c ON c.clinic_code='ANA' LEFT JOIN users u ON u.email='pendaftaran@simrs.test'
WHERE p.medical_record_no='RM00012346'
AND NOT EXISTS (SELECT 1 FROM patient_visits WHERE visit_no='VIS-DUMMY-002');

INSERT INTO patient_visits (visit_no, patient_id, doctor_id, polyclinic_id, visit_date, visit_time, payment_type, chief_complaint, status, created_by)
SELECT 'VIS-DUMMY-003', p.id, d.id, c.id, CURDATE(), '09:00:00', 'umum', 'Sakit gigi', 'waiting', u.id
FROM patients p JOIN doctors d ON d.doctor_code='DR-003' JOIN polyclinics c ON c.clinic_code='GIG' LEFT JOIN users u ON u.email='pendaftaran@simrs.test'
WHERE p.medical_record_no='RM00012347'
AND NOT EXISTS (SELECT 1 FROM patient_visits WHERE visit_no='VIS-DUMMY-003');

INSERT INTO visit_queue (visit_id, polyclinic_id, doctor_id, queue_date, queue_no, status, called_at)
SELECT v.id, v.polyclinic_id, v.doctor_id, CURDATE(), 'A001', 'done', NOW()
FROM patient_visits v WHERE v.visit_no='VIS-DUMMY-001'
AND NOT EXISTS (SELECT 1 FROM visit_queue WHERE visit_id=v.id);

INSERT INTO visit_queue (visit_id, polyclinic_id, doctor_id, queue_date, queue_no, status, called_at)
SELECT v.id, v.polyclinic_id, v.doctor_id, CURDATE(), 'B001', 'in_service', NOW()
FROM patient_visits v WHERE v.visit_no='VIS-DUMMY-002'
AND NOT EXISTS (SELECT 1 FROM visit_queue WHERE visit_id=v.id);

INSERT INTO visit_queue (visit_id, polyclinic_id, doctor_id, queue_date, queue_no, status)
SELECT v.id, v.polyclinic_id, v.doctor_id, CURDATE(), 'D001', 'waiting'
FROM patient_visits v WHERE v.visit_no='VIS-DUMMY-003'
AND NOT EXISTS (SELECT 1 FROM visit_queue WHERE visit_id=v.id);

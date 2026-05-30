-- Dummy data dashboard Parking Management.
-- Aman dijalankan ulang: hanya menghapus dan membuat ulang data dengan prefix TRK-DMY-/PAY-DMY-.

INSERT INTO parking_areas (area_code, name, location, capacity, reserved_capacity, status, created_at)
VALUES
    ('A', 'Area A (Depan)', 'Depan lobby utama', 100, 5, 'active', NOW()),
    ('B', 'Area B (Gedung Utama)', 'Sisi gedung utama', 100, 8, 'active', NOW()),
    ('C', 'Area C (Karyawan)', 'Area pegawai dan dokter', 80, 20, 'active', NOW()),
    ('D', 'Area D (Motor)', 'Area motor', 15, 0, 'active', NOW()),
    ('E', 'Area E (VIP)', 'Dekat IGD dan VIP', 20, 5, 'active', NOW())
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    location = VALUES(location),
    capacity = VALUES(capacity),
    reserved_capacity = VALUES(reserved_capacity),
    status = VALUES(status);

INSERT INTO parking_gates (gate_code, name, gate_type, area_id, device_type, status, created_at)
SELECT 'IN-01', 'Gate Masuk 1', 'entry', id, 'manual', 'active', NOW() FROM parking_areas WHERE area_code = 'A'
ON DUPLICATE KEY UPDATE name = VALUES(name), gate_type = VALUES(gate_type), area_id = VALUES(area_id), status = VALUES(status);

INSERT INTO parking_gates (gate_code, name, gate_type, area_id, device_type, status, created_at)
SELECT 'OUT-01', 'Gate Keluar 1', 'exit', id, 'manual', 'active', NOW() FROM parking_areas WHERE area_code = 'A'
ON DUPLICATE KEY UPDATE name = VALUES(name), gate_type = VALUES(gate_type), area_id = VALUES(area_id), status = VALUES(status);

INSERT INTO parking_gates (gate_code, name, gate_type, area_id, device_type, status, created_at)
SELECT 'IN-02', 'Gate Masuk 2', 'entry', id, 'manual', 'active', NOW() FROM parking_areas WHERE area_code = 'B'
ON DUPLICATE KEY UPDATE name = VALUES(name), gate_type = VALUES(gate_type), area_id = VALUES(area_id), status = VALUES(status);

INSERT INTO parking_gates (gate_code, name, gate_type, area_id, device_type, status, created_at)
SELECT 'OUT-02', 'Gate Keluar 2', 'exit', id, 'manual', 'active', NOW() FROM parking_areas WHERE area_code = 'B'
ON DUPLICATE KEY UPDATE name = VALUES(name), gate_type = VALUES(gate_type), area_id = VALUES(area_id), status = VALUES(status);

INSERT INTO parking_gates (gate_code, name, gate_type, area_id, device_type, status, created_at)
SELECT 'IN-MTR', 'Gate Masuk Motor', 'entry', id, 'manual', 'active', NOW() FROM parking_areas WHERE area_code = 'D'
ON DUPLICATE KEY UPDATE name = VALUES(name), gate_type = VALUES(gate_type), area_id = VALUES(area_id), status = VALUES(status);

INSERT INTO parking_vehicle_types (type_code, name, category, is_free, status, created_at)
VALUES
    ('MBL', 'Mobil', 'mobil', 0, 'active', NOW()),
    ('MTR', 'Motor', 'motor', 0, 'active', NOW()),
    ('MBG', 'Motor Besar', 'motor', 0, 'active', NOW()),
    ('AMB', 'Ambulance', 'ambulance', 1, 'active', NOW()),
    ('OTH', 'Lainnya', 'other', 0, 'active', NOW()),
    ('EMP', 'Kendaraan Dokter/Pegawai', 'doctor_employee', 0, 'active', NOW()),
    ('VIP', 'VIP', 'vip', 0, 'active', NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name), category = VALUES(category), is_free = VALUES(is_free), status = VALUES(status);

INSERT INTO parking_rates (rate_code, name, vehicle_type_id, rate_type, initial_minutes, initial_rate, next_hour_rate, progressive_rate, max_daily_rate, grace_minutes, lost_ticket_fee, inpatient_special_rate, status, created_at)
SELECT CONCAT('RATE-', type_code), CONCAT('Tarif ', name), id, 'hourly', 60,
    CASE type_code WHEN 'MTR' THEN 3000 WHEN 'MBG' THEN 5000 WHEN 'AMB' THEN 0 ELSE 5000 END,
    CASE type_code WHEN 'MTR' THEN 2000 WHEN 'MBG' THEN 3000 WHEN 'AMB' THEN 0 ELSE 5000 END,
    CASE type_code WHEN 'MTR' THEN 3000 WHEN 'MBG' THEN 5000 WHEN 'AMB' THEN 0 ELSE 7000 END,
    CASE type_code WHEN 'MTR' THEN 20000 WHEN 'MBG' THEN 30000 WHEN 'AMB' THEN 0 ELSE 50000 END,
    15,
    CASE type_code WHEN 'MTR' THEN 50000 WHEN 'AMB' THEN 0 ELSE 100000 END,
    CASE type_code WHEN 'MTR' THEN 10000 WHEN 'AMB' THEN 0 ELSE 25000 END,
    'active',
    NOW()
FROM parking_vehicle_types
WHERE type_code IN ('MBL','MTR','MBG','AMB','OTH','EMP','VIP')
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    vehicle_type_id = VALUES(vehicle_type_id),
    initial_rate = VALUES(initial_rate),
    next_hour_rate = VALUES(next_hour_rate),
    max_daily_rate = VALUES(max_daily_rate),
    lost_ticket_fee = VALUES(lost_ticket_fee),
    status = VALUES(status);

DELETE gl FROM parking_gate_logs gl JOIN parking_tickets t ON t.id = gl.ticket_id WHERE t.ticket_no LIKE 'TRK-DMY-%';
DELETE v FROM parking_validations v JOIN parking_tickets t ON t.id = v.ticket_id WHERE t.ticket_no LIKE 'TRK-DMY-%';
DELETE p FROM parking_payments p JOIN parking_tickets t ON t.id = p.ticket_id WHERE t.ticket_no LIKE 'TRK-DMY-%';
DELETE FROM parking_tickets WHERE ticket_no LIKE 'TRK-DMY-%';

SET @area_a = (SELECT id FROM parking_areas WHERE area_code = 'A');
SET @area_b = (SELECT id FROM parking_areas WHERE area_code = 'B');
SET @area_c = (SELECT id FROM parking_areas WHERE area_code = 'C');
SET @area_d = (SELECT id FROM parking_areas WHERE area_code = 'D');
SET @area_e = (SELECT id FROM parking_areas WHERE area_code = 'E');
SET @gate_in_1 = (SELECT id FROM parking_gates WHERE gate_code = 'IN-01');
SET @gate_out_1 = (SELECT id FROM parking_gates WHERE gate_code = 'OUT-01');
SET @gate_in_2 = (SELECT id FROM parking_gates WHERE gate_code = 'IN-02');
SET @gate_out_2 = (SELECT id FROM parking_gates WHERE gate_code = 'OUT-02');
SET @gate_motor = (SELECT id FROM parking_gates WHERE gate_code = 'IN-MTR');
SET @type_car = (SELECT id FROM parking_vehicle_types WHERE type_code = 'MBL');
SET @type_motor = (SELECT id FROM parking_vehicle_types WHERE type_code = 'MTR');
SET @type_big = (SELECT id FROM parking_vehicle_types WHERE type_code = 'MBG');
SET @type_amb = (SELECT id FROM parking_vehicle_types WHERE type_code = 'AMB');
SET @type_other = (SELECT id FROM parking_vehicle_types WHERE type_code = 'OTH');
SET @parking_user = (SELECT id FROM users WHERE email = 'parking.admin@simrs.test' LIMIT 1);

CREATE TEMPORARY TABLE simrs_seed_digits (n INT PRIMARY KEY);
INSERT INTO simrs_seed_digits (n)
SELECT 0 UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9;

CREATE TEMPORARY TABLE simrs_seed_seq (n INT PRIMARY KEY);
INSERT INTO simrs_seed_seq (n)
SELECT a.n + (b.n * 10) + (c.n * 100) + 1
FROM simrs_seed_digits a
CROSS JOIN simrs_seed_digits b
CROSS JOIN simrs_seed_digits c
WHERE a.n + (b.n * 10) + (c.n * 100) + 1 <= 260;

-- 6 hari sebelumnya untuk grafik pendapatan 7 hari.
INSERT INTO parking_tickets
    (ticket_no, qr_token, plate_number, vehicle_type_id, area_id, entry_gate_id, exit_gate_id, entry_time, exit_time, duration_minutes, calculated_amount, discount_amount, payable_amount, status, payment_status, source, created_by, checked_out_by, created_at, updated_at)
SELECT
    CONCAT('TRK-DMY-HIST-', LPAD(n, 4, '0')),
    CONCAT('QR-DMY-HIST-', LPAD(n, 4, '0')),
    CONCAT(ELT((n % 5) + 1, 'B', 'D', 'F', 'A', 'H'), ' ', 1200 + n, ' HIS'),
    ELT((n % 3) + 1, @type_car, @type_motor, @type_other),
    ELT((n % 5) + 1, @area_a, @area_b, @area_c, @area_d, @area_e),
    ELT((n % 2) + 1, @gate_in_1, @gate_in_2),
    ELT((n % 2) + 1, @gate_out_1, @gate_out_2),
    DATE_ADD(DATE_SUB(CURDATE(), INTERVAL n DAY), INTERVAL 7 HOUR),
    DATE_ADD(DATE_SUB(CURDATE(), INTERVAL n DAY), INTERVAL 10 HOUR),
    180,
    ELT(n, 4250000, 5100000, 3250000, 6400000, 5000000, 9500000),
    0,
    ELT(n, 4250000, 5100000, 3250000, 6400000, 5000000, 9500000),
    'paid',
    'paid',
    'manual',
    @parking_user,
    @parking_user,
    DATE_SUB(NOW(), INTERVAL n DAY),
    DATE_SUB(NOW(), INTERVAL n DAY)
FROM simrs_seed_seq
WHERE n <= 6;

INSERT INTO parking_payments (payment_no, ticket_id, payment_date, payment_method, amount, status, reference_no, notes, created_by, created_at)
SELECT
    CONCAT('PAY-DMY-HIST-', LPAD(s.n, 4, '0')),
    t.id,
    DATE_SUB(CURDATE(), INTERVAL s.n DAY),
    'qris',
    t.payable_amount,
    'paid',
    CONCAT('QRISHIST', LPAD(s.n, 4, '0')),
    'Dummy pendapatan historis dashboard parkir',
    @parking_user,
    DATE_SUB(NOW(), INTERVAL s.n DAY)
FROM simrs_seed_seq s
JOIN parking_tickets t ON t.ticket_no = CONCAT('TRK-DMY-HIST-', LPAD(s.n, 4, '0'))
WHERE s.n <= 6;

-- 214 kendaraan aktif: total okupansi 214/315 = 68%.
INSERT INTO parking_tickets
    (ticket_no, qr_token, plate_number, vehicle_type_id, area_id, entry_gate_id, entry_time, duration_minutes, calculated_amount, discount_amount, payable_amount, status, payment_status, source, created_by, created_at)
SELECT
    CONCAT('TRK-DMY-ACT-', LPAD(n, 4, '0')),
    CONCAT('QR-DMY-ACT-', LPAD(n, 4, '0')),
    CONCAT(ELT((n % 5) + 1, 'B', 'D', 'F', 'A', 'H'), ' ', 1000 + n, ' ', ELT((n % 5) + 1, 'ABC', 'DEF', 'GHI', 'JKL', 'MNO')),
    CASE
        WHEN n <= 126 THEN @type_car
        WHEN n <= 192 THEN @type_motor
        WHEN n <= 204 THEN @type_big
        WHEN n <= 208 THEN @type_amb
        ELSE @type_other
    END,
    CASE
        WHEN n <= 85 THEN @area_a
        WHEN n <= 157 THEN @area_b
        WHEN n <= 192 THEN @area_c
        WHEN n <= 204 THEN @area_d
        ELSE @area_e
    END,
    CASE WHEN n <= 204 THEN ELT((n % 2) + 1, @gate_in_1, @gate_in_2) ELSE @gate_motor END,
    CASE
        WHEN n <= 13 THEN DATE_ADD(CURDATE(), INTERVAL ((6 * 60) + (n * 3)) MINUTE)
        ELSE DATE_ADD(DATE_SUB(CURDATE(), INTERVAL ((n % 5) + 1) DAY), INTERVAL ((6 * 60) + (n % 180)) MINUTE)
    END,
    0,
    0,
    0,
    0,
    'active',
    'unpaid',
    'manual',
    @parking_user,
    CASE
        WHEN n <= 13 THEN DATE_ADD(CURDATE(), INTERVAL ((6 * 60) + (n * 3)) MINUTE)
        ELSE DATE_ADD(DATE_SUB(CURDATE(), INTERVAL ((n % 5) + 1) DAY), INTERVAL ((6 * 60) + (n % 180)) MINUTE)
    END
FROM simrs_seed_seq
WHERE n <= 214;

-- 115 kendaraan keluar dan sudah bayar hari ini, total pendapatan Rp 6.250.000.
INSERT INTO parking_tickets
    (ticket_no, qr_token, plate_number, vehicle_type_id, area_id, entry_gate_id, exit_gate_id, entry_time, exit_time, duration_minutes, calculated_amount, discount_amount, payable_amount, status, payment_status, source, created_by, checked_out_by, created_at, updated_at)
SELECT
    CONCAT('TRK-DMY-PAID-', LPAD(n, 4, '0')),
    CONCAT('QR-DMY-PAID-', LPAD(n, 4, '0')),
    CONCAT(ELT((n % 5) + 1, 'B', 'D', 'F', 'A', 'H'), ' ', 3000 + n, ' ', ELT((n % 5) + 1, 'ABC', 'DEF', 'GHI', 'JKL', 'MNO')),
    CASE WHEN n <= 80 THEN @type_car WHEN n <= 110 THEN @type_motor ELSE @type_other END,
    ELT((n % 5) + 1, @area_a, @area_b, @area_c, @area_d, @area_e),
    ELT((n % 2) + 1, @gate_in_1, @gate_in_2),
    ELT((n % 2) + 1, @gate_out_1, @gate_out_2),
    DATE_ADD(CURDATE(), INTERVAL ((5 * 60) + n) MINUTE),
    DATE_ADD(CURDATE(), INTERVAL ((7 * 60) + n + (n % 90)) MINUTE),
    120 + (n % 90),
    CASE WHEN n <= 110 THEN 50000 ELSE 150000 END,
    0,
    CASE WHEN n <= 110 THEN 50000 ELSE 150000 END,
    'paid',
    'paid',
    'manual',
    @parking_user,
    @parking_user,
    DATE_ADD(CURDATE(), INTERVAL ((5 * 60) + n) MINUTE),
    DATE_ADD(CURDATE(), INTERVAL ((7 * 60) + n + (n % 90)) MINUTE)
FROM simrs_seed_seq
WHERE n <= 115;

INSERT INTO parking_payments (payment_no, ticket_id, payment_date, payment_method, amount, status, reference_no, notes, created_by, created_at)
SELECT
    CONCAT('PAY-DMY-TODAY-', LPAD(s.n, 4, '0')),
    t.id,
    CURDATE(),
    CASE WHEN s.n % 4 = 0 THEN 'qris' WHEN s.n % 4 = 1 THEN 'cash' WHEN s.n % 4 = 2 THEN 'debit' ELSE 'transfer' END,
    t.payable_amount,
    'paid',
    CONCAT('PAYTODAY', LPAD(s.n, 4, '0')),
    'Dummy pembayaran hari ini dashboard parkir',
    @parking_user,
    DATE_ADD(CURDATE(), INTERVAL ((7 * 60) + s.n + (s.n % 90)) MINUTE)
FROM simrs_seed_seq s
JOIN parking_tickets t ON t.ticket_no = CONCAT('TRK-DMY-PAID-', LPAD(s.n, 4, '0'))
WHERE s.n <= 115;

-- 28 ticket belum dibayar.
INSERT INTO parking_tickets
    (ticket_no, qr_token, plate_number, vehicle_type_id, area_id, entry_gate_id, exit_gate_id, entry_time, exit_time, duration_minutes, calculated_amount, discount_amount, payable_amount, status, payment_status, source, created_by, checked_out_by, created_at, updated_at)
SELECT
    CONCAT('TRK-DMY-UNPAID-', LPAD(n, 4, '0')),
    CONCAT('QR-DMY-UNPAID-', LPAD(n, 4, '0')),
    CONCAT(ELT((n % 5) + 1, 'B', 'D', 'F', 'A', 'H'), ' ', 5000 + n, ' ', ELT((n % 5) + 1, 'ABC', 'DEF', 'GHI', 'JKL', 'MNO')),
    CASE WHEN n <= 20 THEN @type_car ELSE @type_motor END,
    ELT((n % 5) + 1, @area_a, @area_b, @area_c, @area_d, @area_e),
    ELT((n % 2) + 1, @gate_in_1, @gate_in_2),
    ELT((n % 2) + 1, @gate_out_1, @gate_out_2),
    DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 1 DAY), INTERVAL ((8 * 60) + n) MINUTE),
    DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 1 DAY), INTERVAL ((11 * 60) + n) MINUTE),
    180,
    CASE WHEN n <= 20 THEN 25000 ELSE 10000 END,
    0,
    CASE WHEN n <= 20 THEN 25000 ELSE 10000 END,
    'unpaid',
    'unpaid',
    'manual',
    @parking_user,
    @parking_user,
    DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 1 DAY), INTERVAL ((8 * 60) + n) MINUTE),
    DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 1 DAY), INTERVAL ((11 * 60) + n) MINUTE)
FROM simrs_seed_seq
WHERE n <= 28;

-- 3 ticket lost.
INSERT INTO parking_tickets
    (ticket_no, qr_token, plate_number, vehicle_type_id, area_id, entry_gate_id, entry_time, duration_minutes, calculated_amount, discount_amount, payable_amount, status, payment_status, source, notes, created_by, created_at, updated_at)
SELECT
    CONCAT('TRK-DMY-LOST-', LPAD(n, 4, '0')),
    CONCAT('QR-DMY-LOST-', LPAD(n, 4, '0')),
    CONCAT(ELT(n, 'B', 'D', 'F'), ' ', 7000 + n, ' LOST'),
    @type_car,
    ELT(n, @area_a, @area_b, @area_e),
    ELT((n % 2) + 1, @gate_in_1, @gate_in_2),
    DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 1 DAY), INTERVAL ((4 * 60) + n) MINUTE),
    0,
    100000,
    0,
    100000,
    'lost_ticket',
    'unpaid',
    'manual',
    'Dummy ticket lost dashboard parkir',
    @parking_user,
    DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 1 DAY), INTERVAL ((4 * 60) + n) MINUTE),
    NOW()
FROM simrs_seed_seq
WHERE n <= 3;

INSERT INTO parking_gate_logs (ticket_id, gate_id, action, response_status, response_message, created_by, created_at)
SELECT t.id, @gate_in_1, 'open_entry', 'simulated', 'Gate masuk terbuka', @parking_user, DATE_SUB(NOW(), INTERVAL 5 MINUTE)
FROM parking_tickets t WHERE t.ticket_no = 'TRK-DMY-ACT-0001'
UNION ALL
SELECT t.id, @gate_out_1, 'open_exit', 'simulated', 'Gate keluar terbuka', @parking_user, DATE_SUB(NOW(), INTERVAL 4 MINUTE)
FROM parking_tickets t WHERE t.ticket_no = 'TRK-DMY-PAID-0001'
UNION ALL
SELECT t.id, @gate_in_2, 'open_entry', 'simulated', 'Gate masuk terbuka', @parking_user, DATE_SUB(NOW(), INTERVAL 3 MINUTE)
FROM parking_tickets t WHERE t.ticket_no = 'TRK-DMY-ACT-0002'
UNION ALL
SELECT t.id, @gate_out_2, 'open_exit', 'simulated', 'Gate keluar terbuka', @parking_user, DATE_SUB(NOW(), INTERVAL 2 MINUTE)
FROM parking_tickets t WHERE t.ticket_no = 'TRK-DMY-PAID-0002'
UNION ALL
SELECT t.id, @gate_motor, 'open_entry', 'simulated', 'Gate motor terbuka', @parking_user, DATE_SUB(NOW(), INTERVAL 1 MINUTE)
FROM parking_tickets t WHERE t.ticket_no = 'TRK-DMY-ACT-0130';

DROP TEMPORARY TABLE IF EXISTS simrs_seed_seq;
DROP TEMPORARY TABLE IF EXISTS simrs_seed_digits;

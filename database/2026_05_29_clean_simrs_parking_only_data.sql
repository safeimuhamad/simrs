-- Final data cleanup: fokus hanya SIMRS dan Parking Management.
-- Jalankan setelah backup database. Script ini aman dijalankan ulang.

SET FOREIGN_KEY_CHECKS = 0;

-- Bersihkan data user ERP/CMS/Event lama, pertahankan user dummy SIMRS/Parking.
DELETE FROM activity_logs;

DELETE FROM users
WHERE email IS NULL
   OR email NOT LIKE '%@simrs.test';

-- Bersihkan role lama ERP dan rapikan role yang masih dipakai SIMRS/Parking.
UPDATE roles
SET name = 'finance',
    description = 'Keuangan rumah sakit dan laporan pendapatan',
    status = 'active'
WHERE name = 'Finance';

DELETE rp
FROM role_permissions rp
LEFT JOIN roles r ON r.id = rp.role_id
WHERE r.id IS NULL
   OR r.name NOT IN (
        'super_admin',
        'admin_rs',
        'pendaftaran',
        'dokter',
        'perawat',
        'farmasi',
        'kasir',
        'finance',
        'manajemen',
        'parking_admin',
        'parking_operator',
        'cashier',
        'management'
   );

DELETE FROM roles
WHERE name NOT IN (
    'super_admin',
    'admin_rs',
    'pendaftaran',
    'dokter',
    'perawat',
    'farmasi',
    'kasir',
    'finance',
    'manajemen',
    'parking_admin',
    'parking_operator',
    'cashier',
    'management'
);

-- Pastikan user SIMRS yang tadinya menunjuk role Finance lama tetap menunjuk role finance.
UPDATE users u
JOIN roles r ON r.name = 'finance'
SET u.role_id = r.id
WHERE u.email = 'finance@simrs.test';

ALTER TABLE users MODIFY role VARCHAR(50) NULL;

UPDATE users u
JOIN roles r ON r.id = u.role_id
SET u.role = r.name;

-- Hapus permission lama non SIMRS/Parking/System.
DELETE rp
FROM role_permissions rp
LEFT JOIN permissions p ON p.id = rp.permission_id
WHERE p.id IS NULL
   OR p.module NOT IN (
        'simrs_dashboard',
        'simrs_patient',
        'simrs_doctor',
        'simrs_polyclinic',
        'simrs_schedule',
        'simrs_registration',
        'simrs_queue',
        'simrs_outpatient',
        'simrs_inpatient',
        'simrs_lab',
        'simrs_radiology',
        'simrs_medical_record',
        'simrs_prescription',
        'simrs_pharmacy',
        'simrs_billing',
        'simrs_cashier',
        'simrs_report',
        'parking_dashboard',
        'parking_master',
        'parking_ticket',
        'parking_checkin',
        'parking_checkout',
        'parking_payment',
        'parking_validation',
        'parking_report',
        'user',
        'role',
        'activity_logs'
   );

DELETE FROM permissions
WHERE module NOT IN (
    'simrs_dashboard',
    'simrs_patient',
    'simrs_doctor',
    'simrs_polyclinic',
    'simrs_schedule',
    'simrs_registration',
    'simrs_queue',
    'simrs_outpatient',
    'simrs_inpatient',
    'simrs_lab',
    'simrs_radiology',
    'simrs_medical_record',
    'simrs_prescription',
    'simrs_pharmacy',
    'simrs_billing',
    'simrs_cashier',
    'simrs_report',
    'parking_dashboard',
    'parking_master',
    'parking_ticket',
    'parking_checkin',
    'parking_checkout',
    'parking_payment',
    'parking_validation',
    'parking_report',
    'user',
    'role',
    'activity_logs'
);

-- Bersihkan rekening Micool dan transaksi bank lama. Bank dibuat ulang khusus rumah sakit.
UPDATE patient_payments SET bank_account_id = NULL;
UPDATE parking_payments SET bank_account_id = NULL;

TRUNCATE TABLE bank_transactions;
TRUNCATE TABLE bank_transfers;
TRUNCATE TABLE bank_accounts;

INSERT INTO bank_accounts
    (account_code, coa_id, account_name, bank_name, account_number, account_holder, account_type, opening_balance, current_balance, status)
VALUES
    ('RS-CASH-001', NULL, 'Kas Utama Rumah Sakit', 'Cash', '-', 'SIM Rumah Sakit', 'cash', 0, 0, 'active'),
    ('RS-BCA-001', NULL, 'BCA Operasional Rumah Sakit', 'BCA', '8880012026', 'PT SIM Rumah Sakit Sehat', 'bank', 0, 0, 'active'),
    ('RS-QRIS-001', NULL, 'Settlement QRIS Rumah Sakit', 'QRIS', 'QRIS-SIMRS-001', 'PT SIM Rumah Sakit Sehat', 'bank', 0, 0, 'active'),
    ('RS-EMONEY-001', NULL, 'Settlement E-Money Parkir', 'E-Money', 'EMONEY-SIMRS-001', 'PT SIM Rumah Sakit Sehat', 'bank', 0, 0, 'active');

-- Drop tabel ERP/CMS/Event lama yang masih tersisa setelah cleanup fokus SIMRS.
DROP TABLE IF EXISTS
    approval_request_steps,
    chart_of_accounts,
    event_client_milestones,
    event_client_notifications,
    event_documents,
    event_portal_contents,
    event_ticket_attendees,
    expense_items,
    journal_entry_lines,
    journal_entries,
    marketing_lead_followups,
    ops_schedule,
    unit_maintenance_checklists,
    unit_maintenance_documents,
    vehicle_maintenance_checklists,
    vehicle_maintenance_documents,
    vendor_public_products,
    website_cta,
    website_portfolio_images;

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO activity_logs
    (user_id, module, action, reference_id, reference_number, description, ip_address, user_agent, created_at)
SELECT
    u.id,
    'System - Cleanup',
    'cleanup',
    NULL,
    'SIMRS-PARKING-CLEAN',
    'Database dibersihkan dari data ERP lama. Data aktif difokuskan untuk SIMRS dan Parking Management.',
    '127.0.0.1',
    'SQL cleanup',
    NOW()
FROM users u
WHERE u.email = 'superadmin@simrs.test'
LIMIT 1;

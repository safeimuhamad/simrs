-- Cleanup fokus SIMRS.
-- Jalankan setelah backup database. Script ini memigrasikan item obat/alkes ke tabel SIMRS-native
-- lalu menghapus tabel lama ERP/CMS/Event yang tidak relevan.

CREATE TABLE IF NOT EXISTS medical_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
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
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    KEY idx_medical_items_status (status),
    KEY idx_medical_items_category (category),
    KEY idx_medical_items_sku (sku)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS medical_stock_movements (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    medical_item_id INT UNSIGNED NOT NULL,
    movement_type ENUM('in','out','adjustment') NOT NULL,
    reference_type VARCHAR(80) NULL,
    reference_id INT UNSIGNED NULL,
    quantity DECIMAL(14,2) NOT NULL DEFAULT 0,
    stock_before DECIMAL(14,2) NOT NULL DEFAULT 0,
    stock_after DECIMAL(14,2) NOT NULL DEFAULT 0,
    notes TEXT NULL,
    created_by INT UNSIGNED NULL,
    created_at DATETIME NULL,
    KEY idx_medical_stock_item (medical_item_id),
    KEY idx_medical_stock_reference (reference_type, reference_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO medical_items
    (id, sku, name, category, item_type, unit_name, current_stock, minimum_stock, unit_price, description, status, created_at, updated_at)
SELECT
    id,
    sku,
    name,
    CASE
        WHEN LOWER(category) = 'alkes' THEN 'alkes'
        WHEN LOWER(category) = 'farmasi' THEN 'bmhp'
        WHEN LOWER(category) = 'obat' THEN 'obat'
        ELSE 'lainnya'
    END,
    CASE WHEN LOWER(category) IN ('alkes','farmasi') THEN 'supply' ELSE 'medicine' END,
    COALESCE(NULLIF(unit_name, ''), 'pcs'),
    COALESCE(current_stock, 0),
    COALESCE(minimum_stock, 0),
    COALESCE(NULLIF(unit_price, 0), package_price, 0),
    description,
    CASE WHEN status = 'active' THEN 'active' ELSE 'inactive' END,
    created_at,
    NOW()
FROM quotation_products
WHERE is_medicine = 1 OR category IN ('Obat','Alkes','Farmasi');

DELETE rp
FROM role_permissions rp
JOIN permissions p ON p.id = rp.permission_id
WHERE p.module NOT IN (
    'simrs_dashboard',
    'simrs_patient',
    'simrs_doctor',
    'simrs_polyclinic',
    'simrs_schedule',
    'simrs_registration',
    'simrs_queue',
    'simrs_outpatient',
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

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS
    aging_receivables,
    approval_matrices,
    approval_requests,
    attendance_logs,
    attendances,
    blog_posts,
    brands,
    client_approvals,
    client_documents,
    client_event_milestones,
    client_notifications,
    customers,
    delivery_order_items,
    delivery_orders,
    departments,
    employee_cash_advances,
    employee_contracts,
    employees,
    event_client_access,
    event_portal_content,
    event_ticket_orders,
    event_tickets,
    expenses,
    goods_receipt_items,
    goods_receipts,
    invoice_items,
    invoice_payments,
    invoices,
    leave_requests,
    marketing_leads,
    master_events,
    mobile_api_tokens,
    overtime_requests,
    partner_units,
    partners,
    payroll_items,
    payroll_periods,
    payrolls,
    positions,
    purchase_order_items,
    purchase_orders,
    purchase_request_items,
    purchase_requests,
    quotation_items,
    quotation_products,
    quotations,
    recruitment_applicants,
    rental_items,
    rental_technicians,
    rentals,
    schedules,
    stock_movements,
    technicians,
    unit_maintenance_logs,
    unit_maintenances,
    units,
    vehicle_maintenance_logs,
    vehicle_maintenances,
    vehicle_usage_logs,
    vehicles,
    vendor_bill_items,
    vendor_bill_payments,
    vendor_bills,
    vendors,
    website_about,
    website_faqs,
    website_inquiries,
    website_portfolios,
    website_posts,
    website_products,
    website_services,
    website_settings,
    website_sliders,
    website_testimonials;

SET FOREIGN_KEY_CHECKS = 1;

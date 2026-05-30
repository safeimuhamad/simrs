-- Bulk dummy data farmasi: resep pending dan 20 item stok kritis.

INSERT INTO medical_items (sku, name, category, item_type, unit_name, current_stock, minimum_stock, unit_price, description, status, created_at)
SELECT sku, name, category, 'medicine', unit_name, current_stock, minimum_stock, unit_price, description, 'active', NOW()
FROM (
    SELECT 'OBT-CEF-200' sku, 'Cefixime 200 mg' name, 'obat' category, 'kapsul' unit_name, 4 current_stock, 30 minimum_stock, 6500 unit_price, 'Antibiotik sefalosporin' description UNION ALL
    SELECT 'OBT-CTM-004', 'CTM 4 mg', 'obat', 'tablet', 12, 100, 350, 'Antihistamin' UNION ALL
    SELECT 'OBT-OMZ-020', 'Omeprazole 20 mg', 'obat', 'kapsul', 8, 60, 1800, 'Obat lambung' UNION ALL
    SELECT 'OBT-MTF-500', 'Metformin 500 mg', 'obat', 'tablet', 15, 120, 900, 'Antidiabetes oral' UNION ALL
    SELECT 'OBT-AML-005', 'Amlodipine 5 mg', 'obat', 'tablet', 18, 150, 750, 'Antihipertensi' UNION ALL
    SELECT 'OBT-SMB-002', 'Salbutamol 2 mg', 'obat', 'tablet', 6, 80, 550, 'Bronkodilator' UNION ALL
    SELECT 'OBT-IBU-400', 'Ibuprofen 400 mg', 'obat', 'tablet', 9, 75, 1200, 'Analgesik antiinflamasi' UNION ALL
    SELECT 'OBT-LOR-010', 'Loratadine 10 mg', 'obat', 'tablet', 5, 60, 1500, 'Antialergi' UNION ALL
    SELECT 'OBT-ORS-001', 'Oralit Sachet', 'obat', 'sachet', 20, 200, 1000, 'Rehidrasi oral' UNION ALL
    SELECT 'OBT-VIT-C500', 'Vitamin C 500 mg', 'obat', 'tablet', 14, 120, 1250, 'Suplemen vitamin' UNION ALL
    SELECT 'OBT-ZNC-020', 'Zinc 20 mg', 'obat', 'tablet', 7, 80, 1000, 'Suplemen zinc' UNION ALL
    SELECT 'OBT-CPR-500', 'Ciprofloxacin 500 mg', 'obat', 'tablet', 3, 40, 4500, 'Antibiotik kuinolon' UNION ALL
    SELECT 'OBT-DMP-010', 'Domperidone 10 mg', 'obat', 'tablet', 10, 90, 1300, 'Antiemetik' UNION ALL
    SELECT 'OBT-CET-010', 'Cetirizine 10 mg', 'obat', 'tablet', 11, 100, 1100, 'Antihistamin' UNION ALL
    SELECT 'OBT-PRED-005', 'Prednisone 5 mg', 'obat', 'tablet', 6, 70, 900, 'Kortikosteroid' UNION ALL
    SELECT 'BMHP-INF-001', 'Infusion Set Dewasa', 'bmhp', 'pcs', 5, 50, 8500, 'Set infus dewasa' UNION ALL
    SELECT 'BMHP-SYR-003', 'Spuit 3 cc', 'bmhp', 'pcs', 22, 300, 1200, 'Spuit steril' UNION ALL
    SELECT 'BMHP-GLO-MED', 'Sarung Tangan Medis M', 'bmhp', 'box', 2, 25, 65000, 'Disposable gloves' UNION ALL
    SELECT 'ALK-ALC-070', 'Alkohol Swab 70%', 'alkes', 'box', 4, 35, 25000, 'Alkohol swab box' UNION ALL
    SELECT 'ALK-GAU-STER', 'Kasa Steril 16x16', 'alkes', 'pack', 8, 80, 18000, 'Kasa steril'
) src
WHERE NOT EXISTS (SELECT 1 FROM medical_items mi WHERE mi.sku = src.sku);

UPDATE medical_items
SET current_stock = CASE sku
    WHEN 'OBT-CEF-200' THEN 4 WHEN 'OBT-CTM-004' THEN 12 WHEN 'OBT-OMZ-020' THEN 8 WHEN 'OBT-MTF-500' THEN 15
    WHEN 'OBT-AML-005' THEN 18 WHEN 'OBT-SMB-002' THEN 6 WHEN 'OBT-IBU-400' THEN 9 WHEN 'OBT-LOR-010' THEN 5
    WHEN 'OBT-ORS-001' THEN 20 WHEN 'OBT-VIT-C500' THEN 14 WHEN 'OBT-ZNC-020' THEN 7 WHEN 'OBT-CPR-500' THEN 3
    WHEN 'OBT-DMP-010' THEN 10 WHEN 'OBT-CET-010' THEN 11 WHEN 'OBT-PRED-005' THEN 6 WHEN 'BMHP-INF-001' THEN 5
    WHEN 'BMHP-SYR-003' THEN 22 WHEN 'BMHP-GLO-MED' THEN 2 WHEN 'ALK-ALC-070' THEN 4 WHEN 'ALK-GAU-STER' THEN 8
    ELSE current_stock END,
    status = 'active'
WHERE sku IN (
    'OBT-CEF-200','OBT-CTM-004','OBT-OMZ-020','OBT-MTF-500','OBT-AML-005','OBT-SMB-002','OBT-IBU-400','OBT-LOR-010',
    'OBT-ORS-001','OBT-VIT-C500','OBT-ZNC-020','OBT-CPR-500','OBT-DMP-010','OBT-CET-010','OBT-PRED-005','BMHP-INF-001',
    'BMHP-SYR-003','BMHP-GLO-MED','ALK-ALC-070','ALK-GAU-STER'
);

INSERT INTO prescriptions (prescription_no, visit_id, patient_id, doctor_id, status, notes)
SELECT CONCAT('RX-BULK-', LPAD(n.seq, 3, '0')), v.id, v.patient_id, v.doctor_id,
       ELT(((n.seq - 1) % 4) + 1, 'pending', 'verified', 'prepared', 'pending'),
       'Resep dummy farmasi untuk validasi dan persiapan obat'
FROM (
    SELECT 1 seq UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL
    SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL
    SELECT 11 UNION ALL SELECT 12 UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15
) n
JOIN patient_visits v ON v.visit_no = ELT(((n.seq - 1) % 5) + 1, 'VIS-DUMMY-001','VIS-DUMMY-002','VIS-DUMMY-003','VIS-DASH-004','VIS-DASH-005')
WHERE NOT EXISTS (SELECT 1 FROM prescriptions rx WHERE rx.prescription_no = CONCAT('RX-BULK-', LPAD(n.seq, 3, '0')));

INSERT INTO prescription_items (prescription_id, product_id, item_name, dosage, frequency, duration, quantity, unit_name, price)
SELECT rx.id, mi.id, mi.name,
       CASE mi.unit_name WHEN 'tablet' THEN '1 tablet' WHEN 'kapsul' THEN '1 kapsul' ELSE 'sesuai instruksi' END,
       ELT(((n.seq - 1) % 3) + 1, '2x sehari', '3x sehari', '1x sehari'),
       ELT(((n.seq - 1) % 3) + 1, '3 hari', '5 hari', '7 hari'),
       ELT(((n.seq - 1) % 4) + 1, 6, 9, 10, 12),
       mi.unit_name,
       mi.unit_price
FROM (
    SELECT 1 seq, 'OBT-CEF-200' sku UNION ALL SELECT 2, 'OBT-OMZ-020' UNION ALL SELECT 3, 'OBT-MTF-500' UNION ALL
    SELECT 4, 'OBT-AML-005' UNION ALL SELECT 5, 'OBT-SMB-002' UNION ALL SELECT 6, 'OBT-IBU-400' UNION ALL
    SELECT 7, 'OBT-LOR-010' UNION ALL SELECT 8, 'OBT-ORS-001' UNION ALL SELECT 9, 'OBT-VIT-C500' UNION ALL
    SELECT 10, 'OBT-ZNC-020' UNION ALL SELECT 11, 'OBT-CPR-500' UNION ALL SELECT 12, 'OBT-DMP-010' UNION ALL
    SELECT 13, 'OBT-CET-010' UNION ALL SELECT 14, 'OBT-PRED-005' UNION ALL SELECT 15, 'OBT-CTM-004'
) n
JOIN prescriptions rx ON rx.prescription_no = CONCAT('RX-BULK-', LPAD(n.seq, 3, '0'))
JOIN medical_items mi ON mi.sku = n.sku
WHERE NOT EXISTS (SELECT 1 FROM prescription_items pi WHERE pi.prescription_id = rx.id AND pi.product_id = mi.id);

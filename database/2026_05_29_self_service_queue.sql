CREATE TABLE IF NOT EXISTS self_service_queues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    queue_no VARCHAR(20) NOT NULL,
    queue_date DATE NOT NULL,
    service_type VARCHAR(40) NOT NULL,
    polyclinic_id INT NULL,
    doctor_id INT NULL,
    service_name VARCHAR(150) NOT NULL,
    visitor_name VARCHAR(150) NULL,
    phone VARCHAR(50) NULL,
    counter_no VARCHAR(20) NOT NULL,
    status ENUM('waiting','called','serving','done','cancelled') DEFAULT 'waiting',
    printed_at DATETIME NULL,
    called_at DATETIME NULL,
    served_at DATETIME NULL,
    finished_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_self_queue_no_date (queue_no, queue_date),
    KEY idx_self_queue_date_status (queue_date, status),
    KEY idx_self_queue_service (service_type, polyclinic_id)
);

INSERT INTO self_service_queues (queue_no, queue_date, service_type, service_name, visitor_name, phone, counter_no, status, printed_at, called_at)
SELECT 'A001', CURDATE(), 'polyclinic', 'Poli Umum', 'Pasien Kiosk Demo', '081200000001', '1', 'called', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM self_service_queues WHERE queue_no = 'A001' AND queue_date = CURDATE());

INSERT INTO self_service_queues (queue_no, queue_date, service_type, service_name, visitor_name, phone, counter_no, status, printed_at)
SELECT 'B001', CURDATE(), 'polyclinic', 'Poli Anak', 'Pengunjung Demo', '081200000002', '2', 'waiting', NOW()
WHERE NOT EXISTS (SELECT 1 FROM self_service_queues WHERE queue_no = 'B001' AND queue_date = CURDATE());

INSERT INTO self_service_queues (queue_no, queue_date, service_type, service_name, visitor_name, phone, counter_no, status, printed_at)
SELECT 'L001', CURDATE(), 'laboratory', 'Laboratorium', 'Pasien Lab Demo', '081200000003', '4', 'waiting', NOW()
WHERE NOT EXISTS (SELECT 1 FROM self_service_queues WHERE queue_no = 'L001' AND queue_date = CURDATE());

INSERT INTO doctor_schedules (doctor_id, polyclinic_id, day_of_week, start_time, end_time, quota, status)
SELECT d.id, p.id, days.day_no, '08:00:00', '14:00:00', 30, 'active'
FROM doctors d
JOIN polyclinics p ON p.clinic_code = 'UMU'
JOIN (
    SELECT 1 AS day_no UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
) days
WHERE d.doctor_code = 'DR-001'
AND NOT EXISTS (
    SELECT 1 FROM doctor_schedules ds
    WHERE ds.doctor_id = d.id
      AND ds.polyclinic_id = p.id
      AND ds.day_of_week = days.day_no
);

INSERT INTO doctor_schedules (doctor_id, polyclinic_id, day_of_week, start_time, end_time, quota, status)
SELECT d.id, p.id, days.day_no, '09:00:00', '13:00:00', 24, 'active'
FROM doctors d
JOIN polyclinics p ON p.clinic_code = 'ANA'
JOIN (
    SELECT 1 AS day_no UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
) days
WHERE d.doctor_code = 'DR-002'
AND NOT EXISTS (
    SELECT 1 FROM doctor_schedules ds
    WHERE ds.doctor_id = d.id
      AND ds.polyclinic_id = p.id
      AND ds.day_of_week = days.day_no
);

INSERT INTO doctor_schedules (doctor_id, polyclinic_id, day_of_week, start_time, end_time, quota, status)
SELECT d.id, p.id, days.day_no, '08:30:00', '12:30:00', 20, 'active'
FROM doctors d
JOIN polyclinics p ON p.clinic_code = 'GIG'
JOIN (
    SELECT 1 AS day_no UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
) days
WHERE d.doctor_code = 'DR-003'
AND NOT EXISTS (
    SELECT 1 FROM doctor_schedules ds
    WHERE ds.doctor_id = d.id
      AND ds.polyclinic_id = p.id
      AND ds.day_of_week = days.day_no
);

INSERT INTO doctor_schedules (doctor_id, polyclinic_id, day_of_week, start_time, end_time, quota, status)
SELECT d.id, p.id, days.day_no, '10:00:00', '15:00:00', 22, 'active'
FROM doctors d
JOIN polyclinics p ON p.clinic_code = 'KBD'
JOIN (
    SELECT 1 AS day_no UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5
) days
WHERE d.doctor_code = 'DR-004'
AND NOT EXISTS (
    SELECT 1 FROM doctor_schedules ds
    WHERE ds.doctor_id = d.id
      AND ds.polyclinic_id = p.id
      AND ds.day_of_week = days.day_no
);

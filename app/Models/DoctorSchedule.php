<?php

class DoctorSchedule extends SimrsModel
{
    protected $table = 'doctor_schedules';
    protected $fillable = ['doctor_id','polyclinic_id','day_of_week','start_time','end_time','quota','status'];

    public function all($search = '', $limit = 20, $offset = 0)
    {
        $where = [];
        $params = [];

        $search = trim((string) $search);
        if ($search !== '') {
            $where[] = "(d.name LIKE ? OR d.specialist LIKE ? OR p.name LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $status = trim((string) ($_GET['status'] ?? ''));
        if ($status !== '') {
            $where[] = "ds.status = ?";
            $params[] = $status;
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "
            SELECT ds.*, d.name AS doctor_name, d.specialist, p.name AS polyclinic_name
            FROM doctor_schedules ds
            LEFT JOIN doctors d ON d.id = ds.doctor_id
            LEFT JOIN polyclinics p ON p.id = ds.polyclinic_id
            {$whereSql}
            ORDER BY ds.day_of_week ASC, ds.start_time ASC
            LIMIT ? OFFSET ?
        ";
        $stmt = $this->db->prepare($sql);
        $position = 1;
        foreach ($params as $param) {
            $stmt->bindValue($position++, $param);
        }
        $stmt->bindValue($position++, (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue($position, (int) $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll($search = '')
    {
        $where = [];
        $params = [];

        $search = trim((string) $search);
        if ($search !== '') {
            $where[] = "(d.name LIKE ? OR d.specialist LIKE ? OR p.name LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $status = trim((string) ($_GET['status'] ?? ''));
        if ($status !== '') {
            $where[] = "ds.status = ?";
            $params[] = $status;
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $stmt = $this->db->prepare("
            SELECT COUNT(*)
            FROM doctor_schedules ds
            LEFT JOIN doctors d ON d.id = ds.doctor_id
            LEFT JOIN polyclinics p ON p.id = ds.polyclinic_id
            {$whereSql}
        ");
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function activeByDay($day)
    {
        $stmt = $this->db->prepare("
            SELECT ds.*, d.name AS doctor_name, p.name AS polyclinic_name
            FROM doctor_schedules ds
            LEFT JOIN doctors d ON d.id = ds.doctor_id
            LEFT JOIN polyclinics p ON p.id = ds.polyclinic_id
            WHERE ds.status = 'active' AND ds.day_of_week = ?
            ORDER BY p.name ASC, ds.start_time ASC
        ");
        $stmt->execute([$day]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

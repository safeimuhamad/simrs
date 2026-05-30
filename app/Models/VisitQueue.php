<?php

class VisitQueue extends SimrsModel
{
    protected $table = 'visit_queue';
    protected $fillable = ['visit_id','polyclinic_id','doctor_id','queue_no','queue_date','status','called_at','served_at','finished_at'];

    public function today($status = '', $doctorId = null, array $polyclinicIds = [])
    {
        $params = [date('Y-m-d')];
        $where = "WHERE q.queue_date = ?";

        if ($status !== '') {
            $where .= " AND q.status = ?";
            $params[] = $status;
        }

        if ($doctorId) {
            $where .= " AND (q.doctor_id = ? OR v.doctor_id = ?)";
            $params[] = $doctorId;
            $params[] = $doctorId;
        } elseif (!empty($polyclinicIds)) {
            $placeholders = implode(',', array_fill(0, count($polyclinicIds), '?'));
            $where .= " AND q.polyclinic_id IN ({$placeholders})";
            foreach ($polyclinicIds as $polyclinicId) {
                $params[] = (int) $polyclinicId;
            }
        }

        $stmt = $this->db->prepare("
            SELECT q.*, v.visit_no, v.status AS visit_status, p.medical_record_no, p.name AS patient_name, d.name AS doctor_name, c.name AS polyclinic_name
            FROM visit_queue q
            LEFT JOIN patient_visits v ON v.id = q.visit_id
            LEFT JOIN patients p ON p.id = v.patient_id
            LEFT JOIN doctors d ON d.id = q.doctor_id
            LEFT JOIN polyclinics c ON c.id = q.polyclinic_id
            {$where}
            ORDER BY q.id ASC
        ");
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function changeStatus($id, $status)
    {
        $timeColumn = match ($status) {
            'called' => 'called_at',
            'in_service' => 'served_at',
            'done' => 'finished_at',
            default => null,
        };

        $data = ['status' => $status];
        if ($timeColumn) {
            $data[$timeColumn] = date('Y-m-d H:i:s');
        }

        return $this->update($id, $data);
    }
}

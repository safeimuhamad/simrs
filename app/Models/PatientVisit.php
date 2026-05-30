<?php

class PatientVisit extends SimrsModel
{
    protected $table = 'patient_visits';
    protected $fillable = ['visit_no','patient_id','doctor_id','polyclinic_id','schedule_id','visit_date','visit_time','payment_type','chief_complaint','status','fhir_encounter_id','created_by'];

    public function withRelations($id)
    {
        $stmt = $this->db->prepare("
            SELECT v.*, p.medical_record_no, p.name AS patient_name, p.gender, p.birth_date,
                   d.name AS doctor_name, d.specialist, c.name AS polyclinic_name
            FROM patient_visits v
            LEFT JOIN patients p ON p.id = v.patient_id
            LEFT JOIN doctors d ON d.id = v.doctor_id
            LEFT JOIN polyclinics c ON c.id = v.polyclinic_id
            WHERE v.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function list($status = '', $date = null, $doctorId = null, array $polyclinicIds = [])
    {
        $date = $date ?: date('Y-m-d');
        $params = [$date];
        $where = "WHERE v.visit_date = ?";

        if ($status !== '') {
            $where .= " AND v.status = ?";
            $params[] = $status;
        }

        if ($doctorId) {
            $where .= " AND v.doctor_id = ?";
            $params[] = $doctorId;
        } elseif (!empty($polyclinicIds)) {
            $placeholders = implode(',', array_fill(0, count($polyclinicIds), '?'));
            $where .= " AND v.polyclinic_id IN ({$placeholders})";
            foreach ($polyclinicIds as $polyclinicId) {
                $params[] = (int) $polyclinicId;
            }
        }

        $stmt = $this->db->prepare("
            SELECT v.*, p.medical_record_no, p.name AS patient_name, d.name AS doctor_name, c.name AS polyclinic_name, q.queue_no, q.status AS queue_status
            FROM patient_visits v
            LEFT JOIN patients p ON p.id = v.patient_id
            LEFT JOIN doctors d ON d.id = v.doctor_id
            LEFT JOIN polyclinics c ON c.id = v.polyclinic_id
            LEFT JOIN visit_queue q ON q.visit_id = v.id
            {$where}
            ORDER BY v.id DESC
        ");
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function changeStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }
}

<?php

class InpatientAdmission extends SimrsModel
{
    protected $table = 'inpatient_admissions';
    protected $fillable = ['admission_no','visit_id','patient_id','doctor_id','bed_id','admission_date','discharge_date','status','notes'];

    public function list($search = '')
    {
        $params = [];
        $where = '';

        if ($search !== '') {
            $where = "WHERE p.name LIKE ? OR p.medical_record_no LIKE ? OR ia.admission_no LIKE ? OR b.bed_no LIKE ?";
            $like = '%' . $search . '%';
            $params = [$like, $like, $like, $like];
        }

        $stmt = $this->db->prepare("
            SELECT ia.*, p.medical_record_no, p.name AS patient_name, d.name AS doctor_name,
                   b.bed_no, b.room_name, b.class_name
            FROM inpatient_admissions ia
            LEFT JOIN patients p ON p.id = ia.patient_id
            LEFT JOIN doctors d ON d.id = ia.doctor_id
            LEFT JOIN inpatient_beds b ON b.id = ia.bed_id
            {$where}
            ORDER BY ia.id DESC
        ");
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

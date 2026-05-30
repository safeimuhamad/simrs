<?php

class LabOrder extends SimrsModel
{
    protected $table = 'lab_orders';
    protected $fillable = ['order_no','visit_id','patient_id','doctor_id','order_date','tests','status','result_notes','fhir_service_request_id'];

    public function list($search = '')
    {
        $params = [];
        $where = '';

        if ($search !== '') {
            $where = "WHERE lo.order_no LIKE ? OR p.name LIKE ? OR p.medical_record_no LIKE ? OR lo.tests LIKE ?";
            $like = '%' . $search . '%';
            $params = [$like, $like, $like, $like];
        }

        $stmt = $this->db->prepare("
            SELECT lo.*, p.medical_record_no, p.name AS patient_name, d.name AS doctor_name, v.visit_no
            FROM lab_orders lo
            LEFT JOIN patients p ON p.id = lo.patient_id
            LEFT JOIN doctors d ON d.id = lo.doctor_id
            LEFT JOIN patient_visits v ON v.id = lo.visit_id
            {$where}
            ORDER BY lo.id DESC
        ");
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

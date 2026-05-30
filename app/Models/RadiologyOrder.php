<?php

class RadiologyOrder extends SimrsModel
{
    protected $table = 'radiology_orders';
    protected $fillable = ['order_no','visit_id','patient_id','doctor_id','order_date','examination','status','result_notes','fhir_service_request_id'];

    public function list($search = '')
    {
        $params = [];
        $where = '';

        if ($search !== '') {
            $where = "WHERE ro.order_no LIKE ? OR p.name LIKE ? OR p.medical_record_no LIKE ? OR ro.examination LIKE ?";
            $like = '%' . $search . '%';
            $params = [$like, $like, $like, $like];
        }

        $stmt = $this->db->prepare("
            SELECT ro.*, p.medical_record_no, p.name AS patient_name, d.name AS doctor_name, v.visit_no
            FROM radiology_orders ro
            LEFT JOIN patients p ON p.id = ro.patient_id
            LEFT JOIN doctors d ON d.id = ro.doctor_id
            LEFT JOIN patient_visits v ON v.id = ro.visit_id
            {$where}
            ORDER BY ro.id DESC
        ");
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

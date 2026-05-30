<?php

class ParkingValidation extends SimrsModel
{
    protected $table = 'parking_validations';
    protected $fillable = ['ticket_id','patient_visit_id','patient_id','validation_type','discount_type','discount_value','validated_by','validated_at','notes'];

    public function byTicket($ticketId)
    {
        $stmt = $this->db->prepare("
            SELECT pv.*, p.name AS patient_name, p.medical_record_no, v.visit_no
            FROM parking_validations pv
            LEFT JOIN patients p ON p.id = pv.patient_id
            LEFT JOIN patient_visits v ON v.id = pv.patient_visit_id
            WHERE pv.ticket_id = ?
            ORDER BY pv.id DESC
        ");
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

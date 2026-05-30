<?php

class ParkingMember extends SimrsModel
{
    protected $table = 'parking_members';
    protected $fillable = ['member_no','member_type','user_id','employee_id','doctor_id','name','plate_number','vehicle_type_id','valid_from','valid_to','status','notes'];

    public function findActiveByPlate($plateNumber)
    {
        $plateNumber = strtoupper(trim($plateNumber));
        $stmt = $this->db->prepare("
            SELECT * FROM parking_members
            WHERE UPPER(plate_number) = ?
              AND status = 'active'
              AND (valid_from IS NULL OR valid_from <= CURDATE())
              AND (valid_to IS NULL OR valid_to >= CURDATE())
            LIMIT 1
        ");
        $stmt->execute([$plateNumber]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

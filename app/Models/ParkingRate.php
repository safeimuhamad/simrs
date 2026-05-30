<?php

class ParkingRate extends SimrsModel
{
    protected $table = 'parking_rates';
    protected $fillable = ['rate_code','name','vehicle_type_id','rate_type','initial_minutes','initial_rate','next_hour_rate','progressive_rate','max_daily_rate','grace_minutes','lost_ticket_fee','inpatient_special_rate','status'];

    public function activeByVehicleType($vehicleTypeId)
    {
        $stmt = $this->db->prepare("SELECT * FROM parking_rates WHERE vehicle_type_id = ? AND status = 'active' ORDER BY id DESC LIMIT 1");
        $stmt->execute([$vehicleTypeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

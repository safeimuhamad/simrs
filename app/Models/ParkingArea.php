<?php

class ParkingArea extends SimrsModel
{
    protected $table = 'parking_areas';
    protected $fillable = ['area_code','name','location','capacity','reserved_capacity','status'];

    public function occupancy()
    {
        $stmt = $this->db->query("
            SELECT a.*, COUNT(t.id) AS active_count
            FROM parking_areas a
            LEFT JOIN parking_tickets t ON t.area_id = a.id AND t.status IN ('active','unpaid')
            GROUP BY a.id
            ORDER BY a.name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

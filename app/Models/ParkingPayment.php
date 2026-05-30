<?php

class ParkingPayment extends SimrsModel
{
    protected $table = 'parking_payments';
    protected $fillable = ['payment_no','ticket_id','patient_billing_id','bank_account_id','payment_date','payment_method','amount','status','reference_no','notes','created_by'];

    public function byTicket($ticketId)
    {
        $stmt = $this->db->prepare("SELECT * FROM parking_payments WHERE ticket_id = ? ORDER BY id ASC");
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function report($from, $to)
    {
        $stmt = $this->db->prepare("
            SELECT pp.*, t.ticket_no, t.plate_number, vt.name AS vehicle_type_name
            FROM parking_payments pp
            LEFT JOIN parking_tickets t ON t.id = pp.ticket_id
            LEFT JOIN parking_vehicle_types vt ON vt.id = t.vehicle_type_id
            WHERE pp.payment_date BETWEEN ? AND ?
            ORDER BY pp.payment_date DESC, pp.id DESC
        ");
        $stmt->execute([$from, $to]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

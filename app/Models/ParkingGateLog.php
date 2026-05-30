<?php

class ParkingGateLog extends SimrsModel
{
    protected $table = 'parking_gate_logs';
    protected $fillable = ['ticket_id','gate_id','action','response_status','response_message','created_by'];

    public function byTicket($ticketId)
    {
        $stmt = $this->db->prepare("
            SELECT gl.*, g.name AS gate_name
            FROM parking_gate_logs gl
            LEFT JOIN parking_gates g ON g.id = gl.gate_id
            WHERE gl.ticket_id = ?
            ORDER BY gl.id DESC
        ");
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

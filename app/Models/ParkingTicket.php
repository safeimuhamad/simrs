<?php

class ParkingTicket extends SimrsModel
{
    protected $table = 'parking_tickets';
    protected $fillable = ['ticket_no','qr_token','plate_number','vehicle_type_id','area_id','entry_gate_id','exit_gate_id','patient_visit_id','patient_id','member_id','entry_time','exit_time','duration_minutes','calculated_amount','discount_amount','payable_amount','status','payment_status','source','notes','created_by','checked_out_by'];

    public function openTickets($search = '')
    {
        $sql = $this->baseSelect() . " WHERE t.status IN ('active','unpaid')";
        $params = [];
        if ($search !== '') {
            $sql .= " AND (t.ticket_no LIKE ? OR t.plate_number LIKE ?)";
            $params = ["%{$search}%", "%{$search}%"];
        }
        $sql .= " ORDER BY t.entry_time DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function recent($search = '')
    {
        $sql = $this->baseSelect() . " WHERE 1=1";
        $params = [];
        if ($search !== '') {
            $sql .= " AND (t.ticket_no LIKE ? OR t.plate_number LIKE ?)";
            $params = ["%{$search}%", "%{$search}%"];
        }
        $sql .= " ORDER BY t.id DESC LIMIT 100";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function detail($id)
    {
        $stmt = $this->db->prepare($this->baseSelect() . " WHERE t.id = ? LIMIT 1");
        $stmt->execute([$id]);
        $ticket = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($ticket) {
            $ticket['payments'] = (new ParkingPayment())->byTicket($id);
            $ticket['validations'] = (new ParkingValidation())->byTicket($id);
            $ticket['logs'] = (new ParkingGateLog())->byTicket($id);
        }
        return $ticket;
    }

    public function byToken($token)
    {
        $stmt = $this->db->prepare("SELECT * FROM parking_tickets WHERE qr_token = ? LIMIT 1");
        $stmt->execute([$token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function activeByPlate($plate)
    {
        $stmt = $this->db->prepare($this->baseSelect() . " WHERE t.plate_number = ? AND t.status IN ('active','unpaid') ORDER BY t.id DESC LIMIT 1");
        $stmt->execute([strtoupper(trim((string) $plate))]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function kioskLookup($keyword = '')
    {
        $keyword = strtoupper(trim((string) $keyword));
        $sql = $this->baseSelect() . " WHERE t.status IN ('active','unpaid','paid')";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (t.ticket_no = ? OR t.plate_number = ? OR t.qr_token = ?)";
            $params = [$keyword, $keyword, $keyword];
        }

        $sql .= " ORDER BY FIELD(t.status, 'unpaid', 'active', 'paid'), t.id DESC LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function latestKioskTicket()
    {
        $stmt = $this->db->query($this->baseSelect() . " WHERE t.status IN ('active','unpaid','paid') ORDER BY t.id DESC LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function baseSelect()
    {
        return "
            SELECT t.*, a.name AS area_name, vt.name AS vehicle_type_name, vt.category AS vehicle_category,
                   eg.name AS entry_gate_name, xg.name AS exit_gate_name,
                   p.name AS patient_name, p.medical_record_no, v.visit_no,
                   m.name AS member_name
            FROM parking_tickets t
            LEFT JOIN parking_areas a ON a.id = t.area_id
            LEFT JOIN parking_vehicle_types vt ON vt.id = t.vehicle_type_id
            LEFT JOIN parking_gates eg ON eg.id = t.entry_gate_id
            LEFT JOIN parking_gates xg ON xg.id = t.exit_gate_id
            LEFT JOIN patients p ON p.id = t.patient_id
            LEFT JOIN patient_visits v ON v.id = t.patient_visit_id
            LEFT JOIN parking_members m ON m.id = t.member_id
        ";
    }
}

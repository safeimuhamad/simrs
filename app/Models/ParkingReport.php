<?php

class ParkingReport
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function dashboard()
    {
        $occupancy = (new ParkingArea())->occupancy();

        return [
            'entries_today' => $this->count("SELECT COUNT(*) FROM parking_tickets WHERE DATE(entry_time)=CURDATE()"),
            'exits_today' => $this->count("SELECT COUNT(*) FROM parking_tickets WHERE DATE(exit_time)=CURDATE()"),
            'active_vehicles' => $this->count("SELECT COUNT(*) FROM parking_tickets WHERE status='active'"),
            'income_today' => $this->sum("SELECT COALESCE(SUM(amount),0) FROM parking_payments WHERE payment_date=CURDATE() AND status='paid'"),
            'lost_ticket' => $this->count("SELECT COUNT(*) FROM parking_tickets WHERE status='lost_ticket'"),
            'unpaid' => $this->count("SELECT COUNT(*) FROM parking_tickets WHERE payment_status='unpaid' AND status='unpaid'"),
            'paid_today' => $this->count("SELECT COUNT(*) FROM parking_payments WHERE payment_date=CURDATE() AND status='paid'"),
            'avg_duration_today' => $this->sum("SELECT COALESCE(AVG(duration_minutes),0) FROM parking_tickets WHERE DATE(exit_time)=CURDATE()"),
            'total_capacity' => array_sum(array_map(fn($area) => (int) ($area['capacity'] ?? 0), $occupancy)),
            'occupancy' => $occupancy,
            'vehicle_types' => $this->activeByVehicleType(),
            'income_7_days' => $this->incomeLastDays(7),
            'recent_transactions' => $this->recentTransactions(5),
            'gate_logs' => $this->recentGateLogs(5),
        ];
    }

    public function occupancy()
    {
        return (new ParkingArea())->occupancy();
    }

    public function operatorDashboard()
    {
        $stats = $this->dashboard();
        $stats['income_today_split'] = $this->incomeTodaySplit();
        $stats['income_today_hours'] = $this->incomeTodayHours();
        $stats['active_recent'] = $this->activeRecentTickets(5);
        $stats['gate_queue_summary'] = $this->gateQueueSummary();

        return $stats;
    }

    public function gateQueue($search = '')
    {
        $sql = "
            SELECT
                t.id,
                t.ticket_no,
                t.plate_number,
                t.entry_time,
                t.exit_time,
                t.status,
                t.payment_status,
                a.name AS area_name,
                vt.name AS vehicle_type_name,
                TIMESTAMPDIFF(MINUTE, t.entry_time, COALESCE(t.exit_time, NOW())) AS duration_minutes
            FROM parking_tickets t
            LEFT JOIN parking_areas a ON a.id = t.area_id
            LEFT JOIN parking_vehicle_types vt ON vt.id = t.vehicle_type_id
            WHERE t.status IN ('active','unpaid','lost_ticket')
        ";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (t.ticket_no LIKE ? OR t.plate_number LIKE ? OR vt.name LIKE ? OR a.name LIKE ?)";
            $like = '%' . $search . '%';
            $params = [$like, $like, $like, $like];
        }

        $sql .= " ORDER BY t.status='lost_ticket' DESC, t.entry_time ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function income($from, $to)
    {
        $stmt = $this->db->prepare("
            SELECT
                p.payment_date,
                COUNT(p.id) AS total_transactions,
                COALESCE(SUM(p.amount),0) AS total_income,
                COALESCE(SUM(CASE WHEN p.payment_method='cash' THEN p.amount ELSE 0 END),0) AS cash_income,
                COALESCE(SUM(CASE WHEN p.payment_method<>'cash' THEN p.amount ELSE 0 END),0) AS non_cash_income
            FROM parking_payments p
            WHERE p.payment_date BETWEEN ? AND ?
              AND p.status = 'paid'
            GROUP BY p.payment_date
            ORDER BY p.payment_date DESC
        ");
        $stmt->execute([$from, $to]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function duration()
    {
        $stmt = $this->db->query("
            SELECT
                COALESCE(vt.name, 'Lainnya') AS vehicle_type_name,
                COUNT(t.id) AS total_tickets,
                COALESCE(AVG(t.duration_minutes),0) AS avg_duration,
                COALESCE(MIN(t.duration_minutes),0) AS min_duration,
                COALESCE(MAX(t.duration_minutes),0) AS max_duration
            FROM parking_tickets t
            LEFT JOIN parking_vehicle_types vt ON vt.id = t.vehicle_type_id
            WHERE t.duration_minutes IS NOT NULL
            GROUP BY vt.id, vt.name
            ORDER BY avg_duration DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function lostTickets($search = '')
    {
        $sql = "
            SELECT
                t.*,
                a.name AS area_name,
                vt.name AS vehicle_type_name
            FROM parking_tickets t
            LEFT JOIN parking_areas a ON a.id = t.area_id
            LEFT JOIN parking_vehicle_types vt ON vt.id = t.vehicle_type_id
            WHERE t.status = 'lost_ticket'
        ";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (t.ticket_no LIKE ? OR t.plate_number LIKE ? OR vt.name LIKE ? OR a.name LIKE ?)";
            $like = '%' . $search . '%';
            $params = [$like, $like, $like, $like];
        }

        $sql .= " ORDER BY t.updated_at DESC, t.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function count($sql)
    {
        return (int) $this->db->query($sql)->fetchColumn();
    }

    private function sum($sql)
    {
        return (float) $this->db->query($sql)->fetchColumn();
    }

    private function activeByVehicleType()
    {
        $stmt = $this->db->query("
            SELECT COALESCE(vt.name, 'Lainnya') AS name, COUNT(t.id) AS total
            FROM parking_tickets t
            LEFT JOIN parking_vehicle_types vt ON vt.id = t.vehicle_type_id
            WHERE t.status = 'active'
            GROUP BY vt.id, vt.name
            ORDER BY total DESC, name ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function incomeTodaySplit()
    {
        $stmt = $this->db->query("
            SELECT
                COALESCE(SUM(CASE WHEN payment_method='cash' THEN amount ELSE 0 END),0) AS cash,
                COALESCE(SUM(CASE WHEN payment_method<>'cash' THEN amount ELSE 0 END),0) AS non_cash
            FROM parking_payments
            WHERE payment_date = CURDATE()
              AND status = 'paid'
        ");

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['cash' => 0, 'non_cash' => 0];
    }

    private function incomeTodayHours()
    {
        $stmt = $this->db->query("
            SELECT HOUR(created_at) AS hour_label, COALESCE(SUM(amount),0) AS total
            FROM parking_payments
            WHERE payment_date = CURDATE()
              AND status = 'paid'
            GROUP BY HOUR(created_at)
        ");
        $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        $series = [];

        foreach ([0, 4, 8, 12, 16, 20, 24] as $hour) {
            if ($hour === 24) {
                $series[] = ['label' => '24:00', 'total' => (float) ($rows[23] ?? 0)];
                continue;
            }

            $total = 0;
            for ($i = $hour; $i < min(24, $hour + 4); $i++) {
                $total += (float) ($rows[$i] ?? 0);
            }

            $series[] = ['label' => sprintf('%02d:00', $hour), 'total' => $total];
        }

        return $series;
    }

    private function activeRecentTickets($limit)
    {
        $stmt = $this->db->prepare("
            SELECT
                t.*,
                a.name AS area_name,
                vt.name AS vehicle_type_name,
                TIMESTAMPDIFF(MINUTE, t.entry_time, NOW()) AS live_duration_minutes
            FROM parking_tickets t
            LEFT JOIN parking_areas a ON a.id = t.area_id
            LEFT JOIN parking_vehicle_types vt ON vt.id = t.vehicle_type_id
            WHERE t.status IN ('active','unpaid','paid')
            ORDER BY t.entry_time DESC, t.id DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function gateQueueSummary()
    {
        $stmt = $this->db->query("
            SELECT
                g.id,
                g.name,
                g.gate_type,
                COALESCE(a.name, '-') AS area_name,
                CASE
                    WHEN g.gate_type = 'exit' THEN COUNT(CASE WHEN t.status='unpaid' THEN 1 END)
                    ELSE COUNT(CASE WHEN t.status='active' AND DATE(t.entry_time)=CURDATE() THEN 1 END)
                END AS total
            FROM parking_gates g
            LEFT JOIN parking_areas a ON a.id = g.area_id
            LEFT JOIN parking_tickets t ON t.area_id = g.area_id
            WHERE g.status = 'active'
            GROUP BY g.id, g.name, g.gate_type, a.name
            ORDER BY g.gate_type='exit', g.id
            LIMIT 4
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function incomeLastDays($days)
    {
        $days = max(1, (int) $days);
        $from = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));
        $stmt = $this->db->prepare("
            SELECT payment_date, COALESCE(SUM(amount),0) AS total
            FROM parking_payments
            WHERE payment_date BETWEEN ? AND CURDATE()
              AND status = 'paid'
            GROUP BY payment_date
            ORDER BY payment_date ASC
        ");
        $stmt->execute([$from]);
        $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $series = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $series[] = [
                'date' => $date,
                'label' => date('d M', strtotime($date)),
                'total' => (float) ($rows[$date] ?? 0),
            ];
        }

        return $series;
    }

    private function recentTransactions($limit)
    {
        $stmt = $this->db->prepare("
            SELECT t.*, a.name AS area_name, vt.name AS vehicle_type_name
            FROM parking_tickets t
            LEFT JOIN parking_areas a ON a.id = t.area_id
            LEFT JOIN parking_vehicle_types vt ON vt.id = t.vehicle_type_id
            ORDER BY COALESCE(t.exit_time, t.entry_time) DESC, t.id DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function recentGateLogs($limit)
    {
        $stmt = $this->db->prepare("
            SELECT gl.*, g.name AS gate_name, g.gate_type, t.plate_number, vt.name AS vehicle_type_name
            FROM parking_gate_logs gl
            LEFT JOIN parking_gates g ON g.id = gl.gate_id
            LEFT JOIN parking_tickets t ON t.id = gl.ticket_id
            LEFT JOIN parking_vehicle_types vt ON vt.id = t.vehicle_type_id
            ORDER BY gl.id DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

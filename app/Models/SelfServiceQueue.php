<?php

class SelfServiceQueue
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function services()
    {
        $services = [];
        $stmt = $this->db->query("
            SELECT id, name, queue_prefix, location
            FROM polyclinics
            WHERE status = 'active'
            ORDER BY name ASC
        ");

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $poly) {
            $services[] = [
                'type' => 'polyclinic',
                'polyclinic_id' => (int) $poly['id'],
                'name' => $poly['name'],
                'subtitle' => trim(($poly['location'] ?? '') . ' - Antrean poli'),
                'prefix' => $poly['queue_prefix'] ?: 'A',
                'counter_no' => $this->counterFromPrefix($poly['queue_prefix'] ?: 'A'),
                'icon' => $this->iconForPolyclinic($poly['name']),
            ];
        }

        return array_merge($services, [
            ['type' => 'registration', 'polyclinic_id' => null, 'name' => 'Pendaftaran', 'subtitle' => 'Registrasi pasien dan informasi kunjungan', 'prefix' => 'R', 'counter_no' => '1', 'icon' => 'app_registration'],
            ['type' => 'laboratory', 'polyclinic_id' => null, 'name' => 'Laboratorium', 'subtitle' => 'Pengambilan sampel dan hasil lab', 'prefix' => 'L', 'counter_no' => '4', 'icon' => 'science'],
            ['type' => 'radiology', 'polyclinic_id' => null, 'name' => 'Radiologi', 'subtitle' => 'Rontgen, USG, dan pemeriksaan radiologi', 'prefix' => 'X', 'counter_no' => '5', 'icon' => 'radiology'],
            ['type' => 'pharmacy', 'polyclinic_id' => null, 'name' => 'Farmasi', 'subtitle' => 'Pengambilan dan konsultasi obat', 'prefix' => 'F', 'counter_no' => '6', 'icon' => 'medication'],
        ]);
    }

    public function createTicket($type, $polyclinicId, $doctorId, $visitorName, $phone)
    {
        $service = $this->resolveService($type, $polyclinicId);
        if (!$service) {
            throw new RuntimeException('Layanan tidak ditemukan.');
        }

        $queueDate = date('Y-m-d');
        $queueNo = $this->nextQueueNo($service['prefix'], $queueDate);

        $stmt = $this->db->prepare("
            INSERT INTO self_service_queues
            (queue_no, queue_date, service_type, polyclinic_id, doctor_id, service_name, visitor_name, phone, counter_no, status, printed_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'waiting', NOW())
        ");
        $stmt->execute([
            $queueNo,
            $queueDate,
            $service['type'],
            $service['polyclinic_id'],
            $doctorId ?: null,
            $service['name'],
            trim($visitorName) ?: null,
            trim($phone) ?: null,
            $service['counter_no'],
        ]);

        return $this->find($this->db->lastInsertId());
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM self_service_queues WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function latestCalled($limit = 6)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM self_service_queues
            WHERE queue_date = CURDATE()
              AND status IN ('called','serving')
            ORDER BY COALESCE(called_at, updated_at, created_at) DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function displayCalled($limit = 6)
    {
        $rows = array_merge(
            $this->displaySelfServiceRows(['called', 'serving'], 'called'),
            $this->displayVisitRows(['called', 'in_service'], 'called')
        );

        usort($rows, fn($a, $b) => strcmp((string) ($b['activity_time'] ?? ''), (string) ($a['activity_time'] ?? '')));

        return array_slice($rows, 0, (int) $limit);
    }

    public function waiting($limit = 10)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM self_service_queues
            WHERE queue_date = CURDATE()
              AND status = 'waiting'
            ORDER BY id ASC
            LIMIT ?
        ");
        $stmt->bindValue(1, (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function waitingForCalling($status = '')
    {
        $params = [date('Y-m-d')];
        $where = "WHERE queue_date = ?";

        if ($status !== '') {
            $where .= " AND status = ?";
            $params[] = $status;
        }

        $stmt = $this->db->prepare("
            SELECT
                ssq.*,
                ssq.visitor_name AS patient_name,
                'Self Service' AS medical_record_no,
                '-' AS doctor_name,
                ssq.service_name AS polyclinic_name,
                'self_service' AS queue_source
            FROM self_service_queues ssq
            {$where}
            ORDER BY ssq.id ASC
        ");
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function displayWaiting($limit = 10)
    {
        $rows = array_merge(
            $this->displaySelfServiceRows(['waiting'], 'waiting'),
            $this->displayVisitRows(['waiting'], 'waiting')
        );

        usort($rows, fn($a, $b) => strcmp((string) ($a['activity_time'] ?? ''), (string) ($b['activity_time'] ?? '')));

        return array_slice($rows, 0, (int) $limit);
    }

    public function summary()
    {
        $summary = ['waiting' => 0, 'called' => 0, 'serving' => 0, 'done' => 0, 'cancelled' => 0];

        foreach (['self_service_queues', 'visit_queue'] as $table) {
            $stmt = $this->db->query("
                SELECT status, COUNT(*) total
                FROM {$table}
                WHERE queue_date = CURDATE()
                GROUP BY status
            ");

            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $status = $row['status'] === 'in_service' ? 'serving' : $row['status'];
                $summary[$status] = ($summary[$status] ?? 0) + (int) $row['total'];
            }
        }

        return $summary;
    }

    public function changeStatus($id, $status)
    {
        $status = $status === 'in_service' ? 'serving' : $status;
        if (!in_array($status, ['waiting', 'called', 'serving', 'done', 'cancelled'], true)) {
            return false;
        }

        $timeColumn = match ($status) {
            'called' => 'called_at',
            'serving' => 'served_at',
            'done' => 'finished_at',
            default => null,
        };

        $sets = ['status = ?'];
        $params = [$status];

        if ($timeColumn) {
            $sets[] = "{$timeColumn} = NOW()";
        }

        $params[] = $id;
        $stmt = $this->db->prepare("
            UPDATE self_service_queues
            SET " . implode(', ', $sets) . "
            WHERE id = ?
        ");

        return $stmt->execute($params);
    }

    private function displaySelfServiceRows(array $statuses, $mode)
    {
        $placeholders = implode(',', array_fill(0, count($statuses), '?'));
        $stmt = $this->db->prepare("
            SELECT
                ssq.id AS source_id,
                ssq.queue_no,
                ssq.service_name,
                ssq.visitor_name,
                ssq.counter_no,
                ssq.status,
                " . ($mode === 'called' ? "COALESCE(ssq.called_at, ssq.updated_at, ssq.created_at)" : "ssq.created_at") . " AS activity_time
            FROM self_service_queues ssq
            WHERE ssq.queue_date = CURDATE()
              AND ssq.status IN ({$placeholders})
        ");
        $stmt->execute($statuses);

        return array_map(function ($row) {
            $row['display_id'] = 'self-' . $row['source_id'];
            $row['source'] = 'self_service';
            return $row;
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function displayVisitRows(array $statuses, $mode)
    {
        $placeholders = implode(',', array_fill(0, count($statuses), '?'));
        $stmt = $this->db->prepare("
            SELECT
                q.id AS source_id,
                q.queue_no,
                c.name AS service_name,
                p.name AS visitor_name,
                GREATEST(1, LEAST(9, ASCII(UPPER(LEFT(COALESCE(c.queue_prefix, 'A'), 1))) - 64)) AS counter_no,
                q.status,
                " . ($mode === 'called' ? "COALESCE(q.called_at, q.served_at, q.updated_at, q.created_at)" : "q.created_at") . " AS activity_time
            FROM visit_queue q
            LEFT JOIN patient_visits v ON v.id = q.visit_id
            LEFT JOIN patients p ON p.id = v.patient_id
            LEFT JOIN polyclinics c ON c.id = q.polyclinic_id
            WHERE q.queue_date = CURDATE()
              AND q.status IN ({$placeholders})
        ");
        $stmt->execute($statuses);

        return array_map(function ($row) {
            $row['display_id'] = 'visit-' . $row['source_id'];
            $row['source'] = 'visit_queue';
            return $row;
        }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    private function resolveService($type, $polyclinicId)
    {
        foreach ($this->services() as $service) {
            if ($service['type'] !== $type) {
                continue;
            }

            if ($type === 'polyclinic' && (int) $service['polyclinic_id'] !== (int) $polyclinicId) {
                continue;
            }

            return $service;
        }

        return null;
    }

    private function nextQueueNo($prefix, $date)
    {
        $prefix = strtoupper(substr((string) $prefix, 0, 1)) ?: 'A';
        $stmt = $this->db->prepare("
            SELECT queue_no
            FROM self_service_queues
            WHERE queue_date = ?
              AND queue_no LIKE ?
            ORDER BY queue_no DESC
            LIMIT 1
        ");
        $stmt->execute([$date, $prefix . '%']);
        $last = (string) $stmt->fetchColumn();
        $next = $last !== '' ? ((int) substr($last, 1)) + 1 : 1;

        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    private function counterFromPrefix($prefix)
    {
        $letter = strtoupper(substr((string) $prefix, 0, 1)) ?: 'A';
        $number = ord($letter) - 64;

        return (string) max(1, min(9, $number));
    }

    private function iconForPolyclinic($name)
    {
        $name = strtolower((string) $name);

        if (str_contains($name, 'gigi')) {
            return 'dentistry';
        }

        if (str_contains($name, 'anak')) {
            return 'child_care';
        }

        if (str_contains($name, 'kebidanan') || str_contains($name, 'obgyn')) {
            return 'pregnant_woman';
        }

        if (str_contains($name, 'penyakit dalam')) {
            return 'ecg_heart';
        }

        return 'stethoscope';
    }
}

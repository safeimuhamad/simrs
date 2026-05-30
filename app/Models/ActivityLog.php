<?php

class ActivityLog
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO activity_logs
            (
                user_id,
                module,
                action,
                reference_id,
                reference_number,
                description,
                ip_address,
                user_agent
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['user_id'] ?? null,
            $data['module'] ?? null,
            $data['action'] ?? null,
            $data['reference_id'] ?? null,
            $data['reference_number'] ?? null,
            $data['description'] ?? null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
    }

    public function latest($limit = 20)
    {
        $stmt = $this->db->prepare("
            SELECT 
                al.*,
                u.name AS user_name
            FROM activity_logs al
            LEFT JOIN users u ON u.id = al.user_id
            ORDER BY al.id DESC
            LIMIT {$limit}
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function paginate($limit = 20, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT 
                al.*,
                u.name AS user_name
            FROM activity_logs al
            LEFT JOIN users u ON u.id = al.user_id
            ORDER BY al.id DESC
            LIMIT {$limit} OFFSET {$offset}
        ");

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countAll()
    {
        $stmt = $this->db->query("
            SELECT COUNT(*) AS total
            FROM activity_logs
        ");

        $row = $stmt->fetch();

        return (int) ($row['total'] ?? 0);
    }
}
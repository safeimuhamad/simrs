<?php

class Doctor extends SimrsModel
{
    protected $table = 'doctors';
    protected $fillable = ['user_id','doctor_code','name','specialist','sip_no','phone','email','fhir_practitioner_id','status'];

    public function currentForUser($userId = null)
    {
        $userId = $userId ?? ($_SESSION['user_id'] ?? null);

        if (!$userId) {
            return null;
        }

        $stmt = $this->db->prepare("
            SELECT *
            FROM doctors
            WHERE user_id = ?
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($doctor) {
            return $doctor;
        }

        $userStmt = $this->db->prepare("SELECT email FROM users WHERE id = ? LIMIT 1");
        $userStmt->execute([$userId]);
        $email = trim((string) $userStmt->fetchColumn());

        if ($email === '') {
            return null;
        }

        $stmt = $this->db->prepare("
            SELECT *
            FROM doctors
            WHERE email = ?
            LIMIT 1
        ");
        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function assignedPolyclinicIds($doctorId)
    {
        if (!$doctorId) {
            return [];
        }

        $stmt = $this->db->prepare("
            SELECT DISTINCT polyclinic_id
            FROM doctor_schedules
            WHERE doctor_id = ?
              AND status = 'active'
        ");
        $stmt->execute([$doctorId]);

        return array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));
    }

    protected function searchClause($search)
    {
        if ($search === '') {
            return ['', []];
        }

        $like = '%' . $search . '%';
        return ['WHERE name LIKE ? OR doctor_code LIKE ? OR specialist LIKE ?', [$like, $like, $like]];
    }
}

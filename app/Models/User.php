<?php

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE email = ?
            AND status = 'active'
            LIMIT 1
        ");

        $stmt->execute([$username]);

        return $stmt->fetch();
    }

    public function updateLastLogin($id)
    {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET last_login = NOW() 
            WHERE id = ?
        ");

        return $stmt->execute([$id]);
    }

    public function countAll($search = '', $status = '')
    {
        [$where, $params] = $this->filterQuery($search, $status);

        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total 
            FROM users u
            LEFT JOIN roles r ON r.id = u.role_id
            {$where}
        ");

        $stmt->execute($params);

        $row = $stmt->fetch();

        return (int) ($row['total'] ?? 0);
    }

    public function getAll()
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            ORDER BY name ASC
        ");
        $stmt->execute();

        return $stmt->fetchAll();
    }
    public function getPaginated($limit, $offset, $search = '', $status = '')
    {
        [$where, $params] = $this->filterQuery($search, $status);

        $stmt = $this->db->prepare("
            SELECT 
                u.*,
                r.name AS role_name,
                NULL AS employee_name,
                NULL AS employee_code
            FROM users u
            LEFT JOIN roles r ON r.id = u.role_id
            {$where}
            ORDER BY u.id DESC
            LIMIT ? OFFSET ?
        ");

        $position = 1;
        foreach ($params as $param) {
            $stmt->bindValue($position++, $param);
        }

        $stmt->bindValue($position++, (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue($position, (int) $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    private function filterQuery($search = '', $status = '')
    {
        $where = [];
        $params = [];

        $search = trim((string) $search);
        if ($search !== '') {
            $where[] = "(u.name LIKE ? OR u.username LIKE ? OR u.email LIKE ? OR r.name LIKE ?)";
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $status = trim((string) $status);
        if ($status !== '') {
            $where[] = "u.status = ?";
            $params[] = $status;
        }

        return [$where ? 'WHERE ' . implode(' AND ', $where) : '', $params];
    }

    public function getPermissionsByRoleId($roleId)
    {
        $stmt = $this->db->prepare("
            SELECT p.permission_key
            FROM role_permissions rp
            INNER JOIN permissions p ON p.id = rp.permission_id
            WHERE rp.role_id = ?
        ");

        $stmt->execute([$roleId]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO users 
            (
                name,
                username,
                email,
                password,
                activation_token,
                activation_expires_at,
                role_id,
                employee_id,
                data_scope,
                status
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['name'],
            $data['username'],
            $data['email'],
            $data['password'], // sudah hash dari controller
            $data['activation_token'] ?? null,
            $data['activation_expires_at'] ?? null,
            $data['role_id'],
            $data['employee_id'] ?? null,
            $data['data_scope'] ?? 'own',
            $data['status'] ?? 'pending'
        ]);
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                u.*,
                r.name AS role_name,
                NULL AS employee_name,
                NULL AS employee_code
            FROM users u
            LEFT JOIN roles r ON r.id = u.role_id
            WHERE u.id = ?
            LIMIT 1
        ");

        $stmt->execute([$id]);

        return $stmt->fetch();
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("
            SELECT
                u.*,
                r.name AS role_name
            FROM users u
            LEFT JOIN roles r ON r.id = u.role_id
            WHERE u.email = ? OR u.username = ?
            LIMIT 1
        ");

        $stmt->execute([$email, $email]);

        return $stmt->fetch();
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE users SET
                name = ?,
                email = ?,
                role_id = ?,
                employee_id = ?,
                data_scope = ?,
                status = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['name'],
            $data['email'],
            $data['role_id'],
            $data['employee_id'] ?? null,
            $data['data_scope'] ?? 'own',
            $data['status'],
            $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("
            DELETE u
            FROM users u
            LEFT JOIN roles r ON r.id = u.role_id
            WHERE u.id = ?
            AND COALESCE(r.name, u.role, '') != 'super_admin'
        ");

        return $stmt->execute([$id]);
    }

    public function findByActivationToken($token)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE activation_token = ?
            AND status = 'pending'
            AND activation_expires_at >= NOW()
            LIMIT 1
        ");

        $stmt->execute([$token]);

        return $stmt->fetch();
    }

    public function activateAccount($id, $password)
    {
        $stmt = $this->db->prepare("
            UPDATE users SET
                password = ?,
                status = 'active',
                activation_token = NULL,
                activation_expires_at = NULL,
                activated_at = NOW()
            WHERE id = ?
            AND status = 'pending'
        ");

        return $stmt->execute([
            password_hash($password, PASSWORD_DEFAULT),
            $id
        ]);
    }

    public function saveResetToken($id, $token)
    {
        $stmt = $this->db->prepare("
            UPDATE users SET
                reset_token = ?,
                reset_expires_at = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $token,
            date('Y-m-d H:i:s', strtotime('+1 hour')),
            $id
        ]);
    }

    public function findByResetToken($token)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE reset_token = ?
            AND reset_expires_at >= NOW()
            AND status = 'active'
            LIMIT 1
        ");

        $stmt->execute([$token]);

        return $stmt->fetch();
    }

    public function updatePasswordByResetToken($id, $password)
    {
        $stmt = $this->db->prepare("
            UPDATE users SET
                password = ?,
                reset_token = NULL,
                reset_expires_at = NULL
            WHERE id = ?
        ");

        return $stmt->execute([
            password_hash($password, PASSWORD_DEFAULT),
            $id
        ]);
    }

    public function findByEmployeeId($employeeId)
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM users
            WHERE employee_id = ?
            LIMIT 1
        ");

        $stmt->execute([$employeeId]);

        return $stmt->fetch();
    }
}

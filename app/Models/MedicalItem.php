<?php

class MedicalItem
{
    private $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getPaginated($limit, $offset, $search = '')
    {
        $sql = "SELECT * FROM medical_items WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (name LIKE ? OR category LIKE ? OR sku LIKE ? OR status LIKE ?)";
            $params = ["%{$search}%", "%{$search}%", "%{$search}%", "%{$search}%"];
        }

        $status = trim((string) ($_GET['status'] ?? ''));
        if ($status !== '') {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY id DESC LIMIT ? OFFSET ?";
        $params[] = (int) $limit;
        $params[] = (int) $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $index => $value) {
            $stmt->bindValue($index + 1, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll($search = '')
    {
        $sql = "SELECT COUNT(*) FROM medical_items WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $sql .= " AND (name LIKE ? OR category LIKE ? OR sku LIKE ? OR status LIKE ?)";
            $params = ["%{$search}%", "%{$search}%", "%{$search}%", "%{$search}%"];
        }

        $status = trim((string) ($_GET['status'] ?? ''));
        if ($status !== '') {
            $sql .= " AND status = ?";
            $params[] = $status;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function activeMedicines()
    {
        $stmt = $this->db->query("
            SELECT *
            FROM medical_items
            WHERE status = 'active'
            ORDER BY name ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function lowStock()
    {
        $stmt = $this->db->query("
            SELECT *
            FROM medical_items
            WHERE status = 'active'
              AND current_stock <= minimum_stock
            ORDER BY current_stock ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM medical_items WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO medical_items
            (sku, name, category, item_type, unit_name, current_stock, minimum_stock, unit_price, description, status, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->execute([
            $data['sku'],
            $data['name'],
            $data['category'],
            $data['item_type'],
            $data['unit_name'],
            $data['current_stock'],
            $data['minimum_stock'],
            $data['unit_price'],
            $data['description'],
            $data['status'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE medical_items SET
                sku = ?,
                name = ?,
                category = ?,
                item_type = ?,
                unit_name = ?,
                current_stock = ?,
                minimum_stock = ?,
                unit_price = ?,
                description = ?,
                status = ?,
                updated_at = NOW()
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['sku'],
            $data['name'],
            $data['category'],
            $data['item_type'],
            $data['unit_name'],
            $data['current_stock'],
            $data['minimum_stock'],
            $data['unit_price'],
            $data['description'],
            $data['status'],
            $id,
        ]);
    }

    public function deactivate($id)
    {
        $stmt = $this->db->prepare("UPDATE medical_items SET status = 'inactive', updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function decreaseStock($id, $quantity, $referenceType, $referenceId, $notes, $userId = null)
    {
        $item = $this->find($id);
        if (!$item) {
            throw new RuntimeException('Item farmasi tidak ditemukan.');
        }

        $before = (float) ($item['current_stock'] ?? 0);
        $after = $before - (float) $quantity;

        $stmt = $this->db->prepare("UPDATE medical_items SET current_stock = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$after, $id]);

        $move = $this->db->prepare("
            INSERT INTO medical_stock_movements
            (medical_item_id, movement_type, reference_type, reference_id, quantity, stock_before, stock_after, notes, created_by, created_at)
            VALUES (?, 'out', ?, ?, ?, ?, ?, ?, ?, NOW())
        ");

        $move->execute([$id, $referenceType, $referenceId, (float) $quantity, $before, $after, $notes, $userId]);
    }
}

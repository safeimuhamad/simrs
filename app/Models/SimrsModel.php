<?php

class SimrsModel
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function all($search = '', $limit = 20, $offset = 0)
    {
        [$where, $params] = $this->searchClause($search);
        [$where, $params] = $this->appendStatusFilter($where, $params);
        $sql = "SELECT * FROM {$this->table} {$where} ORDER BY {$this->primaryKey} DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->prepare($sql);
        $index = 1;

        foreach ($params as $param) {
            $stmt->bindValue($index++, $param);
        }

        $stmt->bindValue($index++, (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue($index, (int) $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countAll($search = '')
    {
        [$where, $params] = $this->searchClause($search);
        [$where, $params] = $this->appendStatusFilter($where, $params);
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} {$where}");
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function active()
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE status = 'active' ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1");
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $data = $this->filterData($data);
        $columns = array_keys($data);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $sql = "INSERT INTO {$this->table} (" . implode(', ', $columns) . ") VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($data));

        return (int) $this->db->lastInsertId();
    }

    public function update($id, $data)
    {
        $data = $this->filterData($data);
        $sets = implode(', ', array_map(fn($column) => "{$column} = ?", array_keys($data)));
        $values = array_values($data);
        $values[] = $id;

        $stmt = $this->db->prepare("UPDATE {$this->table} SET {$sets} WHERE {$this->primaryKey} = ?");
        return $stmt->execute($values);
    }

    protected function filterData($data)
    {
        $filtered = [];

        foreach ($this->fillable as $column) {
            if (array_key_exists($column, $data)) {
                $filtered[$column] = $data[$column];
            }
        }

        return $filtered;
    }

    protected function searchClause($search)
    {
        if ($search === '') {
            return ['', []];
        }

        $like = '%' . $search . '%';
        return ['WHERE name LIKE ?', [$like]];
    }

    protected function appendStatusFilter($where, array $params, $column = 'status')
    {
        $status = trim((string) ($_GET['status'] ?? ''));

        if ($status === '') {
            return [$where, $params];
        }

        $where .= $where === '' ? "WHERE {$column} = ?" : " AND {$column} = ?";
        $params[] = $status;

        return [$where, $params];
    }
}

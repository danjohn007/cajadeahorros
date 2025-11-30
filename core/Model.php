<?php
/**
 * Modelo base del sistema
 * Sistema de Gestión Integral de Caja de Ahorros
 */

abstract class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function find($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->db->fetch($sql, ['id' => $id]);
    }

    public function findAll($orderBy = null) {
        $sql = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        return $this->db->fetchAll($sql);
    }

    public function findWhere($conditions, $params = [], $orderBy = null) {
        $sql = "SELECT * FROM {$this->table} WHERE {$conditions}";
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        return $this->db->fetchAll($sql, $params);
    }

    public function findOneWhere($conditions, $params = []) {
        $sql = "SELECT * FROM {$this->table} WHERE {$conditions} LIMIT 1";
        return $this->db->fetch($sql, $params);
    }

    public function create($data) {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data) {
        return $this->db->update($this->table, $data, "{$this->primaryKey} = :id", ['id' => $id]);
    }

    public function delete($id) {
        return $this->db->delete($this->table, "{$this->primaryKey} = :id", ['id' => $id]);
    }

    public function count($conditions = null, $params = []) {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        if ($conditions) {
            $sql .= " WHERE {$conditions}";
        }
        $result = $this->db->fetch($sql, $params);
        return $result['total'] ?? 0;
    }

    public function exists($conditions, $params = []) {
        return $this->count($conditions, $params) > 0;
    }

    public function paginate($page = 1, $perPage = 10, $conditions = null, $params = [], $orderBy = null) {
        $offset = ($page - 1) * $perPage;
        
        $sql = "SELECT * FROM {$this->table}";
        if ($conditions) {
            $sql .= " WHERE {$conditions}";
        }
        if ($orderBy) {
            $sql .= " ORDER BY {$orderBy}";
        }
        $sql .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $data = $this->db->fetchAll($sql, $params);
        $total = $this->count($conditions, $params);
        
        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    public function beginTransaction() {
        return $this->db->beginTransaction();
    }

    public function commit() {
        return $this->db->commit();
    }

    public function rollBack() {
        return $this->db->rollBack();
    }
}

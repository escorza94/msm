<?php
class Model {
    protected $db;
    protected $table;
    public function __construct() { $this->db = Database::getInstance(); }
    public function findAll() { return $this->db->query("SELECT * FROM {$this->table}")->fetchAll(); }
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}

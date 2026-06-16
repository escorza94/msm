<?php
class Database {
    private static $instance = null;
    private $conn;
    private function __construct() {
        $config = require BASE_PATH . '/config.php';
        $dsn = "mysql:host={$config['DB_HOST']};dbname={$config['DB_NAME']};charset=utf8mb4";
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
        try { $this->conn = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], $options); } catch (\PDOException $e) { die("Error de DB: " . $e->getMessage()); }
    }
    public static function getInstance() {
        if (self::$instance == null) { self::$instance = new Database(); }
        return self::$instance->conn;
    }
}

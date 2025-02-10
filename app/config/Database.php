<?php
namespace App\config;
use Exception, PDO, PDOException;
require_once __DIR__ . '/env.php';

class Database {
    public static Database $instance;
    private PDO $pdo;

    private function __construct() {
        try {
            $dsn = "pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT . ";options='--client_encoding=UTF8'";
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
            ]);
        } catch (PDOException $ex) {
            throw new Exception("Connection to the database failed: " . $ex->getMessage());
        }
    }
    public static function getInstance(): Database
    {
        if (!isset(self::$instance)) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
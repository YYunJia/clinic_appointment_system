<?php
// services/DatabaseService.php
class DatabaseService {
    private static $instance = null;
    private $connection;
    
    private $host = "localhost";
    private $dbname = "dental_clinic";
    private $username = "root";
    private $password = "";
    
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset=utf8";
            $this->connection = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get singleton instance of database connection
     * @return DatabaseService
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new DatabaseService();
        }
        return self::$instance;
    }
    
    /**
     * Get PDO connection
     * @return PDO
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Test database connection
     * @return bool
     */
    public function testConnection() {
        try {
            $this->connection->query("SELECT 1");
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get database info
     * @return array
     */
    public function getDatabaseInfo() {
        return [
            'host' => $this->host,
            'database' => $this->dbname,
            'username' => $this->username
        ];
    }
}
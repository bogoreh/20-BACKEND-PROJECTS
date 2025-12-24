<?php
require_once __DIR__ . '/../config/database.php';

class Database {
    private static $connection = null;
    
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                self::$connection = new mysqli(
                    DatabaseConfig::$host,
                    DatabaseConfig::$username,
                    DatabaseConfig::$password,
                    DatabaseConfig::$database,
                    DatabaseConfig::$port
                );
                
                if (self::$connection->connect_error) {
                    throw new Exception("Connection failed: " . self::$connection->connect_error);
                }
                
                self::$connection->set_charset("utf8mb4");
                
            } catch (Exception $e) {
                die("Database connection error: " . $e->getMessage());
            }
        }
        return self::$connection;
    }
    
    public static function closeConnection() {
        if (self::$connection !== null) {
            self::$connection->close();
            self::$connection = null;
        }
    }
}
?>
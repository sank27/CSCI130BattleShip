<?php

class Database {

    private static $db;
    private $connection;

    private $host = 'localhost';
    private $login = 'battleship';
    private $password = 'battleship';
    private $database = 'battleship';

    private function __construct() {
        $this->connection = new MySQLi($this->host, $this->login, $this->password, $this->database);
    }

    function __destruct() {
        $this->connection->close();
    }

    public static function getConnection() {
        if (self::$db == null) {
            self::$db = new Database();
        }
        return self::$db->connection;
    }
}

?>
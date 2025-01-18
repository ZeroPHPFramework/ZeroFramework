<?php
namespace Zero\Drivers;

use PDO;
use PDOException;

class MysqlDriver {

    public $connection;
    public $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->createConnection();
    }

    public function createConnection() {
        try {
            $this->connection = new PDO('mysql:host=' . $this->config['host'] . ';dbname=' . $this->config['database'] . ';charset=utf8', $this->config['username'], $this->config['password']);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->connection->setAttribute(PDO::ATTR_PERSISTENT, true);
        } catch (PDOException $e) {
            dd("Connection failed: " . $e->getMessage());
        }

        return $this->connection;
    }
}
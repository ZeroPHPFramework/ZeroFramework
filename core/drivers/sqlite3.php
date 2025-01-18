<?php
namespace Zero\Drivers;

use PDO;
use PDOException;

class Sqlite3Driver {

    public $connection;
    public $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->createConnection();
    }

    public function createConnection() {
        try {
            // dd($this->config['database']);
            // create file folder if not exists
            $folder = dirname($this->config['database']);
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }
            
            $this->connection = new PDO('sqlite:' . $this->config['database']);
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

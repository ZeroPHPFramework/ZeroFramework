<?php

namespace Zero\Lib;

use PDO;

class Database {

    public $generalDb;
    public $readDb;
    public $writeDb;

    public $dbalWriteDb;
    public $dbalReadDb;

    public $connector;

    public function __construct() {
        $this->connect();
    }

    public function connect() {
        $this->generalDb = new PDO('mysql:host=' . DEF_DB_HOST . ';dbname=' . DEF_DB_NAME . ';charset=utf8', DEF_DB_USER, DEF_DB_PWD);
        $this->generalDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->generalDb->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->generalDb->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->generalDb->setAttribute(PDO::ATTR_PERSISTENT, true);

        $this->readDb = new PDO('mysql:host=' . DEF_DB_READ_HOST . ';dbname=' . DEF_DB_NAME . ';charset=utf8', DEF_DB_USER, DEF_DB_PWD);
        $this->readDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->readDb->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->readDb->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->readDb->setAttribute(PDO::ATTR_PERSISTENT, true);

        $this->writeDb = new PDO('mysql:host=' . DEF_DB_WRITE_HOST . ';dbname=' . DEF_DB_NAME . ';charset=utf8', DEF_DB_USER, DEF_DB_PWD);
        $this->writeDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->writeDb->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $this->writeDb->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->writeDb->setAttribute(PDO::ATTR_PERSISTENT, true);
    }

    public function setConnector($connector) {
        $this->connector = $connector;
        return $this;
    }

    public function connection($type) {
        if($type == 'log') {
           $this->generalDb   = new PDO('mysql:host=' . DEF_DB_LOG_HOST . ';dbname=' . DEF_DB_LOG_DATABASE . ';charset=utf8', DEF_DB_LOG_USERNAME, DEF_DB_LOG_PASSWORD);
           $this->readDb      = new PDO('mysql:host=' . DEF_DB_LOG_HOST . ';dbname=' . DEF_DB_LOG_DATABASE . ';charset=utf8', DEF_DB_LOG_USERNAME, DEF_DB_LOG_PASSWORD);
           $this->writeDb     = new PDO('mysql:host=' . DEF_DB_LOG_HOST . ';dbname=' . DEF_DB_LOG_DATABASE . ';charset=utf8', DEF_DB_LOG_USERNAME, DEF_DB_LOG_PASSWORD);
        }
        return $this;
    }

    // Check is write or write
    public function isWrite($query) {
        $query_ex = explode(" ", trim(strtolower($query)),2);
        if (in_array($query_ex,array("alter","create","delete","drop","insert","truncate","update")))
            return true;
        return false;
    }

    public function escape($string) {
        $db = $this->isWrite($query) ? $this->writeDb : $this->readDb;
        return $db->quote($string);
    }

    public function query($query, $bind = null, $params = array(), $state = 'fetch', $debug=false) {
        $db    = null;
        $stmt  = null;

        if($this->connector == 'write') {
            $db = $this->writeDb;
        } elseif($this->connector == 'read') {
            $db = $this->readDb;
        } else {
            // $db = $this->isWrite($query) ? $this->writeDb : $this->readDb;
            $db = $this->generalDb;
        }

        $stmt = $db->prepare($query);

        if (strlen($bind) > 0 && count($params) > 0) {

           $splitedBind = str_split($bind);

            // if($debug) {
            //     die(json_encode($bind));
            // }
           $dump =[];
            
            foreach ($splitedBind as $key => $value) {
                // Mustbe support, float, string int and many data type
                $dataType = PDO::PARAM_STR;

                if ( $value == 'i') {
                    $dataType = PDO::PARAM_INT;
                } elseif ($value == 'b') {
                    $dataType = PDO::PARAM_INT;
                } elseif ($value == 'n') {
                    $dataType = PDO::PARAM_STR;
                } elseif ($value == 'd') {
                    $dataType = PDO::PARAM_INT;
                }

                $dump[] = [
                    $key + 1,
                    $params[$key],
                    $dataType
                ];
                $stmt->bindValue($key + 1, $params[$key], $dataType);
            }

        } elseif (is_array($bind) && count($params) == 0) {
           
           foreach($bind as $key => $value)  {
                // Check if keuys is number
                if(is_numeric($key)) {
                    $stmt->bindValue($key + 1, $value);
                } else {
                    $stmt->bindParam($key, $value);
                }
           }
        }

        $stmt->execute();

        if ($state == 'fetch') {
            $stmt = $stmt->fetchAll() ?? [];
        } elseif($state == 'first') {
            $stmt = $stmt->fetch();
        } elseif($state == 'create') {
            $stmt = $db->lastInsertId();
        } elseif($state == 'update') {
            $stmt = $stmt->rowCount();
        } elseif($state == 'delete') {
            $stmt = $stmt->rowCount();
        }
        return $stmt;
    }

    public function fetch($query, $bind=null, $params=null, $debug=false) {
        return $this->query($query, $bind, $params, 'fetch', $debug);
    }

    public function first($query, $bind=null, $params=null, $debug=false) {
        return $this->query($query, $bind, $params, 'first', $debug);
    }

    public function create($query, $bind=null, $params=null, $debug=false) {

        return $this->query($query, $bind, $params, 'create', $debug);
    }

    public function update($query, $bind=null, $params=null, $debug=false) {
        return $this->query($query, $bind, $params, 'update', $debug);
    }

    public function delete($query, $bind=null, $params=null, $debug=false) {
        return $this->query($query, $bind, $params, 'delete', $debug);
    }

    public function getDbalWriteDb() {
        return $this->dbalWriteDb;
    }

    public function getDbalReadDb() {
        return $this->dbalReadDb;
    }

}

class DB {
    public static function fetch($query, $bind=null, $params=null, $debug=false) {
        $db = new Database();
        return $db->fetch($query, $bind, $params, $debug);
    }

    public function select($query, $bind=null, $params=null, $debug=false) {
        return self::fetch($query, $bind, $params, $debug);
    }

    public static function first($query, $bind=null, $params=null, $debug=false) {
        $db = new Database();
        return $db->first($query, $bind, $params, $debug);
    }

    public static function create($query, $bind=null, $params=null, $debug=false) {
        $db = new Database();
        return $db->create($query, $bind, $params, $debug);
    }

    public static function update($query, $bind=null, $params=null, $debug=false) {
        $db = new Database();
        return $db->update($query, $bind, $params, $debug);
    }

    public static function delete($query, $bind=null, $params=null, $debug=false) {
        $db = new Database();
        return $db->delete($query, $bind, $params, $debug);
    }

    public static function query($query, $bind=null, $params=null, $debug=false) {
        $db = new Database();
        return $db->query($query, $bind, $params, $debug);
    }

    public static function escape($string) {
        $db = new Database();
        return $db->escape($string);
    }

    public static function connection($type) {
        $db = new Database();
        return $db->connection($type);
    }

    public static function write() {
        $db = new Database();
        return $db->setConnector('write');
    }

    public static function read() {
        $db = new Database();
        return $db->setConnector('read');
    }

}

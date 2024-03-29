<?php

namespace PHPMVC\LIB;


class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    private static $instance = null;
    private $dbh;
    private $stmt;
    private $error;

    private function __construct() {
        // Set DNS
        $dns = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = array(\PDO::ATTR_PERSISTENT => true, \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,);
        // Create PDO instance
        try {
            $this->dbh = new \PDO($dns, $this->user, $this->pass, $options);

        } catch (\PDOException $e) {
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }
    public static function getInstance()
    {
        if (self::$instance == null)
        {
            self::$instance = new Database();
        }

        return self::$instance;
    }
    // Prepare statement with query
    public function query($sql) {
        $this->stmt = $this->dbh->prepare($sql);
    }

    // Bind Values
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = \PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = \PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = \PDO::PARAM_NULL;
                    break;
                default:
                    $type = \PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    // Execute the prepared statement
    public function execute() {
       // var_dump($this->stmt) ;
        return $this->stmt->execute();
    }

    // Get result as array of objects
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll(\PDO::FETCH_OBJ);
    }

    // Get single record as object
    public function single() {
        $this->execute();
        return $this->stmt->fetch(\PDO::FETCH_OBJ);
    }

    // Get row count
    public function rowCount() {
        return $this->stmt->rowCount();
    }
}
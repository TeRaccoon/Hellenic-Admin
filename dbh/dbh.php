<?php
require_once('config.php');

class DatabaseConnection {
    private $server_name;
    private $dB_username;
    private $dB_password;
    private $dB_name;
    private $conn;

    public function __construct() {
        $this->server_name = DatabaseConfig::$server_name;
        $this->dB_username = DatabaseConfig::$dB_username;
        $this->dB_password = DatabaseConfig::$dB_password;
        $this->dB_name = DatabaseConfig::$dB_name;
    }

    public function connect($auto_commit = true) {
        $this->conn = new mysqli($this->server_name, $this->dB_username, $this->dB_password, $this->dB_name);

        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }

        $this->conn->autocommit($auto_commit);
    }

    public function get_connection() {
        return $this->conn;
    }

    public function close_connection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
    public function auto_commit($mode) {
        return $this->conn->autocommit($mode);
    }
    public function prepare_statement($sql) {
        return $this->conn->prepare($sql);
    }
    public function query($query) {
        require_once 'error-handler.php';
        try {
            $result = $this->conn->query($query);
    
            if ($result === false) {
                ErrorHandler::set_error('ERROR: There was an error querying the database!<br>' . $query, 'query', 'F_SQL_DBH_001', $this->conn->error);
                $this->abort();
            }
    
            return $result;
        } catch (Exception $e) {
            ErrorHandler::set_error('ERROR: There was an error querying the database!', 'query', 'F_SQL_DBH_002', $this->conn->error);
            $this->abort();            
        }
    }
    public function execute($stmt) {
        return $stmt->execute();
    }
    public function commit() {
        return $this->conn->commit();
    }
    public function abort() {
        $this->conn->rollback();
        $this->close_connection();

        header("Location: {$_SERVER["HTTP_REFERER"]}");
        exit();
    }
}
?>
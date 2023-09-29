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
        return $this->conn->query($query);
    }
    public function execute($stmt) {
        return $stmt->execute();
    }
    public function commit() {
        return $this->conn->commit();
    }
    public function retrieve_results($stmt, $close) {
        $results = null;
        
        $stmt->execute();
        $stmt->bind_result($results);
        $stmt->fetch();

        if ($close) {
            $stmt->close();
        }
        return $results;
    }
}
?>
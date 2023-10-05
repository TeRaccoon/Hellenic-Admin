<?php

class RetailItemDatabase {
    private $conn;
    private $db_utility;

    public function __construct($conn, $db_utility) {
        $this->conn = $conn;
        $this->db_utility = $db_utility;
    }

    function get_categories($username) {
        $query = 'SELECT DISTINCT category FROM retail_items ORDER BY category';
        $params = [
            ['type' => 's', 'value' => $username]
        ];
        $password_hash = $this->db_utility->execute_query($query, $params, 'assoc-array')['password'];
        return $password_hash;
    }

    function get_access_level($username) {
        $query = 'SELECT level FROM users WHERE username = ?';
        $params = [
            ['type' => 's', 'value' => $username]
        ];
        $access_level = $this->db_utility->execute_query($query, $params, 'assoc-array')['level'];
        return $access_level;
    }

    function user_exists($username) {
        $query = 'SELECT username FROM users WHERE username = ?';
        $params = [
            ['type' => 's', 'value' => $username]
        ];
        $row_count = $this->db_utility->execute_query($query, $params, 'row-count');
        return $row_count;
    }
}
?>
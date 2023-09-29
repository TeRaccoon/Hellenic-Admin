<?php

class DatabaseUtility {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function ExecuteQuery($query, $params, $results_format) {
        $stmt = $this->conn->prepareStatement($query);

        if ($stmt) {
            $paramTypes = "";
            $paramValues = [];

            foreach ($params as $param) {
                $paramTypes .= $param["type"];
                $paramValues[] = &$param["value"];
            }

            array_unshift($paramValues, $paramTypes);
            call_user_func_array([$stmt, "bind_param"], $paramValues);


            $this->conn->execute($stmt);

            switch ($results_format) {
                case "assoc-array":
                    return $this->BindResults($stmt);

                case "row-count":
                    return $stmt->num_rows;
                    
                default:
                    return true;
            }

        } else {
            return false; 
        }
    }

    public function BindResults($stmt) {
        $meta = $stmt->result_metadata();

        $result = array();
    
        while ($field = $meta->fetch_field()) {
            $result[$field->name] = null;
            $bindParams[] = &$result[$field->name];
        }
    
        call_user_func_array(array($stmt, 'bind_result'), $bindParams);
    
        $rows = array();

        while ($stmt->fetch()) {
            $row = array();
            foreach ($result as $key => $value) {
                $row[$key] = $value;
            }
            $rows[] = $row;
        }

        if (sizeof($rows) == 1) {
            return $rows[0];
        }

        return $rows;
    }
    public function construct_insert_query($table_name, $field_names, $submitted_data) {
        $nonEmptyFields = array_filter($field_names, function($field) {
            return $_POST[$field] !== "";
        });
        $fields_string = implode(", ", $nonEmptyFields);
        
        $nonEmptyValues = array_filter($submitted_data, function($data) {
            return $data !== "";
        });
        $values_string = implode(", ", array_map(function($data) {
            return "'$data'";
        }, $nonEmptyValues));
        
        $query = "INSERT INTO $table_name ($fields_string) VALUES ($values_string)";
        return $query;
    }
    public function construct_append_query($table_name, $field_names, $submitted_data) {
        $set_string = implode(", ", array_map(function($field) use ($submitted_data) {
            return "$field = " . ($submitted_data[$field] == "" ? "NULL" : "'{$submitted_data[$field]}'");
        }, $field_names));
        $query = 'UPDATE ' . $table_name . ' SET ' . $set_string . ' WHERE ID = ' . $_POST['id'];
        return $query;
    }
}
?>
<?php

class UserDatabase {
    private $conn;
    private $dbUtility;

    public function __construct($conn, $dbUtility) {
        $this->conn = $conn;
        $this->dbUtility = $dbUtility;
    }

    function get_user_password($username) {
        $query = "SELECT password FROM users WHERE username = ?";
        $params = [
            ["type" => "s", "value" => $username]
        ];
        $password_hash = $this->dbUtility->execute_query($query, $params, "assoc-array")["password"];
        return $password_hash;
    }

    function get_access_level($username) {
        $query = "SELECT level FROM users WHERE username = ?";
        $params = [
            ["type" => "s", "value" => $username]
        ];
        $access_level = $this->dbUtility->execute_query($query, $params, "assoc-array")["level"];
        return $access_level;
    }

    function user_exists($username) {
        $query = "SELECT username FROM users WHERE username = ?";
        $params = [
            ["type" => "s", "value" => $username]
        ];
        $row_count = $this->dbUtility->execute_query($query, $params, "row-count");
        return $row_count;
    }
}
class CustomerDatabase {
    private $conn;
    private $dbUtility;

    public function __construct($conn, $dbUtility) {
        $this->conn = $conn;
        $this->dbUtility = $dbUtility;
    }
    function get_customer_discount($customer_id) {
        $query = "SELECT discount FROM customers WHERE id = ?";
        $params = [
            ["type" => "s", "value" => $customer_id]
        ];
        $discount = $this->dbUtility->execute_query($query, $params, "assoc-array")["discount"];
        return $discount;
    }
    function set_invoice_values($net_value, $vat, $total, $id) {
        $query = "UPDATE invoices SET net_value = ?, VAT = ?, total = ? WHERE id = ?";
        $params = [
            ["type" => "d", "value" => $net_value],
            ["type" => "d", "value" => $vat],
            ["type" => "d", "value" => $total],
            ["type" => "i", "value" => $id]
        ];
        $this->dbUtility->execute_query($query, $params, false);
    }
}
class ItemDatabase {
    private $conn;
    private $dbUtility;

    public function __construct($conn, $dbUtility) {
        $this->conn = $conn;
        $this->dbUtility = $dbUtility;
    }

    function get_total_sold($item_id) {
        $query = "SELECT total_sold FROM items WHERE id = ?";
        $params = [
            ["type" => "i", "value" => $item_id]
        ];
        $total_sold = $this->dbUtility->execute_query($query, $params, "assoc-array")["total_sold"];
        return $total_sold;
    }
    function get_calculated_total_sold($item_id) {
        $query = "SELECT SUM(quantity) AS total_sold FROM invoiced_items WHERE item_id = ?";
        $params = [
            ["type" => "i", "value" => $item_id]
        ];
        $total_sold = $this->dbUtility->execute_query($query, $params, "assoc-array")["total_sold"];
        return $total_sold ?? 0;
    }
    function get_list_price($item_id) {
        $query = "SELECT list_price FROM items WHERE id = ?";
        $params = [
            ["type" => "i", "value" => $item_id]
        ];
        $list_price = $this->dbUtility->execute_query($query, $params, "assoc-array")["list_price"];
        return $list_price;
    }
    function get_invoiced_item_total($invoiced_item_id) {
        $query = "SELECT (ii.quantity * i.list_price) AS invoiced_item_total FROM invoiced_items AS ii INNER JOIN items AS i ON ii.item_id = i.id WHERE ii.id = ?";
        $params = [
            ["type" => "i", "value" => $invoiced_item_id]
        ];
        $invoiced_item_total = $this->dbUtility->execute_query($query, $params, "assoc-array")["invoiced_item_total"];
        return $invoiced_item_total;
    }
    function get_invoice_total($invoice_id) {
        $query = "SELECT SUM(invoiced_items.quantity * items.list_price) AS total FROM invoiced_items INNER JOIN items ON item_id = items.id WHERE invoice_id = ?";
        $params = [
            ["type" => "i", "value" =>  $invoice_id]
        ];
        $invoice_total = $this->dbUtility->execute_query($query, $params, "assoc-array")["total"];
        return $invoice_total;
    }
    function get_invoiced_items_data($invoiced_item_id) {
        $query = "SELECT item_id, quantity, vat_charge, invoice_id FROM invoiced_items WHERE id = ?";
        $params = [
            ["type" => "i", "value" => $invoiced_item_id]
        ];
        $invoiced_item_data = $this->dbUtility->execute_query($query, $params, "assoc-array");
        return $invoiced_item_data;
    }
    function set_total_sold($total_sold, $item_id) {
        $query = "UPDATE items SET total_sold = ? WHERE ID = ?";
        $params = [
            ["type" => "i", "value" => $total_sold],
            ["type" => "i", "value" => $item_id]
        ];
        $this->dbUtility->execute_query($query, $params, false);
    }
}

class CustomerPaymentsDatabase {
    private $conn;
    private $dbUtility;

    public function __construct($conn, $dbUtility) {
        $this->conn = $conn;
        $this->dbUtility = $dbUtility;
    }
    function get_total_invoice_payments($invoice_id) {
        $query = "SELECT SUM(amount) AS total FROM customer_payments WHERE invoice_id = ?";
        $params = [
            ["type" => "i", "value" => $invoice_id]
        ];
        $total_payments = $this->dbUtility->execute_query($query, $params, "assoc-array")["total"];
        return $total_payments;
    }
}

class InvoicedItemsDatabase {
    private $conn;
    private $dbUtility;

    public function __construct($conn, $dbUtility) {
        $this->conn = $conn;
        $this->dbUtility = $dbUtility;
    }
}
class InvoiceDatabase {
    private $conn;
    private $dbUtility;

    public function __construct($conn, $dbUtility) {
        $this->conn = $conn;
        $this->dbUtility = $dbUtility;
    }

    function get_customer_id($invoice_id) {
        $query = "SELECT customer_id FROM invoices WHERE id = ?";
        $params = [
            ["type" => "i", "value" => $invoice_id]
        ];
        $customer_id = $this->dbUtility->execute_query($query, $params, "assoc-array")["customer_id"];
        return $customer_id;
    }
    function get_invoice_price_data($invoice_id) {
        $query = "SELECT net_value, VAT, total FROM invoices WHERE id = ?";
        $params = [
            ["type" => "i", "value" => $invoice_id]
        ];
        $price_data = $this->dbUtility->execute_query($query, $params, "assoc-array");
        return $price_data;
    }
    function get_customer_debt($customer_id) {
        $query = "SELECT (SELECT SUM(total) FROM invoices WHERE customer_id = ?) - COALESCE((SELECT SUM(amount) FROM customer_payments WHERE customer_id = ? AND status = 'Processed' AND invoice_id IS NOT NULL), 0) AS total";
        $params = [
            ["type" => "i", "value" => $customer_id],
            ["type" => "i", "value" => $customer_id]
        ];
        $customer_debt = $this->dbUtility->execute_query($query, $params, "assoc-array")["total"];
        return $customer_debt;
    }
    function set_invoice_payment($status, $invoice_id) {
        if ($status == "Yes" || $status == "No") {
            $query = "UPDATE invoices SET payment_status = ? WHERE id = ?";
            $params = [
                ["type" => "s", "value" => $status],
                ["type" => "i", "value" => $invoice_id]
            ];
            $this->dbUtility->execute_query($query, $params, false);
        }
    }
}
?>
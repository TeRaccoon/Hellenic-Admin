<?php
session_start();

if (isset($_POST['action'])) {

    require_once 'dbh.php';
    require_once 'database_functions.php';
    require_once 'database_utility.php';

    $database = new DatabaseConnection();

    $database->connect(false);

    $database_utility = new DatabaseUtility($database);
    $user_database = new UserDatabase($database, $database_utility);

    switch ($_POST['action']) {
        case 'add':
            insert($database, $database_utility);
            break;

        case 'append':
            if ($_POST['table_name'] == 'users') {
                append_user($user_database, $_POST['username']);
            }
            append($database, $database_utility);
            break;

        case 'delete':
            drop($database, $_POST['table_name'], $_POST['id']);
            break;

        case 'login':
            login($user_database);
            break;
    }
    if ($_POST['action'] != 'login') {
        // header("Location: {$_SERVER["HTTP_REFERER"]}");
    }
    $database->close_connection();
    exit();
}
else {
    $_SESSION["error"] = "ERROR: Inconclusive call! Please contact administrator!";
    $_SESSION["error_type"] = "other";
    $_SESSION["error_code"] = "E_SQL-MD-0001";
    // header("Location: {$_SERVER["HTTP_REFERER"]}");
    exit();
}

function insert($conn, $database_utility) {
    $table_name = $_POST['table_name'];
    $field_names = get_field_names($conn, $table_name);
    $submitted_data = construct_submitted_data($field_names);

    $query = $database_utility->construct_insert_query($table_name, $field_names, $submitted_data);
    $conn -> query($query);
    $conn -> commit();
    synchronise($conn, $table_name, null, null);

}
function append($conn, $database_utility) {
    $table_name = $_POST['table_name'];
    $field_names = get_field_names($conn, $table_name);
    $submitted_data = construct_submitted_data($field_names);
    $query = $database_utility->construct_append_query($table_name, $field_names, $submitted_data);

    $conn -> query($query);
    $conn -> commit();

    synchronise($conn, $table_name, $_POST['id'], $query);
}
function append_user($user_database, $username) {
    $current_password = $user_database -> get_user_password();
    if ($current_password != $_POST['password']) {
        $_POST['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 10]);
    }
}
function drop($conn, $table_name, $ids) {
    if (str_contains($ids, ",")) {
        $ids = rtrim($ids, ',');
        $query = "DELETE FROM $table_name WHERE ID IN ($ids)";
    }
    else {
        $query = "DELETE FROM $table_name WHERE ID = '$ids'";
    }
    $ids = explode(',', $ids);
    synchronise($conn, $table_name, $ids, $query);
}

function check_date($original_date) {
    return date("Y-m-d", strtotime($original_date));
}
function synchronise($conn, $table_name, $id, $query_string) {
    require_once "database_functions.php";
    require_once "database_utility.php";

    $database_utility = new DatabaseUtility($conn);
    $customer_database = new CustomerDatabase($conn, $database_utility);
    $item_database = new ItemDatabase($conn, $database_utility);
    $invoice_database = new InvoiceDatabase($conn, $database_utility);

    $action = $_POST["action"];

    if ($id == null) {
        $id = get_row_contents($conn, "SELECT auto_increment from information_schema.tables WHERE table_name = '".$table_name."' AND table_schema = DATABASE()")[0][0] - 1;
    }
    switch ($table_name) {
        case "invoiced_items":
            $id = is_array($id) ? $id : [$id];
            foreach ($id as $item_id) {
                $invoiced_item_data = $item_database->get_invoiced_items_data($item_id);

                if ($action == "delete") { // Need to retrieve data from what was deleted before deleting
                    $database_utility->execute_delete_query($table_name, $item_id);
                }
                $customer_id = $invoice_database->get_customer_id($invoiced_item_data["invoice_id"]);
                update_invoiced_item($customer_database, $item_database, $invoiced_item_data, $customer_id);
            }

            if (!$conn -> commit()) {
                handle_error($conn, "ERROR: " + $action + " failed, synchronisation aborted! Please contact administrator!", $_POST['action'], "E_SQL-MD-007-F", $conn -> error, null);
            }
            //Calculate debt from updated invoices
            $debt = $invoice_database->get_customer_debt($customer_id);

            $conn->query("UPDATE customers SET outstanding_balance = '".$debt."' WHERE id = '".$customer_id."'");
            break;

        default:
            if (!$conn -> commit()) {
                handle_error($conn, "ERROR: " + $action + " failed, synchronisation aborted! Please contact administrator!", $action, "E_SQL-MD-003-D", $conn -> error, null);
            }
            break;
    }
    if ($query_string != null) {
        $conn -> query($query_string);
    }
    if (!$conn -> commit()) {
        echo "Error";
        handle_error($conn, "ERROR: " + $action + " failed, synchronisation aborted! Please contact administrator!", $action, "E_SQL-MD-003-A", $conn -> error, null);
    }
}
function handle_error($conn, $error, $error_type, $error_code, $error_description, $row_id) {
    if ($row_id != null) {
        $_SESSION["row_id"] = $row_id;
    }
    $_SESSION["error"] = $error;
    $_SESSION["error_type"] = $error_type;
    $_SESSION["error_code"] = $error_code;
    $_SESSION["error_description"] = $error_description;

    // header("Location: {$_SERVER["HTTP_REFERER"]}");
    $conn->rollback();
    $conn->close();
    exit();
}
function updateCustomerPayment($invoice_database, $conn, $payment_data, $id, $query_string) {
    // Check if payment status is processed
    if ($payment_data["status"][0] == "Processed" || $_POST["status"] == "Processed") {
        $invoice_id = $payment_data["invoice_id"][0];
        $customer_id = $payment_data["customer_id"][0];
        
        $total_invoice_payments = handle_data($conn, "ASSOC", "SELECT SUM(amount) AS total FROM customer_payments WHERE invoice_id = '".$invoice_id."'", "total");
        
        // Adjust total payments based on action
        if ($_POST["action"] == "delete" || $_POST["action"] == "append") {
            // Calculate total payments excluding current payment
            $total_invoice_payments = handle_data($conn, "ASSOC", "SELECT SUM(amount) AS total FROM customer_payments WHERE invoice_id = '".$invoice_id."' AND id != '".$id."'", "total");
            
            // Execute append / delete query
            $conn->query($query_string);
            
            // If action is append, add the new amount to the total
            if ($_POST["action"] == "append") {
                $total_invoice_payments += $_POST["amount"];
            }
        }
        
        // Get the total of the invoice referenced
        $invoice_total = handle_data($conn, "ASSOC", "SELECT total FROM invoices WHERE id = '".$invoice_id."'", "total");
        
        if ($invoice_id != null) {
            // Update invoice payment status based on total payments
            if ($total_invoice_payments >= $invoice_total) {
                $conn->query("UPDATE invoices SET payment_status = 'Yes' WHERE id = '".$invoice_id."'");
            } else {
                $conn->query("UPDATE invoices SET payment_status = 'No' WHERE id = '".$invoice_id."'");
            }
            $conn->commit();

            // Update last payment date
            $conn->query("UPDATE customers SET last_payment_date = '".$payment_data["date"][0]."' WHERE id = '".$customer_id."'");

            // Calculate customer debt
            $debt = $invoice_database->get_customer_debt($customer_id);
            
            // Reset customer credit entries for current invoice
            $conn->query("DELETE FROM customer_payments WHERE linked_payment_id = '".$id."'");
            
            // If customer is in credit
            if ($total_invoice_payments > $invoice_total) {
                // Cap customer payments to invoice total
                $conn->query("UPDATE customer_payments SET amount = '".$invoice_total."' WHERE id = '".$id."'");
                
                // Calculate and add excess payment money to credit
                $excess = $total_invoice_payments - $invoice_total;
                $conn->query("INSERT INTO customer_payments (customer_id, amount, reference, date, type, status, linked_payment_id) VALUES ('".$payment_data["customer_id"][0]."', '".$excess."', 'Credit', '".$payment_data["date"][0]."', 'Credit', '".$payment_data["status"][0]."', '".$id."')");
                
                // Reset debt since customer is in credit to prevent negative debt
                $debt = 0;
            }
            
            // Update customer's outstanding balance
            $conn->query("UPDATE customers SET outstanding_balance = '".$debt."' WHERE id = '".$customer_id."'");
        }
    }
}
function update_invoiced_item($customer_database, $item_database, $invoiced_item_data, $customer_id) {
    $discount = $customer_database->get_customer_discount($customer_id);
    $invoice_values = get_invoice_value($item_database, $invoiced_item_data, $discount);
    
    $item_id = $invoiced_item_data["item_id"];

    //Ammending item_invoice total to applied invoice
    $customer_database->set_invoice_values($invoice_values[0], $invoice_values[1], $invoice_values[2], $invoiced_item_data["invoice_id"]);

    //Appending total sold
    $total_sold = $item_database->get_calculated_total_sold($item_id);
    $item_database->set_total_sold($total_sold, $item_id);
}
function get_invoice_value($item_database, $item_data, $discount) {
    $invoice_id = $item_data["invoice_id"];
    $vat_charge = $item_data["vat_charge"];

    $invoiced_item_total = $item_database->get_invoice_total($invoice_id);
    

    $net = $discount == 0 ? $invoiced_item_total : $invoiced_item_total * (1 - $discount / 100);
    $vat = $vat_charge == "Yes" ? $net * 0.2 : 0;
    $total = $net + $vat;

    return [
        0 => round($net, 2),
        1 => round($vat, 2),
        2 => round($total, 2),
    ];
}
function get_row_contents($conn, $query_string) {
    $query = $conn->query($query_string);
    $contents = $query->fetch_all();
    return $contents;
 }

function handle_data($conn, $data_type, $query, $offset) {
    $results = $conn->query($query);
    if ($results) {
        switch ($data_type) {
            case "ASSOC":
                if ($offset != null && $results->num_rows > 0) {
                    $data = $results -> fetch_array(MYSQLI_ASSOC)[$offset];
                    if ($data == null) {
                        return "0";
                    }
                    return $data;
                }
                return $results -> fetch_array(MYSQLI_ASSOC);

            case "NUM":
                return $results -> fetch_array(MYSQLI_NUM);

            case "ROW_COUNT":
                return $results -> num_rows;

            case "ALL":
                return $results->fetch_all();

            case "ASSOC-ALL":
                if ($offset == null) {
                    $data = array();
                    while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
                        foreach ($row as $key => $value) {
                            if (!isset($data[$key])) {
                                $data[$key] = array();
                            }
                            $data[$key][] = $value;
                        }
                    }
                    return $data;
                }
                else {
                    $data = array();
                    while ($row = $results->fetch_array(MYSQLI_ASSOC)) {
                        if ($row != null) {
                            $data[] = $row[$offset];
                        }
                    }
                    return $data;
                }
        }
    }
}

function login($user_database) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $password_hash = $user_database->get_user_password($username);
    if ($password_hash == null || !password_verify($password, $password_hash)) {
        $_SESSION['login_error'] =  "Error: The username or password is invalid!";
        header("Location: {$_SERVER["HTTP_REFERER"]}");
    }
    else {
        $_SESSION['logged_in'] = true;
        $access_level = $user_database->get_access_level($username);
        $_SESSION['access_level'] = $access_level;
        header("Location: ../welcome.php");
    }
  }
  function create_account($user_database) {
    require 'dbh.php';
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 10]);
    $access_level = $_POST['level'];
    $user_database->user_exists($username);
    if ($rows != 0) {
        $_SESSION['mysql_error'] =  "Error: A user with that username is taken!";
        header("Location: {$_SERVER["HTTP_REFERER"]}");
    } 
    else {
        $stmt = $conn->prepare("INSERT INTO users (`username`, `password`, `level`) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $access_level);
        $stmt->execute();
        header("Location: {$_SERVER["HTTP_REFERER"]}");
        exit();
    }
  }

  function get_field_names($conn, $table_name) {
    $query = $conn->query('SHOW FULL COLUMNS FROM '. $table_name);
    while ($row = $query->fetch_assoc()) {
        if ($row['Extra'] == null) {
            $field_names[] = $row['Field'];
        }
    }
    return $field_names;
  }
  function construct_submitted_data($field_names) {
    $submitted_data = [];
    foreach ($field_names as $field_name) {
        if (str_contains($field_name, "date")) {
           $submitted_data[$field_name] = check_date($_POST[$field_name]);
        } else {
            $submitted_data[$field_name] = $_POST[$field_name];
        }
    }
    return $submitted_data;
  }
?>
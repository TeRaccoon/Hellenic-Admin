<?php
if (isset($_GET['query'])) {
    execute_query();
}
function execute_query() {
    $conn = mysqli_connect("localhost", "root", "password", "hellenic");
    
    $query = $_GET['query'];

    switch ($query) {
        case 'invoices':
            $results = $conn->query("SELECT * FROM invoices");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'invoices_today':
            $results = $conn->query("SELECT * FROM invoices WHERE DATE(created_at) = CURDATE()");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'invoices_month':
            $results = $conn->query("SELECT * FROM invoices WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'invoices_today_yesterday_difference':
            $results = $conn->query("SELECT (SELECT COUNT(*) FROM invoices WHERE DATE(created_at) = CURDATE()) - (SELECT COUNT(*) FROM invoices WHERE DATE(created_at) = CURDATE() - INTERVAL 1 DAY) AS difference");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'invoices_completed':
            $results = $conn->query("SELECT * FROM invoices WHERE `status` = 'complete'");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'invoices_completed_today':
            $results = $conn->query("SELECT * FROM invoices WHERE DATE(created_at) = CURDATE() AND `status` = 'complete'");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'invoices_completed_week':
            $results = $conn->query("SELECT * FROM invoices WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1) AND `status` = 'complete'");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'invoices_due_today':
            $results = $conn->query("SELECT * FROM invoices WHERE delivery_date = curdate()");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'invoices_due_week':
            $results = $conn->query("SELECT * FROM invoices WHERE delivery_date < curdate() + 7 AND delivery_date >= curdate()");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'invoices_pending':
            $results = $conn->query("SELECT * FROM `invoices` WHERE `status` = 'pending'");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'invoices_pending_today':
            $results = $conn->query("SELECT * FROM invoices WHERE DATE(created_at) = DATE(CURDATE()) AND `status` = 'pending'");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'invoices_pending_week':
            $results = $conn->query("SELECT * FROM `invoices` WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1) AND `status` = 'pending'");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'invoices_overdue':
            $results = $conn->query("SELECT * FROM `invoices` WHERE `status` = 'overdue'");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'invoices_overdue_week':
            $results = $conn->query("SELECT * FROM `invoices` WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1) AND `status` = 'overdue'");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'income_today':
            $results = $conn->query("SELECT SUM(total) AS total_sum FROM invoices WHERE created_at = curdate()");
            echo number_format(handle_data("ASSOC", $results, 'total_sum'), 2);
            break;
        case 'income_week':
            $results = $conn->query("SELECT SUM(total) AS total_sum FROM invoices WHERE created_at >= curdate() - INTERVAL 1 WEEK");
            echo number_format(handle_data("ASSOC", $results, 'total_sum'), 2);
            break;
        case 'income_month':
            $results = $conn->query("SELECT SUM(total) AS total_sum FROM invoices WHERE MONTH(created_at) = MONTH(curdate()) AND YEAR(created_at) = YEAR(curdate())");
            echo number_format(handle_data("ASSOC", $results, 'total_sum'), 2);
            break;
        case 'products_expiring_month':
            $results = $conn->query("SELECT * FROM stocked_items WHERE expiry_date >= curdate() AND expiry_date < curdate() + INTERVAL 1 MONTH");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'products_expiring_week':
            $results = $conn->query("SELECT * FROM stocked_items WHERE expiry_date >= curdate() AND expiry_date < curdate() + INTERVAL 1 WEEK");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'total_customers':
            $results = $conn->query("SELECT * FROM `customers`");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'total_retail_customers':
            $results = $conn->query("SELECT * FROM `customers` WHERE `customer_type` = 'Retail'");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'total_wholesale_customers':
            $results = $conn->query("SELECT * FROM `customers` WHERE `customer_type` = 'Wholesale'");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'total_new_customers':
            $results = $conn->query("SELECT * FROM customers WHERE MONTH(created_at) = MONTH(curdate()) AND YEAR(created_at) = YEAR(curdate())");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'total_customers_week':
            $results = $conn->query("SELECT * FROM `customers` WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'total_outstanding_customers':
            $results = $conn->query("SELECT * FROM `customers` WHERE `outstanding_balance` != 0");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'total_debted_customers':
            $results = $conn->query("SELECT * FROM `customers` WHERE `outstanding_balance` != 0 AND `last_payment_date` IS NULL");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'invoices_out_today':
            $results = $conn->query("SELECT * FROM `invoices` WHERE `delivery_date` = CURDATE() AND `delivery_type` = 'Collection'");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'invoices_out_week':
            $results = $conn->query("SELECT * FROM `invoices` WHERE YEARWEEK(delivery_date, 1) = YEARWEEK(CURDATE(), 1) AND `delivery_type` = 'Collection'");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'collections_today':
            $results = $conn->query("SELECT * FROM `invoices` WHERE `delivery_date` = CURDATE() AND `delivery_type` = 'Collection'");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'collections_week':
            $results = $conn->query("SELECT * FROM `invoices` WHERE YEARWEEK(delivery_date, 1) = YEARWEEK(CURDATE(), 1) AND `delivery_type` = 'Collection'");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'deliveries_today':
            $results = $conn->query("SELECT * FROM `invoices` WHERE `delivery_date` = CURDATE() AND `delivery_type` = 'Delivery'");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'deliveries_week':
            $results = $conn->query("SELECT * FROM `invoices` WHERE YEARWEEK(delivery_date, 1) = YEARWEEK(CURDATE(), 1) AND `delivery_type` = 'Delivery'");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'items_sold_today':
            $results = $conn->query("SELECT SUM(quantity) as total_sum FROM `invoiced_items` WHERE `created_at` = curdate()");
            echo handle_data("ASSOC", $results, 'total_sum');
            break;
        case 'items_sold_week':
            $results = $conn->query("SELECT SUM(quantity) as total_sum FROM `invoiced_items` WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)");
            echo handle_data("ASSOC", $results, 'total_sum');
            break;
        case 'invoiced_items':
            $results = $conn->query("SELECT `quantity` FROM `invoiced_items`");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'invoiced_items_week':
            $results = $conn->query("SELECT `quantity` FROM `invoiced_items` WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)");
            echo handle_data("ROW_COUNT", $results, null);
            break;
        case 'top_item':
            $results = $conn->query("SELECT item.item_name, SUM(i_items.quantity) AS total_quantity from invoiced_items i_items JOIN items item ON i_items.item_id = item.id GROUP BY i_items.item_id, item.item_name ORDER BY total_quantity DESC");
            echo(handle_data("ASSOC", $results, 'item_name'));
            break;
        case 'top_item_week':
            $results = $conn->query("SELECT item.item_name, SUM(i_items.quantity) AS total_quantity from invoiced_items i_items JOIN items item ON i_items.item_id = item.id WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1) GROUP BY i_items.item_id, item.item_name ORDER BY total_quantity DESC");
            echo handle_data("ASSOC", $results, 'item_name');
            break;
        case 'bottom_item':
            $results = $conn->query("SELECT item.item_name, SUM(i_items.quantity) AS total_quantity from invoiced_items i_items JOIN items item ON i_items.item_id = item.id GROUP BY i_items.item_id, item.item_name ORDER BY total_quantity ASC");
            echo handle_data("ASSOC", $results, 'item_name');
            break;
        case 'bottom_item_week':
            $results = $conn->query("SELECT item.item_name, SUM(i_items.quantity) AS total_quantity from invoiced_items i_items JOIN items item ON i_items.item_id = item.id WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1) GROUP BY i_items.item_id, item.item_name ORDER BY total_quantity ASC");
            echo handle_data("ASSOC", $results, 'item_name');
            break;
        case 'month_in':
            $results = $conn->query("SELECT SUM(total) AS total_income FROM invoices");
            echo handle_data("ASSOC", $results, 'total_income');
            break;
        case 'month_out':
            $results = $conn->query("SELECT SUM(invoiced_items.quantity * items.unit_cost) AS total_cost FROM invoiced_items JOIN items ON invoiced_items.item_id = items.id");
            echo handle_data("ASSOC", $results, 'total_cost');
            break;
        case 'invoice_profit_month':
            $results = $conn->query("SELECT (SELECT SUM(total) AS total_income FROM invoices) - (SELECT SUM(invoiced_items.quantity * items.unit_cost) AS total_cost FROM invoiced_items JOIN items ON invoiced_items.item_id = items.id) AS invoice_profit");
            echo number_format(handle_data("ASSOC", $results, 'invoice_profit'), 2);
            break;
        case 'total_owed':
            $results = $conn->query("SELECT SUM(outstanding_balance) AS total_owed FROM customers");
            echo handle_data("ASSOC", $results, 'total_owed');
            break;
        case 'money_received':
            $results = $conn->query("SELECT SUM(amount) AS amount_received FROM customer_payments");
            echo handle_data("ASSOC", $results, 'amount_received');
            break;
        case 'account_balances':
            $startDate = $_GET['startDate'];
            $endDate = $_GET['endDate'];
            $results = $conn->query("SELECT account_code, SUM(debit) AS total_debit, SUM(credit) AS total_credit, SUM(debit) - SUM(credit) AS balance FROM general_ledger WHERE date >= '".$startDate."' AND date <= '".$endDate."' GROUP BY account_code");
            echo handle_data("ASSOC", $results, null);
            break;
        case 'sales_revenue':
            $startDate = $_GET['startDate'];
            $endDate = $_GET['endDate'];
            $results = $conn->query("SELECT SUM(ii.quantity * it.list_price) * (1 - c.discount / 100) AS total_profit FROM invoices AS i INNER JOIN customers AS c ON i.customer_id = c.id INNER JOIN invoiced_items AS ii ON i.id = ii.invoice_id INNER JOIN items AS it ON ii.item_id = it.id WHERE ii.created_at >= '".$startDate."' AND ii.created_at <= '".$endDate."' GROUP BY c.discount");
            echo handle_data("ASSOC", $results, null);
            break;
        case 'sales_cost':
            $startDate = $_GET['startDate'];
            $endDate = $_GET['endDate'];
            $results = $conn->query("SELECT SUM(ii.quantity * it.unit_cost) AS total_cost FROM invoices AS i INNER JOIN invoiced_items AS ii ON i.id = ii.invoice_id INNER JOIN items AS it ON ii.item_id = it.id WHERE ii.created_at >= '".$startDate."' AND ii.created_at <= '".$endDate."'");
            echo handle_data("ASSOC", $results, null);
            break;
        case 'expenses':
            $startDate = $_GET['startDate'];
            $endDate = $_GET['endDate'];
            $results = $conn->query("SELECT SUM(amount) AS total_expenses FROM payments WHERE (category = 'Expense' OR category = 'Salary') AND date >= '".$startDate."' AND date <= '".$endDate."'");
            echo handle_data("ASSOC", $results, 'total_expenses');
            break;
        case '0-30-debtor':
            $results = $conn->query("SELECT c.id, c.forename, c.surname, SUM(i.total) FROM invoices AS i INNER JOIN customers as c ON i.customer_id WHERE i.created_at <= curdate() + 30 AND i.customer_id = c.id AND i.payment_status = 'No' AND i.due_date < curdate() GROUP BY c.id");
            echo handle_data("ALL", $results, null);
            break;
        case '31-60-debtor':
            $results = $conn->query("SELECT c.id, c.forename, c.surname, SUM(i.total) FROM invoices AS i INNER JOIN customers as c ON i.customer_id WHERE i.created_at < curdate() + 60 AND i.created_at > curdate() + 31 AND i.customer_id = c.id AND i.payment_status = 'No' AND i.due_date < curdate() GROUP BY c.id");
            echo handle_data("ALL", $results, null);
            break;
        case '61-90-debtor':
            $results = $conn->query("SELECT c.id, c.forename, c.surname, SUM(i.total) FROM invoices AS i INNER JOIN customers as c ON i.customer_id WHERE i.created_at < curdate() + 90 AND i.created_at > curdate() + 61 AND i.customer_id = c.id AND i.payment_status = 'No' AND i.due_date < curdate() GROUP BY c.id");
            echo handle_data("ALL", $results, null);
            break;
        case '90-x-debtor':
            $results = $conn->query("SELECT c.id, c.forename, c.surname, SUM(i.total) FROM invoices AS i INNER JOIN customers as c ON i.customer_id WHERE i.created_at > curdate() + 90 AND i.customer_id = c.id AND i.payment_status = 'No' AND i.due_date < curdate() GROUP BY c.id");
            echo handle_data("ALL", $results, null);
            break;
        case '0-30-creditor':
            $results = $conn->query("SELECT s.id, s.forename, s.surname, SUM(total) FROM supplier_invoices AS si INNER JOIN suppliers AS s ON supplier_id WHERE created_at <= curdate() + 30 AND payment_status = 'No' GROUP BY s.id");
            echo handle_data("ALL", $results, null);
            break;
        case '31-60-creditor':
            $results = $conn->query("SELECT s.id, s.forename, s.surname, SUM(total) FROM supplier_invoices AS si INNER JOIN suppliers AS s ON supplier_id WHERE created_at < curdate() + 60 AND created_at > curdate() AND payment_status = 'No' GROUP BY s.id");
            echo handle_data("ALL", $results, null);
            break;
        case '61-90-creditor':
            $results = $conn->query("SELECT s.id, s.forename, s.surname, SUM(total) FROM supplier_invoices AS si INNER JOIN suppliers AS s ON supplier_id WHERE created_at < curdate() + 90 AND created_at > curdate() + 61 AND payment_status = 'No' GROUP BY s.id");
            echo handle_data("ALL", $results, null);
            break;
        case '90-x-creditor':
            $results = $conn->query("SELECT s.id, s.forename, s.surname, SUM(total) FROM supplier_invoices AS si INNER JOIN suppliers AS s ON supplier_id WHERE created_at > curdate() + 90 AND payment_status = 'No' GROUP BY s.id");
            echo handle_data("ALL", $results, null);
            break;
        case 'blank':
            echo " ";
            break;
        default:
            echo "No match!";
            break;
    }
}
function handle_data($data_type, $results, $offset) {
    if ($data_type == "ASSOC") {
        if ($offset != null && $results->num_rows > 0) {
            $data = $results -> fetch_array(MYSQLI_ASSOC)[$offset];
            if ($data == null) {
                return "0";
            }
            return $data;
        }
        return json_encode($results -> fetch_all(MYSQLI_ASSOC));
    }
    elseif ($data_type == "ALL") {
        return json_encode($results -> fetch_all());
    }
    elseif ($data_type == "NUM") {
        return $results -> fetch_array(MYSQLI_NUM);
    }
    elseif ($data_type == "ROW_COUNT") {
        return $results -> num_rows;
    }
}
?>
<?php
if (isset($_POST["action"])) {
  require_once 'dbh.php';

  $database = new DatabaseConnection();
  $database->connect();

  $action = $_POST["action"];
  switch ($action) {
      case "get_invoice_months":
          get_invoice_months($database, true);
          break;
      case "get_item_name_totals":
          get_items_totals_months($database, true);
          break;
      case "get_top_items":
          get_top_items($database, true);
          break;
      case "get_profits":
          get_month_profit($database, true);
          break;
  }  
}


function get_row_count($conn, $query_string) {
  $query = $conn->query($query_string);
  return $query->num_rows;
}
function get_table_info($conn, $table_name) {
  $table_names = getTableNames($conn);
  if (in_array($table_name, $table_names)) {
    $query = $conn->query('SHOW FULL COLUMNS FROM '. $table_name);
    $formatted_names = [];
    $field_names = [];
    $editable_formatted_names = [];
    $editable_field_names = [];
    $required_fields = [];
    while($row = $query->fetch_assoc()) {
        $formatted_names[] = $row['Comment'];
        $field_names[] = $row['Field'];
        if ($row['Extra'] == null)
        {
          $editable_formatted_names[] = $row['Comment'];
          $editable_field_names[] = $row['Field'];
          if ($row['Null'] == "NO") {
            $required_fields[] = $row['Field'];
          }
        }
    }
    return [
        0 => $formatted_names,
        1 => $field_names,
        2 => $editable_formatted_names,
        3 => $editable_field_names,
        4 => $required_fields,
    ];
  }
  return null;
}
function getTableNames($conn) {
  $query = $conn->query("SHOW TABLES");
  $data = $query->fetch_all(MYSQLI_ASSOC);
  $table_names = [];
  foreach ($data as $table) {
    $table_names[] = $table['Tables_in_hellenic'];
  }
  return $table_names;
}
function get_table_contents($conn, $table_name, $filter) {
  $table_names = (getTableNames($conn));
  if (in_array($table_name, $table_names)) {
    if ($filter == "") {
      $query = $conn->query("SELECT * FROM ". $table_name);
      return $query->fetch_all(MYSQLI_ASSOC);
    } else {
      $query_string = "SELECT * FROM ".$table_name." ".$filter.";";
      $query = $conn->query($query_string);
      return $query->fetch_all(MYSQLI_ASSOC);
    }
  }
  return null;
}
function get_row_contents($conn, $query_string) {
  $query = $conn->query($query_string);
  $contents = $query->fetch_all();
  return $contents;
}
function get_invoice_info($conn, $invoice_id) {
  $query_string = "SELECT
  invoices.title,
  invoices.due_date,
  invoices.net_value,
  invoices.total,
  invoices.vat,
  invoices.delivery_date,
  invoices.created_at,
  customers.forename,
  customers.surname,
  customers.outstanding_balance,
  customer_address.invoice_address_one,
  customer_address.invoice_address_two,
  customer_address.invoice_address_three,
  customer_address.invoice_postcode,
  customer_address.delivery_address_one,
  customer_address.delivery_address_two,
  customer_address.delivery_address_three,
  customer_address.delivery_postcode
FROM
  invoices
  INNER JOIN customers ON invoices.customer_id = customers.id
  INNER JOIN customer_address ON customer_address.customer_id = invoices.customer_id
WHERE
  invoices.id = ". $invoice_id;
  $query = $conn->query($query_string);
  $contents = $query->fetch_all();
  return $contents;
}
function get_invoice_products($conn, $invoice_id) {
  $query_string = "SELECT
  items.item_name,
  items.list_price,
  invoiced_items.quantity,
  invoiced_items.vat_charge
  FROM
  invoices
  INNER JOIN invoiced_items ON invoices.id = invoiced_items.invoice_id
  INNER JOIN items ON invoiced_items.item_id = items.id
  WHERE
  invoices.id = ". $invoice_id;
  $query = $conn->query($query_string);
  $contents = $query->fetch_all();
  return $contents;
}

function get_customer_names($conn) {
  $customer_identifiers = array();
  $query = $conn->query('SELECT id, forename, surname, email FROM customers');
  while($row = $query->fetch_assoc()) {
      $customer_identifiers[] = $row['id'];
      $customer_identifiers[] = $row['forename'];
      $customer_identifiers[] = $row['surname'];
      $customer_identifiers[] = $row['email'];
  }
  return $customer_identifiers;
}

function get_non_extra_types($conn, $table_name) {
  $query = $conn->query("SHOW FIELDS FROM ".$table_name);
  $contents = $query->fetch_all();
  foreach($contents as $item) {
    if ($item[5] == null) {
      $types[] = $item[1];
    }
  }
  return $types;
}

function get_raw_types_form($conn, $table_name) {
  $query = $conn->query("SHOW FULL COLUMNS FROM ". $table_name);
  while ($row = $query->fetch_assoc()) {
    if ($row["Extra"] == null) {
      $types[] = $row["Type"];
    }
  }
  return $types;
}
function get_raw_types_table($conn, $table_name) {
  $query = $conn->query("SHOW FIELDS FROM ".$table_name);
  $contents = $query->fetch_all();
  foreach($contents as $item) {
      $types[] = $item[1];
  }
  return $types;
}
function get_tables($conn) {
  $query = $conn->query("SHOW TABLES");
  $contents = $query->fetch_all();
  return $contents;
}
function pull_assoc($conn, $table_name) {
  $output = null;
  $query = $conn->query("SELECT TABLE_NAME, REFERENCED_TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME IS NOT NULL AND TABLE_SCHEMA = 'hellenic' AND TABLE_NAME = '".$table_name."'");
  while ($contents = $query->fetch_assoc()) {
    $assoc_table = $contents['REFERENCED_TABLE_NAME'];
    $assoc_column = $contents['COLUMN_NAME'];
    if ($assoc_table == "customers") {
      $alt_query = $conn->query("SELECT `id`, CONCAT(`forename`, ' ', `surname`) AS full_name FROM `customers`");
    } elseif ($assoc_table == "items") {
      $alt_query = $conn->query("SELECT `id`, `item_name` FROM `items`");
    } elseif ($assoc_table == "invoices") {
      $alt_query = $conn->query("SELECT `id`, `title` FROM `invoices`");
    } elseif ($assoc_table == "suppliers") {
      $alt_query = $conn->query("SELECT `id`, CONCAT(`forename`, ' ', `surname`) AS full_name FROM `suppliers`");
    } elseif ($assoc_table == "retail_items") {
      $alt_query = $conn->query("SELECT ri.id, i.item_name FROM `items` AS i INNER JOIN `retail_items` AS ri ON ri.item_id = i.id");
    } elseif ($assoc_table == "offers") {
      $alt_query = $conn->query("SELECT `id`, `name` FROM `offers`");
    } elseif ($assoc_table == "page_sections") {
      $alt_query = $conn->query("SELECT `id`, `name` FROM `page_sections`");
    }
    $results = $alt_query->fetch_all();
    foreach ($results as $item) {
      $output[$assoc_column][$item[0]] = $item[1];
    }
  }
  return $output;
}

function format_month_total($conn) {
  $year_totals = get_row_contents($conn, "SELECT YEAR(created_at) AS year, MONTH(created_at) AS month, SUM(total) AS total_amount FROM invoices GROUP BY YEAR(created_at), MONTH(created_at)");
  $formatted_totals = array("", "", "", "", "", "", "", "", "", "", "", "");
  for ($i = 0; $i < count($year_totals); $i++) {
    $formatted_totals[($year_totals[$i][1]) - 1] = $year_totals[$i];
  }
  for ($i = 1; $i < 13; $i++) {
    if ($formatted_totals[$i-1] == null) {
      $formatted_totals[$i-1] = array($year_totals[0][0], $i-1, 0);
    }
  }
  return $formatted_totals;
}
function get_high_total_month($month_total) {
  $highest = 1;
  foreach ($month_total as $value) {
    if ($value[2] > $highest) {
      $highest = $value[2];
    }
  }
  return $highest;
}

function get_item_totals($conn) {
  $results = get_row_contents($conn, "SELECT items.item_name, SUM(invoiced_items.quantity) AS total_quantity FROM items JOIN invoiced_items ON items.id = invoiced_items.item_id JOIN invoices ON invoices.id = invoiced_items.invoice_id GROUP BY items.id, items.item_name ORDER BY total_quantity DESC");
  return $results;
}

function get_top_items($conn, $echo) {
  $results = get_row_contents($conn, "SELECT items.item_name, COUNT(*) AS total_count, SUM(invoiced_items.quantity) AS total_quantity, items.unit_cost, items.list_price FROM invoiced_items JOIN items ON items.id = invoiced_items.item_id GROUP BY items.item_name, items.unit_cost, items.list_price ORDER BY total_quantity DESC;");
  $top_items = force_length_jagged($results, 5);
  if ($echo) {
    echo json_encode($top_items);
  }
  return $top_items;
}

function get_invoices_for_items($conn) {
  $results = get_row_contents($conn, "SELECT items.item_name, COUNT(*) AS total_count, SUM(invoiced_items.quantity) AS total_quantity, items.unit_cost, items.list_price FROM invoiced_items JOIN items ON items.id = invoiced_items.item_id GROUP BY items.item_name, items.unit_cost, items.list_price ORDER BY total_quantity DESC;");
  return $results;
}

function get_items_totals_months($conn, $echo) {
  $results = array();
  for ($i = 1; $i < 13; $i++) {
    $results[$i-1] = get_row_contents($conn, "SELECT items.item_name, SUM(invoiced_items.quantity) AS total_quantity FROM items JOIN invoiced_items ON items.id = invoiced_items.item_id JOIN invoices ON invoices.id = invoiced_items.invoice_id WHERE MONTH(invoices.created_at) = '".$i."' AND YEAR(invoices.created_at) = YEAR(CURDATE()) GROUP BY items.id, items.item_name");
  }
  if ($echo) {
    echo json_encode($results);
  }
  else {
    return $results;
  }
}

function get_invoice_months($conn, $echo) {
  $results = array();
  for ($i = 1; $i < 13; $i++) {
    $results[$i-1] = get_row_count($conn, "SELECT * FROM invoices WHERE MONTH(created_at) = '".$i."'");
  }
  if ($echo) {
    echo json_encode($results);
  } else {
    return $results;
  }
}

function get_month_profit($conn, $echo) {
  $results = array();
  $profit = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
  for ($i = 0; $i < 12; $i++) {
    $results[] = get_row_contents($conn, "SELECT 
    c.discount,
    SUM((ii.quantity * (it.list_price - it.unit_cost))) AS profit
    FROM
        invoices AS i
        INNER JOIN customers AS c ON i.customer_id = c.id
        INNER JOIN invoiced_items AS ii ON i.id = ii.invoice_id
        INNER JOIN items AS it ON ii.item_id = it.id
    WHERE
        MONTH(i.created_at) = '".($i + 1)."'
        AND YEAR(i.created_at) = YEAR(curdate())
    GROUP BY
        c.discount");
      if ($results[$i] != null) {
        for ($k = 0; $k < count($results[$i]); $k++) {
          $profit[$i] += $results[$i][$k][1] / 100 * (100 - $results[$i][$k][0]);
        }
      }
  }
  if ($echo) {
    echo json_encode($profit);
  }
  return $profit;
}
function force_length_jagged($data, $length) {
  if (count($data) < 5) {
      $data = array_slice($data, 0, 5);
  } elseif (count($data) < $length) {
      for ($i = count($data) - 1; $i < 5; $i++) {
          $data[] = array(0, 0);
      }
  }
  return $data;
}
function getTable($tab) {
  session_start();
  switch ($tab) {
      case 'sales_invoices':
          $_SESSION['filter'] = "WHERE `type` = 'Sales'";
          break;
      case 'packing_invoices':
          $_SESSION['filter'] = "WHERE `type` = 'Packing'";
          break;
      case 'retail_customer':
          $_SESSION['filter'] = "WHERE `customer_type` = 'Retail'";
          break;
      case 'wholesale_customer':
          $_SESSION['filter'] = "WHERE `customer_type` = 'Wholesale'";
          break;
      case 'invoice_delivery_today':
          $_SESSION['filter'] = "WHERE delivery_date = curdate()";
          break;
      case 'expiring_month':
          $_SESSION['filter'] = "WHERE `expiry_date` >= curdate() AND `expiry_date` < curdate() + INTERVAL 1 MONTH";
          break;
      case 'expired':
          $_SESSION['filter'] = "WHERE `expiry_date` < curdate()";
          break;
      case 'debted_customers':
          $_SESSION['filter'] = "WHERE `outstanding_balance` != 0";
          break;
      case 'all':
          // Set the filter for all
          $_SESSION['filter'] = "";
          break;
  }
  ob_start();
  $_SESSION['table'] = $_GET['table'];
  include '../templates/table.php';
  return ob_get_clean();
}

function generateForm($table_name, $filter, $formType) {
  $server_name = "localhost";
  $dB_username = "root";
  $dB_password = "password";
  $dB_name = "hellenic";
  $conn = mysqli_connect($server_name, $dB_username, $dB_password, $dB_name);



  $customer_names = get_row_contents($conn, "SELECT CONCAT(forename, ' ', surname) AS full_name FROM `customers`");
  $customer_ids = get_row_contents($conn, "SELECT id FROM `customers`");
  $supplier_names = get_row_contents($conn, "SELECT CONCAT(forename, ' ', surname) AS full_name FROM `suppliers`");
  $supplier_ids = get_row_contents($conn, "SELECT id FROM `suppliers`");

  $customer_identifiers = get_customer_names($conn);
  $invoice_titles = get_row_contents($conn, "SELECT `title` FROM `invoices`");
  $invoice_ids = get_row_contents($conn, "SELECT `id` FROM `invoices` ORDER BY CAST(`id` AS UNSIGNED) ASC");
  $item_names = get_row_contents($conn, "SELECT `item_name` FROM `items`");
  $retail_item_names = get_row_contents($conn, "SELECT i.item_name FROM `items` AS i INNER JOIN `retail_items` AS ri ON ri.item_id = i.id");
  $item_ids = get_row_contents($conn, "SELECT `id` FROM `items`");
  $retail_item_ids = get_row_contents($conn, "SELECT `id` FROM `retail_items`");
  $offer_ids = get_row_contents($conn, "SELECT `id` FROM `offers`");
  $offer_names = get_row_contents($conn, "SELECT `name` FROM `offers`");
  $warehouse_names = get_row_contents($conn, "SELECT `name` FROM `warehouse`");
  $warehouse_ids = get_row_contents($conn, "SELECT `id` FROM `warehouse`");
  $page_section_ids = get_row_contents($conn, "SELECT `id` FROM `page_sections`");
  $page_section_names = get_row_contents($conn, "SELECT `name` FROM `page_sections`");
  
  if ($formType == "add") {
    $next_ID = get_row_contents($conn, "SELECT auto_increment from information_schema.tables WHERE table_name = 'invoices' AND table_schema = DATABASE()")[0][0];
    ob_start();
    include '../templates/add_form.php';
    return ob_get_clean();
  }
  else {
    ob_start();
    include '../templates/edit_form.php';
    return ob_get_clean();
  }
}

if (isset($_GET['tab'])) {
  echo getTable($_GET['tab'], $_GET['table']);
}
if (isset($_GET['generateEditForm'])) {
  echo generateForm($_GET['table'], $_GET['filter'], "edit");
}
if (isset($_GET['generateAddForm'])) {
  echo generateForm($_GET['table'], $_GET['filter'], "add");
}
if (isset($_GET['checkError'])) {
  get_error_info();
}
if (isset($_GET['retrieveSubmittedData'])) {
  echo get_submitted_data();
}
?>
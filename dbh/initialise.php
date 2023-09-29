<?php

function initialise($access_required) {
  $access_levels = ["full", "high", "low"];
  if (isset($_SESSION['logged_in'])) {
    if (array_search($access_required, $access_levels) < array_search($_SESSION['access_level'], $access_levels)) {
      header("Location: access-denied.html");
    }
    $_SESSION['access_required'] = $access_required;
    require 'dbh/dbh.php';
    require 'dbh/data_handler.php'; 

    $database = new DatabaseConnection();
    $database->connect();

    return $database;
  } else {
    header("Location: login.php");
  }
  return null;
}
function loadPageData($conn, $table_name, $filter) {
  $rows = get_table_contents($conn, $table_name, $filter);
  $raw_types = get_non_extra_types($conn, $table_name);
  return array($rows, $raw_types);
}
?>
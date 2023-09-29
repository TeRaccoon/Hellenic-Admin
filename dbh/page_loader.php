<?php
if (isset($_GET['table'])) {
    loadPage();
}
if (isset($_GET['getData'])) {
    getSessionData();
}
if (isset($_GET['setTable'])) {
    setTable();
}

function loadPage() {
    session_start();
    $_SESSION['table'] = $_GET['table'];
    $_SESSION['filter'] = "";
    if (isset($_GET['altTable'])) {
        $_SESSION['altTable'] = $_GET['altTable'];
    }
    $access_levels = ["full", "high", "low"];
    if (isset($_SESSION['logged_in'])) {
        if (array_search($_GET['accessLevel'], $access_levels) < array_search($_SESSION['access_level'], $access_levels)) {
            header("Location: access-denied.html");
        }
        $_SESSION['access_required'] = $_GET['accessLevel'];
    //   require 'dbh/dbh.php';
    //   require 'dbh/data_handler.php';
    } else {
      header("Location: login.php");
    }
}
function getSessionData() {
    session_start();
    $table_name = $_SESSION['table'];
    $filter = $_SESSION['filter'];
    $altTable = "";
    if (isset($_SESSION['altTable'])) {
        $altTable = $_SESSION['altTable'];
    }
    $data = array($table_name, $filter, $altTable);
    echo json_encode($data);
}

function setTable() {
    session_start();
    $_SESSION['table'] = $_GET['table'];
}
function setAltTable() {
    session_start();
}
?>
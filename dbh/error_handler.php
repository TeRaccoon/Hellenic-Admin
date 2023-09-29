<?php
if (isset($_GET['checkError'])) {
    get_error_data();
    clear_error_session();
}
if (isset($_GET['checkLogin'])) {
    checkLogin();
}
function checkLogin() {
  session_start();
  $loginError = null;
  if (isset($_SESSION['login_error'])) {
    $loginError = $_SESSION['login_error'];
  }
  echo $loginError;
}
function get_error_data() {
    session_start();
    $sql_error = null;
    if (isset($_SESSION['error'])) {
        $sql_error = $_SESSION['error'];
    }
    $row_id = -1;
    if (isset($_SESSION['row_id'])) {
      $row_id = $_SESSION['row_id'];
    }
    $type = "";
    if (isset($_SESSION['error_type'])) {
      $type = $_SESSION['error_type'];
    }
    $submitted_data = null;
    if (isset($_SESSION['submitted_data'])) {
       $submitted_data = $_SESSION['submitted_data'];
    }
    $error_code = null;
    if (isset($_SESSION['error_code'])) {
      $error_code = $_SESSION['error_code'];
    }
    $description = null;
    if (isset($_SESSION['error_description'])) {
      $description = $_SESSION['error_description'];
    }
    $array = [[$sql_error, $row_id, $type], $submitted_data, $error_code, $description];
    echo json_encode($array);
}

function clear_error_session() {
    unset($_SESSION['error_type']);
    unset($_SESSION['error']);
    unset($_SESSION['row_id']);
    unset($_SESSION['error_type']);
    unset($_SESSION['submitted_data']);
    unset($_SESSION['error_code']);
    unset($_SESSION['error_description']);
  }
?>
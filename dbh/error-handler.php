<?php
class ErrorHandler {
    public static function set_error($message, $type, $code, $description) {
        $_SESSION['error'] = $message;
        $_SESSION['error_type'] = $type;
        $_SESSION['error_code'] = $code;
        $_SESSION['error_description'] = $description;
    }

    public static function clear_error() {
        unset($_SESSION['error']);
        unset($_SESSION['error_type']);
        unset($_SESSION['error_code']);
        unset($_SESSION['error_description']);
    }
}

?>
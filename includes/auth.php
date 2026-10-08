<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_id'])) {
    if (file_exists("login.php")) {
        header("Location: login.php");
    } else {
        header("Location: ../login.php");
    }
    exit();
}

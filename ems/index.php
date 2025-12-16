<?php
session_start();

/* If user already logged in */
if (isset($_SESSION['user_id'], $_SESSION['role'])) {

    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    }

    if ($_SESSION['role'] === 'employee') {
        header("Location: employee/dashboard.php");
        exit;
    }
}

/* If NOT logged in */
header("Location: auth/login.php");
exit;

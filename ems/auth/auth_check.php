<?php
// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* CHECK LOGIN */
if (!isset($_SESSION['user_id'], $_SESSION['role'])) {
    header("Location: ../index.php");
    exit;
}

/* ROLE CHECK FUNCTION (prevent redeclare) */
if (!function_exists('requireRole')) {
    function requireRole($role) {
        if ($_SESSION['role'] !== $role) {
            echo "<h3 style='color:red;text-align:center;margin-top:50px;'>
                  Access Denied
                  </h3>";
            exit;
        }
    }
}

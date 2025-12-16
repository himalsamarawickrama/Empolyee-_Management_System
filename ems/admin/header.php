<?php
require_once("../auth/auth_check.php");
requireRole('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>EMS Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
  background: #f4f6f9;
}
.sidebar {
  min-height: 100vh;
}
.nav-link {
  color: #fff;
}
.nav-link:hover {
  background: rgba(255,255,255,0.15);
}
</style>
</head>

<body>

<div class="container-fluid">
  <div class="row">

    <!-- SIDEBAR -->
    <?php include "sidebar.php"; ?>

    <!-- MAIN CONTENT -->
    <div class="col-md-9 col-lg-10 p-4">

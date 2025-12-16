<?php
require_once("../auth/auth_check.php");
requireRole('admin');
require_once("../config/db.php");

// Dashboard data
$totalEmployees = $conn->query(
    "SELECT COUNT(*) total FROM employees"
)->fetch_assoc()['total'];

$totalDepartments = $conn->query(
    "SELECT COUNT(*) total FROM departments"
)->fetch_assoc()['total'];

$todayAttendance = $conn->query(
    "SELECT COUNT(*) total 
     FROM attendance 
     WHERE date = CURDATE() AND status='Present'"
)->fetch_assoc()['total'];

$totalSalary = $conn->query(
    "SELECT SUM(salary) total FROM employees"
)->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
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

      <h2 class="mb-4">Admin Dashboard</h2>

      <div class="row g-3">

        <div class="col-md-3">
          <div class="card text-bg-primary shadow">
            <div class="card-body text-center">
              <h5>Total Employees</h5>
              <h2><?= $totalEmployees ?></h2>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card text-bg-success shadow">
            <div class="card-body text-center">
              <h5>Departments</h5>
              <h2><?= $totalDepartments ?></h2>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card text-bg-warning shadow">
            <div class="card-body text-center">
              <h5>Present Today</h5>
              <h2><?= $todayAttendance ?></h2>
            </div>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card text-bg-dark shadow">
            <div class="card-body text-center">
              <h5>Total Salary</h5>
              <h2>₹<?= number_format($totalSalary) ?></h2>
            </div>
          </div>
        </div>

      </div>

    </div>
  </div>
</div>

</body>
</html>

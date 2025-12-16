<?php
include "../config/db.php";
include "../auth/auth_check.php";

if ($_SESSION['role'] !== 'employee') {
    die("Access Denied");
}

$user_id = $_SESSION['user_id'];

/* FETCH EMPLOYEE INFO */
$emp = $conn->query("
  SELECT e.id, u.name, d.name AS department, e.position
  FROM employees e
  JOIN users u ON e.user_id = u.id
  JOIN departments d ON e.department_id = d.id
  WHERE u.id = $user_id
")->fetch_assoc();

/* ATTENDANCE COUNT */
$attendance = $conn->query("
  SELECT 
    SUM(status='Present') present,
    SUM(status='Absent') absent,
    SUM(status='Leave') leave_count
  FROM attendance
  WHERE employee_id = {$emp['id']}
")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<title>Employee Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
<h2>Welcome, <?= htmlspecialchars($emp['name']) ?></h2>

<div class="row mt-4">

<div class="col-md-4">
<div class="card shadow">
<div class="card-body">
<h5>Department</h5>
<p><?= $emp['department'] ?></p>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card shadow">
<div class="card-body">
<h5>Position</h5>
<p><?= $emp['position'] ?></p>
</div>
</div>
</div>

<div class="col-md-4">
<div class="card shadow">
<div class="card-body">
<h5>Attendance</h5>
<p>Present: <?= $attendance['present'] ?></p>
<p>Absent: <?= $attendance['absent'] ?></p>
<p>Leave: <?= $attendance['leave_count'] ?></p>
</div>
</div>
</div>

</div>

<a href="attendance.php" class="btn btn-primary mt-3">My Attendance</a>
<a href="salary.php" class="btn btn-success mt-3">My Salary</a>
<a href="profile.php" class="btn btn-secondary mt-3">My Profile</a>
<a href="../auth/logout.php" class="btn btn-danger mt-3">Logout</a>

</div>
</body>
</html>

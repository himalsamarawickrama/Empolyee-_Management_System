<?php
include "header.php";
include "../config/db.php";
include "../auth/auth_check.php";

if ($_SESSION['role'] !== 'admin') {
  die("Access Denied");
}

/* DEPARTMENT REPORT */
$deptReport = $conn->query("
  SELECT d.name AS department, COUNT(e.id) AS total
  FROM departments d
  LEFT JOIN employees e ON d.id = e.department_id
  GROUP BY d.id
");

/* ATTENDANCE REPORT */
$attendanceReport = $conn->query("
  SELECT 
    u.name,
    SUM(a.status='Present') AS present,
    SUM(a.status='Absent') AS absent,
    SUM(a.status='Leave') AS leave_count
  FROM attendance a
  JOIN employees e ON a.employee_id = e.id
  JOIN users u ON e.user_id = u.id
  GROUP BY u.name
");

/* SALARY HISTORY REPORT */
$salaryReport = $conn->query("
  SELECT 
    u.name,
    h.old_salary,
    h.new_salary,
    h.increment_percent,
    h.increment_date
  FROM salary_history h
  JOIN employees e ON h.employee_id = e.id
  JOIN users u ON e.user_id = u.id
  ORDER BY h.increment_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Reports & Analytics</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

<h2 class="mb-4">Reports & Analytics</h2>

<!-- DEPARTMENT REPORT -->
<h4>Department-wise Employees</h4>
<table class="table table-bordered table-hover">
<thead class="table-dark">
<tr>
  <th>Department</th>
  <th>Total Employees</th>
</tr>
</thead>
<tbody>
<?php while ($d = $deptReport->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($d['department']) ?></td>
  <td><?= $d['total'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<!-- ATTENDANCE REPORT -->
<h4 class="mt-5">Attendance Summary</h4>
<table class="table table-bordered table-hover">
<thead class="table-dark">
<tr>
  <th>Employee</th>
  <th>Present</th>
  <th>Absent</th>
  <th>Leave</th>
</tr>
</thead>
<tbody>
<?php while ($a = $attendanceReport->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($a['name']) ?></td>
  <td><?= $a['present'] ?></td>
  <td><?= $a['absent'] ?></td>
  <td><?= $a['leave_count'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<!-- SALARY HISTORY -->
<h4 class="mt-5">Salary Increment History</h4>
<table class="table table-bordered table-hover">
<thead class="table-dark">
<tr>
  <th>Employee</th>
  <th>Old Salary</th>
  <th>New Salary</th>
  <th>Increment %</th>
  <th>Date</th>
</tr>
</thead>
<tbody>
<?php while ($s = $salaryReport->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($s['name']) ?></td>
  <td>₹<?= number_format($s['old_salary']) ?></td>
  <td>₹<?= number_format($s['new_salary']) ?></td>
  <td><?= $s['increment_percent'] ?>%</td>
  <td><?= date("d M Y", strtotime($s['increment_date'])) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>

</div>

</body>
</html>

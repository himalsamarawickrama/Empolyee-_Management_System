<?php
include "header.php";
include "../config/db.php";
include "../auth/auth_check.php";

if ($_SESSION['role'] !== 'admin') {
    die("Access Denied");
}

$msg = "";

/* SUCCESS MESSAGE */
if (isset($_GET['success'])) {
    $msg = "Salary increment applied successfully";
}

/* FETCH EMPLOYEES */
$data = $conn->query("
    SELECT 
      e.id,
      u.name,
      e.salary,
      e.increment_rate,
      e.last_increment
    FROM employees e
    JOIN users u ON e.user_id = u.id
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Salary Increment</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

<h2>Salary Increment Management</h2>

<?php if ($msg): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<table class="table table-bordered table-hover">
<thead class="table-dark">
<tr>
  <th>Employee</th>
  <th>Current Salary</th>
  <th>Increment %</th>
  <th>Last Increment</th>
  <th>Action</th>
</tr>
</thead>
<tbody>

<?php while ($row = $data->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['name']) ?></td>
<td>₹<?= number_format($row['salary']) ?></td>
<td><?= $row['increment_rate'] ?>%</td>
<td><?= $row['last_increment'] ?? 'Never' ?></td>
<td>

<?php
$canIncrement = true;
if ($row['last_increment']) {
  $lastYear = date("Y", strtotime($row['last_increment']));
  $currentYear = date("Y");
  if ($lastYear == $currentYear) {
    $canIncrement = false;
  }
}
?>

<?php if ($canIncrement): ?>
<form method="POST" action="apply_increment.php"
      onsubmit="return confirm('Apply salary increment?')">
  <input type="hidden" name="employee_id" value="<?= $row['id'] ?>">
  <button class="btn btn-sm btn-success">Apply Increment</button>
</form>
<?php else: ?>
<span class="badge bg-secondary">Already Incremented</span>
<?php endif; ?>

</td>
</tr>
<?php endwhile; ?>

</tbody>
</table>

<a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>

</div>
</body>
</html>

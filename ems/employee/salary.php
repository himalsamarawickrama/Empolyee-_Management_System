<?php
include "../config/db.php";
include "../auth/auth_check.php";

if ($_SESSION['role'] !== 'employee') {
    die("Access Denied");
}

$user_id = $_SESSION['user_id'];

$emp = $conn->query("
  SELECT e.id, e.salary
  FROM employees e
  WHERE e.user_id = $user_id
")->fetch_assoc();

$history = $conn->query("
  SELECT old_salary, new_salary, increment_percent, increment_date
  FROM salary_history
  WHERE employee_id = {$emp['id']}
  ORDER BY increment_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>My Salary</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
<h2>My Salary</h2>

<h4>Current Salary: ₹<?= number_format($emp['salary']) ?></h4>

<h5 class="mt-4">Increment History</h5>
<table class="table table-bordered">
<thead class="table-dark">
<tr>
<th>Old</th>
<th>New</th>
<th>%</th>
<th>Date</th>
</tr>
</thead>
<tbody>

<?php while ($row = $history->fetch_assoc()): ?>
<tr>
<td>₹<?= number_format($row['old_salary']) ?></td>
<td>₹<?= number_format($row['new_salary']) ?></td>
<td><?= $row['increment_percent'] ?>%</td>
<td><?= date("d M Y", strtotime($row['increment_date'])) ?></td>
</tr>
<?php endwhile; ?>

</tbody>
</table>

<a href="dashboard.php" class="btn btn-secondary">Back</a>
</div>
</body>
</html>

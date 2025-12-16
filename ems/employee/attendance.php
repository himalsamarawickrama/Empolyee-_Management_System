<?php
include "../config/db.php";
include "../auth/auth_check.php";

if ($_SESSION['role'] !== 'employee') {
    die("Access Denied");
}

$user_id = $_SESSION['user_id'];

$emp = $conn->query("
  SELECT id FROM employees WHERE user_id = $user_id
")->fetch_assoc();

$data = $conn->query("
  SELECT date, status 
  FROM attendance
  WHERE employee_id = {$emp['id']}
  ORDER BY date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>My Attendance</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
<h2>My Attendance</h2>

<table class="table table-bordered table-hover">
<thead class="table-dark">
<tr>
<th>Date</th>
<th>Status</th>
</tr>
</thead>
<tbody>

<?php while ($row = $data->fetch_assoc()): ?>
<tr>
<td><?= date("d M Y", strtotime($row['date'])) ?></td>
<td><?= $row['status'] ?></td>
</tr>
<?php endwhile; ?>

</tbody>
</table>

<a href="dashboard.php" class="btn btn-secondary">Back</a>
</div>
</body>
</html>

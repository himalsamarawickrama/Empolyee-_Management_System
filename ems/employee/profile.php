<?php
include "../config/db.php";
include "../auth/auth_check.php";

if ($_SESSION['role'] !== 'employee') {
    die("Access Denied");
}

$user_id = $_SESSION['user_id'];

$emp = $conn->query("
  SELECT u.name, u.email, d.name AS department,
         e.position, e.join_date
  FROM users u
  JOIN employees e ON u.id = e.user_id
  JOIN departments d ON e.department_id = d.id
  WHERE u.id = $user_id
")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<title>My Profile</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
<h2>My Profile</h2>

<table class="table table-bordered">
<tr><th>Name</th><td><?= $emp['name'] ?></td></tr>
<tr><th>Email</th><td><?= $emp['email'] ?></td></tr>
<tr><th>Department</th><td><?= $emp['department'] ?></td></tr>
<tr><th>Position</th><td><?= $emp['position'] ?></td></tr>
<tr><th>Join Date</th><td><?= date("d M Y", strtotime($emp['join_date'])) ?></td></tr>
</table>

<a href="dashboard.php" class="btn btn-secondary">Back</a>
</div>
</body>
</html>

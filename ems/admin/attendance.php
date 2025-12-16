<?php
include "../config/db.php";
include "header.php";
include "../auth/auth_check.php";
if ($_SESSION['role'] !== 'admin') die("Access denied");

// Employees list
$employees = $conn->query("
  SELECT e.id, u.name
  FROM employees e
  JOIN users u ON e.user_id = u.id
");

// Save attendance
if (isset($_POST['save'])) {

  $employee_id = $_POST['employee_id'];
  $date = $_POST['date'];
  $status = $_POST['status'];

  // Prevent duplicate attendance per day
  $check = $conn->prepare("
    SELECT id FROM attendance
    WHERE employee_id=? AND date=?
  ");
  $check->bind_param("is", $employee_id, $date);
  $check->execute();
  $check->store_result();

  if ($check->num_rows == 0) {
    $stmt = $conn->prepare("
      INSERT INTO attendance (employee_id, date, status)
      VALUES (?, ?, ?)
    ");
    $stmt->bind_param("iss", $employee_id, $date, $status);
    $stmt->execute();
  }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Attendance</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
<h2>Mark Attendance</h2>

<form method="POST" class="row g-3">

<input type="date" name="date" class="form-control" required>

<select name="employee_id" class="form-select" required>
<option value="">Select Employee</option>
<?php while ($e = $employees->fetch_assoc()): ?>
<option value="<?= $e['id'] ?>"><?= $e['name'] ?></option>
<?php endwhile; ?>
</select>

<select name="status" class="form-select" required>
<option value="Present">Present</option>
<option value="Absent">Absent</option>
<option value="Leave">Leave</option>
</select>

<button name="save" class="btn btn-success">Save Attendance</button>
</form>
</div>

</body>
</html>

<?php
$data = $conn->query("
SELECT u.name, a.date, a.status
FROM attendance a
JOIN employees e ON a.employee_id = e.id
JOIN users u ON e.user_id = u.id
ORDER BY a.date DESC
");
?>


<table class="table table-bordered mt-4">
<thead class="table-dark">
<tr>
<th>Employee</th>
<th>Date</th>
<th>Status</th>
</tr>
</thead>
<tbody>
<?php while ($row = $data->fetch_assoc()): ?>
<tr>
<td><?= $row['name'] ?></td>
<td><?= $row['date'] ?></td>
<td><?= $row['status'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<?php
include "header.php";
require_once "../config/db.php";
require_once "../auth/auth_check.php";

requireRole('admin');

$msg = "";

/* ADD QUALIFICATION */
if (isset($_POST['add'])) {

    $employee_id   = intval($_POST['employee_id']);
    $qualification = trim($_POST['qualification']);
    $institute   = trim($_POST['institute']);
    $year          = intval($_POST['year']);

    $sql = "
        INSERT INTO qualifications 
        (employee_id, qualification, institute, year)
        VALUES (?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("issi", $employee_id, $qualification, $institution, $year);

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    $msg = "Qualification added successfully";
}

/* FETCH EMPLOYEES */
$employees = $conn->query("
    SELECT e.id, u.name
    FROM employees e
    JOIN users u ON e.user_id = u.id
    ORDER BY u.name
");

if (!$employees) {
    die("Employee query failed: " . $conn->error);
}

/* FETCH QUALIFICATIONS */
$quals = $conn->query("
    SELECT 
        q.id,
        u.name,
        q.qualification,
        q.institute,
        q.year
    FROM qualifications q
    JOIN employees e ON q.employee_id = e.id
    JOIN users u ON e.user_id = u.id
    ORDER BY u.name
");

if (!$quals) {
    die("Qualification query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Employee Qualifications</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container mt-4">

<h2>Employee Qualifications</h2>

<?php if (!empty($msg)): ?>
  <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<form method="POST" class="row g-2 mb-4">

  <div class="col-md-3">
    <select name="employee_id" class="form-select" required>
      <option value="">Select Employee</option>
      <?php while ($e = $employees->fetch_assoc()): ?>
        <option value="<?= $e['id'] ?>">
          <?= htmlspecialchars($e['name']) ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="col-md-3">
    <input name="qualification" class="form-control" placeholder="Qualification" required>
  </div>

  <div class="col-md-3">
    <input name="institution" class="form-control" placeholder="Institution" required>
  </div>

  <div class="col-md-2">
    <input type="number" name="year" class="form-control" placeholder="Year" required>
  </div>

  <div class="col-md-1">
    <button name="add" class="btn btn-primary w-100">Add</button>
  </div>

</form>

<table class="table table-bordered table-hover">
<thead class="table-dark">
<tr>
  <th>Employee</th>
  <th>Qualification</th>
  <th>Institution</th>
  <th>Year</th>
</tr>
</thead>
<tbody>

<?php if ($quals->num_rows > 0): ?>
  <?php while ($q = $quals->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($q['name']) ?></td>
      <td><?= htmlspecialchars($q['qualification']) ?></td>
      <td><?= htmlspecialchars($q['institute']) ?></td>
      <td><?= htmlspecialchars($q['year']) ?></td>
    </tr>
  <?php endwhile; ?>
<?php else: ?>
  <tr>
    <td colspan="4" class="text-center">No qualifications found</td>
  </tr>
<?php endif; ?>

</tbody>
</table>

<a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>

</div>
</body>
</html>

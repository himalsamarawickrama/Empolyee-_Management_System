<?php
include "../config/db.php";
include "../auth/auth_check.php";
if ($_SESSION['role'] !== 'admin') die("Access denied");

// Fetch employees
$employees = $conn->query("
  SELECT e.id, u.name, e.salary, e.increment_rate, e.last_increment
  FROM employees e
  JOIN users u ON e.user_id = u.id
");

// Handle increment
if (isset($_POST['increment'])) {

  $employee_id = $_POST['employee_id'];

  // Get employee data
  $stmt = $conn->prepare("
    SELECT salary, increment_rate, last_increment
    FROM employees WHERE id=?
  ");
  $stmt->bind_param("i", $employee_id);
  $stmt->execute();
  $result = $stmt->get_result()->fetch_assoc();

  $old_salary = $result['salary'];
  $rate = $result['increment_rate'];
  $last_increment = $result['last_increment'];

  // Check yearly increment rule
  if ($last_increment && date('Y') == date('Y', strtotime($last_increment))) {
    $error = "Salary already incremented this year.";
  } else {

    $increment_amount = ($old_salary * $rate) / 100;
    $new_salary = $old_salary + $increment_amount;
    $today = date('Y-m-d');

    // Update employees table
    $update = $conn->prepare("
      UPDATE employees
      SET salary=?, last_increment=?
      WHERE id=?
    ");
    $update->bind_param("isi", $new_salary, $today, $employee_id);
    $update->execute();

    // Insert into history
    $history = $conn->prepare("
      INSERT INTO salary_history
      (employee_id, old_salary, new_salary, increment_percent, increment_date)
      VALUES (?, ?, ?, ?, ?)
    ");
    $history->bind_param(
      "iiiis",
      $employee_id,
      $old_salary,
      $new_salary,
      $rate,
      $today
    );
    $history->execute();

    $success = "Salary incremented successfully.";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Salary Increment</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
<h2>Salary Increment</h2>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if (isset($success)): ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="POST">

<select name="employee_id" class="form-select mb-3" required>
<option value="">Select Employee</option>
<?php while ($e = $employees->fetch_assoc()): ?>
<option value="<?= $e['id'] ?>">
  <?= $e['name'] ?> (Current: <?= $e['salary'] ?>)
</option>
<?php endwhile; ?>
</select>

<button name="increment" class="btn btn-primary">
Apply Increment
</button>

</form>
</div>

</body>
</html>

<?php
$history = $conn->query("
SELECT u.name, h.old_salary, h.new_salary,
       h.increment_percent, h.increment_date
FROM salary_history h
JOIN employees e ON h.employee_id = e.id
JOIN users u ON e.user_id = u.id
ORDER BY h.increment_date DESC
");
?>

<table class="table table-bordered mt-4">
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
<?php while ($r = $history->fetch_assoc()): ?>
<tr>
<td><?= $r['name'] ?></td>
<td><?= $r['old_salary'] ?></td>
<td><?= $r['new_salary'] ?></td>
<td><?= $r['increment_percent'] ?>%</td>
<td><?= $r['increment_date'] ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<?php
include "../config/db.php";
include "../auth/auth_check.php";

if ($_SESSION['role'] !== 'admin') {
    die("Access Denied");
}

/* CHECK EMPLOYEE ID */
if (!isset($_GET['id'])) {
    header("Location: employees.php");
    exit;
}

$emp_id = intval($_GET['id']);
$msg = "";

/* UPDATE EMPLOYEE */
if (isset($_POST['update'])) {

    $department_id = intval($_POST['department_id']);
    $position = trim($_POST['position']);
    $salary = intval($_POST['salary']);

    $stmt = $conn->prepare("
        UPDATE employees
        SET department_id = ?, position = ?, salary = ?
        WHERE id = ?
    ");
    $stmt->bind_param("isii", $department_id, $position, $salary, $emp_id);
    $stmt->execute();

    $msg = "Employee updated successfully";
}

/* FETCH EMPLOYEE */
$stmt = $conn->prepare("
    SELECT e.*, u.name, u.email
    FROM employees e
    JOIN users u ON e.user_id = u.id
    WHERE e.id = ?
");
$stmt->bind_param("i", $emp_id);
$stmt->execute();
$employee = $stmt->get_result()->fetch_assoc();

/* FETCH DEPARTMENTS */
$departments = $conn->query("SELECT id, name FROM departments");
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Employee</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

<h2>Edit Employee</h2>

<?php if ($msg): ?>
<div class="alert alert-success"><?= $msg ?></div>
<?php endif; ?>

<form method="POST" class="card p-4 shadow-sm">

<div class="mb-3">
<label class="form-label">Employee Name</label>
<input class="form-control" value="<?= htmlspecialchars($employee['name']) ?>" disabled>
</div>

<div class="mb-3">
<label class="form-label">Email</label>
<input class="form-control" value="<?= htmlspecialchars($employee['email']) ?>" disabled>
</div>

<div class="mb-3">
<label class="form-label">Department</label>
<select name="department_id" class="form-select" required>
<?php while ($d = $departments->fetch_assoc()): ?>
<option value="<?= $d['id'] ?>"
<?= $d['id'] == $employee['department_id'] ? 'selected' : '' ?>>
<?= htmlspecialchars($d['name']) ?>
</option>
<?php endwhile; ?>
</select>
</div>

<div class="mb-3">
<label class="form-label">Position</label>
<input class="form-control" name="position"
value="<?= htmlspecialchars($employee['position']) ?>" required>
</div>

<div class="mb-3">
<label class="form-label">Salary</label>
<input type="number" class="form-control" name="salary"
value="<?= $employee['salary'] ?>" required>
</div>

<button name="update" class="btn btn-primary">Update Employee</button>
<a href="employees.php" class="btn btn-secondary">Back</a>

</form>

</div>
</body>
</html>

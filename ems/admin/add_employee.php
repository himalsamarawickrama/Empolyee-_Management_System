<?php
include "header.php";
require_once "../config/db.php";
require_once "../auth/auth_check.php";

requireRole('admin');

$msg = "";
$error = "";

/* FETCH DEPARTMENTS */
$departments = $conn->query("SELECT id, name FROM departments");

if (!$departments) {
    die("Department query failed: " . $conn->error);
}

/* SAVE EMPLOYEE */
if (isset($_POST['save'])) {

    $name   = trim($_POST['name']);
    $email  = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $department_id = intval($_POST['department_id']);
    $position      = trim($_POST['position']);
    $join_date     = $_POST['join_date'];
    $salary        = intval($_POST['salary']);
    $increment     = intval($_POST['increment_rate']);

    /* CHECK EMAIL EXISTS */
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "Email already exists";
    } else {

        /* INSERT INTO USERS */
        $stmt = $conn->prepare("
            INSERT INTO users (name, email, password, role)
            VALUES (?, ?, ?, 'employee')
        ");
        if (!$stmt) {
            die("User prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sss", $name, $email, $password);
        $stmt->execute();

        $user_id = $conn->insert_id;

        /* INSERT INTO EMPLOYEES */
        $stmt = $conn->prepare("
            INSERT INTO employees
            (user_id, department_id, position, join_date, salary, increment_rate)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        if (!$stmt) {
            die("Employee prepare failed: " . $conn->error);
        }

        $stmt->bind_param(
            "iissii",
            $user_id,
            $department_id,
            $position,
            $join_date,
            $salary,
            $increment
        );
        $stmt->execute();

        $msg = "Employee added successfully";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Employee</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="container mt-4">

<h2 class="mb-3">Add Employee</h2>

<?php if ($msg): ?>
  <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<?php if ($error): ?>
  <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" class="row g-3">

  <div class="col-md-6">
    <label class="form-label">Employee Name</label>
    <input type="text" name="name" class="form-control" required>
  </div>

  <div class="col-md-6">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" required>
  </div>

  <div class="col-md-6">
    <label class="form-label">Password</label>
    <input type="password" name="password" class="form-control" required>
  </div>

  <div class="col-md-6">
    <label class="form-label">Department</label>
    <select name="department_id" class="form-select" required>
      <option value="">Select Department</option>
      <?php while ($d = $departments->fetch_assoc()): ?>
        <option value="<?= $d['id'] ?>">
          <?= htmlspecialchars($d['name']) ?>
        </option>
      <?php endwhile; ?>
    </select>
  </div>

  <div class="col-md-6">
    <label class="form-label">Position</label>
    <input type="text" name="position" class="form-control" required>
  </div>

  <div class="col-md-3">
    <label class="form-label">Join Date</label>
    <input type="date" name="join_date" class="form-control" required>
  </div>

  <div class="col-md-3">
    <label class="form-label">Salary</label>
    <input type="number" name="salary" class="form-control" required>
  </div>

  <div class="col-md-3">
    <label class="form-label">Increment Rate (%)</label>
    <input type="number" name="increment_rate" value="5" class="form-control" required>
  </div>

  <div class="col-md-12 mt-3">
    <button name="save" class="btn btn-success">Save Employee</button>
    <a href="employees.php" class="btn btn-secondary">Cancel</a>
  </div>

</form>

</div>
</body>
</html>

<?php include "footer.php"; ?>

<?php
include "../config/db.php";
include "../auth/auth_check.php";

if ($_SESSION['role'] !== 'admin') {
    die("Access denied");
}

/* GET EMPLOYEES */
$employees = $conn->query("
    SELECT e.id, u.name
    FROM employees e
    JOIN users u ON e.user_id = u.id
");

if (!$employees) {
    die("Employee query failed: " . $conn->error);
}

/* SAVE QUALIFICATION */
if (isset($_POST['save'])) {

    $employee_id   = intval($_POST['employee_id']);
    $qualification = trim($_POST['degree']); // form field
    $institution   = trim($_POST['institution']);
    $year          = intval($_POST['year']);

    $sql = "
        INSERT INTO qualifications 
        (employee_id, qualification, institution, year)
        VALUES (?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "issi",
        $employee_id,
        $qualification,
        $institution,
        $year
    );

    if (!$stmt->execute()) {
        die("Execute failed: " . $stmt->error);
    }

    header("Location: qualifications.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add Qualification</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">
<h2>Add Qualification</h2>

<form method="POST">

<select name="employee_id" class="form-select mb-2" required>
<option value="">Select Employee</option>
<?php while ($e = $employees->fetch_assoc()): ?>
<option value="<?= $e['id'] ?>">
    <?= htmlspecialchars($e['name']) ?>
</option>
<?php endwhile; ?>
</select>

<input name="degree" class="form-control mb-2" placeholder="Qualification / Degree" required>
<input name="institution" class="form-control mb-2" placeholder="Institution" required>
<input type="number" name="year" class="form-control mb-2" placeholder="Year" required>

<button name="save" class="btn btn-success">Save</button>

</form>
</div>

</body>
</html>

<?php
include "../config/db.php";
include "../auth/auth_check.php";

if ($_SESSION['role'] !== 'admin') {
    die("Access Denied");
}

if (!isset($_POST['employee_id'])) {
    header("Location: salary.php");
    exit;
}

$employee_id = (int) $_POST['employee_id'];

/* FETCH EMPLOYEE SALARY INFO */
$stmt = $conn->prepare("
    SELECT salary, increment_rate 
    FROM employees 
    WHERE id = ?
");

if (!$stmt) {
    die("Prepare failed (SELECT): " . $conn->error);
}

$stmt->bind_param("i", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Employee not found");
}

$emp = $result->fetch_assoc();

$old_salary = (float) $emp['salary'];
$rate       = (float) $emp['increment_rate'];

$new_salary = $old_salary + ($old_salary * $rate / 100);

/* UPDATE EMPLOYEE SALARY */
$stmt = $conn->prepare("
    UPDATE employees 
    SET salary = ?, last_increment = CURDATE()
    WHERE id = ?
");

if (!$stmt) {
    die("Prepare failed (UPDATE): " . $conn->error);
}

$stmt->bind_param("di", $new_salary, $employee_id);
$stmt->execute();

/* INSERT SALARY HISTORY */
$stmt = $conn->prepare("
    INSERT INTO salary_history 
    (employee_id, old_salary, new_salary, increment_percent, increment_date)
    VALUES (?, ?, ?, ?, CURDATE())
");

if (!$stmt) {
    die("Prepare failed (INSERT): " . $conn->error);
}

$stmt->bind_param(
    "iddi",
    $employee_id,
    $old_salary,
    $new_salary,
    $rate
);
$stmt->execute();

/* REDIRECT BACK */
header("Location: salary.php?success=1");
exit;

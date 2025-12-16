<?php
include "header.php";
require_once "../config/db.php";

if ($_SESSION['role'] !== 'admin') {
    die("Access Denied");
}

$data = $conn->query("
  SELECT 
    e.id,
    u.name,
    d.name AS department,
    e.position,
    e.salary,
    e.join_date
  FROM employees e
  JOIN users u ON e.user_id = u.id
  JOIN departments d ON e.department_id = d.id
  ORDER BY u.name
");
?>

<h2 class="mb-3">Employee Management</h2>

<!-- ADD EMPLOYEE BUTTON -->
<a href="add_employee.php" class="btn btn-primary mb-3">
  + Add Employee
</a>

<table class="table table-bordered table-hover">
<thead class="table-dark">
<tr>
  <th>Name</th>
  <th>Department</th>
  <th>Position</th>
  <th>Salary</th>
  <th>Join Date</th>
  <th>Action</th>
</tr>
</thead>
<tbody>

<?php while ($row = $data->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($row['name']) ?></td>
  <td><?= htmlspecialchars($row['department']) ?></td>
  <td><?= htmlspecialchars($row['position']) ?></td>
  <td>₹<?= number_format($row['salary']) ?></td>
  <td><?= date("d M Y", strtotime($row['join_date'])) ?></td>
  <td>
    <a href="edit_employee.php?id=<?= $row['id'] ?>"
       class="btn btn-sm btn-warning">
       Edit
    </a>
  </td>
</tr>
<?php endwhile; ?>

</tbody>
</table>

<a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>

<?php include "footer.php"; ?>

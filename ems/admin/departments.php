<?php
include "header.php";
require_once("../config/db.php");
require_once("../auth/auth_check.php");
requireRole('admin');

/* ✅ ALWAYS define variables */
$msg = "";

/* ADD DEPARTMENT */
if (isset($_POST['add'])) {
    $name = trim($_POST['name']);

    if ($name !== "") {
        $check = $conn->prepare(
            "SELECT id FROM departments WHERE name=?"
        );
        $check->bind_param("s", $name);
        $check->execute();

        if ($check->get_result()->num_rows == 0) {
            $q = $conn->prepare(
                "INSERT INTO departments(name) VALUES(?)"
            );
            $q->bind_param("s", $name);
            $q->execute();
            $msg = "Department added successfully";
        } else {
            $msg = "Department already exists";
        }
    }
}

/* DELETE DEPARTMENT */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM departments WHERE id=$id");
    $msg = "Department deleted";
}

/* FETCH DEPARTMENTS (ALWAYS RUN) */
$departments = $conn->query("
    SELECT d.id, d.name, COUNT(e.id) AS total
    FROM departments d
    LEFT JOIN employees e ON d.id = e.department_id
    GROUP BY d.id
    ORDER BY d.name
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Departments</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container-fluid">
  <div class="row">

    <!-- CONTENT -->
    <div class="col-md-9 col-lg-10 p-4">

      <h2>Department Management</h2>

      <?php if (!empty($msg)): ?>
        <div class="alert alert-info"><?= $msg ?></div>
      <?php endif; ?>

      <form method="POST" class="row g-2 my-3">
        <div class="col-md-6">
          <input class="form-control" name="name"
                 placeholder="Department Name" required>
        </div>
        <div class="col-md-3">
          <button name="add" class="btn btn-primary w-100">
            Add Department
          </button>
        </div>
      </form>

      <table class="table table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Department Name</th>
            <th>Total Employees</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($departments && $departments->num_rows > 0): ?>
            <?php while ($d = $departments->fetch_assoc()): ?>
              <tr>
                <td><?= $d['id'] ?></td>
                <td><?= htmlspecialchars($d['name']) ?></td>
                <td><?= $d['total'] ?></td>
                <td>
                  <a href="?delete=<?= $d['id'] ?>"
                     class="btn btn-sm btn-danger"
                     onclick="return confirm('Delete this department?')">
                     Delete
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="4" class="text-center text-muted">
                No departments found
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

    </div>
  </div>
</div>

</body>
</html>

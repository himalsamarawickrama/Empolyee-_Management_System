<?php
// start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/db.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['name']    = $user['name'];

            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../employee/dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Invalid email";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>EMS Login</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body {
  background: #f5f6fa;
}
.login-box {
  max-width: 400px;
  margin: 100px auto;
}
</style>
</head>

<body>

<div class="login-box card shadow p-4">
  <h3 class="text-center mb-3">Employee Management System</h3>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" autocomplete="off">
    <div class="mb-3">
      <input type="email" name="email" class="form-control" placeholder="Email" required>
    </div>

    <div class="mb-3">
      <input type="password" name="password" class="form-control" placeholder="Password" required>
    </div>

    <button class="btn btn-primary w-100">Login</button>
  </form>
</div>

</body>
</html>

<?php
session_start();
include 'config.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

function test_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$success_msg = '';
if (isset($_SESSION['success_msg'])) {
    $success_msg = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}

$email = $pwd = $role = "";
$emailerr = $pwderr = $roleerr = "";
$no_error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = test_input($_POST['mail']);
    $pwd = test_input($_POST['pwd']);
    $role = isset($_POST['role']) ? $_POST['role'] : '';

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) < 1) {
        $emailerr = "Invalid Email";
    } else {
        $row = mysqli_fetch_assoc($result);

        if (empty($pwd)) {
            $pwderr = "Please Enter Password";
        } else if (!password_verify($pwd, $row['password_hash'])) {
            $pwderr = "Incorrect Password";
        } else if (empty($role)) {
            $roleerr = "Please select at least one role";
        } else if ($role !== $row['roles']) {
            $roleerr = "Selected role does not match";
        } else if ($row['status'] == 'unactive') {
            echo "<div class='alert alert-danger'>User is inactive. Contact the admin.</div>";
        } else if ($row['is_deleted'] == '1') {
            echo "<div class='alert alert-danger'>Account is deleted. Contact the admin.</div>";
        } else {
            $_SESSION['id'] = $row['id'];
            $_SESSION['user'] = $row['username'];
            $_SESSION['profile_img'] = $row['profile_path'];
            $_SESSION['role'] = $row['roles'];

            if (isset($_POST['remember'])) {
                $token = bin2hex(random_bytes(16));
                setcookie('remember', $token, time() + (86400 * 30), '/', "", true, true);
                $update = "UPDATE users SET remember_token=? WHERE id=?";
                $stmt = mysqli_prepare($conn, $update);
                mysqli_stmt_bind_param($stmt, "si", $token, $row['id']);
                mysqli_stmt_execute($stmt);
            }
            $no_error = true;
        }
    }

    if ($no_error) {
        header('Location: dashboard.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h4 class="mb-0">User Login</h4>
        </div>
        <div class="card-body">
            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($success_msg); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

          <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">

            <!-- Email -->
            <div class="mb-3">
              <label for="mail" class="form-label">Email</label>
              <input type="email" class="form-control" id="mail" name="mail" value="<?= htmlspecialchars($email) ?>">
              <div class="text-danger"><?= $emailerr ?></div>
            </div>

            <!-- Password -->
            <div class="mb-3">
              <label for="pwd" class="form-label">Password</label>
              <input type="password" class="form-control" id="pwd" name="pwd" value="<?= htmlspecialchars($pwd) ?>">
              <div class="text-danger"><?= $pwderr ?></div>
            </div>

            <!-- Remember Me -->
            <div class="form-check mb-3">
              <input type="checkbox" class="form-check-input" id="remember" name="remember">
              <label class="form-check-label" for="remember">Remember Me</label>
            </div>

            <!-- Role -->
            <div class="mb-3">
              <label for="role" class="form-label">Role</label>
              <select class="form-select" id="role" name="role">
                <option value="" disabled selected>Select your role</option>
                <option value="admin" <?= $role == 'admin' ? 'selected' : '' ?>>Admin</option>
                <option value="editor" <?= $role == 'editor' ? 'selected' : '' ?>>Editor</option>
                <option value="viewer" <?= $role == 'viewer' ? 'selected' : '' ?>>Viewer</option>
              </select>
              <div class="text-danger"><?= $roleerr ?></div>
            </div>

            <!-- Submit -->
            <div class="d-grid mb-3">
              <button type="submit" class="btn btn-primary">Login</button>
            </div>

            <!-- Links -->
            <div class="text-center">
              <a href="forget-password.php" class="text-decoration-none">Forgot Password?</a> |
              <a href="register.php" class="text-decoration-none">Register</a>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

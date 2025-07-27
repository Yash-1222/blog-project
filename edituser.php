<?php
include "config.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$success = "";
$user = null;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !$id) {
    die("ID is required.");
}

// Fetch user data
if ($id) {
    $sql = "SELECT * FROM users WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result)) {
        $user = mysqli_fetch_assoc($result);
    } else {
        die("User not found.");
    }
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pid = intval($_POST['pid']);
    $name = $_POST['name'];
    $content = $_POST['content'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    // Build query directly (unsafe if inputs aren't trusted)
    $update = "UPDATE users SET username='$name', comment='$content', email='$email', roles='$role', status='$status' WHERE id = $pid";
    $result = mysqli_query($conn, $update);

    if ($result) {
        $success = "User updated successfully. Redirecting...";

        // Re-fetch updated data
        $sql = "SELECT * FROM users WHERE id = $pid";
        $result = mysqli_query($conn, $sql);
        if ($result && mysqli_num_rows($result)) {
            $user = mysqli_fetch_assoc($result);
        }

        header("Refresh:3; URL=users.php");
    } else {
        $success = "Update failed: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Update User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Update User</h4>
          </div>
          <div class="card-body">

            <?php if (!empty($success)): ?>
              <div class="alert alert-info"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . urlencode($id) ?>" method="post">
              <input type="hidden" name="pid" value="<?= htmlspecialchars($user['id'] ?? '') ?>" />

              <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="name"
                  name="name"
                  value="<?= htmlspecialchars($user['username'] ?? '') ?>"
                  required
                />
              </div>

              <div class="mb-3">
                <label for="content" class="form-label">Comment</label>
                <textarea class="form-control" id="content" name="content" rows="3"><?= htmlspecialchars($user['comment'] ?? '') ?></textarea>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  name="email"
                  value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                  required
                />
              </div>

              <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <input type="text" class="form-control" id="role_display" value="<?= htmlspecialchars($user['roles'] ?? '') ?>" disabled />
                <input type="hidden" name="role" value="<?= htmlspecialchars($user['roles'] ?? '') ?>" />
              </div>

              <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                  <option value="active" <?= ($user['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                  <option value="unactive" <?= ($user['status'] ?? '') === 'unactive' ? 'selected' : '' ?>>Unactive</option>
                </select>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-success">Update</button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php mysqli_close($conn); ?>

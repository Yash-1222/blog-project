<?php
include "config.php";
$email = isset($_GET['email']) ? mysqli_real_escape_string($conn, $_GET['email']) : '';
$token = isset($_GET['token']) ? mysqli_real_escape_string($conn, $_GET['token']) : '';

$sql = "SELECT * FROM users WHERE email='$email' AND reset_link_token='$token'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    if ($user['expiry_date'] >= date("Y-m-d H:i:s")) {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <!-- ✅ Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card p-4 shadow" style="max-width: 400px; width: 100%;">
        <h2 class="text-center mb-4">Set New Password</h2>
        <form action="update-password.php" method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

            <div class="mb-3">
                <label class="form-label">New Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">Update Password</button>
        </form>
    </div>
</div>

</body>
</html>
<?php
    } else {
        echo "<div class='container mt-5'><div class='alert alert-danger'>The reset link has expired.</div></div>";
    }
} else {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Invalid password reset link.</div></div>";
}
?>

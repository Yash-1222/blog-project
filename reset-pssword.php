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
<head><title>Reset Password</title></head>
<body>
    <h2>Set New Password</h2>
    <form action="update-password.php" method="POST">
        <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
        <label>New Password:</label>
        <input type="password" name="password" required><br>
        <label>Confirm Password:</label>
        <input type="password" name="confirm_password" required><br>
        <button type="submit">Update Password</button>
    </form>
</body>
</html>
<?php
    } else {
        echo "The reset link has expired.";
    }
} else {
    echo "Invalid password reset link.";
}
?>

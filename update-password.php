<?php
include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $token = mysqli_real_escape_string($conn, $_POST['token']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        die("Passwords do not match.");
    }

    $sql = "SELECT * FROM users WHERE email='$email' AND reset_link_token='$token'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update = "UPDATE users SET password_hash='$hashed_password', reset_link_token=NULL, expiry_date=NULL WHERE email='$email'";
        if (mysqli_query($conn, $update)) {
            echo "Password has been successfully updated."."<br>";
            echo "<a href='login.php'>login</a>";
        } else {
            echo "Failed to update password.";
        }
    } else {
        echo "Invalid token or email.";
    }
}
?>

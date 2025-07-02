<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load Composer's autoloader (same folder)
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

include "config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");

    if (mysqli_num_rows($result) > 0) {
        $token = bin2hex(random_bytes(16));
        $expiry_date = date("Y-m-d H:i:s", time() + 3600); // 1 hour from now
        $sql ="UPDATE users SET reset_link_token='$token', expiry_date='$expiry_date' WHERE email='$email'";
        mysqli_query($conn, $sql);

      $reset_link = "http://localhost82.local.com/training/login&password/reset-pssword.php?email=" . urlencode($email) . "&token=" . urlencode($token);

        $mail = new PHPMailer(true);

        try {
            // SMTP settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'yyash.rajput22@gmail.com';         // ⛳ Your Gmail
            $mail->Password   = 'cbxtflexnnsdpquk';      // 🔐 App password, not Gmail password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;     
            $mail->Port       = 465;

            // Email settings
            $mail->setFrom('yyash.rajput22@gmail.com', 'appify');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset';
            $mail->Body    = "Click to reset your password: <a href='$reset_link'>$reset_link</a>";

            $mail->send();
            echo "✅ Reset link sent successfully. Check your inbox.";
        } catch (Exception $e) {
            echo " Mailer Error: " . $mail->ErrorInfo;
        }

    } else {
        echo " No user found with that email.";
    }
} else {
    echo " Invalid request.";
}
?>

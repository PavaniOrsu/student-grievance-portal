<?php
require 'db_connection.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    $stmt = $conn->prepare("SELECT fullname, verify_token, is_verified FROM users WHERE username=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['is_verified'] == 1) {
            echo "<div style='font-family:Arial;text-align:center;margin-top:50px;'>
                    <h3>Your email is already verified ✅</h3>
                    <a href='login.php'>Go to Login</a>
                  </div>";
        } else {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'pavaniorsu99@gmail.com';
                $mail->Password = 'xnblbqzywtfamyhy'; // same app password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('pavaniorsu99@gmail.com', 'Student Grievance Portal');
                $mail->addAddress($email, $user['fullname']);
                $mail->isHTML(true);
                $mail->Subject = 'Resend Verification Email';
                $mail->Body = "
                    <h2>Hello, {$user['fullname']}!</h2>
                    <p>Click below to verify your email:</p>
                    <a href='http://localhost/student_portal/verify_email.php?token={$user['verify_token']}' style='background:#004aad;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Verify Email</a>
                ";
                $mail->send();

                echo "<div style='font-family:Arial;text-align:center;margin-top:50px;'>
                        <h3>Verification email resent successfully! 📩</h3>
                        <p>Check your inbox.</p>
                      </div>";
            } catch (Exception $e) {
                echo "<div style='font-family:Arial;text-align:center;margin-top:50px;'>
                        <h3>Failed to resend email ❌</h3>
                        <p>Error: {$mail->ErrorInfo}</p>
                      </div>";
            }
        }
    } else {
        echo "<div style='font-family:Arial;text-align:center;margin-top:50px;'>
                <h3>Email not found ❌</h3>
                <p>Please sign up first.</p>
              </div>";
    }
}
?>
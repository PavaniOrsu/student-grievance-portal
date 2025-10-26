<?php
session_start();
require 'db_connection.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $check = $conn->prepare("SELECT id, fullname, is_verified FROM users WHERE verify_token = ?");
    $check->bind_param("s", $token);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if ($user['is_verified'] == 1) {
            $_SESSION['verified_user'] = $user['fullname'];
            header("Location: complaint_form.php");
            exit();
        } else {
            $update = $conn->prepare("UPDATE users SET is_verified = 1 WHERE verify_token = ?");
            $update->bind_param("s", $token);
            $update->execute();

            $_SESSION['verified_user'] = $user['fullname'];
            header("Location: complaint_form.php");
            exit();
        }
    } else {
        echo "<div style='font-family:Arial;text-align:center;margin-top:50px;'>
                <h2>Invalid or expired verification link ❌</h2>
                <p>Please try signing up again.</p>
              </div>";
    }
} else {
    echo "<div style='font-family:Arial;text-align:center;margin-top:50px;'>
            <h2>No token found ⚠</h2>
          </div>";
}
?>
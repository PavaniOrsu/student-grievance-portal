<?php
session_start();
require 'db_connection.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";
$verify_link = "";

if (isset($_POST['signup'])) {
    $fullname = trim($_POST['fullname']);
    $username = strtolower(trim($_POST['username'])); // College Email
    $vtu_no = trim($_POST['vtu_no']);
    $department = trim($_POST['department']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!preg_match("/^vtu\d+@veltech\.edu\.in$/", $username)) {
        $message = "<div class='alert alert-danger'>Please use your college email (e.g., vtu12345@veltech.edu.in)</div>";
    } elseif ($password !== $confirm_password) {
        $message = "<div class='alert alert-danger'>Passwords do not match.</div>";
    } else {
        $check = $conn->prepare("SELECT id, is_verified, verify_token FROM users WHERE username=?");
        $check->bind_param("s", $username);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if ($user['is_verified'] == 1) {
                $message = "<div class='alert alert-warning'>You already have an account. <a href='login.php' class='alert-link'>Login here</a>.</div>";
            } else {
                $verify_link = "<a href='verify_email.php?token={$user['verify_token']}' class='btn btn-sm btn-primary ms-2'>Verify Email</a>";
                $message = "<div class='alert alert-info'>You already registered but haven’t verified your email. Check your inbox or <a href='resend_verification.php?email=$username' class='alert-link'>Resend Verification</a>.</div>";
            }
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $verify_token = bin2hex(random_bytes(16));

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'pavaniorsu99@gmail.com';
                $mail->Password = 'xnblbqzywtfamyhy'; // Gmail App Password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('pavaniorsu99@gmail.com', 'Student Grievance Portal');
                $mail->addAddress($username, $fullname);
                $mail->isHTML(true);
                $mail->Subject = 'Verify Your Email';
                $mail->Body = "
                    <h2>Hello, $fullname!</h2>
                    <p>Please verify your email to activate your account:</p>
                    <a href='http://localhost/student_portal/verify_email.php?token=$verify_token' style='background:#004aad;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;'>Verify Email</a>
                    <p>If you didn’t create this account, ignore this email.</p>
                ";

                $mail->send();

                $stmt = $conn->prepare("INSERT INTO users (fullname, username, vtu_no, department, password, verify_token, is_verified) VALUES (?, ?, ?, ?, ?, ?, 0)");
                $stmt->bind_param("ssssss", $fullname, $username, $vtu_no, $department, $hashed_password, $verify_token);
                $stmt->execute();

                $message = "<div class='alert alert-success'>Signup successful! Check your email to verify your account.</div>";
                $verify_link = "<a href='verify_email.php?token=$verify_token' class='btn btn-sm btn-primary ms-2'>Verify Email</a>";
            } catch (Exception $e) {
                $message = "<div class='alert alert-danger'>Could not send verification email. Error: {$mail->ErrorInfo}</div>";
            }
        }
    }
}

if (isset($_SESSION['verified_user'])) {
    header("Location: complaint_form.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Signup - Grievance Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, rgba(142,45,226,0.1), rgba(74,0,224,0.1));
            padding-top: 80px;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, #8e2de2, #4a00e0);
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
            font-weight: 500;
        }
        .nav-link:hover {
            color: #fbc531 !important;
        }

        /* Signup Card */
        .signup-card {
            max-width: 520px;
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            margin: auto;
        }
        .signup-card h3 {
            background: linear-gradient(135deg, #8e2de2, #4a00e0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
        }
        .form-control {
            border-radius: 10px;
            padding: 10px 12px;
        }
        .btn-primary {
            background: linear-gradient(135deg, #8e2de2, #4a00e0);
            border: none;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px;
            transition: 0.3s;
        }
        .btn-primary:hover {
            background: #fbc531;
            color: #2c2c2c;
            transform: scale(1.03);
        }

        footer {
            margin-top: 80px;
            background: linear-gradient(135deg, #4a00e0, #8e2de2);
            color: #ffffff;
            text-align: center;
            padding: 20px 0;
            font-size: 0.9rem;
        }
        footer a {
            color: #fbc531;
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-mortarboard"></i> Student Grievance Portal</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link active" href="signup.php">Signup</a></li>
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="track_complaints.php">Track Complaints</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Signup Form -->
<div class="container mt-5">
    <div class="signup-card">
        <h3 class="text-center mb-4">Student Registration</h3>
        <?php echo $message; ?>
        <form method="post" action="">
            <div class="mb-3">
                <label>Full Name</label>
                <input type="text" name="fullname" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email (College ID)</label>
                <?php echo $verify_link; ?>
                <input type="email" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>VTU Number</label>
                <input type="text" name="vtu_no" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Department</label>
                <input type="text" name="department" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" name="signup" class="btn btn-primary w-100">Sign Up</button>
        </form>
        <p class="mt-3 text-center">Already registered? <a href="login.php" class="text-decoration-none text-primary fw-bold">Login here</a></p>
    </div>
</div>

<!-- Footer -->
<footer>
  <p>© 2025 Student Grievance Portal | Designed for VTU Students</p>
  <p><i class="bi bi-envelope"></i> <a href="mailto:support@college.edu">support@college.edu</a></p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
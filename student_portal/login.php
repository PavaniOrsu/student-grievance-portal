<?php
session_start();
require 'db_connection.php';

$error = "";

// Redirect if already logged in
if (isset($_SESSION['verified_user'])) {
    header("Location: complaint_form.php");
    exit();
}

if (isset($_POST['login_btn'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        if ($user['is_verified'] == 1) {
            $_SESSION['verified_user'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            header("Location: complaint_form.php");
            exit();
        } else {
            $error = "<div class='alert alert-warning'>Please verify your email before logging in.</div>";
        }
    } else {
        $error = "<div class='alert alert-danger'>Invalid username or password.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - Student Grievance Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
/* ---------- Background ---------- */
body {
    background-image: url('images/background1.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

/* Dark overlay */
body::before {
    content: "";
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.2);
    z-index: 0;
}

/* ---------- Login Form ---------- */
.login-container {
   background: rgba(255, 255, 255, 0.4); /* White with 80% opacity */
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
   
    position: relative;
    z-index: 1;
    padding: 40px 30px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    max-width: 400px;
    width: 100%;
    text-align: center;
}

.login-container h2 {
    font-weight: 700;
    color: #8e2de2;
    margin-bottom: 20px;
}

.login-container .form-control {
    border-radius: 8px;
    margin-bottom: 15px;
    padding: 12px;
}

.btn-login {
    width: 100%;
    padding: 12px;
    font-weight: 600;
    border-radius: 8px;
    background: linear-gradient(135deg, #8e2de2, #4a00e0);
    border: none;
    color: #fff;
    transition: all 0.3s ease;
}
.btn-login:hover {
    background: #fbc531;
    color: #2c2c2c;
    transform: scale(1.03);
}

.login-container a {
    color: #8e2de2;
    text-decoration: none;
}
.login-container a:hover {
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 576px) {
    .login-container {
        padding: 30px 20px;
    }
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
        <li class="nav-item"><a class="nav-link" href="signup.php">Signup</a></li>
        <li class="nav-item"><a class="nav-link active" href="login.php">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="track_complaints.php">Track Complaints</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="login-container">
    <h2>Student Login</h2>
    <p class="text-muted mb-4">Access your Grievance Portal</p>
    <?php echo $error; ?>
    <form method="post">
        <input type="email" name="username" class="form-control" placeholder="vtuXXXX@veltech.edu.in" required>
        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
        <button type="submit" name="login_btn" class="btn btn-login">Login</button>
    </form>
    <p class="mt-3 mb-0">Don’t have an account? <a href="signup.php">Register here</a></p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

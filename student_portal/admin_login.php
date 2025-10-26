<?php
session_start();
require 'db_connection.php';

$error = "";

$admin_username = "admin@veltech.edu.in";
$admin_password = "admin123";

if (isset($_POST['login_btn'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "<div class='alert alert-danger text-center'>Invalid credentials.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login - Student Grievance Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background:  linear-gradient(135deg, rgba(142,45,226,0.1), rgba(74,0,224,0.1));
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Poppins', sans-serif;
}
.card {
    width: 400px;
    padding: 40px;
    border-radius: 15px;
    background: rgba(255,255,255,0.95);
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    transition: 0.3s;
}
.card:hover {
    transform: translateY(-5px);
}
.card h3 {
    color: #4a00e0;
    font-weight: 700;
    text-align: center;
    margin-bottom: 25px;
}
.btn-primary {
    background-color: #8e2de2;
    border-color: #8e2de2;
    transition: 0.3s;
}
.btn-primary:hover {
    background-color: #4a00e0;
    border-color: #4a00e0;
}
.form-control:focus {
    border-color: #8e2de2;
    box-shadow: 0 0 5px rgba(142,45,226,0.5);
}
.alert {
    margin-bottom: 20px;
}
</style>
</head>
<body>

<div class="card">
    <h3>Admin Login</h3>
    <?php echo $error; ?>
    <form method="post">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="username" class="form-control" required placeholder="admin@veltech.edu.in">
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Enter password">
        </div>
        <button type="submit" name="login_btn" class="btn btn-primary w-100">Login</button>
    </form>
</div>

</body>
</html>

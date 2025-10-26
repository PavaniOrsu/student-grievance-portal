<?php
session_start();
require 'db_connection.php';

if(!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT fullname, username, vtu_no, department FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>About Me | Student Grievance Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
/* Make the page full height and footer stick */
html, body {
    height: 100%;
    margin: 0;
    display: flex;
    flex-direction: column;
}

body {
    background: linear-gradient(135deg, rgba(142,45,226,0.1), rgba(74,0,224,0.1));
    font-family: 'Poppins', sans-serif;
    padding-top: 70px;
    flex: 1; 
}

/* Main content wrapper */
.container1 {
    flex: 1; 
}

/* Footer */
footer {
    background: linear-gradient(135deg, #4a00e0, #8e2de2);
    color: #ffffff;
    text-align: center;
    padding: 25px 0;
}


/* Navbar */
.navbar {
    background: linear-gradient(135deg, #8e2de2, #4a00e0);
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}
.navbar-brand, .nav-link {
    color: #fff !important;
    font-weight: 500;
    transition: 0.3s;
}
.nav-link:hover {
    color: #fbc531 !important;
}

/* Container for About Me card */
.container1 {
    max-width: 480px;
    margin: 0 auto;
}
.container1 {
    max-width: 480px;
    margin: 40px auto;
}
/* Heading */
h2 {
    color: #4a00e0;
    font-weight: 700;
    text-align: center;
    margin-bottom: 30px;
}

/* Profile Card */
.profile-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    padding: 40px;
    text-align: center;
    transition: transform 0.3s, box-shadow 0.3s;
    
}
.profile-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15);
}
.profile-card img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    margin-bottom: 20px;
}
.profile-card p {
    font-size: 1rem;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.profile-card i {
    color: #8e2de2;
    font-size: 1.3rem;
    margin-right: 10px;
}


/* Footer */
footer {
    background: linear-gradient(135deg, #4a00e0, #8e2de2);
    color: #fff;
    text-align: center;
    padding: 25px 0;
}
footer a {
    color: #fbc531;
    text-decoration: none;
}
footer a:hover {
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 576px) {
    .profile-card {
        padding: 20px 15px;
    }
    .profile-card p {
        font-size: 0.95rem;
    }
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm">
  <div class="container">
    <!-- Brand -->
    <a class="navbar-brand fw-bold" href="index.php">
      <i class="bi bi-mortarboard"></i> Student Grievance Portal
    </a>

    <!-- Toggler for mobile -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Links -->
    <div class="collapse navbar-collapse" id="navbarNav">
       <ul class="navbar-nav ms-auto">
       <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
       <li class="nav-item"><a class="nav-link" href="signup.php">Signup</a></li>
       <li class="nav-item"><a class="nav-link" href="track_complaints.php">Track Complaints</a></li>
       <li class="nav-item"><a class="nav-link active" href="about.php">About</a></li>
       </ul>
    </div>

  </div>
</nav>


<div class="container1">
    <h2><i class="bi bi-person-circle"></i> About Me</h2>

    <?php if(!$user): ?>
        <div class="alert alert-danger text-center">
            <i class="bi bi-exclamation-triangle"></i> User details not found.
        </div>
    <?php else: ?>
        <div class="profile-card">
            <p><i class="bi bi-person-fill"></i><strong>Full Name:</strong> <?php echo htmlspecialchars($user['fullname']); ?></p>
            <p><i class="bi bi-envelope-fill"></i><strong>Email:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><i class="bi bi-journal-bookmark-fill"></i><strong>VTU Number:</strong> <?php echo htmlspecialchars($user['vtu_no']); ?></p>
            <p><i class="bi bi-building"></i><strong>Department:</strong> <?php echo htmlspecialchars($user['department']); ?></p>
        </div>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer>
  <p>© 2025 Student Grievance Portal | Designed for VTU Students</p>
  <p><i class="bi bi-envelope"></i> <a href="mailto:support@college.edu">support@college.edu</a></p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Grievance Portal</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
/* General */
html {
  scroll-behavior: smooth;
}
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #f0f3ff, #f9f9ff);
  color: #333;
  overflow-x: hidden;
  padding-top: 50px; /* Prevents overlap with fixed navbar */
  scroll-behavior: smooth;
}


/* Navbar */
.navbar {
  background: linear-gradient(135deg, #8e2de2, #4a00e0);
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}
.navbar-brand, .nav-link {
  color: #fff !important;
  font-weight: 500;
  transition: 0.3s;
}
.nav-link:hover {
  color: #fbc531 !important;
}

/* Hero Section */
.hero {
  text-align: center;
  padding: 120px 20px 100px;
  background: linear-gradient(135deg, rgba(142,45,226,0.1), rgba(74,0,224,0.1));
  border-bottom: 2px solid rgba(142,45,226,0.2);
}
.hero h1 {
  font-weight: 700;
  font-size: 2.8rem;
  background: linear-gradient(135deg, #8e2de2, #4a00e0);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.hero p {
  color: #555;
  font-size: 1.1rem;
  margin-bottom: 30px;
}
.hero .btn {
  background: linear-gradient(135deg, #8e2de2, #4a00e0);
  color: #fff;
  border: none;
  padding: 12px 28px;
  font-weight: 600;
  border-radius: 10px;
  transition: all 0.3s ease;
}
.hero .btn:hover {
  background: #fbc531;
  color: #2c2c2c;
  transform: scale(1.05);
}

/* About Section */
.about {
  padding: 90px 0;
}
.about h2 {
  color: #4a00e0;
  font-weight: 700;
  margin-bottom: 15px;
}
.about p {
  color: #555;
  max-width: 700px;
  margin: 0 auto 50px;
}
.feature-card {
  border: none;
  border-radius: 20px;
  background: #fff;
  box-shadow: 0 10px 25px rgba(0,0,0,0.08);
  padding: 40px 25px;
  transition: all 0.3s ease;
}
.feature-card:hover {
  transform: translateY(-10px);
  box-shadow: 0 15px 35px rgba(0,0,0,0.12);
}
.feature-card i {
  font-size: 2.8rem;
  background: linear-gradient(135deg, #8e2de2, #4a00e0);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  margin-bottom: 20px;
}
.feature-card h5 {
  color: #4a00e0;
  font-weight: 600;
  margin-bottom: 10px;
}

/* Complaint CTA */
.complaint {
  padding: 90px 20px;
  background: linear-gradient(135deg, rgba(142,45,226,0.05), rgba(74,0,224,0.05));
  text-align: center;
}
.complaint h2 {
  color: #4a00e0;
  font-weight: 700;
  margin-bottom: 15px;
}
.complaint p {
  color: #555;
  margin-bottom: 30px;
}
.btn-primary {
  background: linear-gradient(135deg, #8e2de2, #4a00e0);
  border: none;
  font-weight: 600;
  padding: 10px 30px;
  border-radius: 8px;
  transition: 0.3s;
}
.btn-primary:hover {
  background: #fbc531;
  color: #2c2c2c;
}

footer {
  background: linear-gradient(135deg, #4a00e0, #8e2de2);
  color: #ffffff;
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
@media (max-width: 768px) {
  .hero h1 {
    font-size: 2rem;
  }
  .hero p {
    font-size: 1rem;
  }
}

  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm fixed-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#"><i class="bi bi-mortarboard"></i> Student Grievance Portal</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="signup.php">Signup</a></li>
        <li class="nav-item"><a class="nav-link" href="track_complaints.php">Track Complaints</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link active" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- Hero -->
<section class="hero">
  <div class="container">
    <h1>Welcome to the Student Grievance Portal</h1>
    <p>Submit, track, and resolve your issues easily and transparently.</p>
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="complaint_form.php" class="btn btn-lg">Submit a Complaint</a>
    <?php else: ?>
      <a href="login.php" class="btn btn-lg">Login to Submit</a>
    <?php endif; ?>
  </div>
</section>

<!-- About -->
<section class="about text-center">
  <div class="container">
    <h2>About the Portal</h2>
    <p>This portal helps students raise academic or campus-related concerns securely and transparently.</p>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="feature-card">
          <i class="bi bi-shield-lock"></i>
          <h5>Confidential & Secure</h5>
          <p>Your data is safe and accessed only by authorized officials.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card">
          <i class="bi bi-bar-chart"></i>
          <h5>Transparent Tracking</h5>
          <p>Check your complaint progress anytime with your ID.</p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="feature-card">
          <i class="bi bi-lightning-charge"></i>
          <h5>Quick Resolution</h5>
          <p>Our grievance team ensures timely action and updates.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Call to Action -->
<section class="complaint">
  <div class="container">
    <h2>Need to Raise a Complaint?</h2>
    <p>Login using your college email to submit and track your grievances.</p>
    <?php if(isset($_SESSION['user_id'])): ?>
      <a href="complaint_form.php" class="btn btn-primary">Go to Complaint Form</a>
    <?php else: ?>
      <a href="login.php" class="btn btn-primary">Login to Continue</a>
    <?php endif; ?>
  </div>
</section>

<!-- Footer -->
<footer>
  <p>© 2025 Student Grievance Portal | Designed for VTU Students</p>
  <p><i class="bi bi-envelope"></i> <a href="mailto:support@college.edu">support@college.edu</a></p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

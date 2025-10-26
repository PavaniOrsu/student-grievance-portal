<?php
session_start();
require 'db_connection.php';

// Ensure user is logged in
if (!isset($_SESSION['verified_user'])) {
    header("Location: login.php");
    exit();
}

// Fetch user_id if not in session
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    $identifier = trim($_SESSION['verified_user']);

    // Try lookup by username (email)
    $stmt = $conn->prepare("SELECT id, username, fullname FROM users WHERE username = ?");
    $stmt->bind_param("s", $identifier);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['verified_user'] = $row['username'];
    } else {
        // Try lookup by fullname
        $stmt2 = $conn->prepare("SELECT id, username FROM users WHERE fullname = ?");
        $stmt2->bind_param("s", $identifier);
        $stmt2->execute();
        $res2 = $stmt2->get_result();

        if ($res2 && $res2->num_rows > 0) {
            $row2 = $res2->fetch_assoc();
            $_SESSION['user_id'] = $row2['id'];
            $_SESSION['verified_user'] = $row2['username'];
        } else {
            session_unset();
            session_destroy();
            header("Location: login.php");
            exit();
        }
    }
}

$user_id = intval($_SESSION['user_id']);
$message = "";

// Handle complaint form submission
if (isset($_POST['submit_complaint'])) {
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($subject === "" || $description === "") {
        $message = "<div class='alert alert-danger'>All fields are required.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO complaints (user_id, subject, description, status, created_at, updated_at)
                                VALUES (?, ?, ?, 'Pending', NOW(), NOW())");
        if ($stmt === false) {
            $message = "<div class='alert alert-danger'>Prepare failed: " . htmlspecialchars($conn->error) . "</div>";
        } else {
            $stmt->bind_param("iss", $user_id, $subject, $description);
            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'>Complaint submitted successfully!</div>";
            } else {
                $message = "<div class='alert alert-danger'>Something went wrong. Please try again.</div>";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Submit Complaint - Student Grievance Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, rgba(142,45,226,0.1), rgba(74,0,224,0.1));
    font-family: 'Poppins', sans-serif;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
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


/* Container */
.container1 {
    max-width: 600px;
    background: rgba(255, 255, 255, 0.95);
    padding: 40px;
    margin:100px auto;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}
h3 {
    color: #4a00e0;
    text-align: center;
    font-weight: 700;
    margin-bottom: 25px;
}
textarea { resize: none; }

/* Buttons */
.btn-primary {
    background: linear-gradient(135deg, #8e2de2, #4a00e0);
    border: none;
    transition: all 0.3s;
}
.btn-primary:hover {
    background: #fbc531;
    color: #2c2c2c;
    transform: scale(1.02);
}
.btn-outline-secondary:hover {
    background-color: #f0f2f5;
}

/* Footer */
footer {
    margin-top: auto;
    background: linear-gradient(135deg, #4a00e0, #8e2de2);
    color: #ffffff;
    text-align: center;
    padding: 15px 0;
}
footer a {
    color: #fbc531;
    text-decoration: none;
}
footer a:hover {
    text-decoration: underline;
}

/* Icons */
label i {
    color: #8e2de2;
    margin-right: 5px;
}
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm" style="background: linear-gradient(135deg, #8e2de2, #4a00e0);">
  <div class="container">
    <!-- Brand -->
    <a class="navbar-brand fw-bold me-4" href="index.php">
      <i class="bi bi-mortarboard"></i> Student Grievance Portal
    </a>

    <!-- Toggler -->
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
        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>


<!-- Complaint Form -->
<div class="container1 ">
    <h3><i class="bi bi-chat-dots"></i> Submit a Complaint</h3>
    <?php echo $message; ?>
    <form method="post">
        <div class="mb-3">
            <label><i class="bi bi-tag"></i> Subject</label>
            <input type="text" name="subject" class="form-control" required placeholder="Enter complaint subject">
        </div>
        <div class="mb-3">
            <label><i class="bi bi-pencil-square"></i> Description</label>
            <textarea name="description" class="form-control" rows="4" required placeholder="Describe your issue in detail"></textarea>
        </div>
        <button type="submit" name="submit_complaint" class="btn btn-primary w-100"><i class="bi bi-send"></i> Submit Complaint</button>
        <a href="track_complaints.php" class="btn btn-outline-secondary w-100 mt-3"><i class="bi bi-list-check"></i> Track My Complaints</a>
    </form>
</div>

<!-- Footer -->
<footer>
  <p>© 2025 Student Grievance Portal | Designed for Students of VTU</p>
  <p><i class="bi bi-envelope"></i> <a href="mailto:support@college.edu">support@college.edu</a></p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

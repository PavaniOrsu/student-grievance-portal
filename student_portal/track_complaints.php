<?php
session_start();
require 'db_connection.php';

// Redirect to login if not logged in
if (!isset($_SESSION['verified_user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch complaints of the logged-in user
$stmt = $conn->prepare("
    SELECT c.complaint_id, c.subject, c.description, c.status, c.created_at, 
           IFNULL(t.status_update, c.status) AS latest_status, 
           IFNULL(t.updated_on, c.updated_at) AS last_updated
    FROM complaints c
    LEFT JOIN track_complaints t ON c.complaint_id = t.complaint_id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Track Complaints - Student Grievance Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<style>
/* Body */
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, rgba(142,45,226,0.1), rgba(74,0,224,0.1));
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    padding-top: 70px; /* For fixed navbar */
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


/* Container */
.container1 {
    max-width: 95%;
    margin: 30px auto;
    background: #fff;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

/* Heading */
h3 {
    text-align: center;
    font-weight: 700;
    color: #4a00e0;
    margin-bottom: 25px;
}

/* Buttons */
.btn-primary, .btn-back {
    background: linear-gradient(135deg, #8e2de2, #4a00e0);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
}
.btn-primary:hover, .btn-back:hover {
    background: #fbc531;
    color: #2c2c2c;
}

/* Table */
.table {
    border-radius: 10px;
    overflow: hidden;
}
.table thead {
    background: linear-gradient(135deg, #8e2de2, #4a00e0);
    color: #fff;
}
.table-hover tbody tr:hover {
    background-color: rgba(142,45,226,0.1);
    transition: 0.3s;
}

/* Status badges */
.badge-pending {
    background-color: #ff9800;
    color: #fff;
    font-weight: 600;
}
.badge-inprogress {
    background-color: #2196f3;
    color: #fff;
    font-weight: 600;
}
.badge-resolved {
    background-color: #4caf50;
    color: #fff;
    font-weight: 600;
}

/* Footer */
footer {
    background: linear-gradient(135deg, #4a00e0, #8e2de2);
    color: #fff;
    text-align: center;
    padding: 25px 0;
    margin-top: auto;
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
      </ul>
    </div>
  </div>
</nav>

<div class="container1">
    <h3><i class="bi bi-clipboard-data"></i> Track My Complaints</h3>
    <div class="mb-3 text-end">
        <a href="complaint_form.php" class="btn btn-back me-2"><i class="bi bi-plus-circle"></i> New Complaint</a>
        <a href="logout.php" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle text-center">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Created On</th>
                    <th>Last Updated</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['complaint_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['subject']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>
                            <?php 
                                $status = strtolower($row['latest_status']);
                                if ($status == 'pending') echo "<span class='badge badge-pending'>Pending</span>";
                                elseif ($status == 'in progress') echo "<span class='badge badge-inprogress'>In Progress</span>";
                                elseif ($status == 'resolved') echo "<span class='badge badge-resolved'>Resolved</span>";
                                else echo htmlspecialchars($row['latest_status']);
                            ?>
                        </td>
                        <td><?php echo date("d M Y, h:i A", strtotime($row['created_at'])); ?></td>
                        <td><?php echo date("d M Y, h:i A", strtotime($row['last_updated'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">No complaints found. <a href="complaint_form.php" class="alert-link">Submit one now</a>.</div>
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

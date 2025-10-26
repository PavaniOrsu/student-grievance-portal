<?php
session_start();
require 'db_connection.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle status update
if (isset($_POST['update_status'])) {
    $complaint_id = intval($_POST['complaint_id']);
    $new_status = $_POST['status'];

    // Update complaints table
    $stmt = $conn->prepare("UPDATE complaints SET status = ?, updated_at = NOW() WHERE complaint_id = ?");
    $stmt->bind_param("si", $new_status, $complaint_id);
    $stmt->execute();

    // Log change in track_complaints
    $track_stmt = $conn->prepare("INSERT INTO track_complaints (complaint_id, status_update, updated_on) VALUES (?, ?, NOW())");
    $track_stmt->bind_param("is", $complaint_id, $new_status);
    $track_stmt->execute();
}

// Filters
$search = $_GET['search'] ?? '';
$filter_department = $_GET['department'] ?? '';
$filter_status = $_GET['status'] ?? '';

$query = "
SELECT c.complaint_id, c.subject, c.description, c.status, c.created_at, u.fullname, u.department, u.username
FROM complaints c
JOIN users u ON c.user_id = u.id
WHERE 1
";

if ($search) $query .= " AND (u.fullname LIKE '%$search%' OR c.subject LIKE '%$search%')";
if ($filter_department) $query .= " AND u.department = '$filter_department'";
if ($filter_status) $query .= " AND c.status = '$filter_status'";
$query .= " ORDER BY c.created_at DESC";

$result = $conn->query($query);
$departments = $conn->query("SELECT DISTINCT department FROM users");

// Analytics: status count
$status_data = [];
$status_query = $conn->query("SELECT status, COUNT(*) AS count FROM complaints GROUP BY status");
while ($row = $status_query->fetch_assoc()) {
    $status_data[$row['status']] = $row['count'];
}

// Analytics: department count
$dept_data = [];
$dept_query = $conn->query("SELECT u.department, COUNT(c.complaint_id) AS count 
FROM complaints c 
JOIN users u ON c.user_id = u.id 
GROUP BY u.department");
while ($row = $dept_query->fetch_assoc()) {
    $dept_data[$row['department']] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard - Student Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body {
    background: rgba(142,45,226,0.05);
    font-family: 'Poppins', sans-serif;
}
.sidebar {
    width: 250px;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    background: linear-gradient(135deg, #8e2de2, #4a00e0);
    padding-top: 20px;
    color: white;
}
.sidebar h4 {
    text-align: center;
    margin-bottom: 30px;
}
.sidebar a {
    color: #fff;
    display: block;
    padding: 10px 20px;
    text-decoration: none;
    transition: 0.3s;
}
.sidebar a:hover {
    background: rgba(255, 255, 255, 0.15);
}
.main-content {
    margin-left: 270px;
    padding: 30px;
}
.chart-card {
    background: rgba(142,45,226,0.1);
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 0 8px rgba(0,0,0,0.08);
    margin-bottom: 20px;
}
.chart-wrapper {
    height: 250px; /* fixed height for canvas */
    position: relative;
}
.filter-bar, .table-container {
    background: white;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0,0,0,0.08);
}
h2.section-title {
    color: #4a00e0;
    font-weight: 700;
}
.btn-primary {
    background-color: #8e2de2;
    border-color: #8e2de2;
}
.btn-primary:hover {
    background-color: #4a00e0;
    border-color: #4a00e0;
}
.table thead {
    background-color: #8e2de2;
    color: white;
}
/* Mobile sidebar adjustments */
@media (max-width: 768px) {
    .sidebar {
        width: 100%;
        height: auto;
        position: relative;
        display: none; /* hide by default */
    }

    .sidebar.active {
        display: block;
    }

    .main-content {
        margin-left: 0;
        padding: 15px;
    }
    .chart-card {
        height: 300px;
    }
    .table-container {
        padding: 10px;
    }

    .table thead {
        font-size: 12px;
    }

    .table tbody td {
        font-size: 12px;
    }
}

</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4>Admin Panel</h4>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="admin_logout.php">Logout</a>
</div>
<button class="btn btn-primary d-md-none mb-3" id="sidebarToggle">☰ Menu</button>
<script>
document.getElementById('sidebarToggle').addEventListener('click', function(){
    document.querySelector('.sidebar').classList.toggle('active');
});
</script>
<!-- Main Content -->
<div class="main-content">
    <h2 class="section-title text-center mb-4">Student Complaints Dashboard</h2>

    <!-- Analytics Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="chart-card">
                <h5 class="text-center text-primary mb-3">Complaints by Status</h5>
                <div class="chart-wrapper">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-card">
                <h5 class="text-center text-primary mb-3">Complaints by Department</h5>
                <div class="chart-wrapper">
                    <canvas id="deptChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="get" class="filter-bar mb-4">
        <div class="row align-items-center g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by student or subject" value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
                <select name="department" class="form-select">
                    <option value="">All Departments</option>
                    <?php while($d = $departments->fetch_assoc()): ?>
                        <option value="<?= $d['department'] ?>" <?= $filter_department == $d['department'] ? 'selected' : '' ?>>
                            <?= $d['department'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="Pending" <?= $filter_status == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="In Progress" <?= $filter_status == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                    <option value="Resolved" <?= $filter_status == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Apply</button>
            </div>
        </div>
    </form>

    <!-- Complaints Table -->
    <div class="table-container table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="text-center">
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Department</th>
                    <th>Email</th>
                    <th>Subject</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Submitted On</th>
                    <th>Update</th>
                </tr>
            </thead>
            <tbody>
                <?php if($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['complaint_id'] ?></td>
                        <td><?= htmlspecialchars($row['fullname']) ?></td>
                        <td><?= htmlspecialchars($row['department']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['subject']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td>
                            <form method="post" class="d-flex gap-2">
                                <input type="hidden" name="complaint_id" value="<?= $row['complaint_id'] ?>">
                                <select name="status" class="form-select form-select-sm">
                                    <option value="Pending" <?= $row['status']=='Pending'?'selected':'' ?>>Pending</option>
                                    <option value="In Progress" <?= $row['status']=='In Progress'?'selected':'' ?>>In Progress</option>
                                    <option value="Resolved" <?= $row['status']=='Resolved'?'selected':'' ?>>Resolved</option>
                                </select>
                                <button type="submit" name="update_status" class="btn btn-sm btn-primary">Save</button>
                            </form>
                        </td>
                        <td><?= date("d M Y, h:i A", strtotime($row['created_at'])) ?></td>
                        <td><a href="delete_complaint.php?id=<?= $row['complaint_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this complaint?')">Delete</a></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9" class="text-center text-muted">No complaints found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
const statusData = <?= json_encode($status_data) ?>;
const deptData = <?= json_encode($dept_data) ?>;

// Pie Chart (Status)
new Chart(document.getElementById('statusChart'), {
    type: 'pie',
    data: {
        labels: Object.keys(statusData),
        datasets: [{
            data: Object.values(statusData),
            backgroundColor: ['#8e2de2','#4a00e0','#6a11cb','#00b894'],
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Bar Chart (Department)
new Chart(document.getElementById('deptChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(deptData),
        datasets: [{
            label: 'Complaints',
            data: Object.values(deptData),
            backgroundColor: '#8e2de2',
            barPercentage: 0.5,   // make bars thinner
            categoryPercentage: 0.6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

</script>

</body>
</html>

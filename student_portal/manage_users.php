<?php
session_start();
require 'db_connection.php';
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}
$result = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <style>
        table { width: 90%; margin: 30px auto; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: center; }
        th { background-color: #007bff; color: white; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Manage Users</h2>
    <table>
        <tr><th>ID</th><th>Full Name</th><th>Username</th><th>Department</th><th>Actions</th></tr>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= $row['fullname'] ?></td>
            <td><?= $row['username'] ?></td>
            <td><?= $row['department'] ?></td>
            <td>
                <a href="edit_user.php?id=<?= $row['id'] ?>">Edit</a> |
                <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Are you sure?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

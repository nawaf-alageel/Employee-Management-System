<?php
// File: admin/index.php

// Include the configuration file
include '../php/config.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

// Set page title and header
$page_title = "Admin Panel - Employee Management System";
$header_title = "Admin Panel";
include '../php/header.php';
?>

<div class="admin-dashboard">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <ul>
        <li><a href="<?php echo $base_path; ?>/admin/manage_employees.php" class="button">Manage Employees</a></li>
        <li><a href="<?php echo $base_path; ?>/admin/manage_tasks.php" class="button">Manage Tasks</a></li>
        <li><a href="<?php echo $base_path; ?>/admin/manage_users.php" class="button">Manage Users</a></li>
        <li><a href="<?php echo $base_path; ?>/admin/view_employee.php" class="button">view employee</a></li>

    </ul>
</div>

<?php
include '../php/footer.php';
?>

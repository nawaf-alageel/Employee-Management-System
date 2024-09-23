<?php
// File: php/header.php

// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set the correct base path for URLs (URL, not a file path)
$base_path = '/My-porgiact/employee_management'; // Adjust based on your project directory
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Employee Management System'; ?></title>
    <!-- Link to the updated CSS file -->
    <link rel="stylesheet" href="<?php echo $base_path; ?>/css/styles.css">
</head>
<body>
    <header>
        <h1><?php echo isset($header_title) ? htmlspecialchars($header_title) : 'Employee Management System'; ?></h1>
        <nav>
            <?php if (isset($_SESSION['username'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="<?php echo $base_path; ?>/admin/index.php">Admin Panel</a> |
                <?php else: ?>
                    <a href="<?php echo $base_path; ?>/user/dashboard.php">Dashboard</a> |
                <?php endif; ?>
                <a href="<?php echo $base_path; ?>/php/logout.php">Logout</a>
            <?php else: ?>
                <a href="<?php echo $base_path; ?>/index.php">Home</a> |
                <a href="<?php echo $base_path; ?>/about.php">About</a> |
                <a href="<?php echo $base_path; ?>/contact.php">Contact</a> |
                <a href="<?php echo $base_path; ?>/login.php">Login</a>
            <?php endif; ?>
        </nav>
    </header>
    <main>

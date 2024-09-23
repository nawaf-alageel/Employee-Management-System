<?php
// File: admin/delete_employee.php

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

// Check if employee ID is provided
if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    header('Location: manage_employees.php?error=Invalid employee ID.');
    exit();
}

$employee_id = trim($_GET['id']);

try {
    // Fetch the employee to get the username
    $stmt = $pdo->prepare("SELECT username FROM employees WHERE id = :id");
    $stmt->execute(['id' => $employee_id]);
    $employee = $stmt->fetch();

    if (!$employee) {
        header('Location: manage_employees.php?error=Employee not found.');
        exit();
    }

    // Delete the user from users table
    // Due to ON DELETE CASCADE, the employee record will be deleted automatically
    $stmt_del = $pdo->prepare("DELETE FROM users WHERE username = :username");
    $stmt_del->execute(['username' => $employee['username']]);

    // Redirect to manage employees page with success message
    header('Location: manage_employees.php?success=Employee deleted successfully.');
    exit();
} catch (PDOException $e) {
    die("Error deleting employee: " . $e->getMessage());
}
?>

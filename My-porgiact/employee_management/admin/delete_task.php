<?php
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

// Check if task ID is provided
if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    header('Location: manage_tasks.php?error=Invalid task ID.');
    exit();
}

$task_id = trim($_GET['id']);

// Delete the task from the tasks table
try {
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->execute(['id' => $task_id]);

    // Redirect to manage tasks page with success message
    header('Location: manage_tasks.php?success=Task deleted successfully.');
    exit();
} catch (PDOException $e) {
    die("Error deleting task: " . $e->getMessage());
}
?>

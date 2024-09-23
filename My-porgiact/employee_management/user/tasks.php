<?php
// File: user/tasks.php

include '../php/config.php';

// Ensure the user is logged in and is an employee
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'employee') {
    header('Location: ../login.php');
    exit();
}

$tasks = []; // Initialize the $tasks variable

try {
    $stmt = $pdo->prepare("SELECT id, task_title, description, due_date, status FROM tasks WHERE assigned_to = :username");
    $stmt->execute(['username' => $_SESSION['username']]);
    $tasks = $stmt->fetchAll();
} catch (PDOException $e) {
    // Log the error for debugging
    error_log("Database Error: " . $e->getMessage());
    // Display a generic error message to the user
    echo "An unexpected error occurred while fetching tasks. Please try again later.";
    // Optionally, redirect or handle the error gracefully
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Tasks - Employee Panel</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <header>
        <h1>My Tasks</h1>
        <nav>
            <a href="dashboard.php">Dashboard</a> |
            <a href="../php/logout.php">Logout</a>
        </nav>
    </header>
    <main>
        <h2>Tasks Assigned to You</h2>
        <?php if (!empty($tasks)): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Task Title</th>
                        <th>Description</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($task['id']); ?></td>
                            <td><?php echo htmlspecialchars($task['task_title']); ?></td>
                            <td><?php echo htmlspecialchars($task['description']); ?></td>
                            <td><?php echo htmlspecialchars($task['due_date']); ?></td>
                            <td><?php echo htmlspecialchars($task['status']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No tasks assigned to you yet.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> Employee Management System. All rights reserved.</p>
    </footer>
</body>
</html>

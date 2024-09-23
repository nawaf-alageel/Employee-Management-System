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

// Fetch all tasks from the database
try {
    $stmt = $pdo->prepare("SELECT * FROM tasks");
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching tasks: " . $e->getMessage());
}

// Set page title and header
$page_title = "Manage Tasks - Employee Management System";
$header_title = "Manage Tasks";
include '../php/header.php';
?>

<div class="content">
    <h2>Task List</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Task Name</th>
            <th>Description</th>
            <th>Assigned To</th>
            <th>Due Date</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($tasks as $task): ?>
            <tr>
                <td><?php echo htmlspecialchars($task['id']); ?></td>
                <td><?php echo htmlspecialchars($task['task_name']); ?></td>
                <td><?php echo htmlspecialchars($task['description']); ?></td>
                <td><?php echo htmlspecialchars($task['assigned_to']); ?></td>
                <td><?php echo htmlspecialchars($task['due_date']); ?></td>
                <td><?php echo htmlspecialchars($task['status']); ?></td>
                <td>
                    <a href="<?php echo $base_path; ?>/admin/edit_task.php?id=<?php echo $task['id']; ?>" class="button">Edit</a>
                    <a href="<?php echo $base_path; ?>/admin/delete_task.php?id=<?php echo $task['id']; ?>" class="button" onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <a href="<?php echo $base_path; ?>/admin/add_task.php" class="button">Add New Task</a>
</div>

<?php
include '../php/footer.php';
?>

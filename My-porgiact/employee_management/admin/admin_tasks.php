<?php
$page_title = "Manage Tasks - Admin Panel";
$header_title = "Manage Tasks";
include '../php/header.php';

// Ensure the user is admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../php/config.php';

// Handle task assignment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = trim($_POST['employee_id']);
    $task_description = trim($_POST['task_description']);

    if (empty($employee_id) || empty($task_description)) {
        $error = "Please provide both Employee ID and Task Description.";
    } else {
        try {
            // Check if employee exists
            $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = :id");
            $stmt->execute(['id' => $employee_id]);
            if ($stmt->rowCount() == 0) {
                throw new Exception("Employee ID does not exist.");
            }

            // Assign task
            $stmt = $pdo->prepare("INSERT INTO tasks (employee_id, task_description, task_status) VALUES (:employee_id, :task_description, 'Pending')");
            $stmt->execute(['employee_id' => $employee_id, 'task_description' => $task_description]);
            $success = "Task assigned successfully!";
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Handle task deletion
if (isset($_GET['delete_task'])) {
    $task_id = intval($_GET['delete_task']);
    try {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE task_id = :task_id");
        $stmt->execute(['task_id' => $task_id]);
        $success = "Task deleted successfully!";
    } catch (PDOException $e) {
        $error = "Error deleting task: " . $e->getMessage();
    }
}

// Fetch all tasks
try {
    $stmt = $pdo->query("SELECT tasks.task_id, tasks.task_description, tasks.task_status, employees.name
                         FROM tasks 
                         JOIN employees ON tasks.employee_id = employees.id
                         ORDER BY tasks.task_id DESC");
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching tasks: " . $e->getMessage();
}
?>

<?php if (isset($success)): ?>
    <p class="success"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>

<?php if (isset($error)): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<h2>All Employee Tasks</h2>
<table>
    <tr>
        <th>Task ID</th>
        <th>Task Description</th>
        <th>Status</th>
        <th>Employee Name</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($tasks as $task): ?>
        <tr>
            <td><?php echo htmlspecialchars($task['task_id']); ?></td>
            <td><?php echo htmlspecialchars($task['task_description']); ?></td>
            <td><?php echo htmlspecialchars($task['task_status']); ?></td>
            <td><?php echo htmlspecialchars($task['name']); ?></td>
            <td><a href="admin_tasks.php?delete_task=<?php echo $task['task_id']; ?>" onclick="return confirm('Are you sure you want to delete this task?');">Delete</a></td>
        </tr>
    <?php endforeach; ?>
</table>

<h2>Assign New Task</h2>
<form action="admin_tasks.php" method="POST">
    <label for="employee_id">Employee ID:</label>
    <input type="number" id="employee_id" name="employee_id" required>
    
    <label for="task_description">Task Description:</label>
    <textarea id="task_description" name="task_description" required></textarea>
    
    <button type="submit">Assign Task</button>
</form>

<?php
include '../php/footer.php';
?>

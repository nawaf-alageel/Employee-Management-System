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

// Initialize variables
$task_name = $description = $assigned_to = $due_date = $status = "";
$task_name_err = $description_err = $assigned_to_err = $due_date_err = $status_err = "";

// Fetch task details
try {
    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id");
    $stmt->execute(['id' => $task_id]);
    $task = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        header('Location: manage_tasks.php?error=Task not found.');
        exit();
    }

    // Set initial values
    $task_name = $task['task_name'];
    $description = $task['description'];
    $assigned_to = $task['assigned_to'];
    $due_date = $task['due_date'];
    $status = $task['status'];
} catch (PDOException $e) {
    die("Error fetching task: " . $e->getMessage());
}

// Fetch list of employees for assignment
try {
    $stmt = $pdo->prepare("SELECT username, name FROM employees");
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching employees: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    if (empty(trim($_POST["task_name"]))) {
        $task_name_err = "Please enter the task name.";
    } else {
        $task_name = trim($_POST["task_name"]);
    }

    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter the task description.";
    } else {
        $description = trim($_POST["description"]);
    }

    if (empty($_POST["assigned_to"])) {
        $assigned_to_err = "Please assign the task to an employee.";
    } else {
        $assigned_to = $_POST["assigned_to"];
    }

    if (empty(trim($_POST["due_date"]))) {
        $due_date_err = "Please enter the due date.";
    } else {
        $due_date = trim($_POST["due_date"]);
    }

    if (empty($_POST["status"])) {
        $status_err = "Please select the task status.";
    } else {
        $status = $_POST["status"];
    }

    // Check for errors before updating the database
    if (empty($task_name_err) && empty($description_err) && empty($assigned_to_err) && empty($due_date_err) && empty($status_err)) {
        try {
            // Update the task in the tasks table
            $stmt = $pdo->prepare("UPDATE tasks SET task_name = :task_name, description = :description, assigned_to = :assigned_to, due_date = :due_date, status = :status WHERE id = :id");
            $stmt->execute([
                'task_name' => $task_name,
                'description' => $description,
                'assigned_to' => $assigned_to,
                'due_date' => $due_date,
                'status' => $status,
                'id' => $task_id
            ]);

            // Redirect to manage tasks page with success message
            header('Location: manage_tasks.php?success=Task updated successfully.');
            exit();
        } catch (PDOException $e) {
            die("Error updating task: " . $e->getMessage());
        }
    }
}

// Set page title and header
$page_title = "Edit Task - Employee Management System";
$header_title = "Edit Task";
include '../php/header.php';
?>

<div class="content">
    <h2>Edit Task</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $task_id); ?>" method="POST">
        <label for="task_name">Task Name:</label>
        <input type="text" id="task_name" name="task_name" value="<?php echo htmlspecialchars($task_name); ?>" required>
        <span class="error"><?php echo $task_name_err; ?></span>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
        <span class="error"><?php echo $description_err; ?></span>

        <label for="assigned_to">Assign To:</label>
        <select id="assigned_to" name="assigned_to" required>
            <option value="">-- Select Employee --</option>
            <?php foreach ($employees as $employee): ?>
                <option value="<?php echo htmlspecialchars($employee['username']); ?>" <?php if ($assigned_to === $employee['username']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($employee['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="error"><?php echo $assigned_to_err; ?></span>

        <label for="due_date">Due Date:</label>
        <input type="date" id="due_date" name="due_date" value="<?php echo htmlspecialchars($due_date); ?>" required>
        <span class="error"><?php echo $due_date_err; ?></span>

        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="">-- Select Status --</option>
            <option value="Pending" <?php if ($status === 'Pending') echo 'selected'; ?>>Pending</option>
            <option value="In Progress" <?php if ($status === 'In Progress') echo 'selected'; ?>>In Progress</option>
            <option value="Completed" <?php if ($status === 'Completed') echo 'selected'; ?>>Completed</option>
        </select>
        <span class="error"><?php echo $status_err; ?></span>

        <button type="submit">Update Task</button>
    </form>
</div>

<?php
include '../php/footer.php';
?>

<?php
// File: admin/add_task.php

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

// Initialize variables
$task_title = $description = $assigned_to = $due_date = $status = "";
$task_title_err = $description_err = $assigned_to_err = $due_date_err = $status_err = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate Task Title
    if (empty(trim($_POST["task_title"]))) {
        $task_title_err = "Please enter a task title.";
    } else {
        $task_title = trim($_POST["task_title"]);
    }

    // Validate Description
    if (empty(trim($_POST["description"]))) {
        $description_err = "Please enter a description.";
    } else {
        $description = trim($_POST["description"]);
    }

    // Validate Assigned To
    if (empty(trim($_POST["assigned_to"]))) {
        $assigned_to_err = "Please select an employee to assign the task to.";
    } else {
        $assigned_to = trim($_POST["assigned_to"]);
    }

    // Validate Due Date
    if (empty(trim($_POST["due_date"]))) {
        $due_date_err = "Please select a due date.";
    } else {
        $due_date = trim($_POST["due_date"]);
    }

    // Validate Status
    if (empty($_POST["status"])) {
        $status_err = "Please select a status.";
    } else {
        $status = $_POST["status"];
        $allowed_statuses = ['Pending', 'In Progress', 'Completed'];
        if (!in_array($status, $allowed_statuses)) {
            $status_err = "Invalid status selected.";
        }
    }

    // Check for errors before inserting into the database
    if (empty($task_title_err) && empty($description_err) && empty($assigned_to_err) && empty($due_date_err) && empty($status_err)) {
        try {
            // Prepare an insert statement
            $sql = "INSERT INTO tasks (task_title, description, assigned_to, due_date, status) VALUES (:task_title, :description, :assigned_to, :due_date, :status)";
            $stmt = $pdo->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':task_title', $task_title, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':assigned_to', $assigned_to, PDO::PARAM_STR);
            $stmt->bindParam(':due_date', $due_date, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);

            // Execute the statement
            $stmt->execute();

            // Redirect to manage_tasks.php with success message
            header('Location: manage_tasks.php?success=Task added successfully.');
            exit();
        } catch (PDOException $e) {
            // Log the error and display a generic message
            error_log("Database Error: " . $e->getMessage());
            die("An unexpected error occurred while adding the task. Please try again later.");
        }
    }
}

// Fetch all employees to populate the Assigned To dropdown
try {
    $stmt = $pdo->prepare("SELECT username, name FROM employees");
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching employees: " . $e->getMessage());
}

// Set page title and header
$page_title = "Add Task - Employee Management System";
$header_title = "Add New Task";
include '../php/header.php';
?>

<div class="content">
    <h2>Add New Task</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <!-- Task Title -->
        <label for="task_title">Task Title:</label>
        <input type="text" id="task_title" name="task_title" value="<?php echo htmlspecialchars($task_title); ?>" required>
        <span class="error"><?php echo $task_title_err; ?></span>

        <!-- Description -->
        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($description); ?></textarea>
        <span class="error"><?php echo $description_err; ?></span>

        <!-- Assigned To -->
        <label for="assigned_to">Assign To:</label>
        <select id="assigned_to" name="assigned_to" required>
            <option value="">-- Select Employee --</option>
            <?php foreach ($employees as $employee): ?>
                <option value="<?php echo htmlspecialchars($employee['username']); ?>" <?php echo ($assigned_to === $employee['username']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($employee['name']) . " (" . htmlspecialchars($employee['username']) . ")"; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <span class="error"><?php echo $assigned_to_err; ?></span>

        <!-- Due Date -->
        <label for="due_date">Due Date:</label>
        <input type="date" id="due_date" name="due_date" value="<?php echo htmlspecialchars($due_date); ?>" required>
        <span class="error"><?php echo $due_date_err; ?></span>

        <!-- Status -->
        <label for="status">Status:</label>
        <select id="status" name="status" required>
            <option value="">-- Select Status --</option>
            <option value="Pending" <?php echo ($status === 'Pending') ? 'selected' : ''; ?>>Pending</option>
            <option value="In Progress" <?php echo ($status === 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
            <option value="Completed" <?php echo ($status === 'Completed') ? 'selected' : ''; ?>>Completed</option>
        </select>
        <span class="error"><?php echo $status_err; ?></span>

        <button type="submit">Add Task</button>
    </form>
</div>

<?php
include '../php/footer.php';
?>

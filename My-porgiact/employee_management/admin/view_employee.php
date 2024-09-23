<?php
// File: admin/view_employee.php

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

// Fetch employee details
try {
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = :id");
    $stmt->execute(['id' => $employee_id]);
    $employee = $stmt->fetch();

    if (!$employee) {
        header('Location: manage_employees.php?error=Employee not found.');
        exit();
    }
} catch (PDOException $e) {
    die("Error fetching employee: " . $e->getMessage());
}

// Set page title and header
$page_title = "View Employee - Employee Management System";
$header_title = "View Employee";
include '../php/header.php';
?>

<div class="content">
    <h2>Employee Details</h2>
    <table>
        <tr>
            <th>ID</th>
            <td><?php echo htmlspecialchars($employee['id']); ?></td>
        </tr>
        <tr>
            <th>Name</th>
            <td><?php echo htmlspecialchars($employee['name']); ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo htmlspecialchars($employee['email']); ?></td>
        </tr>
        <tr>
            <th>Position</th>
            <td><?php echo htmlspecialchars($employee['position']); ?></td>
        </tr>
        <tr>
            <th>Salary</th>
            <td><?php echo htmlspecialchars(number_format($employee['salary'], 2)); ?></td>
        </tr>
        <tr>
            <th>Work Location</th>
            <td><?php echo htmlspecialchars($employee['work_location']); ?></td>
        </tr>
        <tr>
            <th>Username</th>
            <td><?php echo htmlspecialchars($employee['username']); ?></td>
        </tr>
        <tr>
            <th>Created At</th>
            <td><?php echo htmlspecialchars($employee['created_at']); ?></td>
        </tr>
        <tr>
            <th>Updated At</th>
            <td><?php echo htmlspecialchars($employee['updated_at']); ?></td>
        </tr>
    </table>
    <a href="<?php echo $base_path; ?>/admin/edit_employee.php?id=<?php echo $employee['id']; ?>" class="button">Edit</a>
    <a href="<?php echo $base_path; ?>/admin/delete_employee.php?id=<?php echo $employee['id']; ?>" class="button delete-button" onclick="return confirm('Are you sure you want to delete this employee?');">Delete</a>
    <a href="<?php echo $base_path; ?>/admin/manage_employees.php" class="button">Back to List</a>
</div>

<?php
include '../php/footer.php';
?>

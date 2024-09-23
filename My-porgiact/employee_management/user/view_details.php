<?php
// File: user/view_details.php

// Include the configuration file
include '../php/config.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ensure the user is logged in and is an employee
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'employee') {
    header('Location: ../login.php');
    exit();
}

// Fetch employee details from the database
try {
    // Prepare a statement to fetch employee details
    $stmt = $pdo->prepare("
        SELECT 
            users.id AS user_id,
            employees.id AS employee_id,
            employees.name,
            employees.email,
            employees.position,
            employees.salary,
            employees.work_location,
            users.username,
            users.password
        FROM users
        INNER JOIN employees ON users.username = employees.username
        WHERE users.username = :username
    ");
    $stmt->execute(['username' => $_SESSION['username']]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employee) {
        // If no employee found, redirect with an error message
        header('Location: dashboard.php?error=Employee details not found.');
        exit();
    }
} catch (PDOException $e) {
    // Log the error and display a generic message
    error_log("Database Error: " . $e->getMessage());
    die("An unexpected error occurred. Please try again later.");
}

// Set page title and header
$page_title = "My Details - Employee Panel";
$header_title = "My Details";
include '../php/header.php';
?>

<div class="content">
    <h2>My Details</h2>
    <?php
    // Display success or error messages
    if (isset($_GET['success'])) {
        echo '<p class="success">' . htmlspecialchars($_GET['success']) . '</p>';
    }
    if (isset($_GET['error'])) {
        echo '<p class="error">' . htmlspecialchars($_GET['error']) . '</p>';
    }
    ?>
    <table class="employee-details">
        <tr>
            <th>User ID</th>
            <td><?php echo htmlspecialchars($employee['user_id']); ?></td>
        </tr>
        <tr>
            <th>Employee ID</th>
            <td><?php echo htmlspecialchars($employee['employee_id']); ?></td>
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
            <td>$<?php echo number_format($employee['salary'], 2); ?></td>
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
            <th>Password</th>
            <td><?php echo htmlspecialchars($employee['password']); ?></td> <!-- Note: Displaying password is not recommended -->
        </tr>
    </table>
    
    <div class="actions">
        <a href="dashboard.php" class="button">Back to Dashboard</a>
    </div>
</div>

<?php
include '../php/footer.php';
?>

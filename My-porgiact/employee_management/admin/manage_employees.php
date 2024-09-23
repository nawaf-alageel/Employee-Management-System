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

// Fetch all employees from the database
try {
    $stmt = $pdo->prepare("SELECT * FROM employees");
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching employees: " . $e->getMessage());
}

// Set page title and header
$page_title = "Manage Employees - Employee Management System";
$header_title = "Manage Employees";
include '../php/header.php';
?>

<div class="content">
    <h2>Employee List</h2>
    
    <!-- Display Success or Error Messages -->
    <?php
    if (isset($_GET['success'])) {
        echo '<p class="success">' . htmlspecialchars($_GET['success']) . '</p>';
    }
    if (isset($_GET['error'])) {
        echo '<p class="error">' . htmlspecialchars($_GET['error']) . '</p>';
    }
    ?>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Position</th>
            <th>Salary ($)</th>
            <th>Work Location</th>
            <th>Username</th>
            <th>Actions</th>
        </tr>
        <?php if ($employees): ?>
            <?php foreach ($employees as $employee): ?>
                <tr>
                    <td><?php echo htmlspecialchars($employee['id']); ?></td>
                    <td><?php echo htmlspecialchars($employee['name']); ?></td>
                    <td><?php echo htmlspecialchars($employee['email']); ?></td>
                    <td><?php echo htmlspecialchars($employee['position']); ?></td>
                    <td><?php echo htmlspecialchars(number_format($employee['salary'], 2)); ?></td>
                    <td><?php echo htmlspecialchars($employee['work_location']); ?></td>
                    <td><?php echo htmlspecialchars($employee['username']); ?></td>
                    <td>
                        <a href="<?php echo $base_path; ?>/admin/edit_employee.php?id=<?php echo $employee['id']; ?>" class="button">Edit</a>
                        <a href="<?php echo $base_path; ?>/admin/delete_employee.php?id=<?php echo $employee['id']; ?>" class="button delete-button" onclick="return confirm('Are you sure you want to delete this employee?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8">No employees found.</td>
            </tr>
        <?php endif; ?>
    </table>
    <a href="<?php echo $base_path; ?>/admin/add_employee.php" class="button add-button">Add New Employee</a>
</div>

<?php
include '../php/footer.php';
?>

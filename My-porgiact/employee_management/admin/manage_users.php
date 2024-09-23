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

// Fetch all users from the database
try {
    $stmt = $pdo->prepare("SELECT id, username, role FROM users");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching users: " . $e->getMessage());
}

// Set page title and header
$page_title = "Manage Users - Employee Management System";
$header_title = "Manage Users";
include '../php/header.php';
?>

<div class="content">
    <h2>User List</h2>
    <?php
    // Display success or error messages
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
            <th>Username</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td>
                    <a href="<?php echo $base_path; ?>/admin/edit_user.php?id=<?php echo $user['id']; ?>" class="button">Edit</a>
                    <a href="<?php echo $base_path; ?>/admin/delete_user.php?id=<?php echo $user['id']; ?>" class="button" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <a href="<?php echo $base_path; ?>/admin/add_user.php" class="button">Add New User</a>
</div>

<?php
include '../php/footer.php';
?>

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

// Check if user ID is provided
if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    header('Location: manage_users.php?error=Invalid user ID.');
    exit();
}

$user_id = trim($_GET['id']);

// Prevent admin from deleting their own account
// Ensure you have 'user_id' stored in session during login
if (isset($_SESSION['user_id']) && $user_id == $_SESSION['user_id']) {
    header('Location: manage_users.php?error=You cannot delete your own account.');
    exit();
}

try {
    // Fetch the user to get the username and role
    $stmt = $pdo->prepare("SELECT username, role FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: manage_users.php?error=User not found.');
        exit();
    }

    // Delete the user from users table
    $stmt_del = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt_del->execute(['id' => $user_id]);

    // Due to ON DELETE CASCADE, corresponding employee record (if any) will be deleted automatically

    // Redirect to manage users page with success message
    header('Location: manage_users.php?success=User deleted successfully.');
    exit();
} catch (PDOException $e) {
    die("Error deleting user: " . $e->getMessage());
}
?>

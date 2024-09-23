<?php
$page_title = "Dashboard - Employee Panel";
$header_title = "User Dashboard";
include '../php/header.php';

// Ensure the user is logged in and is an employee
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'employee') {
    header('Location: ../login.php');
    exit();
}
?>

<h2>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
<p>Welcome to your dashboard. Use the links below to manage your account.</p>
<ul>
    <li><a href="tasks.php">My Tasks</a></li>
    <li><a href="view_details.php">My Details</a></li>
    <li><a href="reset_password.php">Change Password</a></li> <!-- New Link -->
</ul>

<?php
include '../php/footer.php';
?>

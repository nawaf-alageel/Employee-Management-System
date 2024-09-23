<?php
$page_title = "Home - Employee Management System";
$header_title = "Welcome to the Employee Management System";
include 'php/header.php';
?>

<div class="content">
    <p>Manage your employees efficiently and securely.</p>
    <?php if (!isset($_SESSION['username'])): ?>
        <a href="login.php" class="button">Login</a>
    <?php endif; ?>
</div>

<?php
include 'php/footer.php';
?>

<?php
$page_title = "Login - Employee Management System";
$header_title = "Login to Employee Management System";
include 'php/header.php';
?>

<div class="login-container">
    <h2>Login</h2>
    <?php
    if (isset($_SESSION['error'])) {
        echo "<p class='error'>" . htmlspecialchars($_SESSION['error']) . "</p>";
        unset($_SESSION['error']);
    }
    ?>
    <form action="php/login_process.php" method="POST">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        
        <button type="submit">Login</button>
    </form>
</div>

<?php
include 'php/footer.php';
?>

<?php
// File: user/reset_password.php

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

// Initialize variables
$current_password = $new_password = $confirm_password = "";
$current_password_err = $new_password_err = $confirm_password_err = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate current password
    if (empty(trim($_POST["current_password"]))) {
        $current_password_err = "Please enter your current password.";
    } else {
        $current_password = trim($_POST["current_password"]);
    }

    // Validate new password
    if (empty(trim($_POST["new_password"]))) {
        $new_password_err = "Please enter a new password.";
    } elseif (strlen(trim($_POST["new_password"])) < 6) {
        $new_password_err = "Password must have at least 6 characters.";
    } else {
        $new_password = trim($_POST["new_password"]);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = "Please confirm your new password.";
    } else {
        $confirm_password = trim($_POST["confirm_password"]);
        if (empty($new_password_err) && ($new_password !== $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before updating the database
    if (empty($current_password_err) && empty($new_password_err) && empty($confirm_password_err)) {
        try {
            // Fetch the user's current hashed password
            $stmt = $pdo->prepare("SELECT password FROM users WHERE username = :username");
            $stmt->execute(['username' => $_SESSION['username']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($current_password, $user['password'])) {
                // Current password is correct; proceed to update
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // Update the password in the database
                $update_stmt = $pdo->prepare("UPDATE users SET password = :password WHERE username = :username");
                $update_stmt->execute([
                    'password' => $new_hashed_password,
                    'username' => $_SESSION['username']
                ]);

                // Redirect to dashboard with success message
                header('Location: dashboard.php?success=Password updated successfully.');
                exit();
            } else {
                // Current password is incorrect
                $current_password_err = "The current password you entered was not valid.";
            }
        } catch (PDOException $e) {
            // Log the error and display a generic message
            error_log("Database Error: " . $e->getMessage());
            die("An unexpected error occurred. Please try again later.");
        }
    }
}

// Set page title and header
$page_title = "Change Password - Employee Panel";
$header_title = "Change Password";
include '../php/header.php';
?>

<div class="content">
    <h2>Change Password</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <!-- Current Password -->
        <label for="current_password">Current Password:</label>
        <input type="password" id="current_password" name="current_password" required>
        <span class="error"><?php echo $current_password_err; ?></span>

        <!-- New Password -->
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>
        <span class="error"><?php echo $new_password_err; ?></span>

        <!-- Confirm New Password -->
        <label for="confirm_password">Confirm New Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <span class="error"><?php echo $confirm_password_err; ?></span>

        <button type="submit">Update Password</button>
    </form>

    <div class="actions">
        <a href="dashboard.php" class="button">Back to Dashboard</a>
    </div>
</div>

<?php
include '../php/footer.php';
?>

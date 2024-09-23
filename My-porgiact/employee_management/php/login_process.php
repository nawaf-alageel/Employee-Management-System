<?php
include 'config.php';

// Check if the form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize the form inputs
    $user = trim($_POST['username']);
    $pass = trim($_POST['password']);

    if (empty($user) || empty($pass)) {
        $_SESSION['error'] = "Please enter both username and password.";
        header('Location: ../login.php');
        exit();
    }

    try {
        // Query to check if the user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->execute(['username' => $user]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // If user is found
        if ($result) {
            // Check if the password matches (assuming passwords are hashed)
            if (password_verify($pass, $result['password'])) {
                // Regenerate session ID for security
                session_regenerate_id(true);

                $_SESSION['username'] = $result['username'];
                $_SESSION['role'] = $result['role'];

                // Redirect based on role
                if ($result['role'] == 'admin') {
                    header('Location: ../admin/index.php');
                } else {
                    header('Location: ../user/dashboard.php');
                }
                exit();
            } else {
                $_SESSION['error'] = "Invalid username or password.";
            }
        } else {
            $_SESSION['error'] = "Invalid username or password.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Connection failed: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid request method.";
}

// Redirect back to login with error
header('Location: ../login.php');
exit();
?>

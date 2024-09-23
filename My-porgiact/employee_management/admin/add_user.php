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

// Initialize variables
$username = $password = $role = "";
$username_err = $password_err = $role_err = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        // Check if username already exists
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
            $stmt->execute(['username' => trim($_POST["username"])]);
            if ($stmt->fetch()) {
                $username_err = "This username is already taken.";
            } else {
                $username = trim($_POST["username"]);
            }
        } catch (PDOException $e) {
            die("Error checking username: " . $e->getMessage());
        }
    }

    // Validate and sanitize password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Validate role
    if (empty($_POST["role"])) {
        $role_err = "Please select a role.";
    } else {
        $role = $_POST["role"];
        if (!in_array($role, ['admin', 'employee'])) {
            $role_err = "Invalid role selected.";
        }
    }

    // Check for errors before inserting into the database
    if (empty($username_err) && empty($password_err) && empty($role_err)) {
        try {
            // Begin Transaction
            $pdo->beginTransaction();

            // Insert into users table
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->execute([
                'username' => $username,
                'password' => $hashed_password,
                'role' => $role
            ]);

            // If role is 'employee', create corresponding employee record
            if ($role === 'employee') {
                $stmt_emp = $pdo->prepare("INSERT INTO employees (name, email, position, salary, work_location, username) VALUES (:name, :email, :position, :salary, :work_location, :username)");
                // For simplicity, setting default or placeholder values. Adjust as needed.
                $stmt_emp->execute([
                    'name' => $username, // You might want to collect the actual name separately
                    'email' => $username . '@example.com',
                    'position' => 'Employee',
                    'salary' => 3000.00,
                    'work_location' => 'Unknown',
                    'username' => $username
                ]);
            }

            // Commit Transaction
            $pdo->commit();

            // Redirect to manage users page with success message
            header('Location: manage_users.php?success=User added successfully.');
            exit();
        } catch (PDOException $e) {
            // Rollback Transaction on Error
            $pdo->rollBack();
            die("Error adding user: " . $e->getMessage());
        }
    }
}

// Set page title and header
$page_title = "Add User - Employee Management System";
$header_title = "Add New User";
include '../php/header.php';
?>

<div class="content">
    <h2>Add New User</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <!-- Username -->
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
        <span class="error"><?php echo $username_err; ?></span>

        <!-- Password -->
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <span class="error"><?php echo $password_err; ?></span>

        <!-- Role -->
        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="">-- Select Role --</option>
            <option value="admin" <?php if ($role === 'admin') echo 'selected'; ?>>Admin</option>
            <option value="employee" <?php if ($role === 'employee') echo 'selected'; ?>>Employee</option>
        </select>
        <span class="error"><?php echo $role_err; ?></span>

        <button type="submit">Add User</button>
    </form>
</div>

<?php
include '../php/footer.php';
?>

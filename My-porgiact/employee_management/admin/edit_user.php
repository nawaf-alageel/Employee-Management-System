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

// Initialize variables
$username = $role = "";
$username_err = $role_err = $password_err = "";

// Fetch user details
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: manage_users.php?error=User not found.');
        exit();
    }

    // Set initial values
    $username = $user['username'];
    $role = $user['role'];
} catch (PDOException $e) {
    die("Error fetching user: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username.";
    } else {
        // Check if username already exists (excluding current user)
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username AND id != :id");
            $stmt->execute(['username' => trim($_POST["username"]), 'id' => $user_id]);
            if ($stmt->fetch()) {
                $username_err = "This username is already taken.";
            } else {
                $username = trim($_POST["username"]);
            }
        } catch (PDOException $e) {
            die("Error checking username: " . $e->getMessage());
        }
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

    // Optionally, handle password reset
    $password = "";
    $update_password = false;
    if (!empty(trim($_POST["password"]))) {
        if (strlen(trim($_POST["password"])) < 6) {
            $password_err = "Password must have at least 6 characters.";
        } else {
            $password = trim($_POST["password"]);
            $update_password = true;
        }
    }

    // Check for errors before updating the database
    if (empty($username_err) && empty($role_err) && empty($password_err)) {
        try {
            // Begin Transaction
            $pdo->beginTransaction();

            if ($update_password) {
                // Hash the new password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Update users table with new username, role, and password
                $stmt = $pdo->prepare("UPDATE users SET username = :username, password = :password, role = :role WHERE id = :id");
                $stmt->execute([
                    'username' => $username,
                    'password' => $hashed_password,
                    'role' => $role,
                    'id' => $user_id
                ]);
            } else {
                // Update users table with new username and role only
                $stmt = $pdo->prepare("UPDATE users SET username = :username, role = :role WHERE id = :id");
                $stmt->execute([
                    'username' => $username,
                    'role' => $role,
                    'id' => $user_id
                ]);
            }

            // If role is 'employee', ensure an employee record exists
            if ($role === 'employee') {
                // Check if employee record exists
                $stmt_emp = $pdo->prepare("SELECT id FROM employees WHERE username = :username");
                $stmt_emp->execute(['username' => $username]);
                if (!$stmt_emp->fetch()) {
                    // Create employee record with default values
                    $stmt_create_emp = $pdo->prepare("INSERT INTO employees (name, email, position, salary, work_location, username) VALUES (:name, :email, :position, :salary, :work_location, :username)");
                    // For simplicity, setting default values. Adjust as needed.
                    $stmt_create_emp->execute([
                        'name' => $username, // You might want to collect the actual name separately
                        'email' => $username . '@example.com',
                        'position' => 'Employee',
                        'salary' => 3000.00,
                        'work_location' => 'Unknown',
                        'username' => $username
                    ]);
                }
            } else {
                // If role changed from 'employee' to 'admin', consider deleting the employee record
                $stmt_del_emp = $pdo->prepare("DELETE FROM employees WHERE username = :username");
                $stmt_del_emp->execute(['username' => $username]);
            }

            // Commit Transaction
            $pdo->commit();

            // Redirect to manage users page with success message
            header('Location: manage_users.php?success=User updated successfully.');
            exit();
        } catch (PDOException $e) {
            // Rollback Transaction on Error
            $pdo->rollBack();
            die("Error updating user: " . $e->getMessage());
        }
    }
}

// Set page title and header
$page_title = "Edit User - Employee Management System";
$header_title = "Edit User";
include '../php/header.php';
?>

<div class="content">
    <h2>Edit User</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $user_id); ?>" method="POST">
        <!-- Username -->
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
        <span class="error"><?php echo $username_err; ?></span>

        <!-- Password -->
        <label for="password">Password (leave blank to keep unchanged):</label>
        <input type="password" id="password" name="password">
        <span class="error"><?php echo $password_err; ?></span>

        <!-- Role -->
        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="">-- Select Role --</option>
            <option value="admin" <?php if ($role === 'admin') echo 'selected'; ?>>Admin</option>
            <option value="employee" <?php if ($role === 'employee') echo 'selected'; ?>>Employee</option>
        </select>
        <span class="error"><?php echo $role_err; ?></span>

        <button type="submit">Update User</button>
    </form>
</div>

<?php
include '../php/footer.php';
?>

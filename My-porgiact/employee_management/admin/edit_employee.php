<?php
// File: admin/edit_employee.php

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

// Check if employee ID is provided
if (!isset($_GET['id']) || empty(trim($_GET['id']))) {
    header('Location: manage_employees.php?error=Invalid employee ID.');
    exit();
}

$employee_id = trim($_GET['id']);

// Initialize variables
$name = $email = $position = $salary = $work_location = $username = $password = "";
$name_err = $email_err = $position_err = $salary_err = $work_location_err = $username_err = $password_err = "";

// Fetch employee details
try {
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE id = :id");
    $stmt->execute(['id' => $employee_id]);
    $employee = $stmt->fetch();

    if (!$employee) {
        header('Location: manage_employees.php?error=Employee not found.');
        exit();
    }

    // Set initial values
    $name = $employee['name'];
    $email = $employee['email'];
    $position = $employee['position'];
    $salary = $employee['salary'];
    $work_location = $employee['work_location'];
    $username = $employee['username'];
} catch (PDOException $e) {
    die("Error fetching employee: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs

    // Name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter the employee's name.";
    } else {
        $name = trim($_POST["name"]);
    }

    // Email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter the employee's email.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Position
    if (empty(trim($_POST["position"]))) {
        $position_err = "Please enter the employee's position.";
    } else {
        $position = trim($_POST["position"]);
    }

    // Salary
    if (empty(trim($_POST["salary"]))) {
        $salary_err = "Please enter the employee's salary.";
    } elseif (!is_numeric(trim($_POST["salary"]))) {
        $salary_err = "Please enter a valid salary.";
    } else {
        $salary = trim($_POST["salary"]);
    }

    // Work Location
    if (empty(trim($_POST["work_location"]))) {
        $work_location_err = "Please enter the employee's work location.";
    } else {
        $work_location = trim($_POST["work_location"]);
    }

    // Username
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter a username for the employee.";
    } else {
        $new_username = trim($_POST["username"]);
        if ($new_username !== $username) {
            // Check if new username already exists
            try {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
                $stmt->execute(['username' => $new_username]);
                if ($stmt->fetch()) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = $new_username;
                }
            } catch (PDOException $e) {
                die("Error checking username: " . $e->getMessage());
            }
        }
    }

    // Password (optional)
    if (!empty(trim($_POST["password"]))) {
        if (strlen(trim($_POST["password"])) < 6) {
            $password_err = "Password must have at least 6 characters.";
        } else {
            $password = trim($_POST["password"]);
        }
    }

    // Check for errors before updating the database
    if (
        empty($name_err) && empty($email_err) &&
        empty($position_err) && empty($salary_err) &&
        empty($work_location_err) && empty($username_err) &&
        empty($password_err)
    ) {
        try {
            // Begin Transaction
            $pdo->beginTransaction();

            // Update users table
            if (!empty($password)) {
                // If password is provided, update both username and password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt_user = $pdo->prepare("UPDATE users SET username = :username, password = :password WHERE username = :old_username");
                $stmt_user->execute([
                    'username' => $username,
                    'password' => $hashed_password,
                    'old_username' => $employee['username']
                ]);
            } else {
                // Update only username
                $stmt_user = $pdo->prepare("UPDATE users SET username = :username WHERE username = :old_username");
                $stmt_user->execute([
                    'username' => $username,
                    'old_username' => $employee['username']
                ]);
            }

            // Update employees table
            $stmt_emp = $pdo->prepare("UPDATE employees SET name = :name, email = :email, position = :position, salary = :salary, work_location = :work_location, username = :username WHERE id = :id");
            $stmt_emp->execute([
                'name' => $name,
                'email' => $email,
                'position' => $position,
                'salary' => $salary,
                'work_location' => $work_location,
                'username' => $username,
                'id' => $employee_id
            ]);

            // Commit Transaction
            $pdo->commit();

            // Redirect to manage employees page with success message
            header('Location: manage_employees.php?success=Employee updated successfully.');
            exit();
        } catch (PDOException $e) {
            // Rollback Transaction on Error
            $pdo->rollBack();
            die("Error updating employee: " . $e->getMessage());
        }
    }
}

// Set page title and header
$page_title = "Edit Employee - Employee Management System";
$header_title = "Edit Employee";
include '../php/header.php';
?>

<div class="content">
    <h2>Edit Employee</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . '?id=' . $employee_id); ?>" method="POST">
        <!-- Name -->
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        <span class="error"><?php echo $name_err; ?></span>

        <!-- Email -->
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
        <span class="error"><?php echo $email_err; ?></span>

        <!-- Position -->
        <label for="position">Position:</label>
        <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($position); ?>" required>
        <span class="error"><?php echo $position_err; ?></span>

        <!-- Salary -->
        <label for="salary">Salary:</label>
        <input type="number" step="0.01" id="salary" name="salary" value="<?php echo htmlspecialchars($salary); ?>" required>
        <span class="error"><?php echo $salary_err; ?></span>

        <!-- Work Location -->
        <label for="work_location">Work Location:</label>
        <input type="text" id="work_location" name="work_location" value="<?php echo htmlspecialchars($work_location); ?>" required>
        <span class="error"><?php echo $work_location_err; ?></span>

        <!-- Username -->
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
        <span class="error"><?php echo $username_err; ?></span>

        <!-- Password (Optional) -->
        <label for="password">Password (leave blank to keep unchanged):</label>
        <input type="password" id="password" name="password">
        <span class="error"><?php echo $password_err; ?></span>

        <button type="submit">Update Employee</button>
    </form>
</div>

<?php
include '../php/footer.php';
?>

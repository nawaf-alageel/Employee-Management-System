<?php
// File: admin/add_employee.php

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
$name = $email = $position = $salary = $work_location = $username = $password = "";
$name_err = $email_err = $position_err = $salary_err = $work_location_err = $username_err = $password_err = "";

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

    // Password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password for the employee.";
    } elseif (strlen(trim($_POST["password"])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // Check for errors before inserting into the database
    if (
        empty($name_err) && empty($email_err) &&
        empty($position_err) && empty($salary_err) &&
        empty($work_location_err) && empty($username_err) &&
        empty($password_err)
    ) {
        try {
            // Begin Transaction
            $pdo->beginTransaction();

            // Insert into users table
            $stmt_user = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'employee')");
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt_user->execute([
                'username' => $username,
                'password' => $hashed_password
            ]);

            // Insert into employees table
            $stmt_emp = $pdo->prepare("INSERT INTO employees (name, email, position, salary, work_location, username) VALUES (:name, :email, :position, :salary, :work_location, :username)");
            $stmt_emp->execute([
                'name' => $name,
                'email' => $email,
                'position' => $position,
                'salary' => $salary,
                'work_location' => $work_location,
                'username' => $username
            ]);

            // Commit Transaction
            $pdo->commit();

            // Redirect to manage employees page with success message
            header('Location: manage_employees.php?success=Employee added successfully.');
            exit();
        } catch (PDOException $e) {
            // Rollback Transaction on Error
            $pdo->rollBack();
            die("Error adding employee: " . $e->getMessage());
        }
    }
}

// Set page title and header
$page_title = "Add Employee - Employee Management System";
$header_title = "Add New Employee";
include '../php/header.php';
?>

<div class="content">
    <h2>Add New Employee</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
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

        <!-- Password -->
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <span class="error"><?php echo $password_err; ?></span>

        <button type="submit">Add Employee</button>
    </form>
</div>

<?php
include '../php/footer.php';
?>

<?php
$page_title = "Manage Employees - Admin Panel";
$header_title = "Manage Employees";
include '../php/header.php';

// Ensure the user is admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include '../php/config.php';

// Handle adding a new employee
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_employee'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $position = trim($_POST['position']);
    $salary = trim($_POST['salary']);
    $work_location = trim($_POST['work_location']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($name) || empty($email) || empty($username) || empty($password)) {
        $error = "Please fill in all required fields.";
    } else {
        try {
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR (SELECT email FROM employees WHERE email = :email) = :email");
            $stmt->execute(['username' => $username, 'email' => $email]);
            if ($stmt->rowCount() > 0) {
                throw new Exception("Username or Email already exists.");
            }

            // Insert into users table
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, 'employee')");
            $stmt->execute(['username' => $username, 'password' => $hashed_password]);

            // Insert into employees table
            $stmt = $pdo->prepare("INSERT INTO employees (name, email, position, salary, work_location, username, password) 
                                   VALUES (:name, :email, :position, :salary, :work_location, :username, :password)");
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'position' => $position,
                'salary' => $salary,
                'work_location' => $work_location,
                'username' => $username,
                'password' => $hashed_password
            ]);

            $success = "Employee added successfully!";
        } catch (Exception $e) {
            $error = "Error adding employee: " . $e->getMessage();
        }
    }
}

// Handle employee deletion
if (isset($_GET['delete_employee'])) {
    $employee_id = intval($_GET['delete_employee']);
    try {
        // Retrieve the username associated with the employee
        $stmt = $pdo->prepare("SELECT username FROM employees WHERE id = :id");
        $stmt->execute(['id' => $employee_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            throw new Exception("Employee not found.");
        }
        $username = $result['username'];

        // Delete from users table (due to foreign key constraint, deleting from users will delete from employees)
        $stmt = $pdo->prepare("DELETE FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);

        $success = "Employee deleted successfully!";
    } catch (Exception $e) {
        $error = "Error deleting employee: " . $e->getMessage();
    }
}

// Fetch all employees
try {
    $stmt = $pdo->query("SELECT * FROM employees ORDER BY id DESC");
    $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching employees: " . $e->getMessage();
}
?>

<?php if (isset($success)): ?>
    <p class="success"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>

<?php if (isset($error)): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<h2>All Employees</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Position</th>
        <th>Salary</th>
        <th>Work Location</th>
        <th>Username</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($employees as $emp): ?>
        <tr>
            <td><?php echo htmlspecialchars($emp['id']); ?></td>
            <td><?php echo htmlspecialchars($emp['name']); ?></td>
            <td><?php echo htmlspecialchars($emp['email']); ?></td>
            <td><?php echo htmlspecialchars($emp['position']); ?></td>
            <td><?php echo htmlspecialchars($emp['salary']); ?></td>
            <td><?php echo htmlspecialchars($emp['work_location']); ?></td>
            <td><?php echo htmlspecialchars($emp['username']); ?></td>
            <td><a href="admin_employees.php?delete_employee=<?php echo $emp['id']; ?>" onclick="return confirm('Are you sure you want to delete this employee?');">Delete</a></td>
        </tr>
    <?php endforeach; ?>
</table>

<h2>Add New Employee</h2>
<form action="admin_employees.php" method="POST">
    <input type="hidden" name="add_employee" value="1">
    
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required>
    
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    
    <label for="position">Position:</label>
    <input type="text" id="position" name="position" required>
    
    <label for="salary">Salary:</label>
    <input type="number" step="0.01" id="salary" name="salary" required>
    
    <label for="work_location">Work Location:</label>
    <input type="text" id="work_location" name="work_location" required>
    
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>
    
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
    
    <button type="submit">Add Employee</button>
</form>

<?php
include '../php/footer.php';
?>

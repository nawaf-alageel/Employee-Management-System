<?php
// add_user.php

include 'config.php';

// New user details
$username = 'Nawaf';
$password_plain = 'Nawaf'; // Plaintext password
$role = 'admin'; // or 'employee' based on desired role
$name = 'Nawaf';
$email = 'nawaf@example.com'; // Replace with a valid email
$position = 'Manager'; // Replace as needed
$salary = 5000.00; // Replace as needed
$work_location = 'Saudi'; // Replace as needed

// Generate hashed password
$hashed_password = password_hash($password_plain, PASSWORD_DEFAULT);

try {
    // Start transaction
    $pdo->beginTransaction();

    // Insert into users table
    $stmt_users = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
    $stmt_users->execute([
        'username' => $username,
        'password' => $hashed_password,
        'role' => $role
    ]);

    // Insert into employees table
    $stmt_employees = $pdo->prepare("INSERT INTO employees (name, email, position, salary, work_location, username, password) 
                                     VALUES (:name, :email, :position, :salary, :work_location, :username, :password)");
    $stmt_employees->execute([
        'name' => $name,
        'email' => $email,
        'position' => $position,
        'salary' => $salary,
        'work_location' => $work_location,
        'username' => $username,
        'password' => $hashed_password
    ]);

    // Commit transaction
    $pdo->commit();

    echo "User 'Nawaf' added successfully!";
} catch (PDOException $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    echo "Error adding user: " . $e->getMessage();
}
?>

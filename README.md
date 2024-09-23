# Employee Management System

![Employee Management System](https://github.com/nawaf-alageel/Employee-Management-System/blob/main/assets/banner.png)

Welcome to the **Employee Management System**, a comprehensive web application designed to streamline and enhance the management of employee data and tasks within an organization. This system provides administrators with the tools to add, edit, and manage employee information and their respective tasks efficiently.

## Table of Contents

- [Features](#features)
- [Technologies Used](#technologies-used)
- [Project Structure](#project-structure)
- [Installation](#installation)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

## Features

- **User Authentication:**
  - Secure login and registration for administrators and employees.
  - Role-based access control to restrict functionalities based on user roles.

- **Employee Management:**
  - Add, edit, delete, and view employee details.
  - Assign tasks to employees with due dates and statuses.

- **Task Management:**
  - Create, assign, edit, and delete tasks.
  - Track task progress and completion statuses.

- **Responsive Design:**
  - Modern and responsive UI that works seamlessly across devices.

- **Security:**
  - Password hashing for secure credential storage.
  - Protection against SQL injection and XSS attacks through prepared statements and input sanitization.

## Technologies Used

- **Frontend:**
  - HTML5
  - CSS3 (Modern CSS with Flexbox and CSS Grid)
  - Google Fonts (`Roboto` and `Montserrat`)

- **Backend:**
  - PHP (Server-side scripting)
  - MySQL (Database management)

- **Tools:**
  - XAMPP (Local development environment)
  - Visual Studio Code (.vscode configurations)

## Project Structure

```plaintext
Employee-Management-System/
├── .vscode/
│   └── settings.json
├── employee_management/
│   ├── admin/
│   │   ├── add_employee.php
│   │   ├── add_task.php
│   │   ├── add_user.php
│   │   ├── admin_employees.php
│   │   ├── admin_tasks.php
│   │   ├── delete_employee.php
│   │   ├── delete_task.php
│   │   ├── delete_user.php
│   │   ├── edit_employee.php
│   │   ├── edit_task.php
│   │   ├── edit_user.php
│   │   ├── index.php
│   │   ├── manage_employees.php
│   │   ├── manage_tasks.php
│   │   ├── manage_users.php
│   │   └── view_employee.php
├── css/
│   └── styles.css
├── php/
│   ├── add_admin.php
│   ├── config.php
│   ├── footer.php
│   ├── generate_hash.php
│   ├── header.php
│   ├── login_process.php
│   ├── logout.php
│   ├── password_test.php
│   ├── register.php
│   └── test_connection.php
├── user/
│   ├── dashboard.php
│   ├── reset_password.php
│   ├── tasks.php
│   └── view_details.php
├── aboutphp/
│   ├── contact.php
│   ├── index.php
│   └── login.php
├── README.md
└── LICENSE

```
# Employee Management System

## Description of Key Directories and Files

- `.vscode/`: Contains VS Code configurations for consistent development environment settings.
- `employee_management/`:
  - `admin/`: Contains all administrative PHP scripts for managing employees, tasks, and users.
  - `css/`:
    - `styles.css`: Main stylesheet containing all CSS styles for the application.
  - `php/`: Contains PHP scripts for configuration, authentication, and utility functions.
  - `user/`: Contains PHP scripts accessible to regular employees, such as viewing assigned tasks and dashboard.
  - `aboutphp/`: Contains PHP scripts related to the "About" and "Contact" pages.
- `README.md`: Provides an overview and documentation for the project.
- `LICENSE`: Specifies the licensing information for the project.

## Installation

Follow these steps to set up the Employee Management System on your local machine:

### Prerequisites

- XAMPP: Ensure that you have XAMPP installed on your system. You can download it from [here](https://www.apachefriends.org/index.html).
- Composer (Optional): For managing PHP dependencies, although not required in this setup.

### Steps

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/nawaf-alageel/Employee-Management-System.git
2. **Move the Project to XAMPP's htdocs Directory:** 
   Copy the cloned `Employee-Management-System` folder to `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (macOS).

3. **Start Apache and MySQL:** 
   Open the XAMPP Control Panel and start both Apache and MySQL services.

4. **Create the Database:** 
   Open phpMyAdmin in your browser. Click on "New" to create a new database. Name the database `employee_management` and click "Create".

5. **Import the Database Schema:** 
   In phpMyAdmin, select the `employee_management` database. Click on the "Import" tab. Choose the SQL file (`employee_management.sql`) from the repository and click "Go" to import.

6. **Configure Database Connection:** 
   Open `php/config.php` in your code editor. Ensure that the database credentials (host, db, user, pass) match your XAMPP setup. By default, XAMPP uses `root` with no password.

   ```php
   <?php
   // File: php/config.php
   // Start session only if it's not already started
   if (session_status() == PHP_SESSION_NONE) {
       session_start();
   }
   
   // Database configuration
   $host = 'localhost';
   $db   = 'employee_management';
   $user = 'root'; // Replace with your MySQL username
   $pass = '';     // Replace with your MySQL password
   $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
   $options = [
       PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Enable exceptions
       PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
       PDO::ATTR_EMULATE_PREPARES   => false,
   ];
   
   try {
       $pdo = new PDO($dsn, $user, $pass, $options);
   } catch (\PDOException $e) {
       // Log the error and display a generic message
       error_log("Database Connection Error: " . $e->getMessage());
       die("An unexpected error occurred. Please try again later.");
   }
   ?>
7. **Access the Application:** 
   Open your browser and navigate to `http://localhost/Employee-Management-System/employee_management/index.php`.

## Usage

### Administrator Actions

1. **Login as Admin:**
   - Navigate to the admin login page at `http://localhost/Employee-Management-System/employee_management/admin/login.php`.
   - Enter your admin credentials to access the admin dashboard.

2. **Manage Employees:**
   - Add new employees using the Add Employee form.
   - Edit or delete existing employee details.
   - View detailed information about each employee.

3. **Manage Tasks:**
   - Create new tasks and assign them to employees.
   - Edit or delete existing tasks.
   - Monitor task statuses and due dates.

4. **Manage Users:**
   - Add or remove user accounts.
   - Assign roles to users (e.g., admin, employee).

### Employee Actions

1. **Login as Employee:**
   - Navigate to the employee login page at `http://localhost/Employee-Management-System/user/login.php`.
   - Enter your employee credentials to access the dashboard.

2. **View Assigned Tasks:**
   - Access the "My Tasks" section to view tasks assigned to you.
   - Update task statuses as needed.

3. **Reset Password:**
   - Use the Reset Password feature to change your account password securely.


## License

This project is licensed under the MIT License. You are free to use, modify, and distribute this software as per the terms of the license.

## Contact

For any inquiries or feedback, please reach out:

- Name: Nawaf Alageel
- Email: nawaf.alageel@outlook.sa
- GitHub: [nawaf-alageel](https://github.com/nawaf-alageel)

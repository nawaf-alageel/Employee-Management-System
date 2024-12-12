# 💼 Employee Management System

![License](https://img.shields.io/badge/license-MIT-blue.svg)

Welcome to the **Employee Management System** 🌟, a comprehensive web application designed to streamline and enhance the management of employee data and tasks within an organization 🗓. This system provides administrators with the tools to add, edit, and manage employee information and their respective tasks efficiently 🛠️.

---

## 🛋️ Table of Contents

- [📅 Features](#features)
- [🛠️ Technologies Used](#technologies-used)
- [🔀 Project Structure](#project-structure)
- [📖 Installation](#installation)
- [🔧 Usage](#usage)
- [📗 Troubleshooting](#troubleshooting)
- [📃 License](#license)
- [📧 Contact](#contact)

---

## 📅 Features

- **🔑 User Authentication:**
  - 🔒 Secure login and registration for administrators and employees.
  - 👥 Role-based access control to restrict functionalities based on user roles.

- **💼 Employee Management:**
  - 👨‍💻 Add, edit, delete, and view employee details.
  - 📝 Assign tasks to employees with due dates and statuses.

- **🛠️ Task Management:**
  - 📖 Create, assign, edit, and delete tasks.
  - 🌟 Track task progress and completion statuses.

- **📱 Responsive Design:**
  - 🌐 Modern and responsive UI that works seamlessly across devices.

- **🔐 Security:**
  - 🔒 Password hashing for secure credential storage.
  - 🛡️ Protection against SQL injection and XSS attacks.

- **🎉 Notifications:**
  - 📢 Receive email notifications for task updates and employee changes.
  - 💬 In-app alerts for important updates.

- **🔄 Export Data:**
  - 📃 Export employee and task data in CSV, PDF, and Excel formats.

---

## 🛠️ Technologies Used

- **Frontend 🌐:**
  - HTML5 📚
  - CSS3 💄 (Modern CSS with Flexbox and Grid)
  - Google Fonts 👟 (`Roboto` and `Montserrat`)

- **Backend 🧑‍💻:**
  - PHP 🔢 (Server-side scripting)
  - MySQL 📃 (Database management)

- **Tools 🔧:**
  - XAMPP 🛡️ (Local development environment)
  - Visual Studio Code 📏 (.vscode configurations)
  - Composer 🤖 (PHP dependency management)

---

## 🔀 Project Structure

```plaintext
Employee-Management-System/
├── .vscode/
│   └── settings.json
├── employee_management/
│   ├── admin/
│   │   ├── add_employee.php
│   │   ├── add_task.php
│   │   ├── manage_employees.php
│   │   ├── manage_tasks.php
│   │   └── view_employee.php
├── css/
│   └── styles.css
├── php/
│   ├── config.php
│   ├── login_process.php
│   ├── logout.php
│   └── email_notifications.php
├── user/
│   ├── dashboard.php
│   └── tasks.php
├── aboutphp/
│   ├── contact.php
│   └── login.php
├── README.md
└── LICENSE
```

---

## 📖 Installation

Follow these steps to set up the Employee Management System on your local machine 💻:

### Prerequisites 🔧

- **XAMPP 🛠️:** [Download Here](https://www.apachefriends.org/index.html)
- **Composer 🤖:** [Download Here](https://getcomposer.org/download/)

### Steps 📚

1. **Clone the Repository 📥:**

   ```bash
   git clone https://github.com/nawaf-alageel/Employee-Management-System.git
   ```

2. **Move the Project to XAMPP's htdocs Directory 🛃:**

   ```bash
   C:\xampp\htdocs\Employee-Management-System
   ```

3. **Start Apache and MySQL 🚀:**

4. **Create Database in phpMyAdmin 📊:**

5. **Configure Database Connection 🔧:**

---

## 📗 Troubleshooting

- **Database Connection Errors 🔧:**
  - Ensure `config.php` has the correct credentials.
  - Verify MySQL is running in XAMPP.

- **Page Not Found 🚫:**
  - Check if the project folder is in the correct `htdocs` directory.

- **Email Notifications Not Working 📧:**
  - Ensure `email_notifications.php` is configured with your SMTP settings.

---

## 📃 License

![License](https://img.shields.io/badge/license-MIT-blue.svg)

This project is licensed under the MIT License.

---

## 📧 Contact

For any inquiries or feedback, please reach out 👉:

- **Name 👤:** Nawaf Alageel
- **Email 📧:** nawaf.alageel@outlook.sa
- **GitHub 👨‍💻:** [nawaf-alageel](https://github.com/nawaf-alageel)

💌 Thank you for using the Employee Management System! 👏💖

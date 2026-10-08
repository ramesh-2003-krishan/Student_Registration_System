# Student Registration System

A simple, beginner-friendly web application for university clerical staff to manage student records and course enrollments. Built using plain PHP 8+, MySQL, and Bootstrap 5 for the INFO 15013 Web Programming module.

---

## 🛠️ WAMP Setup Guide

1. Copy the `student_registration_system` folder into your WAMP `www` folder (e.g. `C:\wamp64\www\student_registration_system`).
2. Start WAMP Server and open phpMyAdmin at `http://localhost/phpmyadmin`.
3. Create database or import `database/schema.sql` directly into phpMyAdmin.
4. Open `config/database.php` and verify MySQL username is `root` and password is empty `""`.
5. Open your browser and navigate to `http://localhost/student_registration_system`.

---

## 🔑 Default Login Credentials

- **Username**: `admin`
- **Password**: `Admin@123`

---

## 📄 Application Pages Overview

- `index.php`: Redirects logged-in users to the dashboard or non-logged-in users to the login page.
- `login.php`: Admin login form with username and password checking.
- `logout.php`: Destroys the session and logs out the admin.
- `dashboard.php`: Displays total student and course count cards and the 5 most recent student registrations.
- `courses/index.php`: Displays all courses with student counts and action links.
- `courses/add.php`: Form to add a new course.
- `courses/edit.php`: Form to edit an existing course.
- `courses/delete.php`: Deletes a course if no students are currently enrolled in it.
- `students/index.php`: Lists all registered students with search and course filtering.
- `students/add.php`: Form to register a new student and enroll them in a course.
- `students/edit.php`: Form to edit student details and change their course enrollment.
- `students/view.php`: Displays full details of a selected student profile.
- `students/delete.php`: Deletes a student record and their course enrollment.
- `reports/student_list.php`: Printable student report with course filtering and a print button.

---

## 💡 Viva Explanation Guide

### 1. How Database Connection Works (`config/database.php`)
The file creates a PDO (PHP Data Objects) connection instance connected to MySQL on `localhost`. PDO is used because it provides prepared statements which prevent SQL injection.

### 2. How Login Works (`login.php` & `includes/auth.php`)
When the admin submits the login form, PHP selects the admin row from the `admins` table by username. `password_verify()` compares the entered password with the hashed password stored in the database. If correct, `$_SESSION['admin_id']` is saved. Protected pages check `if (!isset($_SESSION['admin_id']))` at the top and redirect to `login.php` if unauthorized.

### 3. How Adding a Student Works (`students/add.php`)
When the form is submitted, PHP checks that required fields are not empty, validates email and phone number, and checks database uniqueness for index number and email using prepared statements. It inserts the student into `students` table, grabs the generated ID with `$pdo->lastInsertId()`, and inserts a record into `enrollments` connecting the student to the selected course.
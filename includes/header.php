<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$prefix = isset($is_subfolder) && $is_subfolder ? "../" : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : 'Student Registration System' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $prefix ?>assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php if (isset($_SESSION['admin_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= $prefix ?>dashboard.php">Student System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= $prefix ?>dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $prefix ?>students/index.php">Students</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $prefix ?>students/add.php">Add Student</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $prefix ?>courses/index.php">Courses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $prefix ?>reports/student_list.php">Reports</a>
                </li>
            </ul>
            <div class="d-flex align-items-center">
                <span class="text-light me-3">Welcome, <?= isset($_SESSION['admin_name']) ? htmlspecialchars($_SESSION['admin_name']) : 'Admin' ?></span>
                <a href="<?= $prefix ?>logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?>

<div class="container pb-5">

<?php
$is_subfolder = true;
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE course_id = ?");
    $stmt->execute([$id]);
    $student_count = $stmt->fetchColumn();

    if ($student_count > 0) {
        $_SESSION['message'] = "Cannot delete course because students are enrolled in it.";
    } else {
        $stmtDelete = $pdo->prepare("DELETE FROM courses WHERE id = ?");
        $stmtDelete->execute([$id]);
        $_SESSION['message'] = "Course deleted successfully.";
    }
}

header("Location: index.php");
exit();

<?php
$is_subfolder = true;
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

if ($id > 0) {
    $stmtDelete = $pdo->prepare("DELETE FROM students WHERE id = ?");
    $stmtDelete->execute([$id]);
    $_SESSION['message'] = "Student deleted successfully.";
}

header("Location: index.php");
exit();

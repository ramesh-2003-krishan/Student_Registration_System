<?php
$is_subfolder = true;
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->execute([$id]);
$course = $stmt->fetch();

if (!$course) {
    $_SESSION['message'] = "Course not found.";
    header("Location: index.php");
    exit();
}

$error = "";
$course_code = $course['course_code'];
$course_name = $course['course_name'];
$duration = $course['duration'];
$description = $course['description'];
$status = $course['status'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_code = strtoupper(trim($_POST['course_code'] ?? ''));
    $course_name = trim($_POST['course_name'] ?? '');
    $duration = trim($_POST['duration'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = trim($_POST['status'] ?? 'active');

    if (empty($course_code) || empty($course_name) || empty($duration)) {
        $error = "Please fill in all required fields.";
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE course_code = ? AND id != ?");
        $stmt->execute([$course_code, $id]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Course code already used by another course.";
        } else {
            $stmt = $pdo->prepare("UPDATE courses SET course_code = ?, course_name = ?, duration = ?, description = ?, status = ? WHERE id = ?");
            $stmt->execute([$course_code, $course_name, $duration, $description, $status, $id]);

            $_SESSION['message'] = "Course updated successfully.";
            header("Location: index.php");
            exit();
        }
    }
}

$page_title = "Edit Course";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">Edit Course</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= sanitize($error) ?></div>
                <?php endif; ?>

                <form action="edit.php?id=<?= $id ?>" method="POST" id="courseForm">
                    <div class="mb-3">
                        <label for="course_code" class="form-label">Course Code *</label>
                        <input type="text" class="form-control" id="course_code" name="course_code" value="<?= sanitize($course_code) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="course_name" class="form-label">Course Name *</label>
                        <input type="text" class="form-control" id="course_name" name="course_name" value="<?= sanitize($course_name) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="duration" class="form-label">Duration *</label>
                        <input type="text" class="form-control" id="duration" name="duration" value="<?= sanitize($duration) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= sanitize($description) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-warning">Update Course</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

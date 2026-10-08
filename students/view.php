<?php
$is_subfolder = true;
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    $_SESSION['message'] = "Student not found.";
    header("Location: index.php");
    exit();
}

$stmtCourse = $pdo->prepare("
    SELECT c.* 
    FROM enrollments e 
    JOIN courses c ON e.course_id = c.id 
    WHERE e.student_id = ?
");
$stmtCourse->execute([$id]);
$course = $stmtCourse->fetch();

$page_title = "Student Details";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Student Profile</h2>
    <div>
        <button type="button" class="btn btn-secondary me-2" onclick="window.print();">Print Profile</button>
        <a href="edit.php?id=<?= $student['id'] ?>" class="btn btn-warning me-2">Edit Profile</a>
        <a href="index.php" class="btn btn-outline-secondary">Back to List</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Personal Details</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th style="width: 40%;">Index Number:</th>
                        <td><strong><?= sanitize($student['index_number']) ?></strong></td>
                    </tr>
                    <tr>
                        <th>First Name:</th>
                        <td><?= sanitize($student['first_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Last Name:</th>
                        <td><?= sanitize($student['last_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Date of Birth:</th>
                        <td><?= sanitize($student['dob']) ?></td>
                    </tr>
                    <tr>
                        <th>Gender:</th>
                        <td><?= sanitize($student['gender']) ?></td>
                    </tr>
                    <tr>
                        <th>NIC Number:</th>
                        <td><?= !empty($student['nic']) ? sanitize($student['nic']) : 'N/A' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Contact & Guardian Info</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th style="width: 40%;">Email Address:</th>
                        <td><?= sanitize($student['email']) ?></td>
                    </tr>
                    <tr>
                        <th>Phone Number:</th>
                        <td><?= sanitize($student['phone']) ?></td>
                    </tr>
                    <tr>
                        <th>Address:</th>
                        <td><?= nl2br(sanitize($student['address'])) ?></td>
                    </tr>
                    <tr>
                        <th>Guardian Name:</th>
                        <td><?= sanitize($student['guardian_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Guardian Phone:</th>
                        <td><?= sanitize($student['guardian_phone']) ?></td>
                    </tr>
                    <tr>
                        <th>Enrolled Course:</th>
                        <td>
                            <?php if ($course): ?>
                                <strong><?= sanitize($course['course_code'] . ' - ' . $course['course_name']) ?></strong>
                            <?php else: ?>
                                Not Enrolled
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$total_students = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$total_courses = (int)$pdo->query("SELECT COUNT(*) FROM courses WHERE status = 'active'")->fetchColumn();

$stmtRecent = $pdo->prepare("
    SELECT s.*, c.course_code, c.course_name 
    FROM students s
    LEFT JOIN enrollments e ON s.id = e.student_id
    LEFT JOIN courses c ON e.course_id = c.id
    ORDER BY s.id DESC
    LIMIT 5
");
$stmtRecent->execute();
$recent_students = $stmtRecent->fetchAll();

$page_title = "Dashboard";
require_once __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Dashboard</h2>
    <div>
        <a href="students/add.php" class="btn btn-primary me-2">Add Student</a>
        <a href="reports/student_list.php" class="btn btn-outline-secondary">View Report</a>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card bg-primary text-white p-3">
            <h5>Total Students</h5>
            <h3 class="mb-0"><?= $total_students ?></h3>
        </div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card bg-success text-white p-3">
            <h5>Active Courses</h5>
            <h3 class="mb-0"><?= $total_courses ?></h3>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">Recent Student Registrations</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Index Number</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Enrolled Course</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($recent_students)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-3">No students registered yet.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($recent_students as $s): ?>
                        <tr>
                            <td><?= sanitize($s['index_number']) ?></td>
                            <td><?= sanitize($s['first_name'] . ' ' . $s['last_name']) ?></td>
                            <td><?= sanitize($s['email']) ?></td>
                            <td>
                                <?php if (!empty($s['course_code'])): ?>
                                    <?= sanitize($s['course_code'] . ' - ' . $s['course_name']) ?>
                                <?php else: ?>
                                    Not Enrolled
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="students/view.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-info text-white">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

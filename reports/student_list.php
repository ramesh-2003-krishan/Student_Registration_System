<?php
$is_subfolder = true;
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$course_filter = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

$courses = $pdo->query("SELECT id, course_code, course_name FROM courses ORDER BY course_code ASC")->fetchAll();

$sql = "
    SELECT s.*, c.course_code, c.course_name 
    FROM students s
    LEFT JOIN enrollments e ON s.id = e.student_id
    LEFT JOIN courses c ON e.course_id = c.id
    WHERE 1=1
";
$params = [];

if ($course_filter > 0) {
    $sql .= " AND c.id = ?";
    $params[] = $course_filter;
}

$sql .= " ORDER BY s.index_number ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

$page_title = "Printable Student Report";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <h2>Student Registration Report</h2>
    <button type="button" class="btn btn-secondary" onclick="window.print();">Print Report</button>
</div>

<div class="card mb-4 no-print">
    <div class="card-body">
        <form method="GET" action="student_list.php" class="row g-2 align-items-center">
            <div class="col-md-6">
                <label for="course_id" class="form-label mb-1">Filter by Course:</label>
                <select name="course_id" id="course_id" class="form-select" onchange="this.form.submit();">
                    <option value="0">All Courses</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $course_filter === (int)$c['id'] ? 'selected' : '' ?>>
                            <?= sanitize($c['course_code'] . ' - ' . $c['course_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 text-end">
                <span class="text-muted">Total Records: <strong><?= count($students) ?></strong></span>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Index Number</th>
                    <th>Full Name</th>
                    <th>Gender</th>
                    <th>DOB</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Enrolled Course</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-3">No student records found.</td>
                    </tr>
                <?php else: ?>
                    <?php $count = 1; foreach ($students as $s): ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><strong><?= sanitize($s['index_number']) ?></strong></td>
                            <td><?= sanitize($s['first_name'] . ' ' . $s['last_name']) ?></td>
                            <td><?= sanitize($s['gender']) ?></td>
                            <td><?= sanitize($s['dob']) ?></td>
                            <td><?= sanitize($s['email']) ?></td>
                            <td><?= sanitize($s['phone']) ?></td>
                            <td>
                                <?php if (!empty($s['course_code'])): ?>
                                    <?= sanitize($s['course_code'] . ' - ' . $s['course_name']) ?>
                                <?php else: ?>
                                    Not Enrolled
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

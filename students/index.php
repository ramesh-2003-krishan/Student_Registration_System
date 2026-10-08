<?php
$is_subfolder = true;
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$search = trim($_GET['search'] ?? '');
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

if (!empty($search)) {
    $sql .= " AND (s.first_name LIKE ? OR s.last_name LIKE ? OR s.index_number LIKE ?)";
    $term = "%" . $search . "%";
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}

if ($course_filter > 0) {
    $sql .= " AND c.id = ?";
    $params[] = $course_filter;
}

$sql .= " ORDER BY s.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

$page_title = "Student Directory";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Students</h2>
    <a href="add.php" class="btn btn-primary">Add New Student</a>
</div>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-info">
        <?= sanitize($_SESSION['message']) ?>
    </div>
    <?php unset($_SESSION['message']); ?>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-2">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control" placeholder="Search by name or index number..." value="<?= sanitize($search) ?>">
            </div>
            <div class="col-md-4">
                <select name="course_id" class="form-select">
                    <option value="0">All Courses</option>
                    <?php foreach ($courses as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $course_filter === (int)$c['id'] ? 'selected' : '' ?>>
                            <?= sanitize($c['course_code'] . ' - ' . $c['course_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
                <a href="index.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>Index Number</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Enrolled Course</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($students)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-3">No students found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($students as $s): ?>
                        <tr>
                            <td><strong><?= sanitize($s['index_number']) ?></strong></td>
                            <td><?= sanitize($s['first_name'] . ' ' . $s['last_name']) ?></td>
                            <td><?= sanitize($s['gender']) ?></td>
                            <td><?= sanitize($s['email']) ?></td>
                            <td><?= sanitize($s['phone']) ?></td>
                            <td>
                                <?php if (!empty($s['course_code'])): ?>
                                    <?= sanitize($s['course_code'] . ' - ' . $s['course_name']) ?>
                                <?php else: ?>
                                    Not Enrolled
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="view.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-info text-white">View</a>
                                <a href="edit.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                <a href="delete.php?id=<?= $s['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
$is_subfolder = true;
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$courses = $pdo->query("SELECT id, course_code, course_name FROM courses WHERE status = 'active' ORDER BY course_code ASC")->fetchAll();

$error = "";
$index_number = "";
$first_name = "";
$last_name = "";
$dob = "";
$gender = "Male";
$email = "";
$phone = "";
$address = "";
$nic = "";
$guardian_name = "";
$guardian_phone = "";
$course_id = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $index_number = strtoupper(trim($_POST['index_number'] ?? ''));
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $gender = trim($_POST['gender'] ?? 'Male');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $nic = trim($_POST['nic'] ?? '');
    $guardian_name = trim($_POST['guardian_name'] ?? '');
    $guardian_phone = trim($_POST['guardian_phone'] ?? '');
    $course_id = (int)($_POST['course_id'] ?? 0);

    if (empty($index_number) || empty($first_name) || empty($last_name) || empty($dob) || empty($email) || empty($phone) || empty($address) || empty($guardian_name) || empty($guardian_phone) || $course_id <= 0) {
        $error = "Please fill in all required fields and select a course.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($phone) !== 10 || !is_numeric($phone)) {
        $error = "Student phone number must be 10 digits.";
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE index_number = ?");
        $stmt->execute([$index_number]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Index number is already registered.";
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Email address is already registered.";
            } else {
                $stmtInsert = $pdo->prepare("
                    INSERT INTO students 
                    (index_number, first_name, last_name, dob, gender, email, phone, address, nic, guardian_name, guardian_phone) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmtInsert->execute([
                    $index_number, $first_name, $last_name, $dob, $gender, 
                    $email, $phone, $address, !empty($nic) ? $nic : null, 
                    $guardian_name, $guardian_phone
                ]);
                $student_id = $pdo->lastInsertId();

                $stmtEnroll = $pdo->prepare("INSERT INTO enrollments (student_id, course_id) VALUES (?, ?)");
                $stmtEnroll->execute([$student_id, $course_id]);

                $_SESSION['message'] = "Student registered successfully.";
                header("Location: index.php");
                exit();
            }
        }
    }
}

$page_title = "Add Student";
require_once __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Register New Student</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= sanitize($error) ?></div>
                <?php endif; ?>

                <form action="add.php" method="POST" id="studentForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="index_number" class="form-label">Index Number *</label>
                            <input type="text" class="form-control" id="index_number" name="index_number" value="<?= sanitize($index_number) ?>" placeholder="e.g. HS/2022/0328" required>
                        </div>

                        <div class="col-md-6">
                            <label for="course_id" class="form-label">Course *</label>
                            <select class="form-select" id="course_id" name="course_id" required>
                                <option value="0">-- Select Course --</option>
                                <?php foreach ($courses as $c): ?>
                                    <option value="<?= $c['id'] ?>" <?= $course_id === (int)$c['id'] ? 'selected' : '' ?>>
                                        <?= sanitize($c['course_code'] . ' - ' . $c['course_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?= sanitize($first_name) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?= sanitize($last_name) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="dob" class="form-label">Date of Birth *</label>
                            <input type="date" class="form-control" id="dob" name="dob" value="<?= sanitize($dob) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="gender" class="form-label">Gender *</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>Male</option>
                                <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
                                <option value="Other" <?= $gender === 'Other' ? 'selected' : '' ?>>Other</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= sanitize($email) ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="phone" class="form-label">Phone Number (10 digits) *</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?= sanitize($phone) ?>" placeholder="0771234567" required>
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label">Address *</label>
                            <textarea class="form-control" id="address" name="address" rows="2" required><?= sanitize($address) ?></textarea>
                        </div>

                        <div class="col-md-4">
                            <label for="nic" class="form-label">NIC Number</label>
                            <input type="text" class="form-control" id="nic" name="nic" value="<?= sanitize($nic) ?>">
                        </div>

                        <div class="col-md-4">
                            <label for="guardian_name" class="form-label">Guardian Name *</label>
                            <input type="text" class="form-control" id="guardian_name" name="guardian_name" value="<?= sanitize($guardian_name) ?>" required>
                        </div>

                        <div class="col-md-4">
                            <label for="guardian_phone" class="form-label">Guardian Phone *</label>
                            <input type="text" class="form-control" id="guardian_phone" name="guardian_phone" value="<?= sanitize($guardian_phone) ?>" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Register Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

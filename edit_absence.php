<?php
declare(strict_types=1);

require __DIR__ . '/config/db.php';
require __DIR__ . '/helpers/functions.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid ID");
}
$id = (int)$_GET['id'];

// Get absence record (include student_id)
$stmt = $pdo->prepare("SELECT id, student_id, absence_date, reason FROM absences WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
    die("Record not found.");
}

// Load students for dropdown
$students = [];
try {
    $students = $pdo->query("
        SELECT id, student_no, first_name, last_name
        FROM students
        ORDER BY last_name ASC, first_name ASC
    ")->fetchAll();
} catch (Throwable $e) {
    $students = [];
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
    $date = $_POST['date'] ?? '';
    $reason = trim($_POST['reason'] ?? '');

    if ($studentId <= 0 || $date === '' || $reason === '') {
        $error = "All fields required.";
    } else {
        $update = $pdo->prepare("UPDATE absences SET student_id = ?, absence_date = ?, reason = ? WHERE id = ?");
        $update->execute([$studentId, $date, $reason, $id]);

        header("Location: admin_dashboard.php");
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Option for Absences</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="card shadow-sm" style="max-width: 720px; margin: 0 auto;">
    <div class="card-body p-4">
      <h4 class="mb-1">Edit Absence</h4>
      <div class="text-muted mb-3">Update the student, date, or reason, then save.</div>

      <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo e($error); ?></div>
      <?php endif; ?>

      <form method="POST" class="d-grid gap-3">

        <!-- Student Dropdown -->
        <div>
          <label class="form-label">Student</label>
          <select name="student_id" class="form-select" required>
            <option value="">-- Select Student --</option>
            <?php foreach ($students as $s):
              $sid = (int)($s['id'] ?? 0);
              $selected = ($sid === (int)$row['student_id']) ? 'selected' : '';
              $label = trim(($s['last_name'] ?? '') . ', ' . ($s['first_name'] ?? ''));
              $no = $s['student_no'] ?? '';
            ?>
              <option value="<?php echo $sid; ?>" <?php echo $selected; ?>>
                <?php echo e($label); ?> (<?php echo e($no); ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="form-label">Date</label>
          <input type="date" name="date" class="form-control"
                 value="<?php echo e($row['absence_date'] ?? ''); ?>" required>
        </div>

        <div>
          <label class="form-label">Reason</label>
          <input type="text" name="reason" class="form-control"
                 value="<?php echo e($row['reason'] ?? ''); ?>" required>
        </div>

        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary">Update</button>
          <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
        </div>
      </form>

    </div>
  </div>
</div>

</body>
</html>
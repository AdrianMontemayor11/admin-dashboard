<?php
declare(strict_types=1);

require __DIR__ . '/config/auth.php';
require __DIR__ . '/config/db.php';
require __DIR__ . '/helpers/functions.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  http_response_code(400);
  exit("Invalid ID");
}

$id = (int)$_GET['id'];

// Fetch absence record
$stmt = $pdo->prepare("SELECT id, student_id, absence_date, reason FROM absences WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
  http_response_code(404);
  exit("Record not found");
}

// Fetch students list for dropdown
$students = [];
try {
  $s = $pdo->query("SELECT id, student_no, first_name, last_name FROM students ORDER BY last_name, first_name");
  $students = $s->fetchAll();
} catch (Throwable $e) {
  $students = [];
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $student_id = (int)($_POST['student_id'] ?? 0);
  $date = trim((string)($_POST['date'] ?? ''));
  $reason = trim((string)($_POST['reason'] ?? ''));

  if ($student_id <= 0 || $date === '' || $reason === '') {
    $error = "All fields are required.";
  } else {
    $up = $pdo->prepare("UPDATE absences SET student_id = ?, absence_date = ?, reason = ? WHERE id = ?");
    $up->execute([$student_id, $date, $reason, $id]);
    exit("OK"); // JS will handle this
  }
}

// Helper: convert DB date to input date (YYYY-MM-DD)
$inputDate = '';
if (!empty($row['absence_date'])) {
  $ts = strtotime((string)$row['absence_date']);
  $inputDate = $ts ? date('Y-m-d', $ts) : (string)$row['absence_date'];
}
?>

<form id="editAbsenceForm" method="POST">
  <div class="modal-header">
    <h5 class="modal-title">Edit Absence</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
  </div>

  <div class="modal-body">
    <?php if ($error): ?>
      <div class="alert alert-danger mb-3"><?php echo e($error); ?></div>
    <?php endif; ?>

    <div class="mb-3">
      <label class="form-label">Student</label>
      <select class="form-select" name="student_id" required>
        <option value="">Select student…</option>
        <?php foreach ($students as $s): ?>
          <?php
            $sid = (int)$s['id'];
            $label = ($s['last_name'] ?? '') . ', ' . ($s['first_name'] ?? '') . ' (' . ($s['student_no'] ?? '') . ')';
          ?>
          <option value="<?php echo $sid; ?>" <?php echo ($sid === (int)$row['student_id']) ? 'selected' : ''; ?>>
            <?php echo e($label); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Date</label>
      <input type="date" class="form-control" name="date" value="<?php echo e($inputDate); ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Reason</label>
      <input type="text" class="form-control" name="reason" value="<?php echo e($row['reason'] ?? ''); ?>" required>
    </div>

    <input type="hidden" name="id" value="<?php echo (int)$row['id']; ?>">
  </div>

  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
    <button type="submit" class="btn btn-primary">Update</button>
  </div>
</form>
<?php
declare(strict_types=1);

require __DIR__ . '/config/auth.php';
require __DIR__ . '/config/db.php';
require __DIR__ . '/helpers/functions.php';

$adminName = $_SESSION['admin_name'] ?? 'Admin';

$q = trim((string)($_GET['q'] ?? ''));
$from = trim((string)($_GET['from'] ?? ''));
$to   = trim((string)($_GET['to'] ?? ''));

$sql = "
  SELECT a.id, a.absence_date, a.reason,
         s.student_no, CONCAT(s.last_name, ', ', s.first_name) AS student_name,
         t.employee_no, CONCAT(t.last_name, ', ', t.first_name) AS teacher_name
  FROM absences a
  LEFT JOIN students s ON s.id = a.student_id
  LEFT JOIN teachers t ON t.id = a.teacher_id
  WHERE 1=1
";

$params = [];

if ($from !== '') {
  $sql .= " AND a.absence_date >= ? ";
  $params[] = $from;
}

if ($to !== '') {
  $sql .= " AND a.absence_date <= ? ";
  $params[] = $to;
}

if ($q !== '') {
  $sql .= " AND (
      CONCAT(s.last_name, ', ', s.first_name) LIKE ?
      OR s.student_no LIKE ?
      OR CONCAT(t.last_name, ', ', t.first_name) LIKE ?
      OR t.employee_no LIKE ?
      OR a.reason LIKE ?
    ) ";
  $like = "%{$q}%";
  array_push($params, $like, $like, $like, $like, $like);
}

$sql .= " ORDER BY a.absence_date DESC, a.id DESC LIMIT 200 ";

$rows = [];
try {
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
  $rows = $stmt->fetchAll();
} catch (Throwable $e) {
  $rows = [];
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Attendance</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
<h3>Attendance Page Ready</h3>
<p>This is the generated attendance.php file.</p>
</body>
</html>

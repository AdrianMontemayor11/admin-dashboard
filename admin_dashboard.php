<?php
declare(strict_types=1);

require __DIR__ . '/config/auth.php';
require __DIR__ . '/config/db.php';
require __DIR__ . '/helpers/functions.php';   

// ---- BASIC SELECT COUNT() QUERIES ----
try {
    $totalStudents = (int)$pdo->query("SELECT COUNT(*) AS c FROM students")->fetch()['c'];
} catch (Throwable $e) {
    $totalStudents = 0;
}

try {
    $totalTeachers = (int)$pdo->query("SELECT COUNT(*) AS c FROM teachers")->fetch()['c'];
} catch (Throwable $e) {
    $totalTeachers = 0;
}

try {
    $absencesToday = (int)$pdo->query("SELECT COUNT(*) AS c FROM absences WHERE absence_date = CURDATE()")->fetch()['c'];
} catch (Throwable $e) {
    $absencesToday = 0;
}

try {
    $totalAbsences = (int)$pdo->query("SELECT COUNT(*) AS c FROM absences")->fetch()['c'];
} catch (Throwable $e) {
    $totalAbsences = 0;
}

// ---- OPTIONAL: Recent absences list (nice for the dashboard) ----
$recentAbsences = [];
try {
    $stmt = $pdo->prepare("
        SELECT a.id, a.absence_date, a.reason,
               s.student_no, CONCAT(s.last_name, ', ', s.first_name) AS student_name,
               t.employee_no, CONCAT(t.last_name, ', ', t.first_name) AS teacher_name
        FROM absences a
        LEFT JOIN students s ON s.id = a.student_id
        LEFT JOIN teachers t ON t.id = a.teacher_id
        ORDER BY a.absence_date DESC, a.id DESC
        LIMIT 8
    ");
    $stmt->execute();
    $recentAbsences = $stmt->fetchAll();
} catch (Throwable $e) {
    $recentAbsences = [];
}

$adminName = $_SESSION['admin_name'] ?? 'Admin';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Dashboard</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="assets/css/styles.css" rel="stylesheet">

  <style>
    /* Blur background when modal is open */
    body.modal-blur .main,
    body.modal-blur .sidebar {
      filter: blur(6px);
    }
    body.modal-blur .modal {
      filter: none;
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="brand d-flex align-items-center gap-2">
      <div class="rounded-3 d-inline-flex align-items-center justify-content-center" style="width:42px;height:42px;background:rgba(255,255,255,.12);">
        <i class="bi bi-speedometer2 fs-4"></i>
      </div>
      <div>
        <div class="fw-semibold">School Admin</div>
        <div class="small text-white-50">Dashboard</div>
      </div>
    </div>

    <nav class="pt-3">
      <a class="nav-link active" href="admin_dashboard.php"><i class="bi bi-grid-1x2"></i> Overview</a>
      <a class="nav-link" href="#"><i class="bi bi-people"></i> Students</a>
      <a class="nav-link" href="#"><i class="bi bi-person-badge"></i> Teachers</a>
      <a class="nav-link" href="attendance.php"><i class="bi bi-calendar2-check"></i> Attendance</a>
      <a class="nav-link" href="#"><i class="bi bi-gear"></i> Settings</a>

      <hr class="border-light opacity-25 my-3 mx-3">
      <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </nav>
  </aside>

  <!-- Main content -->
  <main class="main">
    <!-- Topbar -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
      <div>
        <h3 class="mb-1">Welcome, Admin</h3>
        <div class="text-muted">Here’s a quick overview of your school system.</div>
      </div>
      <div class="d-flex align-items-center gap-2">
        <span class="badge text-bg-light border rounded-pill px-3 py-2">
          <i class="bi bi-calendar3 me-1"></i><?php echo date('F d, Y'); ?>
        </span>
        <span class="badge text-bg-primary rounded-pill px-3 py-2">
          <i class="bi bi-shield-check me-1"></i> Admin
        </span>
      </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-md-6 col-xl-3">
        <div class="card stat-card">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="text-muted small">Total Students</div>
                <div class="fs-3 fw-bold"><?php echo formatNumber($totalStudents); ?></div>
                <div class="small text-muted mt-1">All enrolled students</div>
              </div>
              <div class="icon">
                <i class="bi bi-mortarboard fs-4 text-primary"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-xl-3">
        <div class="card stat-card">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="text-muted small">Total Teachers</div>
                <div class="fs-3 fw-bold"><?php echo formatNumber($totalTeachers); ?></div>
                <div class="small text-muted mt-1">Active teachers</div>
              </div>
              <div class="icon" style="background: rgba(25, 135, 84, .12);">
                <i class="bi bi-person-workspace fs-4 text-success"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-xl-3">
        <div class="card stat-card">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="text-muted small">Absences Today</div>
                <div class="fs-3 fw-bold"><?php echo formatNumber($absencesToday); ?></div>
                <div class="small text-muted mt-1">Recorded for today</div>
              </div>
              <div class="icon" style="background: rgba(220, 53, 69, .12);">
                <i class="bi bi-exclamation-triangle fs-4 text-danger"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-xl-3">
        <div class="card stat-card">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <div class="text-muted small">Total Absences</div>
                <div class="fs-3 fw-bold"><?php echo formatNumber($totalAbsences); ?></div>
                <div class="small text-muted mt-1">All time</div>
              </div>
              <div class="icon" style="background: rgba(111, 66, 193, .12);">
                <i class="bi bi-clipboard-data fs-4" style="color:#6f42c1;"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Absences Table -->
    <div class="card table-card">
      <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
          <div>
            <h5 class="mb-0">Recent Absences</h5>
            <div class="text-muted small">Latest recorded absence entries</div>
          </div>
          <a class="btn btn-outline-primary btn-sm" href="attendance.php">
            <i class="bi bi-arrow-right"></i> View Attendance
          </a>
        </div>

        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr class="text-muted small">
                <th>Date</th>
                <th>Student</th>
                <th>Teacher</th>
                <th>Reason</th>
                <th style="width:110px; text-align:right;">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!$recentAbsences): ?>
                <tr>
                  <td colspan="5" class="text-muted">No absence records found (or tables not yet set up).</td>
                </tr>
              <?php else: ?>
                <?php foreach ($recentAbsences as $row): ?>
                  <tr>
                    <td class="text-nowrap"><?php echo e($row['absence_date'] ?? ''); ?></td>
                    <td>
                      <div class="fw-semibold"><?php echo e($row['student_name'] ?? '—'); ?></div>
                      <div class="small text-muted"><?php echo e($row['student_no'] ?? ''); ?></div>
                    </td>
                    <td>
                      <div class="fw-semibold"><?php echo e($row['teacher_name'] ?? '—'); ?></div>
                      <div class="small text-muted"><?php echo e($row['employee_no'] ?? ''); ?></div>
                    </td>
                    <td><?php echo e($row['reason'] ?? ''); ?></td>
                    <td style="text-align:right;">
                      <a href="#"
                         class="btn btn-sm btn-primary js-edit-absence"
                         data-id="<?php echo (int)($row['id'] ?? 0); ?>">
                        Edit
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>

    <div class="text-center text-muted small mt-4">
      &copy; <?php echo date('Y'); ?> School Admin Dashboard
    </div>
  </main>

  <!-- Edit Modal -->
  <div class="modal fade" id="editAbsenceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content" id="editAbsenceModalContent">
        <div class="modal-body p-4">Loading…</div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    const modalEl = document.getElementById('editAbsenceModal');
    const modal = new bootstrap.Modal(modalEl);
    const modalContent = document.getElementById('editAbsenceModalContent');

    modalEl.addEventListener('show.bs.modal', () => document.body.classList.add('modal-blur'));
    modalEl.addEventListener('hidden.bs.modal', () => document.body.classList.remove('modal-blur'));

    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('.js-edit-absence');
      if (!btn) return;

      e.preventDefault();
      const id = btn.dataset.id;

      modalContent.innerHTML = '<div class="modal-body p-4">Loading…</div>';
      modal.show();

      try {
        const res = await fetch(`edit_absence_modal.php?id=${encodeURIComponent(id)}`);
        const html = await res.text();
        modalContent.innerHTML = html;

        const form = document.getElementById('editAbsenceForm');
        if (form) {
          form.addEventListener('submit', async (ev) => {
            ev.preventDefault();

            const fd = new FormData(form);
            const post = await fetch(`edit_absence_modal.php?id=${encodeURIComponent(id)}`, {
              method: 'POST',
              body: fd
            });

            const text = await post.text();

            if (text.trim() === 'OK') {
              modal.hide();
              window.location.reload();
            } else {
              modalContent.innerHTML = text;
            }
          });
        }
      } catch (err) {
        modalContent.innerHTML = '<div class="modal-body p-4 text-danger">Failed to load form.</div>';
      }
    });
  </script>
</body>
</html>
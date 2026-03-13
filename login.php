<?php
declare(strict_types=1);

session_start();

// If already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin_dashboard.php');
    exit;
}

$error = '';

// Demo credentials (replace with DB-based auth if needed)
$DEMO_USER = 'admin';
$DEMO_PASS = 'admin123';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === $DEMO_USER && $password === $DEMO_PASS) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_name'] = 'Adrian (Admin)';
        header('Location: admin_dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0">
          <div class="card-body p-4">
            <h4 class="mb-1">Admin Login</h4>
            <p class="text-muted mb-4">Sign in to continue</p>

            <?php if ($error): ?>
              <div class="alert alert-danger py-2"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post" autocomplete="off">
              <div class="mb-3">
                <label class="form-label">Username</label>
                <input class="form-control" name="username" required>
              </div>
              <div class="mb-3">
                <label class="form-label">Password</label>
                <input class="form-control" type="password" name="password" required>
              </div>
              <button class="btn btn-primary w-100">Login</button>
            </form>

            <hr class="my-4">
            <div class="small text-muted">
              Demo: <span class="badge bg-secondary">admin</span> / <span class="badge bg-secondary">admin123</span>
            </div>
          </div>
        </div>
        <div class="text-center mt-3 small text-muted">
          &copy; <?php echo date('Y'); ?> School Admin
        </div>
      </div>
    </div>
  </div>
</body>
</html>

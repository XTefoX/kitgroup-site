<?php
// admin/login.php
session_start();

// Include config and database
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';

// If already logged in, go to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: ' . BASE_URL . '/admin');
    exit;
}

$error = '';

// Handle login POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please fill in both fields.';
    } else {
        // Fetch admin by username
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT id, username, password_hash, full_name, role FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password_hash'])) {
            // Success – set session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_full_name'] = $admin['full_name'];
            $_SESSION['admin_role'] = $admin['role'];

            // Update last_login
            $update = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
            $update->execute([$admin['id']]);

            // Redirect to dashboard (using BASE_URL)
            header('Location: ' . BASE_URL . '/admin');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

// Set page title and meta for SEO
$page_title = 'Admin Login – Kit Group';
$page_desc  = 'Secure login for Kit Group admin panel.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom brand colors (Red, Blue, White) -->
    <style>
        :root {
            --primary-red: #C8102E;
            --primary-blue: #003399;
            --white: #ffffff;
        }
        body {
            background: #f8f9fa;
            display: flex;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-card {
            max-width: 400px;
            margin: 0 auto;
            border: none;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .login-card .card-header {
            background: var(--primary-blue);
            color: #fff;
            text-align: center;
            border-radius: 10px 10px 0 0 !important;
            padding: 1.5rem;
        }
        .login-card .card-header h3 {
            margin: 0;
            font-weight: 600;
        }
        .login-card .card-body {
            padding: 2rem;
        }
        .btn-login {
            background: var(--primary-red);
            color: #fff;
            border: none;
            width: 100%;
            padding: 0.75rem;
            font-weight: 600;
        }
        .btn-login:hover {
            background: #a00d25;
            color: #fff;
        }
        .brand-text {
            color: var(--primary-red);
            font-weight: 700;
        }
        .error-msg {
            color: var(--primary-red);
            margin-top: 1rem;
        }
    </style>
    <!-- Optional: favicon -->
    <link rel="icon" href="<?= BASE_URL ?>/assets/images/favicon.ico" type="image/x-icon">
</head>
<body>

    <div class="container">
        <div class="login-card card">
            <div class="card-header">
                <h3>Kit Group Admin</h3>
                <small>Secure Access Only</small>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger error-msg"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-login">Log In</button>
                </form>
                <div class="mt-3 text-center small text-muted">
                    &copy; <?= date('Y') ?> Kit Group – All rights reserved.
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
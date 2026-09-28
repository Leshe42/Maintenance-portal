<?php
/**
 * MaintenancePro v2.0 - Login Page
 * User authentication interface
 */

require_once 'config.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

$error_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } else {
        try {
            $db = Database::getInstance();
            
            // Find user by username
            $user = $db->fetchOne(
                "SELECT id, username, email, full_name, role, password_hash, is_active FROM users WHERE username = ?",
                [$username]
            );

            if ($user && $user['is_active']) {
                // Verify password
                if (verifyPassword($password, $user['password_hash'])) {
                    // Authentication successful
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['last_activity'] = time();

                    // Log login activity
                    logActivity('LOGIN', 'USER', $user['id'], 'User logged in');

                    // Redirect to dashboard
                    header("Location: dashboard.php");
                    exit;
                } else {
                    $error_message = 'Invalid username or password.';
                }
            } else {
                $error_message = 'Invalid username or password.';
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            $error_message = 'An error occurred during login. Please try again.';
        }
    }
}

$demo_users = [
    [
        'username' => 'planner01',
        'password' => 'password123',
        'name' => 'Sarah Mitchell',
        'role' => 'Maintenance Planner',
        'color' => '#7c3aed'
    ],
    [
        'username' => 'engineer01',
        'password' => 'password123',
        'name' => 'Rashid Omar',
        'role' => 'Maintenance Engineer',
        'color' => '#0369a1'
    ],
    [
        'username' => 'workman01',
        'password' => 'password123',
        'name' => 'John Petersen',
        'role' => 'Workman',
        'color' => '#047857'
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MaintenancePro</title>
    <link rel="stylesheet" href="design-system.css">
    <style>
        body { background: linear-gradient(135deg, #2563eb 0%, #0369a1 100%); display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .login-container { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; max-width: 1000px; width: 100%; padding: 20px; }
        .login-form-section { background: white; border-radius: 12px; padding: 40px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
        .login-form-section h1 { font-size: 28px; margin: 0 0 8px 0; color: #0f172a; font-weight: 700; }
        .login-form-section .subtitle { font-size: 14px; color: #64748b; margin: 0 0 32px 0; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #0f172a; }
        .form-input { width: 100%; padding: 10px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; font-family: inherit; transition: all 0.2s; }
        .form-input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
        .btn-login { width: 100%; padding: 10px; background: #2563eb; color: white; border: none; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; margin-top: 8px; }
        .btn-login:hover { background: #1d4ed8; }
        .alert-error { background: #fee2e2; color: #991b1b; border-left: 4px solid #dc2626; padding: 12px; border-radius: 6px; margin-bottom: 20px; }
        .demo-section { color: white; }
        .demo-section h2 { font-size: 20px; margin: 0 0 24px 0; font-weight: 700; }
        .demo-users { display: flex; flex-direction: column; gap: 12px; }
        .demo-user-card { background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; padding: 16px; cursor: pointer; transition: all 0.2s; text-align: center; }
        .demo-user-card:hover { background: rgba(255,255,255,0.15); transform: translateY(-2px); }
        .demo-user-avatar { display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 50%; background: rgba(255,255,255,0.3); margin-bottom: 8px; font-weight: 700; font-size: 18px; }
        .demo-user-name { font-size: 14px; font-weight: 600; margin-bottom: 4px; }
        .demo-user-role { font-size: 12px; opacity: 0.8; }
        .features-list { display: flex; flex-direction: column; gap: 12px; margin-top: 32px; padding-top: 32px; border-top: 1px solid rgba(255,255,255,0.2); }
        .feature-item { display: flex; align-items: center; gap: 12px; font-size: 14px; }
        @media (max-width: 768px) { .login-container { grid-template-columns: 1fr; gap: 20px; } }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-form-section">
            <h1>Welcome Back</h1>
            <p class="subtitle">Sign in to your account</p>
            <?php if ($error_message): ?>
                <div class="alert-error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-input" placeholder="planner01" required autofocus>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-login">Sign In</button>
            </form>
        </div>

        <div class="demo-section">
            <h2>Try Demo Accounts</h2>
            <p style="margin: 0 0 16px 0; opacity: 0.9; font-size: 14px;">Click any card to auto-fill</p>
            <div class="demo-users">
                <?php foreach ($demo_users as $user): ?>
                <div class="demo-user-card" onclick="autofill('<?php echo $user['username']; ?>', '<?php echo $user['password']; ?>')">
                    <div class="demo-user-avatar" style="background: <?php echo $user['color']; ?>20; color: <?php echo $user['color']; ?>;">MP</div>
                    <div class="demo-user-name"><?php echo $user['name']; ?></div>
                    <div class="demo-user-role"><?php echo $user['role']; ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        function autofill(username, password) {
            document.querySelector('input[name="username"]').value = username;
            document.querySelector('input[name="password"]').value = password;
            document.querySelector('form').submit();
        }
    </script>
</body>
</html>

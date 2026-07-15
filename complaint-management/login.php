<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

require_guest();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Email and password are required.';
    } else {
        $result = attempt_login($email, $password);

        if ($result === true) {
            flash('success', 'Welcome back!');
            if (is_admin()) {
                redirect('admin/dashboard.php');
            }
            redirect('user/dashboard.php');
        }

        $error = is_string($result) ? $result : 'Login failed.';
    }
}

$pageTitle = 'Login - ' . APP_NAME;
require __DIR__ . '/includes/header.php';
?>
<div class="auth-card">
    <h1>Login</h1>
    <p class="subtitle">Sign in to manage your complaints</p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="form">
        <label>
            Email
            <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
        </label>
        <label>
            Password
            <input type="password" name="password" required>
        </label>
        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>

    <p class="auth-footer">
        New user? <a href="<?= e(app_url('signup.php')) ?>">Create account</a>
    </p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>

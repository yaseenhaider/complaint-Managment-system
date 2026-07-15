<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

require_guest();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($fullName === '' || $email === '' || $password === '') {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $result = register_user($fullName, $email, $password);

        if ($result === true) {
            flash('success', 'Account created. Please login.');
            redirect('login.php');
        }

        $error = is_string($result) ? $result : 'Registration failed.';
    }
}

$pageTitle = 'Sign Up - ' . APP_NAME;
require __DIR__ . '/includes/header.php';
?>
<div class="auth-card">
    <h1>Sign Up</h1>
    <p class="subtitle">Register to submit complaints</p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="form">
        <label>
            Full Name
            <input type="text" name="full_name" required value="<?= e($_POST['full_name'] ?? '') ?>">
        </label>
        <label>
            Email
            <input type="email" name="email" required value="<?= e($_POST['email'] ?? '') ?>">
        </label>
        <label>
            Password
            <input type="password" name="password" required minlength="6">
        </label>
        <label>
            Confirm Password
            <input type="password" name="confirm_password" required minlength="6">
        </label>
        <button type="submit" class="btn btn-primary btn-block">Create Account</button>
    </form>

    <p class="auth-footer">
        Already have an account? <a href="<?= e(app_url('login.php')) ?>">Login</a>
    </p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>

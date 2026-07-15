<?php

declare(strict_types=1);

/**
 * One-time setup: open http://localhost/complaint-management/install.php
 * Delete this file after successful installation.
 */

require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/database.php';

$host = DB_HOST;
$user = DB_USER;
$pass = DB_PASS;
$name = DB_NAME;

$messages = [];
$done = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $conn = new mysqli($host, $user, $pass);
        $conn->query("CREATE DATABASE IF NOT EXISTS `{$name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $conn->select_db($name);

        $conn->query(
            "CREATE TABLE IF NOT EXISTS users (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                full_name VARCHAR(100) NOT NULL,
                email VARCHAR(150) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB"
        );

        $conn->query(
            "CREATE TABLE IF NOT EXISTS complaints (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                user_id INT UNSIGNED NOT NULL,
                subject VARCHAR(200) NOT NULL,
                category VARCHAR(50) NOT NULL DEFAULT 'General',
                description TEXT NOT NULL,
                status ENUM('pending', 'in_progress', 'resolved', 'rejected') NOT NULL DEFAULT 'pending',
                admin_notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                CONSTRAINT fk_complaints_user
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            ) ENGINE=InnoDB"
        );

        $adminEmail = 'admin@cms.local';
        $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
        $adminName = 'System Admin';
        $role = 'admin';

        $check = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $check->bind_param('s', $adminEmail);
        $check->execute();
        $exists = $check->get_result()->fetch_assoc();

        if ($exists) {
            $upd = $conn->prepare('UPDATE users SET password = ?, role = ? WHERE email = ?');
            $upd->bind_param('sss', $adminPass, $role, $adminEmail);
            $upd->execute();
            $messages[] = 'Admin password reset to: admin123';
        } else {
            $ins = $conn->prepare('INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)');
            $ins->bind_param('ssss', $adminName, $adminEmail, $adminPass, $role);
            $ins->execute();
            $messages[] = 'Admin account created.';
        }

        $messages[] = 'Database and tables ready.';
        $messages[] = 'Admin login: admin@cms.local / admin123';
        $done = true;
    } catch (Throwable $e) {
        $messages[] = 'Error: ' . $e->getMessage();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install - <?= htmlspecialchars(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars(app_url('assets/css/style.css')) ?>">
</head>
<body>
<main class="container main-content">
    <div class="auth-card">
        <h1>Install Database</h1>
        <p class="subtitle">Make sure XAMPP Apache and MySQL are running.</p>

        <?php foreach ($messages as $msg): ?>
            <div class="alert <?= $done ? 'alert-success' : 'alert-error' ?>"><?= htmlspecialchars($msg) ?></div>
        <?php endforeach; ?>

        <?php if ($done): ?>
            <p><a class="btn btn-primary btn-block" href="<?= htmlspecialchars(app_url('login.php')) ?>">Go to Login</a></p>
        <?php else: ?>
            <form method="post">
                <button type="submit" class="btn btn-primary btn-block">Run Installation</button>
            </form>
        <?php endif; ?>
    </div>
</main>
</body>
</html>

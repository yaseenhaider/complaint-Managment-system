<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';

$user = current_user();
$flash = get_flash();
$pageTitle = $pageTitle ?? APP_NAME;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= e(app_url('assets/css/style.css')) ?>">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a class="logo" href="<?= e(app_url($user ? (is_admin() ? 'admin/dashboard.php' : 'user/dashboard.php') : 'login.php')) ?>"><?= e(APP_NAME) ?></a>
        <?php if ($user): ?>
            <nav class="nav">
                <?php if (is_admin()): ?>
                    <a href="<?= e(app_url('admin/dashboard.php')) ?>">Dashboard</a>
                    <a href="<?= e(app_url('admin/complaints.php')) ?>">Complaints</a>
                    <a href="<?= e(app_url('admin/users.php')) ?>">Users</a>
                <?php else: ?>
                    <a href="<?= e(app_url('user/dashboard.php')) ?>">Dashboard</a>
                    <a href="<?= e(app_url('user/new-complaint.php')) ?>">New Complaint</a>
                    <a href="<?= e(app_url('user/my-complaints.php')) ?>">My Complaints</a>
                <?php endif; ?>
                <span class="user-badge"><?= e($user['full_name']) ?></span>
                <a class="btn btn-outline btn-sm" href="<?= e(app_url('logout.php')) ?>">Logout</a>
            </nav>
        <?php endif; ?>
    </div>
</header>
<main class="container main-content">
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
    <?php endif; ?>

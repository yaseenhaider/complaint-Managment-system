<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_admin();

$stats = [
    'users' => 0,
    'complaints' => 0,
    'pending' => 0,
    'resolved' => 0,
];

$stats['users'] = (int) db()->query('SELECT COUNT(*) AS c FROM users WHERE role = "user"')->fetch_assoc()['c'];
$stats['complaints'] = (int) db()->query('SELECT COUNT(*) AS c FROM complaints')->fetch_assoc()['c'];
$stats['pending'] = (int) db()->query('SELECT COUNT(*) AS c FROM complaints WHERE status = "pending"')->fetch_assoc()['c'];
$stats['resolved'] = (int) db()->query('SELECT COUNT(*) AS c FROM complaints WHERE status = "resolved"')->fetch_assoc()['c'];

$recent = db()->query(
    'SELECT c.id, c.subject, c.status, c.created_at, u.full_name
     FROM complaints c
     JOIN users u ON u.id = c.user_id
     ORDER BY c.created_at DESC
     LIMIT 8'
);

$pageTitle = 'Admin Dashboard - ' . APP_NAME;
require __DIR__ . '/../includes/header.php';
?>
<h1>Admin Dashboard</h1>
<p class="subtitle">Overview of all complaints and users</p>

<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-value"><?= $stats['users'] ?></span>
        <span class="stat-label">Users</span>
    </div>
    <div class="stat-card">
        <span class="stat-value"><?= $stats['complaints'] ?></span>
        <span class="stat-label">Complaints</span>
    </div>
    <div class="stat-card stat-pending">
        <span class="stat-value"><?= $stats['pending'] ?></span>
        <span class="stat-label">Pending</span>
    </div>
    <div class="stat-card stat-resolved">
        <span class="stat-value"><?= $stats['resolved'] ?></span>
        <span class="stat-label">Resolved</span>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Latest Complaints</h2>
        <a class="btn btn-outline btn-sm" href="<?= e(app_url('admin/complaints.php')) ?>">View All</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Subject</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $recent->fetch_assoc()): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= e($row['full_name']) ?></td>
                    <td><?= e($row['subject']) ?></td>
                    <td><span class="badge badge-<?= e($row['status']) ?>"><?= e(complaint_status_label($row['status'])) ?></span></td>
                    <td><?= e(date('d M Y', strtotime($row['created_at']))) ?></td>
                    <td><a href="<?= e(app_url('admin/view-complaint.php?id=' . $row['id'])) ?>">Manage</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>

<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_login();

$userId = (int) current_user()['id'];

$stats = [
    'total' => 0,
    'pending' => 0,
    'in_progress' => 0,
    'resolved' => 0,
];

$stmt = db()->prepare(
    'SELECT status, COUNT(*) AS cnt FROM complaints WHERE user_id = ? GROUP BY status'
);
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $stats['total'] += (int) $row['cnt'];
    $key = $row['status'];
    if (isset($stats[$key])) {
        $stats[$key] = (int) $row['cnt'];
    }
}

$recent = db()->prepare(
    'SELECT id, subject, status, created_at FROM complaints WHERE user_id = ? ORDER BY created_at DESC LIMIT 5'
);
$recent->bind_param('i', $userId);
$recent->execute();
$recentComplaints = $recent->get_result();

$pageTitle = 'User Dashboard - ' . APP_NAME;
require __DIR__ . '/../includes/header.php';
?>
<h1>Welcome, <?= e(current_user()['full_name']) ?></h1>
<p class="subtitle">Track and manage your complaints</p>

<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-value"><?= (int) $stats['total'] ?></span>
        <span class="stat-label">Total</span>
    </div>
    <div class="stat-card stat-pending">
        <span class="stat-value"><?= (int) $stats['pending'] ?></span>
        <span class="stat-label">Pending</span>
    </div>
    <div class="stat-card stat-progress">
        <span class="stat-value"><?= (int) $stats['in_progress'] ?></span>
        <span class="stat-label">In Progress</span>
    </div>
    <div class="stat-card stat-resolved">
        <span class="stat-value"><?= (int) $stats['resolved'] ?></span>
        <span class="stat-label">Resolved</span>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Recent Complaints</h2>
        <a class="btn btn-primary btn-sm" href="<?= e(app_url('user/new-complaint.php')) ?>">+ New Complaint</a>
    </div>
    <?php if ($recentComplaints->num_rows === 0): ?>
        <p class="empty">No complaints yet. Submit your first complaint.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $recentComplaints->fetch_assoc()): ?>
                    <tr>
                        <td><?= e($row['subject']) ?></td>
                        <td><span class="badge badge-<?= e($row['status']) ?>"><?= e(complaint_status_label($row['status'])) ?></span></td>
                        <td><?= e(date('d M Y', strtotime($row['created_at']))) ?></td>
                        <td><a href="<?= e(app_url('user/view-complaint.php?id=' . $row['id'])) ?>">View</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>

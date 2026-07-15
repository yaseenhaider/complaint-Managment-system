<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_login();

$userId = (int) current_user()['id'];

$stmt = db()->prepare(
    'SELECT id, subject, category, status, created_at FROM complaints WHERE user_id = ? ORDER BY created_at DESC'
);
$stmt->bind_param('i', $userId);
$stmt->execute();
$complaints = $stmt->get_result();

$pageTitle = 'My Complaints - ' . APP_NAME;
require __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1>My Complaints</h1>
    <a class="btn btn-primary" href="<?= e(app_url('user/new-complaint.php')) ?>">+ New Complaint</a>
</div>

<div class="card">
    <?php if ($complaints->num_rows === 0): ?>
        <p class="empty">You have not submitted any complaints yet.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Subject</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $complaints->fetch_assoc()): ?>
                    <tr>
                        <td><?= (int) $row['id'] ?></td>
                        <td><?= e($row['subject']) ?></td>
                        <td><?= e($row['category']) ?></td>
                        <td><span class="badge badge-<?= e($row['status']) ?>"><?= e(complaint_status_label($row['status'])) ?></span></td>
                        <td><?= e(date('d M Y H:i', strtotime($row['created_at']))) ?></td>
                        <td><a href="<?= e(app_url('user/view-complaint.php?id=' . $row['id'])) ?>">View</a></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>

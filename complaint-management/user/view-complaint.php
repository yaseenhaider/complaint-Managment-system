<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_login();

$userId = (int) current_user()['id'];
$id = (int) ($_GET['id'] ?? 0);

$stmt = db()->prepare(
    'SELECT c.*, u.full_name AS user_name FROM complaints c
     JOIN users u ON u.id = c.user_id
     WHERE c.id = ? AND c.user_id = ? LIMIT 1'
);
$stmt->bind_param('ii', $id, $userId);
$stmt->execute();
$complaint = $stmt->get_result()->fetch_assoc();

if (!$complaint) {
    flash('error', 'Complaint not found.');
    redirect('user/my-complaints.php');
}

$pageTitle = 'Complaint #' . $id . ' - ' . APP_NAME;
require __DIR__ . '/../includes/header.php';
?>
<a class="back-link" href="<?= e(app_url('user/my-complaints.php')) ?>">&larr; Back to list</a>

<div class="card complaint-detail">
    <div class="detail-header">
        <h1><?= e($complaint['subject']) ?></h1>
        <span class="badge badge-<?= e($complaint['status']) ?>"><?= e(complaint_status_label($complaint['status'])) ?></span>
    </div>
    <dl class="detail-list">
        <dt>Category</dt>
        <dd><?= e($complaint['category']) ?></dd>
        <dt>Submitted</dt>
        <dd><?= e(date('d M Y H:i', strtotime($complaint['created_at']))) ?></dd>
        <dt>Last Updated</dt>
        <dd><?= e(date('d M Y H:i', strtotime($complaint['updated_at']))) ?></dd>
        <dt>Description</dt>
        <dd class="description"><?= nl2br(e($complaint['description'])) ?></dd>
        <?php if (!empty($complaint['admin_notes'])): ?>
            <dt>Admin Response</dt>
            <dd class="admin-notes"><?= nl2br(e($complaint['admin_notes'])) ?></dd>
        <?php endif; ?>
    </dl>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>

<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);

$stmt = db()->prepare(
    'SELECT c.*, u.full_name, u.email FROM complaints c
     JOIN users u ON u.id = c.user_id
     WHERE c.id = ? LIMIT 1'
);
$stmt->bind_param('i', $id);
$stmt->execute();
$complaint = $stmt->get_result()->fetch_assoc();

if (!$complaint) {
    flash('error', 'Complaint not found.');
    redirect('admin/complaints.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? '';
    $adminNotes = trim($_POST['admin_notes'] ?? '');
    $allowedStatus = ['pending', 'in_progress', 'resolved', 'rejected'];

    if (!in_array($status, $allowedStatus, true)) {
        $error = 'Invalid status.';
    } else {
        $stmt = db()->prepare('UPDATE complaints SET status = ?, admin_notes = ? WHERE id = ?');
        $stmt->bind_param('ssi', $status, $adminNotes, $id);
        $stmt->execute();

        flash('success', 'Complaint updated.');
        redirect('admin/view-complaint.php?id=' . $id);
    }
}

$pageTitle = 'Manage Complaint #' . $id . ' - ' . APP_NAME;
require __DIR__ . '/../includes/header.php';
?>
<a class="back-link" href="<?= e(app_url('admin/complaints.php')) ?>">&larr; Back to list</a>

<div class="card complaint-detail">
    <div class="detail-header">
        <h1><?= e($complaint['subject']) ?></h1>
        <span class="badge badge-<?= e($complaint['status']) ?>"><?= e(complaint_status_label($complaint['status'])) ?></span>
    </div>

    <dl class="detail-list">
        <dt>User</dt>
        <dd><?= e($complaint['full_name']) ?> (<?= e($complaint['email']) ?>)</dd>
        <dt>Category</dt>
        <dd><?= e($complaint['category']) ?></dd>
        <dt>Submitted</dt>
        <dd><?= e(date('d M Y H:i', strtotime($complaint['created_at']))) ?></dd>
        <dt>Description</dt>
        <dd class="description"><?= nl2br(e($complaint['description'])) ?></dd>
    </dl>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="form admin-form">
        <label>
            Status
            <select name="status" required>
                <?php foreach (['pending', 'in_progress', 'resolved', 'rejected'] as $s): ?>
                    <option value="<?= e($s) ?>" <?= $complaint['status'] === $s ? 'selected' : '' ?>><?= e(complaint_status_label($s)) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Admin Notes / Response
            <textarea name="admin_notes" rows="4"><?= e($complaint['admin_notes'] ?? '') ?></textarea>
        </label>
        <button type="submit" class="btn btn-primary">Update Complaint</button>
    </form>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>

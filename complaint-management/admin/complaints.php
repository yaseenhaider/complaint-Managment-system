<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_admin();

$filter = $_GET['status'] ?? '';
$allowed = ['', 'pending', 'in_progress', 'resolved', 'rejected'];

if (!in_array($filter, $allowed, true)) {
    $filter = '';
}

$sql = 'SELECT c.id, c.subject, c.category, c.status, c.created_at, u.full_name, u.email
        FROM complaints c
        JOIN users u ON u.id = c.user_id';

if ($filter !== '') {
    $sql .= ' WHERE c.status = ?';
}

$sql .= ' ORDER BY c.created_at DESC';

if ($filter !== '') {
    $stmt = db()->prepare($sql);
    $stmt->bind_param('s', $filter);
    $stmt->execute();
    $complaints = $stmt->get_result();
} else {
    $complaints = db()->query($sql);
}

$pageTitle = 'All Complaints - ' . APP_NAME;
require __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1>All Complaints</h1>
    <form method="get" class="filter-form">
        <select name="status" onchange="this.form.submit()">
            <option value="">All Status</option>
            <?php foreach (['pending', 'in_progress', 'resolved', 'rejected'] as $s): ?>
                <option value="<?= e($s) ?>" <?= $filter === $s ? 'selected' : '' ?>><?= e(complaint_status_label($s)) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Subject</th>
                <th>Category</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($complaints->num_rows === 0): ?>
                <tr><td colspan="7" class="empty">No complaints found.</td></tr>
            <?php endif; ?>
            <?php while ($row = $complaints->fetch_assoc()): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= e($row['full_name']) ?><br><small><?= e($row['email']) ?></small></td>
                    <td><?= e($row['subject']) ?></td>
                    <td><?= e($row['category']) ?></td>
                    <td><span class="badge badge-<?= e($row['status']) ?>"><?= e(complaint_status_label($row['status'])) ?></span></td>
                    <td><?= e(date('d M Y', strtotime($row['created_at']))) ?></td>
                    <td><a href="<?= e(app_url('admin/view-complaint.php?id=' . $row['id'])) ?>">Manage</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>

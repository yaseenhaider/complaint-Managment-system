<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_admin();

$users = db()->query(
    'SELECT u.id, u.full_name, u.email, u.created_at,
            (SELECT COUNT(*) FROM complaints c WHERE c.user_id = u.id) AS complaint_count
     FROM users u
     WHERE u.role = "user"
     ORDER BY u.created_at DESC'
);

$pageTitle = 'Users - ' . APP_NAME;
require __DIR__ . '/../includes/header.php';
?>
<h1>Registered Users</h1>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Complaints</th>
                <th>Joined</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($users->num_rows === 0): ?>
                <tr><td colspan="5" class="empty">No users registered yet.</td></tr>
            <?php endif; ?>
            <?php while ($row = $users->fetch_assoc()): ?>
                <tr>
                    <td><?= (int) $row['id'] ?></td>
                    <td><?= e($row['full_name']) ?></td>
                    <td><?= e($row['email']) ?></td>
                    <td><?= (int) $row['complaint_count'] ?></td>
                    <td><?= e(date('d M Y', strtotime($row['created_at']))) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>

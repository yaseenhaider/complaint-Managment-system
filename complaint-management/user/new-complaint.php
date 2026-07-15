<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';
require_login();

$error = '';
$userId = (int) current_user()['id'];
$categories = complaint_categories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject'] ?? '');
    $category = trim($_POST['category'] ?? 'General');
    $description = trim($_POST['description'] ?? '');

    if ($subject === '' || $description === '') {
        $error = 'Subject and description are required.';
    } elseif (!in_array($category, $categories, true)) {
        $error = 'Invalid category selected.';
    } else {
        $status = 'pending';
        $stmt = db()->prepare(
            'INSERT INTO complaints (user_id, subject, category, description, status) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->bind_param('issss', $userId, $subject, $category, $description, $status);
        $stmt->execute();

        flash('success', 'Complaint submitted successfully.');
        redirect('user/my-complaints.php');
    }
}

$pageTitle = 'New Complaint - ' . APP_NAME;
require __DIR__ . '/../includes/header.php';
?>
<h1>Submit New Complaint</h1>

<div class="card">
    <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="form">
        <label>
            Subject
            <input type="text" name="subject" required maxlength="200" value="<?= e($_POST['subject'] ?? '') ?>">
        </label>
        <label>
            Category
            <select name="category" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat) ?>" <?= ($cat === ($_POST['category'] ?? 'General')) ? 'selected' : '' ?>><?= e($cat) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Description
            <textarea name="description" rows="6" required><?= e($_POST['description'] ?? '') ?></textarea>
        </label>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Submit Complaint</button>
            <a class="btn btn-outline" href="<?= e(app_url('user/dashboard.php')) ?>">Cancel</a>
        </div>
    </form>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>

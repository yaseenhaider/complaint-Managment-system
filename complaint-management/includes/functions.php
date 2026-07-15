<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    if (!str_starts_with($path, 'http') && !str_starts_with($path, '/')) {
        require_once __DIR__ . '/../config/app.php';
        $path = app_url($path);
    }

    header('Location: ' . $path);
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return $flash;
}

function complaint_status_label(string $status): string
{
    return match ($status) {
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'resolved' => 'Resolved',
        'rejected' => 'Rejected',
        default => ucfirst($status),
    };
}

function complaint_categories(): array
{
    return [
        'General',
        'Billing',
        'Technical',
        'Service',
        'Product',
        'Other',
    ];
}

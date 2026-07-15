<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function is_admin(): bool
{
    $user = current_user();

    return $user !== null && ($user['role'] ?? '') === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        flash('error', 'Please login to continue.');
        redirect('login.php');
    }
}

function require_admin(): void
{
    require_login();

    if (!is_admin()) {
        flash('error', 'Admin access only.');
        redirect('user/dashboard.php');
    }
}

function require_guest(): void
{
    if (is_logged_in()) {
        if (is_admin()) {
            redirect('admin/dashboard.php');
        }
        redirect('user/dashboard.php');
    }
}

function login_user(array $user): void
{
    $_SESSION['user'] = [
        'id' => (int) $user['id'],
        'full_name' => $user['full_name'],
        'email' => $user['email'],
        'role' => $user['role'],
    ];
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            (bool) $params['secure'],
            (bool) $params['httponly']
        );
    }

    session_destroy();
}

function find_user_by_email(string $email): ?array
{
    $stmt = db()->prepare('SELECT id, full_name, email, password, role FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    return $result ?: null;
}

function register_user(string $fullName, string $email, string $password): bool|string
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Invalid email address.';
    }

    if (strlen($password) < 6) {
        return 'Password must be at least 6 characters.';
    }

    if (find_user_by_email($email)) {
        return 'Email is already registered.';
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'user';

    $stmt = db()->prepare('INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('ssss', $fullName, $email, $hash, $role);
    $stmt->execute();

    return true;
}

function attempt_login(string $email, string $password): bool|string
{
    $user = find_user_by_email(trim($email));

    if (!$user || !password_verify($password, $user['password'])) {
        return 'Invalid email or password.';
    }

    login_user($user);

    return true;
}

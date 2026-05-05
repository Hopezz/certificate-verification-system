<?php

declare(strict_types=1);

require_once __DIR__ . '/functions.php';

start_secure_session();

function current_admin(): ?array
{
    if (empty($_SESSION['admin_id'])) {
        return null;
    }

    $stmt = db()->prepare('SELECT id, email, role FROM admins WHERE id = ?');
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch();

    return $admin ?: null;
}

function require_admin(): array
{
    $admin = current_admin();

    if (!$admin) {
        redirect('login.php');
    }

    return $admin;
}

function require_superadmin(): array
{
    $admin = require_admin();

    if ($admin['role'] !== 'superadmin') {
        flash('error', 'Only superadmins can manage admin accounts.');
        redirect('dashboard.php');
    }

    return $admin;
}

function login_admin(string $email, string $password): bool
{
    $stmt = db()->prepare('SELECT id, password FROM admins WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($password, $admin['password'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int) $admin['id'];

    return true;
}

function logout_admin(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
    }

    session_destroy();
}


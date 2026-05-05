<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);

    session_start();
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function csrf_token(): string
{
    start_secure_session();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    start_secure_session();

    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('Invalid security token. Please go back and try again.');
    }
}

function old(string $key, array $source, string $default = ''): string
{
    $value = $source[$key] ?? $default;
    return is_string($value) ? $value : $default;
}

function flash(string $type, string $message): void
{
    start_secure_session();
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flashes(): array
{
    start_secure_session();
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

function program_label(string $program): string
{
    $labels = [
        'bachelor' => 'Bachelor',
        'master' => 'Master',
        'phd' => 'PhD',
    ];

    return $labels[$program] ?? ucfirst($program);
}

function validate_graduate(array $data, ?int $existingId = null): array
{
    $errors = [];
    $required = ['name', 'department', 'program', 'year_of_graduation', 'current_status', 'matric_number'];

    foreach ($required as $field) {
        if (trim((string) ($data[$field] ?? '')) === '') {
            $errors[$field] = 'This field is required.';
        }
    }

    if (!in_array($data['program'] ?? '', ['bachelor', 'master', 'phd'], true)) {
        $errors['program'] = 'Choose a valid program.';
    }

    $year = (string) ($data['year_of_graduation'] ?? '');
    $currentYear = (int) date('Y') + 1;
    if (!preg_match('/^\d{4}$/', $year) || (int) $year < 1900 || (int) $year > $currentYear) {
        $errors['year_of_graduation'] = 'Enter a valid graduation year.';
    }

    if (($data['program'] ?? '') !== 'bachelor' && trim((string) ($data['grade'] ?? '')) !== '') {
        $errors['grade'] = 'Grade is only used for Bachelor records.';
    }

    $matric = trim((string) ($data['matric_number'] ?? ''));
    if ($matric !== '') {
        $sql = 'SELECT id FROM graduates WHERE matric_number = ?';
        $params = [$matric];

        if ($existingId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $existingId;
        }

        $stmt = db()->prepare($sql);
        $stmt->execute($params);

        if ($stmt->fetch()) {
            $errors['matric_number'] = 'This matric number already exists.';
        }
    }

    $refNumber = trim((string) ($data['ref_number'] ?? ''));
    if ($refNumber !== '') {
        $sql = 'SELECT id FROM graduates WHERE ref_number = ?';
        $params = [$refNumber];

        if ($existingId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $existingId;
        }

        $stmt = db()->prepare($sql);
        $stmt->execute($params);

        if ($stmt->fetch()) {
            $errors['ref_number'] = 'This reference number already exists.';
        }
    }

    return $errors;
}

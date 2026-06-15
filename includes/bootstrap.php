<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/upload_paths.php';

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function require_login(): int
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    return (int) $_SESSION['user_id'];
}

function flash_take(string $key): string
{
    if (empty($_SESSION[$key])) {
        return '';
    }

    $message = (string) $_SESSION[$key];
    unset($_SESSION[$key]);

    return $message;
}

function flash_set(string $key, string $message): void
{
    $_SESSION[$key] = $message;
}

function redirect_with_message(string $location, ?string $message = null, ?string $error = null): never
{
    if ($message !== null) {
        flash_set('message', $message);
    }
    if ($error !== null) {
        flash_set('error', $error);
    }

    header('Location: ' . $location);
    exit;
}

function pg_bool_is_true(mixed $value): bool
{
    return $value === true || $value === 't' || $value === '1' || $value === 1;
}

function password_is_hashed(string $stored): bool
{
    return str_starts_with($stored, '$2y$')
        || str_starts_with($stored, '$2a$')
        || str_starts_with($stored, '$argon2');
}

function verify_user_password($dbconn, string $username, string $password): ?int
{
    $res = pg_query_params(
        $dbconn,
        'SELECT id, password FROM users WHERE username = $1',
        [$username]
    );

    if (!$res || !($row = pg_fetch_assoc($res))) {
        return null;
    }

    $stored = (string) $row['password'];
    $userId = (int) $row['id'];

    if (password_is_hashed($stored)) {
        return password_verify($password, $stored) ? $userId : null;
    }

    if (!hash_equals($stored, $password)) {
        return null;
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    pg_query_params($dbconn, 'UPDATE users SET password = $1 WHERE id = $2', [$hash, $userId]);

    return $userId;
}

function register_user($dbconn, string $username, string $password): ?string
{
    $res = pg_query_params($dbconn, 'SELECT id FROM users WHERE username = $1', [$username]);
    if ($res && pg_num_rows($res) > 0) {
        return 'そのユーザー名は既に使われています。';
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $insert = pg_query_params(
        $dbconn,
        'INSERT INTO users (username, password) VALUES ($1, $2)',
        [$username, $hash]
    );

    return $insert ? null : '登録に失敗しました。';
}

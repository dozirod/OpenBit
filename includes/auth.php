<?php
declare(strict_types=1);

require_once __DIR__ . '/db.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (!function_exists('openbit_auth_user')) {
    function openbit_auth_user(): ?array
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!is_int($userId) && !ctype_digit((string)$userId)) {
            return null;
        }

        try {
            $row = openbit_db_find_user_by_id((int)$userId);
        } catch (Throwable $exception) {
            return null;
        }

        if (!$row) {
            unset($_SESSION['user_id']);
            return null;
        }

        return $row;
    }
}

if (!function_exists('openbit_auth_login')) {
    function openbit_auth_login(string $email, string $password, string &$error = ''): bool
    {
        $normalizedEmail = strtolower(trim($email));
        if ($normalizedEmail === '' || $password === '') {
            $error = 'Please enter email and password.';
            return false;
        }

        try {
            $user = openbit_db_find_auth_by_email($normalizedEmail);
        } catch (Throwable $exception) {
            $error = 'Database is not available. Enable pdo_sqlite or sqlite3 in PHP.';
            return false;
        }

        if (!$user || !password_verify($password, (string)$user['password_hash'])) {
            $error = 'Invalid email or password.';
            return false;
        }

        $_SESSION['user_id'] = (int)$user['id'];
        return true;
    }
}

if (!function_exists('openbit_auth_register')) {
    function openbit_auth_register(string $displayName, string $email, string $password, string &$error = ''): bool
    {
        $cleanName = trim($displayName);
        $normalizedEmail = strtolower(trim($email));

        if ($cleanName === '' || strlen($cleanName) < 2) {
            $error = 'Display name must be at least 2 characters.';
            return false;
        }

        if (!filter_var($normalizedEmail, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
            return false;
        }

        if (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $newId = openbit_db_insert_user($cleanName, $normalizedEmail, $passwordHash);
            $_SESSION['user_id'] = $newId;
            return true;
        } catch (Throwable $exception) {
            $error = openbit_db_is_unique_error($exception)
                ? 'This email is already registered.'
                : 'Registration failed. Database is not available.';
            return false;
        }
    }
}

if (!function_exists('openbit_auth_logout')) {
    function openbit_auth_logout(): void
    {
        unset($_SESSION['user_id']);
    }
}

if (!function_exists('openbit_flash_set')) {
    function openbit_flash_set(string $message): void
    {
        $_SESSION['flash_message'] = $message;
    }
}

if (!function_exists('openbit_flash_get')) {
    function openbit_flash_get(): ?string
    {
        if (!isset($_SESSION['flash_message'])) {
            return null;
        }

        $message = (string)$_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
}

<?php
declare(strict_types=1);

if (!function_exists('openbit_db_backend')) {
    function openbit_db_backend(): string
    {
        if (extension_loaded('pdo_sqlite')) {
            return 'pdo_sqlite';
        }

        if (class_exists('SQLite3')) {
            return 'sqlite3';
        }

        return 'none';
    }
}

if (!function_exists('openbit_db_path')) {
    function openbit_db_path(): string
    {
        $dataDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data';
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0775, true);
        }

        return $dataDir . DIRECTORY_SEPARATOR . 'openbit.sqlite';
    }
}

if (!function_exists('openbit_db_connection')) {
    function openbit_db_connection(): PDO|SQLite3
    {
        static $db = null;

        if ($db instanceof PDO || $db instanceof SQLite3) {
            return $db;
        }

        $backend = openbit_db_backend();
        $dbPath = openbit_db_path();

        if ($backend === 'pdo_sqlite') {
            $pdo = new PDO('sqlite:' . $dbPath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            openbit_db_ensure_schema($pdo);
            $db = $pdo;
            return $db;
        }

        if ($backend === 'sqlite3') {
            $sqlite = new SQLite3($dbPath);
            $sqlite->enableExceptions(true);
            openbit_db_ensure_schema($sqlite);
            $db = $sqlite;
            return $db;
        }

        throw new RuntimeException(
            'No SQLite driver found. Enable either pdo_sqlite or sqlite3 extension in your PHP configuration.'
        );
    }
}

if (!function_exists('openbit_db_ensure_schema')) {
    function openbit_db_ensure_schema(PDO|SQLite3 $db): void
    {
        $schema = 'CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            display_name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )';

        if ($db instanceof PDO) {
            $db->exec($schema);
            return;
        }

        $db->exec($schema);
    }
}

if (!function_exists('openbit_db_find_user_by_id')) {
    function openbit_db_find_user_by_id(int $id): ?array
    {
        $db = openbit_db_connection();

        if ($db instanceof PDO) {
            $stmt = $db->prepare('SELECT id, display_name, email, created_at FROM users WHERE id = :id LIMIT 1');
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch();
            return $row ?: null;
        }

        $stmt = $db->prepare('SELECT id, display_name, email, created_at FROM users WHERE id = :id LIMIT 1');
        $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row ?: null;
    }
}

if (!function_exists('openbit_db_find_auth_by_email')) {
    function openbit_db_find_auth_by_email(string $email): ?array
    {
        $db = openbit_db_connection();

        if ($db instanceof PDO) {
            $stmt = $db->prepare('SELECT id, password_hash FROM users WHERE email = :email LIMIT 1');
            $stmt->execute(['email' => $email]);
            $row = $stmt->fetch();
            return $row ?: null;
        }

        $stmt = $db->prepare('SELECT id, password_hash FROM users WHERE email = :email LIMIT 1');
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $result = $stmt->execute();
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return $row ?: null;
    }
}

if (!function_exists('openbit_db_insert_user')) {
    function openbit_db_insert_user(string $displayName, string $email, string $passwordHash): int
    {
        $db = openbit_db_connection();

        if ($db instanceof PDO) {
            $stmt = $db->prepare(
                'INSERT INTO users (display_name, email, password_hash) VALUES (:display_name, :email, :password_hash)'
            );
            $stmt->execute([
                'display_name' => $displayName,
                'email' => $email,
                'password_hash' => $passwordHash,
            ]);
            return (int)$db->lastInsertId();
        }

        $stmt = $db->prepare(
            'INSERT INTO users (display_name, email, password_hash) VALUES (:display_name, :email, :password_hash)'
        );
        $stmt->bindValue(':display_name', $displayName, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':password_hash', $passwordHash, SQLITE3_TEXT);
        $stmt->execute();
        return (int)$db->lastInsertRowID();
    }
}

if (!function_exists('openbit_db_is_unique_error')) {
    function openbit_db_is_unique_error(Throwable $exception): bool
    {
        $message = strtolower($exception->getMessage());
        return str_contains($message, 'unique') || str_contains($message, 'constraint');
    }
}

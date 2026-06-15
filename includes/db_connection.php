<?php

declare(strict_types=1);

function db_env_value(string $key): ?string
{
    $value = getenv($key);
    return ($value !== false && $value !== '') ? $value : null;
}

function db_load_local_config(): ?array
{
    foreach ([__DIR__ . '/../db_config_local.php', __DIR__ . '/db_config_local.php'] as $path) {
        if (!is_readable($path)) {
            continue;
        }

        $config = require $path;
        if (!is_array($config)) {
            continue;
        }

        $host = $config['host'] ?? null;
        $db = $config['db'] ?? null;
        $user = $config['user'] ?? null;
        $pass = $config['pass'] ?? null;

        if ($host && $db && $user && $pass !== null) {
            return compact('host', 'db', 'user', 'pass');
        }
    }

    return null;
}

function db_resolve_config(): array
{
    if (db_env_value('POSTGRES_HOST') !== null) {
        return [
            'host' => db_env_value('POSTGRES_HOST') ?? 'db',
            'db' => db_env_value('POSTGRES_DB') ?? 'board_db',
            'user' => db_env_value('POSTGRES_USER') ?? 'board_user',
            'pass' => db_env_value('POSTGRES_PASSWORD') ?? 'board_pass',
        ];
    }

    $local = db_load_local_config();
    if ($local !== null) {
        return $local;
    }

    http_response_code(500);
    exit(
        'Database configuration not found. ' .
        'For Docker: set POSTGRES_* env vars. ' .
        'For university hosting: copy db_config_local.php.example to db_config_local.php and set your DB password.'
    );
}

$config = db_resolve_config();
extract($config, EXTR_SKIP);

$dbconn = pg_connect("host=$host dbname=$db user=$user password=$pass")
    or die('Could not connect: ' . pg_last_error());

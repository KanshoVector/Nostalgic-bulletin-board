<?php

declare(strict_types=1);

function db_env_value(string $key): ?string
{
    $value = getenv($key);
    return ($value !== false && $value !== '') ? $value : null;
}

function db_load_local_config(): ?array
{
    $paths = [
        __DIR__ . '/../db_config_local.php',
        __DIR__ . '/db_config_local.php',
    ];

    foreach ($paths as $path) {
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

        if ($host && $db && $user && $pass !== null && $pass !== 'YOUR_PASSWORD_HERE') {
            return compact('host', 'db', 'user', 'pass');
        }
    }

    return null;
}

function db_config_error_message(): string
{
    $root = __DIR__ . '/..';
    $localPath = $root . '/db_config_local.php';
    $examplePath = $root . '/db_config_local.php.example';

    if (!is_readable($localPath) && is_readable($examplePath)) {
        return 'db_config_local.php が見つかりません。db_config_local.php.example を db_config_local.php にコピーし、pass に大学 DB のパスワードを設定して、db.php と同じフォルダにアップロードしてください。';
    }

    if (is_readable($localPath)) {
        return 'db_config_local.php の内容が不正です。host / db / user / pass が正しく設定されているか確認してください。';
    }

    return 'db_config_local.php が見つかりません。大学サーバーでは db.php と同じフォルダに db_config_local.php を配置してください。';
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
    exit(db_config_error_message());
}

$config = db_resolve_config();
extract($config, EXTR_SKIP);

$dbconn = pg_connect("host=$host dbname=$db user=$user password=$pass")
    or die('Could not connect: ' . pg_last_error());

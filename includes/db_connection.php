<?php
/**
 * ハイブリッド DB 接続
 * 1. POSTGRES_* 環境変数あり → Docker Compose
 * 2. なし → Git 管理外の db_config_local.php を読み込み（大学サーバー）
 * 3. どちらも無し → 安全なエラー（認証情報はソースに含めない）
 */

function db_env_value(string $key): ?string
{
    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return $value;
    }
    return null;
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
        $db   = $config['db'] ?? null;
        $user = $config['user'] ?? null;
        $pass = $config['pass'] ?? null;

        if ($host !== null && $host !== '' &&
            $db !== null && $db !== '' &&
            $user !== null && $user !== '' &&
            $pass !== null) {
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
            'db'   => db_env_value('POSTGRES_DB') ?? 'board_db',
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
        'Use Docker Compose (POSTGRES_* env vars) or place db_config_local.php on the server.'
    );
}

$config = db_resolve_config();
extract($config, EXTR_SKIP);

$dbconn = pg_connect("host=$host dbname=$db user=$user password=$pass")
    or die('Could not connect: ' . pg_last_error());

try {
    $dsn = "pgsql:host=$host;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('データベース接続失敗: ' . $e->getMessage());
}

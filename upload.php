<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/upload_service.php';

$userId = require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$memo = trim($_POST['memo'] ?? '');
$lat = filter_var(trim($_POST['lat'] ?? ''), FILTER_VALIDATE_FLOAT);
$lng = filter_var(trim($_POST['lng'] ?? ''), FILTER_VALIDATE_FLOAT);
$isPublic = isset($_POST['is_public']) ? 't' : 'f';

if ($lat === false || $lng === false || $lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
    redirect_with_message('index.php', error: '位置情報が不正です。');
}

$upload = upload_store_image($_FILES['file_data'] ?? []);
if (!($upload['ok'] ?? false) && !($upload['skipped'] ?? false)) {
    redirect_with_message('index.php', error: $upload['error'] ?? '画像の保存に失敗しました。');
}

$filename = ($upload['ok'] ?? false) ? $upload['filename'] : null;
$insert = pg_query_params(
    $dbconn,
    'INSERT INTO location_diary (user_id, memo, filename, lat, lng, is_public) VALUES ($1, $2, $3, $4, $5, $6)',
    [$userId, $memo, $filename, $lat, $lng, $isPublic]
);

if (!$insert) {
    upload_delete_file($filename);
    redirect_with_message('index.php', error: '投稿に失敗しました。');
}

redirect_with_message('view.php', '投稿が完了しました。');

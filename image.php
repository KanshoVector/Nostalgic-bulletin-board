<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/upload_paths.php';

$filename = normalize_upload_filename($_GET['f'] ?? null);
$path = upload_file_path($filename);

if ($path === null || !is_file($path)) {
    http_response_code(404);
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = $finfo ? finfo_file($finfo, $path) : null;
if ($finfo) {
    finfo_close($finfo);
}

$allowed = ['image/jpeg', 'image/png', 'image/gif'];
if ($mime === null || !in_array($mime, $allowed, true)) {
    http_response_code(403);
    exit;
}

header('Content-Type: ' . $mime);
header('Cache-Control: public, max-age=86400');
header('X-Content-Type-Options: nosniff');
readfile($path);

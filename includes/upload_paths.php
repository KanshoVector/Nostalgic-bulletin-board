<?php
/**
 * uploads/ ディレクトリのパス解決
 * 相対パスの file_exists はサーバー環境で CWD が異なると失敗するため、
 * アプリルート基準の絶対パスで判定する。
 */

function uploads_base_dir(): string
{
    static $dir = null;
    if ($dir !== null) {
        return $dir;
    }

    $candidate = realpath(__DIR__ . '/../uploads');
    $dir = $candidate !== false ? $candidate : __DIR__ . '/../uploads';

    return $dir;
}

function normalize_upload_filename(?string $filename): ?string
{
    if ($filename === null) {
        return null;
    }

    $filename = trim($filename);
    if ($filename === '') {
        return null;
    }

    $filename = str_replace('\\', '/', $filename);
    if (str_starts_with($filename, './')) {
        $filename = substr($filename, 2);
    }

    if (str_starts_with($filename, 'uploads/')) {
        $filename = substr($filename, strlen('uploads/'));
    }

    $basename = basename($filename);
    return $basename !== '' ? $basename : null;
}

function upload_file_path(?string $filename): ?string
{
    $normalized = normalize_upload_filename($filename);
    if ($normalized === null) {
        return null;
    }

    return uploads_base_dir() . DIRECTORY_SEPARATOR . $normalized;
}

function upload_file_exists(?string $filename): bool
{
    $path = upload_file_path($filename);
    return $path !== null && is_file($path);
}

function upload_public_url(?string $filename): ?string
{
    $normalized = normalize_upload_filename($filename);
    if ($normalized === null) {
        return null;
    }

    $segments = explode('/', $normalized);
    $encoded = array_map('rawurlencode', $segments);

    return 'uploads/' . implode('/', $encoded);
}

function upload_relative_path(?string $filename): ?string
{
    $normalized = normalize_upload_filename($filename);
    if ($normalized === null) {
        return null;
    }

    return 'uploads/' . $normalized;
}

function ensure_uploads_dir(): void
{
    $dir = uploads_base_dir();
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

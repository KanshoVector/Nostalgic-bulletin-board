<?php

declare(strict_types=1);

function upload_public_url(?string $filename): ?string
{
    $normalized = normalize_upload_filename($filename);
    if ($normalized === null) {
        return null;
    }

    return 'image.php?f=' . rawurlencode($normalized);
}

function upload_file_exists(?string $filename): bool
{
    $path = upload_file_path($filename);
    return $path !== null && is_readable($path) && is_file($path);
}

function ensure_uploads_dir(): void
{
    $dir = uploads_base_dir();
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

function uploads_base_dir(): string
{
    static $dir = null;
    if ($dir !== null) {
        return $dir;
    }

    $resolved = realpath(__DIR__ . '/../uploads');
    $dir = $resolved !== false ? $resolved : __DIR__ . '/../uploads';

    return $dir;
}

function normalize_upload_filename(?string $filename): ?string
{
    if ($filename === null) {
        return null;
    }

    $filename = trim(str_replace('\\', '/', $filename));
    if ($filename === '') {
        return null;
    }

    if (str_starts_with($filename, './')) {
        $filename = substr($filename, 2);
    }
    if (str_starts_with($filename, 'uploads/')) {
        $filename = substr($filename, 8);
    }

    $basename = basename($filename);
    return $basename !== '' && $basename !== '.' ? $basename : null;
}

function upload_file_path(?string $filename): ?string
{
    $normalized = normalize_upload_filename($filename);
    if ($normalized === null) {
        return null;
    }

    return uploads_base_dir() . DIRECTORY_SEPARATOR . $normalized;
}

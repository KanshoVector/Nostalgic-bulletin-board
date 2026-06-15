<?php

declare(strict_types=1);

function upload_store_image(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return ['ok' => false, 'skipped' => true];
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'ファイルのアップロードに失敗しました。'];
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : false;
    if ($finfo) {
        finfo_close($finfo);
    }

    if (!$mime || !isset($allowed[$mime])) {
        return ['ok' => false, 'error' => 'JPEG / PNG / GIF のみアップロードできます。'];
    }

    if (($file['size'] ?? 0) > 5 * 1024 * 1024) {
        return ['ok' => false, 'error' => 'ファイルサイズは 5MB までです。'];
    }

    ensure_uploads_dir();
    $filename = time() . '_' . getmypid() . '.' . $allowed[$mime];
    $destination = upload_file_path($filename);

    if ($destination === null || !move_uploaded_file($file['tmp_name'], $destination)) {
        return ['ok' => false, 'error' => 'ファイルの保存に失敗しました。'];
    }

    return ['ok' => true, 'filename' => $filename];
}

function upload_delete_file(?string $filename): void
{
    if ($filename === null || !upload_file_exists($filename)) {
        return;
    }

    $path = upload_file_path($filename);
    if ($path !== null) {
        unlink($path);
    }
}

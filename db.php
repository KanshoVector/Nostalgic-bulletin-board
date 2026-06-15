<?php
// セッション開始（すべてのページで必要）
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once __DIR__ . '/includes/db_connection.php';
require_once __DIR__ . '/includes/upload_paths.php';
?>

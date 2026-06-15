<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/view_controller.php';

$userId = require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_with_message('view.php', error: '不正なリクエストです。');
}

$postId = filter_input(INPUT_POST, 'post_id', FILTER_VALIDATE_INT);
if (!$postId) {
    redirect_with_message('view.php', error: '投稿 ID が無効です。');
}

view_delete_post($dbconn, $userId, $postId);

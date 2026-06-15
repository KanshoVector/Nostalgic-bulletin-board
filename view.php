<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/view_controller.php';

$userId = require_login();
view_handle_post($dbconn, $userId);

$mode = (isset($_GET['mode']) && $_GET['mode'] === 'public') ? 'public' : 'my_posts';
$search = trim($_GET['search'] ?? '');
$editId = isset($_GET['edit_id']) ? (int) $_GET['edit_id'] : 0;
$message = flash_take('message');
$error = flash_take('error');
$ui = layout_classes();

if (isset($_GET['message'])) {
    $message = trim(urldecode((string) $_GET['message']));
}
if (isset($_GET['error'])) {
    $error = trim(urldecode((string) $_GET['error']));
}

$postToEdit = view_load_post_for_edit($dbconn, $userId, $editId);
if ($editId > 0 && $postToEdit === null) {
    redirect_with_message('view.php', error: '指定された投稿が見つからないか、編集権限がありません。');
}

$data = view_load_posts($dbconn, $userId, $mode, $search, (int) ($_GET['page'] ?? 1), 10);
$posts = $data['posts'];
$totalPages = $data['total_pages'];
$currentPage = $data['current_page'];

layout_start('投稿一覧');
$links = [
    ['href' => 'index.php', 'label' => '← 投稿へ戻る'],
];
if ($editId > 0 && $postToEdit) {
    $links[] = ['href' => 'view.php', 'label' => '← 投稿一覧へ'];
}
$links[] = ['href' => 'logout.php', 'label' => 'ログアウト', 'class' => 'rounded-lg bg-slate-700 px-3 py-2 text-white transition hover:bg-slate-800'];
render_app_header('あなたの投稿', $links);
render_flash_messages($message, $error);

if ($editId > 0 && $postToEdit) {
    require __DIR__ . '/templates/post_edit_form.php';
} else {
    require __DIR__ . '/templates/post_list.php';
}

render_app_footer(true);

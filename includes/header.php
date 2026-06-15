<?php
if (!defined('VIEW_FILE_INCLUDED')) {
    die('直接アクセスは許可されていません。');
}

$page_title = '投稿一覧・編集';
require_once __DIR__ . '/tailwind_head.php';
?>
<div class="mx-auto min-h-screen max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
  <header class="mb-8 flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <p class="text-sm font-medium uppercase tracking-wide text-teal-700">Nostalgic Board</p>
      <h1 class="text-2xl font-bold text-slate-800">あなたの投稿</h1>
    </div>
    <nav class="flex flex-wrap items-center gap-3 text-sm font-medium">
      <a href="index.php" class="rounded-lg px-3 py-2 text-slate-600 transition hover:bg-white hover:text-slate-900">← 投稿へ戻る</a>
      <?php if (isset($post_id_to_edit) && $post_id_to_edit > 0): ?>
        <a href="view.php" class="rounded-lg px-3 py-2 text-slate-600 transition hover:bg-white hover:text-slate-900">← 投稿一覧へ</a>
      <?php endif; ?>
      <a href="logout.php" class="rounded-lg bg-slate-700 px-3 py-2 text-white transition hover:bg-slate-800">ログアウト</a>
    </nav>
  </header>
  <main>

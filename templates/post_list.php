<?php

declare(strict_types=1);

$ui = layout_classes();
$modeQuery = $search !== '' ? '&search=' . urlencode($search) : '';
?>
<div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
  <div>
    <h2 class="text-xl font-semibold text-slate-800">投稿一覧</h2>
    <p class="mt-1 text-sm text-slate-600">自分の投稿や公開された投稿を確認できます。</p>
  </div>
  <div class="flex flex-wrap gap-2">
    <a href="?mode=my_posts<?= h($modeQuery) ?>" class="<?= $mode === 'my_posts' ? 'rounded-lg bg-slate-700 px-4 py-2 text-sm font-medium text-white' : h($ui['button_secondary']) ?>">自分の投稿</a>
    <a href="?mode=public<?= h($modeQuery) ?>" class="<?= $mode === 'public' ? 'rounded-lg bg-slate-700 px-4 py-2 text-sm font-medium text-white' : h($ui['button_secondary']) ?>">公開された投稿</a>
  </div>
</div>

<form method="get" class="mb-6 flex flex-wrap items-center gap-2">
  <input type="hidden" name="mode" value="<?= h($mode) ?>">
  <input type="text" name="search" value="<?= h($search) ?>" placeholder="キーワード検索" class="w-full min-w-[12rem] rounded-lg border border-slate-300 px-3 py-2 text-sm sm:w-52">
  <button type="submit" class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-medium text-white hover:bg-teal-800">検索</button>
  <?php if ($search !== ''): ?>
    <a href="?mode=<?= h($mode) ?>" class="<?= h($ui['button_secondary']) ?>">クリア</a>
  <?php endif; ?>
</form>

<?php if (!$posts): ?>
  <div class="rounded-2xl border border-dashed border-slate-300 bg-white/70 px-6 py-12 text-center text-slate-600">
    投稿が見つかりませんでした。
  </div>
<?php else: ?>
  <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg shadow-slate-200/50">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-700 text-white">
          <tr>
            <th class="px-4 py-3 text-left">ID</th>
            <th class="px-4 py-3 text-left">メモ</th>
            <th class="px-4 py-3 text-left">画像</th>
            <th class="px-4 py-3 text-left">日時</th>
            <th class="px-4 py-3 text-left">公開</th>
            <th class="px-4 py-3 text-left">操作</th>
            <th class="min-w-[18rem] px-4 py-3 text-left">コメント</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php foreach ($posts as $post):
            $imageUrl = upload_public_url($post['filename'] ?? null);
          ?>
            <tr class="align-top hover:bg-slate-50/80">
              <td class="px-4 py-4 text-center font-medium"><?= h((string) $post['id']) ?></td>
              <td class="max-w-xs px-4 py-4">
                <div class="whitespace-pre-wrap break-words"><?= nl2br(h($post['memo'])) ?></div>
                <p class="mt-2 text-xs text-slate-500">投稿者: <?= h($post['author_username']) ?></p>
              </td>
              <td class="px-4 py-4 text-center">
                <?php if ($imageUrl): ?>
                  <img src="<?= h($imageUrl) ?>" data-src="<?= h($imageUrl) ?>" alt="" class="post-image mx-auto max-h-24 max-w-[7rem] cursor-pointer rounded-lg border border-slate-200 object-cover shadow-sm transition hover:scale-105">
                <?php else: ?>
                  <span class="text-slate-400">なし</span>
                <?php endif; ?>
              </td>
              <td class="whitespace-nowrap px-4 py-4 text-center text-slate-600"><?= h($post['created_at']) ?></td>
              <td class="px-4 py-4 text-center">
                <?php if (pg_bool_is_true($post['is_public'])): ?>
                  <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">公開</span>
                <?php else: ?>
                  <span class="rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700">非公開</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-4">
                <?php if ((int) $post['author_id'] === $userId): ?>
                  <div class="flex flex-col gap-2">
                    <a href="view.php?edit_id=<?= h((string) $post['id']) ?>" class="rounded-lg bg-slate-600 px-3 py-1.5 text-center text-xs font-medium text-white hover:bg-slate-700">編集</a>
                    <form action="delete.php" method="post" onsubmit="return confirm('この投稿を削除しますか？');">
                      <input type="hidden" name="post_id" value="<?= h((string) $post['id']) ?>">
                      <button type="submit" class="w-full rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700">削除</button>
                    </form>
                  </div>
                <?php else: ?>
                  <span class="text-slate-400">-</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-4">
                <?php
                $comments = view_load_comments($dbconn, (int) $post['id']);
                require __DIR__ . '/comment_section.php';
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php if ($totalPages > 1): ?>
    <nav class="mt-6 flex flex-wrap justify-center gap-2">
      <?php for ($i = 1; $i <= $totalPages; $i++):
        $query = http_build_query(array_filter([
            'page' => $i,
            'mode' => $mode,
            'search' => $search !== '' ? $search : null,
        ]));
      ?>
        <?php if ($i === $currentPage): ?>
          <span class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg bg-slate-700 px-3 text-sm font-semibold text-white"><?= $i ?></span>
        <?php else: ?>
          <a href="?<?= h($query) ?>" class="<?= h($ui['button_secondary']) ?> h-9 min-w-9"><?= $i ?></a>
        <?php endif; ?>
      <?php endfor; ?>
    </nav>
  <?php endif; ?>
<?php endif; ?>

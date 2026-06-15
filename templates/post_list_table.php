<?php
if (!defined('VIEW_FILE_INCLUDED')) {
    die('直接アクセスは許可されていません。');
}
?>
<?php if ($posts): ?>
  <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg shadow-slate-200/50">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-700 text-white">
          <tr>
            <th class="px-4 py-3 text-left font-semibold">ID</th>
            <th class="px-4 py-3 text-left font-semibold">メモ</th>
            <th class="px-4 py-3 text-left font-semibold">画像</th>
            <th class="px-4 py-3 text-left font-semibold">日時</th>
            <th class="px-4 py-3 text-left font-semibold">公開状態</th>
            <th class="px-4 py-3 text-left font-semibold">ナビ</th>
            <th class="px-4 py-3 text-left font-semibold">操作</th>
            <th class="min-w-[18rem] px-4 py-3 text-left font-semibold">コメント</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
          <?php foreach ($posts as $post): ?>
            <tr class="align-top transition hover:bg-slate-50/80">
              <td class="px-4 py-4 text-center font-medium text-slate-700"><?php echo htmlspecialchars($post['id']); ?></td>
              <td class="max-w-xs px-4 py-4 text-slate-700">
                <div class="whitespace-pre-wrap break-words"><?php echo nl2br(htmlspecialchars($post['memo'])); ?></div>
                <div class="mt-2 text-xs text-slate-500">投稿者: <?php echo htmlspecialchars($post['author_username']); ?></div>
              </td>
              <td class="px-4 py-4 text-center">
                <?php
                $image_url = upload_public_url($post['filename'] ?? null);
                if ($image_url !== null && upload_file_exists($post['filename'])):
                ?>
                  <img src="<?php echo htmlspecialchars($image_url, ENT_QUOTES, 'UTF-8'); ?>"
                       data-src="<?php echo htmlspecialchars($image_url, ENT_QUOTES, 'UTF-8'); ?>"
                       alt="投稿画像"
                       class="post-image mx-auto max-h-24 max-w-[7rem] cursor-pointer rounded-lg border border-slate-200 object-cover shadow-sm transition hover:scale-105">
                <?php else: ?>
                  <span class="text-slate-400">画像なし</span>
                <?php endif; ?>
              </td>
              <td class="whitespace-nowrap px-4 py-4 text-center text-slate-600"><?php echo htmlspecialchars($post['created_at']); ?></td>
              <td class="px-4 py-4 text-center">
                <?php if ($post['is_public'] === 't'): ?>
                  <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">公開</span>
                <?php else: ?>
                  <span class="inline-flex rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700">非公開</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-4 text-center">
                <a href="https://maps.apple.com/maps?q=<?php echo htmlspecialchars($post['lat']); ?>,<?php echo htmlspecialchars($post['lng']); ?>"
                   target="_blank" rel="noopener noreferrer"
                   class="font-medium text-teal-700 hover:text-teal-800 hover:underline">ナビ起動</a>
              </td>
              <td class="px-4 py-4">
                <?php if ($post['author_id'] == $user_id): ?>
                  <div class="flex flex-col gap-2">
                    <a href="view.php?edit_id=<?php echo htmlspecialchars($post['id']); ?>"
                       class="inline-flex items-center justify-center rounded-lg bg-slate-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-slate-700">編集</a>
                    <form action="delete.php" method="post" onsubmit="return confirm('本当にこの投稿を削除しますか？');">
                      <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['id']); ?>">
                      <button type="submit" class="delete-button inline-flex w-full items-center justify-center rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-white transition hover:bg-red-700">削除</button>
                    </form>
                  </div>
                <?php else: ?>
                  <span class="text-slate-400">-</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-4">
                <?php
                $current_post_for_comments = $post;
                require 'templates/comment_section.php';
                ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <?php if ($total_pages > 1): ?>
    <nav class="pagination mt-6 flex flex-wrap justify-center gap-2">
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <?php
          $page_url_params = [
              'page' => $i,
              'mode' => $display_mode,
          ];
          if ($search_query) {
              $page_url_params['search'] = $search_query;
          }
          $page_url = '?' . http_build_query($page_url_params);
        ?>
        <?php if ($i == $current_page): ?>
          <span class="current-page inline-flex h-9 min-w-9 items-center justify-center rounded-lg bg-slate-700 px-3 text-sm font-semibold text-white"><?php echo $i; ?></span>
        <?php else: ?>
          <a href="<?php echo htmlspecialchars($page_url, ENT_QUOTES, 'UTF-8'); ?>"
             class="inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-300 bg-white px-3 text-sm font-medium text-slate-700 transition hover:bg-slate-50"><?php echo $i; ?></a>
        <?php endif; ?>
      <?php endfor; ?>
    </nav>
  <?php endif; ?>
<?php else: ?>
  <div class="rounded-2xl border border-dashed border-slate-300 bg-white/70 px-6 py-12 text-center text-slate-600">
    まだ投稿がありません。または、検索条件に一致する投稿が見つかりませんでした。
  </div>
<?php endif; ?>

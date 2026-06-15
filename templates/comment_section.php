<?php
if (!defined('VIEW_FILE_INCLUDED')) {
    die('直接アクセスは許可されていません。');
}

if (!isset($current_post_for_comments)) {
    die('コメントセクションには投稿データが必要です。');
}

$post_id = $current_post_for_comments['id'];
?>
<div class="comments-section space-y-4">
  <h4 class="text-sm font-semibold text-slate-800">コメント</h4>

  <ul class="comment-list space-y-3">
    <?php
    $comments_sql = "
        SELECT c.id, c.comment_text, c.created_at, u.username AS comment_author_username, u.id AS comment_author_id
        FROM comments c
        JOIN users u ON c.user_id = u.id
        WHERE c.post_id = $1
        ORDER BY c.created_at ASC;
    ";
    $comments_result = pg_query_params($dbconn, $comments_sql, [$post_id]);
    $comments = pg_fetch_all($comments_result);

    if ($comments):
        foreach ($comments as $comment):
    ?>
      <li class="comment-item rounded-xl border border-slate-200 bg-slate-50 p-3">
        <div class="flex flex-wrap items-center gap-2 text-xs">
          <span class="comment-author font-semibold text-slate-800"><?php echo htmlspecialchars($comment['comment_author_username']); ?></span>
          <span class="comment-date text-slate-500"><?php echo htmlspecialchars($comment['created_at']); ?></span>
        </div>
        <div class="comment-text comment-text-<?php echo htmlspecialchars($comment['id']); ?> mt-2 whitespace-pre-wrap break-words text-sm text-slate-700">
          <?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?>
        </div>

        <?php if ($comment['comment_author_id'] == $user_id): ?>
          <div class="comment-actions mt-3 flex flex-wrap gap-2">
            <button type="button" class="edit-comment-button text-xs font-medium text-teal-700 hover:underline"
                    data-comment-id="<?php echo htmlspecialchars($comment['id']); ?>"
                    data-current-text="<?php echo htmlspecialchars($comment['comment_text'], ENT_QUOTES, 'UTF-8'); ?>">編集</button>
            <form method="post" class="inline" onsubmit="return confirm('このコメントを本当に削除しますか？');">
              <input type="hidden" name="action" value="delete_comment">
              <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['id']); ?>">
              <button type="submit" class="delete-comment-button text-xs font-medium text-red-600 hover:underline">削除</button>
            </form>
          </div>
          <div class="comment-edit-form comment-edit-form-<?php echo htmlspecialchars($comment['id']); ?> mt-3 hidden border-t border-dashed border-slate-200 pt-3">
            <form method="post" class="space-y-2">
              <input type="hidden" name="action" value="edit_comment">
              <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($comment['id']); ?>">
              <textarea name="new_comment_text" required class="<?php echo $input_class; ?> min-h-[4rem] resize-y"><?php echo htmlspecialchars($comment['comment_text'], ENT_QUOTES, 'UTF-8'); ?></textarea>
              <div class="flex flex-wrap gap-2">
                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700">更新</button>
                <button type="button" class="cancel-button rounded-lg bg-slate-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-600"
                        data-comment-id="<?php echo htmlspecialchars($comment['id']); ?>">キャンセル</button>
              </div>
            </form>
          </div>
        <?php endif; ?>
      </li>
    <?php endforeach;
    else: ?>
      <li class="text-sm text-slate-500">まだコメントはありません。</li>
    <?php endif; ?>
  </ul>

  <form method="post" class="comment-form space-y-2 border-t border-slate-200 pt-3">
    <input type="hidden" name="action" value="add_comment">
    <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post_id); ?>">
    <textarea name="comment_text" placeholder="コメントを入力..." required class="<?php echo $input_class; ?> min-h-[4rem] resize-y"></textarea>
    <button type="submit" class="comment-submit-button rounded-lg bg-emerald-600 px-4 py-2 text-xs font-medium text-white transition hover:bg-emerald-700">コメントする</button>
  </form>
</div>

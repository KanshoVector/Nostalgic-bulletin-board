<?php

declare(strict_types=1);

$ui = layout_classes();
?>
<div class="space-y-4">
  <h4 class="text-sm font-semibold text-slate-800">コメント</h4>
  <ul class="space-y-3">
    <?php if ($comments): ?>
      <?php foreach ($comments as $comment): ?>
        <li class="rounded-xl border border-slate-200 bg-slate-50 p-3">
          <div class="flex flex-wrap items-center gap-2 text-xs">
            <span class="font-semibold text-slate-800"><?= h($comment['comment_author_username']) ?></span>
            <span class="text-slate-500"><?= h($comment['created_at']) ?></span>
          </div>
          <div class="comment-text comment-text-<?= h((string) $comment['id']) ?> mt-2 whitespace-pre-wrap break-words text-sm text-slate-700">
            <?= nl2br(h($comment['comment_text'])) ?>
          </div>
          <?php if ((int) $comment['comment_author_id'] === $userId): ?>
            <div class="mt-3 flex flex-wrap gap-2">
              <button type="button" class="edit-comment-button text-xs font-medium text-teal-700 hover:underline" data-comment-id="<?= h((string) $comment['id']) ?>">編集</button>
              <form method="post" class="inline" onsubmit="return confirm('このコメントを削除しますか？');">
                <input type="hidden" name="action" value="delete_comment">
                <input type="hidden" name="comment_id" value="<?= h((string) $comment['id']) ?>">
                <button type="submit" class="text-xs font-medium text-red-600 hover:underline">削除</button>
              </form>
            </div>
            <div class="comment-edit-form comment-edit-form-<?= h((string) $comment['id']) ?> mt-3 hidden border-t border-dashed border-slate-200 pt-3">
              <form method="post" class="space-y-2">
                <input type="hidden" name="action" value="edit_comment">
                <input type="hidden" name="comment_id" value="<?= h((string) $comment['id']) ?>">
                <textarea name="new_comment_text" required class="<?= h($ui['input']) ?> min-h-[4rem] resize-y"><?= h($comment['comment_text']) ?></textarea>
                <div class="flex gap-2">
                  <button type="submit" class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:bg-blue-700">更新</button>
                  <button type="button" class="cancel-button rounded-lg bg-slate-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-slate-600" data-comment-id="<?= h((string) $comment['id']) ?>">キャンセル</button>
                </div>
              </form>
            </div>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    <?php else: ?>
      <li class="text-sm text-slate-500">まだコメントはありません。</li>
    <?php endif; ?>
  </ul>
  <form method="post" class="space-y-2 border-t border-slate-200 pt-3">
    <input type="hidden" name="action" value="add_comment">
    <input type="hidden" name="post_id" value="<?= h((string) $post['id']) ?>">
    <textarea name="comment_text" placeholder="コメントを入力" required class="<?= h($ui['input']) ?> min-h-[4rem] resize-y"></textarea>
    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-medium text-white hover:bg-emerald-700">コメントする</button>
  </form>
</div>

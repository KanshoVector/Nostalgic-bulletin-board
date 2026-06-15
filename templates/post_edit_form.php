<?php

declare(strict_types=1);

$ui = layout_classes();
$imageUrl = upload_public_url($postToEdit['filename'] ?? null);
?>
<div class="mx-auto max-w-2xl">
  <div class="mb-6">
    <h2 class="text-xl font-semibold text-slate-800">投稿内容を編集</h2>
  </div>
  <form method="post" enctype="multipart/form-data" class="<?= h($ui['card']) ?> space-y-5">
    <input type="hidden" name="action" value="update_post">
    <input type="hidden" name="edit_id" value="<?= h((string) $editId) ?>">
    <input type="hidden" name="old_image_filename" value="<?= h($postToEdit['filename'] ?? '') ?>">
    <div>
      <label for="memo" class="mb-1.5 block text-sm font-medium text-slate-700">メモ</label>
      <textarea name="memo" id="memo" rows="4" class="<?= h($ui['input']) ?> resize-y"><?= h($postToEdit['memo']) ?></textarea>
    </div>
    <div>
      <p class="mb-2 text-sm font-medium text-slate-700">現在の画像</p>
      <?php if ($imageUrl): ?>
        <img src="<?= h($imageUrl) ?>" alt="" class="mx-auto max-h-48 rounded-xl border border-slate-200 object-contain shadow-sm">
      <?php else: ?>
        <p class="text-center text-sm text-slate-500">画像はありません。</p>
      <?php endif; ?>
      <label for="new_file_data" class="mb-1.5 mt-4 block text-sm font-medium text-slate-700">新しい画像（任意）</label>
      <input type="file" name="new_file_data" id="new_file_data" accept="image/jpeg,image/png,image/gif" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
      <div>
        <label for="lat_edit" class="mb-1.5 block text-sm font-medium text-slate-700">緯度</label>
        <input type="text" name="lat" id="lat_edit" value="<?= h((string) $postToEdit['lat']) ?>" required class="<?= h($ui['input']) ?>">
      </div>
      <div>
        <label for="lng_edit" class="mb-1.5 block text-sm font-medium text-slate-700">経度</label>
        <input type="text" name="lng" id="lng_edit" value="<?= h((string) $postToEdit['lng']) ?>" required class="<?= h($ui['input']) ?>">
      </div>
    </div>
    <label class="flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
      <input type="checkbox" name="is_public" id="is_public_edit" <?= pg_bool_is_true($postToEdit['is_public']) ? 'checked' : '' ?> class="mt-0.5 h-4 w-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
      <span>この投稿を公開する</span>
    </label>
    <button type="submit" class="<?= h($ui['button']) ?>">更新する</button>
  </form>
</div>

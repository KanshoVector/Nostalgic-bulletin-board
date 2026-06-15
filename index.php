<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/layout.php';

require_login();
$message = flash_take('message');
$error = flash_take('error');
$ui = layout_classes();

layout_start('位置メモ投稿');
render_app_header('マイメモリー', [
    ['href' => 'view.php', 'label' => '投稿一覧を見る'],
    ['href' => 'logout.php', 'label' => 'ログアウト', 'class' => 'rounded-lg bg-slate-700 px-3 py-2 text-white transition hover:bg-slate-800'],
]);
render_flash_messages($message, $error);
?>
<div class="mx-auto max-w-2xl">
  <div class="mb-6">
    <h2 class="text-xl font-semibold text-slate-800">位置・メモ・画像を投稿</h2>
    <p class="mt-1 text-sm text-slate-600">現在地と思い出を記録し、公開設定も選べます。</p>
  </div>
  <form action="upload.php" method="post" enctype="multipart/form-data" class="<?= h($ui['card']) ?> space-y-5">
    <div>
      <label for="memo" class="mb-1.5 block text-sm font-medium text-slate-700">メモ（任意）</label>
      <textarea name="memo" id="memo" rows="4" placeholder="今日の出来事をメモ" class="<?= h($ui['input']) ?> resize-y"></textarea>
    </div>
    <div>
      <label for="file_data" class="mb-1.5 block text-sm font-medium text-slate-700">画像（任意）</label>
      <input type="file" name="file_data" id="file_data" accept="image/jpeg,image/png,image/gif" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
    </div>
    <label class="flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
      <input type="checkbox" name="is_public" id="is_public" value="1" checked class="mt-0.5 h-4 w-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
      <span>この投稿を公開する</span>
    </label>
    <input type="hidden" name="lat" id="lat" value="0.0">
    <input type="hidden" name="lng" id="lng" value="0.0">
    <button type="submit" class="<?= h($ui['button']) ?>">投稿する</button>
  </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const lat = document.getElementById('lat');
  const lng = document.getElementById('lng');
  if (!navigator.geolocation || !lat || !lng) return;
  navigator.geolocation.getCurrentPosition(
    (pos) => { lat.value = pos.coords.latitude; lng.value = pos.coords.longitude; },
    () => {},
    { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
  );
});
</script>
<?php
render_app_footer(false);

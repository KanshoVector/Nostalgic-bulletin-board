<?php
if (!defined('VIEW_FILE_INCLUDED')) {
    die('直接アクセスは許可されていません。');
}

if (!isset($post_to_edit)) {
    die('編集フォームには投稿データが必要です。');
}
?>
<div class="mb-6">
  <h2 class="text-xl font-semibold text-slate-800">投稿内容を編集</h2>
  <p class="mt-1 text-sm text-slate-600">メモ・画像・位置情報・公開設定を更新できます。</p>
</div>

<div class="<?php echo $card_class; ?> max-w-2xl">
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const latInput = document.getElementById('lat_edit');
    const lngInput = document.getElementById('lng_edit');

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        function(pos) {
          latInput.value = pos.coords.latitude;
          lngInput.value = pos.coords.longitude;
        },
        function(error) {
          let errorMessage = '位置情報の取得に失敗しました。';
          switch (error.code) {
            case error.PERMISSION_DENIED:
              errorMessage = '位置情報の利用が許可されませんでした。手動で入力してください。';
              break;
            case error.POSITION_UNAVAILABLE:
              errorMessage = '位置情報の取得に失敗しました。';
              break;
            case error.TIMEOUT:
              errorMessage = '位置情報の取得がタイムアウトしました。';
              break;
          }
          alert(errorMessage + ' 現在の緯度: ' + latInput.value + ', 経度: ' + lngInput.value);
        }
      );
    } else {
      alert('お使いのブラウザは位置情報に対応していません。');
    }
  });
  </script>

  <form method="post" enctype="multipart/form-data" class="space-y-5">
    <input type="hidden" name="action" value="update_post">
    <input type="hidden" name="old_image_filename" value="<?php echo htmlspecialchars($post_to_edit['filename'], ENT_QUOTES, 'UTF-8'); ?>">

    <div>
      <label for="memo" class="mb-1.5 block text-sm font-medium text-slate-700">メモ</label>
      <textarea name="memo" id="memo" rows="4" placeholder="今日の出来事をメモ" class="<?php echo $input_class; ?> resize-y"><?php echo htmlspecialchars($post_to_edit['memo'], ENT_QUOTES, 'UTF-8'); ?></textarea>
    </div>

    <div>
      <p class="mb-2 text-sm font-medium text-slate-700">現在の画像</p>
      <?php if (!empty($post_to_edit['filename']) && file_exists('uploads/' . $post_to_edit['filename'])): ?>
        <img src="uploads/<?php echo htmlspecialchars($post_to_edit['filename'], ENT_QUOTES, 'UTF-8'); ?>"
             alt="現在の画像"
             class="mx-auto max-h-48 rounded-xl border border-slate-200 object-contain shadow-sm">
      <?php else: ?>
        <p class="text-center text-sm text-slate-500">画像はありません。</p>
      <?php endif; ?>
      <label for="new_file_data" class="mb-1.5 mt-4 block text-sm font-medium text-slate-700">新しい画像（変更する場合のみ）</label>
      <input type="file" name="new_file_data" id="new_file_data" accept="image/*"
             class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
      <div>
        <label for="lat_edit" class="mb-1.5 block text-sm font-medium text-slate-700">緯度</label>
        <input type="text" name="lat" id="lat_edit" value="<?php echo htmlspecialchars((float)$post_to_edit['lat'], ENT_QUOTES, 'UTF-8'); ?>" required class="<?php echo $input_class; ?>">
      </div>
      <div>
        <label for="lng_edit" class="mb-1.5 block text-sm font-medium text-slate-700">経度</label>
        <input type="text" name="lng" id="lng_edit" value="<?php echo htmlspecialchars((float)$post_to_edit['lng'], ENT_QUOTES, 'UTF-8'); ?>" required class="<?php echo $input_class; ?>">
      </div>
    </div>

    <label class="flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
      <input type="checkbox" id="is_public_edit" name="is_public" <?php echo $post_to_edit['is_public'] ? 'checked' : ''; ?> class="mt-0.5 h-4 w-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
      <span>この投稿を公開する</span>
    </label>

    <button type="submit" class="<?php echo $btn_primary; ?>">更新する</button>
  </form>
</div>

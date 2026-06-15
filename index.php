<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("db.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$message = '';
$error = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

$page_title = '位置メモ投稿';
require_once __DIR__ . '/includes/tailwind_head.php';
?>

<div class="mx-auto min-h-screen max-w-3xl px-4 py-6 sm:px-6 lg:px-8">
  <header class="mb-8 flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <p class="text-sm font-medium uppercase tracking-wide text-teal-700">Nostalgic Board</p>
      <h1 class="text-2xl font-bold text-slate-800">マイメモリー</h1>
    </div>
    <nav class="flex flex-wrap items-center gap-3 text-sm font-medium">
      <a href="view.php" class="rounded-lg px-3 py-2 text-slate-600 transition hover:bg-white hover:text-slate-900">投稿一覧を見る</a>
      <a href="logout.php" class="rounded-lg bg-slate-700 px-3 py-2 text-white transition hover:bg-slate-800">ログアウト</a>
    </nav>
  </header>

  <main>
    <div class="mb-6 text-center sm:text-left">
      <h2 class="text-xl font-semibold text-slate-800">位置・メモ・画像を投稿</h2>
      <p class="mt-1 text-sm text-slate-600">現在地と思い出を記録して、公開設定も選べます。</p>
    </div>

    <?php if (!empty($message)): ?>
      <div class="message-box success-message mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
        <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
      <div class="message-box error-message mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <form action="upload.php" method="post" enctype="multipart/form-data" class="<?php echo $card_class; ?> space-y-5">
      <div>
        <label for="memo" class="mb-1.5 block text-sm font-medium text-slate-700">メモ（任意）</label>
        <textarea name="memo" id="memo" rows="4" placeholder="今日の出来事をメモ" class="<?php echo $input_class; ?> resize-y"></textarea>
      </div>
      <div>
        <label for="file_data" class="mb-1.5 block text-sm font-medium text-slate-700">画像（任意）</label>
        <input type="file" name="file_data" id="file_data" accept="image/*" class="block w-full text-sm text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-medium file:text-slate-700 hover:file:bg-slate-200">
      </div>
      <label class="flex items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700">
        <input type="checkbox" name="is_public" id="is_public" value="1" checked class="mt-0.5 h-4 w-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500">
        <span>この投稿を公開する（他のユーザーも閲覧可能になります）</span>
      </label>
      <input type="hidden" name="lat" id="lat" value="0.0">
      <input type="hidden" name="lng" id="lng" value="0.0">
      <button type="submit" class="<?php echo $btn_primary; ?>">投稿する</button>
    </form>
  </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const latInput = document.getElementById('lat');
  const lngInput = document.getElementById('lng');

  latInput.value = '0.0';
  lngInput.value = '0.0';

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
            errorMessage = 'ブラウザで位置情報の利用が許可されませんでした。ブラウザの設定をご確認ください。';
            break;
          case error.POSITION_UNAVAILABLE:
            errorMessage = '位置情報が利用できませんでした。';
            break;
          case error.TIMEOUT:
            errorMessage = '位置情報の取得がタイムアウトしました。';
            break;
        }
        alert(errorMessage + '\n（緯度・経度は初期値0.0で投稿されます）');
      },
      {
        enableHighAccuracy: true,
        timeout: 5000,
        maximumAge: 0
      }
    );
  } else {
    alert('お使いのブラウザは位置情報に対応していません。\n（緯度・経度は初期値0.0で投稿されます）');
  }
});
</script>
</body>
</html>

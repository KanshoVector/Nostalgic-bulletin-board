<?php
require_once("db.php");

$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = trim($_POST['password'] ?? '');

  if ($username === '' || $password === '') {
    $error = 'ユーザー名とパスワードを入力してください。';
  } else {
    $res = pg_query_params($dbconn, "SELECT id FROM users WHERE username = $1", [$username]);
    if (pg_num_rows($res) > 0) {
      $error = 'そのユーザー名は既に使われています。';
    } else {
      pg_query_params($dbconn, "INSERT INTO users (username, password) VALUES ($1, $2)", [$username, $password])
        or die('登録失敗: ' . pg_last_error());
      $success = '登録が完了しました！';
    }
  }
}

$page_title = '新規登録';
$body_class = 'flex min-h-full items-center justify-center bg-gradient-to-br from-stone-100 via-slate-50 to-teal-50 px-4 py-12 text-slate-800 antialiased';
require_once __DIR__ . '/includes/tailwind_head.php';
?>

<div class="w-full max-w-md">
  <div class="mb-8 text-center">
    <p class="text-sm font-semibold uppercase tracking-wider text-teal-700">Nostalgic Board</p>
    <h1 class="mt-2 text-3xl font-bold text-slate-800">新規登録</h1>
    <p class="mt-2 text-sm text-slate-600">はじめての方はこちらから</p>
  </div>

  <div class="<?php echo $card_class; ?>">
    <?php if ($error !== null): ?>
      <div class="message-box error-message mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php elseif ($success !== null): ?>
      <div class="message-box success-message rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        <?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
        <a href="login.php" class="ml-1 font-semibold text-teal-700 hover:underline">ログイン</a>
      </div>
    <?php else: ?>
      <form method="post" class="space-y-5">
        <div>
          <label for="username" class="mb-1.5 block text-sm font-medium text-slate-700">ユーザー名</label>
          <input type="text" name="username" id="username" required class="<?php echo $input_class; ?>">
        </div>
        <div>
          <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">パスワード</label>
          <input type="password" name="password" id="password" required class="<?php echo $input_class; ?>">
        </div>
        <button type="submit" class="<?php echo $btn_primary; ?>">登録</button>
      </form>

      <p class="mt-6 text-center text-sm text-slate-600">
        すでにアカウントをお持ちの方は
        <a href="login.php" class="font-medium text-teal-700 hover:text-teal-800 hover:underline">ログイン</a>
      </p>
    <?php endif; ?>
  </div>
</div>
</body>
</html>

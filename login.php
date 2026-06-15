<?php
require_once("db.php");

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = trim($_POST['password'] ?? '');

  $res = pg_query_params($dbconn, "SELECT id FROM users WHERE username = $1 AND password = $2", [$username, $password]);
  if ($row = pg_fetch_assoc($res)) {
    $_SESSION['user_id'] = $row['id'];
    header("Location: index.php");
    exit;
  } else {
    $error = 'ログインに失敗しました。';
  }
}

$page_title = 'ログイン';
$body_class = 'flex min-h-full items-center justify-center bg-gradient-to-br from-stone-100 via-slate-50 to-teal-50 px-4 py-12 text-slate-800 antialiased';
require_once __DIR__ . '/includes/tailwind_head.php';
?>

<div class="w-full max-w-md">
  <div class="mb-8 text-center">
    <p class="text-sm font-semibold uppercase tracking-wider text-teal-700">Nostalgic Board</p>
    <h1 class="mt-2 text-3xl font-bold text-slate-800">ログイン</h1>
    <p class="mt-2 text-sm text-slate-600">位置情報と思い出を記録する掲示板</p>
  </div>

  <div class="<?php echo $card_class; ?>">
    <?php if ($error !== null): ?>
      <div class="message-box error-message mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
        <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <form method="post" class="space-y-5">
      <div>
        <label for="username" class="mb-1.5 block text-sm font-medium text-slate-700">ユーザー名</label>
        <input type="text" name="username" id="username" required class="<?php echo $input_class; ?>">
      </div>
      <div>
        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">パスワード</label>
        <input type="password" name="password" id="password" required class="<?php echo $input_class; ?>">
      </div>
      <button type="submit" class="<?php echo $btn_primary; ?>">ログイン</button>
    </form>

    <p class="mt-6 text-center text-sm text-slate-600">
      アカウントをお持ちでない方は
      <a href="register.php" class="font-medium text-teal-700 hover:text-teal-800 hover:underline">新規登録</a>
    </p>
  </div>
</div>
</body>
</html>

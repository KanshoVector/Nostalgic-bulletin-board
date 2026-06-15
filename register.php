<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/layout.php';

$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'ユーザー名とパスワードを入力してください。';
    } else {
        $error = register_user($dbconn, $username, $password);
        if ($error === null) {
            $success = '登録が完了しました。';
        }
    }
}

render_auth_shell('新規登録', 'はじめての方はこちらから', function (array $ui) use ($success, $error): void {
    if ($error !== null): ?>
      <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"><?= h($error) ?></div>
    <?php elseif ($success !== null): ?>
      <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
        <?= h($success) ?>
        <a href="login.php" class="ml-1 font-semibold text-teal-700 hover:underline">ログイン</a>
      </div>
    <?php else: ?>
      <form method="post" class="space-y-5">
        <div>
          <label for="username" class="mb-1.5 block text-sm font-medium text-slate-700">ユーザー名</label>
          <input type="text" name="username" id="username" required class="<?= h($ui['input']) ?>">
        </div>
        <div>
          <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">パスワード</label>
          <input type="password" name="password" id="password" required class="<?= h($ui['input']) ?>">
        </div>
        <button type="submit" class="<?= h($ui['button']) ?>">登録</button>
      </form>
      <p class="mt-6 text-center text-sm text-slate-600">
        すでにアカウントをお持ちの方は
        <a href="login.php" class="font-medium text-teal-700 hover:text-teal-800 hover:underline">ログイン</a>
      </p>
    <?php endif;
});

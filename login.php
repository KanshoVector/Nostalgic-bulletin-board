<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/includes/layout.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $userId = verify_user_password($dbconn, $username, $password);

    if ($userId !== null) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        header('Location: index.php');
        exit;
    }

    $error = 'ログインに失敗しました。';
}

render_auth_shell('ログイン', '位置情報と思い出を記録する掲示板', function (array $ui) use ($error): void {
    if ($error !== null): ?>
      <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"><?= h($error) ?></div>
    <?php endif; ?>
    <form method="post" class="space-y-5">
      <div>
        <label for="username" class="mb-1.5 block text-sm font-medium text-slate-700">ユーザー名</label>
        <input type="text" name="username" id="username" required class="<?= h($ui['input']) ?>">
      </div>
      <div>
        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">パスワード</label>
        <input type="password" name="password" id="password" required class="<?= h($ui['input']) ?>">
      </div>
      <button type="submit" class="<?= h($ui['button']) ?>">ログイン</button>
    </form>
    <p class="mt-6 text-center text-sm text-slate-600">
      アカウントをお持ちでない方は
      <a href="register.php" class="font-medium text-teal-700 hover:text-teal-800 hover:underline">新規登録</a>
    </p>
    <?php
});

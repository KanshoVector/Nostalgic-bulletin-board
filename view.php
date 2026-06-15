<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('VIEW_FILE_INCLUDED', true);

require_once("db.php");

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

if (isset($_GET['message'])) {
    $message .= htmlspecialchars(urldecode($_GET['message']), ENT_QUOTES, 'UTF-8');
}
if (isset($_GET['error'])) {
    $error .= htmlspecialchars(urldecode($_GET['error']), ENT_QUOTES, 'UTF-8');
}

if (isset($_SESSION['message'])) {
    $message .= $_SESSION['message'];
    unset($_SESSION['message']);
}
if (isset($_SESSION['error'])) {
    $error .= $_SESSION['error'];
    unset($_SESSION['error']);
}

require_once("includes/comment_logic.php");
require_once("includes/post_logic.php");
require_once("includes/header.php");
?>

<?php if (!empty($message)): ?>
  <div class="message-box success-message mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">
    <?php echo $message; ?>
  </div>
<?php endif; ?>
<?php if (!empty($error)): ?>
  <div class="message-box error-message mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700">
    <?php echo $error; ?>
  </div>
<?php endif; ?>

<?php if ($post_id_to_edit > 0 && $post_to_edit): ?>
  <?php require_once("templates/post_edit_form.php"); ?>
<?php else: ?>
  <div class="mb-6">
    <h2 class="text-xl font-semibold text-slate-800">投稿一覧</h2>
    <p class="mt-1 text-sm text-slate-600">自分の投稿や公開された投稿を確認・検索できます。</p>
  </div>

  <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
    <div class="flex flex-wrap gap-2">
      <a href="?mode=my_posts<?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>"
         class="<?php echo $display_mode === 'my_posts'
           ? 'rounded-lg bg-slate-700 px-4 py-2 text-sm font-medium text-white shadow-sm'
           : 'rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50'; ?>">
        自分の投稿
      </a>
      <a href="?mode=public<?php echo !empty($search_query) ? '&search=' . urlencode($search_query) : ''; ?>"
         class="<?php echo $display_mode === 'public'
           ? 'rounded-lg bg-slate-700 px-4 py-2 text-sm font-medium text-white shadow-sm'
           : 'rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50'; ?>">
        公開された投稿
      </a>
    </div>

    <form method="get" class="flex flex-wrap items-center gap-2">
      <input type="hidden" name="mode" value="<?php echo htmlspecialchars($display_mode, ENT_QUOTES, 'UTF-8'); ?>">
      <input type="text" name="search" placeholder="キーワード検索..." value="<?php echo htmlspecialchars($search_query, ENT_QUOTES, 'UTF-8'); ?>"
             class="w-full min-w-[12rem] rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-500/20 sm:w-52">
      <button type="submit" class="rounded-lg bg-teal-700 px-4 py-2 text-sm font-medium text-white transition hover:bg-teal-800">検索</button>
      <?php if (!empty($search_query)): ?>
        <a href="?mode=<?php echo htmlspecialchars($display_mode, ENT_QUOTES, 'UTF-8'); ?>" class="<?php echo $btn_secondary; ?>">クリア</a>
      <?php endif; ?>
    </form>
  </div>

  <?php require_once("templates/post_list_table.php"); ?>
<?php endif; ?>

<?php require_once("includes/footer.php"); ?>

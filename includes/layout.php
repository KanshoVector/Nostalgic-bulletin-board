<?php

declare(strict_types=1);

function layout_classes(): array
{
    static $classes = [
        'input' => 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-500/20',
        'button' => 'inline-flex w-full items-center justify-center rounded-lg bg-slate-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2',
        'button_secondary' => 'inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50',
        'card' => 'rounded-2xl border border-slate-200/80 bg-white p-6 shadow-lg shadow-slate-200/50 sm:p-8',
    ];

    return $classes;
}

function layout_start(string $title, string $bodyClass = 'min-h-full bg-gradient-to-br from-stone-100 via-slate-50 to-teal-50 text-slate-800 antialiased'): void
{
    $ui = layout_classes();
    ?>
<!DOCTYPE html>
<html lang="ja" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($title) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+JP:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'Noto Sans JP', 'sans-serif'] } } } };
  </script>
</head>
<body class="<?= h($bodyClass) ?>">
    <?php
}

function layout_end(bool $withModal = false): void
{
    if ($withModal): ?>
<div id="imageModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/80 p-4 backdrop-blur-sm">
  <button type="button" class="close absolute right-6 top-6 text-4xl leading-none text-white/90 transition hover:text-white" aria-label="閉じる">&times;</button>
  <img id="modalImage" class="max-h-[90vh] max-w-full rounded-xl shadow-2xl" src="" alt="">
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('imageModal');
  const modalImage = document.getElementById('modalImage');
  const closeBtn = document.querySelector('#imageModal .close');
  const showModal = () => { modal?.classList.replace('hidden', 'flex'); modal?.classList.add('flex'); };
  const hideModal = () => { modal?.classList.add('hidden'); modal?.classList.remove('flex'); };

  document.body.addEventListener('click', (event) => {
    const target = event.target;
    if (!(target instanceof HTMLImageElement) || !target.matches('.post-image[data-src]')) return;
    if (modalImage) modalImage.src = target.getAttribute('data-src') || '';
    showModal();
  });
  closeBtn?.addEventListener('click', hideModal);
  modal?.addEventListener('click', (event) => { if (event.target === modal) hideModal(); });
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && modal?.classList.contains('flex')) hideModal();
  });

  document.body.addEventListener('click', (event) => {
    const target = event.target;
    if (!(target instanceof HTMLElement)) return;
    if (target.matches('.edit-comment-button')) {
      const id = target.dataset.commentId;
      document.querySelector('.comment-text-' + id)?.classList.add('hidden');
      target.classList.add('hidden');
      target.nextElementSibling?.classList.add('hidden');
      document.querySelector('.comment-edit-form-' + id)?.classList.remove('hidden');
    }
    if (target.matches('.cancel-button')) {
      const id = target.dataset.commentId;
      document.querySelector('.comment-text-' + id)?.classList.remove('hidden');
      document.querySelector('.edit-comment-button[data-comment-id="' + id + '"]')?.classList.remove('hidden');
      document.querySelector('.edit-comment-button[data-comment-id="' + id + '"]')?.nextElementSibling?.classList.remove('hidden');
      document.querySelector('.comment-edit-form-' + id)?.classList.add('hidden');
    }
  });
});
</script>
    <?php endif; ?>
</body>
</html>
    <?php
}

function render_flash_messages(string $message, string $error): void
{
    if ($message !== ''): ?>
<div class="message-box success-message mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"><?= h($message) ?></div>
    <?php endif;
    if ($error !== ''): ?>
<div class="message-box error-message mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-700"><?= h($error) ?></div>
    <?php endif;
}

function render_app_header(string $title, array $links): void
{
    ?>
<div class="mx-auto min-h-screen max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
  <header class="mb-8 flex flex-col gap-4 border-b border-slate-200 pb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <p class="text-sm font-medium uppercase tracking-wide text-teal-700">Nostalgic Board</p>
      <h1 class="text-2xl font-bold text-slate-800"><?= h($title) ?></h1>
    </div>
    <nav class="flex flex-wrap items-center gap-3 text-sm font-medium">
      <?php foreach ($links as $link): ?>
        <a href="<?= h($link['href']) ?>" class="<?= h($link['class'] ?? 'rounded-lg px-3 py-2 text-slate-600 transition hover:bg-white hover:text-slate-900') ?>"><?= h($link['label']) ?></a>
      <?php endforeach; ?>
    </nav>
  </header>
  <main>
    <?php
}

function render_app_footer(bool $withModal = true): void
{
    ?>
  </main>
</div>
    <?php
    layout_end($withModal);
}

function render_auth_shell(string $title, string $subtitle, callable $content): void
{
    $ui = layout_classes();
    layout_start($title, 'flex min-h-full items-center justify-center bg-gradient-to-br from-stone-100 via-slate-50 to-teal-50 px-4 py-12 text-slate-800 antialiased');
    ?>
<div class="w-full max-w-md">
  <div class="mb-8 text-center">
    <p class="text-sm font-semibold uppercase tracking-wider text-teal-700">Nostalgic Board</p>
    <h1 class="mt-2 text-3xl font-bold text-slate-800"><?= h($title) ?></h1>
    <p class="mt-2 text-sm text-slate-600"><?= h($subtitle) ?></p>
  </div>
  <div class="<?= h($ui['card']) ?>">
    <?php $content($ui); ?>
  </div>
</div>
    <?php
    layout_end(false);
}

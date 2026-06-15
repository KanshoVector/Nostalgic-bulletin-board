<?php
$page_title = $page_title ?? 'マイメモリー';
$body_class = $body_class ?? 'min-h-full bg-gradient-to-br from-stone-100 via-slate-50 to-teal-50 text-slate-800 antialiased';
$input_class = 'w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 shadow-sm transition focus:border-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-500/20';
$btn_primary = 'inline-flex w-full items-center justify-center rounded-lg bg-slate-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2';
$btn_secondary = 'inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50';
$card_class = 'rounded-2xl border border-slate-200/80 bg-white p-6 shadow-lg shadow-slate-200/50 sm:p-8';
?>
<!DOCTYPE html>
<html lang="ja" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Noto+Sans+JP:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Inter', 'Noto Sans JP', 'sans-serif'],
          },
        },
      },
    };
  </script>
</head>
<body class="<?php echo htmlspecialchars($body_class, ENT_QUOTES, 'UTF-8'); ?>">

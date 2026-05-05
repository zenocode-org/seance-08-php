<?php

declare(strict_types=1);

/** @var string $title */
/** @var string $content */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= \h($title) ?></title>
  <style>
    :root { font-family: system-ui, sans-serif; line-height: 1.5; color: #0f172a; background: #f8fafc; }
    body { margin: 0 auto; max-width: 48rem; padding: 1.25rem 1rem 3rem; }
    a { color: #2563eb; }
    h1 { font-size: 1.5rem; }
    .sub { color: #64748b; font-size: 0.95rem; }
    .err { background: #fef2f2; border: 1px solid #fecaca; padding: 0.75rem 1rem; border-radius: 8px; color: #991b1b; }
    .ok { background: #ecfdf5; border: 1px solid #a7f3d0; padding: 0.25rem 0.6rem; border-radius: 6px; color: #065f46; font-size: 0.85rem; }
    .no { background: #fff7ed; border: 1px solid #fed7aa; padding: 0.25rem 0.6rem; border-radius: 6px; color: #9a3412; font-size: 0.85rem; }
    table { width: 100%; border-collapse: collapse; margin-top: 1rem; font-size: 0.95rem; }
    th, td { text-align: left; padding: 0.5rem 0.6rem; border-bottom: 1px solid #e2e8f0; }
    th { color: #475569; font-weight: 600; }
    .muted { color: #64748b; font-size: 0.9rem; }
    ul.books { list-style: none; padding: 0; margin: 1rem 0 0; }
    ul.books li { padding: 0.5rem 0; border-bottom: 1px solid #e2e8f0; }
  </style>
</head>
<body>
  <p class="muted"><a href="/">Catalogue</a><?php if (isset($_GET['id']) && (string) $_GET['id'] !== '') { ?> · fiche livre<?php } ?></p>
  <?= $content ?>
</body>
</html>

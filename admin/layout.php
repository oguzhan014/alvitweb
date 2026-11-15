<?php
// Admin layout helper
function adminHeader($title) {
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title) ?> | Alvit Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body">

<!-- SIDEBAR -->
<aside class="admin-sidebar">
  <div class="admin-sidebar-header">
    <h2>al<span>vi</span>t</h2>
    <small>Yönetim Paneli</small>
  </div>
  <?php
    $navUri = $_SERVER['REQUEST_URI'];
    $navItems = [
        ['/admin/products',     'Ürünler',          'layers'],
        ['/admin/categories',   'Kategoriler',      'layers'],
        ['/admin/collections',  'Koleksiyonlar',    'layers'],
        ['/admin/colors',       'Renk Kataloğu',    'leaf'],
        ['/admin/sliders',      'Slider',           'eye'],
        null,
        ['/admin/pages',        'Sayfalar',         'document'],
        ['/admin/certificates', 'Kalite Belgeleri', 'shield'],
        ['/admin/files',        'Kurumsal Dosyalar','document'],
        ['/admin/messages',     'Mesajlar',         'users'],
        ['/admin/settings',     'Ayarlar',          'gear'],
    ];
    $onSub = false;
    foreach ($navItems as $item) {
        if ($item && strpos($navUri, $item[0]) !== false) { $onSub = true; break; }
    }
  ?>
  <nav class="admin-nav">
    <a href="/admin" <?= $onSub ? '' : 'class="active"' ?>>
      <span class="icon"><?= icon('layers', 17) ?></span> Dashboard
    </a>
    <?php foreach ($navItems as $item): ?>
      <?php if ($item === null): ?>
      <div class="admin-nav-divider"></div>
      <?php else: ?>
      <a href="<?= $item[0] ?>" <?= strpos($navUri, $item[0]) !== false ? 'class="active"' : '' ?>>
        <span class="icon"><?= icon($item[2], 17) ?></span> <?= e($item[1]) ?>
      </a>
      <?php endif; ?>
    <?php endforeach; ?>
    <div class="admin-nav-divider"></div>
    <a href="/" target="_blank">
      <span class="icon">&#8594;</span> Siteyi Gör
    </a>
    <a href="/admin/logout">
      <span class="icon">&#10005;</span> Çıkış
    </a>
  </nav>
</aside>

<!-- MAIN -->
<main class="admin-main">
<?php
}

function adminFooter() {
?>
</main>
</body>
</html>
<?php
}
?>

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
  <nav class="admin-nav">
    <a href="/admin" <?= strpos($_SERVER['REQUEST_URI'], '/admin/products') === false && strpos($_SERVER['REQUEST_URI'], '/admin/categories') === false && strpos($_SERVER['REQUEST_URI'], '/admin/pages') === false && strpos($_SERVER['REQUEST_URI'], '/admin/sliders') === false && strpos($_SERVER['REQUEST_URI'], '/admin/messages') === false && strpos($_SERVER['REQUEST_URI'], '/admin/settings') === false ? 'class="active"' : '' ?>>
      <span class="icon">&#9776;</span> Dashboard
    </a>
    <a href="/admin/products" <?= strpos($_SERVER['REQUEST_URI'], '/admin/products') !== false ? 'class="active"' : '' ?>>
      <span class="icon">&#9881;</span> Ürünler
    </a>
    <a href="/admin/categories" <?= strpos($_SERVER['REQUEST_URI'], '/admin/categories') !== false ? 'class="active"' : '' ?>>
      <span class="icon">&#9783;</span> Kategoriler
    </a>
    <a href="/admin/sliders" <?= strpos($_SERVER['REQUEST_URI'], '/admin/sliders') !== false ? 'class="active"' : '' ?>>
      <span class="icon">&#9724;</span> Slider
    </a>
    <div class="admin-nav-divider"></div>
    <a href="/admin/pages" <?= strpos($_SERVER['REQUEST_URI'], '/admin/pages') !== false ? 'class="active"' : '' ?>>
      <span class="icon">&#9998;</span> Sayfalar
    </a>
    <a href="/admin/messages" <?= strpos($_SERVER['REQUEST_URI'], '/admin/messages') !== false ? 'class="active"' : '' ?>>
      <span class="icon">&#9993;</span> Mesajlar
    </a>
    <a href="/admin/settings" <?= strpos($_SERVER['REQUEST_URI'], '/admin/settings') !== false ? 'class="active"' : '' ?>>
      <span class="icon">&#9881;</span> Ayarlar
    </a>
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

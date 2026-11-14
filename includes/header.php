<?php $settings = getSettings(); $categories = getCategories(); ?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' | ' : '' ?><?= e($settings['site_name']) ?> - <?= e($settings['site_slogan']) ?></title>
  <meta name="description" content="<?= e($settings['site_description']) ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<div class="topbar">
  <div class="container topbar-inner">
    <div class="topbar-contact">
      <a href="tel:<?= e(preg_replace('/\s+/', '', $settings['phone'])) ?>">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        <?= e($settings['phone']) ?>
      </a>
      <a href="mailto:<?= e($settings['email']) ?>">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
        <?= e($settings['email']) ?>
      </a>
    </div>
    <div class="topbar-social">
      <?php if (!empty($settings['social']['facebook'])): ?><a href="<?= e($settings['social']['facebook']) ?>" target="_blank" rel="noopener" aria-label="Facebook">f</a><?php endif; ?>
      <?php if (!empty($settings['social']['instagram'])): ?><a href="<?= e($settings['social']['instagram']) ?>" target="_blank" rel="noopener" aria-label="Instagram">ig</a><?php endif; ?>
      <?php if (!empty($settings['social']['youtube'])): ?><a href="<?= e($settings['social']['youtube']) ?>" target="_blank" rel="noopener" aria-label="YouTube">yt</a><?php endif; ?>
    </div>
  </div>
</div>

<header class="header">
  <div class="header-inner">
    <a href="/" class="logo">
      <img src="/<?= e($settings['logo']) ?>" alt="<?= e($settings['site_name']) ?>">
    </a>

    <nav class="nav-menu">
      <a href="/" <?= ($uri ?? '') === '/' ? 'class="active"' : '' ?>>Anasayfa</a>
      <a href="/kategoriler" <?= ($uri ?? '') === '/kategoriler' ? 'class="active"' : '' ?>>Ürünler</a>
      <a href="/sayfa/hakkimizda">Hakkımızda</a>
      <a href="/sayfa/kalite-belgelerimiz">Belgeler</a>
      <a href="/iletisim">İletişim</a>

      <form class="header-search header-search-mobile" action="/ara" method="get" autocomplete="off">
        <input type="text" name="q" placeholder="Ürün, model veya kategori ara..." value="<?= e($_GET['q'] ?? '') ?>">
        <button type="submit" aria-label="Ara">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
        <div class="search-suggestions"></div>
      </form>
    </nav>

    <div class="header-actions">
      <form class="header-search header-search-desktop" action="/ara" method="get" autocomplete="off">
        <input type="text" name="q" placeholder="Ürün, model veya kategori ara..." value="<?= e($_GET['q'] ?? '') ?>">
        <button type="submit" aria-label="Ara">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
        <div class="search-suggestions"></div>
      </form>

      <button class="menu-toggle" aria-label="Menü">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </div>
</header>

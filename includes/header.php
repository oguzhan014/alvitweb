<?php
$settings = getSettings();
$categories = getCategories();
$currentUri = $uri ?? '';
$L = lang();

$kurumsalLinks = [
    ['slug' => 'hakkimizda',             'label_tr' => 'Hakkımızda',             'label_en' => 'About Us'],
    ['slug' => 'vizyon-misyon',          'label_tr' => 'Vizyon & Misyon',        'label_en' => 'Vision & Mission'],
    ['slug' => 'kalite-politikasi',      'label_tr' => 'Kalite Politikası',      'label_en' => 'Quality Policy'],
    ['slug' => 'kalite-belgelerimiz',    'label_tr' => 'Kalite Belgelerimiz',    'label_en' => 'Our Certificates'],
    ['slug' => 'raporlar',               'label_tr' => 'Raporlar',               'label_en' => 'Reports'],
    ['slug' => 'montaj-kilavuzlari',     'label_tr' => 'Montaj Kılavuzları',     'label_en' => 'Installation Guides'],
    ['slug' => 'urun-montaj-videolari',  'label_tr' => 'Ürün Montaj Videoları',  'label_en' => 'Installation Videos'],
];
$isKurumsal = strpos($currentUri, '/sayfa/') === 0;
$isUrunler = in_array($currentUri, ['/urunler', '/kategoriler'], true)
    || strpos($currentUri, '/kategori/') === 0;
?>
<!DOCTYPE html>
<html lang="<?= $L ?>">
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

    <div class="topbar-right">
      <a href="<?= langUrl('/sayfa/montaj-kilavuzlari') ?>" class="topbar-link"><?= t('footer_guides') ?></a>
      <a href="<?= langUrl('/sayfa/raporlar') ?>" class="topbar-link"><?= $L === 'tr' ? 'Raporlar' : 'Reports' ?></a>

      <a href="<?= switchLangUrl() ?>" class="topbar-link lang-switch" title="<?= $L === 'tr' ? 'Switch to English' : 'Türkçe\'ye geç' ?>">
        <?= icon('globe', 14) ?> <?= $L === 'tr' ? 'EN' : 'TR' ?>
      </a>

      <div class="topbar-social">
        <?php if (!empty($settings['social']['facebook'])): ?>
        <a href="<?= e($settings['social']['facebook']) ?>" target="_blank" rel="noopener" aria-label="Facebook">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
        </a>
        <?php endif; ?>
        <?php if (!empty($settings['social']['instagram'])): ?>
        <a href="<?= e($settings['social']['instagram']) ?>" target="_blank" rel="noopener" aria-label="Instagram">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1.2" fill="currentColor" stroke="none"/></svg>
        </a>
        <?php endif; ?>
        <?php if (!empty($settings['social']['youtube'])): ?>
        <a href="<?= e($settings['social']['youtube']) ?>" target="_blank" rel="noopener" aria-label="YouTube">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M23 12s0-3.5-.46-5.17a2.9 2.9 0 0 0-2.04-2.05C18.83 4.3 12 4.3 12 4.3s-6.83 0-8.5.48A2.9 2.9 0 0 0 1.46 6.83C1 8.5 1 12 1 12s0 3.5.46 5.17a2.9 2.9 0 0 0 2.04 2.05c1.67.48 8.5.48 8.5.48s6.83 0 8.5-.48a2.9 2.9 0 0 0 2.04-2.05C23 15.5 23 12 23 12zM9.75 15.27V8.73L15.5 12z"/></svg>
        </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<header class="header">
  <div class="header-inner">
    <a href="<?= langUrl() ?>" class="logo">
      <img src="/<?= e($settings['logo']) ?>" alt="<?= e($settings['site_name']) ?>">
    </a>

    <nav class="nav-menu" id="navMenu">
      <a href="<?= langUrl() ?>" class="nav-link<?= $currentUri === '/' ? ' active' : '' ?>"><?= t('nav_home') ?></a>

      <!-- ÜRÜNLER: mega menü -->
      <div class="nav-item has-dropdown">
        <a href="<?= langUrl('/urunler') ?>" class="nav-link<?= $isUrunler ? ' active' : '' ?>">
          <?= t('nav_products') ?>
          <svg class="nav-caret" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </a>
        <button type="button" class="nav-expand" aria-label="<?= $L === 'tr' ? 'Alt menüyü aç' : 'Open submenu' ?>"></button>

        <div class="mega-menu">
          <div class="mega-inner">
            <?php foreach ($categories as $cat): ?>
            <div class="mega-col">
              <a href="<?= langUrl('/kategori/' . e($cat['slug'])) ?>" class="mega-title"><?= e(td($cat, 'name')) ?></a>
              <?php if (!empty($cat['subcategories'])): ?>
              <ul>
                <?php foreach ($cat['subcategories'] as $sub): ?>
                <li><a href="<?= langUrl('/kategori/' . e($sub['slug'])) ?>"><?= e(td($sub, 'name')) ?></a></li>
                <?php endforeach; ?>
              </ul>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="mega-footer-row">
            <a href="<?= langUrl('/urunler') ?>" class="mega-footer">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="7" y1="12" x2="17" y2="12"/><line x1="10" y1="18" x2="14" y2="18"/></svg>
              <?= t('nav_all_products') ?>
            </a>
            <a href="<?= langUrl('/koleksiyonlar') ?>" class="mega-footer">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
              <?= t('nav_collections') ?>
            </a>
            <a href="<?= langUrl('/renkler') ?>" class="mega-footer">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="14" r="2.5"/><circle cx="8.5" cy="7.5" r="2.5"/><circle cx="6.5" cy="15" r="2.5"/><path d="M12 2a10 10 0 1 0 0 20c.83 0 1.5-.67 1.5-1.5 0-.39-.15-.74-.39-1a1.49 1.49 0 0 1 1.09-2.5H16a6 6 0 0 0 6-6c0-5-4.48-9-10-9z"/></svg>
              <?= t('nav_colors') ?>
            </a>
          </div>
        </div>
      </div>

      <!-- KURUMSAL: açılır menü -->
      <div class="nav-item has-dropdown">
        <a href="<?= langUrl('/sayfa/hakkimizda') ?>" class="nav-link<?= $isKurumsal ? ' active' : '' ?>">
          <?= t('nav_corporate') ?>
          <svg class="nav-caret" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
        </a>
        <button type="button" class="nav-expand" aria-label="<?= $L === 'tr' ? 'Alt menüyü aç' : 'Open submenu' ?>"></button>

        <div class="dropdown-menu">
          <?php foreach ($kurumsalLinks as $link): ?>
          <a href="<?= langUrl('/sayfa/' . e($link['slug'])) ?>"
             class="<?= $currentUri === '/sayfa/' . $link['slug'] ? 'active' : '' ?>">
            <?= e($link['label_' . $L]) ?>
          </a>
          <?php endforeach; ?>
        </div>
      </div>

      <a href="<?= langUrl('/galeri') ?>" class="nav-link<?= $currentUri === '/galeri' ? ' active' : '' ?>"><?= t('nav_gallery') ?></a>
      <a href="<?= langUrl('/iletisim') ?>" class="nav-link<?= $currentUri === '/iletisim' ? ' active' : '' ?>"><?= t('nav_contact') ?></a>

      <form class="header-search header-search-mobile" action="<?= langUrl('/ara') ?>" method="get" autocomplete="off">
        <input type="text" name="q" placeholder="<?= t('search_placeholder') ?>" value="<?= e($_GET['q'] ?? '') ?>">
        <button type="submit" aria-label="<?= $L === 'tr' ? 'Ara' : 'Search' ?>">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
        <div class="search-suggestions"></div>
      </form>

    </nav>

    <div class="header-actions">
      <form class="header-search header-search-desktop" action="<?= langUrl('/ara') ?>" method="get" autocomplete="off">
        <input type="text" name="q" placeholder="<?= t('search_placeholder') ?>" value="<?= e($_GET['q'] ?? '') ?>">
        <button type="submit" aria-label="<?= $L === 'tr' ? 'Ara' : 'Search' ?>">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
        <div class="search-suggestions"></div>
      </form>

      <button class="menu-toggle" aria-label="<?= $L === 'tr' ? 'Menü' : 'Menu' ?>">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </div>
</header>

<?php
$pageTitle = t('collections_title');
$seoPageDescription = lang() === 'tr'
    ? 'Alvit seramik banyo koleksiyonları. Özel tasarım seramik banyo serileri - Bartın Seramik A.Ş.'
    : 'Alvit ceramic bathroom collections. Special design ceramic bathroom series - Bartın Seramik A.Ş.';
$collections = getCollections();
$allProducts = getProducts();
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1><?= t('collections_title') ?></h1>
    <div class="breadcrumb-light">
      <a href="<?= langUrl() ?>"><?= t('breadcrumb_home') ?></a>
      <span>&rsaquo;</span>
      <span class="current"><?= t('collections_title') ?></span>
    </div>
  </div>
</div>

<section class="section section-compact">
  <div class="container">
    <div class="page-body page-body-compact">
      <p><?= t('collections_intro') ?></p>
    </div>

    <div class="collection-grid">
      <?php foreach ($collections as $col): ?>
      <a href="<?= langUrl('/koleksiyon/' . e($col['slug'])) ?>" class="collection-card reveal">
        <div class="collection-image">
          <?php if (!empty($col['image'])): ?>
          <img src="/<?= e($col['image']) ?>" alt="<?= e(td($col, 'title')) ?>" loading="lazy">
          <?php else: ?>
          <img src="/assets/images/placeholder.svg" alt="<?= e(td($col, 'title')) ?>">
          <?php endif; ?>
        </div>
        <div class="collection-body">
          <h3><?= e(td($col, 'title')) ?></h3>
          <span class="collection-count"><?= count(array_filter($allProducts, fn($p) => ($p['collection'] ?? '') === $col['slug'])) ?> <?= t('product_count_suffix') ?></span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

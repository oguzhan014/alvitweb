<?php
$collection = getCollectionBySlug($collectionSlug);
if (!$collection) { http_response_code(404); include __DIR__ . '/404.php'; return; }

$pageTitle = td($collection, 'title');
$products = getProductsByCollection($collectionSlug);
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1><?= e(td($collection, 'title')) ?></h1>
    <div class="breadcrumb-light">
      <a href="<?= langUrl() ?>"><?= t('breadcrumb_home') ?></a>
      <span>&rsaquo;</span>
      <a href="<?= langUrl('/koleksiyonlar') ?>"><?= t('collections_title') ?></a>
      <span>&rsaquo;</span>
      <span class="current"><?= e(td($collection, 'title')) ?></span>
    </div>
  </div>
</div>

<section class="section section-compact">
  <div class="container">
    <?php if (empty($products)): ?>
    <p class="catalog-empty"><?= t('collection_empty') ?></p>
    <?php else: ?>
    <p class="collection-intro"><strong><?= count($products) ?> <?= t('product_count_suffix') ?></strong> &mdash; <?= t('collection_intro_suffix') ?></p>
    <div class="product-grid">
      <?php foreach ($products as $product): ?>
      <a href="<?= langUrl('/urun/' . e($product['slug'])) ?>" class="product-card reveal">
        <div class="product-card-image">
          <img src="/<?= getProductImage($product) ?>" alt="<?= e(td($product, 'name')) ?>" loading="lazy">
          <?php if (!empty($product['documents'])): ?>
          <span class="product-card-doc" title="<?= t('product_has_drawing') ?>">PDF</span>
          <?php endif; ?>
        </div>
        <div class="product-card-body">
          <h3><?= e(td($product, 'name')) ?></h3>
          <p><?= e(td($product, 'features')) ?></p>
          <?php if (!empty($product['code'])): ?>
          <div class="product-code"><?= t('code_label') ?>: <?= e($product['code']) ?></div>
          <?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

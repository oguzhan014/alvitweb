<?php
$collection = getCollectionBySlug($collectionSlug);
if (!$collection) { http_response_code(404); include __DIR__ . '/404.php'; return; }

$pageTitle = $collection['title'];
$products = getProductsByCollection($collectionSlug);
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1><?= e($collection['title']) ?></h1>
    <div class="breadcrumb-light">
      <a href="/">Anasayfa</a>
      <span>&rsaquo;</span>
      <a href="/koleksiyonlar">Koleksiyonlar</a>
      <span>&rsaquo;</span>
      <span class="current"><?= e($collection['title']) ?></span>
    </div>
  </div>
</div>

<section class="section section-compact">
  <div class="container">
    <?php if (empty($products)): ?>
    <p class="catalog-empty">Bu koleksiyonda henüz ürün bulunmuyor.</p>
    <?php else: ?>
    <p class="collection-intro"><strong><?= count($products) ?> ürün</strong> &mdash; aynı seriye ait, birbiriyle uyumlu ürünler.</p>
    <div class="product-grid">
      <?php foreach ($products as $product): ?>
      <a href="/urun/<?= e($product['slug']) ?>" class="product-card reveal">
        <div class="product-card-image">
          <img src="/<?= getProductImage($product) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
          <?php if (!empty($product['documents'])): ?>
          <span class="product-card-doc" title="Teknik çizim mevcut">PDF</span>
          <?php endif; ?>
        </div>
        <div class="product-card-body">
          <h3><?= e($product['name']) ?></h3>
          <p><?= e($product['features']) ?></p>
          <?php if (!empty($product['code'])): ?>
          <div class="product-code">Kod: <?= e($product['code']) ?></div>
          <?php endif; ?>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

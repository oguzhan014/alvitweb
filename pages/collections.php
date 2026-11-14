<?php
$pageTitle = 'Koleksiyonlar';
$collections = getCollections();
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1>Koleksiyonlar</h1>
    <div class="breadcrumb-light">
      <a href="/">Anasayfa</a>
      <span>&rsaquo;</span>
      <span class="current">Koleksiyonlar</span>
    </div>
  </div>
</div>

<section class="section section-compact">
  <div class="container">
    <div class="page-body page-body-compact">
      <p>Aynı tasarım dilini paylaşan ürünlerimizi seri seri inceleyebilirsiniz. Her koleksiyon, banyonuzda bütünlüklü bir görünüm için birbiriyle uyumlu ürünlerden oluşur.</p>
    </div>

    <div class="collection-grid">
      <?php foreach ($collections as $col): ?>
      <a href="/koleksiyon/<?= e($col['slug']) ?>" class="collection-card reveal">
        <div class="collection-image">
          <?php if (!empty($col['image'])): ?>
          <img src="/<?= e($col['image']) ?>" alt="<?= e($col['title']) ?>" loading="lazy">
          <?php else: ?>
          <img src="/assets/images/placeholder.svg" alt="<?= e($col['title']) ?>">
          <?php endif; ?>
        </div>
        <div class="collection-body">
          <h3><?= e($col['title']) ?></h3>
          <span class="collection-count"><?= (int)$col['count'] ?> ürün</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<?php
$pageTitle = 'Ürün Kategorileri';
$categories = getCategories();
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1>Ürün Kategorileri</h1>
    <div class="breadcrumb-light">
      <a href="/">Anasayfa</a>
      <span>&rsaquo;</span>
      <span class="current">Kategoriler</span>
    </div>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="category-grid">
      <?php foreach ($categories as $cat): ?>
      <a href="/kategori/<?= e($cat['slug']) ?>" class="category-card reveal">
        <img src="/<?= getCategoryImage($cat) ?>" alt="<?= e($cat['name']) ?>" loading="lazy">
        <div class="category-card-overlay">
          <h3><?= e($cat['name']) ?></h3>
          <div class="arrow">&#8594;</div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<?php
$pageTitle = t('categories_title');
$categories = getCategories();
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1><?= t('categories_title') ?></h1>
    <div class="breadcrumb-light">
      <a href="<?= langUrl() ?>"><?= t('breadcrumb_home') ?></a>
      <span>&rsaquo;</span>
      <span class="current"><?= t('categories_title') ?></span>
    </div>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="category-grid">
      <?php foreach ($categories as $cat): ?>
      <a href="<?= langUrl('/kategori/' . e($cat['slug'])) ?>" class="category-card reveal">
        <img src="/<?= getCategoryImage($cat) ?>" alt="<?= e(td($cat, 'name')) ?>" loading="lazy">
        <div class="category-card-overlay">
          <h3><?= e(td($cat, 'name')) ?></h3>
          <div class="arrow">&#8594;</div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

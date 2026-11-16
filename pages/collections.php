<?php
$pageTitle = t('collections_title');
$collections = getCollections();
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
          <span class="collection-count"><?= (int)$col['count'] ?> <?= t('product_count_suffix') ?></span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

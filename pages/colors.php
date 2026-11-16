<?php
$pageTitle = t('colors_title');
$colors = getColors();
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1><?= t('colors_title') ?></h1>
    <div class="breadcrumb-light">
      <a href="<?= langUrl() ?>"><?= t('breadcrumb_home') ?></a>
      <span>&rsaquo;</span>
      <span class="current"><?= t('nav_colors') ?></span>
    </div>
  </div>
</div>

<section class="section section-compact">
  <div class="container">
    <div class="page-body page-body-compact">
      <p><?= t('colors_intro') ?></p>
    </div>

    <div class="color-grid">
      <?php foreach ($colors as $color): ?>
      <div class="color-card reveal<?= !empty($color['dark']) ? ' color-card-dark' : '' ?>">
        <div class="color-swatch" style="background: <?= e($color['hex']) ?>"></div>
        <div class="color-body">
          <h3><?= e(td($color, 'name')) ?></h3>
          <span class="color-hex"><?= e(strtoupper($color['hex'])) ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="cta-banner cta-inline">
      <div class="cta-banner-inner">
        <div>
          <h2><?= t('colors_cta_title') ?></h2>
          <p><?= t('colors_cta_desc') ?></p>
        </div>
        <a href="<?= langUrl('/iletisim') ?>" class="btn btn-primary"><?= t('cta_btn') ?></a>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

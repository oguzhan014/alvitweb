<?php
$q = trim($_GET['q'] ?? '');
$pageTitle = t('search_title');
$seoNoIndex = true;
$results = $q !== '' ? searchProducts($q) : [];
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1><?= t('search_title') ?></h1>
    <div class="breadcrumb-light">
      <a href="<?= langUrl() ?>"><?= t('breadcrumb_home') ?></a>
      <span>&rsaquo;</span>
      <span class="current"><?= $q !== '' ? '"' . e($q) . '"' : t('search_query') ?></span>
    </div>
  </div>
</div>

<section class="section">
  <div class="container">
    <?php if ($q === ''): ?>
      <div style="text-align:center; padding:60px 0;">
        <p style="font-size:18px; color:var(--text-light);"><?= t('search_prompt') ?></p>
      </div>
    <?php elseif (empty($results)): ?>
      <div style="text-align:center; padding:60px 0;">
        <p style="font-size:18px; color:var(--text-light);"><strong>"<?= e($q) ?>"</strong> <?= t('search_no_result_for') ?></p>
        <p style="margin-top:8px;"><a href="<?= langUrl('/kategoriler') ?>" style="color:var(--accent); font-weight:600;"><?= t('search_browse_cats') ?> &rarr;</a></p>
      </div>
    <?php else: ?>
      <p style="margin-bottom:32px; color:var(--text-light);"><strong><?= count($results) ?></strong> <?= t('search_results_found') ?> &mdash; "<?= e($q) ?>"</p>
      <div class="product-grid">
        <?php foreach ($results as $product): ?>
        <a href="<?= langUrl('/urun/' . e($product['slug'])) ?>" class="product-card reveal">
          <div class="product-card-image">
            <img src="/<?= getProductImage($product) ?>" alt="<?= e(td($product, 'name')) ?>" loading="lazy">
            <?php if (!empty($product['featured'])): ?>
            <span class="product-card-badge"><?= t('featured_badge') ?></span>
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

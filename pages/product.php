<?php
$product = getProductBySlug($productSlug);
if (!$product) { http_response_code(404); include __DIR__ . '/404.php'; return; }

$pageTitle = td($product, 'name');
$category = getCategoryById($product['category_id']);
$subcategory = $product['subcategory_id'] ? getCategoryById($product['subcategory_id']) : null;

$relatedProducts = array_slice(array_filter(getProducts(), function($p) use ($product) {
    return $p['category_id'] == $product['category_id'] && $p['id'] != $product['id'];
}), 0, 4);

include __DIR__ . '/../includes/header.php';
?>

<div class="breadcrumb">
  <div class="container">
    <a href="<?= langUrl() ?>"><?= t('breadcrumb_home') ?></a>
    <span>&rsaquo;</span>
    <a href="<?= langUrl('/kategoriler') ?>"><?= t('categories_title') ?></a>
    <?php if ($category): ?>
    <span>&rsaquo;</span>
    <a href="<?= langUrl('/kategori/' . e($category['slug'])) ?>"><?= e(td($category, 'name')) ?></a>
    <?php endif; ?>
    <?php if ($subcategory): ?>
    <span>&rsaquo;</span>
    <a href="<?= langUrl('/kategori/' . e($subcategory['slug'])) ?>"><?= e(td($subcategory, 'name')) ?></a>
    <?php endif; ?>
    <span>&rsaquo;</span>
    <span class="current"><?= e(td($product, 'name')) ?></span>
  </div>
</div>

<section class="product-detail">
  <div class="container">
    <div class="grid-2">
      <div class="product-gallery">
        <img src="/<?= getProductImage($product) ?>" alt="<?= e(td($product, 'name')) ?>">
      </div>

      <div class="product-info">
        <h1><?= e(td($product, 'name')) ?></h1>

        <?php $specRows = $product['spec_rows'] ?? []; ?>

        <div class="product-meta">
          <?php if (empty($specRows) && !empty($product['code'])): ?>
          <div class="product-meta-item">
            <span class="label"><?= t('product_code') ?></span>
            <span class="value"><?= e($product['code']) ?></span>
          </div>
          <?php endif; ?>

          <?php if (!empty($product['features'])): ?>
          <div class="product-meta-item">
            <span class="label"><?= t('product_features') ?></span>
            <span class="value"><?= e(td($product, 'features')) ?></span>
          </div>
          <?php endif; ?>

          <?php if (empty($specRows) && !empty($product['palet'])): ?>
          <div class="product-meta-item">
            <span class="label"><?= t('product_palet') ?></span>
            <span class="value"><?= e($product['palet']) ?></span>
          </div>
          <?php endif; ?>

          <?php if ($category): ?>
          <div class="product-meta-item">
            <span class="label"><?= t('product_category') ?></span>
            <span class="value"><?= e(td($category, 'name')) ?></span>
          </div>
          <?php endif; ?>
        </div>

        <?php if (!empty($product['highlights']) || !empty($product['attributes'])): ?>
        <div class="spec-block">
          <h3 class="spec-title"><?= t('product_features') ?></h3>
          <?php if (!empty($product['highlights'])): ?>
          <ul class="product-highlights">
            <?php foreach ($product['highlights'] as $hl): ?>
            <li><?= e($hl) ?></li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
          <?php if (!empty($product['attributes'])): ?>
          <dl class="product-attributes">
            <?php foreach ($product['attributes'] as $attr): ?>
            <dt><?= e($attr['label']) ?></dt>
            <dd><?= e($attr['value']) ?></dd>
            <?php endforeach; ?>
          </dl>
          <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($specRows)): ?>
        <div class="spec-block">
          <h3 class="spec-title"><?= t('product_specs') ?></h3>
          <div class="spec-table-wrap">
            <table class="spec-table">
              <thead>
                <tr>
                  <?php foreach ($product['spec_columns'] as $col): ?>
                  <th><?= e($col) ?></th>
                  <?php endforeach; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($specRows as $row): ?>
                <tr>
                  <?php foreach ($row as $cell): ?>
                  <td><?= e($cell !== '' ? $cell : '—') ?></td>
                  <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($product['documents'])): ?>
        <div class="product-docs">
          <h3 class="spec-title"><?= t('product_documents') ?></h3>
          <?php foreach ($product['documents'] as $doc): ?>
            <?php
              $docFile = is_array($doc) ? ($doc['file'] ?? '') : 'assets/documents/' . $doc;
              $docName = is_array($doc) ? ($doc['name'] ?? basename($docFile)) : $doc;
              if ($docFile === '') continue;
            ?>
          <a href="/<?= e($docFile) ?>" target="_blank" rel="noopener" download>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            <?= e($docName) ?>
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($product['collection'])): ?>
          <?php $productCollection = getCollectionBySlug($product['collection']); ?>
          <?php if ($productCollection): ?>
        <div>
          <a href="<?= langUrl('/koleksiyon/' . e($product['collection'])) ?>" class="product-collection-link">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            <?= e(td($productCollection, 'title')) ?> &mdash; <?= t('product_view_series') ?>
          </a>
        </div>
          <?php endif; ?>
        <?php endif; ?>

        <div style="margin-top:32px;">
          <a href="<?= langUrl('/iletisim') ?>" class="btn btn-dark"><?= t('product_get_info') ?></a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if (!empty($relatedProducts)): ?>
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2><?= t('product_related') ?></h2>
      <div class="line"></div>
    </div>
    <div class="product-grid">
      <?php foreach ($relatedProducts as $rp): ?>
      <a href="<?= langUrl('/urun/' . e($rp['slug'])) ?>" class="product-card">
        <div class="product-card-image">
          <img src="/<?= getProductImage($rp) ?>" alt="<?= e(td($rp, 'name')) ?>" loading="lazy">
        </div>
        <div class="product-card-body">
          <h3><?= e(td($rp, 'name')) ?></h3>
          <p><?= e(td($rp, 'features')) ?></p>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<?php
$pageTitle = t('products_all_title');
$seoPageDescription = lang() === 'tr'
    ? 'Alvit seramik banyo ürünleri kataloğu. Lavabo, klozet, rezervuar, banyo mobilyası, aksesuar ve daha fazlası. Bartın Seramik A.Ş. üretimi.'
    : 'Alvit ceramic bathroom products catalog. Washbasins, toilets, cisterns, bathroom furniture, accessories and more. Manufactured by Bartın Seramik A.Ş.';
$categories = getCategories();
$allProducts = array_values(getProducts());
$colorFacets = getColorFacets($allProducts);
$collections = getCollections();

$catCounts = $subCounts = [];
foreach ($allProducts as $p) {
    $catCounts[$p['category_id']] = ($catCounts[$p['category_id']] ?? 0) + 1;
    if (!empty($p['subcategory_id'])) {
        $subCounts[$p['subcategory_id']] = ($subCounts[$p['subcategory_id']] ?? 0) + 1;
    }
}

$preselect = isset($_GET['kategori']) ? (string)$_GET['kategori'] : '';

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1><?= t('products_all_title') ?></h1>
    <div class="breadcrumb-light">
      <a href="<?= langUrl() ?>"><?= t('breadcrumb_home') ?></a>
      <span>&rsaquo;</span>
      <span class="current"><?= t('nav_products') ?></span>
    </div>
  </div>
</div>

<section class="section catalog-section">
  <div class="container catalog-layout">

    <aside class="catalog-filters" id="catalogFilters" data-preselect="<?= e($preselect) ?>">
      <div class="filter-head">
        <h2><?= t('products_filter') ?></h2>
        <button type="button" class="filter-clear" id="filterClear"><?= t('products_clear') ?></button>
        <button type="button" class="filter-close" id="filterClose" aria-label="<?= t('products_close') ?>">&times;</button>
      </div>

      <div class="filter-group">
        <input type="search" id="catalogSearch" class="filter-search"
               placeholder="<?= t('products_search_placeholder') ?>" autocomplete="off">
      </div>

      <div class="filter-group">
        <h3><?= t('products_category') ?></h3>
        <?php foreach ($categories as $cat): ?>
        <label class="filter-item">
          <input type="checkbox" data-filter="cat" value="<?= (int)$cat['id'] ?>"
                 data-slug="<?= e($cat['slug']) ?>">
          <span><?= e(td($cat, 'name')) ?></span>
          <em><?= (int)($catCounts[$cat['id']] ?? 0) ?></em>
        </label>
        <?php if (!empty($cat['subcategories'])): ?>
        <div class="filter-sublist">
          <?php foreach ($cat['subcategories'] as $sub): ?>
            <?php if (empty($subCounts[$sub['id']])) continue; ?>
          <label class="filter-item filter-item-sub">
            <input type="checkbox" data-filter="sub" value="<?= (int)$sub['id'] ?>"
                   data-slug="<?= e($sub['slug']) ?>">
            <span><?= e(td($sub, 'name')) ?></span>
            <em><?= (int)$subCounts[$sub['id']] ?></em>
          </label>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <?php if (!empty($collections)): ?>
      <div class="filter-group">
        <h3><?= t('products_collection') ?></h3>
        <?php foreach ($collections as $col): ?>
        <label class="filter-item">
          <input type="checkbox" data-filter="collection" value="<?= e($col['slug']) ?>">
          <span><?= e(td($col, 'title')) ?></span>
          <em><?= (int)$col['count'] ?></em>
        </label>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($colorFacets)): ?>
      <div class="filter-group">
        <h3><?= t('products_color_option') ?></h3>
        <?php foreach ($colorFacets as $color => $count): ?>
        <label class="filter-item">
          <input type="checkbox" data-filter="color" value="<?= e(mb_strtolower($color, 'UTF-8')) ?>">
          <span><?= e($color) ?></span>
          <em><?= (int)$count ?></em>
        </label>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="filter-group">
        <h3><?= t('products_other') ?></h3>
        <label class="filter-item">
          <input type="checkbox" data-filter="docs" value="1">
          <span><?= t('products_has_drawing') ?></span>
        </label>
        <label class="filter-item">
          <input type="checkbox" data-filter="specs" value="1">
          <span><?= t('products_has_table') ?></span>
        </label>
      </div>
    </aside>

    <div class="catalog-main">
      <div class="catalog-toolbar">
        <button type="button" class="filter-toggle" id="filterToggle">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="6" x2="20" y2="6"/><line x1="7" y1="12" x2="17" y2="12"/><line x1="10" y1="18" x2="14" y2="18"/></svg>
          <?= t('products_filter') ?>
        </button>
        <p class="catalog-count"><strong id="catalogCount"><?= count($allProducts) ?></strong> <?= t('product_count_suffix') ?></p>
        <select id="catalogSort" class="catalog-sort" aria-label="<?= t('catalog_sort') ?>">
          <option value="default"><?= t('products_sort_default') ?></option>
          <option value="az"><?= t('products_sort_az') ?></option>
          <option value="za"><?= t('products_sort_za') ?></option>
        </select>
      </div>

      <div class="chip-row" id="activeChips" hidden></div>

      <div class="product-grid" id="catalogGrid">
        <?php foreach ($allProducts as $product): ?>
          <?php
            $colors = array_map(fn($c) => mb_strtolower($c, 'UTF-8'), getProductColors($product));
            $haystack = normalizeSearch(td($product, 'name') . ' ' . ($product['code'] ?? '') . ' ' . td($product, 'features'));
          ?>
        <a href="<?= langUrl('/urun/' . e($product['slug'])) ?>" class="product-card catalog-item"
           data-cat="<?= (int)$product['category_id'] ?>"
           data-sub="<?= (int)($product['subcategory_id'] ?? 0) ?>"
           data-colors="<?= e(implode('|', $colors)) ?>"
           data-collection="<?= e($product['collection'] ?? '') ?>"
           data-docs="<?= empty($product['documents']) ? '0' : '1' ?>"
           data-specs="<?= empty($product['spec_rows']) ? '0' : '1' ?>"
           data-search="<?= e($haystack) ?>"
           data-name="<?= e(mb_strtolower(td($product, 'name'), 'UTF-8')) ?>">
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

      <p class="catalog-empty" id="catalogEmpty" hidden>
        <?= t('products_empty') ?>
      </p>
    </div>
  </div>
</section>

<div class="filter-backdrop" id="filterBackdrop" hidden></div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

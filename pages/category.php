<?php
$category = getCategoryBySlug($categorySlug);
if (!$category) { http_response_code(404); include __DIR__ . '/404.php'; return; }

$pageTitle = $category['name'];
$isSubcategory = isset($category['parent']);
$parentCategory = $isSubcategory ? $category['parent'] : null;

// Eğer alt kategorisi varsa onları göster, yoksa ürünleri göster
$hasSubcategories = !empty($category['subcategories']);

if ($hasSubcategories) {
    $items = $category['subcategories'];
} else {
    $categoryId = $isSubcategory ? $parentCategory['id'] : $category['id'];
    $subcategoryId = $isSubcategory ? $category['id'] : null;
    $items = getProductsByCategory($categoryId, $subcategoryId);

    // Eğer alt kategori değilse ve ürünler direkt bu kategoride ise
    if (!$isSubcategory && empty($items)) {
        $items = getProductsByCategory($category['id']);
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1><?= e($category['name']) ?></h1>
    <div class="breadcrumb-light">
      <a href="/">Anasayfa</a>
      <span>&rsaquo;</span>
      <a href="/kategoriler">Kategoriler</a>
      <?php if ($parentCategory): ?>
      <span>&rsaquo;</span>
      <a href="/kategori/<?= e($parentCategory['slug']) ?>"><?= e($parentCategory['name']) ?></a>
      <?php endif; ?>
      <span>&rsaquo;</span>
      <span class="current"><?= e($category['name']) ?></span>
    </div>
  </div>
</div>

<section class="section">
  <div class="container">
    <?php if ($hasSubcategories): ?>
      <!-- ALT KATEGORİLER -->
      <div class="category-grid">
        <?php foreach ($items as $sub): ?>
        <a href="/kategori/<?= e($sub['slug']) ?>" class="category-card reveal">
          <img src="/<?= !empty($sub['image']) ? e($sub['image']) : 'assets/images/placeholder.svg' ?>" alt="<?= e($sub['name']) ?>" loading="lazy">
          <div class="category-card-overlay">
            <h3><?= e($sub['name']) ?></h3>
            <div class="arrow">&#8594;</div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <!-- ÜRÜNLER -->
      <?php if (empty($items)): ?>
        <div style="text-align:center; padding:60px 0;">
          <p style="font-size:18px; color:var(--text-light);">Bu kategoride henüz ürün bulunmuyor.</p>
        </div>
      <?php else: ?>
      <div class="product-grid">
        <?php foreach ($items as $product): ?>
        <a href="/urun/<?= e($product['slug']) ?>" class="product-card reveal">
          <div class="product-card-image">
            <img src="/<?= getProductImage($product) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
            <?php if (!empty($product['featured'])): ?>
            <span class="product-card-badge">Öne Çıkan</span>
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
    <?php endif; ?>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

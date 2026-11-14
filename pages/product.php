<?php
$product = getProductBySlug($productSlug);
if (!$product) { http_response_code(404); include __DIR__ . '/404.php'; return; }

$pageTitle = $product['name'];
$category = getCategoryById($product['category_id']);
$subcategory = $product['subcategory_id'] ? getCategoryById($product['subcategory_id']) : null;

// Aynı kategorideki diğer ürünler
$relatedProducts = array_slice(array_filter(getProducts(), function($p) use ($product) {
    return $p['category_id'] == $product['category_id'] && $p['id'] != $product['id'];
}), 0, 4);

include __DIR__ . '/../includes/header.php';
?>

<div class="breadcrumb">
  <div class="container">
    <a href="/">Anasayfa</a>
    <span>&rsaquo;</span>
    <a href="/kategoriler">Kategoriler</a>
    <?php if ($category): ?>
    <span>&rsaquo;</span>
    <a href="/kategori/<?= e($category['slug']) ?>"><?= e($category['name']) ?></a>
    <?php endif; ?>
    <?php if ($subcategory): ?>
    <span>&rsaquo;</span>
    <a href="/kategori/<?= e($subcategory['slug']) ?>"><?= e($subcategory['name']) ?></a>
    <?php endif; ?>
    <span>&rsaquo;</span>
    <span class="current"><?= e($product['name']) ?></span>
  </div>
</div>

<section class="product-detail">
  <div class="container">
    <div class="grid-2">
      <!-- ÜRÜN GÖRSELİ -->
      <div class="product-gallery">
        <img src="/<?= getProductImage($product) ?>" alt="<?= e($product['name']) ?>">
      </div>

      <!-- ÜRÜN BİLGİLERİ -->
      <div class="product-info">
        <h1><?= e($product['name']) ?></h1>

        <div class="product-meta">
          <?php if (!empty($product['code'])): ?>
          <div class="product-meta-item">
            <span class="label">Ürün Kodu</span>
            <span class="value"><?= e($product['code']) ?></span>
          </div>
          <?php endif; ?>

          <?php if (!empty($product['features'])): ?>
          <div class="product-meta-item">
            <span class="label">Özellikler</span>
            <span class="value"><?= e($product['features']) ?></span>
          </div>
          <?php endif; ?>

          <?php if (!empty($product['palet'])): ?>
          <div class="product-meta-item">
            <span class="label">Palet Adedi</span>
            <span class="value"><?= e($product['palet']) ?></span>
          </div>
          <?php endif; ?>

          <?php if ($category): ?>
          <div class="product-meta-item">
            <span class="label">Kategori</span>
            <span class="value"><?= e($category['name']) ?></span>
          </div>
          <?php endif; ?>
        </div>

        <?php if (!empty($product['documents'])): ?>
        <div class="product-docs">
          <h3 style="margin-bottom:12px; font-size:16px; font-weight:600;">Teknik Dosyalar</h3>
          <?php foreach ($product['documents'] as $doc): ?>
          <a href="/assets/documents/<?= e($doc) ?>" target="_blank" download>
            &#128196; <?= e($doc) ?>
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div style="margin-top:32px;">
          <a href="/iletisim" class="btn btn-dark">Bilgi Al</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- İLGİLİ ÜRÜNLER -->
<?php if (!empty($relatedProducts)): ?>
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2>İlgili Ürünler</h2>
      <div class="line"></div>
    </div>
    <div class="product-grid">
      <?php foreach ($relatedProducts as $rp): ?>
      <a href="/urun/<?= e($rp['slug']) ?>" class="product-card">
        <div class="product-card-image">
          <img src="/<?= getProductImage($rp) ?>" alt="<?= e($rp['name']) ?>" loading="lazy">
        </div>
        <div class="product-card-body">
          <h3><?= e($rp['name']) ?></h3>
          <p><?= e($rp['features']) ?></p>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

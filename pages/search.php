<?php
$q = trim($_GET['q'] ?? '');
$pageTitle = 'Arama Sonuçları';
$results = $q !== '' ? searchProducts($q) : [];
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1>Arama Sonuçları</h1>
    <div class="breadcrumb-light">
      <a href="/">Anasayfa</a>
      <span>&rsaquo;</span>
      <span class="current"><?= $q !== '' ? '"' . e($q) . '"' : 'Arama' ?></span>
    </div>
  </div>
</div>

<section class="section">
  <div class="container">
    <?php if ($q === ''): ?>
      <div style="text-align:center; padding:60px 0;">
        <p style="font-size:18px; color:var(--text-light);">Aramak için bir ürün adı, kodu veya kategori yazın.</p>
      </div>
    <?php elseif (empty($results)): ?>
      <div style="text-align:center; padding:60px 0;">
        <p style="font-size:18px; color:var(--text-light);"><strong>"<?= e($q) ?>"</strong> için sonuç bulunamadı.</p>
        <p style="margin-top:8px;"><a href="/kategoriler" style="color:var(--accent); font-weight:600;">Tüm kategorilere göz atın &rarr;</a></p>
      </div>
    <?php else: ?>
      <p style="margin-bottom:32px; color:var(--text-light);"><strong><?= count($results) ?></strong> sonuç bulundu &mdash; "<?= e($q) ?>"</p>
      <div class="product-grid">
        <?php foreach ($results as $product): ?>
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
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

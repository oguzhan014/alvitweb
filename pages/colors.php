<?php
$pageTitle = 'Renk Kataloğu';
$colors = getColors();
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1>Renk Kataloğu</h1>
    <div class="breadcrumb-light">
      <a href="/">Anasayfa</a>
      <span>&rsaquo;</span>
      <span class="current">Renkler</span>
    </div>
  </div>
</div>

<section class="section section-compact">
  <div class="container">
    <div class="page-body page-body-compact">
      <p>Standart beyaz üretimimizin yanı sıra seramik ürünlerimizi aşağıdaki renk seçenekleriyle de üretebiliyoruz. Renk talepleriniz için satış ekibimizle iletişime geçebilirsiniz.</p>
    </div>

    <div class="color-grid">
      <?php foreach ($colors as $color): ?>
      <div class="color-card reveal<?= !empty($color['dark']) ? ' color-card-dark' : '' ?>">
        <div class="color-swatch" style="background: <?= e($color['hex']) ?>"></div>
        <div class="color-body">
          <h3><?= e($color['name']) ?></h3>
          <span class="color-hex"><?= e(strtoupper($color['hex'])) ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="cta-banner cta-inline">
      <div class="cta-banner-inner">
        <div>
          <h2>Renkli Ürün Talebi</h2>
          <p>Projeniz için özel renk ihtiyacınızı bizimle paylaşın, birlikte değerlendirelim.</p>
        </div>
        <a href="/iletisim" class="btn btn-primary">Bize Ulaşın</a>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

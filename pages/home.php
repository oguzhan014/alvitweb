<?php
$pageTitle = 'Anasayfa';
$sliders = getSliders();
$categories = getCategories();
$featured = getFeaturedProducts();
$productCount = count(getProducts());
include __DIR__ . '/../includes/header.php';
?>

<!-- HERO SLIDER -->
<section class="hero">
  <?php foreach ($sliders as $i => $slide): ?>
  <div class="hero-slide <?= $i === 0 ? 'active' : '' ?>">
    <img src="/<?= e($slide['image']) ?>" alt="<?= e($slide['title']) ?>" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
    <div class="hero-overlay">
      <div class="hero-content">
        <span class="hero-eyebrow">ALVIT &mdash; benim banyom...</span>
        <h1><?= e($slide['title']) ?></h1>
        <p><?= e($slide['subtitle']) ?></p>
        <a href="<?= e($slide['link']) ?>" class="btn btn-primary">Ürünleri Keşfet</a>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <div class="hero-dots">
    <?php foreach ($sliders as $i => $slide): ?>
    <button class="hero-dot <?= $i === 0 ? 'active' : '' ?>" aria-label="Slayt <?= $i + 1 ?>"></button>
    <?php endforeach; ?>
  </div>
</section>

<!-- GÜVEN ŞERİDİ -->
<section class="trust-bar">
  <div class="trust-bar-bg">
    <img src="/assets/images/about/fabrika-uretim.jpg" alt="Alvit üretim tesisi" loading="lazy">
  </div>
  <div class="trust-bar-overlay"></div>
  <div class="container trust-bar-inner">
    <p class="trust-bar-caption">Bartın Organize Sanayi Bölgesi'ndeki üretim tesisimizden</p>
    <div class="trust-bar-grid">
      <div class="trust-item">
        <div class="trust-value" data-count="1994">0</div>
        <div class="trust-label">Kuruluş Yılı</div>
      </div>
      <div class="trust-item">
        <div class="trust-value">Bartın</div>
        <div class="trust-label">Türk Üretimi</div>
      </div>
      <div class="trust-item">
        <div class="trust-value" data-count="<?= (int)$productCount ?>" data-suffix="+">0</div>
        <div class="trust-label">Ürün Modeli</div>
      </div>
      <div class="trust-item">
        <div class="trust-value" data-count="100" data-suffix="%">0</div>
        <div class="trust-label">Seramik Kalitesi</div>
      </div>
    </div>
  </div>
</section>

<!-- KATEGORİLER -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2>Ürün Kategorileri</h2>
      <p>Banyo ihtiyaçlarınız için geniş ürün yelpazemizi keşfedin</p>
      <div class="line"></div>
    </div>
    <div class="category-grid">
      <?php foreach ($categories as $cat): ?>
      <a href="/kategori/<?= e($cat['slug']) ?>" class="category-card reveal">
        <img src="/<?= getCategoryImage($cat) ?>" alt="<?= e($cat['name']) ?>" loading="lazy">
        <div class="category-card-overlay">
          <h3><?= e($cat['name']) ?></h3>
          <div class="arrow">&#8594;</div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ÖZELLİKLER -->
<section class="section about-section">
  <div class="container">
    <div class="section-header">
      <h2>Neden Alvit?</h2>
      <p>1994'ten bu yana kaliteli üretim</p>
      <div class="line"></div>
    </div>
    <div class="features-grid">
      <div class="feature-item reveal">
        <div class="feature-icon"><?= icon('award', 28) ?></div>
        <h3>Üstün Kalite</h3>
        <p>TSE ve uluslararası standartlara uygun, birinci sınıf hammaddelerle üretim yapıyoruz.</p>
      </div>
      <div class="feature-item reveal">
        <div class="feature-icon"><?= icon('factory', 28) ?></div>
        <h3>Modern Üretim</h3>
        <p>Son teknoloji üretim hatlarımızla yüksek kalite ve verimlilik sağlıyoruz.</p>
      </div>
      <div class="feature-item reveal">
        <div class="feature-icon"><?= icon('layers', 28) ?></div>
        <h3>Geniş Ürün Yelpazesi</h3>
        <p>Lavabo, klozet, banyo mobilyası ve aksesuar - komple banyo çözümleri sunuyoruz.</p>
      </div>
      <a href="/sayfa/kalite-belgelerimiz" class="feature-item reveal feature-item-link">
        <div class="feature-icon"><?= icon('shield', 28) ?></div>
        <h3>Belgeli Kalite</h3>
        <p>Kalite politikamız ve belgelerimiz hakkında detaylı bilgi alın.</p>
      </a>
    </div>
  </div>
</section>

<!-- ÖNE ÇIKANLAR -->
<?php if (!empty($featured)): ?>
<section class="section featured-section">
  <div class="container">
    <div class="section-header">
      <h2>Öne Çıkan Ürünler</h2>
      <p>En popüler ürünlerimizi keşfedin</p>
      <div class="line"></div>
    </div>
  </div>
  <div class="container carousel-wrap">
    <button class="carousel-btn carousel-prev" aria-label="Önceki ürünler" type="button">&#8592;</button>
    <div class="carousel-track">
      <?php foreach ($featured as $product): ?>
      <a href="/urun/<?= e($product['slug']) ?>" class="product-card carousel-item reveal">
        <div class="product-card-image">
          <img src="/<?= getProductImage($product) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
          <span class="product-card-badge">Öne Çıkan</span>
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
    <button class="carousel-btn carousel-next" aria-label="Sonraki ürünler" type="button">&#8594;</button>
  </div>
</section>
<?php endif; ?>

<!-- CTA BANNER -->
<section class="cta-banner">
  <div class="container cta-banner-inner">
    <div>
      <h2>Projeniz İçin Doğru Ürünü Birlikte Bulalım</h2>
      <p>İhtiyacınıza uygun ürün ve çözüm önerileri için ekibimizle iletişime geçin.</p>
    </div>
    <div class="cta-banner-actions">
      <a href="/iletisim" class="btn btn-primary">Bize Ulaşın</a>
      <a href="/iletisim" class="btn btn-outline">Bayilik Başvurusu</a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

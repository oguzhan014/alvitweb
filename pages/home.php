<?php
$pageTitle = 'Anasayfa';
$sliders = array_values(array_filter(getSliders(), fn($s) => !isset($s['active']) || $s['active']));
$categories = getCategories();
$featured = getFeaturedProducts();
$allProducts = getProducts();
$productCount = count($allProducts);
$collections = getCollections();
$colors = getColors();
$kurumsalAll = loadJson('kurumsal.json');
$videoCount = count($kurumsalAll['urun-montaj-videolari']['videos'] ?? []);
$guideCount = count($kurumsalAll['montaj-kilavuzlari']['documents'] ?? []);

$catCounts = [];
foreach ($allProducts as $p) {
    $catCounts[$p['category_id']] = ($catCounts[$p['category_id']] ?? 0) + 1;
}

$innovation = getProductBySlug('safran-ds-ankastre-klozet');
include __DIR__ . '/../includes/header.php';
?>

<!-- AÇILIŞ -->
<section class="hp-hero">
  <div class="hp-hero-inner">
    <?php foreach ($sliders as $i => $slide): ?>
    <div class="hero-slide hp-slide <?= $i === 0 ? 'active' : '' ?>">
      <div class="hp-hero-text">
        <span class="hp-eyebrow">1994'ten beri &middot; Bartın</span>
        <h1><?= e($slide['title']) ?></h1>
        <p><?= e($slide['subtitle']) ?></p>
        <div class="hp-hero-actions">
          <a href="<?= e($slide['link']) ?>" class="btn btn-dark">Ürünleri Keşfet</a>
          <a href="/koleksiyonlar" class="hp-link">Koleksiyonlar <?= icon('layers', 16) ?></a>
        </div>
      </div>
      <div class="hp-hero-media">
        <img src="/<?= e($slide['image']) ?>" alt="<?= e($slide['title']) ?>" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
        <span class="hp-hero-index"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?> / <?= str_pad(count($sliders), 2, '0', STR_PAD_LEFT) ?></span>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php if (count($sliders) > 1): ?>
  <div class="hero-dots hp-dots">
    <?php foreach ($sliders as $i => $slide): ?>
    <button class="hero-dot hp-dot <?= $i === 0 ? 'active' : '' ?>" aria-label="Slayt <?= $i + 1 ?>"></button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<!-- KATEGORİLER -->
<section class="section hp-section">
  <div class="container">
    <div class="hp-head">
      <div>
        <span class="hp-eyebrow">Ürün Gamı</span>
        <h2>Banyonun her köşesi için</h2>
      </div>
      <a href="/urunler" class="hp-link"><?= (int)$productCount ?> ürünün tamamı <?= icon('layers', 16) ?></a>
    </div>
    <div class="hp-bento">
      <?php foreach ($categories as $i => $cat): ?>
      <a href="/kategori/<?= e($cat['slug']) ?>" class="hp-bento-item reveal<?= $i === 0 ? ' hp-bento-large' : '' ?>">
        <img src="/<?= getCategoryImage($cat) ?>" alt="<?= e($cat['name']) ?>" loading="lazy">
        <div class="hp-bento-body">
          <h3><?= e($cat['name']) ?></h3>
          <span><?= (int)($catCounts[$cat['id']] ?? 0) ?> ürün</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- YENİLİK -->
<section class="hp-innovation">
  <div class="container hp-innovation-inner">
    <div class="hp-innovation-media reveal">
      <?php if ($innovation): ?>
      <img src="/<?= getProductImage($innovation) ?>" alt="<?= e($innovation['name']) ?>" loading="lazy">
      <a href="/urun/<?= e($innovation['slug']) ?>" class="hp-innovation-tag"><?= e($innovation['name']) ?></a>
      <?php endif; ?>
    </div>
    <div class="hp-innovation-text">
      <span class="hp-eyebrow">Yenilik</span>
      <h2>Seramiğe yeni işlevler kazandırıyoruz</h2>
      <ul class="hp-points">
        <li class="reveal">
          <span class="hp-point-icon"><?= icon('bulb', 22) ?></span>
          <div>
            <h3>Entegre bataryalı klozet</h3>
            <p>Safran DS Ankastre, sıcak ve soğuk su bağlantılı entegre bataryasıyla birlikte gelir.</p>
          </div>
        </li>
        <li class="reveal">
          <span class="hp-point-icon"><?= icon('shield', 22) ?></span>
          <div>
            <h3>Antibakteriyel yüzey</h3>
            <p>Antibakteriyel yüzey özelliğimiz TSE tarafından belgelenmiştir.</p>
          </div>
        </li>
        <li class="reveal">
          <span class="hp-point-icon"><?= icon('layers', 22) ?></span>
          <div>
            <h3>Dolap uyumlu yeni seriler</h3>
            <p>Edge, Melt ve New Line lavabolar banyo mobilyalarıyla birlikte kullanılmak üzere tasarlandı.</p>
          </div>
        </li>
      </ul>
      <div class="hp-hero-actions">
        <a href="/koleksiyonlar" class="btn btn-primary">Serileri İncele</a>
        <a href="/sayfa/kalite-belgelerimiz" class="hp-link hp-link-light">Belgelerimiz <?= icon('document', 16) ?></a>
      </div>
    </div>
  </div>
</section>

<!-- RENKLER -->
<?php if (!empty($colors)): ?>
<section class="section hp-section">
  <div class="container">
    <div class="hp-head">
      <div>
        <span class="hp-eyebrow">Renk Kataloğu</span>
        <h2>Beyazın ötesinde <?= count($colors) ?> renk</h2>
      </div>
      <a href="/renkler" class="hp-link">Tüm renkler <?= icon('leaf', 16) ?></a>
    </div>
    <a href="/renkler" class="hp-colors reveal">
      <?php foreach ($colors as $color): ?>
      <span class="hp-color<?= !empty($color['dark']) ? ' hp-color-dark' : '' ?>" style="background: <?= e($color['hex']) ?>">
        <em><?= e($color['name']) ?></em>
      </span>
      <?php endforeach; ?>
    </a>
  </div>
</section>
<?php endif; ?>

<!-- HAKKIMIZDA + RAKAMLAR -->
<section class="section hp-about">
  <div class="container hp-about-inner">
    <div class="hp-about-media reveal">
      <img src="/assets/images/about/fabrika-uretim.jpg" alt="Alvit üretim tesisi" loading="lazy">
    </div>
    <div class="hp-about-text">
      <span class="hp-eyebrow">Hakkımızda</span>
      <h2>Bartın'da üretiyoruz, 1994'ten beri</h2>
      <p>Alvit - Bartın Seramik A.Ş. olarak lavabodan klozete, banyo mobilyasından gömme rezervuara kadar banyonun tamamını kendi tesislerimizde üretiyoruz. Üretimimiz TSE ve CE standartlarına göre denetlenir.</p>
      <div class="hp-stats">
        <div>
          <strong class="trust-value" data-count="1994">0</strong>
          <span>Kuruluş</span>
        </div>
        <div>
          <strong class="trust-value" data-count="<?= (int)$productCount ?>" data-suffix="+">0</strong>
          <span>Ürün modeli</span>
        </div>
        <div>
          <strong class="trust-value" data-count="<?= count($collections) ?>">0</strong>
          <span>Koleksiyon</span>
        </div>
      </div>
      <a href="/sayfa/hakkimizda" class="hp-link">Bizi tanıyın <?= icon('factory', 16) ?></a>
    </div>
  </div>
</section>

<!-- KOLEKSİYONLAR -->
<?php if (!empty($collections)): ?>
<section class="section hp-section">
  <div class="container">
    <div class="hp-head">
      <div>
        <span class="hp-eyebrow">Koleksiyonlar</span>
        <h2>Birbiriyle uyumlu seriler</h2>
      </div>
      <a href="/koleksiyonlar" class="hp-link">Tüm seriler <?= icon('layers', 16) ?></a>
    </div>
  </div>
  <div class="container carousel-wrap">
    <button class="carousel-btn carousel-prev" aria-label="Önceki" type="button">&#8592;</button>
    <div class="carousel-track">
      <?php foreach ($collections as $col): ?>
      <a href="/koleksiyon/<?= e($col['slug']) ?>" class="collection-card carousel-item">
        <div class="collection-image">
          <img src="/<?= e($col['image'] ?: 'assets/images/placeholder.svg') ?>" alt="<?= e($col['title']) ?>" loading="lazy">
        </div>
        <div class="collection-body">
          <h3><?= e($col['title']) ?></h3>
          <span class="collection-count"><?= (int)$col['count'] ?> ürün</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <button class="carousel-btn carousel-next" aria-label="Sonraki" type="button">&#8594;</button>
  </div>
</section>
<?php endif; ?>

<!-- ÖNE ÇIKANLAR -->
<?php if (!empty($featured)): ?>
<section class="section featured-section">
  <div class="container">
    <div class="hp-head">
      <div>
        <span class="hp-eyebrow">Seçtiklerimiz</span>
        <h2>Öne çıkan ürünler</h2>
      </div>
      <a href="/urunler" class="hp-link">Kataloğa git <?= icon('layers', 16) ?></a>
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

<!-- MONTAJ DESTEĞİ -->
<section class="section hp-section">
  <div class="container hp-support">
    <a href="/sayfa/urun-montaj-videolari" class="hp-support-card reveal">
      <span class="hp-point-icon"><?= icon('eye', 22) ?></span>
      <div>
        <h3>Montaj videoları</h3>
        <p><?= (int)$videoCount ?> videoda adım adım montaj anlatımı.</p>
      </div>
    </a>
    <a href="/sayfa/montaj-kilavuzlari" class="hp-support-card reveal">
      <span class="hp-point-icon"><?= icon('document', 22) ?></span>
      <div>
        <h3>Montaj kılavuzları</h3>
        <p><?= (int)$guideCount ?> kılavuzu PDF olarak indirin.</p>
      </div>
    </a>
    <a href="/sayfa/kalite-belgelerimiz" class="hp-support-card reveal">
      <span class="hp-point-icon"><?= icon('award', 22) ?></span>
      <div>
        <h3>Kalite belgeleri</h3>
        <p>TSE, CE ve ISO belgelerimizi inceleyin.</p>
      </div>
    </a>
  </div>
</section>

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

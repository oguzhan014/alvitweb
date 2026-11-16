<?php
$pageTitle = t('nav_home');
$sliders = array_values(array_filter(getSliders(), fn($s) => !isset($s['active']) || $s['active']));
$categories = getCategories();
$featured = getFeaturedProducts();
$allProducts = getProducts();
$productCount = count($allProducts);
$collections = getCollections();
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
        <span class="hp-eyebrow"><?= t('hero_since') ?></span>
        <h1><?= e(td($slide, 'title')) ?></h1>
        <p><?= e(td($slide, 'subtitle')) ?></p>
        <div class="hp-hero-actions">
          <a href="<?= langUrl(e($slide['link'])) ?>" class="btn btn-dark"><?= t('hero_btn_products') ?></a>
          <a href="<?= langUrl('/koleksiyonlar') ?>" class="hp-link"><?= t('hero_btn_collections') ?> <?= icon('layers', 16) ?></a>
        </div>
      </div>
      <div class="hp-hero-media">
        <img src="/<?= e($slide['image']) ?>" alt="<?= e(td($slide, 'title')) ?>" loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
        <span class="hp-hero-index"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?> / <?= str_pad(count($sliders), 2, '0', STR_PAD_LEFT) ?></span>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php if (count($sliders) > 1): ?>
  <div class="hero-dots hp-dots">
    <?php foreach ($sliders as $i => $slide): ?>
    <button class="hero-dot hp-dot <?= $i === 0 ? 'active' : '' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</section>

<!-- KATEGORİLER -->
<section class="section hp-section">
  <div class="container">
    <div class="hp-head">
      <div>
        <span class="hp-eyebrow"><?= t('section_product_range') ?></span>
        <h2><?= t('section_product_range_title') ?></h2>
      </div>
      <a href="<?= langUrl('/urunler') ?>" class="hp-link"><?= (int)$productCount ?> <?= t('all_products_link') ?> <?= icon('layers', 16) ?></a>
    </div>
    <div class="hp-bento">
      <?php foreach ($categories as $i => $cat): ?>
      <a href="<?= langUrl('/kategori/' . e($cat['slug'])) ?>" class="hp-bento-item reveal<?= $i === 0 ? ' hp-bento-large' : '' ?>">
        <img src="/<?= getCategoryImage($cat) ?>" alt="<?= e(td($cat, 'name')) ?>" loading="lazy">
        <div class="hp-bento-body">
          <h3><?= e(td($cat, 'name')) ?></h3>
          <span><?= (int)($catCounts[$cat['id']] ?? 0) ?> <?= t('product_count_suffix') ?></span>
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
      <img src="/<?= getProductImage($innovation) ?>" alt="<?= e(td($innovation, 'name')) ?>" loading="lazy">
      <a href="<?= langUrl('/urun/' . e($innovation['slug'])) ?>" class="hp-innovation-tag"><?= e(td($innovation, 'name')) ?></a>
      <?php endif; ?>
    </div>
    <div class="hp-innovation-text">
      <span class="hp-eyebrow"><?= t('section_innovation') ?></span>
      <h2><?= t('section_innovation_title') ?></h2>
      <ul class="hp-points">
        <li class="reveal">
          <span class="hp-point-icon"><?= icon('bulb', 22) ?></span>
          <div>
            <h3><?= t('innovation_1_title') ?></h3>
            <p><?= t('innovation_1_desc') ?></p>
          </div>
        </li>
        <li class="reveal">
          <span class="hp-point-icon"><?= icon('shield', 22) ?></span>
          <div>
            <h3><?= t('innovation_2_title') ?></h3>
            <p><?= t('innovation_2_desc') ?></p>
          </div>
        </li>
        <li class="reveal">
          <span class="hp-point-icon"><?= icon('layers', 22) ?></span>
          <div>
            <h3><?= t('innovation_3_title') ?></h3>
            <p><?= t('innovation_3_desc') ?></p>
          </div>
        </li>
      </ul>
      <div class="hp-hero-actions">
        <a href="<?= langUrl('/koleksiyonlar') ?>" class="btn btn-primary"><?= t('innovation_btn_series') ?></a>
        <a href="<?= langUrl('/sayfa/kalite-belgelerimiz') ?>" class="hp-link hp-link-light"><?= t('innovation_btn_certs') ?> <?= icon('document', 16) ?></a>
      </div>
    </div>
  </div>
</section>

<!-- HAKKIMIZDA + RAKAMLAR -->
<section class="section hp-about">
  <div class="container hp-about-inner">
    <div class="hp-about-media reveal">
      <img src="/assets/images/about/Alvitfabrika.jpeg" alt="Alvit" loading="lazy">
    </div>
    <div class="hp-about-text">
      <span class="hp-eyebrow"><?= t('section_about') ?></span>
      <h2><?= t('section_about_title') ?></h2>
      <p><?= t('section_about_text') ?></p>
      <div class="hp-stats">
        <div>
          <strong class="trust-value" data-count="1994">0</strong>
          <span><?= t('stat_founded') ?></span>
        </div>
        <div>
          <strong class="trust-value" data-count="<?= (int)$productCount ?>" data-suffix="+">0</strong>
          <span><?= t('stat_products') ?></span>
        </div>
        <div>
          <strong class="trust-value" data-count="<?= count($collections) ?>">0</strong>
          <span><?= t('stat_collections') ?></span>
        </div>
      </div>
      <a href="<?= langUrl('/sayfa/hakkimizda') ?>" class="hp-link"><?= t('about_link') ?> <?= icon('factory', 16) ?></a>
    </div>
  </div>
</section>

<!-- KOLEKSİYONLAR -->
<?php if (!empty($collections)): ?>
<section class="section hp-section">
  <div class="container">
    <div class="hp-head">
      <div>
        <span class="hp-eyebrow"><?= t('section_collections') ?></span>
        <h2><?= t('section_collections_title') ?></h2>
      </div>
      <a href="<?= langUrl('/koleksiyonlar') ?>" class="hp-link"><?= t('all_series_link') ?> <?= icon('layers', 16) ?></a>
    </div>
  </div>
  <div class="container carousel-wrap">
    <button class="carousel-btn carousel-prev" aria-label="Previous" type="button">&#8592;</button>
    <div class="carousel-track">
      <?php foreach ($collections as $col): ?>
      <a href="<?= langUrl('/koleksiyon/' . e($col['slug'])) ?>" class="collection-card carousel-item">
        <div class="collection-image">
          <img src="/<?= e($col['image'] ?: 'assets/images/placeholder.svg') ?>" alt="<?= e(td($col, 'title')) ?>" loading="lazy">
        </div>
        <div class="collection-body">
          <h3><?= e(td($col, 'title')) ?></h3>
          <span class="collection-count"><?= (int)$col['count'] ?> <?= t('product_count_suffix') ?></span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <button class="carousel-btn carousel-next" aria-label="Next" type="button">&#8594;</button>
  </div>
</section>
<?php endif; ?>

<!-- ÖNE ÇIKANLAR -->
<?php if (!empty($featured)): ?>
<section class="section featured-section">
  <div class="container">
    <div class="hp-head">
      <div>
        <span class="hp-eyebrow"><?= t('section_featured') ?></span>
        <h2><?= t('section_featured_title') ?></h2>
      </div>
      <a href="<?= langUrl('/urunler') ?>" class="hp-link"><?= t('catalog_link') ?> <?= icon('layers', 16) ?></a>
    </div>
  </div>
  <div class="container carousel-wrap">
    <button class="carousel-btn carousel-prev" aria-label="Previous" type="button">&#8592;</button>
    <div class="carousel-track">
      <?php foreach ($featured as $product): ?>
      <a href="<?= langUrl('/urun/' . e($product['slug'])) ?>" class="product-card carousel-item reveal">
        <div class="product-card-image">
          <img src="/<?= getProductImage($product) ?>" alt="<?= e(td($product, 'name')) ?>" loading="lazy">
          <span class="product-card-badge"><?= t('featured_badge') ?></span>
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
    <button class="carousel-btn carousel-next" aria-label="Next" type="button">&#8594;</button>
  </div>
</section>
<?php endif; ?>

<!-- MONTAJ DESTEĞİ -->
<section class="section hp-section">
  <div class="container hp-support">
    <a href="<?= langUrl('/sayfa/urun-montaj-videolari') ?>" class="hp-support-card reveal">
      <span class="hp-point-icon"><?= icon('eye', 22) ?></span>
      <div>
        <h3><?= t('support_videos_title') ?></h3>
        <p><?= (int)$videoCount ?> <?= t('support_videos_desc') ?></p>
      </div>
    </a>
    <a href="<?= langUrl('/sayfa/montaj-kilavuzlari') ?>" class="hp-support-card reveal">
      <span class="hp-point-icon"><?= icon('document', 22) ?></span>
      <div>
        <h3><?= t('support_guides_title') ?></h3>
        <p><?= (int)$guideCount ?> <?= t('support_guides_desc') ?></p>
      </div>
    </a>
    <a href="<?= langUrl('/sayfa/kalite-belgelerimiz') ?>" class="hp-support-card reveal">
      <span class="hp-point-icon"><?= icon('award', 22) ?></span>
      <div>
        <h3><?= t('support_certs_title') ?></h3>
        <p><?= t('support_certs_desc') ?></p>
      </div>
    </a>
  </div>
</section>

<!-- CTA BANNER -->
<section class="cta-banner">
  <div class="container cta-banner-inner">
    <div>
      <h2><?= t('cta_title') ?></h2>
      <p><?= t('cta_desc') ?></p>
    </div>
    <div class="cta-banner-actions">
      <a href="<?= langUrl('/iletisim') ?>" class="btn btn-primary"><?= t('cta_btn') ?></a>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<?php
$page = getPageBySlug($pageSlug);
if (!$page) { http_response_code(404); include __DIR__ . '/404.php'; return; }

$pageTitle = $page['title'];
$pageType = $page['type'] ?? 'text';

$isCertPage   = $pageType === 'certificates';
$isAboutPage  = $pageType === 'about';
$isVisionPage = $pageType === 'vision';
$isDocsPage   = $pageType === 'documents';
$isVideoPage  = $pageType === 'videos';

$certificates = $isCertPage ? getCertificates() : [];
$productCount = $isAboutPage ? count(getProducts()) : 0;
$kurumsal = ($isDocsPage || $isVideoPage || $pageType === 'text')
    ? getKurumsalData($page['slug']) : [];
include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1><?= e($page['title']) ?></h1>
    <div class="breadcrumb-light">
      <a href="/">Anasayfa</a>
      <span>&rsaquo;</span>
      <span class="current"><?= e($page['title']) ?></span>
    </div>
  </div>
</div>

<section class="section<?= ($isCertPage || $isDocsPage || $isVideoPage) ? ' section-compact' : '' ?>">
  <div class="container">
    <?php if ($isVisionPage): ?>
    <div class="vm-intro">
      <?= $page['content'] ?>
    </div>

    <div class="vm-grid">
      <?php foreach ($page['blocks'] ?? [] as $i => $block): ?>
      <article class="vm-card reveal">
        <span class="vm-index"><?= str_pad($i + 1, 2, '0', STR_PAD_LEFT) ?></span>
        <div class="vm-icon"><?= icon($block['icon'], 26) ?></div>
        <h3><?= e($block['label']) ?></h3>
        <p><?= e($block['text']) ?></p>
      </article>
      <?php endforeach; ?>
    </div>

    <?php if (!empty($page['values'])): ?>
    <div class="hp-head values-head-row">
      <div>
        <span class="hp-eyebrow">Değerlerimiz</span>
        <h2>Her adımda bizi yönlendiren ilkeler</h2>
      </div>
    </div>
    <div class="values-grid">
      <?php foreach ($page['values'] as $value): ?>
      <div class="value-card reveal">
        <div class="value-icon"><?= icon($value['icon'], 22) ?></div>
        <div>
          <h3><?= e($value['title']) ?></h3>
          <p><?= e($value['text']) ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="vm-commit">
      <div class="vm-commit-text">
        <h2>Sözümüzün arkasındayız</h2>
        <p>Bu ilkeleri yazıda bırakmıyoruz: üretimimiz TSE ve CE standartlarına göre denetleniyor,
           kalite yönetim sistemimiz ISO 9001 kapsamında belgeleniyor.</p>
      </div>
      <div class="vm-commit-actions">
        <a href="/sayfa/kalite-belgelerimiz" class="btn btn-primary">Belgelerimizi İnceleyin</a>
        <a href="/sayfa/kalite-politikasi" class="btn btn-outline">Kalite Politikamız</a>
      </div>
    </div>
    <?php elseif ($isAboutPage): ?>
    <div class="hp-about-inner about-split">
      <div class="hp-about-media reveal">
        <img src="/assets/images/about/fabrika-uretim.jpg" alt="Alvit üretim tesisi">
        <span class="about-photo-caption">Bartın'daki üretim tesisimizden</span>
      </div>
      <div class="hp-about-text">
        <span class="hp-eyebrow">Biz kimiz</span>
        <h2>1994'ten beri Bartın'da seramik üretiyoruz</h2>
        <div class="about-copy"><?= $page['content'] ?></div>
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
            <strong class="trust-value" data-count="<?= count(getCertificates()) ?>">0</strong>
            <span>Kalite belgesi</span>
          </div>
        </div>
      </div>
    </div>

    <div class="hp-head about-links-head">
      <div>
        <span class="hp-eyebrow">Kurumsal</span>
        <h2>Nasıl çalıştığımızı inceleyin</h2>
      </div>
    </div>
    <?php else: ?>
    <div class="page-body<?= ($isCertPage || $isDocsPage || $isVideoPage) ? ' page-body-compact' : '' ?>">
      <?= $page['content'] ?>
    </div>
    <?php endif; ?>

    <?php if ($isAboutPage): ?>
    <div class="about-links">
      <a href="/sayfa/vizyon-misyon" class="feature-item reveal feature-item-link">
        <div class="feature-icon"><?= icon('target', 28) ?></div>
        <h3>Vizyon &amp; Misyon</h3>
        <p>Geleceğe bakışımız ve bizi bu noktaya taşıyan misyonumuz hakkında bilgi alın.</p>
      </a>
      <a href="/sayfa/kalite-politikasi" class="feature-item reveal feature-item-link">
        <div class="feature-icon"><?= icon('shield', 28) ?></div>
        <h3>Kalite Politikamız</h3>
        <p>Üretim sürecimizin her aşamasında nasıl bir kalite anlayışı benimsediğimizi keşfedin.</p>
      </a>
      <a href="/sayfa/kalite-belgelerimiz" class="feature-item reveal feature-item-link">
        <div class="feature-icon"><?= icon('document', 28) ?></div>
        <h3>Kalite Belgelerimiz</h3>
        <p>TSE, CE ve ISO belgelerimizi inceleyin, isterseniz indirin.</p>
      </a>
    </div>
    <?php endif; ?>

    <?php if ($isDocsPage && !empty($kurumsal['documents'])): ?>
      <?php $groups = groupDocumentsByYear($kurumsal['documents']); ?>
    <div class="doc-section">
      <?php foreach ($groups as $groupName => $docs): ?>
      <div class="doc-group">
        <?php if (count($groups) > 1): ?>
        <h3 class="doc-group-title"><?= e($groupName) ?></h3>
        <?php endif; ?>
        <div class="doc-list">
          <?php foreach ($docs as $doc): ?>
          <a href="/<?= e($doc['file']) ?>" target="_blank" rel="noopener" class="doc-item reveal">
            <span class="doc-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            </span>
            <span class="doc-name"><?= e($doc['name']) ?></span>
            <span class="doc-action">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            </span>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($isVideoPage && !empty($kurumsal['videos'])): ?>
    <div class="video-grid">
      <?php foreach ($kurumsal['videos'] as $video): ?>
      <div class="video-card reveal">
        <div class="video-frame">
          <iframe src="https://www.youtube-nocookie.com/embed/<?= e($video['id']) ?>"
                  title="<?= e($video['title']) ?>" loading="lazy"
                  allow="accelerometer; clipboard-write; encrypted-media; picture-in-picture"
                  allowfullscreen></iframe>
        </div>
        <h3><?= e($video['title']) ?></h3>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!$isDocsPage && !empty($kurumsal['documents'])): ?>
    <div class="doc-list doc-list-inline">
      <?php foreach ($kurumsal['documents'] as $doc): ?>
      <a href="/<?= e($doc['file']) ?>" target="_blank" rel="noopener" class="doc-item">
        <span class="doc-icon">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        </span>
        <span class="doc-name"><?= e($doc['name']) ?></span>
        <span class="doc-action">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        </span>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if ($isCertPage && !empty($certificates)):
      $grouped = [];
      foreach ($certificates as $c) { $grouped[$c['category']][] = $c; }
    ?>
    <div class="cert-section">
      <?php foreach ($grouped as $groupName => $items): ?>
      <div class="cert-group">
        <h3 class="cert-group-title"><?= e($groupName) ?></h3>
        <div class="cert-grid">
          <?php foreach ($items as $cert): ?>
          <div class="cert-card reveal">
            <a href="/<?= e($cert['file']) ?>" target="_blank" rel="noopener" class="cert-thumb" aria-label="Büyüt">
              <img src="/<?= e($cert['file']) ?>" alt="<?= e($cert['name']) ?>" loading="lazy">
              <span class="cert-zoom">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
              </span>
            </a>
            <div class="cert-info">
              <h4><?= e($cert['name']) ?></h4>
              <a href="/<?= e($cert['file']) ?>" download class="cert-download">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                İndir
              </a>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php if ($isAboutPage): ?>
<?php
  $aboutCats = getCategories();
  $aboutCounts = [];
  foreach (getProducts() as $ap) {
      $aboutCounts[$ap['category_id']] = ($aboutCounts[$ap['category_id']] ?? 0) + 1;
  }
?>
<section class="hp-innovation about-range">
  <div class="container">
    <div class="hp-head">
      <div>
        <span class="hp-eyebrow">Ne üretiyoruz</span>
        <h2>Banyonun tamamı, tek üreticiden</h2>
      </div>
      <a href="/urunler" class="hp-link hp-link-light">Tüm ürünler <?= icon('layers', 16) ?></a>
    </div>
    <div class="about-range-grid">
      <?php foreach ($aboutCats as $ac): ?>
      <a href="/kategori/<?= e($ac['slug']) ?>" class="about-range-item reveal">
        <strong><?= (int)($aboutCounts[$ac['id']] ?? 0) ?></strong>
        <span><?= e($ac['name']) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>

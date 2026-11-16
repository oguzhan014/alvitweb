<?php
$pageTitle = t('nav_gallery');
$images = glob(__DIR__ . '/../assets/images/gallery/*.{jpg,jpeg,png,webp}', GLOB_BRACE);
sort($images);

include __DIR__ . '/../includes/header.php';
?>

<section class="page-banner">
  <div class="container">
    <h1><?= t('nav_gallery') ?></h1>
    <p><?= t('gallery_desc') ?></p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="gallery-intro">
      <span class="hp-eyebrow"><?= t('gallery_eyebrow') ?></span>
      <h2><?= t('gallery_title') ?></h2>
      <p><?= t('gallery_desc') ?></p>
    </div>
    <div class="gallery-grid">
      <?php foreach ($images as $i => $img):
        $filename = basename($img);
        $src = '/assets/images/gallery/' . $filename;
      ?>
      <a href="<?= $src ?>" class="gallery-item reveal" data-gallery>
        <img src="<?= $src ?>" alt="Alvit galeri <?= $i + 1 ?>" loading="lazy">
        <div class="gallery-overlay">
          <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/><line x1="11" y1="8" x2="11" y2="14"/><line x1="8" y1="11" x2="14" y2="11"/></svg>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<div class="lightbox" id="lightbox">
  <button class="lightbox-close" aria-label="<?= lang() === 'tr' ? 'Kapat' : 'Close' ?>">&times;</button>
  <button class="lightbox-prev" aria-label="<?= lang() === 'tr' ? 'Önceki' : 'Previous' ?>">&#8592;</button>
  <button class="lightbox-next" aria-label="<?= lang() === 'tr' ? 'Sonraki' : 'Next' ?>">&#8594;</button>
  <div class="lightbox-content">
    <img src="" alt="" id="lightboxImg">
  </div>
  <div class="lightbox-counter" id="lightboxCounter"></div>
</div>

<script>
(function() {
  const items = document.querySelectorAll('[data-gallery]');
  const lb = document.getElementById('lightbox');
  const img = document.getElementById('lightboxImg');
  const counter = document.getElementById('lightboxCounter');
  let current = 0;

  function open(index) {
    current = index;
    img.src = items[current].href;
    counter.textContent = (current + 1) + ' / ' + items.length;
    lb.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function close() {
    lb.classList.remove('active');
    document.body.style.overflow = '';
  }

  function nav(dir) {
    current = (current + dir + items.length) % items.length;
    img.src = items[current].href;
    counter.textContent = (current + 1) + ' / ' + items.length;
  }

  items.forEach(function(el, i) {
    el.addEventListener('click', function(e) {
      e.preventDefault();
      open(i);
    });
  });

  lb.querySelector('.lightbox-close').addEventListener('click', close);
  lb.querySelector('.lightbox-prev').addEventListener('click', function() { nav(-1); });
  lb.querySelector('.lightbox-next').addEventListener('click', function() { nav(1); });

  lb.addEventListener('click', function(e) {
    if (e.target === lb || e.target.classList.contains('lightbox-content')) close();
  });

  document.addEventListener('keydown', function(e) {
    if (!lb.classList.contains('active')) return;
    if (e.key === 'Escape') close();
    if (e.key === 'ArrowLeft') nav(-1);
    if (e.key === 'ArrowRight') nav(1);
  });
})();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

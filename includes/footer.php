<?php $settings = getSettings(); $categories = getCategories(); $L = lang(); ?>

<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-col">
        <a href="<?= langUrl() ?>" class="footer-logo"><img src="/<?= e($settings['logo_footer'] ?? $settings['logo']) ?>" alt="<?= e($settings['site_name']) ?>"></a>
        <p><?= e($settings['site_description']) ?></p>
        <div class="footer-social">
          <?php if (!empty($settings['social']['facebook'])): ?>
            <a href="<?= e($settings['social']['facebook']) ?>" target="_blank" rel="noopener">f</a>
          <?php endif; ?>
          <?php if (!empty($settings['social']['instagram'])): ?>
            <a href="<?= e($settings['social']['instagram']) ?>" target="_blank" rel="noopener">ig</a>
          <?php endif; ?>
          <?php if (!empty($settings['social']['youtube'])): ?>
            <a href="<?= e($settings['social']['youtube']) ?>" target="_blank" rel="noopener">yt</a>
          <?php endif; ?>
        </div>
      </div>

      <div class="footer-col">
        <h4><?= t('footer_products') ?></h4>
        <ul>
          <?php foreach ($categories as $cat): ?>
            <li><a href="<?= langUrl('/kategori/' . e($cat['slug'])) ?>"><?= e(td($cat, 'name')) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="footer-col">
        <h4><?= t('footer_corporate') ?></h4>
        <ul>
          <li><a href="<?= langUrl('/sayfa/hakkimizda') ?>"><?= $L === 'tr' ? 'Hakkımızda' : 'About Us' ?></a></li>
          <li><a href="<?= langUrl('/sayfa/vizyon-misyon') ?>"><?= $L === 'tr' ? 'Vizyon & Misyon' : 'Vision & Mission' ?></a></li>
          <li><a href="<?= langUrl('/sayfa/kalite-politikasi') ?>"><?= $L === 'tr' ? 'Kalite Politikası' : 'Quality Policy' ?></a></li>
          <li><a href="<?= langUrl('/sayfa/kalite-belgelerimiz') ?>"><?= $L === 'tr' ? 'Kalite Belgelerimiz' : 'Our Certificates' ?></a></li>
          <li><a href="<?= langUrl('/sayfa/raporlar') ?>"><?= $L === 'tr' ? 'Raporlar' : 'Reports' ?></a></li>
          <li><a href="<?= langUrl('/galeri') ?>"><?= t('nav_gallery') ?></a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4><?= t('footer_support') ?></h4>
        <ul>
          <li><a href="<?= langUrl('/sayfa/montaj-kilavuzlari') ?>"><?= t('footer_guides') ?></a></li>
          <li><a href="<?= langUrl('/sayfa/urun-montaj-videolari') ?>"><?= t('footer_videos') ?></a></li>
          <li><a href="<?= langUrl('/urunler') ?>"><?= t('footer_catalog') ?></a></li>
          <li><a href="<?= langUrl('/iletisim') ?>"><?= t('footer_contact') ?></a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4><?= t('footer_contact') ?></h4>
        <ul>
          <li><?= e($settings['phone']) ?></li>
          <li><?= e($settings['email']) ?></li>
          <li><?= e($settings['address']) ?></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <p>&copy; <?= date('Y') ?> Alvit - Bartın Seramik A.Ş. <?= $L === 'tr' ? 'Tüm hakları saklıdır.' : 'All rights reserved.' ?></p>
      <nav class="footer-legal">
        <a href="<?= langUrl('/sayfa/kisisel-verilerin-korunmasi-politikasi') ?>"><?= $L === 'tr' ? 'KVKK Politikası' : 'Privacy Policy' ?></a>
        <a href="<?= langUrl('/sayfa/cerez-aydinlatma-metni') ?>"><?= $L === 'tr' ? 'Çerez Aydınlatma Metni' : 'Cookie Policy' ?></a>
        <a href="<?= langUrl('/sayfa/kvkk-ilgili-kisi-basvuru-formu') ?>"><?= $L === 'tr' ? 'KVKK Başvuru Formu' : 'GDPR Request Form' ?></a>
      </nav>
    </div>
  </div>
</footer>

<?= seoOrganizationSchema() ?>
<?= seoLocalBusinessSchema() ?>
<?= seoWebSiteSchema() ?>
<?php if (!empty($seoStructuredData)) echo $seoStructuredData; ?>

<div id="cookieBanner" class="cookie-banner" style="display:none;">
  <div class="cookie-banner-inner">
    <p><?= $L === 'tr'
        ? 'Bu web sitesi, deneyiminizi iyileştirmek için çerezler kullanmaktadır. Detaylı bilgi için <a href="' . langUrl('/sayfa/cerez-aydinlatma-metni') . '">Çerez Aydınlatma Metni</a>\'ni inceleyebilirsiniz.'
        : 'This website uses cookies to improve your experience. For details, please see our <a href="' . langUrl('/sayfa/cerez-aydinlatma-metni') . '">Cookie Policy</a>.' ?></p>
    <div class="cookie-banner-actions">
      <button onclick="acceptCookies()" class="cookie-btn cookie-btn-accept"><?= $L === 'tr' ? 'Kabul Et' : 'Accept' ?></button>
      <button onclick="rejectCookies()" class="cookie-btn cookie-btn-reject"><?= $L === 'tr' ? 'Reddet' : 'Reject' ?></button>
    </div>
  </div>
</div>

<script>window._lang = '<?= $L ?>';</script>
<script src="/assets/js/main.js"></script>
</body>
</html>

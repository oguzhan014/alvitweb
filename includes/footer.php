<?php $settings = getSettings(); $categories = getCategories(); ?>

<footer class="footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-col">
        <a href="/" class="footer-logo"><img src="/<?= e($settings['logo_footer'] ?? $settings['logo']) ?>" alt="<?= e($settings['site_name']) ?>"></a>
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
        <h4>Ürünler</h4>
        <ul>
          <?php foreach ($categories as $cat): ?>
            <li><a href="/kategori/<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="footer-col">
        <h4>Kurumsal</h4>
        <ul>
          <li><a href="/sayfa/hakkimizda">Hakkımızda</a></li>
          <li><a href="/sayfa/vizyon-misyon">Vizyon & Misyon</a></li>
          <li><a href="/sayfa/kalite-politikasi">Kalite Politikası</a></li>
          <li><a href="/sayfa/kalite-belgelerimiz">Kalite Belgelerimiz</a></li>
        </ul>
      </div>

      <div class="footer-col">
        <h4>İletişim</h4>
        <ul>
          <li><?= e($settings['phone']) ?></li>
          <li><?= e($settings['email']) ?></li>
          <li><?= e($settings['address']) ?></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <p><?= e($settings['footer_text']) ?></p>
    </div>
  </div>
</footer>

<script src="/assets/js/main.js"></script>
</body>
</html>

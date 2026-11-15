<?php
$pageTitle = 'İletişim';
$settings = getSettings();

// Form gönderimi
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basit form işleme - gerçek projede e-posta gönderimi eklenebilir
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';

    if ($name && $email && $message) {
        // Mesajları JSON dosyasına kaydet
        $messages = loadJson('messages.json');
        $messages[] = [
            'id' => count($messages) + 1,
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'date' => date('Y-m-d H:i:s'),
            'read' => false
        ];
        saveJson('messages.json', $messages);
        $success = true;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1>İletişim</h1>
    <div class="breadcrumb-light">
      <a href="/">Anasayfa</a>
      <span>&rsaquo;</span>
      <span class="current">İletişim</span>
    </div>
  </div>
</div>

<section class="section section-compact">
  <div class="container">
    <div class="contact-cards">
      <a href="tel:<?= e(preg_replace('/\s+/', '', $settings['phone'])) ?>" class="hp-support-card reveal">
        <span class="hp-point-icon"><?= icon('phone', 22) ?></span>
        <div>
          <h3>Telefon</h3>
          <p><?= e($settings['phone']) ?></p>
        </div>
      </a>
      <a href="mailto:<?= e($settings['email']) ?>" class="hp-support-card reveal">
        <span class="hp-point-icon"><?= icon('mail', 22) ?></span>
        <div>
          <h3>E-posta</h3>
          <p><?= e($settings['email']) ?></p>
        </div>
      </a>
      <div class="hp-support-card reveal">
        <span class="hp-point-icon"><?= icon('pin', 22) ?></span>
        <div>
          <h3>Adres</h3>
          <p><?= e($settings['address']) ?></p>
        </div>
      </div>
    </div>

    <div class="contact-split">
      <div class="contact-form-box">
        <span class="hp-eyebrow">Bize yazın</span>
        <h2>Sorunuzu veya talebinizi iletin</h2>
        <p class="contact-lead">Ürün bilgisi ve teklif talepleriniz için formu doldurun; ekibimiz size dönüş yapsın.</p>

        <?php if ($success): ?>
        <div class="contact-success">
          Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.
        </div>
        <?php endif; ?>

        <form method="POST" class="contact-form">
          <input type="text" name="name" placeholder="Adınız Soyadınız" required>
          <input type="email" name="email" placeholder="E-posta Adresiniz" required>
          <input type="text" name="subject" placeholder="Konu">
          <textarea name="message" placeholder="Mesajınız" required></textarea>
          <button type="submit" class="btn btn-dark">Mesaj Gönder</button>
        </form>
      </div>

      <div class="contact-map">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2836.02421410033!2d32.29310119999999!3d41.5983918!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x409b71ab7726ff29%3A0x573fad42f35e810e!2sALVIT%20-%20BARTIN%20SERAMIK%20SAN.%20TIC.%20A.%C5%9E.!5e1!3m2!1str!2str!4v1794763948233!5m2!1str!2str"
          title="Alvit - Bartın Seramik konumu" style="border:0;" allowfullscreen loading="lazy"
          referrerpolicy="strict-origin-when-cross-origin"></iframe>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

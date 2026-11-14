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

<section class="section">
  <div class="container">
    <div class="contact-grid">
      <!-- FORM -->
      <div>
        <h2 style="font-family:var(--font-display); font-size:28px; margin-bottom:24px; color:var(--primary);">Bize Ulaşın</h2>

        <?php if ($success): ?>
        <div style="background:#d4edda; color:#155724; padding:16px; border-radius:8px; margin-bottom:20px;">
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

      <!-- BİLGİLER -->
      <div>
        <h2 style="font-family:var(--font-display); font-size:28px; margin-bottom:24px; color:var(--primary);">İletişim Bilgileri</h2>

        <div class="contact-info-item">
          <div class="contact-info-icon">&#9742;</div>
          <div>
            <h4>Telefon</h4>
            <p><?= e($settings['phone']) ?></p>
          </div>
        </div>

        <div class="contact-info-item">
          <div class="contact-info-icon">&#9993;</div>
          <div>
            <h4>E-posta</h4>
            <p><?= e($settings['email']) ?></p>
          </div>
        </div>

        <div class="contact-info-item">
          <div class="contact-info-icon">&#9873;</div>
          <div>
            <h4>Adres</h4>
            <p><?= e($settings['address']) ?></p>
          </div>
        </div>

        <div style="margin-top:32px; border-radius:12px; overflow:hidden; height:250px; background:var(--bg-light);">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3003.5!2d32.3!3d41.6!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zQmFydMSxbg!5e0!3m2!1str!2str!4v1"
            width="100%" height="250" style="border:0;" allowfullscreen loading="lazy">
          </iframe>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<?php
$pageTitle = t('nav_contact');
$seoPageDescription = lang() === 'tr'
    ? 'Alvit - Bartın Seramik A.Ş. ile iletişime geçin. Adres: Şiremir Tabaklar Köyü, Bartın. Telefon, e-posta ve iletişim formu.'
    : 'Contact Alvit - Bartın Seramik A.Ş. Address: Şiremir Tabaklar Village, Bartın, Turkey. Phone, email and contact form.';
$settings = getSettings();

// Form gönderimi (PRG pattern)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = cleanPostText($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = cleanPostText($_POST['subject'] ?? '');
    $message = cleanPostText($_POST['message'] ?? '');

    if ($name && $email && $message) {
        $messages = loadJson('messages.json');
        $maxId = 0;
        foreach ($messages as $m) { if (($m['id'] ?? 0) > $maxId) $maxId = $m['id']; }
        $messages[] = [
            'id' => $maxId + 1,
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'message' => $message,
            'date' => date('Y-m-d H:i:s'),
            'read' => false
        ];
        saveJson('messages.json', $messages);
    }
    header('Location: ' . langUrl('/iletisim') . '?ok=1');
    exit;
}
$success = isset($_GET['ok']);

include __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
  <div class="container">
    <h1><?= t('nav_contact') ?></h1>
    <div class="breadcrumb-light">
      <a href="<?= langUrl() ?>"><?= t('breadcrumb_home') ?></a>
      <span>&rsaquo;</span>
      <span class="current"><?= t('nav_contact') ?></span>
    </div>
  </div>
</div>

<section class="section section-compact">
  <div class="container">
    <div class="contact-cards">
      <a href="tel:<?= e(preg_replace('/\s+/', '', $settings['phone'])) ?>" class="hp-support-card reveal">
        <span class="hp-point-icon"><?= icon('phone', 22) ?></span>
        <div>
          <h3><?= t('contact_phone') ?></h3>
          <p><?= e($settings['phone']) ?></p>
        </div>
      </a>
      <a href="mailto:<?= e($settings['email']) ?>" class="hp-support-card reveal">
        <span class="hp-point-icon"><?= icon('mail', 22) ?></span>
        <div>
          <h3><?= t('contact_email') ?></h3>
          <p><?= e($settings['email']) ?></p>
        </div>
      </a>
      <div class="hp-support-card reveal">
        <span class="hp-point-icon"><?= icon('pin', 22) ?></span>
        <div>
          <h3><?= t('contact_address') ?></h3>
          <p><?= e($settings['address']) ?></p>
        </div>
      </div>
    </div>

    <div class="contact-split">
      <div class="contact-form-box">
        <span class="hp-eyebrow"><?= t('contact_form_eyebrow') ?></span>
        <h2><?= t('contact_form_title') ?></h2>
        <p class="contact-lead"><?= t('contact_form_lead') ?></p>

        <?php if ($success): ?>
        <div class="contact-success">
          <?= t('contact_form_success') ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="contact-form">
          <input type="text" name="name" placeholder="<?= t('contact_form_name') ?>" required>
          <input type="email" name="email" placeholder="<?= t('contact_form_email') ?>" required>
          <input type="text" name="subject" placeholder="<?= t('contact_form_subject') ?>">
          <textarea name="message" placeholder="<?= t('contact_form_message') ?>" required></textarea>
          <button type="submit" class="btn btn-dark"><?= t('contact_form_submit') ?></button>
        </form>
      </div>

      <div class="contact-map">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2836.02421410033!2d32.29310119999999!3d41.5983918!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x409b71ab7726ff29%3A0x573fad42f35e810e!2sALVIT%20-%20BARTIN%20SERAMIK%20SAN.%20TIC.%20A.%C5%9E.!5e1!3m2!1str!2str!4v1794763948233!5m2!1str!2str"
          title="Alvit - Bartın Seramik" style="border:0;" allowfullscreen loading="lazy"
          referrerpolicy="strict-origin-when-cross-origin"></iframe>
      </div>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

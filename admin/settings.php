<?php
require_once __DIR__ . '/layout.php';
$settings = getSettings();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings['site_name'] = trim($_POST['site_name'] ?? $settings['site_name']);
    $settings['site_slogan'] = trim($_POST['site_slogan'] ?? $settings['site_slogan']);
    $settings['site_description'] = trim($_POST['site_description'] ?? $settings['site_description']);
    $settings['phone'] = trim($_POST['phone'] ?? $settings['phone']);
    $settings['email'] = trim($_POST['email'] ?? $settings['email']);
    $settings['address'] = trim($_POST['address'] ?? $settings['address']);
    $settings['social']['facebook'] = trim($_POST['facebook'] ?? '');
    $settings['social']['instagram'] = trim($_POST['instagram'] ?? '');
    $settings['social']['youtube'] = trim($_POST['youtube'] ?? '');
    $settings['social']['linkedin'] = trim($_POST['linkedin'] ?? '');
    $settings['footer_text'] = trim($_POST['footer_text'] ?? $settings['footer_text']);

    // Şifre değiştirme (giriş kontrolü bu hash'e bakar)
    $passwordChanged = false;
    if (!empty($_POST['new_password'])) {
        $settings['admin_pass'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $passwordChanged = true;
    }

    saveJson('settings.json', $settings);
    adminRedirect('/admin/settings', $passwordChanged ? 'password' : 'saved');
}

$message = adminFlash([
    'saved'    => 'Ayarlar kaydedildi.',
    'password' => 'Ayarlar kaydedildi. Yeni şifreniz bir sonraki girişte geçerli olacak.',
]);

adminHeader('Ayarlar');
?>

<div class="admin-topbar">
  <h1>Site Ayarları</h1>
</div>

<?php if ($message): ?>
<div class="alert alert-success"><?= e($message) ?></div>
<?php endif; ?>

<div class="admin-card">
  <form method="POST">
    <h3 style="margin-bottom:20px; font-size:16px; color:var(--admin-primary);">Genel Bilgiler</h3>

    <div class="form-row">
      <div class="form-group">
        <label>Site Adı</label>
        <input type="text" name="site_name" class="form-control" value="<?= e($settings['site_name']) ?>">
      </div>
      <div class="form-group">
        <label>Slogan</label>
        <input type="text" name="site_slogan" class="form-control" value="<?= e($settings['site_slogan']) ?>">
      </div>
    </div>

    <div class="form-group">
      <label>Site Açıklaması</label>
      <textarea name="site_description" class="form-control"><?= e($settings['site_description']) ?></textarea>
    </div>

    <h3 style="margin:32px 0 20px; font-size:16px; color:var(--admin-primary);">İletişim Bilgileri</h3>

    <div class="form-row">
      <div class="form-group">
        <label>Telefon</label>
        <input type="text" name="phone" class="form-control" value="<?= e($settings['phone']) ?>">
      </div>
      <div class="form-group">
        <label>E-posta</label>
        <input type="email" name="email" class="form-control" value="<?= e($settings['email']) ?>">
      </div>
    </div>

    <div class="form-group">
      <label>Adres</label>
      <input type="text" name="address" class="form-control" value="<?= e($settings['address']) ?>">
    </div>

    <h3 style="margin:32px 0 20px; font-size:16px; color:var(--admin-primary);">Sosyal Medya</h3>

    <div class="form-row">
      <div class="form-group">
        <label>Facebook</label>
        <input type="url" name="facebook" class="form-control" value="<?= e($settings['social']['facebook'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Instagram</label>
        <input type="url" name="instagram" class="form-control" value="<?= e($settings['social']['instagram'] ?? '') ?>">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>YouTube</label>
        <input type="url" name="youtube" class="form-control" value="<?= e($settings['social']['youtube'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>LinkedIn</label>
        <input type="url" name="linkedin" class="form-control" value="<?= e($settings['social']['linkedin'] ?? '') ?>">
      </div>
    </div>

    <div class="form-group">
      <label>Footer Yazısı</label>
      <input type="text" name="footer_text" class="form-control" value="<?= e($settings['footer_text']) ?>">
    </div>

    <h3 style="margin:32px 0 20px; font-size:16px; color:var(--admin-primary);">Şifre Değiştir</h3>

    <div class="form-group">
      <label>Yeni Şifre (değiştirmek istemiyorsanız boş bırakın)</label>
      <input type="password" name="new_password" class="form-control" placeholder="Yeni şifre">
    </div>

    <button type="submit" class="btn-admin btn-admin-success" style="margin-top:12px;">Ayarları Kaydet</button>
  </form>
</div>

<?php adminFooter(); ?>

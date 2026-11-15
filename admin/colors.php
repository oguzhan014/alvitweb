<?php
require_once __DIR__ . '/layout.php';

$colors = loadJson('colors.json');
$message = '';

// SİL
if (preg_match('#/colors/delete/([a-z0-9-]+)$#', $adminUri, $m)) {
    unset($colors[$m[1]]);
    saveJson('colors.json', $colors);
    header('Location: /admin/colors?msg=deleted');
    exit;
}

// KAYDET
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = cleanPostText($_POST);
    $origSlug = trim($post['orig_slug'] ?? '');
    $name = trim($post['name'] ?? '');
    $hex = strtolower(trim($post['hex'] ?? ''));
    $slugInput = trim($post['slug'] ?? '');
    $slug = slugify($slugInput !== '' ? $slugInput : $name);

    if ($name !== '' && $slug !== '' && preg_match('/^#[0-9a-f]{6}$/', $hex)) {
        $entry = $colors[$origSlug] ?? [];
        $entry['name'] = $name;
        $entry['slug'] = $slug;
        $entry['hex'] = $hex;

        // Koyu renklerde kart yazısı beyaza döner
        [$r, $g, $b] = sscanf($hex, "#%02x%02x%02x");
        $entry['dark'] = (0.299 * $r + 0.587 * $g + 0.114 * $b) < 140;

        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $up = uploadImage($_FILES['image'], 'colors');
            if ($up) $entry['image'] = 'assets/images/colors/' . $up;
        }
        $entry['image'] = $entry['image'] ?? '';

        if ($origSlug !== '' && $origSlug !== $slug) unset($colors[$origSlug]);
        $colors[$slug] = $entry;
        saveJson('colors.json', $colors);
        $colors = loadJson('colors.json');
        $message = 'Renk kaydedildi.';
    } else {
        $message = 'Ad zorunlu, renk kodu #rrggbb biçiminde olmalı.';
    }
}

$editSlug = null;
if (preg_match('#/colors/edit/([a-z0-9-]+)$#', $adminUri, $m)) $editSlug = $m[1];
$isForm = $editSlug !== null || strpos($adminUri, '/colors/add') !== false;
$edit = $editSlug !== null ? ($colors[$editSlug] ?? null) : null;

if (!empty($_GET['msg']) && $_GET['msg'] === 'deleted') $message = 'Renk silindi.';

adminHeader('Renk Kataloğu');

if ($isForm):
?>

<div class="admin-topbar">
  <h1><?= $edit ? 'Renk Düzenle' : 'Yeni Renk' ?></h1>
  <a href="/admin/colors" class="btn-admin btn-admin-info">&larr; Geri</a>
</div>

<?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>

<div class="admin-card">
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="orig_slug" value="<?= e($editSlug ?? '') ?>">
    <div class="form-row">
      <div class="form-group">
        <label>Renk Adı *</label>
        <input type="text" name="name" class="form-control" value="<?= e($edit['name'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Adres (slug)</label>
        <input type="text" name="slug" class="form-control" value="<?= e($edit['slug'] ?? '') ?>" placeholder="otomatik oluşur">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Renk Kodu *</label>
        <input type="color" name="hex" class="form-control" style="height:48px; padding:4px;"
               value="<?= e($edit['hex'] ?? '#cccccc') ?>" required>
      </div>
      <div class="form-group">
        <label>Karteladan görsel (opsiyonel)</label>
        <input type="file" name="image" class="form-control" accept="image/*">
      </div>
    </div>

    <button type="submit" class="btn-admin btn-admin-success">Kaydet</button>
  </form>
</div>

<?php else: ?>

<div class="admin-topbar">
  <h1>Renk Kataloğu (<?= count($colors) ?>)</h1>
  <a href="/admin/colors/add" class="btn-admin btn-admin-primary">+ Yeni Renk</a>
</div>

<?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>

<div class="admin-card">
  <table class="admin-table">
    <thead><tr><th>Renk</th><th>Ad</th><th>Kod</th><th>İşlem</th></tr></thead>
    <tbody>
      <?php foreach ($colors as $slug => $color): ?>
      <tr>
        <td><span style="display:block;width:46px;height:46px;border-radius:6px;border:1px solid #ddd;background:<?= e($color['hex'] ?? '#fff') ?>"></span></td>
        <td><strong><?= e($color['name']) ?></strong></td>
        <td><code><?= e(strtoupper($color['hex'] ?? '')) ?></code></td>
        <td>
          <a href="/admin/colors/edit/<?= e($slug) ?>" class="btn-admin btn-admin-info">Düzenle</a>
          <a href="/admin/colors/delete/<?= e($slug) ?>" class="btn-admin btn-admin-danger"
             onclick="return confirm('Renk silinsin mi?')">Sil</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php endif; ?>

<?php adminFooter(); ?>

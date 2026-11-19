<?php
require_once __DIR__ . '/layout.php';
$sliders = getSliders();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $titleEn = trim($_POST['title_en'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $subtitleEn = trim($_POST['subtitle_en'] ?? '');
    $link = trim($_POST['link'] ?? '');
    $active = isset($_POST['active']);

    $image = '';
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploaded = uploadImage($_FILES['image'], 'sliders');
        if ($uploaded) $image = 'assets/images/sliders/' . $uploaded;
    }

    if ($id) {
        foreach ($sliders as &$s) {
            if ($s['id'] == $id) {
                $s['title'] = $title;
                $s['title_en'] = $titleEn;
                $s['subtitle'] = $subtitle;
                $s['subtitle_en'] = $subtitleEn;
                $s['link'] = $link;
                $s['active'] = $active;
                if ($image) $s['image'] = $image;
                break;
            }
        }
        unset($s);
        saveJson('sliders.json', $sliders);
        adminRedirect('/admin/sliders/edit/' . (int)$id, 'updated');
    } else {
        $sliders[] = [
            'id' => getNextId($sliders),
            'title' => $title,
            'title_en' => $titleEn,
            'subtitle' => $subtitle,
            'subtitle_en' => $subtitleEn,
            'image' => $image ?: 'assets/images/sliders/default.jpg',
            'link' => $link,
            'active' => $active,
            'order' => count($sliders) + 1
        ];
        $newId = $sliders[count($sliders) - 1]['id'];
        saveJson('sliders.json', $sliders);
        adminRedirect('/admin/sliders/edit/' . (int)$newId, 'added');
    }
}

$message = adminFlash([
    'added'   => 'Slider eklendi.',
    'updated' => 'Slider güncellendi.',
    'deleted' => 'Slider silindi.',
]);

$action = 'list';
$editSlider = null;
if (strpos($adminUri, '/sliders/add') !== false) {
    $action = 'form';
} elseif (preg_match('#/sliders/edit/(\d+)$#', $adminUri, $m)) {
    $action = 'form';
    foreach ($sliders as $s) {
        if ($s['id'] == (int)$m[1]) { $editSlider = $s; break; }
    }
}

adminHeader('Slider Yönetimi');

if ($action === 'form'):
?>

<div class="admin-topbar">
  <h1><?= $editSlider ? 'Slider Düzenle' : 'Yeni Slider' ?></h1>
  <a href="/admin/sliders" class="btn-admin btn-admin-info">&larr; Geri</a>
</div>

<?php if ($message): ?>
<div class="alert alert-success"><?= e($message) ?></div>
<?php endif; ?>

<div class="admin-card">
  <form method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>
    <?php if ($editSlider): ?>
    <input type="hidden" name="id" value="<?= $editSlider['id'] ?>">
    <?php endif; ?>

    <div class="form-row">
      <div class="form-group">
        <label>Başlık *</label>
        <input type="text" name="title" class="form-control" value="<?= e($editSlider['title'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Title (EN)</label>
        <input type="text" name="title_en" class="form-control" value="<?= e($editSlider['title_en'] ?? '') ?>" placeholder="İngilizce başlık">
      </div>
    </div>
    <div class="form-row">
      <div class="form-group">
        <label>Alt Başlık</label>
        <input type="text" name="subtitle" class="form-control" value="<?= e($editSlider['subtitle'] ?? '') ?>">
      </div>
      <div class="form-group">
        <label>Subtitle (EN)</label>
        <input type="text" name="subtitle_en" class="form-control" value="<?= e($editSlider['subtitle_en'] ?? '') ?>" placeholder="İngilizce alt başlık">
      </div>
    </div>
    <div class="form-group">
      <label>Link</label>
      <input type="text" name="link" class="form-control" value="<?= e($editSlider['link'] ?? '') ?>" placeholder="/kategori/seramik-banyo-urunleri">
    </div>
    <div class="form-group">
      <label>Slider Görseli</label>
      <div class="file-input-wrapper" onclick="this.querySelector('input').click()">
        <input type="file" name="image" accept="image/*">
        <p>Görsel yüklemek için tıklayın (1920x800 px önerilir)</p>
      </div>
    </div>
    <div class="form-group">
      <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
        <input type="checkbox" name="active" <?= ($editSlider['active'] ?? true) ? 'checked' : '' ?>>
        Aktif
      </label>
    </div>
    <button type="submit" class="btn-admin btn-admin-success">
      <?= $editSlider ? 'Güncelle' : 'Slider Ekle' ?>
    </button>
  </form>
</div>

<?php else: ?>

<div class="admin-topbar">
  <h1>Slider Yönetimi</h1>
  <a href="/admin/sliders/add" class="btn-admin btn-admin-primary">+ Yeni Slider</a>
</div>

<?php if ($message): ?>
<div class="alert alert-success"><?= e($message) ?></div>
<?php endif; ?>

<div class="admin-card">
  <table class="admin-table">
    <thead>
      <tr><th>Görsel</th><th>Başlık</th><th>Alt Başlık</th><th>Durum</th><th>İşlem</th></tr>
    </thead>
    <tbody>
      <?php foreach ($sliders as $s): ?>
      <tr>
        <td><img src="/<?= e($s['image']) ?>" alt="" style="width:120px; height:50px; object-fit:cover;"></td>
        <td><strong><?= e($s['title']) ?></strong></td>
        <td><?= e($s['subtitle']) ?></td>
        <td><?= $s['active'] ? '<span style="color:green;">Aktif</span>' : '<span style="color:red;">Pasif</span>' ?></td>
        <td>
          <a href="/admin/sliders/edit/<?= $s['id'] ?>" class="btn-admin btn-admin-info btn-admin-sm">Düzenle</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php endif; ?>

<?php adminFooter(); ?>

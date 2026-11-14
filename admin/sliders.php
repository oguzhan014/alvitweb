<?php
require_once __DIR__ . '/layout.php';
$sliders = getSliders();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
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
                $s['subtitle'] = $subtitle;
                $s['link'] = $link;
                $s['active'] = $active;
                if ($image) $s['image'] = $image;
                break;
            }
        }
        unset($s);
        $message = 'Slider güncellendi.';
    } else {
        $sliders[] = [
            'id' => getNextId($sliders),
            'title' => $title,
            'subtitle' => $subtitle,
            'image' => $image ?: 'assets/images/sliders/default.jpg',
            'link' => $link,
            'active' => $active,
            'order' => count($sliders) + 1
        ];
        $message = 'Slider eklendi.';
    }
    saveJson('sliders.json', $sliders);
    $sliders = getSliders();
}

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

<div class="admin-card">
  <form method="POST" enctype="multipart/form-data">
    <?php if ($editSlider): ?>
    <input type="hidden" name="id" value="<?= $editSlider['id'] ?>">
    <?php endif; ?>

    <div class="form-group">
      <label>Başlık *</label>
      <input type="text" name="title" class="form-control" value="<?= e($editSlider['title'] ?? '') ?>" required>
    </div>
    <div class="form-group">
      <label>Alt Başlık</label>
      <input type="text" name="subtitle" class="form-control" value="<?= e($editSlider['subtitle'] ?? '') ?>">
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

<?php
require_once __DIR__ . '/layout.php';

$certificates = getCertificates();
$message = '';

// SİL
if (preg_match('#/certificates/delete/(\d+)$#', $adminUri, $m)) {
    $delId = (int)$m[1];
    $certificates = array_values(array_filter($certificates, fn($c) => $c['id'] != $delId));
    saveJson('certificates.json', $certificates);
    header('Location: /admin/certificates?msg=deleted');
    exit;
}

// KAYDET
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = cleanPostText($_POST);
    $id = (int)($post['id'] ?? 0);
    $name = trim($post['name'] ?? '');
    $category = trim($post['category'] ?? '');

    $file = trim($post['current_file'] ?? '');
    if (!empty($_FILES['file']['name']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $up = uploadImage($_FILES['file'], 'certificates');
        if ($up) $file = 'assets/images/certificates/' . $up;
    }

    if ($name !== '' && $file !== '') {
        if ($id) {
            foreach ($certificates as &$c) {
                if ($c['id'] == $id) {
                    $c['name'] = $name;
                    $c['category'] = $category ?: 'Diğer Belgeler';
                    $c['file'] = $file;
                    break;
                }
            }
            unset($c);
            saveJson('certificates.json', $certificates);
            adminRedirect('/admin/certificates/edit/' . (int)$id, 'updated');
        } else {
            $newId = getNextId($certificates);
            $certificates[] = [
                'id' => $newId,
                'name' => $name,
                'category' => $category ?: 'Diğer Belgeler',
                'file' => $file,
            ];
            saveJson('certificates.json', $certificates);
            adminRedirect('/admin/certificates/edit/' . (int)$newId, 'added');
        }
    }
    $message = 'Belge adı ve görseli zorunlu.';
}

$message = $message ?: adminFlash([
    'added'   => 'Belge eklendi.',
    'updated' => 'Belge güncellendi.',
    'deleted' => 'Belge silindi.',
]);

$editCert = null;
if (preg_match('#/certificates/edit/(\d+)$#', $adminUri, $m)) {
    foreach ($certificates as $c) {
        if ($c['id'] == (int)$m[1]) { $editCert = $c; break; }
    }
}
$isForm = $editCert !== null || strpos($adminUri, '/certificates/add') !== false;

$groups = [];
foreach ($certificates as $c) $groups[$c['category']][] = $c;

adminHeader('Kalite Belgeleri');

if ($isForm):
?>

<div class="admin-topbar">
  <h1><?= $editCert ? 'Belge Düzenle' : 'Yeni Belge' ?></h1>
  <a href="/admin/certificates" class="btn-admin btn-admin-info">&larr; Geri</a>
</div>

<?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>

<div class="admin-card">
  <form method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>
    <?php if ($editCert): ?>
    <input type="hidden" name="id" value="<?= (int)$editCert['id'] ?>">
    <input type="hidden" name="current_file" value="<?= e($editCert['file']) ?>">
    <?php endif; ?>

    <div class="form-group">
      <label>Belge Adı *</label>
      <input type="text" name="name" class="form-control" value="<?= e($editCert['name'] ?? '') ?>" required>
    </div>

    <div class="form-group">
      <label>Grup</label>
      <input type="text" name="category" class="form-control" list="certGroups"
             value="<?= e($editCert['category'] ?? '') ?>" placeholder="Ör: Ürün Belgeleri">
      <datalist id="certGroups">
        <?php foreach (array_keys($groups) as $g): ?>
        <option value="<?= e($g) ?>"></option>
        <?php endforeach; ?>
      </datalist>
    </div>

    <div class="form-group">
      <label>Belge Görseli <?= $editCert ? '(değiştirmek için seçin)' : '*' ?></label>
      <div class="file-input-wrapper" onclick="this.querySelector('input').click()">
        <input type="file" name="file" accept="image/*" <?= $editCert ? '' : 'required' ?>>
        <p>Görsel yüklemek için tıklayın</p>
        <small>JPG veya PNG</small>
      </div>
      <?php if ($editCert): ?>
      <div class="image-preview">
        <div class="image-preview-item"><img src="/<?= e($editCert['file']) ?>" alt=""></div>
      </div>
      <?php endif; ?>
    </div>

    <button type="submit" class="btn-admin btn-admin-success">Kaydet</button>
  </form>
</div>

<?php else: ?>

<div class="admin-topbar">
  <h1>Kalite Belgeleri (<?= count($certificates) ?>)</h1>
  <a href="/admin/certificates/add" class="btn-admin btn-admin-primary">+ Yeni Belge</a>
</div>

<?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>

<?php foreach ($groups as $groupName => $items): ?>
<div class="admin-card">
  <h3 style="margin-bottom:16px; font-size:15px; color:var(--admin-primary);"><?= e($groupName) ?></h3>
  <table class="admin-table">
    <thead><tr><th>Belge</th><th>Ad</th><th>İşlem</th></tr></thead>
    <tbody>
      <?php foreach ($items as $cert): ?>
      <tr>
        <td><img src="/<?= e($cert['file']) ?>" alt="" style="width:46px;height:60px;object-fit:cover;border-radius:4px;border:1px solid #ddd;"></td>
        <td><?= e($cert['name']) ?></td>
        <td>
          <a href="/<?= e($cert['file']) ?>" target="_blank" class="btn-admin btn-admin-info">Aç</a>
          <a href="/admin/certificates/edit/<?= (int)$cert['id'] ?>" class="btn-admin btn-admin-info">Düzenle</a>
          <a href="/admin/certificates/delete/<?= (int)$cert['id'] ?>" class="btn-admin btn-admin-danger"
             onclick="return confirm('Belge silinsin mi?')">Sil</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php endforeach; ?>

<?php endif; ?>

<?php adminFooter(); ?>

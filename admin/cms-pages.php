<?php
require_once __DIR__ . '/layout.php';
$pages = getPages();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $slug = slugify($title);
    $content = $_POST['content'] ?? '';

    if ($id) {
        foreach ($pages as &$p) {
            if ($p['id'] == $id) {
                $p['title'] = $title;
                $p['slug'] = $slug;
                $p['content'] = $content;
                break;
            }
        }
        unset($p);
        $message = 'Sayfa güncellendi.';
    } else {
        $pages[] = [
            'id' => getNextId($pages),
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'order' => count($pages) + 1
        ];
        $message = 'Sayfa eklendi.';
    }
    saveJson('pages.json', $pages);
    $pages = getPages();
}

$action = 'list';
$editPage = null;
if (strpos($adminUri, '/pages/add') !== false) {
    $action = 'form';
} elseif (preg_match('#/pages/edit/(\d+)$#', $adminUri, $m)) {
    $action = 'form';
    $editPage = getPageBySlug('') ?: null;
    foreach ($pages as $p) {
        if ($p['id'] == (int)$m[1]) { $editPage = $p; break; }
    }
}

adminHeader('Sayfalar');

if ($action === 'form'):
?>

<div class="admin-topbar">
  <h1><?= $editPage ? 'Sayfa Düzenle' : 'Yeni Sayfa' ?></h1>
  <a href="/admin/pages" class="btn-admin btn-admin-info">&larr; Geri</a>
</div>

<div class="admin-card">
  <form method="POST">
    <?php if ($editPage): ?>
    <input type="hidden" name="id" value="<?= $editPage['id'] ?>">
    <?php endif; ?>

    <div class="form-group">
      <label>Sayfa Başlığı *</label>
      <input type="text" name="title" class="form-control" value="<?= e($editPage['title'] ?? '') ?>" required>
    </div>
    <div class="form-group">
      <label>İçerik (HTML destekli)</label>
      <textarea name="content" class="form-control" style="height:300px;"><?= e($editPage['content'] ?? '') ?></textarea>
    </div>
    <button type="submit" class="btn-admin btn-admin-success">
      <?= $editPage ? 'Güncelle' : 'Sayfa Ekle' ?>
    </button>
  </form>
</div>

<?php else: ?>

<div class="admin-topbar">
  <h1>Sayfalar</h1>
  <a href="/admin/pages/add" class="btn-admin btn-admin-primary">+ Yeni Sayfa</a>
</div>

<?php if ($message): ?>
<div class="alert alert-success"><?= e($message) ?></div>
<?php endif; ?>

<div class="admin-card">
  <table class="admin-table">
    <thead>
      <tr><th>Sayfa Adı</th><th>URL</th><th>İşlem</th></tr>
    </thead>
    <tbody>
      <?php foreach ($pages as $p): ?>
      <tr>
        <td><strong><?= e($p['title']) ?></strong></td>
        <td><a href="/sayfa/<?= e($p['slug']) ?>" target="_blank">/sayfa/<?= e($p['slug']) ?></a></td>
        <td>
          <a href="/admin/pages/edit/<?= $p['id'] ?>" class="btn-admin btn-admin-info btn-admin-sm">Düzenle</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php endif; ?>

<?php adminFooter(); ?>

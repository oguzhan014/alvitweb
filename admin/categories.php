<?php
require_once __DIR__ . '/layout.php';
$categories = getCategories();
$message = '';

// Form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $slug = slugify($name);
    $description = trim($_POST['description'] ?? '');
    $parentId = $_POST['parent_id'] ? (int)$_POST['parent_id'] : null;

    // Görsel yükleme
    $image = '';
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploaded = uploadImage($_FILES['image'], 'categories');
        if ($uploaded) $image = 'assets/images/categories/' . $uploaded;
    }

    if ($id) {
        // GÜNCELLE
        foreach ($categories as &$cat) {
            if ($cat['id'] == $id) {
                $cat['name'] = $name;
                $cat['slug'] = $slug;
                $cat['description'] = $description;
                if ($image) $cat['image'] = $image;
                break;
            }
            // Alt kategoride mi?
            if (!empty($cat['subcategories'])) {
                foreach ($cat['subcategories'] as &$sub) {
                    if ($sub['id'] == $id) {
                        $sub['name'] = $name;
                        $sub['slug'] = $slug;
                        if ($image) $sub['image'] = $image;
                        break 2;
                    }
                }
                unset($sub);
            }
        }
        unset($cat);
        $message = 'Kategori güncellendi.';
    } else {
        // YENİ EKLE
        if ($parentId) {
            // Alt kategori olarak ekle
            foreach ($categories as &$cat) {
                if ($cat['id'] == $parentId) {
                    if (empty($cat['subcategories'])) $cat['subcategories'] = [];
                    $subId = 100 * $cat['id'] + count($cat['subcategories']) + 1;
                    $cat['subcategories'][] = [
                        'id' => $subId,
                        'name' => $name,
                        'slug' => $slug,
                        'image' => $image
                    ];
                    break;
                }
            }
            unset($cat);
        } else {
            // Ana kategori
            $categories[] = [
                'id' => getNextId($categories),
                'name' => $name,
                'slug' => $slug,
                'image' => $image,
                'description' => $description,
                'order' => count($categories) + 1,
                'subcategories' => []
            ];
        }
        $message = 'Kategori eklendi.';
    }
    saveJson('categories.json', $categories);
    $categories = getCategories();
}

$action = 'list';
$editCat = null;

if (strpos($adminUri, '/categories/add') !== false) {
    $action = 'form';
} elseif (preg_match('#/categories/edit/(\d+)$#', $adminUri, $m)) {
    $action = 'form';
    $editId = (int)$m[1];
    foreach ($categories as $cat) {
        if ($cat['id'] == $editId) { $editCat = $cat; break; }
        if (!empty($cat['subcategories'])) {
            foreach ($cat['subcategories'] as $sub) {
                if ($sub['id'] == $editId) { $editCat = $sub; break 2; }
            }
        }
    }
}

adminHeader('Kategoriler');

if ($action === 'form'):
?>

<div class="admin-topbar">
  <h1><?= $editCat ? 'Kategori Düzenle' : 'Yeni Kategori' ?></h1>
  <a href="/admin/categories" class="btn-admin btn-admin-info">&larr; Geri</a>
</div>

<?php if ($message): ?>
<div class="alert alert-success"><?= e($message) ?></div>
<?php endif; ?>

<div class="admin-card">
  <form method="POST" enctype="multipart/form-data">
    <?php if ($editCat): ?>
    <input type="hidden" name="id" value="<?= $editCat['id'] ?>">
    <?php endif; ?>

    <div class="form-group">
      <label>Kategori Adı *</label>
      <input type="text" name="name" class="form-control" value="<?= e($editCat['name'] ?? '') ?>" required>
    </div>

    <?php if (!$editCat): ?>
    <div class="form-group">
      <label>Üst Kategori (alt kategori olarak eklemek için)</label>
      <select name="parent_id" class="form-control">
        <option value="">Ana Kategori Olarak Ekle</option>
        <?php foreach ($categories as $cat): ?>
        <option value="<?= $cat['id'] ?>"><?= e($cat['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php endif; ?>

    <div class="form-group">
      <label>Açıklama</label>
      <textarea name="description" class="form-control"><?= e($editCat['description'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
      <label>Kategori Görseli</label>
      <div class="file-input-wrapper" onclick="this.querySelector('input').click()">
        <input type="file" name="image" accept="image/*">
        <p>Görsel yüklemek için tıklayın</p>
      </div>
    </div>

    <button type="submit" class="btn-admin btn-admin-success">
      <?= $editCat ? 'Güncelle' : 'Kategori Ekle' ?>
    </button>
  </form>
</div>

<?php else: ?>

<div class="admin-topbar">
  <h1>Kategoriler</h1>
  <a href="/admin/categories/add" class="btn-admin btn-admin-primary">+ Yeni Kategori</a>
</div>

<?php if ($message): ?>
<div class="alert alert-success"><?= e($message) ?></div>
<?php endif; ?>

<div class="admin-card">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Kategori Adı</th>
        <th>Alt Kategoriler</th>
        <th>Ürün Sayısı</th>
        <th>İşlem</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($categories as $cat): ?>
      <tr>
        <td><strong><?= e($cat['name']) ?></strong></td>
        <td>
          <?php if (!empty($cat['subcategories'])): ?>
            <?php foreach ($cat['subcategories'] as $sub): ?>
              <span style="display:inline-block; background:#e8e8e8; padding:2px 8px; border-radius:4px; margin:2px; font-size:12px;">
                <?= e($sub['name']) ?>
                <a href="/admin/categories/edit/<?= $sub['id'] ?>" style="color:var(--admin-accent);">&#9998;</a>
              </span>
            <?php endforeach; ?>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
        <td><?= count(getProductsByCategory($cat['id'])) ?></td>
        <td>
          <a href="/admin/categories/edit/<?= $cat['id'] ?>" class="btn-admin btn-admin-info btn-admin-sm">Düzenle</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php endif; ?>

<?php adminFooter(); ?>

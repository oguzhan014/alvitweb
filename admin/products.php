<?php
require_once __DIR__ . '/layout.php';
$products = getProducts();
$categories = getCategories();

// Tüm alt kategorileri düz liste yap
$allSubcats = [];
foreach ($categories as $cat) {
    if (!empty($cat['subcategories'])) {
        foreach ($cat['subcategories'] as $sub) {
            $allSubcats[$sub['id']] = $cat['name'] . ' > ' . $sub['name'];
        }
    }
}

$action = 'list';
$editProduct = null;
$message = '';

// DELETE
if (preg_match('#/products/delete/(\d+)$#', $adminUri, $m)) {
    $delId = (int)$m[1];
    $products = array_values(array_filter($products, fn($p) => $p['id'] != $delId));
    saveJson('products.json', $products);
    header('Location: /admin/products?msg=deleted');
    exit;
}

// ADD / EDIT form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = trim($_POST['name'] ?? '');
    $code = trim($_POST['code'] ?? '');
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $subcategoryId = $_POST['subcategory_id'] ? (int)$_POST['subcategory_id'] : null;
    $features = trim($_POST['features'] ?? '');
    $palet = trim($_POST['palet'] ?? '');
    $featured = isset($_POST['featured']);
    $slug = slugify($name);

    // Görsel yükleme
    $images = [];
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploaded = uploadImage($_FILES['image'], 'products');
        if ($uploaded) $images[] = $uploaded;
    }

    if ($id) {
        // GÜNCELLE
        foreach ($products as &$p) {
            if ($p['id'] == $id) {
                $p['name'] = $name;
                $p['slug'] = $slug;
                $p['code'] = $code;
                $p['category_id'] = $categoryId;
                $p['subcategory_id'] = $subcategoryId;
                $p['features'] = $features;
                $p['palet'] = $palet;
                $p['featured'] = $featured;
                if (!empty($images)) $p['images'] = $images;
                break;
            }
        }
        unset($p);
        $message = 'Ürün güncellendi.';
    } else {
        // YENİ EKLE
        $newProduct = [
            'id' => getNextId($products),
            'name' => $name,
            'slug' => $slug,
            'code' => $code,
            'category_id' => $categoryId,
            'subcategory_id' => $subcategoryId,
            'features' => $features,
            'palet' => $palet,
            'images' => $images,
            'documents' => [],
            'featured' => $featured
        ];
        $products[] = $newProduct;
        $message = 'Ürün eklendi.';
    }
    saveJson('products.json', $products);
    $products = getProducts(); // reload
}

// Determine action
if (strpos($adminUri, '/products/add') !== false) {
    $action = 'form';
} elseif (preg_match('#/products/edit/(\d+)$#', $adminUri, $m)) {
    $action = 'form';
    $editProduct = getProductById((int)$m[1]);
}

if (!empty($_GET['msg']) && $_GET['msg'] === 'deleted') {
    $message = 'Ürün silindi.';
}

adminHeader('Ürünler');

if ($action === 'form'):
?>

<!-- ÜRÜN FORMU -->
<div class="admin-topbar">
  <h1><?= $editProduct ? 'Ürün Düzenle' : 'Yeni Ürün Ekle' ?></h1>
  <a href="/admin/products" class="btn-admin btn-admin-info">&larr; Geri</a>
</div>

<?php if ($message): ?>
<div class="alert alert-success"><?= e($message) ?></div>
<?php endif; ?>

<div class="admin-card">
  <form method="POST" enctype="multipart/form-data">
    <?php if ($editProduct): ?>
    <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
    <?php endif; ?>

    <div class="form-row">
      <div class="form-group">
        <label>Ürün Adı *</label>
        <input type="text" name="name" class="form-control" value="<?= e($editProduct['name'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Ürün Kodu</label>
        <input type="text" name="code" class="form-control" value="<?= e($editProduct['code'] ?? '') ?>">
      </div>
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Ana Kategori *</label>
        <select name="category_id" class="form-control" required id="categorySelect">
          <option value="">Seçin...</option>
          <?php foreach ($categories as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= ($editProduct['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
            <?= e($cat['name']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Alt Kategori</label>
        <select name="subcategory_id" class="form-control" id="subcategorySelect">
          <option value="">Yok / Seçin...</option>
          <?php foreach ($categories as $cat): ?>
            <?php if (!empty($cat['subcategories'])): ?>
              <?php foreach ($cat['subcategories'] as $sub): ?>
              <option value="<?= $sub['id'] ?>" data-parent="<?= $cat['id'] ?>" <?= ($editProduct['subcategory_id'] ?? '') == $sub['id'] ? 'selected' : '' ?>>
                <?= e($sub['name']) ?>
              </option>
              <?php endforeach; ?>
            <?php endif; ?>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label>Ürün Özellikleri</label>
      <input type="text" name="features" class="form-control" value="<?= e($editProduct['features'] ?? '') ?>" placeholder="Ör: Dolap Uyumlu Lavabo">
    </div>

    <div class="form-row">
      <div class="form-group">
        <label>Palet Adedi</label>
        <input type="text" name="palet" class="form-control" value="<?= e($editProduct['palet'] ?? '') ?>">
      </div>
      <div class="form-group" style="display:flex; align-items:end; padding-bottom:12px;">
        <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
          <input type="checkbox" name="featured" <?= !empty($editProduct['featured']) ? 'checked' : '' ?>>
          Öne Çıkan Ürün
        </label>
      </div>
    </div>

    <div class="form-group">
      <label>Ürün Görseli</label>
      <div class="file-input-wrapper" onclick="this.querySelector('input').click()">
        <input type="file" name="image" accept="image/*">
        <p>Görsel yüklemek için tıklayın</p>
        <small>JPG, PNG, WebP - Max 5MB</small>
      </div>
      <?php if ($editProduct && !empty($editProduct['images'])): ?>
      <div class="image-preview">
        <?php foreach ($editProduct['images'] as $img): ?>
        <div class="image-preview-item">
          <img src="/assets/images/products/<?= e($img) ?>" alt="">
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <button type="submit" class="btn-admin btn-admin-success" style="margin-top:12px;">
      <?= $editProduct ? 'Güncelle' : 'Ürünü Kaydet' ?>
    </button>
  </form>
</div>

<script>
// Alt kategori filtreleme
document.getElementById('categorySelect').addEventListener('change', function() {
  const parentId = this.value;
  const subSelect = document.getElementById('subcategorySelect');
  Array.from(subSelect.options).forEach(opt => {
    if (opt.value === '') { opt.style.display = ''; return; }
    opt.style.display = opt.dataset.parent === parentId ? '' : 'none';
    if (opt.dataset.parent !== parentId) opt.selected = false;
  });
});
</script>

<?php else: ?>

<!-- ÜRÜN LİSTESİ -->
<div class="admin-topbar">
  <h1>Ürünler (<?= count($products) ?>)</h1>
  <a href="/admin/products/add" class="btn-admin btn-admin-primary">+ Yeni Ürün</a>
</div>

<?php if ($message): ?>
<div class="alert alert-success"><?= e($message) ?></div>
<?php endif; ?>

<div class="admin-card">
  <table class="admin-table">
    <thead>
      <tr>
        <th>Görsel</th>
        <th>Ürün Adı</th>
        <th>Kod</th>
        <th>Kategori</th>
        <th>Öne Çıkan</th>
        <th>İşlemler</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($products as $p): ?>
      <tr>
        <td><img src="/<?= getProductImage($p) ?>" alt=""></td>
        <td><strong><?= e($p['name']) ?></strong></td>
        <td><?= e($p['code'] ?: '-') ?></td>
        <td>
          <?php
          $cat = getCategoryById($p['category_id']);
          echo $cat ? e($cat['name']) : '-';
          if ($p['subcategory_id']) {
              $sub = getCategoryById($p['subcategory_id']);
              if ($sub) echo ' > ' . e($sub['name']);
          }
          ?>
        </td>
        <td><?= !empty($p['featured']) ? '&#11088;' : '-' ?></td>
        <td>
          <a href="/admin/products/edit/<?= $p['id'] ?>" class="btn-admin btn-admin-info btn-admin-sm">Düzenle</a>
          <a href="/admin/products/delete/<?= $p['id'] ?>" class="btn-admin btn-admin-danger btn-admin-sm" onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz?')">Sil</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php endif; ?>

<?php adminFooter(); ?>

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
    if (empty($_POST) && empty($_FILES) && isset($_SERVER['CONTENT_LENGTH']) && (int)$_SERVER['CONTENT_LENGTH'] > 0) {
        $maxPost = ini_get('post_max_size');
        adminRedirect($_SERVER['REQUEST_URI'], 'post_too_large');
    }
    // Bozuk baytlar JSON kodlamasını düşürdüğü için gelen metinler temizlenir
    $post = cleanPostText($_POST);

    $id = $post['id'] ?? null;
    $name = trim($post['name'] ?? '');
    $code = trim($post['code'] ?? '');
    $categoryId = (int)($post['category_id'] ?? 0);
    $subcategoryId = $post['subcategory_id'] ? (int)$post['subcategory_id'] : null;
    $features = trim($post['features'] ?? '');
    $palet = trim($post['palet'] ?? '');
    $featured = isset($post['featured']);
    $collection = trim($post['collection'] ?? '');
    $nameEn = trim($post['name_en'] ?? '');
    $featuresEn = trim($post['features_en'] ?? '');
    $slug = slugify($name);

    // Görsel yükleme
    $images = [];
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploaded = uploadImage($_FILES['image'], 'products');
        if ($uploaded) $images[] = $uploaded;
    }

    // Öne çıkan özellikler (satır başına bir madde)
    $highlights = linesToArray($post['highlights'] ?? '');

    // Etiketli bilgiler: Uyumlu Lavabo / Renk Seçenekleri gibi
    $attributes = [];
    foreach ($post['attr_label'] ?? [] as $i => $label) {
        $label = trim($label);
        $value = trim($post['attr_value'][$i] ?? '');
        if ($label !== '' && $value !== '') {
            $attributes[] = ['label' => $label, 'value' => $value];
        }
    }

    // Teknik tablo: sütun başlıkları + satırlar
    $specColumns = array_values(array_filter(array_map('trim', $post['spec_col'] ?? [])));
    $specRows = [];
    foreach ($post['spec_row'] ?? [] as $row) {
        $cells = array_map('trim', array_slice((array)$row, 0, max(count($specColumns), 1)));
        if (array_filter($cells, fn($c) => $c !== '' && $c !== '-')) {
            $specRows[] = array_values($cells);
        }
    }
    if (!$specColumns) $specRows = [];

    // Teknik dosyalar: mevcutlardan kalanlar + yeni yüklenen
    $documents = [];
    $docError = '';
    foreach ($post['doc_file'] ?? [] as $i => $docFile) {
        $docFile = trim($docFile);
        $docName = trim($post['doc_name'][$i] ?? '');
        if ($docFile !== '' && empty($post['doc_remove'][$i])) {
            $documents[] = ['name' => $docName ?: basename($docFile), 'file' => $docFile];
        }
    }
    if (!empty($_FILES['new_doc']['name'])) {
        $docErr = $_FILES['new_doc']['error'] ?? UPLOAD_ERR_NO_FILE;
        if ($docErr === UPLOAD_ERR_OK) {
            $uploadedDoc = uploadDocument($_FILES['new_doc'], 'urunler');
            if ($uploadedDoc) {
                $docLabel = trim($post['new_doc_name'] ?? '');
                $documents[] = [
                    'name' => $docLabel ?: pathinfo($_FILES['new_doc']['name'], PATHINFO_FILENAME),
                    'file' => $uploadedDoc,
                ];
            } else {
                $docError = 'doc_move_fail';
            }
        } elseif ($docErr === UPLOAD_ERR_INI_SIZE || $docErr === UPLOAD_ERR_FORM_SIZE) {
            $docError = 'doc_too_large';
        } else {
            $docError = 'doc_upload_fail';
        }
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
                $p['name_en'] = $nameEn;
                $p['features_en'] = $featuresEn;
                $p['palet'] = $palet;
                $p['featured'] = $featured;
                $p['highlights'] = $highlights;
                $p['attributes'] = $attributes;
                $p['spec_columns'] = $specColumns;
                $p['spec_rows'] = $specRows;
                $p['documents'] = $documents;
                if ($collection !== '') {
                    $p['collection'] = $collection;
                } else {
                    unset($p['collection']);
                }
                if (!empty($images)) $p['images'] = $images;
                break;
            }
        }
        unset($p);
        saveJson('products.json', $products);
        adminRedirect('/admin/products/edit/' . (int)$id, $docError ?: 'updated');
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
            'name_en' => $nameEn,
            'features_en' => $featuresEn,
            'palet' => $palet,
            'images' => $images,
            'documents' => $documents,
            'featured' => $featured,
            'highlights' => $highlights,
            'attributes' => $attributes,
            'spec_columns' => $specColumns,
            'spec_rows' => $specRows,
        ];
        if ($collection !== '') $newProduct['collection'] = $collection;
        $products[] = $newProduct;
        saveJson('products.json', $products);
        adminRedirect('/admin/products/edit/' . (int)$newProduct['id'], $docError ?: 'added');
    }
}

// Determine action
if (strpos($adminUri, '/products/add') !== false) {
    $action = 'form';
} elseif (preg_match('#/products/edit/(\d+)$#', $adminUri, $m)) {
    $action = 'form';
    $editProduct = getProductById((int)$m[1]);
}

$message = adminFlash([
    'added'           => 'Ürün eklendi.',
    'updated'         => 'Ürün güncellendi.',
    'deleted'         => 'Ürün silindi.',
    'post_too_large'  => '⚠️ Gönderilen veri çok büyük (maks: ' . ini_get('post_max_size') . '). Hiçbir değişiklik kaydedilmedi! Daha küçük dosya deneyin.',
    'doc_too_large'   => '⚠️ PDF dosyası çok büyük! Maksimum boyut: ' . ini_get('upload_max_filesize') . '. Ürün kaydedildi ama PDF yüklenemedi.',
    'doc_move_fail'   => '⚠️ PDF dosyası sunucuya yazılamadı (izin hatası). Ürün kaydedildi ama PDF yüklenemedi.',
    'doc_upload_fail' => '⚠️ PDF yükleme başarısız oldu. Ürün kaydedildi ama PDF yüklenemedi.',
]);

adminHeader('Ürünler');

if ($action === 'form'):
?>

<!-- ÜRÜN FORMU -->
<div class="admin-topbar">
  <h1><?= $editProduct ? 'Ürün Düzenle' : 'Yeni Ürün Ekle' ?></h1>
  <a href="/admin/products" class="btn-admin btn-admin-info">&larr; Geri</a>
</div>

<?php if ($message): ?>
<div class="alert <?= str_contains($message, '⚠️') ? 'alert-danger' : 'alert-success' ?>"><?= $message ?></div>
<?php endif; ?>

<div class="admin-card">
  <form method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>
    <?php if ($editProduct): ?>
    <input type="hidden" name="id" value="<?= $editProduct['id'] ?>">
    <?php endif; ?>

    <div class="form-row">
      <div class="form-group">
        <label>Ürün Adı *</label>
        <input type="text" name="name" class="form-control" value="<?= e($editProduct['name'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Product Name (EN)</label>
        <input type="text" name="name_en" class="form-control" value="<?= e($editProduct['name_en'] ?? '') ?>" placeholder="İngilizce ürün adı">
      </div>
    </div>

    <div class="form-row">
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

    <div class="form-row">
      <div class="form-group">
        <label>Ürün Özellikleri</label>
        <input type="text" name="features" class="form-control" value="<?= e($editProduct['features'] ?? '') ?>" placeholder="Ör: Dolap Uyumlu Lavabo">
      </div>
      <div class="form-group">
        <label>Features (EN)</label>
        <input type="text" name="features_en" class="form-control" value="<?= e($editProduct['features_en'] ?? '') ?>" placeholder="İngilizce özellik">
      </div>
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

    <div class="form-row">
      <div class="form-group">
        <label>Koleksiyon</label>
        <select name="collection" class="form-control">
          <option value="">Yok</option>
          <?php foreach (getCollections() as $col): ?>
          <option value="<?= e($col['slug']) ?>" <?= ($editProduct['collection'] ?? '') === $col['slug'] ? 'selected' : '' ?>>
            <?= e($col['title']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Ürün Görseli</label>
        <div class="file-input-wrapper" onclick="this.querySelector('input').click()">
          <input type="file" name="image" accept="image/*">
          <p>Görsel yüklemek için tıklayın</p>
          <small>JPG, PNG, WebP</small>
        </div>
      </div>
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

    <!-- ÖNE ÇIKAN ÖZELLİKLER -->
    <h3 class="form-section-title">Öne Çıkan Özellikler</h3>
    <div class="form-group">
      <label>Her satıra bir madde yazın</label>
      <textarea name="highlights" class="form-control" rows="4"
        placeholder="Gövde ve Çekmeceler MDF Lam&#10;Yavaş Kapanır Kapak"><?= e(implode("\n", $editProduct['highlights'] ?? [])) ?></textarea>
    </div>

    <!-- ETİKETLİ BİLGİLER -->
    <h3 class="form-section-title">Etiketli Bilgiler</h3>
    <p class="form-hint">Ör: "Renk Seçenekleri → Beyaz, Antrasit". Renk alanları ürün filtresinde de kullanılır.</p>
    <div id="attrRows">
      <?php
        $attrList = $editProduct['attributes'] ?? [];
        $attrList[] = ['label' => '', 'value' => ''];
        foreach ($attrList as $attr):
      ?>
      <div class="repeat-row">
        <input type="text" name="attr_label[]" class="form-control" placeholder="Etiket" value="<?= e($attr['label']) ?>">
        <input type="text" name="attr_value[]" class="form-control" placeholder="Değer" value="<?= e($attr['value']) ?>">
        <button type="button" class="btn-admin btn-admin-danger row-remove">Sil</button>
      </div>
      <?php endforeach; ?>
    </div>
    <button type="button" class="btn-admin btn-admin-info" id="addAttr">+ Satır Ekle</button>

    <!-- TEKNİK TABLO -->
    <h3 class="form-section-title">Teknik Bilgi Tablosu</h3>
    <p class="form-hint">Sütun başlıklarını boş bırakırsanız tablo ürün sayfasında gösterilmez.</p>
    <?php
      $cols = $editProduct['spec_columns'] ?? [];
      if (!$cols) $cols = ['KOD', 'ÜRÜN ÖZELLİKLERİ', 'PALET/AD.'];
      $colCount = count($cols);
    ?>
    <div class="spec-editor" id="specEditor" data-cols="<?= (int)$colCount ?>">
      <div class="repeat-row spec-head">
        <?php foreach ($cols as $col): ?>
        <input type="text" name="spec_col[]" class="form-control" placeholder="Sütun başlığı" value="<?= e($col) ?>">
        <?php endforeach; ?>
        <span class="spec-head-spacer"></span>
      </div>
      <div id="specRows">
        <?php
          $rows = $editProduct['spec_rows'] ?? [];
          $rows[] = array_fill(0, $colCount, '');
          foreach ($rows as $r => $row):
        ?>
        <div class="repeat-row">
          <?php for ($c = 0; $c < $colCount; $c++): ?>
          <input type="text" name="spec_row[<?= (int)$r ?>][]" class="form-control"
                 value="<?= e($row[$c] ?? '') ?>">
          <?php endfor; ?>
          <button type="button" class="btn-admin btn-admin-danger row-remove">Sil</button>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <button type="button" class="btn-admin btn-admin-info" id="addSpecRow">+ Satır Ekle</button>

    <!-- TEKNİK DOSYALAR -->
    <h3 class="form-section-title">Teknik Çizimler (PDF)</h3>
    <?php if ($editProduct && !empty($editProduct['documents'])): ?>
    <div class="doc-rows">
      <?php foreach ($editProduct['documents'] as $i => $doc): ?>
        <?php
          $docFile = is_array($doc) ? ($doc['file'] ?? '') : 'assets/documents/' . $doc;
          $docName = is_array($doc) ? ($doc['name'] ?? '') : $doc;
        ?>
      <div class="repeat-row">
        <input type="hidden" name="doc_file[<?= $i ?>]" value="<?= e($docFile) ?>">
        <input type="text" name="doc_name[<?= $i ?>]" class="form-control" value="<?= e($docName) ?>">
        <a href="/<?= e($docFile) ?>" target="_blank" class="btn-admin btn-admin-info">Aç</a>
        <label class="doc-remove">
          <input type="checkbox" name="doc_remove[<?= $i ?>]" value="1"> Kaldır
        </label>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="form-row">
      <div class="form-group">
        <label>Yeni PDF yükle <small style="color:#888;">(maks: <?= ini_get('upload_max_filesize') ?>)</small></label>
        <input type="file" name="new_doc" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
      </div>
      <div class="form-group">
        <label>Dosya adı (boşsa dosyanın adı kullanılır)</label>
        <input type="text" name="new_doc_name" class="form-control" placeholder="Ör: Anka 65-80 Ölçüler">
      </div>
    </div>

    <button type="submit" class="btn-admin btn-admin-success" style="margin-top:20px;">
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

// Satır sil (etiketli bilgiler + teknik tablo)
document.addEventListener('click', function(e) {
  if (!e.target.classList.contains('row-remove')) return;
  const row = e.target.closest('.repeat-row');
  const holder = row.parentElement;
  if (holder.querySelectorAll('.repeat-row').length > 1) {
    row.remove();
  } else {
    row.querySelectorAll('input[type="text"]').forEach(i => { i.value = ''; });
  }
});

// Etiketli bilgi satırı ekle
document.getElementById('addAttr').addEventListener('click', function() {
  const holder = document.getElementById('attrRows');
  const row = holder.lastElementChild.cloneNode(true);
  row.querySelectorAll('input').forEach(i => { i.value = ''; });
  holder.appendChild(row);
});

// Teknik tablo satırı ekle
document.getElementById('addSpecRow').addEventListener('click', function() {
  const holder = document.getElementById('specRows');
  const rows = holder.querySelectorAll('.repeat-row');
  const row = rows[rows.length - 1].cloneNode(true);
  const index = rows.length;
  row.querySelectorAll('input').forEach(i => {
    i.value = '';
    i.name = 'spec_row[' + index + '][]';
  });
  holder.appendChild(row);
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
          <a href="/admin/products/delete/<?= $p['id'] ?>" class="btn-admin btn-admin-danger btn-admin-sm"
             onclick="return confirm('<?= e(addslashes($p['name'])) ?> silinecek. Emin misiniz?\n\nBu işlem geri alınamaz.')">Sil</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php endif; ?>

<?php adminFooter(); ?>

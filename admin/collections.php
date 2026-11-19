<?php
require_once __DIR__ . '/layout.php';

$collections = loadJson('collections.json');
$products = getProducts();
$message = '';

// SİL
if (preg_match('#/collections/delete/([a-z0-9-]+)$#', $adminUri, $m)) {
    $delSlug = $m[1];
    unset($collections[$delSlug]);
    saveJson('collections.json', $collections);

    // Bu koleksiyona bağlı ürünlerin etiketini kaldır
    foreach ($products as &$p) {
        if (($p['collection'] ?? '') === $delSlug) unset($p['collection']);
    }
    unset($p);
    saveJson('products.json', $products);

    header('Location: /admin/collections?msg=deleted');
    exit;
}

// KAYDET
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = cleanPostText($_POST);
    $origSlug = trim($post['orig_slug'] ?? '');
    $title = trim($post['title'] ?? '');
    $slugInput = trim($post['slug'] ?? '');
    $slug = slugify($slugInput !== '' ? $slugInput : $title);

    if ($title !== '' && $slug !== '') {
        $entry = $collections[$origSlug] ?? $collections[$slug] ?? [];
        $titleEn = trim($post['title_en'] ?? '');
        $entry['title'] = $title;
        $entry['title_en'] = $titleEn;
        $entry['slug'] = $slug;

        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $up = uploadImage($_FILES['image'], 'products');
            if ($up) $entry['image'] = 'assets/images/products/' . $up;
        }

        // Slug değiştiyse eski kaydı kaldır, ürünleri yeni slug'a taşı
        if ($origSlug !== '' && $origSlug !== $slug) {
            unset($collections[$origSlug]);
            foreach ($products as &$p) {
                if (($p['collection'] ?? '') === $origSlug) $p['collection'] = $slug;
            }
            unset($p);
            saveJson('products.json', $products);
            $products = getProducts();
        }

        // Ürün sayısı ve kapak görselini güncel tut
        $inCollection = array_values(array_filter($products, fn($p) => ($p['collection'] ?? '') === $slug));
        $entry['count'] = count($inCollection);
        if (empty($entry['image'])) {
            foreach ($inCollection as $p) {
                if (!empty($p['images'][0])) {
                    $entry['image'] = 'assets/images/products/' . $p['images'][0];
                    break;
                }
            }
        }
        $entry['product_ids'] = $entry['product_ids'] ?? [];

        $collections[$slug] = $entry;
        saveJson('collections.json', $collections);
        adminRedirect('/admin/collections/edit/' . $slug, $origSlug === '' ? 'added' : 'updated');
    }
    $message = 'Başlık zorunlu.';
}

$message = $message ?: adminFlash([
    'added'   => 'Koleksiyon eklendi.',
    'updated' => 'Koleksiyon güncellendi.',
    'deleted' => 'Koleksiyon silindi. Ürünler silinmedi.',
]);

$editSlug = null;
if (preg_match('#/collections/edit/([a-z0-9-]+)$#', $adminUri, $m)) {
    $editSlug = $m[1];
}
$isForm = $editSlug !== null || strpos($adminUri, '/collections/add') !== false;
$edit = $editSlug !== null ? ($collections[$editSlug] ?? null) : null;

// Ürün sayılarını canlı hesapla
$counts = [];
foreach ($products as $p) {
    if (!empty($p['collection'])) {
        $counts[$p['collection']] = ($counts[$p['collection']] ?? 0) + 1;
    }
}

adminHeader('Koleksiyonlar');

if ($isForm):
?>

<div class="admin-topbar">
  <h1><?= $edit ? 'Koleksiyon Düzenle' : 'Yeni Koleksiyon' ?></h1>
  <a href="/admin/collections" class="btn-admin btn-admin-info">&larr; Geri</a>
</div>

<?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>

<div class="admin-card">
  <form method="POST" enctype="multipart/form-data">
    <?= csrfField() ?>
    <input type="hidden" name="orig_slug" value="<?= e($editSlug ?? '') ?>">
    <div class="form-row">
      <div class="form-group">
        <label>Koleksiyon Adı *</label>
        <input type="text" name="title" class="form-control" value="<?= e($edit['title'] ?? '') ?>" required>
      </div>
      <div class="form-group">
        <label>Collection Name (EN)</label>
        <input type="text" name="title_en" class="form-control" value="<?= e($edit['title_en'] ?? '') ?>" placeholder="İngilizce koleksiyon adı">
      </div>
    </div>
    <div class="form-group">
      <label>Adres (slug)</label>
      <input type="text" name="slug" class="form-control" value="<?= e($edit['slug'] ?? '') ?>" placeholder="otomatik oluşur">
    </div>

    <div class="form-group">
      <label>Kapak Görseli</label>
      <div class="file-input-wrapper" onclick="this.querySelector('input').click()">
        <input type="file" name="image" accept="image/*">
        <p>Görsel yüklemek için tıklayın</p>
        <small>Boş bırakırsanız serideki ilk ürünün görseli kullanılır</small>
      </div>
      <?php if (!empty($edit['image'])): ?>
      <div class="image-preview">
        <div class="image-preview-item"><img src="/<?= e($edit['image']) ?>" alt=""></div>
      </div>
      <?php endif; ?>
    </div>

    <p class="form-hint">Ürünleri koleksiyona eklemek için ürün düzenleme ekranındaki
       <strong>Koleksiyon</strong> alanını kullanın.</p>

    <button type="submit" class="btn-admin btn-admin-success">Kaydet</button>
  </form>
</div>

<?php else: ?>

<div class="admin-topbar">
  <h1>Koleksiyonlar (<?= count($collections) ?>)</h1>
  <a href="/admin/collections/add" class="btn-admin btn-admin-primary">+ Yeni Koleksiyon</a>
</div>

<?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>

<div class="admin-card">
  <table class="admin-table">
    <thead>
      <tr><th>Görsel</th><th>Koleksiyon</th><th>Adres</th><th>Ürün</th><th>İşlem</th></tr>
    </thead>
    <tbody>
      <?php foreach ($collections as $slug => $col): ?>
      <tr>
        <td>
          <?php if (!empty($col['image'])): ?>
          <img src="/<?= e($col['image']) ?>" alt="" style="width:46px;height:46px;object-fit:cover;border-radius:6px;">
          <?php endif; ?>
        </td>
        <td><strong><?= e($col['title']) ?></strong></td>
        <td><code>/koleksiyon/<?= e($slug) ?></code></td>
        <td><?= (int)($counts[$slug] ?? 0) ?></td>
        <td>
          <a href="/admin/collections/edit/<?= e($slug) ?>" class="btn-admin btn-admin-info">Düzenle</a>
          <a href="/admin/collections/delete/<?= e($slug) ?>" class="btn-admin btn-admin-danger"
             onclick="return confirm('Koleksiyon silinsin mi? Ürünler silinmez, sadece etiket kaldırılır.')">Sil</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php endif; ?>

<?php adminFooter(); ?>

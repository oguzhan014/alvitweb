<?php
require_once __DIR__ . '/layout.php';
$products = getProducts();
$categories = getCategories();
$pages = getPages();
$messages = loadJson('messages.json');
$unreadMessages = array_filter($messages, fn($m) => empty($m['read']));

adminHeader('Dashboard');
?>

<div class="admin-topbar">
  <h1>Dashboard</h1>
  <div class="admin-topbar-actions">
    <a href="/admin/products/add" class="btn-admin btn-admin-primary">+ Yeni Ürün</a>
  </div>
</div>

<!-- STATS -->
<div class="admin-stats">
  <div class="stat-card">
    <div class="stat-icon blue">&#9881;</div>
    <div class="stat-info">
      <h3><?= count($products) ?></h3>
      <p>Toplam Ürün</p>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green">&#9783;</div>
    <div class="stat-info">
      <h3><?= count($categories) ?></h3>
      <p>Kategori</p>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon orange">&#9998;</div>
    <div class="stat-info">
      <h3><?= count($pages) ?></h3>
      <p>Sayfa</p>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon purple">&#9993;</div>
    <div class="stat-info">
      <h3><?= count($unreadMessages) ?></h3>
      <p>Okunmamış Mesaj</p>
    </div>
  </div>
</div>

<!-- SON ÜRÜNLER -->
<div class="admin-card">
  <h2 style="font-size:18px; margin-bottom:16px;">Son Eklenen Ürünler</h2>
  <table class="admin-table">
    <thead>
      <tr>
        <th>Görsel</th>
        <th>Ürün Adı</th>
        <th>Kod</th>
        <th>Kategori</th>
        <th>İşlem</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach (array_slice(array_reverse($products), 0, 10) as $p): ?>
      <tr>
        <td><img src="/<?= getProductImage($p) ?>" alt=""></td>
        <td><strong><?= e($p['name']) ?></strong></td>
        <td><?= e($p['code'] ?: '-') ?></td>
        <td><?php $cat = getCategoryById($p['category_id']); echo $cat ? e($cat['name']) : '-'; ?></td>
        <td>
          <a href="/admin/products/edit/<?= $p['id'] ?>" class="btn-admin btn-admin-info btn-admin-sm">Düzenle</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php adminFooter(); ?>

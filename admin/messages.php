<?php
require_once __DIR__ . '/layout.php';
$messages = loadJson('messages.json');

// Mesajı sil
if (isset($_POST['delete'])) {
    $delId = (int)$_POST['delete'];
    $messages = array_values(array_filter($messages, fn($m) => $m['id'] != $delId));
    saveJson('messages.json', $messages);
    adminRedirect('/admin/messages', 'Mesaj silindi.');
}

// Mesajı okundu olarak işaretle
if (isset($_GET['read'])) {
    $readId = (int)$_GET['read'];
    foreach ($messages as &$m) {
        if ($m['id'] == $readId) { $m['read'] = true; break; }
    }
    unset($m);
    saveJson('messages.json', $messages);
}

// Detay görünümü
$detail = null;
if (isset($_GET['id'])) {
    $viewId = (int)$_GET['id'];
    foreach ($messages as &$m) {
        if ($m['id'] == $viewId) {
            $m['read'] = true;
            $detail = $m;
            break;
        }
    }
    unset($m);
    if ($detail) saveJson('messages.json', $messages);
}

adminHeader('Mesajlar');
$flash = adminFlash();
?>

<div class="admin-topbar">
  <h1>Mesajlar (<?= count($messages) ?>)</h1>
</div>

<?php if ($flash): ?>
<div class="admin-alert admin-alert-success"><?= e($flash) ?></div>
<?php endif; ?>

<?php if ($detail): ?>
<div class="admin-card" style="margin-bottom:24px;">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <a href="/admin/messages" class="btn-admin btn-admin-sm">&larr; Tüm Mesajlar</a>
    <form method="POST" onsubmit="return confirm('Bu mesajı silmek istediğinize emin misiniz?')">
      <?= csrfField() ?>
      <input type="hidden" name="delete" value="<?= $detail['id'] ?>">
      <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm">Sil</button>
    </form>
  </div>
  <table class="admin-table" style="margin-bottom:0;">
    <tr>
      <th style="width:120px;">Tarih</th>
      <td><?= e($detail['date']) ?></td>
    </tr>
    <tr>
      <th>İsim</th>
      <td><strong><?= e($detail['name']) ?></strong></td>
    </tr>
    <tr>
      <th>E-posta</th>
      <td><a href="mailto:<?= e($detail['email']) ?>"><?= e($detail['email']) ?></a></td>
    </tr>
    <tr>
      <th>Konu</th>
      <td><?= e($detail['subject'] ?: '-') ?></td>
    </tr>
    <tr>
      <th>Mesaj</th>
      <td style="white-space:pre-wrap; line-height:1.7;"><?= e($detail['message']) ?></td>
    </tr>
  </table>
</div>

<?php else: ?>

<div class="admin-card">
  <?php if (empty($messages)): ?>
    <p style="text-align:center; padding:40px; color:var(--admin-text-light);">Henüz mesaj yok.</p>
  <?php else: ?>
  <table class="admin-table">
    <thead>
      <tr><th>Tarih</th><th>İsim</th><th>E-posta</th><th>Konu</th><th>Mesaj</th><th></th></tr>
    </thead>
    <tbody>
      <?php foreach (array_reverse($messages) as $m): ?>
      <tr style="<?= empty($m['read']) ? 'background:#fffde7;' : '' ?> cursor:pointer;" onclick="window.location='/admin/messages?id=<?= $m['id'] ?>'">
        <td style="font-size:12px; white-space:nowrap;"><?= e($m['date']) ?></td>
        <td><strong><?= e($m['name']) ?></strong></td>
        <td><?= e($m['email']) ?></td>
        <td><?= e($m['subject'] ?: '-') ?></td>
        <td style="max-width:300px;"><?= e(mb_substr($m['message'], 0, 80)) ?><?= mb_strlen($m['message']) > 80 ? '...' : '' ?></td>
        <td>
          <?php if (empty($m['read'])): ?>
            <span style="display:inline-block; width:10px; height:10px; background:var(--admin-accent); border-radius:50;" title="Okunmadı"></span>
          <?php else: ?>
            <span style="color:green;">&#10003;</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php endif; ?>

<?php adminFooter(); ?>

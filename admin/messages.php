<?php
require_once __DIR__ . '/layout.php';
$messages = loadJson('messages.json');

// Mesajı okundu olarak işaretle
if (isset($_GET['read'])) {
    $readId = (int)$_GET['read'];
    foreach ($messages as &$m) {
        if ($m['id'] == $readId) { $m['read'] = true; break; }
    }
    unset($m);
    saveJson('messages.json', $messages);
}

adminHeader('Mesajlar');
?>

<div class="admin-topbar">
  <h1>Mesajlar (<?= count($messages) ?>)</h1>
</div>

<div class="admin-card">
  <?php if (empty($messages)): ?>
    <p style="text-align:center; padding:40px; color:var(--admin-text-light);">Henüz mesaj yok.</p>
  <?php else: ?>
  <table class="admin-table">
    <thead>
      <tr><th>Tarih</th><th>İsim</th><th>E-posta</th><th>Konu</th><th>Mesaj</th><th>Durum</th></tr>
    </thead>
    <tbody>
      <?php foreach (array_reverse($messages) as $m): ?>
      <tr style="<?= empty($m['read']) ? 'background:#fffde7;' : '' ?>">
        <td style="font-size:12px; white-space:nowrap;"><?= e($m['date']) ?></td>
        <td><strong><?= e($m['name']) ?></strong></td>
        <td><?= e($m['email']) ?></td>
        <td><?= e($m['subject'] ?: '-') ?></td>
        <td style="max-width:300px;"><?= e(mb_substr($m['message'], 0, 100)) ?>...</td>
        <td>
          <?php if (empty($m['read'])): ?>
            <a href="/admin/messages?read=<?= $m['id'] ?>" class="btn-admin btn-admin-success btn-admin-sm">Okundu</a>
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

<?php adminFooter(); ?>

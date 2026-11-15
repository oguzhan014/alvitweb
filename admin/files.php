<?php
require_once __DIR__ . '/layout.php';

$kurumsal = loadJson('kurumsal.json');
$message = '';

// Yönetilebilir sayfalar: dosya listeleri ve video listesi
$managed = [];
foreach ($kurumsal as $slug => $entry) {
    if (in_array($entry['type'] ?? '', ['documents', 'videos'], true)
        || !empty($entry['documents'])) {
        $managed[$slug] = $entry;
    }
}

$activeSlug = $_GET['sayfa'] ?? array_key_first($managed);
$active = $managed[$activeSlug] ?? null;

// DOSYA SİL
if (preg_match('#/files/delete/([a-z0-9-]+)/(\d+)$#', $adminUri, $m)) {
    $slug = $m[1];
    $idx = (int)$m[2];
    if (isset($kurumsal[$slug]['documents'][$idx])) {
        array_splice($kurumsal[$slug]['documents'], $idx, 1);
        saveJson('kurumsal.json', $kurumsal);
    }
    header('Location: /admin/files?sayfa=' . urlencode($slug) . '&msg=deleted');
    exit;
}

// VİDEO SİL
if (preg_match('#/files/video-delete/([a-z0-9-]+)/(\d+)$#', $adminUri, $m)) {
    $slug = $m[1];
    $idx = (int)$m[2];
    if (isset($kurumsal[$slug]['videos'][$idx])) {
        array_splice($kurumsal[$slug]['videos'], $idx, 1);
        saveJson('kurumsal.json', $kurumsal);
    }
    header('Location: /admin/files?sayfa=' . urlencode($slug) . '&msg=deleted');
    exit;
}

// KAYDET
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = cleanPostText($_POST);
    $slug = $post['slug'] ?? '';

    if (isset($kurumsal[$slug])) {
        // Mevcut dosya adlarını güncelle
        foreach ($post['doc_name'] ?? [] as $i => $docName) {
            if (isset($kurumsal[$slug]['documents'][$i])) {
                $kurumsal[$slug]['documents'][$i]['name'] = trim($docName);
            }
        }
        // Mevcut video başlıklarını güncelle
        foreach ($post['video_title'] ?? [] as $i => $vTitle) {
            if (isset($kurumsal[$slug]['videos'][$i])) {
                $kurumsal[$slug]['videos'][$i]['title'] = trim($vTitle);
            }
        }

        // Yeni dosya
        if (!empty($_FILES['new_doc']['name']) && $_FILES['new_doc']['error'] === UPLOAD_ERR_OK) {
            $folder = $slug === 'raporlar' ? 'raporlar'
                    : ($slug === 'montaj-kilavuzlari' ? 'kilavuzlar' : 'kurumsal');
            $up = uploadDocument($_FILES['new_doc'], $folder);
            if ($up) {
                $label = trim($post['new_doc_name'] ?? '');
                $kurumsal[$slug]['documents'][] = [
                    'name' => $label ?: pathinfo($_FILES['new_doc']['name'], PATHINFO_FILENAME),
                    'url' => '',
                    'file' => $up,
                ];
                $redirectCode = 'uploaded';
            } else {
                $redirectCode = 'badfile';
            }
        }

        // Yeni video
        $newVideo = trim($post['new_video'] ?? '');
        if ($newVideo !== '') {
            if (preg_match('#(?:youtube\.com/(?:watch\?v=|embed/)|youtu\.be/)([A-Za-z0-9_-]{6,})#', $newVideo, $vm)) {
                $newVideo = $vm[1];
            }
            if (preg_match('/^[A-Za-z0-9_-]{6,}$/', $newVideo)) {
                $kurumsal[$slug]['videos'][] = [
                    'id' => $newVideo,
                    'title' => trim($post['new_video_title'] ?? '') ?: 'Montaj Videosu',
                ];
                $redirectCode = 'video';
            } else {
                $redirectCode = 'badvideo';
            }
        }

        saveJson('kurumsal.json', $kurumsal);
        adminRedirect('/admin/files?sayfa=' . urlencode($slug), $redirectCode ?? 'saved');
    }
}

$message = adminFlash([
    'saved'    => 'Değişiklikler kaydedildi.',
    'uploaded' => 'Dosya yüklendi.',
    'video'    => 'Video eklendi.',
    'deleted'  => 'Kayıt listeden kaldırıldı.',
    'badfile'  => 'Dosya türü kabul edilmedi (PDF, DOC, XLS).',
    'badvideo' => 'Geçersiz video bağlantısı.',
]);

adminHeader('Kurumsal Dosyalar');
?>

<div class="admin-topbar">
  <h1>Kurumsal Dosyalar</h1>
  <a href="/sayfa/<?= e($activeSlug) ?>" target="_blank" class="btn-admin btn-admin-info">Sayfayı Gör</a>
</div>

<?php if ($message): ?><div class="alert alert-success"><?= e($message) ?></div><?php endif; ?>

<div class="admin-card">
  <div class="tab-row">
    <?php foreach ($managed as $slug => $entry): ?>
    <a href="/admin/files?sayfa=<?= e($slug) ?>"
       class="btn-admin <?= $slug === $activeSlug ? 'btn-admin-primary' : 'btn-admin-info' ?>">
      <?= e($entry['title']) ?>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php if ($active): ?>
<div class="admin-card">
  <form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="slug" value="<?= e($activeSlug) ?>">

    <?php if (!empty($active['documents'])): ?>
    <h3 class="form-section-title" style="border-top:none; padding-top:0;">
      Dosyalar (<?= count($active['documents']) ?>)
    </h3>
    <?php foreach ($active['documents'] as $i => $doc): ?>
    <div class="repeat-row">
      <input type="text" name="doc_name[<?= $i ?>]" class="form-control" value="<?= e($doc['name']) ?>">
      <a href="/<?= e($doc['file']) ?>" target="_blank" class="btn-admin btn-admin-info">Aç</a>
      <a href="/admin/files/delete/<?= e($activeSlug) ?>/<?= $i ?>" class="btn-admin btn-admin-danger"
         onclick="return confirm('Dosya listeden kaldırılsın mı?')">Sil</a>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <?php if (($active['type'] ?? '') === 'documents' || !empty($active['documents'])): ?>
    <h3 class="form-section-title">Yeni Dosya Yükle</h3>
    <div class="form-row">
      <div class="form-group">
        <label>Dosya (PDF, DOC, XLS)</label>
        <input type="file" name="new_doc" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx">
      </div>
      <div class="form-group">
        <label>Görünecek ad</label>
        <input type="text" name="new_doc_name" class="form-control" placeholder="Ör: 2023 Faaliyet Raporu">
      </div>
    </div>
    <?php endif; ?>

    <?php if (($active['type'] ?? '') === 'videos'): ?>
    <h3 class="form-section-title" style="<?= empty($active['documents']) ? 'border-top:none; padding-top:0;' : '' ?>">
      Videolar (<?= count($active['videos'] ?? []) ?>)
    </h3>
    <?php foreach ($active['videos'] ?? [] as $i => $video): ?>
    <div class="repeat-row">
      <input type="text" name="video_title[<?= $i ?>]" class="form-control" value="<?= e($video['title']) ?>">
      <code style="flex:0 0 auto; padding:0 8px;"><?= e($video['id']) ?></code>
      <a href="https://www.youtube.com/watch?v=<?= e($video['id']) ?>" target="_blank" class="btn-admin btn-admin-info">Aç</a>
      <a href="/admin/files/video-delete/<?= e($activeSlug) ?>/<?= $i ?>" class="btn-admin btn-admin-danger"
         onclick="return confirm('Video kaldırılsın mı?')">Sil</a>
    </div>
    <?php endforeach; ?>

    <h3 class="form-section-title">Yeni Video Ekle</h3>
    <div class="form-row">
      <div class="form-group">
        <label>YouTube bağlantısı veya video kimliği</label>
        <input type="text" name="new_video" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
      </div>
      <div class="form-group">
        <label>Başlık</label>
        <input type="text" name="new_video_title" class="form-control" placeholder="Ör: Asma Klozet Montajı">
      </div>
    </div>
    <?php endif; ?>

    <button type="submit" class="btn-admin btn-admin-success" style="margin-top:16px;">Kaydet</button>
  </form>
</div>
<?php else: ?>
<div class="admin-card"><p>Yönetilecek kurumsal dosya sayfası bulunamadı.</p></div>
<?php endif; ?>

<?php adminFooter(); ?>

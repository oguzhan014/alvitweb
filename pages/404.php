<?php
$pageTitle = t('error_404_title');
include __DIR__ . '/../includes/header.php';
?>

<div class="error-page">
  <div>
    <h1>404</h1>
    <p><?= t('error_404_text') ?></p>
    <a href="<?= langUrl() ?>" class="btn btn-dark"><?= t('error_404_btn') ?></a>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

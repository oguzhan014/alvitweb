<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Giriş | Alvit</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body">

<div class="admin-login">
  <div class="admin-login-brand">
    <img src="/assets/images/about/fabrika-uretim.jpg" alt="" class="admin-login-brand-bg">
    <div class="admin-login-brand-overlay"></div>
    <div class="admin-login-brand-content">
      <img src="/assets/images/brand-logo-footer.png" alt="Alvit" class="admin-login-logo">
      <h2>Ürünlerinizi ve içeriğinizi tek yerden yönetin</h2>
      <p>Kategoriler, ürünler, slider ve kurumsal içerikleri buradan güncelleyebilirsiniz.</p>
      <ul class="admin-login-facts">
        <li><strong>1994</strong><span>Kuruluş Yılı</span></li>
        <li><strong>Bartın</strong><span>Türk Üretimi</span></li>
        <li><strong>TSE&nbsp;·&nbsp;CE&nbsp;·&nbsp;ISO</strong><span>Belgeli Kalite</span></li>
      </ul>
    </div>
  </div>
  <div class="admin-login-panel">
    <div class="login-card">
      <h1>al<span>vi</span>t</h1>
      <p>Yönetim Paneli</p>

      <?php if (!empty($loginError)): ?>
      <div class="alert alert-danger"><?= e($loginError) ?></div>
      <?php endif; ?>

      <form method="POST">
        <input type="text" name="username" class="form-control" placeholder="Kullanıcı Adı" required>
        <input type="password" name="password" class="form-control" placeholder="Şifre" required>
        <button type="submit" class="btn-admin btn-admin-primary">Giriş Yap</button>
      </form>
    </div>
  </div>
</div>

</body>
</html>

<?php
// ============================================================
// ADMIN PANEL - Router
// ============================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../includes/helpers.php';

$adminUri = str_replace('/admin', '', $uri);
$adminUri = rtrim($adminUri, '/');
if (empty($adminUri)) $adminUri = '/';

// Auth check
$isLoggedIn = !empty($_SESSION['admin_logged_in']);

// Login/Logout
if ($adminUri === '/login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!loginRateLimit()) {
            $loginError = 'Çok fazla deneme. 15 dakika sonra tekrar deneyin.';
        } elseif (!csrfCheck()) {
            $loginError = 'Geçersiz istek. Sayfayı yenileyip tekrar deneyin.';
        } else {
            $settings = getSettings();
            $user = $_POST['username'] ?? '';
            $pass = $_POST['password'] ?? '';
            if ($user === $settings['admin_user']
                && password_verify($pass, $settings['admin_pass'] ?? '')) {
                session_regenerate_id(true);
                $_SESSION['admin_logged_in'] = true;
                header('Location: /admin');
                exit;
            }
            loginAttemptRecord();
            $loginError = 'Hatalı kullanıcı adı veya şifre';
        }
    }
    include __DIR__ . '/login.php';
    return;
}

if ($adminUri === '/logout') {
    session_destroy();
    header('Location: /admin/login');
    exit;
}

if (!$isLoggedIn) {
    header('Location: /admin/login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrfCheck()) {
    http_response_code(403);
    die('Geçersiz istek (CSRF). Sayfayı yenileyip tekrar deneyin.');
}

// Admin routes
switch (true) {
    case $adminUri === '/':
        include __DIR__ . '/dashboard.php';
        break;
    case $adminUri === '/products':
    case $adminUri === '/products/add':
    case preg_match('#^/products/edit/(\d+)$#', $adminUri, $m) === 1:
    case preg_match('#^/products/delete/(\d+)$#', $adminUri, $m) === 1:
        include __DIR__ . '/products.php';
        break;
    case $adminUri === '/categories':
    case $adminUri === '/categories/add':
    case preg_match('#^/categories/edit/(\d+)$#', $adminUri, $m) === 1:
        include __DIR__ . '/categories.php';
        break;
    case $adminUri === '/pages':
    case $adminUri === '/pages/add':
    case preg_match('#^/pages/edit/(\d+)$#', $adminUri, $m) === 1:
        include __DIR__ . '/cms-pages.php';
        break;
    case $adminUri === '/sliders':
    case $adminUri === '/sliders/add':
    case preg_match('#^/sliders/edit/(\d+)$#', $adminUri, $m) === 1:
        include __DIR__ . '/sliders.php';
        break;
    case $adminUri === '/collections':
    case $adminUri === '/collections/add':
    case preg_match('#^/collections/(edit|delete)/([a-z0-9-]+)$#', $adminUri) === 1:
        include __DIR__ . '/collections.php';
        break;
    case $adminUri === '/certificates':
    case $adminUri === '/certificates/add':
    case preg_match('#^/certificates/(edit|delete)/(\d+)$#', $adminUri) === 1:
        include __DIR__ . '/certificates.php';
        break;
    case $adminUri === '/files':
    case preg_match('#^/files/(delete|video-delete)/([a-z0-9-]+)/(\d+)$#', $adminUri) === 1:
        include __DIR__ . '/files.php';
        break;
    case $adminUri === '/messages':
        include __DIR__ . '/messages.php';
        break;
    case $adminUri === '/settings':
        include __DIR__ . '/settings.php';
        break;
    default:
        include __DIR__ . '/dashboard.php';
        break;
}

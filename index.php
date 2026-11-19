<?php
// ============================================================
// ALVIT - Ana Router
// ============================================================
session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
]);
session_start();
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/seo.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = rtrim($uri, '/');
if (empty($uri)) $uri = '/';

// Dil algılama: /en/... prefix'i varsa İngilizce
if (preg_match('#^/en(/.*)?$#', $uri, $langMatch)) {
    setLang('en');
    $uri = $langMatch[1] ?? '/';
    if ($uri === '') $uri = '/';
} else {
    setLang('tr');
}

$settings = getSettings();

// ---- ROUTING ----
switch (true) {
    // Anasayfa
    case $uri === '/' || $uri === '/index.php':
        include __DIR__ . '/pages/home.php';
        break;

    // Tüm ürünler (filtreli katalog)
    case $uri === '/urunler':
        include __DIR__ . '/pages/products.php';
        break;

    // Koleksiyonlar
    case $uri === '/koleksiyonlar':
        include __DIR__ . '/pages/collections.php';
        break;

    case preg_match('#^/koleksiyon/([a-z0-9-]+)$#', $uri, $m) === 1:
        $collectionSlug = $m[1];
        include __DIR__ . '/pages/collection.php';
        break;


    // Kategoriler listesi
    case $uri === '/kategoriler':
        include __DIR__ . '/pages/categories.php';
        break;

    // Tek kategori
    case preg_match('#^/kategori/([a-z0-9-]+)$#', $uri, $m) === 1:
        $categorySlug = $m[1];
        include __DIR__ . '/pages/category.php';
        break;

    // Ürün detay
    case preg_match('#^/urun/([a-z0-9-]+)$#', $uri, $m) === 1:
        $productSlug = $m[1];
        include __DIR__ . '/pages/product.php';
        break;

    // Kurumsal sayfalar
    case preg_match('#^/sayfa/([a-z0-9-]+)$#', $uri, $m) === 1:
        $pageSlug = $m[1];
        include __DIR__ . '/pages/page.php';
        break;

    // Galeri
    case $uri === '/galeri':
        include __DIR__ . '/pages/gallery.php';
        break;

    // İletişim
    case $uri === '/iletisim':
        include __DIR__ . '/pages/contact.php';
        break;

    // Arama sonuçları sayfası
    case $uri === '/ara':
        include __DIR__ . '/pages/search.php';
        break;

    // Sitemap
    case $uri === '/sitemap.xml':
        header('Content-Type: application/xml; charset=utf-8');
        echo generateSitemap();
        break;

    // Canlı arama API (JSON)
    case $uri === '/api/arama':
        header('Content-Type: application/json; charset=utf-8');
        $q = $_GET['q'] ?? '';
        $matches = searchProducts($q, 6);
        $out = array_map(function($p) {
            return [
                'name' => td($p, 'name'),
                'code' => $p['code'] ?? '',
                'url' => langUrl('/urun/' . $p['slug']),
                'image' => '/' . getProductImage($p),
            ];
        }, array_values($matches));
        echo json_encode($out, JSON_UNESCAPED_UNICODE);
        break;

    // Admin panel
    case strpos($uri, '/admin') === 0:
        include __DIR__ . '/admin/index.php';
        break;

    // 404
    default:
        http_response_code(404);
        include __DIR__ . '/pages/404.php';
        break;
}

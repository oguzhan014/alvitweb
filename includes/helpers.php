<?php
// ============================================================
// HELPERS - Veri okuma/yazma ve yardımcı fonksiyonlar
// ============================================================

define('DATA_DIR', __DIR__ . '/../data/');
define('UPLOAD_DIR', __DIR__ . '/../assets/images/');

// ---- DİL SİSTEMİ ----
$GLOBALS['_lang'] = 'tr';
$GLOBALS['_translations'] = [];

function setLang($lang) {
    $lang = in_array($lang, ['tr', 'en']) ? $lang : 'tr';
    $GLOBALS['_lang'] = $lang;
    $file = DATA_DIR . 'lang/' . $lang . '.json';
    $GLOBALS['_translations'] = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}

function lang() {
    return $GLOBALS['_lang'];
}

function t($key, $fallback = '') {
    return $GLOBALS['_translations'][$key] ?? $fallback ?: $key;
}

function td($item, $field) {
    if (lang() === 'en') {
        $enKey = $field . '_en';
        if (!empty($item[$enKey])) return $item[$enKey];
    }
    return $item[$field] ?? '';
}

function langUrl($path = '') {
    if (lang() === 'en') return '/en' . $path;
    return $path ?: '/';
}

function switchLangUrl() {
    $uri = $_SERVER['REQUEST_URI'];
    if (lang() === 'tr') {
        return '/en' . $uri;
    }
    return preg_replace('#^/en#', '', $uri) ?: '/';
}

function loadJson($file) {
    $path = DATA_DIR . $file;
    if (!file_exists($path)) return [];
    $content = file_get_contents($path);
    return json_decode($content, true) ?: [];
}

/**
 * JSON veri dosyasını güvenli şekilde yazar.
 *
 * json_encode bozuk UTF-8'de false döndüğü için önce kodlama denenir;
 * başarısızsa dosyaya dokunulmaz. Yazma da geçici dosya üzerinden
 * yapılır, böylece yarım kalan bir yazma mevcut veriyi bozmaz.
 */
function saveJson($file, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        error_log('saveJson: ' . $file . ' kodlanamadi - ' . json_last_error_msg());
        return false;
    }

    $path = DATA_DIR . $file;
    $tmp = $path . '.tmp';
    if (file_put_contents($tmp, $json) === false) {
        error_log('saveJson: ' . $file . ' yazilamadi');
        return false;
    }
    if (!rename($tmp, $path)) {
        @unlink($tmp);
        error_log('saveJson: ' . $file . ' degistirilemedi');
        return false;
    }
    return true;
}

/**
 * Kaydetme sonrası yönlendirme (POST-Redirect-GET).
 * Sayfa yenilendiğinde formun tekrar gönderilmesini önler.
 */
function adminRedirect($path, $code = 'saved') {
    $sep = strpos($path, '?') !== false ? '&' : '?';
    header('Location: ' . $path . $sep . 'msg=' . urlencode($code));
    exit;
}

/**
 * Adres çubuğundaki msg koduna karşılık gelen bildirim metnini döndürür.
 * Sayfaya özel metinler $map ile verilebilir.
 */
function adminFlash($map = []) {
    $default = [
        'saved'    => 'Değişiklikler kaydedildi.',
        'added'    => 'Kayıt eklendi.',
        'updated'  => 'Kayıt güncellendi.',
        'deleted'  => 'Kayıt silindi.',
        'uploaded' => 'Dosya yüklendi.',
    ];
    $code = $_GET['msg'] ?? '';
    if ($code === '') return '';
    return $map[$code] ?? $default[$code] ?? '';
}

/** Formdan gelen metni geçerli UTF-8'e indirger (bozuk baytları atar). */
function cleanUtf8($text) {
    $text = (string)$text;
    if (mb_check_encoding($text, 'UTF-8')) return $text;
    return mb_convert_encoding($text, 'UTF-8', 'UTF-8');
}

/** $_POST dizisini (iç içe dahil) UTF-8 açısından temizler. */
function cleanPostText($input) {
    if (is_array($input)) {
        $out = [];
        foreach ($input as $key => $value) {
            $out[$key] = cleanPostText($value);
        }
        return $out;
    }
    return is_string($input) ? cleanUtf8($input) : $input;
}

function getSettings() {
    return loadJson('settings.json');
}

function getCategories() {
    return loadJson('categories.json');
}

function getProducts() {
    return loadJson('products.json');
}

function getPages() {
    return loadJson('pages.json');
}

function getSliders() {
    return loadJson('sliders.json');
}

function getCertificates() {
    return loadJson('certificates.json');
}

/**
 * İsimle çağrılan satır içi SVG ikon seti.
 * Emoji yerine her yerde bunu kullanıyoruz; tek yerden yönetiliyor.
 */
function icon($name, $size = 24) {
    $paths = [
        'eye'       => '<circle cx="12" cy="12" r="3"/><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/>',
        'target'    => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.5"/>',
        'award'     => '<circle cx="12" cy="8" r="6"/><polyline points="8.2 13.4 7 22 12 19 17 22 15.8 13.4"/>',
        'bulb'      => '<path d="M9 18h6"/><path d="M10 22h4"/><path d="M12 2a7 7 0 0 0-4 12.7V17h8v-2.3A7 7 0 0 0 12 2z"/>',
        'handshake' => '<path d="M11 17l2 2a1.5 1.5 0 0 0 2.1-2.1"/><path d="M13.1 16.9l2.4 2.4a1.5 1.5 0 0 0 2.1-2.1l-5-5"/><path d="M2 10l4-4 4 2 4-2 4 4"/><path d="M6 6v8l5 4"/><path d="M18 6v8"/>',
        'gear'      => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.6 1.6 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.6 1.6 0 0 0-1.8-.3 1.6 1.6 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1A1.6 1.6 0 0 0 9 19.4a1.6 1.6 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.6 1.6 0 0 0 .3-1.8 1.6 1.6 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1A1.6 1.6 0 0 0 4.6 9a1.6 1.6 0 0 0-.3-1.8l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.6 1.6 0 0 0 1.8.3H9a1.6 1.6 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.6 1.6 0 0 0 1 1.5 1.6 1.6 0 0 0 1.8-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.6 1.6 0 0 0-.3 1.8V9a1.6 1.6 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.6 1.6 0 0 0-1.5 1z"/>',
        'shield'    => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>',
        'leaf'      => '<path d="M11 20A7 7 0 0 1 4 13c0-6 7-11 16-11 0 9-5 16-11 16z"/><path d="M4 21c4-6 8-8 12-9"/>',
        'layers'    => '<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>',
        'document'  => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/>',
        'factory'   => '<path d="M2 20h20"/><path d="M4 20V9l5 3V9l5 3V9l5 3v8"/><line x1="8" y1="16" x2="8" y2="17"/><line x1="13" y1="16" x2="13" y2="17"/><line x1="18" y1="16" x2="18" y2="17"/>',
        'users'     => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/>',
        'phone'     => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/>',
        'mail'      => '<rect x="2" y="4" width="20" height="16" rx="2"/><polyline points="22 6 12 13 2 6"/>',
        'pin'       => '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>',
        'globe'     =>'<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15 15 0 0 1 0 20 15 15 0 0 1 0-20z"/>',
    ];
    if (!isset($paths[$name])) return '';
    return '<svg width="' . (int)$size . '" height="' . (int)$size . '" viewBox="0 0 24 24"'
         . ' fill="none" stroke="currentColor" stroke-width="1.6"'
         . ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
         . $paths[$name] . '</svg>';
}

function getCollections() {
    $all = loadJson('collections.json');
    uasort($all, fn($a, $b) => $b['count'] <=> $a['count']);
    return $all;
}

function getCollectionBySlug($slug) {
    $all = loadJson('collections.json');
    return $all[$slug] ?? null;
}

function getProductsByCollection($slug) {
    return array_values(array_filter(getProducts(),
        fn($p) => ($p['collection'] ?? '') === $slug));
}

function getColors() {
    return loadJson('colors.json');
}

/** Kurumsal sayfalara ait dosya/video verisini döndürür. */
function getKurumsalData($slug) {
    $all = loadJson('kurumsal.json');
    return $all[$slug] ?? [];
}

/** Rapor adlarının içindeki yıla göre gruplar: "2021 Ayrıntılı Bilanço" -> "2021" */
function groupDocumentsByYear($documents) {
    $groups = [];
    foreach ($documents as $doc) {
        $year = preg_match('/\b(20\d{2})\b/', $doc['name'], $m) ? $m[1] : 'Diğer';
        $groups[$year][] = $doc;
    }
    krsort($groups);
    return $groups;
}

function getCategoryBySlug($slug) {
    $cats = getCategories();
    foreach ($cats as $cat) {
        if ($cat['slug'] === $slug) return $cat;
        if (!empty($cat['subcategories'])) {
            foreach ($cat['subcategories'] as $sub) {
                if ($sub['slug'] === $slug) {
                    $sub['parent'] = $cat;
                    return $sub;
                }
            }
        }
    }
    return null;
}

function getCategoryById($id) {
    $cats = getCategories();
    foreach ($cats as $cat) {
        if ($cat['id'] == $id) return $cat;
        if (!empty($cat['subcategories'])) {
            foreach ($cat['subcategories'] as $sub) {
                if ($sub['id'] == $id) {
                    $sub['parent'] = $cat;
                    return $sub;
                }
            }
        }
    }
    return null;
}

function getProductsByCategory($categoryId, $subcategoryId = null) {
    $products = getProducts();
    return array_filter($products, function($p) use ($categoryId, $subcategoryId) {
        if ($subcategoryId) {
            return $p['category_id'] == $categoryId && $p['subcategory_id'] == $subcategoryId;
        }
        return $p['category_id'] == $categoryId;
    });
}

/**
 * Ürünün renk seçeneklerini "Renk" içeren özellik alanlarından çıkarır.
 * Dönüş: ['Beyaz', 'Antrasit'] gibi temiz bir liste.
 */
function getProductColors($product) {
    $colors = [];
    foreach ($product['attributes'] ?? [] as $attr) {
        if (stripos($attr['label'], 'renk') === false) continue;
        foreach (explode(',', $attr['value']) as $color) {
            $color = trim($color);
            if ($color !== '' && !in_array($color, $colors, true)) {
                $colors[] = $color;
            }
        }
    }
    return $colors;
}

/**
 * Katalog filtreleri için renkleri ürün sayısıyla birlikte sayar.
 */
function getColorFacets($products, $minCount = 2) {
    $counts = [];
    foreach ($products as $p) {
        foreach (getProductColors($p) as $color) {
            $counts[$color] = ($counts[$color] ?? 0) + 1;
        }
    }
    $counts = array_filter($counts, fn($n) => $n >= $minCount);
    arsort($counts);
    return $counts;
}

function getProductBySlug($slug) {
    $products = getProducts();
    foreach ($products as $p) {
        if ($p['slug'] === $slug) return $p;
    }
    return null;
}

function getProductById($id) {
    $products = getProducts();
    foreach ($products as $p) {
        if ($p['id'] == $id) return $p;
    }
    return null;
}

function getFeaturedProducts() {
    $products = getProducts();
    return array_filter($products, function($p) {
        return !empty($p['featured']);
    });
}

function getPageBySlug($slug) {
    $pages = getPages();
    foreach ($pages as $p) {
        if ($p['slug'] === $slug) return $p;
    }
    return null;
}

function normalizeSearch($text) {
    $text = mb_strtolower($text, 'UTF-8');
    $tr = ['ç'=>'c','ğ'=>'g','ı'=>'i','ö'=>'o','ş'=>'s','ü'=>'u'];
    return strtr($text, $tr);
}

function searchProducts($query, $limit = null) {
    $query = trim($query);
    if ($query === '') return [];
    $needle = normalizeSearch($query);
    $categories = getCategories();

    $results = [];
    foreach (getProducts() as $p) {
        $cat = getCategoryById($p['category_id']);
        $haystack = normalizeSearch($p['name'] . ' ' . ($p['code'] ?? '') . ' ' . $p['slug'] . ' ' . ($p['features'] ?? '') . ' ' . ($cat['name'] ?? ''));
        if (strpos($haystack, $needle) !== false) {
            $results[] = $p;
            if ($limit && count($results) >= $limit) break;
        }
    }
    return $results;
}

function slugify($text) {
    $text = mb_strtolower($text, 'UTF-8');
    $tr = ['ç'=>'c','ğ'=>'g','ı'=>'i','ö'=>'o','ş'=>'s','ü'=>'u','Ç'=>'c','Ğ'=>'g','İ'=>'i','Ö'=>'o','Ş'=>'s','Ü'=>'u'];
    $text = strtr($text, $tr);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

function getNextId($items) {
    $maxId = 0;
    foreach ($items as $item) {
        if ($item['id'] > $maxId) $maxId = $item['id'];
    }
    return $maxId + 1;
}

function uploadImage($file, $subdir = 'products') {
    $targetDir = UPLOAD_DIR . $subdir . '/';
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowed)) return false;

    $filename = slugify(pathinfo($file['name'], PATHINFO_FILENAME)) . '-' . time() . '.' . $ext;
    $targetPath = $targetDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $filename;
    }
    return false;
}

/**
 * PDF/doküman yükler. Dönüş: "assets/documents/<alt klasör>/dosya.pdf" veya false.
 */
function uploadDocument($file, $subdir = '') {
    $base = __DIR__ . '/../assets/documents/';
    $targetDir = $base . ($subdir ? trim($subdir, '/') . '/' : '');
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['pdf', 'doc', 'docx', 'xls', 'xlsx'], true)) return false;

    $filename = slugify(pathinfo($file['name'], PATHINFO_FILENAME)) . '-' . time() . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $targetDir . $filename)) return false;

    return 'assets/documents/' . ($subdir ? trim($subdir, '/') . '/' : '') . $filename;
}

/**
 * Formdan gelen satır bazlı metni diziye çevirir (her satır bir madde).
 */
function linesToArray($text) {
    $lines = preg_split('/\r\n|\r|\n/', (string)$text);
    $lines = array_map('trim', $lines);
    return array_values(array_filter($lines, fn($l) => $l !== ''));
}

function getProductImage($product) {
    if (!empty($product['images']) && !empty($product['images'][0])) {
        $imgPath = 'assets/images/products/' . $product['images'][0];
        if (file_exists(__DIR__ . '/../' . $imgPath)) {
            return $imgPath;
        }
    }
    return 'assets/images/placeholder.svg';
}

function getCategoryImage($category) {
    if (!empty($category['image'])) {
        if (file_exists(__DIR__ . '/../' . $category['image'])) {
            return $category['image'];
        }
    }
    return 'assets/images/placeholder.svg';
}

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

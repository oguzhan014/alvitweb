<?php
// ============================================================
// HELPERS - Veri okuma/yazma ve yardımcı fonksiyonlar
// ============================================================

define('DATA_DIR', __DIR__ . '/../data/');
define('UPLOAD_DIR', __DIR__ . '/../assets/images/');

function loadJson($file) {
    $path = DATA_DIR . $file;
    if (!file_exists($path)) return [];
    $content = file_get_contents($path);
    return json_decode($content, true) ?: [];
}

function saveJson($file, $data) {
    $path = DATA_DIR . $file;
    file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
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

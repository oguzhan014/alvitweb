<?php
// ============================================================
// SEO - Meta tags, structured data, sitemap
// ============================================================

define('SITE_URL', 'https://www.alvit.com.tr');

function seoUrl($path = '') {
    $L = lang();
    $full = $L === 'en' ? '/en' . $path : ($path ?: '/');
    return SITE_URL . $full;
}

function seoCanonical($path = null) {
    if ($path === null) {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = preg_replace('#^/en#', '', $path);
    }
    return seoUrl(rtrim($path, '/') ?: '');
}

function seoHreflang($path = null) {
    if ($path === null) {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = preg_replace('#^/en#', '', $path);
    }
    $path = rtrim($path, '/') ?: '';
    $tr = SITE_URL . ($path ?: '/');
    $en = SITE_URL . '/en' . $path;
    return '<link rel="alternate" hreflang="tr" href="' . e($tr) . '">' . "\n"
         . '  <link rel="alternate" hreflang="en" href="' . e($en) . '">' . "\n"
         . '  <link rel="alternate" hreflang="x-default" href="' . e($tr) . '">';
}

function seoMetaTags($options = []) {
    $title = $options['title'] ?? '';
    $description = $options['description'] ?? '';
    $image = $options['image'] ?? '';
    $type = $options['type'] ?? 'website';
    $canonical = $options['canonical'] ?? seoCanonical();

    if ($image && !str_starts_with($image, 'http')) {
        $image = SITE_URL . '/' . ltrim($image, '/');
    }

    $out = '';

    // Canonical
    $out .= '<link rel="canonical" href="' . e($canonical) . '">' . "\n";

    // Hreflang
    $out .= '  ' . seoHreflang() . "\n";

    // Open Graph
    $out .= '  <meta property="og:type" content="' . e($type) . '">' . "\n";
    $out .= '  <meta property="og:title" content="' . e($title) . '">' . "\n";
    $out .= '  <meta property="og:description" content="' . e($description) . '">' . "\n";
    $out .= '  <meta property="og:url" content="' . e($canonical) . '">' . "\n";
    $out .= '  <meta property="og:site_name" content="Alvit - Bartın Seramik A.Ş.">' . "\n";
    $out .= '  <meta property="og:locale" content="' . (lang() === 'tr' ? 'tr_TR' : 'en_US') . '">' . "\n";
    if ($image) {
        $out .= '  <meta property="og:image" content="' . e($image) . '">' . "\n";
    }

    // Twitter
    $out .= '  <meta name="twitter:card" content="summary_large_image">' . "\n";
    $out .= '  <meta name="twitter:title" content="' . e($title) . '">' . "\n";
    $out .= '  <meta name="twitter:description" content="' . e($description) . '">' . "\n";
    if ($image) {
        $out .= '  <meta name="twitter:image" content="' . e($image) . '">' . "\n";
    }

    return $out;
}

function seoOrganizationSchema() {
    $settings = getSettings();
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'Alvit - Bartın Seramik A.Ş.',
        'alternateName' => 'Alvit',
        'url' => SITE_URL,
        'logo' => SITE_URL . '/' . $settings['logo'],
        'description' => lang() === 'tr'
            ? 'Alvit - Bartın Seramik A.Ş. 1994\'ten bu yana Bartın\'da seramik banyo ürünleri üretmektedir. Lavabo, klozet, rezervuar, banyo mobilyası ve aksesuar üretimi.'
            : 'Alvit - Bartın Seramik A.Ş. has been manufacturing ceramic bathroom products in Bartın since 1994. Production of washbasins, toilets, cisterns, bathroom furniture and accessories.',
        'foundingDate' => '1994',
        'contactPoint' => [
            '@type' => 'ContactPoint',
            'telephone' => $settings['phone'],
            'email' => $settings['email'],
            'contactType' => 'customer service',
            'availableLanguage' => ['Turkish', 'English'],
        ],
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Şiremir Tabaklar Köyü Yemenciler Mevkii No:17',
            'addressLocality' => 'Bartın',
            'addressCountry' => 'TR',
        ],
        'sameAs' => array_values(array_filter([
            $settings['social']['facebook'] ?? '',
            $settings['social']['instagram'] ?? '',
            $settings['social']['youtube'] ?? '',
            $settings['social']['linkedin'] ?? '',
        ])),
    ];
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

function seoLocalBusinessSchema() {
    $settings = getSettings();
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        '@id' => SITE_URL . '/#localbusiness',
        'name' => 'Alvit - Bartın Seramik A.Ş.',
        'image' => SITE_URL . '/' . $settings['logo'],
        'url' => SITE_URL,
        'telephone' => $settings['phone'],
        'email' => $settings['email'],
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Şiremir Tabaklar Köyü Yemenciler Mevkii No:17',
            'addressLocality' => 'Bartın',
            'postalCode' => '74100',
            'addressCountry' => 'TR',
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => 41.6345,
            'longitude' => 32.3375,
        ],
        'priceRange' => '$$',
        'openingHoursSpecification' => [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
            'opens' => '08:00',
            'closes' => '17:00',
        ],
        'areaServed' => [
            ['@type' => 'City', 'name' => 'Bartın'],
            ['@type' => 'Country', 'name' => 'Türkiye'],
        ],
    ];
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

function seoProductSchema($product) {
    $image = SITE_URL . '/' . getProductImage($product);
    $category = getCategoryById($product['category_id']);

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => td($product, 'name'),
        'description' => td($product, 'features') ?: td($product, 'name'),
        'image' => $image,
        'url' => seoUrl('/urun/' . $product['slug']),
        'brand' => [
            '@type' => 'Brand',
            'name' => 'Alvit',
        ],
        'manufacturer' => [
            '@type' => 'Organization',
            'name' => 'Alvit - Bartın Seramik A.Ş.',
        ],
    ];

    if (!empty($product['code'])) {
        $schema['sku'] = $product['code'];
        $schema['mpn'] = $product['code'];
    }

    if ($category) {
        $schema['category'] = td($category, 'name');
    }

    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

function seoBreadcrumbSchema($items) {
    $list = [];
    foreach ($items as $i => $item) {
        $entry = [
            '@type' => 'ListItem',
            'position' => $i + 1,
            'name' => $item['name'],
        ];
        if (!empty($item['url'])) {
            $entry['item'] = seoUrl($item['url']);
        }
        $list[] = $entry;
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $list,
    ];
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

function seoWebSiteSchema() {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'Alvit',
        'url' => SITE_URL,
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => SITE_URL . '/ara?q={search_term_string}',
            ],
            'query-input' => 'required name=search_term_string',
        ],
    ];
    return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

function generateSitemap() {
    $urls = [];

    // Homepage
    $urls[] = ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'weekly'];

    // Static pages
    $urls[] = ['loc' => '/urunler', 'priority' => '0.9', 'changefreq' => 'weekly'];
    $urls[] = ['loc' => '/kategoriler', 'priority' => '0.8', 'changefreq' => 'weekly'];
    $urls[] = ['loc' => '/koleksiyonlar', 'priority' => '0.8', 'changefreq' => 'weekly'];
    $urls[] = ['loc' => '/galeri', 'priority' => '0.6', 'changefreq' => 'monthly'];
    $urls[] = ['loc' => '/iletisim', 'priority' => '0.7', 'changefreq' => 'monthly'];

    // Categories
    foreach (getCategories() as $cat) {
        $urls[] = ['loc' => '/kategori/' . $cat['slug'], 'priority' => '0.8', 'changefreq' => 'weekly'];
        foreach ($cat['subcategories'] ?? [] as $sub) {
            $urls[] = ['loc' => '/kategori/' . $sub['slug'], 'priority' => '0.7', 'changefreq' => 'weekly'];
        }
    }

    // Collections
    foreach (getCollections() as $col) {
        $urls[] = ['loc' => '/koleksiyon/' . $col['slug'], 'priority' => '0.7', 'changefreq' => 'weekly'];
    }

    // Products
    foreach (getProducts() as $p) {
        $urls[] = ['loc' => '/urun/' . $p['slug'], 'priority' => '0.6', 'changefreq' => 'monthly'];
    }

    // CMS Pages
    foreach (getPages() as $page) {
        $urls[] = ['loc' => '/sayfa/' . $page['slug'], 'priority' => '0.5', 'changefreq' => 'monthly'];
    }

    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
    $xml .= '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";

    $today = date('Y-m-d');
    foreach ($urls as $u) {
        $trUrl = SITE_URL . $u['loc'];
        $enUrl = SITE_URL . '/en' . $u['loc'];

        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($trUrl) . "</loc>\n";
        $xml .= '    <xhtml:link rel="alternate" hreflang="tr" href="' . htmlspecialchars($trUrl) . '"/>' . "\n";
        $xml .= '    <xhtml:link rel="alternate" hreflang="en" href="' . htmlspecialchars($enUrl) . '"/>' . "\n";
        $xml .= "    <lastmod>{$today}</lastmod>\n";
        $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n";
        $xml .= "    <priority>{$u['priority']}</priority>\n";
        $xml .= "  </url>\n";

        // English version
        $xml .= "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($enUrl) . "</loc>\n";
        $xml .= '    <xhtml:link rel="alternate" hreflang="tr" href="' . htmlspecialchars($trUrl) . '"/>' . "\n";
        $xml .= '    <xhtml:link rel="alternate" hreflang="en" href="' . htmlspecialchars($enUrl) . '"/>' . "\n";
        $xml .= "    <lastmod>{$today}</lastmod>\n";
        $xml .= "    <changefreq>{$u['changefreq']}</changefreq>\n";
        $xml .= "    <priority>{$u['priority']}</priority>\n";
        $xml .= "  </url>\n";
    }

    $xml .= "</urlset>\n";
    return $xml;
}

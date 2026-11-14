<?php
// PHP Built-in Server Router
// Kullanım: php -S localhost:8000 router.php

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Hassas veri klasorlerine disaridan erisimi engelle (canli sunucudaki .htaccess ile ayni kural)
if (preg_match('#^/(data|includes)/#', $path)) {
    http_response_code(403);
    echo 'Forbidden';
    return true;
}

// Statik dosyalar
$file = __DIR__ . $path;
if ($path !== '/' && file_exists($file) && !is_dir($file)) {
    // MIME type
    $ext = pathinfo($file, PATHINFO_EXTENSION);
    $mimes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
        'pdf' => 'application/pdf',
        'woff2' => 'font/woff2',
        'woff' => 'font/woff',
        'ttf' => 'font/ttf',
        'ico' => 'image/x-icon',
    ];
    if (isset($mimes[$ext])) {
        header('Content-Type: ' . $mimes[$ext]);
    }
    return false; // PHP built-in server statik dosyayı kendisi sunar
}

// Dinamik sayfalar
require __DIR__ . '/index.php';

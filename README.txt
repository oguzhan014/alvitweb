ALVIT - Modern Seramik Banyo Sitesi
====================================

KURULUM
-------
1. Bu klasoru bilgisayarinda bir yere koy (ornegin C:\alvit-site)
2. Terminal/CMD ac ve o klasore git:
   cd C:\alvit-site
3. PHP ile calistir:
   php -S localhost:8000 router.php
4. Tarayicida ac: http://localhost:8000

ADMIN PANEL
-----------
URL: http://localhost:8000/admin
Kullanici Adi: admin
Sifre: alvit2024

DOSYA YAPISI
------------
alvit-site/
  index.php          - Ana router
  router.php         - PHP built-in server router
  .htaccess          - Apache icin URL rewrite
  includes/
    helpers.php      - Veri okuma/yazma fonksiyonlari
    header.php       - Site header
    footer.php       - Site footer
  pages/
    home.php         - Anasayfa
    categories.php   - Kategori listesi
    category.php     - Tek kategori sayfasi
    product.php      - Urun detay
    page.php         - Kurumsal sayfalar
    contact.php      - Iletisim
    404.php          - Hata sayfasi
  admin/
    index.php        - Admin router
    login.php        - Giris sayfasi
    layout.php       - Admin layout
    dashboard.php    - Dashboard
    products.php     - Urun yonetimi
    categories.php   - Kategori yonetimi
    sliders.php      - Slider yonetimi
    cms-pages.php    - Sayfa yonetimi
    messages.php     - Mesaj yonetimi
    settings.php     - Site ayarlari
  assets/
    css/style.css    - Frontend CSS
    css/admin.css    - Admin panel CSS
    js/main.js       - Frontend JavaScript
    images/          - Gorseller
  data/
    settings.json    - Site ayarlari
    categories.json  - Kategoriler
    products.json    - Urunler
    pages.json       - Sayfalar
    sliders.json     - Slider verileri
    messages.json    - Iletisim mesajlari

GORSEL EKLEME
-------------
Urun gorsellerini assets/images/products/ klasorune koyun.
Kategori gorsellerini assets/images/categories/ klasorune koyun.
Slider gorsellerini assets/images/sliders/ klasorune koyun.

Admin panelden de gorsel yukleyebilirsiniz.

Alvit sitesindeki gorselleri indirip ilgili klasorlere koymaniz gerekiyor.

NOT
---
- Veritabani gerektirmez, tum veriler JSON dosyalarinda tutulur.
- PHP 7.4+ gereklidir.
- Apache kullaniyorsaniz mod_rewrite aktif olmali.
- Nginx icin ayri yapilandirma gerekir.

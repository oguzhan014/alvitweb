@echo off
echo Alvit web sitesi baslatiliyor...
echo http://localhost:8000
echo Kapatmak icin Ctrl+C
php -S localhost:8000 -d upload_max_filesize=20M -d post_max_size=25M router.php

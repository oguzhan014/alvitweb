#!/usr/bin/env python3
"""
Alvit Koleksiyon ve Renk Toplayici
==================================
alvit.com.tr'deki "Koleksiyonlar" ve "Renkliler" bolumlerini toplar.

  - Koleksiyonlar: her seri (Gideros, Tulip, Efes...) ve icerdigi urun ID'leri
  - Renkliler    : her renk ve vitrin gorseli (indirilir)

Cikti: data/collections.json, data/colors.json
Gorseller: assets/images/colors/

Kullanim:  python -I -X utf8 scrape_koleksiyon.py
"""

import urllib.request
import re
import os
import json
import time
import html as htmllib

BASE = "https://www.alvit.com.tr"
HEADERS = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    'Referer': BASE + '/',
}

ROOT = os.path.dirname(os.path.abspath(__file__))
COLOR_DIR = os.path.join(ROOT, 'assets', 'images', 'colors')

COLLECTIONS = [
    "cakraz-serisi", "child-serisi-3", "edge-serisi", "efekta-serisi-3",
    "efes-serisi-4", "engelli-serisi-3", "gideros-serisi-4", "inkum-serisi-4",
    "line-serisi-2", "melt-serisi", "safran-serisi-4", "thin-serisi-2",
    "tulip-serisi-2",
]

COLORS = [
    ("parlak-siyah.286", "Parlak Siyah"), ("mat-siyah.287", "Mat Siyah"),
    ("antrasit.288", "Antrasit"), ("vizon.289", "Vizon"),
    ("karamel.290", "Karamel"), ("somon.291", "Somon"),
    ("turuncu.292", "Turuncu"), ("kirmizi.293", "Kırmızı"),
    ("altin.294", "Altın"), ("platin.295", "Platin"),
]

TR = {'ç': 'c', 'ğ': 'g', 'ı': 'i', 'ö': 'o', 'ş': 's', 'ü': 'u', 'İ': 'i'}


def fetch(url, tries=3):
    for i in range(tries):
        try:
            req = urllib.request.Request(url, headers=HEADERS)
            with urllib.request.urlopen(req, timeout=40) as r:
                return r.read().decode('utf-8', errors='replace')
        except Exception as e:
            if i == tries - 1:
                print(f"  ! {url}: {e}")
            time.sleep(2)
    return ""


def clean(s):
    s = re.sub(r'<[^>]+>', ' ', s)
    return re.sub(r'\s+', ' ', htmllib.unescape(s)).strip()


def slugify(s):
    s = ''.join(TR.get(c, c) for c in s.lower())
    return re.sub(r'[^a-z0-9]+', '-', s).strip('-')


def page_title(html, fallback):
    """Sayfa basligini <title> etiketinden alir (h1/h2 koleksiyonlarda bos geliyor)."""
    m = re.search(r'<title>(.*?)</title>', html, re.S)
    if m:
        title = clean(m.group(1)).split(' - ')[0].strip()
        if title:
            return title
    return fallback


def main():
    os.makedirs(COLOR_DIR, exist_ok=True)

    # ---- KOLEKSIYONLAR ----
    print("KOLEKSIYONLAR")
    collections = {}
    for slug in COLLECTIONS:
        html = fetch(f"{BASE}/urunler/koleksiyonlar/{slug}")
        if not html:
            continue
        ids = []
        for m in re.finditer(r'href="https://www\.alvit\.com\.tr/urunler/[^"]*?\.(\d+)"', html):
            if m.group(1) not in ids:
                ids.append(m.group(1))
        title = page_title(html, slug)
        key = re.sub(r'-\d+$', '', slug)
        collections[key] = {
            "title": title,
            "slug": key,
            "product_ids": ids,
            "source_url": f"{BASE}/urunler/koleksiyonlar/{slug}",
        }
        print(f"   {title:<24} {len(ids)} urun")
        time.sleep(0.25)

    with open(os.path.join(ROOT, 'data', 'collections.json'), 'w', encoding='utf-8') as f:
        json.dump(collections, f, ensure_ascii=False, indent=2)

    # ---- RENKLILER ----
    print("\nRENKLILER")
    colors = {}
    for path, name in COLORS:
        html = fetch(f"{BASE}/urunler/renkliler/{path}")
        if not html:
            continue
        m = re.search(r'href="(https://www\.alvit\.com\.tr/files[^"]+)"[^>]*class="[^"]*woocommerce-main-image',
                      html)
        img_url = m.group(1) if m else ""
        slug = slugify(name)
        local = ""
        if img_url:
            ext = os.path.splitext(img_url)[1].lower()
            fname = slug + (ext if ext in ('.jpg', '.jpeg', '.png') else '.jpg')
            path_out = os.path.join(COLOR_DIR, fname)
            if os.path.exists(path_out) and os.path.getsize(path_out) > 1000:
                local, info = f"assets/images/colors/{fname}", "mevcut"
            else:
                try:
                    req = urllib.request.Request(img_url, headers=HEADERS)
                    with urllib.request.urlopen(req, timeout=60) as r:
                        data = r.read()
                    with open(path_out, 'wb') as fh:
                        fh.write(data)
                    local, info = f"assets/images/colors/{fname}", f"{len(data)/1024:.0f} KB"
                except Exception as e:
                    info = str(e)
        else:
            info = "gorsel yok"

        colors[slug] = {"name": name, "slug": slug, "image": local,
                        "source_url": f"{BASE}/urunler/renkliler/{path}"}
        print(f"   {name:<16} {info}")
        time.sleep(0.25)

    with open(os.path.join(ROOT, 'data', 'colors.json'), 'w', encoding='utf-8') as f:
        json.dump(colors, f, ensure_ascii=False, indent=2)

    print(f"\nTamamlandi: {len(collections)} koleksiyon, {len(colors)} renk")


if __name__ == '__main__':
    main()

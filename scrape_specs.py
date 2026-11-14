#!/usr/bin/env python3
"""
Alvit Urun Teknik Bilgi Toplayici
=================================
alvit.com.tr uzerindeki her urun sayfasindan:
  - Urun ozellik tablosunu (KOD / URUN OZELLIKLERI / PALET gibi sekmeler)
  - Ek dosyalari (teknik cizim PDF'leri)
  - Urun gorsel adreslerini
toplar, PDF'leri assets/docs/ klasorune indirir ve
data/product-specs.json dosyasina yazar.

Kullanim:  python -I -X utf8 scrape_specs.py
"""

import urllib.request
import urllib.parse
import re
import os
import json
import time
import html as htmllib

BASE = "https://www.alvit.com.tr"
HEADERS = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    'Accept': 'text/html,application/xhtml+xml',
    'Referer': BASE + '/',
}

ROOT = os.path.dirname(os.path.abspath(__file__))
DOCS_DIR = os.path.join(ROOT, 'assets', 'documents')
OUT_FILE = os.path.join(ROOT, 'data', 'product-specs.json')

PRODUCT_RE = re.compile(r'href="(https://www\.alvit\.com\.tr/urunler/[^"]*?\.(\d+))"')
CATEGORY_RE = re.compile(r'href="(https://www\.alvit\.com\.tr/urunler/[^"#]*?)"')


def fetch(url):
    req = urllib.request.Request(url, headers=HEADERS)
    with urllib.request.urlopen(req, timeout=30) as r:
        return r.read().decode('utf-8', errors='replace')


def clean(s):
    s = re.sub(r'<[^>]+>', ' ', s)
    return re.sub(r'\s+', ' ', htmllib.unescape(s)).strip()


def slugify_file(name):
    """Guvenli dosya adi: sadece harf, rakam, tire, alt tire ve uzanti."""
    name = urllib.parse.unquote(name)
    tr = {'ç': 'c', 'ğ': 'g', 'ı': 'i', 'ö': 'o', 'ş': 's', 'ü': 'u',
          'Ç': 'c', 'Ğ': 'g', 'İ': 'i', 'Ö': 'o', 'Ş': 's', 'Ü': 'u'}
    name = ''.join(tr.get(ch, ch) for ch in name).lower()
    name = re.sub(r'[^a-z0-9._-]+', '-', name).strip('-._')
    return name[:120] or 'dosya.pdf'


def discover_products():
    seen, products, queue = {BASE + '/'}, {}, [BASE + '/']
    while queue:
        url = queue.pop(0)
        try:
            html = fetch(url)
        except Exception as e:
            print(f"  ! {url} -> {e}")
            continue
        for m in PRODUCT_RE.finditer(html):
            products.setdefault(m.group(2), m.group(1))
        for m in CATEGORY_RE.finditer(html):
            curl = m.group(1).rstrip('/ ')
            if re.search(r'\.\d+$', curl) or curl in seen:
                continue
            seen.add(curl)
            queue.append(curl)
        time.sleep(0.2)
    return products


def parse_product(html):
    title_m = re.search(r'class="product_title"[^>]*>(.*?)</h1>', html, re.S)
    title = clean(title_m.group(1)) if title_m else ""

    # Sekme basliklari sayfadan okunur (urun tipine gore degisiyor)
    labels = [m.group(1) for m in re.finditer(r"openTab\('([^']*)','\d+'\)", html)]

    columns, raw_cols = [], []
    for label in labels:
        start = html.find('<div id="%s" class="w3-container' % label)
        if start < 0:
            continue
        end = html.find('<div id="', start + 10)
        block = html[start:end if end > 0 else start + 20000]
        cells = [clean(c) for c in re.findall(r'<td[^>]*>(.*?)</td>', block, re.S)]
        columns.append(label)
        raw_cols.append(cells)

    depth = max((len(c) for c in raw_cols), default=0)
    rows = []
    for i in range(depth):
        row = [(col[i] if i < len(col) else "") for col in raw_cols]
        if any(v and v != '-' for v in row):
            rows.append(row)

    pdfs = []
    for m in re.finditer(r'<a\s+href="([^"]+\.(?:pdf|PDF))"[^>]*>(.*?)</a>', html, re.S):
        url = m.group(1)
        lm = re.search(r'<center>(.*?)</center>', m.group(2), re.S)
        label = urllib.parse.unquote(clean(lm.group(1))) if lm else ''
        if not any(p['url'] == url for p in pdfs):
            pdfs.append({"url": url, "label": label})

    img_m = re.search(r'<a href="([^"]+)" class="zoom first"', html)
    return {
        "name": title,
        "columns": columns,
        "rows": rows,
        "pdfs": pdfs,
        "image": img_m.group(1) if img_m else "",
    }


def download_pdf(url, filename):
    path = os.path.join(DOCS_DIR, filename)
    if os.path.exists(path) and os.path.getsize(path) > 1000:
        return True, "mevcut"
    try:
        req = urllib.request.Request(url, headers=HEADERS)
        with urllib.request.urlopen(req, timeout=60) as r:
            data = r.read()
        if len(data) < 500:
            return False, "cok kucuk"
        with open(path, 'wb') as f:
            f.write(data)
        time.sleep(0.2)
        return True, f"{len(data)/1024:.0f} KB"
    except Exception as e:
        return False, str(e)


def main():
    os.makedirs(DOCS_DIR, exist_ok=True)

    print("1) Urun sayfalari taraniyor...")
    products = discover_products()
    print(f"   {len(products)} urun bulundu\n")

    print("2) Urun detaylari cekiliyor...")
    result, pdf_count = {}, 0
    for n, (pid, url) in enumerate(sorted(products.items(), key=lambda kv: int(kv[0])), 1):
        try:
            data = parse_product(fetch(url))
        except Exception as e:
            print(f"   ! {pid} {url} -> {e}")
            continue
        data["id"] = pid
        data["url"] = url
        result[pid] = data
        pdf_count += len(data["pdfs"])
        print(f"   [{n}/{len(products)}] {data['name']}  "
              f"({len(data['rows'])} satir, {len(data['pdfs'])} pdf)")
        time.sleep(0.2)

    print(f"\n3) PDF'ler indiriliyor ({pdf_count} adet)...")
    ok = fail = 0
    for pid, data in result.items():
        for pdf in data["pdfs"]:
            fname = slugify_file(pdf["url"].rsplit('/', 1)[-1])
            if not fname.lower().endswith('.pdf'):
                fname += '.pdf'
            success, info = download_pdf(pdf["url"], fname)
            pdf["file"] = f"assets/documents/{fname}" if success else ""
            if success:
                ok += 1
            else:
                fail += 1
                print(f"   ! {fname}: {info}")

    with open(OUT_FILE, 'w', encoding='utf-8') as f:
        json.dump(result, f, ensure_ascii=False, indent=2)

    print(f"\nTamamlandi: {len(result)} urun, {ok} PDF indirildi, {fail} hata")
    print(f"Cikti: {OUT_FILE}")


if __name__ == '__main__':
    main()

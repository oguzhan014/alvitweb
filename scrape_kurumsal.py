#!/usr/bin/env python3
"""
Alvit Kurumsal Sayfa Toplayici
==============================
alvit.com.tr/kurumsal altindaki sayfalarin icerigini toplar:
  - Raporlar ve Montaj Kilavuzlari  -> PDF listesi (dosyalar indirilir)
  - Urun Montaj Videolari           -> YouTube video listesi
  - KVKK / Cerez / Basvuru Formu    -> temizlenmis metin icerigi

Cikti: data/kurumsal.json   PDF'ler: assets/documents/<klasor>/

Kullanim:  python -I -X utf8 scrape_kurumsal.py
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
DOCS_ROOT = os.path.join(ROOT, 'assets', 'documents')
OUT_FILE = os.path.join(ROOT, 'data', 'kurumsal.json')

# slug -> (baslik, tip, pdf klasoru)
PAGES = {
    "raporlar": ("Raporlar", "documents", "raporlar"),
    "montaj-kilavuzlari": ("Montaj Kılavuzları", "documents", "kilavuzlar"),
    "urun-montaj-videolari": ("Ürün Montaj Videoları", "videos", None),
    "kisisel-verilerin-korunmasi-politikasi": ("Kişisel Verilerin Korunması Politikası", "text", None),
    "cerez-aydinlatma-metni": ("Çerez Aydınlatma Metni", "text", None),
    "kvkk-ilgili-kisi-basvuru-formu": ("KVKK İlgili Kişi Başvuru Formu", "text", None),
}

TR = {'ç': 'c', 'ğ': 'g', 'ı': 'i', 'ö': 'o', 'ş': 's', 'ü': 'u', 'İ': 'i'}


def fetch(url, tries=4):
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


def clean_text(s):
    s = re.sub(r'<[^>]+>', ' ', s)
    return re.sub(r'\s+', ' ', htmllib.unescape(s)).strip()


def slugify(name):
    name = urllib.parse.unquote(name).lower()
    name = ''.join(TR.get(c, c) for c in name)
    return re.sub(r'[^a-z0-9._-]+', '-', name).strip('-._')[:120]


def live_url(url):
    """Olu staging adreslerini canli alan adina cevirir."""
    url = url.replace('http://yeni.alvit.com.tr', BASE)
    url = url.replace('http://alvit.com.tr', BASE)
    url = url.replace('https://alvit.com.tr', BASE)
    return url


def content_block(html):
    """Sayfanin asil icerik bolumunu dondurur."""
    m = re.search(r'<h2 class="section_header[^"]*">.*?</h2>(.*?)</div>\s*</div>\s*</div>',
                  html, re.S)
    return m.group(1) if m else ""


def sanitize_html(block):
    """Scraped HTML'i guvenli ve sade hale getirir: sadece p/strong/em/br/ul/li/h3."""
    block = re.sub(r'<(script|style)[^>]*>.*?</\1>', '', block, flags=re.S | re.I)
    block = re.sub(r'<img[^>]*>', '', block, flags=re.I)
    block = re.sub(r'</?(span|font|div|table|tbody|tr|td|a)[^>]*>', '', block, flags=re.I)
    block = re.sub(r'<(\w+)[^>]*>', lambda m: f'<{m.group(1).lower()}>', block)

    allowed = {'p', 'strong', 'b', 'em', 'i', 'br', 'ul', 'ol', 'li', 'h3', 'h4'}
    block = re.sub(r'</?(\w+)>',
                   lambda m: m.group(0) if m.group(1).lower() in allowed else '', block)

    block = htmllib.unescape(block)
    block = re.sub(r'[ \t]+', ' ', block)
    block = re.sub(r'<p>\s*<p>', '<p>', block)          # ic ice paragraf
    block = re.sub(r'</p>\s*</p>', '</p>', block)
    block = re.sub(r'<p>\s*(<br>\s*)*</p>', '', block)
    block = re.sub(r'(<br>\s*){3,}', '<br><br>', block)
    # sondaki "EK DOSYALAR" basligi ayri gosterildigi icin metinden cikarilir
    block = re.sub(r'<strong>\s*EK DOSYALAR\s*</strong>.*$', '', block, flags=re.S | re.I)
    return re.sub(r'\n{2,}', '\n', block).strip()


def youtube_title(video_id):
    """Video basligini YouTube oEmbed ucundan alir."""
    api = ("https://www.youtube.com/oembed?url=https://www.youtube.com/watch%3Fv%3D"
           + video_id + "&format=json")
    try:
        req = urllib.request.Request(api, headers=HEADERS)
        with urllib.request.urlopen(req, timeout=30) as r:
            return json.loads(r.read().decode('utf-8')).get('title', '')
    except Exception as e:
        print(f"   ! baslik alinamadi ({video_id}): {e}")
        return ""


def parse_documents(block):
    """'>> <a href=...>Baslik</a>' kalibindaki dosya listesini cikarir."""
    docs = []
    for m in re.finditer(r'<a\s+href="([^"]+\.(?:pdf|PDF|docx?|xlsx?))"[^>]*>(.*?)</a>',
                         block, re.S):
        url = live_url(m.group(1))
        label = clean_text(m.group(2))
        if label and not any(d['url'] == url for d in docs):
            docs.append({"name": label, "url": url})
    return docs


def parse_videos(block, html):
    """YouTube videolarini basliklariyla birlikte toplar."""
    videos, seen = [], set()
    for m in re.finditer(
            r'(?:<h\d[^>]*>(.*?)</h\d>\s*)?<iframe[^>]+src="[^"]*(?:youtube\.com/embed/|youtu\.be/)'
            r'([A-Za-z0-9_-]{6,})', html, re.S):
        vid = m.group(2)
        if vid in seen:
            continue
        seen.add(vid)
        videos.append({"id": vid, "title": clean_text(m.group(1) or "")})
    return videos


def download(url, folder, filename):
    target_dir = os.path.join(DOCS_ROOT, folder)
    os.makedirs(target_dir, exist_ok=True)
    path = os.path.join(target_dir, filename)
    if os.path.exists(path) and os.path.getsize(path) > 1000:
        return f"assets/documents/{folder}/{filename}", "mevcut"
    for attempt in range(3):
        try:
            req = urllib.request.Request(url, headers=HEADERS)
            with urllib.request.urlopen(req, timeout=90) as r:
                data = r.read()
            if len(data) < 500:
                return "", "cok kucuk"
            with open(path, 'wb') as f:
                f.write(data)
            time.sleep(0.25)
            return f"assets/documents/{folder}/{filename}", f"{len(data)/1024:.0f} KB"
        except Exception as e:
            err = str(e)
            time.sleep(2)
    return "", err


def main():
    result = {}
    for slug, (title, kind, folder) in PAGES.items():
        url = f"{BASE}/kurumsal/{slug}"
        print(f"\n### {title}")
        html = fetch(url)
        if not html:
            continue
        block = content_block(html)

        entry = {"title": title, "slug": slug, "type": kind, "source_url": url}

        if kind == "documents":
            docs = parse_documents(block)
            print(f"   {len(docs)} dosya bulundu")
            for d in docs:
                fname = slugify(d['url'].rsplit('/', 1)[-1])
                if not re.search(r'\.\w{2,4}$', fname):
                    fname += '.pdf'
                d['file'], info = download(d['url'], folder, fname)
                print(f"   {'OK ' if d['file'] else '!! '}{d['name'][:48]:<50} {info}")
            entry['documents'] = [d for d in docs if d['file']]

        elif kind == "videos":
            vids = parse_videos(block, html)
            for v in vids:
                v['title'] = v['title'] or youtube_title(v['id'])
                print(f"   video {v['id']}  {v['title']}")
                time.sleep(0.2)
            entry['videos'] = vids

        else:
            entry['content'] = sanitize_html(block)
            # metin sayfalarinda da ek dosya olabiliyor (ornegin basvuru formu)
            docs = parse_documents(block)
            for d in docs:
                fname = slugify(d['url'].rsplit('/', 1)[-1])
                if not re.search(r'\.\w{2,4}$', fname):
                    fname += '.pdf'
                d['file'], info = download(d['url'], 'kurumsal', fname)
                print(f"   ek dosya: {d['name'][:40]:<42} {info}")
            entry['documents'] = [d for d in docs if d['file']]
            print(f"   {len(entry['content'])} karakter metin")

        result[slug] = entry
        time.sleep(0.3)

    with open(OUT_FILE, 'w', encoding='utf-8') as f:
        json.dump(result, f, ensure_ascii=False, indent=2)
    print(f"\nTamamlandi -> {OUT_FILE}")


if __name__ == '__main__':
    main()

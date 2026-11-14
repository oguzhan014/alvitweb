#!/usr/bin/env python3
"""
Alvit Gorsellerini Indir
========================
Bu scripti proje klasorunde calistirin:
  cd alvit-site
  python download_images.py

Tum urun, kategori ve slider gorsellerini alvit.com.tr'den indirir.
"""

IMAGE_MAP = {
    # === URUN GORSELLERI ===
    "products": {
        # Konsol Lavabolar
        "new-line-80.jpg": "https://www.alvit.com.tr/files/app/2023-02/new_line_80_temizjpg_qoMfr.jpg",
        "line-55.jpg": "https://www.alvit.com.tr/files/app/2020-06/55jpg_mwBgm.jpg",
        "line-65.jpg": "https://www.alvit.com.tr/files/app/2020-06/65jpg_m0ZO3.jpg",
        "line-80.jpg": "https://www.alvit.com.tr/files/app/2020-06/80jpg_aXBW8.jpg",
        "line-100.jpg": "https://www.alvit.com.tr/files/app/2020-06/100jpg_Ru8sR.jpg",
        "thin-55.jpg": "https://www.alvit.com.tr/files/app/2020-07/55_cmjpg_Dd9dI.jpg",
        "thin-60.jpg": "https://www.alvit.com.tr/files/app/2020-07/60_cmjpg_9LNzK.jpg",
        "thin-65.jpg": "https://www.alvit.com.tr/files/app/2020-07/65_cmjpg_EOd7Z.jpg",
        "thin-80.jpg": "https://www.alvit.com.tr/files/app/2020-07/80_cmjpg_z2Lky.jpg",
        "thin-100.jpg": "https://www.alvit.com.tr/files/app/2020-07/100_cmjpg_4AwsL.jpg",
        # Tezgah Ustu Lavabolar
        "tulip-55.jpg": "https://www.alvit.com.tr/files/app/2020-07/55jpg_7p0BR.jpg",
        "tulip-65.jpg": "https://www.alvit.com.tr/files/app/2020-07/65jpg_YgH9r.jpg",
        "tulip-75.jpg": "https://www.alvit.com.tr/files/app/2020-07/75_cmjpg_v90kB.jpg",
        "tulip-80.jpg": "https://www.alvit.com.tr/files/app/2020-07/80_cmjpg_H1N91.jpg",
        "tulip-86.jpg": "https://www.alvit.com.tr/files/app/2020-07/86_cmjpg_z4Jo5.jpg",
        "tulip-100.jpg": "https://www.alvit.com.tr/files/app/2020-07/100jpg_oTutE.jpg",
        "tulip-105.jpg": "https://www.alvit.com.tr/files/app/2020-07/105jpg_eu9Q9.jpg",
        "art-40.jpg": "https://www.alvit.com.tr/files/app/2020-06/40x50jpg_fIQkq.jpg",
        # Banyo Mobilyalari (Asma Klozetler)
        "eylul-80.jpg": "https://www.alvit.com.tr/files/app/2023-02/eylul_grijpg_VYtly.jpg",
        "astona-100.jpg": "https://www.alvit.com.tr/files/app/2020-06/astona_100jpg_L0EOP.jpg",
        "astona-80.jpg": "https://www.alvit.com.tr/files/app/2020-06/astona_80jpg_Tpf2H.jpg",
        "craft-105.jpg": "https://www.alvit.com.tr/files/app/2020-06/craft_105jpg_SrQeM.jpg",
        "mimoza-130.jpg": "https://www.alvit.com.tr/files/app/2020-06/mimoza_130jpg_aAOQj.jpg",
        "granada-105.jpg": "https://www.alvit.com.tr/files/app/2020-06/granada_105jpg_G27ln.jpg",
        "carelia-80.jpg": "https://www.alvit.com.tr/files/app/2020-06/carelia_80jpg_5F4GZ.jpg",
        "barcelona-100.jpg": "https://www.alvit.com.tr/files/app/2020-06/barcelona_100jpg_C8Rcp.jpg",
        # Duvara Sifir Klozetler - Arnova/Venus artik sitede yok (urun kaldirilmis), atlandi
        # Klozet Kapaklari
        "star-kapak.jpg": "https://www.alvit.com.tr/files/app/2020-06/star_klozetjpg_bfzPf.JPG",
        "galaxy-kapak.jpg": "https://www.alvit.com.tr/files/app/2020-06/galaxyjpg_CaGjb.jpg",
        "galaxy-slim-kapak.jpg": "https://www.alvit.com.tr/files/app/2020-06/galaxy_slimjpg_pH9Qs.jpg",
        "space-kapak.jpg": "https://www.alvit.com.tr/files/app/2020-06/spacejpg_i08vf.jpg",
        "space-cocuk-kapak.jpg": "https://www.alvit.com.tr/files/app/2020-06/space_cocukjpg_SDScs.jpg",
        # Gomme Rezervuar
        "gomme-1.jpg": "https://www.alvit.com.tr/files/app/2020-06/asma_1jpg_IRAxa.jpg",
        "gomme-2.jpg": "https://www.alvit.com.tr/files/app/2020-06/asma_2jpg_V9pWH.jpg",
        "gomme-3.jpg": "https://www.alvit.com.tr/files/app/2020-06/asma_3jpg_YVHCW.jpg",
        # Butonlar & Ic Takimlar
        "butonlar.jpg": "https://www.alvit.com.tr/files/app/2020-06/butonlarjpg_giDNJ.jpg",
        "ic-takimlar.jpg": "https://www.alvit.com.tr/files/app/2020-06/ictakimjpg_wD4KV.jpg",
        # Asma Klozetler
        "astra-asma-klozet.jpg": "https://www.alvit.com.tr/files/app/600x300/2020-06/asil_astra_asmajpg_1jDcY.JPG",
        "gideros-asma-klozet.jpg": "https://www.alvit.com.tr/files/app/600x300/2020-06/gideros_asma_klozetjpg_bA1bS.jpg",
        # Duvara Sifir Klozetler
        "gideros-ds-klozet.jpg": "https://www.alvit.com.tr/files/app/600x300/2020-06/gideros_ds_klozetjpg_qnUzo.jpg",
        "safran-ds-klozet.jpg": "https://www.alvit.com.tr/files/app/600x300/2020-06/safran_ds_klozetjpg_qJ7n2.jpg",
        "safran-ds-ankastre-klozet.jpg": "https://www.alvit.com.tr/files/app/600x300/2020-06/safran_ds_ankastre_klozetjpg_6R1on.jpg",
        # Takim Klozetler
        "inkum-alt-cikisli-klozet.jpg": "https://www.alvit.com.tr/files/app/600x300/2020-06/inkum_alt_cikisli_klozetjpg_5IMEC.jpg",
        "inkum-arka-cikisli-klozet.jpg": "https://www.alvit.com.tr/files/app/600x300/2020-06/inkum_arka_cikisli_klozetjpg_ZyQJV.jpg",
        "efekta-arka-cikisli-klozet.jpg": "https://www.alvit.com.tr/files/app/600x300/2020-06/efekta_arka_cikisli_klozetjpg_gZd3C.jpg",
        "efekta-alt-cikisli-klozet.jpg": "https://www.alvit.com.tr/files/app/600x300/2020-06/efekta_alt_cikisli_klozetjpg_f8nAX.jpg",
        # Engelli Seri
        "engelli-klozet.jpg": "https://www.alvit.com.tr/files/app/600x300/2020-06/engelli_klozetjpg_JvCw7.jpg",
        "engelli-lavabo-50x60.jpg": "https://www.alvit.com.tr/files/app/2020-06/50x60jpg_U4Vkm.jpg",
        # Cocuk Seri
        "cocuk-klozet.jpg": "https://www.alvit.com.tr/files/app/600x300/2020-06/cocuk_klozetjpg_2hLvk.jpg",
        "cocuk-lavabo-35x45.jpg": "https://www.alvit.com.tr/files/app/600x300/2020-06/cocuk_lavabo_35_x_45_cmjpg_yjVgE.jpg",
        # Lavabo Ayaklari (pedestal) serisi
        "efes-lavabo-50x60.jpg": "https://www.alvit.com.tr/files/app/600x300/2020-06/efes_lavabo_50x60jpg_PkzrG.jpg",
        "efes-lavabo-45x55.jpg": "https://www.alvit.com.tr/files/app/2020-06/45x55jpg_ntoHW.jpg",
        "efes-lavabo-35x45.jpg": "https://www.alvit.com.tr/files/app/2020-06/35x45jpg_f9JiD.jpg",
        "safran-lavabo-50x60.jpg": "https://www.alvit.com.tr/files/app/2020-06/50x60jpg_7JMfi.jpg",
        "safran-lavabo-45x55.jpg": "https://www.alvit.com.tr/files/app/2020-06/45x55jpg_9tBBr.jpg",
        "efekta-lavabo-45x55.jpg": "https://www.alvit.com.tr/files/app/2020-06/45x55jpg_rc942.jpg",
    },
    # === HAKKIMIZDA / URETIM GORSELLERI ===
    "about": {
        "fabrika-uretim.jpg": "https://www.alvit.com.tr/files/app/2021-10/hakkimizdajpg_S7biK.jpg",
    },
    # === KATEGORI GORSELLERI ===
    "categories": {
        "seramik-banyo.jpg": "https://www.alvit.com.tr/files/app/2021-11/2jpg_wA4SH.jpg",
        "banyo-mobilyalari.jpg": "https://www.alvit.com.tr/files/app/2021-11/8jpg_wBVub.jpg",
        "klozet-kapaklari.jpg": "https://www.alvit.com.tr/files/app/2021-11/6jpg_hdyIH.jpg",
        "tamamlayici-urunler.jpg": "https://www.alvit.com.tr/files/app/2020-07/suvara_sifirjpg_ZEsgU.jpg",
        "gomme-rezervuar.jpg": "https://www.alvit.com.tr/files/app/2020-07/kategori_lavabo_ayak_serijpg_n5OiF.jpg",
    },
    # === SLIDER GORSELLERI ===
    "sliders": {
        "slider1.jpg": "https://www.alvit.com.tr/files/app/2021-10/470afe129eab5b074ad9796fe2d26f6a.jpg",
        "slider2.jpg": "https://www.alvit.com.tr/files/app/2020-07/kategorijpg_jJdol.jpg",
        "slider3.jpg": "https://www.alvit.com.tr/files/app/2020-07/11jpg_Lo3qn.jpg",
    },
    # === ALT KATEGORI GORSELLERI ===
    "subcategories": {
        "asma-klozetler.jpg": "https://www.alvit.com.tr/files/app/2020-06/astra_asmajpg_uPpWn.jpg",
        "duvara-sifir.jpg": "https://www.alvit.com.tr/files/app/2020-06/ds_klozetjpg_Q9q4s.jpg",
        "takim-klozetler.jpg": "https://www.alvit.com.tr/files/app/2020-06/alt_cikislijpg_ZIxjc.jpg",
        "engelli-seri.jpg": "https://www.alvit.com.tr/files/app/2020-06/engelli_klozetjpg_MY3fK.jpg",
        "cocuk-seri.jpg": "https://www.alvit.com.tr/files/app/2020-06/cocuk_klozettjpg_p7V8j.jpg",
        "lavabo-ayaklari.jpg": "https://www.alvit.com.tr/files/app/2020-06/50x60jpg_IzjKI.jpg",
    },
    # === KALITE BELGELERI ===
    "certificates": {
        "bide-tse.jpg": "https://www.alvit.com.tr/files/app/2023-03/ts_en_14528jpg_Btk2f.jpg",
        "ce-lavabo.jpg": "https://www.alvit.com.tr/files/app/2022-06/alvit_dop_washbasins_051020_enjpg_cSJSh.jpg",
        "ce-klozet.jpg": "https://www.alvit.com.tr/files/app/2022-06/alvit_dop_wc_051020_enjpg_FpIWg.jpg",
        "isg-yonetim-sistemi.jpg": "https://www.alvit.com.tr/files/app/2022-03/kalite_1jpg_ccxKR.jpg",
        "isg-yonetim-sistemi-ek.jpg": "https://www.alvit.com.tr/files/app/2022-03/kalite_2jpg_XB6dj.jpg",
        "iso-9001-1.jpg": "https://www.alvit.com.tr/files/app/2022-03/kalite_7jpg_N1loo.jpg",
        "iso-9001-2.jpg": "https://www.alvit.com.tr/files/app/2022-03/kalite_8jpg_Co1UA.jpg",
        "lavabo-tse.jpg": "https://www.alvit.com.tr/files/app/2023-03/ts_en_14688jpg_Eu6B4.jpg",
        "pisuvar-tse.jpg": "https://www.alvit.com.tr/files/app/2023-03/ts_en_13407jpg_700Tb.jpg",
        "klozet-tse.jpg": "https://www.alvit.com.tr/files/app/2023-03/ts_en_997jpg_sozZG.jpg",
        "helatasi-tse.jpg": "https://www.alvit.com.tr/files/app/2023-03/ts_799jpg_ukfX7.jpg",
        "antibakteriyel-tse.jpg": "https://www.alvit.com.tr/files/app/2023-01/antibakteriyeljpg_jDDO6.jpg",
    }
}

if __name__ == '__main__':
    import urllib.request
    import os
    import ssl
    import time

    ctx = ssl.create_default_context()
    ctx.check_hostname = False
    ctx.verify_mode = ssl.CERT_NONE

    base = os.path.dirname(os.path.abspath(__file__))
    total = sum(len(v) for v in IMAGE_MAP.values())
    downloaded = 0
    errors = 0

    print(f"Alvit Gorsel Indirici - Toplam {total} gorsel\n")

    for folder, images in IMAGE_MAP.items():
        target_dir = os.path.join(base, 'assets', 'images', folder)
        os.makedirs(target_dir, exist_ok=True)
        print(f"\n[{folder.upper()}] - {len(images)} gorsel")
        print("-" * 40)

        for filename, url in images.items():
            filepath = os.path.join(target_dir, filename)
            if os.path.exists(filepath) and os.path.getsize(filepath) > 1000:
                print(f"  ✓ MEVCUT: {filename}")
                downloaded += 1
                continue
            try:
                req = urllib.request.Request(url, headers={
                    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Referer': 'https://www.alvit.com.tr/',
                    'Accept': 'image/webp,image/apng,image/*,*/*;q=0.8',
                })
                with urllib.request.urlopen(req, context=ctx, timeout=30) as response:
                    data = response.read()
                    if len(data) > 500:
                        with open(filepath, 'wb') as f:
                            f.write(data)
                        size_kb = len(data) / 1024
                        print(f"  ✓ OK: {filename} ({size_kb:.0f} KB)")
                        downloaded += 1
                    else:
                        print(f"  ✗ BOŞ: {filename} - dosya cok kucuk")
                        errors += 1
                time.sleep(0.3)  # Sunucuyu yormamak icin kisa bekleme
            except Exception as e:
                print(f"  ✗ HATA: {filename} - {e}")
                errors += 1

    print(f"\n{'=' * 40}")
    print(f"Sonuc: {downloaded}/{total} basarili, {errors} hata")
    if errors > 0:
        print(f"\nHatali gorseller icin alvit.com.tr sitesinden")
        print(f"manuel olarak indirip ilgili klasore koyabilirsiniz.")
    print(f"\nTamamlandi!")

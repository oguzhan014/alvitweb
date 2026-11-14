// ============================================================
// ALVIT - Frontend JavaScript
// ============================================================

document.addEventListener('DOMContentLoaded', () => {

  // ---------- HEADER SCROLL ----------
  const header = document.querySelector('.header');
  if (header) {
    window.addEventListener('scroll', () => {
      header.classList.toggle('scrolled', window.scrollY > 50);
    });
  }

  // ---------- MOBILE MENU ----------
  const menuToggle = document.querySelector('.menu-toggle');
  const navMenu = document.querySelector('.nav-menu');
  if (menuToggle && navMenu) {
    menuToggle.addEventListener('click', () => {
      navMenu.classList.toggle('open');
      document.body.style.overflow = navMenu.classList.contains('open') ? 'hidden' : '';
    });
    // close on link click
    navMenu.querySelectorAll('a').forEach(link => {
      link.addEventListener('click', () => {
        navMenu.classList.remove('open');
        document.body.style.overflow = '';
      });
    });
  }

  // ---------- MOBILE ACCORDION (Ürünler / Kurumsal) ----------
  document.querySelectorAll('.nav-expand').forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const item = btn.closest('.nav-item');
      const wasOpen = item.classList.contains('open');
      document.querySelectorAll('.nav-item.open').forEach(el => el.classList.remove('open'));
      if (!wasOpen) item.classList.add('open');
    });
  });

  // ---------- HERO SLIDER ----------
  const slides = document.querySelectorAll('.hero-slide');
  const dots = document.querySelectorAll('.hero-dot');
  let currentSlide = 0;
  let slideInterval;

  function showSlide(index) {
    slides.forEach(s => s.classList.remove('active'));
    dots.forEach(d => d.classList.remove('active'));
    currentSlide = index;
    if (currentSlide >= slides.length) currentSlide = 0;
    if (currentSlide < 0) currentSlide = slides.length - 1;
    slides[currentSlide]?.classList.add('active');
    dots[currentSlide]?.classList.add('active');
  }

  function startSlider() {
    if (slides.length <= 1) return;
    slideInterval = setInterval(() => {
      showSlide(currentSlide + 1);
    }, 5000);
  }

  if (slides.length > 0) {
    showSlide(0);
    startSlider();
    dots.forEach((dot, i) => {
      dot.addEventListener('click', () => {
        clearInterval(slideInterval);
        showSlide(i);
        startSlider();
      });
    });
  }

  // ---------- SCROLL REVEAL ----------
  const revealElements = document.querySelectorAll('.reveal');
  const revealOnScroll = () => {
    revealElements.forEach(el => {
      const top = el.getBoundingClientRect().top;
      if (top < window.innerHeight - 80) {
        el.classList.add('revealed');
      }
    });
  };
  window.addEventListener('scroll', revealOnScroll);
  revealOnScroll();

  // ---------- STAT COUNTERS ----------
  const statValues = document.querySelectorAll('.trust-value[data-count]');
  let countersStarted = false;
  function animateCounters() {
    if (countersStarted || !statValues.length) return;
    const section = document.querySelector('.trust-bar');
    if (!section || section.getBoundingClientRect().top > window.innerHeight - 100) return;
    countersStarted = true;
    statValues.forEach(el => {
      const target = parseInt(el.dataset.count, 10) || 0;
      const suffix = el.dataset.suffix || '';
      const duration = 1400;
      const start = performance.now();
      function tick(now) {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.round(target * eased) + suffix;
        if (progress < 1) requestAnimationFrame(tick);
      }
      requestAnimationFrame(tick);
    });
  }
  if (statValues.length) {
    window.addEventListener('scroll', animateCounters);
    animateCounters();
  }

  // ---------- FEATURED CAROUSEL ----------
  document.querySelectorAll('.carousel-wrap').forEach(wrap => {
    const track = wrap.querySelector('.carousel-track');
    const prevBtn = wrap.querySelector('.carousel-prev');
    const nextBtn = wrap.querySelector('.carousel-next');
    if (!track) return;
    const step = () => (track.querySelector('.carousel-item')?.offsetWidth || 270) + 28;
    prevBtn?.addEventListener('click', () => track.scrollBy({ left: -step(), behavior: 'smooth' }));
    nextBtn?.addEventListener('click', () => track.scrollBy({ left: step(), behavior: 'smooth' }));
  });

  // ---------- KATALOG FILTRELEME ----------
  const catalogGrid = document.getElementById('catalogGrid');
  if (catalogGrid) {
    const panel = document.getElementById('catalogFilters');
    const items = Array.from(catalogGrid.querySelectorAll('.catalog-item'));
    const countEl = document.getElementById('catalogCount');
    const emptyEl = document.getElementById('catalogEmpty');
    const chipRow = document.getElementById('activeChips');
    const searchEl = document.getElementById('catalogSearch');
    const sortEl = document.getElementById('catalogSort');
    const boxes = Array.from(panel.querySelectorAll('input[type="checkbox"]'));
    const originalOrder = items.slice();

    const normalize = (s) => s.toLowerCase()
      .replace(/ç/g, 'c').replace(/ğ/g, 'g').replace(/ı/g, 'i')
      .replace(/ö/g, 'o').replace(/ş/g, 's').replace(/ü/g, 'u');

    const checkedValues = (name) => boxes
      .filter(b => b.dataset.filter === name && b.checked)
      .map(b => b.value);

    function matches(el, f) {
      if (f.cat.length && !f.cat.includes(el.dataset.cat)) return false;
      if (f.sub.length && !f.sub.includes(el.dataset.sub)) return false;
      if (f.docs && el.dataset.docs !== '1') return false;
      if (f.specs && el.dataset.specs !== '1') return false;
      if (f.color.length) {
        const own = (el.dataset.colors || '').split('|').filter(Boolean);
        if (!f.color.some(c => own.includes(c))) return false;
      }
      if (f.q && !el.dataset.search.includes(f.q)) return false;
      return true;
    }

    function renderChips(f) {
      chipRow.textContent = '';
      const active = boxes.filter(b => b.checked);
      if (!active.length && !f.q) {
        chipRow.hidden = true;
        return;
      }
      chipRow.hidden = false;
      active.forEach(box => {
        const chip = document.createElement('button');
        chip.type = 'button';
        chip.className = 'chip';
        const label = document.createElement('b');
        label.textContent = box.parentElement.querySelector('span').textContent;
        const x = document.createElement('span');
        x.className = 'chip-x';
        x.textContent = '×';
        chip.append(label, x);
        chip.addEventListener('click', () => { box.checked = false; apply(); });
        chipRow.appendChild(chip);
      });
    }

    function apply() {
      const f = {
        cat: checkedValues('cat'),
        sub: checkedValues('sub'),
        color: checkedValues('color'),
        docs: boxes.some(b => b.dataset.filter === 'docs' && b.checked),
        specs: boxes.some(b => b.dataset.filter === 'specs' && b.checked),
        q: normalize((searchEl.value || '').trim()),
      };

      let visible = 0;
      items.forEach(el => {
        const ok = matches(el, f);
        el.hidden = !ok;
        if (ok) visible++;
      });

      countEl.textContent = visible;
      emptyEl.hidden = visible > 0;
      renderChips(f);
    }

    function applySort() {
      const mode = sortEl.value;
      const list = mode === 'default' ? originalOrder : originalOrder.slice().sort((a, b) => {
        const cmp = a.dataset.name.localeCompare(b.dataset.name, 'tr');
        return mode === 'za' ? -cmp : cmp;
      });
      list.forEach(el => catalogGrid.appendChild(el));
    }

    boxes.forEach(b => b.addEventListener('change', apply));
    sortEl.addEventListener('change', applySort);

    let searchTimer;
    searchEl.addEventListener('input', () => {
      clearTimeout(searchTimer);
      searchTimer = setTimeout(apply, 200);
    });

    document.getElementById('filterClear').addEventListener('click', () => {
      boxes.forEach(b => { b.checked = false; });
      searchEl.value = '';
      apply();
    });

    // Adres çubuğundaki ön seçim (/urunler?kategori=banyo-mobilyalari)
    const pre = panel.dataset.preselect;
    if (pre) {
      const box = boxes.find(b => b.dataset.slug === pre);
      if (box) box.checked = true;
    }
    apply();

    // Mobil çekmece
    const toggle = document.getElementById('filterToggle');
    const backdrop = document.getElementById('filterBackdrop');
    const closeBtn = document.getElementById('filterClose');
    const setDrawer = (open) => {
      panel.classList.toggle('open', open);
      backdrop.hidden = !open;
      document.body.style.overflow = open ? 'hidden' : '';
    };
    toggle.addEventListener('click', () => setDrawer(true));
    closeBtn.addEventListener('click', () => setDrawer(false));
    backdrop.addEventListener('click', () => setDrawer(false));
  }

  // ---------- SMOOTH SCROLL ----------
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener('click', (e) => {
      const target = document.querySelector(link.getAttribute('href'));
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });

  // ---------- CANLI ARAMA ----------
  document.querySelectorAll('.header-search').forEach(form => {
    const input = form.querySelector('input[name="q"]');
    const box = form.querySelector('.search-suggestions');
    if (!input || !box) return;

    let timer = null;

    function closeBox() {
      box.classList.remove('open');
    }

    function renderResults(items, query) {
      box.innerHTML = '';
      if (!items.length) {
        const empty = document.createElement('div');
        empty.className = 's-empty';
        empty.textContent = `"${query}" için sonuç bulunamadı`;
        box.appendChild(empty);
        box.classList.add('open');
        return;
      }
      items.forEach(item => {
        const a = document.createElement('a');
        a.href = item.url;

        const img = document.createElement('img');
        img.src = item.image;
        img.alt = '';
        img.loading = 'lazy';

        const info = document.createElement('div');
        const name = document.createElement('div');
        name.className = 's-name';
        name.textContent = item.name;
        info.appendChild(name);
        if (item.code) {
          const code = document.createElement('div');
          code.className = 's-code';
          code.textContent = 'Kod: ' + item.code;
          info.appendChild(code);
        }

        a.appendChild(img);
        a.appendChild(info);
        box.appendChild(a);
      });

      const more = document.createElement('div');
      more.className = 's-more';
      more.textContent = 'Tüm sonuçlar için Enter\'a basın';
      box.appendChild(more);

      box.classList.add('open');
    }

    input.addEventListener('input', () => {
      const query = input.value.trim();
      clearTimeout(timer);
      if (query.length < 2) {
        closeBox();
        return;
      }
      timer = setTimeout(() => {
        fetch('/api/arama?q=' + encodeURIComponent(query))
          .then(res => res.json())
          .then(items => renderResults(items, query))
          .catch(() => closeBox());
      }, 250);
    });

    input.addEventListener('focus', () => {
      if (box.children.length > 0 && input.value.trim().length >= 2) {
        box.classList.add('open');
      }
    });

    document.addEventListener('click', (e) => {
      if (!form.contains(e.target)) closeBox();
    });
  });

  // ---------- IMAGE ERROR HANDLER ----------
  document.querySelectorAll('img').forEach(img => {
    img.addEventListener('error', () => {
      if (!img.dataset.fallback) {
        img.dataset.fallback = '1';
        img.src = 'assets/images/placeholder.svg';
      }
    });
  });
});

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

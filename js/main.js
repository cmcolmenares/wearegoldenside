/* ══════════════════════════════════════════
   GOLDENSIDE — MAIN.JS
   Adapted from mockup-v2.html
   ══════════════════════════════════════════ */

(function () {
  'use strict';

  // ─── Custom Cursor (desktop only, pointer: fine) ───
  const dot = document.querySelector('.cursor-dot');
  const ring = document.querySelector('.cursor-ring');

  if (dot && ring && window.matchMedia('(pointer: fine)').matches) {
    let mx = 0, my = 0, rx = 0, ry = 0;

    document.addEventListener('mousemove', (e) => {
      mx = e.clientX;
      my = e.clientY;
      dot.style.left = mx + 'px';
      dot.style.top = my + 'px';
    }, { passive: true });

    (function animateRing() {
      rx += (mx - rx) * 0.15;
      ry += (my - ry) * 0.15;
      ring.style.left = rx + 'px';
      ring.style.top = ry + 'px';
      requestAnimationFrame(animateRing);
    })();

    const hoverSelector = 'a, button, .cta, .card, .hamburger, .social-icon, .platform-btn';
    document.querySelectorAll(hoverSelector).forEach((el) => {
      el.addEventListener('mouseenter', () => ring.classList.add('hovering'));
      el.addEventListener('mouseleave', () => ring.classList.remove('hovering'));
    });
  }

  // ─── Header solid on scroll ───
  const header = document.getElementById('header');
  if (header) {
    const updateHeader = () => {
      header.classList.toggle('solid', window.scrollY > 60);
    };
    updateHeader();
    window.addEventListener('scroll', updateHeader, { passive: true });
  }

  // ─── Mobile menu toggle ───
  const hamburger = document.getElementById('hamburger');
  const mobileMenu = document.getElementById('mobile-menu');

  if (hamburger && mobileMenu) {
    const setMenu = (open) => {
      mobileMenu.classList.toggle('open', open);
      hamburger.classList.toggle('active', open);
      hamburger.setAttribute('aria-expanded', String(open));
      hamburger.setAttribute('aria-label', open ? 'Cerrar menu' : 'Abrir menu');
      mobileMenu.setAttribute('aria-hidden', String(!open));
      document.body.style.overflow = open ? 'hidden' : '';
    };

    hamburger.addEventListener('click', () => {
      const isOpen = mobileMenu.classList.contains('open');
      setMenu(!isOpen);
    });

    mobileMenu.querySelectorAll('a').forEach((link) => {
      link.addEventListener('click', () => setMenu(false));
    });

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && mobileMenu.classList.contains('open')) {
        setMenu(false);
      }
    });
  }

  // ─── Parallax (desktop only) ───
  const isDesktop = window.matchMedia('(pointer: fine) and (min-width: 769px)');

  // About parallax (reutilizable para ambas secciones)
  ['about', 'about-2'].forEach((id) => {
    const section = document.getElementById(id);
    const bg = section && section.querySelector('.about__bg');
    if (bg && isDesktop.matches) {
      const update = () => {
        const offset = section.getBoundingClientRect().top;
        bg.style.transform = 'translateY(' + (-offset * 0.3) + 'px)';
      };
      update();
      window.addEventListener('scroll', update, { passive: true });
    }
  });

  // ─── IntersectionObserver Reveal ───
  const revealEls = document.querySelectorAll('.reveal, .red-tape--animated');

  if ('IntersectionObserver' in window && revealEls.length) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.15,
      rootMargin: '0px 0px -40px 0px'
    });

    revealEls.forEach((el) => observer.observe(el));
  } else {
    // Fallback: show everything immediately
    revealEls.forEach((el) => el.classList.add('visible'));
  }

  // ─── GIF video: play once on entry, freeze on last frame ───
  const gifSection = document.querySelector('.gif-section');
  const gifVideo = document.querySelector('.gif-section__video');

  if (gifSection && gifVideo) {
    gifVideo.addEventListener('ended', () => {
      gifVideo.currentTime = gifVideo.duration;
      gifVideo.pause();
    });
    const gifObserver = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting) {
        gifVideo.currentTime = 0;
        gifVideo.play().catch(() => {});
        gifObserver.disconnect();
      }
    }, { threshold: 0.2 });
    gifObserver.observe(gifSection);
  }

  // ─── Infinite carousel (CSS-animated, single clone set) ───
  const carouselInner = document.querySelector('.carousel-inner');

  if (carouselInner) {
    const originalCards = [...carouselInner.querySelectorAll('.card')];
    const clones = originalCards.map(c => {
      const cl = c.cloneNode(true);
      cl.setAttribute('aria-hidden', 'true');
      cl.querySelectorAll('a').forEach(a => a.tabIndex = -1);
      return cl;
    });
    clones.forEach(c => carouselInner.appendChild(c));
  }

  // ─── Contact form async submit (Formspree) ───
  const contactForm = document.getElementById('contacto-form');
  const cfStatus = document.getElementById('cf-status');

  if (contactForm && cfStatus) {
    contactForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      const submitBtn = contactForm.querySelector('.contacto__submit');
      const data = new FormData(contactForm);

      submitBtn.disabled = true;
      submitBtn.textContent = 'Enviando...';
      cfStatus.textContent = '';
      cfStatus.className = 'contacto__status';

      try {
        const res = await fetch(contactForm.action, {
          method: 'POST',
          body: data,
          headers: { Accept: 'application/json' }
        });

        if (res.ok) {
          cfStatus.textContent = 'Mensaje enviado. Nos ponemos en contacto pronto.';
          cfStatus.classList.add('contacto__status--ok');
          contactForm.reset();
        } else {
          throw new Error('server');
        }
      } catch {
        cfStatus.textContent = 'Algo salio mal. Escribenos directamente a wearegoldenside@gmail.com';
        cfStatus.classList.add('contacto__status--err');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Enviar mensaje <svg width="14" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true"><path d="M1 5h12M8 1l4 4-4 4" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
      }
    });
  }

  // ─── Cookie banner ───
  const cookieBanner = document.getElementById('cookie-banner');
  const cookieAccept = document.getElementById('cookie-accept');
  const cookieReject = document.getElementById('cookie-reject');

  if (cookieBanner) {
    const consent = localStorage.getItem('gs_cookie_consent');
    if (!consent) cookieBanner.classList.add('visible');

    const dismissBanner = (value) => {
      localStorage.setItem('gs_cookie_consent', value);
      cookieBanner.classList.remove('visible');
    };

    cookieAccept?.addEventListener('click', () => dismissBanner('accepted'));
    cookieReject?.addEventListener('click', () => dismissBanner('rejected'));
  }
})();

/**
 * Bigtricks Theme — Main JavaScript
 * Handles: Lucide icons, carousel, view toggle, mobile menu,
 *          copy code, AJAX load-more, dark mode,
 *          bell notification drawer, social copy link.
 */

(function () {
  'use strict';

  /* ── 1. Initialize Lucide Icons ──────────────────────── */
  function initLucide() {
    if (typeof lucide !== 'undefined') {
      lucide.createIcons();
    }
  }

  /* ── 2. Auto-carousel ────────────────────────────────── */
  function initCarousel() {
    const carousel = document.querySelector('.bt-carousel');
    if (!carousel) return;

    const slides = carousel.querySelectorAll('.bt-carousel-slide');
    const dots   = carousel.querySelectorAll('.bt-carousel-dot');
    const total  = slides.length;
    if (total < 2) return;

    let current   = 0;
    let autoTimer = null;

    function goTo(index) {
      slides[current].classList.replace('opacity-100', 'opacity-0');
      slides[current].classList.replace('z-10', 'z-0');
      slides[current].setAttribute('aria-hidden', 'true');
      if (dots[current]) {
        dots[current].classList.remove('bg-white', 'w-6');
        dots[current].classList.add('bg-white/40', 'w-2');
      }

      current = (index + total) % total;

      slides[current].classList.replace('opacity-0', 'opacity-100');
      slides[current].classList.replace('z-0', 'z-10');
      slides[current].setAttribute('aria-hidden', 'false');
      if (dots[current]) {
        dots[current].classList.remove('bg-white/40', 'w-2');
        dots[current].classList.add('bg-white', 'w-6');
      }
    }

    function startAuto() {
      stopAuto();
      autoTimer = setInterval(function () { goTo(current + 1); }, 5000);
    }

    function stopAuto() {
      if (autoTimer) { clearInterval(autoTimer); autoTimer = null; }
    }

    carousel.querySelector('.bt-carousel-next')?.addEventListener('click', function () {
      goTo(current + 1); stopAuto(); startAuto();
    });

    carousel.querySelector('.bt-carousel-prev')?.addEventListener('click', function () {
      goTo(current - 1); stopAuto(); startAuto();
    });

    carousel.querySelectorAll('.bt-carousel-dot').forEach(function (dot) {
      dot.addEventListener('click', function () {
        const idx = parseInt(dot.dataset.index, 10);
        if (!isNaN(idx)) { goTo(idx); stopAuto(); startAuto(); }
      });
    });

    startAuto();
  }

  /* ── 3. List / Grid View Toggle ─────────────────────── */
  function initViewToggle() {
    const container  = document.getElementById('bt-feed-container');
    const toggleBtns = document.querySelectorAll('.bt-view-toggle');
    if (!container || !toggleBtns.length) return;

    const saved = localStorage.getItem('bt_view_mode');
    applyView(saved === 'grid' ? 'grid' : 'list');

    function applyView(mode) {
      container.dataset.view = mode;
      container.classList.toggle('space-y-6', mode === 'list');

      toggleBtns.forEach(function (btn) {
        const isActive = btn.dataset.view === mode;
        btn.classList.toggle('bg-indigo-50',    isActive);
        btn.classList.toggle('text-indigo-600', isActive);
        btn.classList.toggle('text-slate-400',  !isActive);
        btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
      });

      localStorage.setItem('bt_view_mode', mode);
    }

    toggleBtns.forEach(function (btn) {
      btn.addEventListener('click', function () { applyView(btn.dataset.view); });
    });
  }

  /* ── 4. Mobile Menu ──────────────────────────────────── */
  function initMobileMenu() {
    const toggleBtn = document.getElementById('bt-mobile-menu-toggle');
    const menu      = document.getElementById('bt-mobile-menu');
    if (!toggleBtn || !menu) return;

    let isOpen = false;

    function setOpen(open) {
      isOpen = open;
      if (open) {
        menu.classList.add('open');
        menu.removeAttribute('hidden');
        toggleBtn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
      } else {
        menu.classList.remove('open');
        menu.setAttribute('hidden', '');
        toggleBtn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
      }
    }

    toggleBtn.addEventListener('click', function () { setOpen(!isOpen); });

    document.addEventListener('click', function (e) {
      if (isOpen && !menu.contains(e.target) && !toggleBtn.contains(e.target)) {
        setOpen(false);
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && isOpen) setOpen(false);
    });
  }

  /* ── 6. Copy Code Buttons ────────────────────────────── */
  function initCopyCode() {
    function copyText(text) {
      if (navigator.clipboard) {
        return navigator.clipboard.writeText(text);
      }
      const ta = document.createElement('textarea');
      ta.value = text;
      ta.style.cssText = 'position:fixed;opacity:0;pointer-events:none;';
      document.body.appendChild(ta);
      ta.focus();
      ta.select();
      document.execCommand('copy');
      document.body.removeChild(ta);
      return Promise.resolve();
    }

    function flashCopied(btn, originalHTML) {
      btn.innerHTML = '<i data-lucide="check" class="w-4 h-4 shrink-0"></i> Copied!';
      btn.classList.add('copied');
      if (typeof lucide !== 'undefined') lucide.createIcons({ nodes: [btn] });
      setTimeout(function () {
        btn.innerHTML = originalHTML;
        btn.classList.remove('copied');
        if (typeof lucide !== 'undefined') lucide.createIcons({ nodes: [btn] });
      }, 2500);
    }

    document.addEventListener('click', function (e) {
      const copyCodeBtn = e.target.closest('.bt-copy-code');
      if (copyCodeBtn) {
        const code = copyCodeBtn.dataset.code;
        if (code) {
          const orig = copyCodeBtn.innerHTML;
          copyText(code).then(function () { flashCopied(copyCodeBtn, orig); });
        }
        return;
      }

      const shareBtn = e.target.closest('.bt-share-copy');
      if (shareBtn) {
        const url = shareBtn.dataset.url || window.location.href;
        const orig = shareBtn.innerHTML;
        copyText(url).then(function () { flashCopied(shareBtn, orig); });
      }
    });
  }

  /* ── 7. AJAX Load More ───────────────────────────────── */
  function initLoadMore() {
    const btn = document.getElementById('bt-load-more');
    if (!btn) return;

    const container = document.getElementById('bt-feed-container');
    const wrap      = document.getElementById('bt-load-more-wrap');
    if (!container) return;

    let isLoading = false;

    btn.addEventListener('click', function () {
      if (isLoading) return;

      const currentPage = parseInt(btn.dataset.page, 10) || 1;
      const maxPages    = parseInt(btn.dataset.maxPages, 10) || 1;
      const nextPage    = currentPage + 1;

      if (nextPage > maxPages) {
        btn.classList.add('no-more');
        btn.innerHTML = '<i data-lucide="check-circle" class="w-5 h-5"></i> All caught up!';
        if (typeof lucide !== 'undefined') lucide.createIcons({ nodes: [btn] });
        return;
      }

      isLoading = true;
      btn.classList.add('loading');
      const origHTML = btn.innerHTML;
      btn.innerHTML = '<i data-lucide="loader-2" class="w-5 h-5 bt-load-icon animate-spin"></i> Loading…';
      if (typeof lucide !== 'undefined') lucide.createIcons({ nodes: [btn] });

      const body = new URLSearchParams({
        action: 'bigtricks_load_more',
        nonce:  btn.dataset.nonce || (bigtricksData.loadMoreNonce || ''),
        page:   nextPage,
        cat:    btn.dataset.cat  || '0',
        type:   btn.dataset.type || 'all',
      });

      fetch(bigtricksData.ajaxUrl, {
        method:  'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body:    body.toString(),
      })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          isLoading = false;
          btn.classList.remove('loading');

          if (!data.success) {
            btn.innerHTML = origHTML;
            return;
          }

          // Inject new posts
          const tmp = document.createElement('div');
          tmp.innerHTML = data.data.html;

          // Maintain view mode classes
          const viewMode = container.dataset.view || 'list';
          tmp.querySelectorAll('.bt-deal-card').forEach(function (card) {
            container.appendChild(card);
          });
          // Re-trigger view mode to apply grid styles if needed
          if (viewMode === 'grid') {
            container.classList.remove('space-y-6');
          }

          // Re-init Lucide for new elements
          if (typeof lucide !== 'undefined') lucide.createIcons();
          document.dispatchEvent(new CustomEvent('bigtricks:contentLoaded'));

          btn.dataset.page = nextPage;

          if (!data.data.has_more || nextPage >= maxPages) {
            wrap.innerHTML = '<p class="text-center text-slate-400 text-sm font-bold py-4">✓ You\'ve seen all the deals!</p>';
          } else {
            btn.innerHTML = origHTML;
          }
        })
        .catch(function () {
          isLoading = false;
          btn.classList.remove('loading');
          btn.innerHTML = origHTML;
        });
    });
  }

  /* ── 8. Dark Mode Toggle ─────────────────────────────── */
  function initDarkMode() {
    const toggle = document.getElementById('bt-dark-toggle');
    if (!toggle) return;

    const PREF_KEY  = 'bt_dark_mode';
    const html      = document.documentElement;
    const isDark    = () => html.classList.contains('dark');

    function applyDark(dark) {
      html.classList.toggle('dark', dark);
      localStorage.setItem(PREF_KEY, dark ? '1' : '0');
    }

    // Load saved pref or system preference
    const saved = localStorage.getItem(PREF_KEY);
    if (saved !== null) {
      applyDark(saved === '1');
    } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      applyDark(true);
    }

    toggle.addEventListener('click', function () {
      applyDark(!isDark());
    });

    // Listen for OS-level change if no saved pref
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
      if (localStorage.getItem(PREF_KEY) === null) {
        applyDark(e.matches);
      }
    });
  }

  /* ── 9. Bell / Notification Drawer ──────────────────── */
  function initNotificationDrawer() {
    const bellBtn  = document.getElementById('bt-bell-toggle');
    const drawer   = document.getElementById('bt-notification-drawer');
    const closeBtn = document.getElementById('bt-notif-close');
    const listEl   = document.getElementById('bt-notif-list');

    if (!bellBtn || !drawer) return;

    let isOpen    = false;
    let populated = false;

    const badgeColorMap = {
      red:    'bg-red-100 text-red-700',
      emerald:'bg-emerald-100 text-emerald-700',
      purple: 'bg-purple-100 text-purple-700',
      blue:   'bg-blue-100 text-blue-700',
      orange: 'bg-orange-100 text-orange-700',
    };

    function populateDrawer() {
      if (populated || !listEl) return;
      populated = true;

      const items = (typeof bigtricksData !== 'undefined' && bigtricksData.carouselData)
        ? bigtricksData.carouselData : [];

      if (!items.length) {
        listEl.innerHTML = '<p class="p-6 text-center text-sm text-slate-400">No notifications.</p>';
        return;
      }

      listEl.innerHTML = items.map(function (item) {
        const badgeCls = badgeColorMap[item.badge_color] || 'bg-slate-100 text-slate-600';
        const imgPart  = item.image
          ? '<img src="' + item.image + '" alt="" class="bt-notif-img">'
          : '<div class="bt-notif-img flex-shrink-0 rounded-xl bg-slate-100"></div>';

        return '<a href="' + item.link + '" class="bt-notif-item" target="_blank" rel="noopener noreferrer">'
          + imgPart
          + '<div class="flex-1 min-w-0">'
          + '<span class="text-xs font-black px-2 py-0.5 rounded-full ' + badgeCls + '">' + item.badge + '</span>'
          + '<p class="text-sm font-bold text-slate-900 dark:text-white mt-1 line-clamp-2 leading-snug">' + item.title + '</p>'
          + '<p class="text-xs text-slate-500 mt-0.5 line-clamp-1">' + item.excerpt + '</p>'
          + '</div>'
          + '</a>';
      }).join('');
    }

    function setOpen(open) {
      isOpen = open;
      if (open) {
        drawer.classList.add('open');
        drawer.removeAttribute('aria-hidden');
        bellBtn.setAttribute('aria-expanded', 'true');
        populateDrawer();
        // hide red badge
        const badge = document.getElementById('bt-notif-badge');
        if (badge) badge.style.display = 'none';
      } else {
        drawer.classList.remove('open');
        drawer.setAttribute('aria-hidden', 'true');
        bellBtn.setAttribute('aria-expanded', 'false');
      }
    }

    bellBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      setOpen(!isOpen);
    });

    if (closeBtn) {
      closeBtn.addEventListener('click', function () { setOpen(false); });
    }

    document.addEventListener('click', function (e) {
      if (isOpen && !drawer.contains(e.target) && !bellBtn.contains(e.target)) {
        setOpen(false);
      }
    });

    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && isOpen) setOpen(false);
    });
  }

  /* ── 10. Animate stats numbers on viewport entry ─────── */
  function initCountUp() {
    const stats = document.querySelectorAll('[data-countup]');
    if (!stats.length || !('IntersectionObserver' in window)) return;

    const io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        const el       = entry.target;
        const target   = parseFloat(el.dataset.countup);
        const prefix   = el.dataset.prefix || '';
        const suffix   = el.dataset.suffix || '';
        const duration = 1500;
        let start      = null;

        function step(ts) {
          if (!start) start = ts;
          const progress = Math.min((ts - start) / duration, 1);
          const eased    = 1 - Math.pow(1 - progress, 3);
          el.textContent = prefix + Math.round(eased * target).toLocaleString('en-IN') + suffix;
          if (progress < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
        io.unobserve(el);
      });
    }, { threshold: 0.5 });

    stats.forEach(function (el) { io.observe(el); });
  }

  /* ── Init ────────────────────────────────────────────── */
  document.addEventListener('DOMContentLoaded', function () {
    initDarkMode();     // Dark mode first to avoid flash
    initLucide();
    initCarousel();
    initViewToggle();
    initMobileMenu();
    initCopyCode();
    initLoadMore();
    initNotificationDrawer();
    initCountUp();

    // Re-run lucide after any AJAX-loaded content
    document.addEventListener('bigtricks:contentLoaded', initLucide);
  });

})();

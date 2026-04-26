/**
 * Bigtricks Theme — Main JavaScript
 * Handles: Lucide icons, carousel, view toggle, mobile menu,
 *          copy code, AJAX load-more, dark mode,
 *          bell notification drawer, social copy link.
 */

(function () {
  "use strict";

  /* ── 0. Shared Utilities ───────────────────────────── */
  if (!window.bigtricksUtils) {
    // Non-enumerable, non-writable container prevents accidental overwrite by third-party scripts.
    Object.defineProperty(window, "bigtricksUtils", {
      value: {},
      writable: false,
      configurable: false,
      enumerable: false,
    });
  }

  // Shared relative-time formatter for dynamic UI components.
  if (typeof window.bigtricksUtils.formatRelativeTime !== "function") {
    window.bigtricksUtils.formatRelativeTime = function (timestamp) {
      if (!timestamp) return "Just now";

      const date = new Date(Number(timestamp) * 1000);
      const now = new Date();
      const diffMs = now - date;
      const diffMins = Math.floor(diffMs / (1000 * 60));
      const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
      const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));

      if (diffMins < 1) {
        return "Just now";
      } else if (diffMins < 60) {
        return `${diffMins}m ago`;
      } else if (diffHours < 24) {
        return `${diffHours}h ago`;
      } else if (diffDays === 1) {
        return "Yesterday";
      } else if (diffDays < 7) {
        return `${diffDays}d ago`;
      }

      return date.toLocaleDateString();
    };
  }

  /* ── 1. Initialize Lucide Icons ──────────────────────── */
  function initLucide() {
    if (typeof lucide !== "undefined") {
      lucide.createIcons();
    }
  }

  /* ── 2. Auto-carousel ────────────────────────────────── */
  function initCarousel() {
    const carousel = document.querySelector(".bt-carousel");
    if (!carousel) return;

    const slides = carousel.querySelectorAll(".bt-carousel-slide");
    const dots = carousel.querySelectorAll(".bt-carousel-dot");
    const total = slides.length;
    if (total < 2) return;

    let current = 0;
    let autoTimer = null;

    function goTo(index) {
      slides[current].classList.replace("opacity-100", "opacity-0");
      slides[current].classList.replace("z-10", "z-0");
      slides[current].setAttribute("aria-hidden", "true");
      if (dots[current]) {
        dots[current].classList.remove("bg-white", "w-6");
        dots[current].classList.add("bg-white/40", "w-2");
      }

      current = (index + total) % total;

      slides[current].classList.replace("opacity-0", "opacity-100");
      slides[current].classList.replace("z-0", "z-10");
      slides[current].removeAttribute("aria-hidden");
      if (dots[current]) {
        dots[current].classList.remove("bg-white/40", "w-2");
        dots[current].classList.add("bg-white", "w-6");
      }
    }

    function startAuto() {
      stopAuto();
      autoTimer = setInterval(function () {
        goTo(current + 1);
      }, 5000);
    }

    function stopAuto() {
      if (autoTimer) {
        clearInterval(autoTimer);
        autoTimer = null;
      }
    }

    carousel
      .querySelector(".bt-carousel-next")
      ?.addEventListener("click", function () {
        goTo(current + 1);
        stopAuto();
        startAuto();
      });

    carousel
      .querySelector(".bt-carousel-prev")
      ?.addEventListener("click", function () {
        goTo(current - 1);
        stopAuto();
        startAuto();
      });

    carousel.querySelectorAll(".bt-carousel-dot").forEach(function (dot) {
      dot.addEventListener("click", function () {
        const idx = parseInt(dot.dataset.index, 10);
        if (!isNaN(idx)) {
          goTo(idx);
          stopAuto();
          startAuto();
        }
      });
    });

    startAuto();
  }

  /* ── 3. List / Grid View Toggle ─────────────────────── */
  function initViewToggle() {
    const container = document.getElementById("bt-feed-container");
    const toggleBtns = document.querySelectorAll(".bt-view-toggle");
    if (!container || !toggleBtns.length) return;

    const saved = localStorage.getItem("bt_view_mode");
    applyView(saved === "grid" ? "grid" : "list");

    function applyView(mode) {
      container.dataset.view = mode;
      container.classList.toggle("space-y-6", mode === "list");

      toggleBtns.forEach(function (btn) {
        const isActive = btn.dataset.view === mode;
        btn.classList.toggle("bg-primary-50", isActive);
        btn.classList.toggle("text-primary-600", isActive);
        btn.classList.toggle("text-slate-400", !isActive);
        btn.setAttribute("aria-pressed", isActive ? "true" : "false");
      });

      localStorage.setItem("bt_view_mode", mode);
    }

    toggleBtns.forEach(function (btn) {
      btn.addEventListener("click", function () {
        applyView(btn.dataset.view);
      });
    });
  }

  /* ── 4. Mobile Menu ──────────────────────────────────── */
  function initMobileMenu() {
    const toggleBtn = document.getElementById("bt-mobile-menu-toggle");
    const menu = document.getElementById("bt-mobile-menu");
    if (!toggleBtn || !menu) return;

    let isOpen = false;

    function setOpen(open) {
      isOpen = open;
      if (open) {
        menu.classList.add("open");
        menu.removeAttribute("hidden");
        toggleBtn.setAttribute("aria-expanded", "true");
        document.body.style.overflow = "hidden";
      } else {
        menu.classList.remove("open");
        menu.setAttribute("hidden", "");
        toggleBtn.setAttribute("aria-expanded", "false");
        document.body.style.overflow = "";
      }
    }

    toggleBtn.addEventListener("click", function () {
      setOpen(!isOpen);
    });

    document.addEventListener("click", function (e) {
      if (isOpen && !menu.contains(e.target) && !toggleBtn.contains(e.target)) {
        setOpen(false);
      }
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && isOpen) setOpen(false);
    });
  }

  /* ── 5. Mobile Submenu Accordion ────────────────────── */
  function initMobileSubmenus() {
    document.querySelectorAll(".bt-submenu-toggle").forEach(function (btn) {
      btn.addEventListener("click", function () {
        const li = btn.closest("li");
        if (!li) return;
        const submenu = li.querySelector(".bt-mobile-submenu");
        if (!submenu) return;

        const isOpen = !submenu.classList.contains("hidden");

        // Close all other open submenus at same level
        const parentList = li.parentElement;
        if (parentList) {
          parentList
            .querySelectorAll(".bt-mobile-submenu")
            .forEach(function (s) {
              if (s !== submenu) {
                s.classList.add("hidden");
                const siblingBtn = s
                  .closest("li")
                  ?.querySelector(".bt-submenu-toggle");
                if (siblingBtn) {
                  siblingBtn.setAttribute("aria-expanded", "false");
                  const chevron = siblingBtn.querySelector("[data-lucide]");
                  if (chevron) chevron.style.transform = "";
                }
              }
            });
        }

        submenu.classList.toggle("hidden", isOpen);
        btn.setAttribute("aria-expanded", String(!isOpen));
        const chevron = btn.querySelector("[data-lucide]");
        if (chevron) chevron.style.transform = isOpen ? "" : "rotate(180deg)";
      });
    });
  }

  /* ── 6. Copy Code Buttons ────────────────────────────── */
  function initCopyCode() {
    function copyText(text) {
      if (navigator.clipboard) {
        return navigator.clipboard.writeText(text);
      }
      const ta = document.createElement("textarea");
      ta.value = text;
      ta.style.cssText = "position:fixed;opacity:0;pointer-events:none;";
      document.body.appendChild(ta);
      ta.focus();
      ta.select();
      document.execCommand("copy");
      document.body.removeChild(ta);
      return Promise.resolve();
    }

    function flashCopied(btn, originalHTML) {
      btn.innerHTML =
        '<i data-lucide="check" class="w-4 h-4 shrink-0"></i> Copied!';
      btn.classList.add("copied");
      if (typeof lucide !== "undefined") lucide.createIcons({ nodes: [btn] });
      setTimeout(function () {
        btn.innerHTML = originalHTML;
        btn.classList.remove("copied");
        if (typeof lucide !== "undefined") lucide.createIcons({ nodes: [btn] });
      }, 2500);
    }

    document.addEventListener("click", function (e) {
      const copyCodeBtn = e.target.closest(".bt-copy-code");
      if (copyCodeBtn) {
        const code = copyCodeBtn.dataset.code;
        if (code) {
          const orig = copyCodeBtn.innerHTML;
          copyText(code).then(function () {
            flashCopied(copyCodeBtn, orig);
          });
        }
        return;
      }

      const shareBtn = e.target.closest(".bt-share-copy");
      if (shareBtn) {
        const url = shareBtn.dataset.url || window.location.href;
        const orig = shareBtn.innerHTML;
        copyText(url).then(function () {
          flashCopied(shareBtn, orig);
        });
      }
    });
  }

  /* ── 7. AJAX Load More ───────────────────────────────── */
  function initLoadMore() {
    const btn = document.getElementById("bt-load-more");
    if (!btn) return;

    const container = document.getElementById("bt-feed-container");
    const wrap = document.getElementById("bt-load-more-wrap");
    if (!container) return;

    let isLoading = false;

    btn.addEventListener("click", function () {
      if (isLoading) return;

      const currentPage = parseInt(btn.dataset.page, 10) || 1;
      const maxPages = parseInt(btn.dataset.maxPages, 10) || 1;
      const nextPage = currentPage + 1;

      isLoading = true;
      btn.classList.add("loading");
      const origHTML = btn.innerHTML;
      btn.innerHTML =
        '<i data-lucide="loader-2" class="w-5 h-5 bt-load-icon animate-spin"></i> Loading…';
      if (typeof lucide !== "undefined") lucide.createIcons({ nodes: [btn] });

      const body = new URLSearchParams({
        action: "bigtricks_load_more",
        nonce: btn.dataset.nonce || bigtricksData.loadMoreNonce || "",
        page: nextPage,
        cat: btn.dataset.cat || "0",
        store: btn.dataset.store || "0",
        card_cat: btn.dataset.cardCat || "0",
        type: btn.dataset.type || "all",
      });

      fetch(bigtricksData.ajaxUrl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: body.toString(),
      })
        .then(function (r) {
          return r.json();
        })
        .then(function (data) {
          isLoading = false;
          btn.classList.remove("loading");

          if (!data.success) {
            btn.innerHTML = origHTML;
            return;
          }

          // Inject new posts
          const tmp = document.createElement("div");
          tmp.innerHTML = data.data.html;

          // Maintain view mode classes
          const viewMode = container.dataset.view || "list";
          tmp.querySelectorAll(".bt-deal-card").forEach(function (card) {
            container.appendChild(card);
          });
          // Re-trigger view mode to apply grid styles if needed
          if (viewMode === "grid") {
            container.classList.remove("space-y-6");
          }

          // Re-init Lucide for only the new elements (scoped = faster)
          if (typeof lucide !== "undefined")
            lucide.createIcons({ nodes: [container] });
          document.dispatchEvent(new CustomEvent("bigtricks:contentLoaded"));

          btn.dataset.page = nextPage;

          if (!data.data.has_more || nextPage >= maxPages) {
            wrap.innerHTML =
              '<p class="text-center text-slate-400 text-sm font-bold py-4">✓ You\'ve seen all the deals!</p>';
          } else {
            btn.innerHTML = origHTML;
          }
        })
        .catch(function () {
          isLoading = false;
          btn.classList.remove("loading");
          btn.innerHTML = origHTML;
        });
    });
  }

  /* ── 8. Dark Mode Toggle ─────────────────────────────── */
  function initDarkMode() {
    const toggle = document.getElementById("bt-dark-toggle");
    if (!toggle) return;

    const PREF_KEY = "bt_dark_mode";
    const html = document.documentElement;
    const isDark = () => html.classList.contains("dark");

    function getCookiePref() {
      const match = document.cookie.match(/(?:^|; )bt_dark_mode=(0|1)(?:;|$)/);
      return match ? match[1] : null;
    }

    function setCookiePref(pref) {
      // Client-side persistence fallback when localStorage is blocked.
      document.cookie =
        "bt_dark_mode=" +
        pref +
        "; path=/; max-age=31536000; samesite=lax";
    }

    function getLocalPref() {
      try {
        return localStorage.getItem(PREF_KEY);
      } catch (e) {
        return null;
      }
    }

    function getSavedPref() {
      const local = getLocalPref();
      if (local === "0" || local === "1") return local;
      return getCookiePref();
    }

    function setLocalPref(pref) {
      try {
        localStorage.setItem(PREF_KEY, pref);
      } catch (e) {
        // Ignore storage quota/privacy mode failures.
      }
    }

    function applyDark(dark, persist) {
      const pref = dark ? "1" : "0";
      html.classList.toggle("dark", dark);

      if (!persist) return;

      setLocalPref(pref);
      setCookiePref(pref);
    }

    // Safety net: keep state correct even if the early wp_head script was blocked.
    const initialPref = getSavedPref();
    if (initialPref === "0" || initialPref === "1") {
      applyDark(initialPref === "1", false);
    }

    toggle.addEventListener("click", function () {
      applyDark(!isDark(), true);
    });

    // Listen for OS-level change only when there is no explicit user preference.
    const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");
    const handleSystemThemeChange = function (e) {
      if (getSavedPref() === null) {
        applyDark(e.matches, false);
      }
    };

    if (typeof mediaQuery.addEventListener === "function") {
      mediaQuery.addEventListener("change", handleSystemThemeChange);
    } else if (typeof mediaQuery.addListener === "function") {
      mediaQuery.addListener(handleSystemThemeChange);
    }
  }

  /* ── 9. Bell / Notification Drawer ──────────────────── */
  function initNotificationDrawer() {
    const bellBtn = document.getElementById("bt-bell-toggle");
    const drawer = document.getElementById("bt-notification-drawer");
    const closeBtn = document.getElementById("bt-notif-close");
    const listEl = document.getElementById("bt-notif-list");

    if (!bellBtn || !drawer) return;

    let isOpen = false;
    let populated = false;

    // Show red badge based on IDs cached from the last bell-open.
    // This avoids an inline DB payload — the badge is slightly trailing
    // (reflects the previous visit's fetch) which is acceptable UX.
    (function () {
      var badge = document.getElementById("bt-notif-badge");
      if (!badge) return;
      var cachedIds = [];
      try {
        cachedIds = JSON.parse(localStorage.getItem("bt_notif_ids") || "[]");
      } catch (e) {}
      if (!cachedIds.length) {
        badge.style.display = "none";
        return;
      }
      var seen = [];
      try {
        seen = JSON.parse(localStorage.getItem("bt_notif_seen") || "[]");
      } catch (e) {}
      var hasUnseen = cachedIds.some(function (id) {
        return seen.indexOf(id) === -1;
      });
      badge.style.display = hasUnseen ? "" : "none";
    })();

    const badgeColorMap = {
      red: "bg-red-100 text-red-700",
      emerald: "bg-emerald-100 text-emerald-700",
      purple: "bg-purple-100 text-purple-700",
      blue: "bg-blue-100 text-blue-700",
      orange: "bg-orange-100 text-orange-700",
    };

    function renderItems(items) {
      listEl.innerHTML = "";
      items.forEach(function (item) {
        const badgeCls =
          badgeColorMap[item.badge_color] || "bg-slate-100 text-slate-600";

        const a = document.createElement("a");
        a.href = item.link || "#";
        a.className = "bt-notif-item";
        a.target = "_blank";
        a.rel = "noopener noreferrer";

        if (item.image) {
          const img = document.createElement("img");
          img.src = item.image;
          img.alt = "";
          img.className = "bt-notif-img";
          a.appendChild(img);
        } else {
          const div = document.createElement("div");
          div.className = "bt-notif-img flex-shrink-0 rounded-xl bg-slate-100";
          a.appendChild(div);
        }

        const inner = document.createElement("div");
        inner.className = "flex-1 min-w-0";

        const badgeEl = document.createElement("span");
        badgeEl.className =
          "text-xs font-black px-2 py-0.5 rounded-full " + badgeCls;
        badgeEl.textContent = item.badge || "";
        inner.appendChild(badgeEl);

        const titleEl = document.createElement("p");
        titleEl.className =
          "text-sm font-bold text-slate-900 dark:text-white mt-1 line-clamp-2 leading-snug";
        titleEl.textContent = item.title || "";
        inner.appendChild(titleEl);

        const excerptEl = document.createElement("p");
        excerptEl.className = "text-xs text-slate-500 mt-0.5 line-clamp-1";
        excerptEl.textContent = item.excerpt || "";
        inner.appendChild(excerptEl);

        a.appendChild(inner);
        listEl.appendChild(a);
      });
    }

    function populateDrawer() {
      if (populated || !listEl) return;
      populated = true;

      // Show a loading state while fetching
      listEl.innerHTML =
        '<p class="p-6 text-center text-sm text-slate-400">Loading…</p>';

      const restUrl =
        typeof bigtricksData !== "undefined" && bigtricksData.restUrl
          ? bigtricksData.restUrl.replace(/\/$/, "") +
            "/bigtricks/v1/notifications"
          : null;

      if (!restUrl) {
        listEl.innerHTML =
          '<p class="p-6 text-center text-sm text-slate-400">No notifications.</p>';
        return;
      }

      fetch(restUrl)
        .then(function (r) {
          return r.json();
        })
        .then(function (items) {
          if (!Array.isArray(items) || !items.length) {
            listEl.innerHTML =
              '<p class="p-6 text-center text-sm text-slate-400">No notifications.</p>';
            return;
          }
          // Cache IDs so the badge can reflect unseen state on the next page load
          try {
            localStorage.setItem(
              "bt_notif_ids",
              JSON.stringify(
                items.map(function (n) {
                  return n.id;
                }),
              ),
            );
          } catch (e) {}
          renderItems(items);
        })
        .catch(function () {
          listEl.innerHTML =
            '<p class="p-6 text-center text-sm text-slate-400">Could not load notifications.</p>';
        });
    }

    function setOpen(open) {
      isOpen = open;
      if (open) {
        drawer.classList.add("open");
        drawer.removeAttribute("aria-hidden");
        bellBtn.setAttribute("aria-expanded", "true");
        populateDrawer();
        // Mark all locally-cached IDs as seen and hide badge immediately.
        // populateDrawer() will also update the cache after the REST fetch.
        const badge = document.getElementById("bt-notif-badge");
        var cachedIds = [];
        try {
          cachedIds = JSON.parse(localStorage.getItem("bt_notif_ids") || "[]");
        } catch (e) {}
        try {
          localStorage.setItem("bt_notif_seen", JSON.stringify(cachedIds));
        } catch (e) {}
        if (badge) badge.style.display = "none";
      } else {
        drawer.classList.remove("open");
        drawer.setAttribute("aria-hidden", "true");
        bellBtn.setAttribute("aria-expanded", "false");
      }
    }

    bellBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      setOpen(!isOpen);
    });

    if (closeBtn) {
      closeBtn.addEventListener("click", function () {
        setOpen(false);
      });
    }

    document.addEventListener("click", function (e) {
      if (isOpen && !drawer.contains(e.target) && !bellBtn.contains(e.target)) {
        setOpen(false);
      }
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && isOpen) setOpen(false);
    });
  }

  /* ── 10. Animate stats numbers on viewport entry ─────── */
  function initCountUp() {
    const stats = document.querySelectorAll("[data-countup]");
    if (!stats.length || !("IntersectionObserver" in window)) return;

    const io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          const el = entry.target;
          const target = parseFloat(el.dataset.countup);
          const prefix = el.dataset.prefix || "";
          const suffix = el.dataset.suffix || "";
          const duration = 1500;
          let start = null;

          function step(ts) {
            if (!start) start = ts;
            const progress = Math.min((ts - start) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            el.textContent =
              prefix +
              Math.round(eased * target).toLocaleString("en-IN") +
              suffix;
            if (progress < 1) requestAnimationFrame(step);
          }
          requestAnimationFrame(step);
          io.unobserve(el);
        });
      },
      { threshold: 0.5 },
    );

    stats.forEach(function (el) {
      io.observe(el);
    });
  }

  /* ── 11. AJAX Search (autocomplete) ─────────────────── */
  function initAjaxSearch() {
    const searchInputs = document.querySelectorAll(
      "#bt-search, #bt-mobile-search-bar, #bt-mobile-search",
    );
    if (!searchInputs.length) return;

    let searchTimeout = null;
    let currentDropdown = null;

    // Type badge color mapping
    const typeColors = {
      post: "bg-blue-100 text-blue-700",
      deal: "bg-emerald-100 text-emerald-700",
      "referral-codes": "bg-purple-100 text-purple-700",
      "credit-card": "bg-orange-100 text-orange-700",
    };

    function createDropdown(inputEl) {
      const wrapper = inputEl.closest("form");
      if (!wrapper) return null;

      // Remove existing dropdown
      const existing = wrapper.querySelector(".bt-search-dropdown");
      if (existing) existing.remove();

      const dropdown = document.createElement("div");
      dropdown.className =
        "bt-search-dropdown absolute top-full left-0 right-0 mt-2 bg-white dark:bg-slate-900 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden z-50 max-h-[500px] overflow-y-auto";
      dropdown.style.display = "none";

      wrapper.style.position = "relative";
      wrapper.appendChild(dropdown);

      return dropdown;
    }

    function showResults(inputEl, results, totalCount, query) {
      let dropdown = inputEl
        .closest("form")
        .querySelector(".bt-search-dropdown");
      if (!dropdown) {
        dropdown = createDropdown(inputEl);
      }
      if (!dropdown) return;

      if (!results.length) {
        dropdown.innerHTML =
          '<div class="p-6 text-center text-slate-400 text-sm">' +
          '<i data-lucide="search-x" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>' +
          "<p>No results found for <strong>" +
          query +
          "</strong></p>" +
          "</div>";
        dropdown.style.display = "block";
        currentDropdown = dropdown;
        if (typeof lucide !== "undefined")
          lucide.createIcons({ nodes: [dropdown] });
        return;
      }

      dropdown.innerHTML =
        '<div class="p-3 bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-500 flex items-center justify-between">' +
        "<span>" +
        results.length +
        " of " +
        totalCount +
        " results</span>" +
        '<a href="/?s=' +
        encodeURIComponent(query) +
        '" class="text-primary-600 hover:underline">View all →</a>' +
        "</div>" +
        '<div class="divide-y divide-slate-100 dark:divide-slate-800">' +
        results
          .map(function (item) {
            const typeColor =
              typeColors[item.type] || "bg-slate-100 text-slate-600";
            const thumb = item.thumbnail
              ? '<img src="' +
                item.thumbnail +
                '" alt="" class="w-16 h-16 object-cover rounded-xl shrink-0">'
              : '<div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-xl shrink-0 flex items-center justify-center"><i data-lucide="file-text" class="w-6 h-6 text-slate-400"></i></div>';

            return (
              '<a href="' +
              item.url +
              '" class="flex items-start gap-3 p-4 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors group">' +
              thumb +
              '<div class="flex-1 min-w-0">' +
              '<span class="inline-block text-xs font-bold px-2 py-0.5 rounded-full mb-1 ' +
              typeColor +
              '">' +
              item.type_label +
              "</span>" +
              '<h4 class="font-bold text-sm text-slate-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 line-clamp-2 mb-1">' +
              item.title +
              "</h4>" +
              '<p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1">' +
              item.excerpt +
              "</p>" +
              "</div>" +
              "</a>"
            );
          })
          .join("") +
        "</div>";

      dropdown.style.display = "block";
      currentDropdown = dropdown;
      if (typeof lucide !== "undefined")
        lucide.createIcons({ nodes: [dropdown] });
    }

    function hideDropdown() {
      if (currentDropdown) {
        currentDropdown.style.display = "none";
        currentDropdown = null;
      }
    }

    function performSearch(inputEl, query) {
      if (!query || query.length < 2) {
        hideDropdown();
        return;
      }

      fetch(bigtricksData.ajaxUrl, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({
          action: "bigtricks_ajax_search",
          s: query,
        }),
      })
        .then(function (r) {
          return r.json();
        })
        .then(function (data) {
          if (data.success && data.data.results) {
            showResults(
              inputEl,
              data.data.results,
              data.data.count,
              data.data.query,
            );
          } else {
            hideDropdown();
          }
        })
        .catch(function () {
          hideDropdown();
        });
    }

    // Attach event listeners to all search inputs
    searchInputs.forEach(function (input) {
      input.addEventListener("input", function (e) {
        const query = e.target.value.trim();

        clearTimeout(searchTimeout);

        if (!query || query.length < 2) {
          hideDropdown();
          return;
        }

        // Debounce: wait 300ms after user stops typing
        searchTimeout = setTimeout(function () {
          performSearch(input, query);
        }, 300);
      });

      // Hide dropdown when input loses focus (with small delay to allow clicks)
      input.addEventListener("blur", function () {
        setTimeout(hideDropdown, 200);
      });

      // Show dropdown again on focus if there's a query
      input.addEventListener("focus", function (e) {
        const query = e.target.value.trim();
        if (query && query.length >= 2 && currentDropdown) {
          currentDropdown.style.display = "block";
        }
      });
    });

    // Hide dropdown when clicking outside
    document.addEventListener("click", function (e) {
      const isSearchInput = Array.from(searchInputs).some(function (input) {
        return input.contains(e.target) || input === e.target;
      });

      if (!isSearchInput && currentDropdown) {
        const isDropdownClick = currentDropdown.contains(e.target);
        if (!isDropdownClick) {
          hideDropdown();
        }
      }
    });

    // Hide on Escape key
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && currentDropdown) {
        hideDropdown();
      }
    });
  }

  /* ── 12. Account Menu (<details>) click-outside close ─ */
  function initAccountMenu() {
    const details = document.querySelector("#bt-account-menu");
    if (!details) return;

    function handleBtAccountOutsideClick(e) {
      if (!details.contains(e.target)) {
        details.removeAttribute("open");
      }
    }

    function handleBtAccountEscKey(e) {
      if (e.key === "Escape" && details.hasAttribute("open")) {
        details.removeAttribute("open");
      }
    }

    document.addEventListener("click", handleBtAccountOutsideClick);
    document.addEventListener("keydown", handleBtAccountEscKey);
  }

  /* ── 13. Back To Top Button ─────────────────────────── */
  function initBackToTop() {
    const btn = document.getElementById("bt-back-to-top");
    if (!btn) return;

    const threshold = 420;
    let ticking = false;

    function setVisible(visible) {
      btn.classList.toggle("opacity-0", !visible);
      btn.classList.toggle("translate-y-2", !visible);
      btn.classList.toggle("pointer-events-none", !visible);
      btn.classList.toggle("opacity-100", visible);
      btn.classList.toggle("translate-y-0", visible);
      if (visible) {
        btn.removeAttribute("aria-hidden");
      } else {
        btn.setAttribute("aria-hidden", "true");
      }
    }

    function updateVisibility() {
      setVisible(window.scrollY > threshold);
      ticking = false;
    }

    window.addEventListener(
      "scroll",
      function () {
        if (!ticking) {
          window.requestAnimationFrame(updateVisibility);
          ticking = true;
        }
      },
      { passive: true },
    );

    btn.addEventListener("click", function () {
      const reduceMotion = window.matchMedia(
        "(prefers-reduced-motion: reduce)",
      ).matches;

      window.scrollTo({ top: 0, behavior: reduceMotion ? "auto" : "smooth" });
    });

    updateVisibility();
  }

  /* ── Init ────────────────────────────────────────────── */
  document.addEventListener("DOMContentLoaded", function () {
    initDarkMode(); // Dark mode first to avoid flash
    initLucide();
    initCarousel();
    initViewToggle();
    initMobileMenu();
    initMobileSubmenus();
    initCopyCode();
    initLoadMore();
    initNotificationDrawer();
    initCountUp();
    initAjaxSearch(); // AJAX search autocomplete
    initAccountMenu(); // Account <details> dropdown click-outside
    initBackToTop();

    // Google login placeholder — show informational alert until a Social Login plugin is active.
    document
      .getElementById("bt-google-login-placeholder")
      ?.addEventListener("click", function () {
        alert("Google login requires a Social Login plugin.");
      });

    // Note: load-more already calls lucide.createIcons({ nodes: [container] }) before
    // dispatching bigtricks:contentLoaded, so no full-page re-scan is needed here.
  });
})();

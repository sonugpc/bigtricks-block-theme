# Bigtricks WordPress Theme — Plan & Progress

> **Theme Root:** `/Users/sonugpc/Local Sites/tst/app/public/wp-content/bigtricks-block/`
> **PHP Binary:** `/Users/sonugpc/Library/Application Support/Local/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php`
> **WordPress Version:** 6.4+ | **PHP:** 8.0+ (declare strict_types)
> **Last validated:** All 11 PHP files pass `php -l` with zero errors.

---

## 1. Tech Stack

| Layer | Choice |
|---|---|
| Framework | Classic PHP theme (not block/FSE) |
| CSS utility | Tailwind CSS — Play CDN (`cdn.tailwindcss.com`) |
| Dark mode | Tailwind `darkMode: 'class'` |
| Icons | Lucide Icons — unpkg CDN (`lucide.min.js`) |
| Fonts | Google Fonts: **Plus Jakarta Sans** (headings) + **Inter** (body) |
| JavaScript | Vanilla JS — `assets/js/main.js` |
| Custom CSS | `assets/css/bigtricks.css` |
| Editor styles | `assets/css/editor-style.css` (Gutenberg WYSIWYG) |
| Carousel data | `carousel-config.json` (static JSON, no DB queries) |

---

## 2. File Inventory

### PHP Templates (12 files)

| File | Purpose |
|---|---|
| `functions.php` | All theme logic: CPT, meta, AJAX, walkers, helpers |
| `header.php` | HTML head, sticky header, nav, dark mode, bell drawer |
| `footer.php` | Footer columns, copyright, back-to-top |
| `front-page.php` | Homepage: JSON carousel hero + AJAX load-more feed |
| `single.php` | Single post: deal details, social share, comments |
| `sidebar.php` | Right sidebar: Download App widget + WP widget area |
| `category.php` | Category archive: description, tag-image, AJAX load-more |
| `comments.php` | Chat-style comment template (walker at top of file) |
| `archive-store.php` | Store CPT archive |
| `page.php` | Generic page template |
| `page-login.php` | Login page template |
| `index.php` | Fallback index |
| `search.php` | Search results |
| `404.php` | 404 error page |

### Assets

| Path | Purpose |
|---|---|
| `assets/css/bigtricks.css` | Theme CSS: dark mode, chat comments, bell drawer, social share, download app, category page |
| `assets/css/editor-style.css` | Gutenberg block editor styles (mirrors front-end typography) |
| `assets/js/main.js` | All frontend JS: carousel, dark mode, bell drawer, AJAX load-more, upvotes, copy code, share |
| `assets/images/placeholder.svg` | Fallback image for posts without thumbnails |

### Config / Meta

| File | Purpose |
|---|---|
| `style.css` | Theme header (name, version, description, tags) |
| `theme.json` | WP theme.json v3 (global settings, typography scale) |
| `carousel-config.json` | 5 static carousel slides (id, title, subtitle, badge, link, color, cta_text, cta_link) |
| `screenshot.png` | WP Admin theme preview image |

---

## 3. functions.php — Section Map

```
§1  after_setup_theme      — theme supports, nav menus, editor style
§2  wp_enqueue_scripts     — Google Fonts, Tailwind CDN, Lucide, bigtricks.css, main.js
    wp_localize_script      — bigtricksData { ajaxUrl, nonce, loadMoreNonce, restUrl, siteUrl, carouselData }
    wp_head (priority 5)    — Anti-FOUC dark mode script + Tailwind config (darkMode: 'class', fonts, colors)
§3  init (CPT)              — Registers 'store' Custom Post Type (slug: /store/)
§4  init (post meta)        — Registers 5 deal meta fields (see Post Meta Fields)
§5  add_meta_boxes          — Admin metabox: Deal Details (type, link, temperature, upvotes, referral code)
    save_post               — Saves deal meta with nonce + capability checks
§6  widgets_init            — Registers 'sidebar-1' (Right Sidebar)
§7  Helper functions:
    bigtricks_deal_type_badge()    — Returns badge HTML for deal|blog|referral|credit_card
    bigtricks_deal_cta_button()    — Returns CTA button HTML (type-aware, normal|large size)
    bigtricks_get_thumbnail_url()  — Featured image URL or placeholder SVG
    bigtricks_get_top_categories() — Top N categories by post count
§8  AJAX: bigtricks_upvote  — Cookie-rate-limited upvote (+1 to _deal_upvotes)
§9  after_switch_theme      — Flush rewrite rules on activation
§10 AJAX: bigtricks_load_more — Paginated post cards (page, cat, type params → JSON {html, has_more})
§11 init (term meta)        — Registers 'tag-image' term meta for 'category' taxonomy
    category_add/edit_form_fields  — Admin UI input for tag-image
    created/edited_category        — Saves tag-image with nonce + capability checks
§12 Bigtricks_Icon_Nav_Walker (class) — Extends Walker_Nav_Menu; parses [icon-name] prefix from menu title, injects <i data-lucide=""> icon
```

---

## 4. Post Meta Fields

| Meta Key | Type | Description |
|---|---|---|
| `_deal_type` | string | `deal` \| `blog` \| `referral` \| `credit_card` |
| `_deal_link` | string (URL) | Outbound affiliate / deal link |
| `_deal_temperature` | integer | Heat score (displays as `°` or 🔥 on badges) |
| `_deal_upvotes` | integer | Community upvote count |
| `_referral_code` | string | Referral / coupon code (shown on copy button) |

All fields are `show_in_rest: true` and registered via `register_post_meta()`.

---

## 5. Nav Menus

**Registered locations:**
- `primary` → Primary Navigation (header)
- `footer` → Footer Navigation

**Icon support via `Bigtricks_Icon_Nav_Walker`:**  
Prefix any menu item label with `[icon-name]` to inject a Lucide icon.

```
Examples:
  [home] Home
  [tag] Deals
  [shopping-bag] Stores
  [credit-card] Cards
  [book-open] Blog
  [gift] Referrals
```

---

## 6. Carousel (front-page hero + bell notification drawer)

**Source file:** `carousel-config.json` — 5 slide objects.

**Schema per slide:**
```json
{
  "id": 1,
  "title": "...",
  "subtitle": "...",
  "badge": "Hot Deal",
  "link": "https://...",
  "color": "from-indigo-600 to-purple-600",
  "image": "",
  "cta_text": "Grab Deal",
  "cta_link": "https://..."
}
```

**Passed to JS via:** `bigtricksData.carouselData` (PHP reads and JSON-decodes the file, passes array via `wp_localize_script`).

**Used by:**
1. `front-page.php` — hero carousel HTML (rendered server-side from JSON)
2. Bell notification drawer in `header.php` — JS renders from `bigtricksData.carouselData`

---

## 7. AJAX Endpoints

| Action | Handler | Auth | Params |
|---|---|---|---|
| `bigtricks_upvote` | `bigtricks_ajax_upvote()` | Nonce `bigtricks_nonce` | `post_id`, `nonce` |
| `bigtricks_load_more` | `bigtricks_ajax_load_more()` | Nonce `bigtricks_load_more` | `page`, `cat`, `type`, `nonce` |

Load-more returns `{ html: "...", has_more: true|false }`.

---

## 8. Dark Mode

**Toggle:** `#bt-dark-toggle` button in header (sun/moon Lucide icons).  
**Storage:** `localStorage` key `bt_dark_mode` (`"1"` = dark).  
**Anti-FOUC:** Inline script injected in `<head>` (before `wp_head`) reads localStorage and adds `dark` class to `<html>` immediately.  
**Tailwind config:** `darkMode: 'class'` set in `tailwind.config` via `wp_head` hook (priority 5).  
**CSS:** `.dark` overrides in `assets/css/bigtricks.css` cover body, cards, nav, sidebar, comments.

---

## 9. Bell Notification Drawer

- **Button:** `#bt-bell-btn` beside dark mode toggle in header
- **Drawer:** `#bt-bell-drawer` — fixed right-side panel, slides in via CSS `translateX`
- **Overlay:** `#bt-bell-overlay` — backdrop, click closes drawer
- **Data source:** `bigtricksData.carouselData` (same JSON as carousel)
- **JS:** `initBellDrawer()` in `main.js` — renders notification cards, handles open/close/Escape key

---

## 10. Category Page (`category.php`)

- Category hero with name and post count
- `tag-image` term meta → displayed as category icon/image (URL or attachment ID); falls back to initial letter
- Category description card (styled)
- Deal type filter chips (all / deal / blog / referral / credit_card)
- Deal card grid (same card component as front-page)
- AJAX load-more (`#bt-load-more` with `data-cat-id`, `data-page`, `data-type`)

---

## 11. Single Post (`single.php`)

**Features:**
- Featured image hero
- Deal type badge + category breadcrumb
- Social share bar **below title** with fake share count (`mt_rand(100, 999)`)
  - Share buttons: WhatsApp, Twitter/X, Facebook, Copy Link
  - `#bt-share-count` element with `animate-pulse`
- Post content area
- **Sidebar** (right): deal details card, referral code copy button, `bigtricks_deal_cta_button('large')`, WhatsApp share
- **Conditional CTA:** If `_deal_type === 'blog'`, the CTA block and WhatsApp share button are **hidden**
- Related posts section (same category)
- Comments via `comments_template()`

**Removed (vs original):** Sticky "Back to Updates" back bar.

---

## 12. Comments (`comments.php`)

**Important:** `Bigtricks_Chat_Comment_Walker` class is defined at the **top of the file** (before any output) to avoid "Class not found" fatal errors.

**Features:**
- Chat-bubble style layout (avatar left, bubble right)
- No website/URL field in comment form
- Comment form fields: name + email only
- Moderation notice inside bubble if pending
- Hover-reveal reply/edit action links
- Nested replies with `ml-10` indent
- `comment_text( $comment )` — comment object passed explicitly (avoids `null` warning)

---

## 13. Sidebar (`sidebar.php`)

1. **Download App widget** (hardcoded, always first):
   - Play Store link: `https://play.google.com/store/apps/details?id=in.bigtricks`
   - Dark gradient card with smartphone icon
   - Google Play badge SVG


2. **WP Dynamic Sidebar** (`sidebar-1`) — standard widget area

---

## 14. JavaScript (`assets/js/main.js`) — Function Map

| Function | Purpose |
|---|---|
| `initDarkMode()` | Dark/light toggle, localStorage persistence, sun/moon icon swap |
| `initBellDrawer()` | Bell open/close, renders carousel data as notifications, overlay + Escape |
| `initLoadMore()` | AJAX load-more; increments `data-page`, appends HTML, hides button when `has_more=false`, re-runs `lucide.createIcons()` |
| `initShareButtons()` | `.bt-share-copy` click → clipboard copy of URL → "Copied!" toast |
| `initCarousel()` | Hero carousel auto-advance, dot pagination, swipe |
| `initViewToggle()` | Grid/list view switch on feed |
| `initMobileMenu()` | Mobile hamburger menu open/close |
| `initUpvotes()` | `.bt-upvote-btn` click → AJAX upvote with optimistic UI |
| `initCopyCode()` | `.bt-copy-code` click → clipboard copy of referral code → toast |
| `initCountUp()` | Animated counter for stats (intersectionObserver) |

---

## 15. WordPress Activation Checklist

After activating the theme in **WP Admin → Appearance → Themes**:

1. **Settings → Permalinks** → click Save (flush rewrite rules for `store` CPT)
2. **Appearance → Menus** → create menu, add items with `[icon-name]` prefix, assign location **Primary Navigation**
3. **Posts → Categories** → edit any category → fill in **Category Icon / Image URL** field (`tag-image` meta)
4. **Widgets** → optional: add widgets to Right Sidebar (Download App is always rendered above them)
5. **Settings → Reading** → optionally set Front page to a static page (or use blog posts)

---

## 16. Known Bugs Fixed

| Bug | Fix |
|---|---|
| `Class "Bigtricks_Chat_Comment_Walker" not found` (Fatal) | Moved class definition to top of `comments.php` before any template output |
| `Warning: Attempt to read property "comment_content" on null` | Changed `comment_text()` → `comment_text( $comment )` to pass comment object explicitly |

---

## 17. PHP Lint Status

All files validated with `php -l` (PHP 8.2.29):

```
✓ functions.php
✓ front-page.php
✓ single.php
✓ header.php
✓ sidebar.php
✓ category.php
✓ comments.php
✓ footer.php
✓ archive-store.php
✓ page.php
✓ page-login.php
✓ index.php
✓ search.php
✓ 404.php
```

---

## 18. Theme Constants

```php
BIGTRICKS_VERSION  →  '1.0.0'
BIGTRICKS_DIR      →  get_template_directory()
BIGTRICKS_URI      →  get_template_directory_uri()
```

---

## 19. bigtricksData (JS global)

```js
bigtricksData = {
  ajaxUrl:        "/wp-admin/admin-ajax.php",
  nonce:          "...",           // bigtricks_nonce
  loadMoreNonce:  "...",           // bigtricks_load_more
  restUrl:        "https://.../wp-json/",
  siteUrl:        "https://...",
  carouselData:   [ { id, title, subtitle, badge, link, color, image, cta_text, cta_link }, ... ]
}
```

---

## 20. Future / Pending Work

- [ ] Add real images to `carousel-config.json` slides (currently `"image": ""`)
- [ ] Replace Tailwind Play CDN with a compiled production build for performance
- [ ] Add OG / Open Graph meta tags in `header.php`
- [ ] Schema.org JSON-LD markup for deal posts (Product, Offer)
- [ ] Sitemap + `robots.txt` configuration
- [ ] WooCommerce compatibility layer (if needed for affiliate redirect tracking)

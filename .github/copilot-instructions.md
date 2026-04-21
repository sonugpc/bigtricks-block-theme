# Bigtricks WordPress Theme — AI Agent Instructions

> **Project**: Bigtricks Block Theme — Deals, Credit Cards, and Referral Codes Platform  
> **Type**: Classic WordPress PHP Theme (not block/FSE)  
> **PHP**: 8.0+ with `declare(strict_types=1)`  
> **Last Updated**: April 2026

---

## Quick Start

### Essential Files

- **[PLAN.md](../PLAN.md)** — Complete theme architecture, file inventory, and section map
- **[CC.md](../CC.md)** — Credit Card Manager plugin documentation
- **[functions.php](../functions.php)** — 850+ lines, all theme logic organized by hook
- **[single-deal.php](../single-deal.php)** — 739-line deal detail template (reference implementation)
- **[carousel-config.json](../carousel-config.json)** — Static hero carousel data (no DB)

### Development Commands

```bash
# PHP: /Applications/Local.app/Contents/Resources/extraResources/lightning-services/php-8.2.29+0/bin/darwin-arm64/bin/php
# Site root: /Users/sonugpc/Local Sites/tst/app/public

# --- Tailwind CSS ---
npm run dev              # Watch + rebuild CSS during development
npm run build:css        # Minified production CSS

# --- Lucide Icons ---
npm run build:icons      # Regenerate slim icon bundle (assets/js/lucide-custom.js)
                         # Run this whenever you add a new data-lucide="icon-name" attribute

# --- Full production build (CSS + icons) ---
npm run build

# --- Lint PHP before committing ---
php -l <filename>.php
```

### Dev vs Production: Lucide Icons

| Mode                                           | Script loaded                        | Icons available                     |
| ---------------------------------------------- | ------------------------------------ | ----------------------------------- |
| **Dev** (`SCRIPT_DEBUG=true` in wp-config.php) | unpkg CDN — full library             | All 1500+ icons                     |
| **Prod** (default)                             | `assets/js/lucide-custom.js` — local | Only the ~78 used icons, **~16 KB** |

**To enable dev mode** (add to `wp-config.php`):

```php
define( 'SCRIPT_DEBUG', true );
```

**To add a new icon for production:**

1. Use `data-lucide="icon-name"` in any template — works immediately in dev (CDN).
2. Before deploying, run `npm run build:icons` — the script **auto-scans all .php/.js files** and rebuilds the bundle with every icon it finds.

> No manual list to maintain. If a new icon is not in the Lucide package (e.g. social brand icons), add it to `MANUAL_ICONS` in [generate-lucide-bundle.js](../generate-lucide-bundle.js).

---

## Project Architecture

### Multi-CPT Ecosystem

This theme coordinates 4 post types across 3 plugins + native WordPress posts:

| Post Type           | Plugin               | Meta Prefix | Purpose                              |
| ------------------- | -------------------- | ----------- | ------------------------------------ |
| `deal`              | bigtricks-deals      | `_btdeals_` | Affiliate deals with pricing/coupons |
| `credit-card`       | credit-card-manager  | (none)      | Credit cards with fees/rewards       |
| `referral-codes`    | referral-code-plugin | (none)      | Signup codes with bonuses            |
| `post`              | WordPress Core       | (none)      | Standard blog articles               |
| **Shared Taxonomy** | `store`              | —           | Merchants across all types           |

### Tech Stack

| Layer      | Technology                                  | Notes                                                                |
| ---------- | ------------------------------------------- | -------------------------------------------------------------------- |
| CSS        | Tailwind CSS (self-hosted build)            | `npm run dev` to watch, `npm run build:css` for prod                 |
| Icons      | Lucide Icons — slim self-hosted bundle      | Dev: full CDN; Prod: `assets/js/lucide-custom.js` (~16 KB, 78 icons) |
| Fonts      | Google Fonts: Plus Jakarta Sans + Inter     | Loaded via CDN                                                       |
| JavaScript | Vanilla JS (`assets/js/main.js`)            | No jQuery, uses Fetch API                                            |
| Dark Mode  | Tailwind `darkMode: 'class'` + localStorage | Anti-FOUC script in `wp_head` priority 5                             |
| State      | localStorage                                | `bt_dark_mode`, `bt_view_mode` (list/grid)                           |

---

## Critical Conventions

### Meta Field Naming & Type Safety

**Always type-cast AND sanitize meta fields:**

```php
// ✅ CORRECT - Deal meta (prefixed with _btdeals_)
$sale_price = floatval( get_post_meta( $post_id, '_btdeals_offer_sale_price', true ) );
$offer_url = esc_url( get_post_meta( $post_id, '_btdeals_offer_url', true ) );
$coupon_code = sanitize_text_field( get_post_meta( $post_id, '_btdeals_coupon_code', true ) );
$disclaimer = wp_kses_post( get_post_meta( $post_id, '_btdeals_disclaimer', true ) );

// ✅ CORRECT - Credit card meta (no prefix)
$annual_fee = floatval( get_post_meta( $post_id, 'annual_fee', true ) );
$cashback_rate = sanitize_text_field( get_post_meta( $post_id, 'cashback_rate', true ) );

// ❌ WRONG - Missing type cast & sanitization
$sale_price = get_post_meta( $post_id, '_btdeals_offer_sale_price', true );
```

**Deal Meta Field Reference** (`_btdeals_*` prefix):

- `_btdeals_offer_url` — Affiliate link
- `_btdeals_offer_thumbnail_url` — Product image (priority #1)
- `_btdeals_product_thumbnail_url` — Alt product image (priority #2)
- `_btdeals_offer_sale_price` — Current price (float)
- `_btdeals_offer_old_price` — MRP/original price (float)
- `_btdeals_coupon_code` — Discount code
- `_btdeals_discount` — Percentage off (int)
- `_btdeals_discount_tag` — Badge text (e.g., "Hot Deal")
- `_btdeals_expiration_date` — Expiry timestamp
- `_btdeals_is_expired` — Boolean flag
- `_btdeals_mask_coupon` — Boolean (hide code until reveal button)
- `_btdeals_button_text` — Custom CTA text
- `_btdeals_verify_label` — Trust badge text
- `_btdeals_product_feature` — HTML features list
- `_btdeals_disclaimer` — Legal notice HTML
- `_btdeals_store` — Store name (fallback if taxonomy not set)

### Thumbnail Priority Logic

**Always follow this priority** (see [single-deal.php L58-67](../single-deal.php#L58-L67)):

```php
// Priority: offer_thumbnail_url > product_thumbnail_url > featured_image > store_logo
$product_image_url = '';
if ( $offer_thumbnail_url ) {
    $product_image_url = $offer_thumbnail_url;
} elseif ( $product_thumbnail_url ) {
    $product_image_url = $product_thumbnail_url;
} elseif ( has_post_thumbnail() ) {
    $product_image_url = get_the_post_thumbnail_url( $post_id, 'large' );
} elseif ( $store_logo ) {
    $product_image_url = $store_logo;
}
```

### Template Component Dispatch

**Single AJAX endpoint handles all CPTs** via template map ([functions.php L560-570](../functions.php#L560-L570)):

```php
$template_map = [
    'post'           => 'card-post',
    'deal'           => 'card-deal',
    'referral-codes' => 'card-referral-code',
    'credit-card'    => 'card-credit-card',
];
get_template_part( 'template-parts/' . $template_map[$post_type], null, [ 'post_id' => $pid ] );
```

**Usage in templates:**

```php
<?php get_template_part( 'template-parts/card-deal', null, [ 'post_id' => get_the_ID() ] ); ?>
```

### AJAX Load More Pattern

**Client-side** ([main.js L200-280](../assets/js/main.js#L200-L280)):

```javascript
fetch(bigtricksData.ajaxUrl, {
  method: "POST",
  body: new URLSearchParams({
    action: "bigtricks_load_more",
    page: currentPage,
    cat: categoryId, // optional
    store: storeId, // optional
    type: postType, // optional: 'deal', 'credit-card', 'post'
    nonce: loadMoreNonce,
  }),
})
  .then((res) => res.json())
  .then((data) => {
    container.insertAdjacentHTML("beforeend", data.html);
    lucide.createIcons(); // Re-init icons after AJAX inject
    document.dispatchEvent(new Event("bigtricks:contentLoaded"));
  });
```

**Server-side** ([functions.php L527-580](../functions.php#L527-L580)):

```php
add_action( 'wp_ajax_bigtricks_load_more', 'bigtricks_load_more_handler' );
add_action( 'wp_ajax_nopriv_bigtricks_load_more', 'bigtricks_load_more_handler' );

function bigtricks_load_more_handler() {
    check_ajax_referer( 'bigtricks_load_more', 'nonce' );

    $page = absint( $_POST['page'] ?? 1 );
    $cat = absint( $_POST['cat'] ?? 0 );
    $store = absint( $_POST['store'] ?? 0 );
    $type = sanitize_text_field( $_POST['type'] ?? '' );

    // Build WP_Query with tax_query for store filtering
    // Dispatch to template parts via $template_map
    // Return JSON: { html: '<cards>', has_more: bool }

    wp_send_json( [ 'html' => $html, 'has_more' => $has_more ] );
}
```

### Dark Mode Implementation

**Anti-FOUC strategy** ([functions.php L120-126](../functions.php#L120-L126)):

```php
// Priority 5 = BEFORE any styles load
add_action( 'wp_head', function () {
    echo '<script>!function(){var s=localStorage.getItem("bt_dark_mode");if(s==="1"||(s===null&&window.matchMedia&&window.matchMedia("(prefers-color-scheme: dark)").matches)){document.documentElement.classList.add("dark")}}</script>';
}, 5 );
```

**Toggle in JavaScript:**

```javascript
const darkModeToggle = document.getElementById("dark-mode-toggle");
darkModeToggle?.addEventListener("click", () => {
  document.documentElement.classList.toggle("dark");
  const isDark = document.documentElement.classList.contains("dark");
  localStorage.setItem("bt_dark_mode", isDark ? "1" : "0");
});
```

### Lucide Icons Pattern

**Menu walker integration** ([functions.php ~L700-800](../functions.php)):

- Parses `[icon-name]` prefix from menu labels
- Injects `<i data-lucide="icon-name"></i>` element
- Example: Menu label `"[trending-up] Deals"` → `<i data-lucide="trending-up"></i> Deals`

**Re-initialize after AJAX:**

```javascript
// After inserting dynamic content
lucide.createIcons();
```

---

## Common Development Tasks

### Adding a New Meta Field to Deals

1. **Register meta** in [functions.php](../functions.php) (section §4):

```php
register_post_meta( 'deal', '_btdeals_new_field', [
    'type'         => 'string',
    'single'       => true,
    'show_in_rest' => true,
    'sanitize_callback' => 'sanitize_text_field',
] );
```

2. **Add admin UI** in meta box callback (section §5):

```php
$new_field = get_post_meta( $post->ID, '_btdeals_new_field', true );
?>
<p>
    <label for="btdeals_new_field"><strong>New Field:</strong></label><br>
    <input type="text" id="btdeals_new_field" name="btdeals_new_field" value="<?php echo esc_attr( $new_field ); ?>" style="width:100%;">
</p>
```

3. **Save handler** (section §5):

```php
if ( isset( $_POST['btdeals_new_field'] ) ) {
    update_post_meta( $post_id, '_btdeals_new_field', sanitize_text_field( $_POST['btdeals_new_field'] ) );
}
```

4. **Display in template** (e.g., [single-deal.php](../single-deal.php)):

```php
$new_field = sanitize_text_field( get_post_meta( $post_id, '_btdeals_new_field', true ) );
if ( $new_field ) {
    echo '<div class="new-field">' . esc_html( $new_field ) . '</div>';
}
```

### Creating a New Template Part Widget

1. **Create file** in `template-parts/widget-{name}.php`
2. **Follow existing pattern** from [widget-latest-deals.php](../template-parts/widget-latest-deals.php):
   - Self-contained (no external dependencies)
   - Dark mode support (`dark:bg-*`, `dark:text-*`)
   - Responsive (`lg:`, `md:` breakpoints)
   - Lazy loading images (`loading="lazy"`)
3. **Document in** [template-parts/WIDGETS-README.md](../template-parts/WIDGETS-README.md)
4. **Use with** `get_template_part( 'template-parts/widget-{name}' );`

### Modifying Tailwind Configuration

**Two places** (keep in sync) — [functions.php L120-180](../functions.php#L120-L180):

1. **Pre-CDN config** (priority 5):

```javascript
window.tailwind = {
  config: {
    darkMode: "class",
    theme: {
      extend: {
        colors: {
          primary: { 600: "#4f46e5", DEFAULT: "#4f46e5" },
          // Add new colors here
        },
      },
    },
  },
};
```

2. **Post-CDN restoration** (priority 20) — duplicate the config

### Debugging AJAX Load More

1. **Check browser console** for fetch errors
2. **Verify nonce** is passed: `data-nonce="<?php echo esc_attr( wp_create_nonce( 'bigtricks_load_more' ) ); ?>"`
3. **Test backend** directly:

```bash
curl -X POST https://example.com/wp-admin/admin-ajax.php \
  -d "action=bigtricks_load_more&page=2&nonce=YOUR_NONCE"
```

4. **Common issues:**
   - Nonce mismatch (regenerate page)
   - Template part not found (check `$template_map`)
   - Query returns 0 posts (check tax_query or post_type)

---

## File Organization

### Template Hierarchy

```
single-deal.php         → Deals (priority override at 999)
single-credit-card.php  → Credit cards (if exists, else single.php)
single-{cpt}.php        → Custom post types
single.php              → Standard posts
category.php            → Category archives
taxonomy-store.php      → Store taxonomy archives
archive-store.php       → Store CPT archive
front-page.php          → Homepage
page.php                → Pages
search.php              → Search results
404.php                 → Not found
index.php               → Ultimate fallback
```

### Template Parts (`template-parts/`)

```
breadcrumb.php          → Context-aware breadcrumb builder
card-deal.php           → Deal card (used in archives + AJAX)
card-credit-card.php    → Credit card card
card-post.php           → Blog post card
card-referral-code.php  → Referral code card
widget-latest-deals.php → Sidebar widget: 5 recent deals
widget-top-stores.php   → Sidebar widget: 6 top stores
WIDGETS-README.md       → Widget documentation
```

### Assets

```
assets/css/bigtricks.css      → Theme styles (dark mode, chat comments)
assets/css/editor-style.css   → Gutenberg WYSIWYG styles
assets/js/main.js             → All frontend JS (carousel, AJAX, dark mode)
assets/images/placeholder.svg → Fallback for missing thumbnails
```

---

## Security & Performance

### Nonce Verification

**All AJAX handlers must verify nonces:**

```php
check_ajax_referer( 'bigtricks_load_more', 'nonce' );
```

**Nonce regeneration** — Current implementation creates nonce once per page load. For multiple AJAX requests, consider returning fresh nonce in response.

### Sanitization Checklist

| Input Type     | Sanitization                | Output Escaping   |
| -------------- | --------------------------- | ----------------- |
| Text field     | `sanitize_text_field()`     | `esc_html()`      |
| Textarea       | `sanitize_textarea_field()` | `esc_textarea()`  |
| URL            | `esc_url_raw()`             | `esc_url()`       |
| HTML (trusted) | `wp_kses_post()`            | `wp_kses_post()`  |
| Integer        | `absint()` or `intval()`    | `absint()`        |
| Float          | `floatval()`                | `number_format()` |

### Performance Notes

⚠️ **File I/O on every page** — `carousel-config.json` read in `wp_enqueue_scripts`. Cache in transient for high traffic.

⚠️ **CDN dependencies** — Tailwind Play CDN and Lucide unpkg not production-optimized. Consider:

- Self-host Tailwind build
- Inline critical CSS
- SVG sprite for icons

⚠️ **Store taxonomy flush** — Rewrite rules only flushed on theme activation. Run `flush_rewrite_rules()` if slug changes.

---

## Common Pitfalls & Solutions

### Plugin Template Override Issue

**Problem:** bigtricks-deals plugin tries to load its own `single-deal.php`  
**Solution:** Forced override at priority 999 ([functions.php L194-195](../functions.php#L194-L195)):

```php
add_filter( 'single_template', fn($t) => locate_template(['single-deal.php']) ?: $t, 999 );
add_filter( 'template_include', fn($t) => (is_singular('deal') ? locate_template(['single-deal.php']) : false) ?: $t, 999 );
```

### Dark Mode Flash on Load

**Problem:** Page renders in light mode before dark class applied  
**Solution:** Inline script in `wp_head` priority 5 (before styles):

```javascript
!(function () {
  var s = localStorage.getItem("bt_dark_mode");
  if (
    s === "1" ||
    (s === null &&
      window.matchMedia &&
      window.matchMedia("(prefers-color-scheme: dark)").matches)
  ) {
    document.documentElement.classList.add("dark");
  }
})();
```

### Icons Not Rendering After AJAX

**Problem:** Lucide icons need re-initialization for dynamic content  
**Solution:** Call `lucide.createIcons()` after inserting HTML

### Store Logo Returns Attachment ID

**Problem:** Term meta `thumb_image` may store attachment ID or direct URL  
**Solution:** Always check and convert ([single-deal.php L101-106](../single-deal.php#L101-L106)):

```php
$store_logo = get_term_meta( $store->term_id, 'thumb_image', true );
if ( $store_logo && is_numeric( $store_logo ) ) {
    $store_logo = wp_get_attachment_image_url( (int) $store_logo, 'medium' );
}
```

---

## Testing Checklist

Before committing template changes:

- [ ] PHP lint: `php -l <filename>.php` (zero errors)
- [ ] Dark mode: Toggle and verify all elements
- [ ] AJAX load more: Test pagination, category filtering, store filtering
- [ ] Responsive: Mobile (375px), tablet (768px), desktop (1400px)
- [ ] Icons: Verify Lucide icons render (check unpkg CDN)
- [ ] Meta fields: Type-cast all `get_post_meta()` calls
- [ ] Sanitization: All user input sanitized, all output escaped
- [ ] Thumbnails: Test priority fallback (offer > product > featured)
- [ ] Expired deals: Verify opacity overlay and "Expired" badge
- [ ] Nonce security: All AJAX handlers use `check_ajax_referer()`

---

## Related Documentation

- **[PLAN.md](../PLAN.md)** — Complete architecture and progress tracker
- **[CC.md](../CC.md)** — Credit Card Manager plugin docs
- **[template-parts/WIDGETS-README.md](../template-parts/WIDGETS-README.md)** — Widget usage guide
- **WordPress Codex** → [Plugin API](https://developer.wordpress.org/plugins/hooks/)
- **Tailwind CSS** → [Dark Mode](https://tailwindcss.com/docs/dark-mode)
- **Lucide Icons** → [Icon Library](https://lucide.dev/icons/)

---

## Agent Behavioral Rules

1. **Always type-cast meta fields** — No raw `get_post_meta()` output
2. **Follow thumbnail priority** — offer_thumbnail > product_thumbnail > featured
3. **Verify nonces in AJAX** — Use `check_ajax_referer()` before processing
4. **Re-init Lucide after AJAX** — Call `lucide.createIcons()` after DOM inject
5. **Preserve dark mode** — Use `dark:*` utilities for all new components
6. **Use template parts** — Don't duplicate card HTML, use `get_template_part()`
7. **Respect meta prefixes** — Deals use `_btdeals_`, others don't
8. **Link existing docs** — Reference [PLAN.md](../PLAN.md) instead of duplicating
9. **Security first** — Sanitize input, escape output, verify nonces
10. **Test responsively** — Mobile-first, use Tailwind breakpoints (`md:`, `lg:`)

---

**Last Updated:** April 17, 2026  
**Maintained By:** AI Agent Bootstrap (see commit history)

# Bigtricks Widget Components

This theme includes reusable widget components that can be used anywhere in your templates.

## Table of Contents

1. [Content Widgets](#content-widgets)
   - Latest Deals Widget
   - Top Stores Widget
   - More Referral Codes Widget
   - Related Posts Component
2. [Social & Community Widgets](#social--community-widgets)
   - Follow Us Widget
   - Telegram CTA Widget
   - WhatsApp CTA Widget
3. [Engagement Widgets](#engagement-widgets)
   - Newsletter Widget
   - Trending Categories Widget
   - Download App Widget

---

## Lucide Icons

All widget components use [Lucide Icons](https://lucide.dev/icons/) via `data-lucide` attributes.

### Dev vs Production

| Mode                          | Loaded from                  | Icons available        |
| ----------------------------- | ---------------------------- | ---------------------- |
| **Dev** (`SCRIPT_DEBUG=true`) | unpkg CDN                    | All 1500+ icons        |
| **Prod** (default)            | `assets/js/lucide-custom.js` | ~78 used icons, ~16 KB |

### Using icons in templates

```php
<i data-lucide="icon-name" class="w-4 h-4"></i>
```

Icons are replaced with inline SVGs by `lucide.createIcons()` on page load. After AJAX-injected content, always call:

```javascript
lucide.createIcons();
```

### Adding a new icon for production

1. Use `data-lucide="icon-name"` in a template — works immediately in dev (CDN loads all icons).
2. Run `npm run build:icons` — the script **auto-scans all .php/.js files** and rebuilds the bundle automatically.

No manual list to maintain. The generator skips itself and `lucide-custom.js` to avoid false positives.

> **Note:** Social brand icons (`facebook`, `instagram`, `twitter`) were removed from Lucide and are provided via the `MANUAL_ICONS` map in the generator script.

---

## Content Widgets

### 1. Latest Deals Widget

**File:** `template-parts/widget-latest-deals.php`

Displays the 5 most recent deals with:

- Product thumbnail (with discount badge)
- Deal title
- Sale price (highlighted in green)
- Old/MRP price (strikethrough in gray)
- Relative time (e.g., "2h ago", "30 min ago")
- Link to "View More Deals"

**Usage:**

```php
<?php get_template_part( 'template-parts/widget-latest-deals' ); ?>
```

**Features:**

- ✅ Fully responsive
- ✅ Dark mode support
- ✅ Thumbnail priority: offer_thumbnail_url > product_thumbnail_url > featured_image
- ✅ Auto-calculates discount if not in meta
- ✅ Excludes current post when on single deal page

---

### 2. Top Stores Widget

**File:** `template-parts/widget-top-stores.php`

Displays 6 top stores (ordered by deal count) with:

- Store logo (or icon fallback)
- Store name
- Number of deals

**Usage:**

```php
<?php get_template_part( 'template-parts/widget-top-stores' ); ?>
```

**Features:**

- ✅ Fully responsive
- ✅ Dark mode support
- ✅ 2-column grid layout
- ✅ Links to store archive pages
- ✅ Handles both attachment IDs and direct URLs for logos
- ✅ Uses `thumb_image` term meta key (matching page-stores.php)

---

### 3. More Referral Codes Widget

**File:** `template-parts/widget-more-referral-codes.php`

Displays recent referral codes with bonus information and instant copy buttons.

**Usage:**

```php
<?php get_template_part( 'template-parts/widget-more-referral-codes' ); ?>
```

**Features:**

- ✅ Fully responsive
- ✅ Dark mode support
- ✅ Copy code buttons
- ✅ Bonus amount display
- ✅ Excludes current post when on single referral code page

---

### 4. Related Posts Component

**File:** `template-parts/related-posts.php`

Displays related posts in a clean card layout with:

- Post thumbnail (if available)
- Post title (2-line clamp)
- Published date
- Hover effects

**Usage:**

```php
<?php
get_template_part( 'template-parts/related-posts', null, [
	'post_id'        => get_the_ID(),
	'category'       => 'credit-cards', // Optional category slug
	'posts_per_page' => 3,
	'title'          => __( 'Related Articles', 'bigtricks' ),
	'icon'           => 'newspaper', // Lucide icon name
] );
?>
```

**Parameters:**

- `post_id` (int) - Current post ID to exclude from results
- `category` (string) - Optional category slug to filter by. If not provided, uses post's first category
- `posts_per_page` (int) - Number of posts to display (default: 3)
- `title` (string) - Section heading text (default: "Related Articles")
- `icon` (string) - Lucide icon name for heading (default: "zap")

**Features:**

- ✅ Fully responsive (3-column grid on desktop)
- ✅ Dark mode support
- ✅ Random post order for variety
- ✅ Fallback to first category if none specified
- ✅ Auto-resets post data after query
- ✅ Compatible with bigtricks_get_thumbnail_url() helper

---

## Social & Community Widgets

### 5. Follow Us Widget

**File:** `template-parts/widget-follow-us.php`

Compact social media follow buttons with dynamic URLs from theme settings.

**Usage:**

```php
<?php get_template_part( 'template-parts/widget-follow-us' ); ?>
```

**Features:**

- ✅ Fully responsive
- ✅ Dark mode support
- ✅ **Dynamic URLs from Theme Settings** (Appearance → Theme Settings)
- ✅ Hover effects with smooth transitions
- ✅ Icon animations (arrow slides on hover)
- ✅ Platform-specific brand colors
- ✅ All links open in new tab with noopener
- ✅ Auto-hides if no URLs are configured
- ✅ Conditionally shows each platform based on URL presence

**Social Platforms:**

- Telegram (blue)
- WhatsApp (green)
- Twitter (black/gray)
- Instagram (purple/pink gradient)

**Configuration:**
Go to **Appearance → Theme Settings → Social & Community Links** and set:

- `bt_telegram_url` - Telegram Channel URL
- `bt_whatsapp_url` - WhatsApp Group URL
- `bt_twitter_url` - Twitter/X Profile URL
- `bt_instagram_url` - Instagram Profile URL

---

### 6. Telegram CTA Widget

**File:** `template-parts/widget-telegram-cta.php`

Large promotional banner for Telegram channel subscription.

**Usage:**

```php
<?php get_template_part( 'template-parts/widget-telegram-cta' ); ?>
```

**Features:**

- ✅ Eye-catching gradient background (blue to cyan)
- ✅ Dark mode support
- ✅ **Dynamic URL from Theme Settings** (`bt_telegram_url`)
- ✅ Animated icon on hover
- ✅ Hover lift effect
- ✅ "Must Join" badge
- ✅ Auto-hides if Telegram URL is not configured

**Configuration:**
Set `bt_telegram_url` in **Appearance → Theme Settings → Social & Community Links**

---

### 7. WhatsApp CTA Widget

**File:** `template-parts/widget-whatsapp-cta.php`

Large promotional banner for WhatsApp community.

**Usage:**

```php
<?php get_template_part( 'template-parts/widget-whatsapp-cta' ); ?>
```

**Features:**

- ✅ WhatsApp brand gradient background (green)
- ✅ Dark mode support
- ✅ **Dynamic URL from Theme Settings** (`bt_whatsapp_url`)
- ✅ Animated icon on hover
- ✅ Hover lift effect
- ✅ Auto-hides if WhatsApp URL is not configured

**Configuration:**
Set `bt_whatsapp_url` in **Appearance → Theme Settings → Social & Community Links**

---

## Engagement Widgets

### 8. Newsletter Widget

**File:** `template-parts/widget-newsletter.php`

Email newsletter signup form for daily deal digest.

**Usage:**

```php
<?php get_template_part( 'template-parts/widget-newsletter' ); ?>
```

**Features:**

- ✅ Fully responsive
- ✅ Dark mode support
- ✅ Nonce-protected form submission
- ✅ Email validation (HTML5 required attribute)
- ✅ Flame icon with gradient background
- ✅ Clean, minimal design

**Form Action:**
By default, submits to homepage. Update form handler in `functions.php` to process signups.

---

### 9. Trending Categories Widget

**File:** `template-parts/widget-trending-categories.php`

Displays top categories by post count.

**Usage:**

```php
<?php
get_template_part( 'template-parts/widget-trending-categories', null, [
	'count' => 8
] );
?>
```

**Parameters:**

- `count` (int) - Number of categories to display (default: 8)

**Features:**

- ✅ Fully responsive
- ✅ Dark mode support
- ✅ Hover effects on category links
- ✅ Animated chevron icon
- ✅ "View All Categories" footer link
- ✅ Uses `bigtricks_get_top_categories()` helper function

---

### 10. Download App Widget

**File:** `template-parts/widget-download-app.php`

App download promotional widget with Play Store link.

**Usage:**

```php
<?php get_template_part( 'template-parts/widget-download-app' ); ?>
```

**Features:**

- ✅ Dark gradient background with radial overlay
- ✅ 5-star rating display
- ✅ Feature list with emoji bullets
- ✅ Official Google Play Store SVG icon
- ✅ Hover effects and active state
- ✅ Fully responsive

**App Features Listed:**

- 🔔 Real-time loot alerts
- 🏷️ Exclusive app-only coupons
- ⚡ Price drop notifications
- 📱 Works offline too

---

## Examples

### Sidebar Usage (Current Implementation)

**Standard Sidebar (`sidebar.php`):**

```php
<aside class="w-full lg:w-[380px] shrink-0 space-y-8 hidden lg:block">
    <?php
    get_template_part( 'template-parts/widget-telegram-cta' );
    get_template_part( 'template-parts/widget-trending-categories', null, [ 'count' => 8 ] );
    get_template_part( 'template-parts/widget-newsletter' );
    get_template_part( 'template-parts/widget-whatsapp-cta' );

    if ( is_active_sidebar( 'sidebar-1' ) ) :
        dynamic_sidebar( 'sidebar-1' );
    endif;

    get_template_part( 'template-parts/widget-download-app' );
    ?>
</aside>
```

**Single Deal Sidebar:**

```php
<aside class="lg:w-80 shrink-0 space-y-6">
    <?php
    get_template_part( 'template-parts/widget-latest-deals' );
    get_template_part( 'template-parts/widget-top-stores' );
    get_template_part( 'template-parts/widget-follow-us' );
    ?>
</aside>
```

**Single Credit Card Sidebar:**

```php
<aside class="lg:w-[340px] shrink-0 space-y-8">
    <?php
    get_template_part( 'template-parts/widget-top-stores' );
    get_template_part( 'template-parts/widget-follow-us' );
    get_template_part( 'template-parts/widget-telegram-cta' );
    ?>
</aside>
```

**Single Referral Code Sidebar:**

```php
<aside class="w-full lg:w-80 xl:w-[320px] shrink-0 space-y-6">
    <?php
    get_template_part( 'template-parts/widget-more-referral-codes' );
    get_template_part( 'template-parts/widget-top-stores' );
    get_template_part( 'template-parts/widget-follow-us' );
    ?>
</aside>
```

### Homepage / Custom Page Usage

```php
<div class="container mx-auto">
    <div class="grid md:grid-cols-3 gap-6">
        <div class="md:col-span-2">
            <!-- Your main content -->
        </div>
        <div class="space-y-6">
            <?php get_template_part( 'template-parts/widget-latest-deals' ); ?>
            <?php get_template_part( 'template-parts/widget-top-stores' ); ?>
        </div>
    </div>
</div>
```

### Footer Widget Area

```php
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php get_template_part( 'template-parts/widget-latest-deals' ); ?>
    <?php get_template_part( 'template-parts/widget-top-stores' ); ?>
    <!-- Other widgets -->
</div>
```

---

## Customization

### Modify Number of Items

**Latest Deals:**
Edit line 18 in `template-parts/widget-latest-deals.php`:

```php
'posts_per_page' => 5, // Change this number
```

**Top Stores:**
Edit line 13 in `template-parts/widget-top-stores.php`:

```php
'number' => 6, // Change this number
```

### Modify Grid Columns (Top Stores)

Edit line 29 in `template-parts/widget-top-stores.php`:

```php
<div class="grid grid-cols-2 gap-3"> <!-- Change grid-cols-2 to grid-cols-3, etc. -->
```

---

## Meta Fields Used

### Latest Deals Widget

- `_btdeals_offer_thumbnail_url` - Primary thumbnail
- `_btdeals_product_thumbnail_url` - Fallback thumbnail
- `_btdeals_offer_sale_price` - Sale price (displayed in green)
- `_btdeals_offer_old_price` - Original MRP (displayed with strikethrough)
- `_btdeals_discount` - Discount percentage (shown in badge)

### Top Stores Widget

- `thumb_image` - Store logo (term meta on 'store' taxonomy)

---

## Theme Settings Configuration

Several widgets now use **dynamic URLs from Theme Settings** instead of hardcoded values.

### How to Configure Social Links

1. Go to **WordPress Admin → Appearance → Theme Settings**
2. Scroll to **Social & Community Links** section
3. Enter your URLs:
   - **Telegram Channel URL** (`bt_telegram_url`)
   - **WhatsApp Group URL** (`bt_whatsapp_url`)
   - **Twitter / X Profile URL** (`bt_twitter_url`)
   - **Instagram Profile URL** (`bt_instagram_url`)
4. Click **Save Changes**

### Widgets Using Dynamic Settings

| Widget           | Setting Keys Used                                                          | Behavior if Empty                                               |
| ---------------- | -------------------------------------------------------------------------- | --------------------------------------------------------------- |
| **Follow Us**    | `bt_telegram_url`, `bt_whatsapp_url`, `bt_twitter_url`, `bt_instagram_url` | Hides widget if all URLs empty; shows only configured platforms |
| **Telegram CTA** | `bt_telegram_url`                                                          | Widget hidden if URL not set                                    |
| **WhatsApp CTA** | `bt_whatsapp_url`                                                          | Widget hidden if URL not set                                    |

### Helper Function

All widgets use the `bigtricks_option()` helper function:

```php
$telegram_url = bigtricks_option( 'bt_telegram_url', 'https://t.me/default' );
```

**Parameters:**

- `$key` (string) - Setting key name
- `$default` (string) - Fallback value if setting is empty (optional)

**Returns:** The setting value or default

### Benefits

- ✅ **Centralized management** - Update URLs in one place
- ✅ **No code editing required** - Use admin interface
- ✅ **Automatic validation** - URLs are sanitized with `esc_url_raw()`
- ✅ **Graceful degradation** - Widgets hide if URLs missing
- ✅ **Secure** - Settings stored in `bigtricks_theme_options` with sanitization callbacks

---

## Dark Mode

Both widgets are fully dark mode compatible with proper contrast and colors.

**Light Mode Colors:**

- Background: White (`bg-white`)
- Text: Slate 900 (`text-slate-900`)
- Borders: Slate 200 (`border-slate-200`)

**Dark Mode Colors:**

- Background: Slate 900 (`dark:bg-slate-900`)
- Text: White (`dark:text-white`)
- Borders: Slate 800 (`dark:border-slate-800`)

All hover states, icons, and badges also support dark mode.

---

## Dependencies

- **Lucide Icons:** Used for icons (clock, zap, store, arrow-right, send, message-circle, smartphone, flame, etc.)
- **Tailwind CSS:** All styling uses Tailwind utility classes
- **bigtricks_time_ago():** Helper function for relative time display (defined in widget if not available)
- **bigtricks_option():** Helper function for reading theme settings (defined in `functions.php`)
- **bigtricks_get_top_categories():** Returns top categories by post count (defined in `functions.php`)

---

## Widget Inventory

| Widget Name         | File                             | Dynamic URLs | Dark Mode | Responsive |
| ------------------- | -------------------------------- | ------------ | --------- | ---------- |
| Latest Deals        | `widget-latest-deals.php`        | No           | ✅        | ✅         |
| Top Stores          | `widget-top-stores.php`          | No           | ✅        | ✅         |
| More Referral Codes | `widget-more-referral-codes.php` | No           | ✅        | ✅         |
| Related Posts       | `related-posts.php`              | No           | ✅        | ✅         |
| Follow Us           | `widget-follow-us.php`           | ✅           | ✅        | ✅         |
| Telegram CTA        | `widget-telegram-cta.php`        | ✅           | ✅        | ✅         |
| WhatsApp CTA        | `widget-whatsapp-cta.php`        | ✅           | ✅        | ✅         |
| Newsletter          | `widget-newsletter.php`          | No           | ✅        | ✅         |
| Trending Categories | `widget-trending-categories.php` | No           | ✅        | ✅         |
| Download App        | `widget-download-app.php`        | No           | ✅        | ✅         |

---

## Notes

- **All widgets are fully responsive** with mobile, tablet, and desktop breakpoints
- **Dark mode is supported** across all widgets with proper color contrast
- **Content widgets** automatically exclude the current post when used on single pages
- **Social widgets** gracefully hide if URLs are not configured in Theme Settings
- Widgets use `wp_reset_postdata()` to avoid conflicts
- Empty states are handled gracefully with appropriate messages
- All URLs and content are properly escaped for security
- All widgets follow WordPress coding standards and best practices
- Icon animations are GPU-accelerated for smooth performance

---

## Quick Reference

### Most Common Use Cases

**Want to add sidebar to a single template?**

```php
<aside class="lg:w-80 shrink-0 space-y-6">
    <?php
    get_template_part( 'template-parts/widget-latest-deals' );
    get_template_part( 'template-parts/widget-follow-us' );
    ?>
</aside>
```

**Want to promote Telegram/WhatsApp?**

```php
<?php
get_template_part( 'template-parts/widget-telegram-cta' );
get_template_part( 'template-parts/widget-whatsapp-cta' );
?>
```

**Want to show trending content?**

```php
<?php
get_template_part( 'template-parts/widget-trending-categories', null, [ 'count' => 10 ] );
get_template_part( 'template-parts/widget-latest-deals' );
?>
```

**Want social links without large CTAs?**

```php
<?php get_template_part( 'template-parts/widget-follow-us' ); ?>
```

---

## Changelog

**April 2026:**

- ✅ Created 6 new sidebar widgets (Telegram CTA, WhatsApp CTA, Follow Us, Newsletter, Trending Categories, Download App)
- ✅ Implemented dynamic social URLs via Theme Settings
- ✅ Updated `sidebar.php` to use new widget system
- ✅ Added dark mode support to all widgets
- ✅ Improved documentation with configuration guide
- ✅ Added widget inventory table and quick reference

---

## Support

For issues or questions about widgets:

1. Check this documentation first
2. Verify Theme Settings are configured (**Appearance → Theme Settings**)
3. Ensure all URLs are valid and properly formatted
4. Check browser console for JavaScript errors (Lucide icons)
5. Verify Tailwind CSS is loaded correctly

---

**Last Updated:** April 19, 2026  
**Total Widgets:** 10  
**All widgets production-ready** ✅

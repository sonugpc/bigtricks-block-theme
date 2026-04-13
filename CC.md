`

# Credit Card Manager

A WordPress plugin for managing, listing, filtering, comparing, and presenting credit cards as a dedicated content type.

The plugin creates a full credit card catalog system with:

- A public custom post type for credit cards
- Taxonomies for bank/issuer, network type, and card category
- A structured admin entry screen with custom fields and AI JSON import
- Public archive, single-card, and comparison templates
- Multiple shortcodes for embedding cards, grids, filters, and comparison tables
- Custom REST API endpoints for card listings, individual cards, and available filters
- Performance helpers such as cached filter responses and a numeric meta cache table

## Plugin Summary

- Plugin name: `Credit Card Manager`
- Text domain: `credit-card-manager`
- Version: `1.0.0`
- Main file: `plugin.php`
- Core bootstrap class: `CreditCardManager_Core`
- Custom post type slug: `credit-card`
- Public archive slug: `/credit-cards/`
- Custom REST namespace: `/wp-json/ccm/v1/`

## What The Plugin Registers

### Custom Post Type

The plugin registers one public post type:

- Post type key: `credit-card`
- REST base: `credit-cards`
- Archive enabled: yes
- Public UI: yes
- Queryable: yes
- Menu icon: `dashicons-id-alt`
- Rewrite slug: `credit-cards`

Supported post features:

- Title
- Editor
- Featured image
- Excerpt
- Custom fields
- Comments

### Taxonomies

The plugin uses these taxonomies on the `credit-card` post type:

1. `store`
   Used as bank/issuer taxonomy. The plugin does not fully register a new `store` taxonomy itself. Instead, it attaches the existing `store` taxonomy to the `credit-card` post type with `register_taxonomy_for_object_type()`.

2. `network-type`
   Non-hierarchical taxonomy for card network branding.

   Default terms inserted on first run:
   - Visa
   - Mastercard
   - American Express
   - Discover
   - RuPay

3. `card-category`
   Hierarchical taxonomy for card use-cases and segments.

   Default terms inserted on first run:
   - Cashback
   - Travel
   - Rewards
   - Business
   - Premium
   - Secured
   - Student
   - No Annual Fee

### Taxonomy Conflict Handling

The plugin checks whether any of these taxonomies already exist and whether they are attached elsewhere. If a conflict is detected, it stores the conflict details in an option and shows a warning notice in wp-admin on credit-card screens and the Plugins screen.

## Custom Fields Registered

The plugin registers post meta for the `credit-card` post type and exposes those fields in the REST API.

### Basic card information

- `rating`
- `review_count`
- `featured`
- `trending`
- `theme_color`

### Pricing, rewards, and conversion fields

- `annual_fee`
- `joining_fee`
- `welcome_bonus`
- `welcome_bonus_points`
- `welcome_bonus_type`
- `reward_type`
- `reward_conversion_rate`
- `reward_conversion_value`
- `cashback_rate`
- `reward_rate`
- `apply_link`

### Credit and eligibility fields

- `credit_limit`
- `interest_rate`
- `processing_time`
- `min_income`
- `min_age`
- `max_age`
- `documents`

### Structured list fields

- `pros`
- `cons`
- `best_for`
- `features`
- `rewards`
- `fees`
- `eligibility`
- `custom_faqs`

### Visual and scoring fields

- `gradient`
- `bg_gradient`
- `overall_score`
- `reward_score`
- `fees_score`
- `benefits_score`
- `support_score`

### Field shape notes

- `pros`, `cons`, `best_for`, and `documents` are stored as arrays of strings.
- `features` is stored as an array of objects with `title`, `description`, and optionally `icon` in the REST schema.
- `rewards` is stored as an array of objects with `category`, `rate`, and `description`.
- `fees` is stored as an array of objects with `type`, `amount`, and `description`.
- `eligibility` is stored as an array of objects with `criteria` and `value`.
- `custom_faqs` is stored as an array of question/answer objects.

### Meta sanitization behavior

- Ratings are clamped to `0-5`
- Percentages are clamped to `0-100`
- Decimal fields are converted to `float`
- Boolean flags are saved as `1` or `0`
- String arrays are sanitized with `sanitize_text_field`
- Complex arrays are sanitized key by key

## Admin Experience

When editing a `credit-card` post, the plugin adds one main meta box:

- Meta box title: `Credit Card Details`

That screen includes these sections:

1. `AI Data Import`
   Includes a JSON textarea and buttons for:
   - Import JSON data
   - Export current data
   - Clear JSON

   The sample JSON structure is grouped into:
   - `basic`
   - `fees`
   - `rewards`
   - `eligibility`
   - `lists`
   - `custom_faqs`

2. `Basic Information`
   - Rating
   - Review count
   - Featured flag
   - Trending flag
   - Theme color picker

3. `Fees & Benefits`
   - Annual fee
   - Joining fee
   - Welcome bonus description
   - Welcome bonus points/amount
   - Welcome bonus type
   - Cashback/reward rate
   - Reward type
   - Reward conversion rate
   - Reward conversion value

4. `Eligibility & Terms`
   - Credit limit
   - Interest rate
   - Processing time
   - Minimum income
   - Minimum age
   - Maximum age
   - Application link

5. Repeater-style list sections
   - Pros
   - Cons
   - Best For
   - Required Documents
   - Key Features
   - Frequently Asked Questions

### Admin list table enhancements

The credit-card list table adds these columns:

- Image
- Rating
- Annual Fee
- Network
- Bank
- Featured

Sortable admin columns:

- Rating
- Annual Fee

## Frontend Behavior

### Conditional asset loading

Frontend CSS and JS are only enqueued when relevant pages are being viewed:

- Credit card archive pages
- Single credit card pages
- Compare page
- Pages containing these shortcodes:
  - `credit-card`
  - `credit_card_grid`
  - `ccm_filters`

Frontend assets:

- `assets/frontend.css`
- `assets/archive-responsive.css`
- `assets/frontend.js`

Shortcode-specific styling:

- `assets/shortcodes.css`

Admin assets:

- `assets/admin.css`
- `assets/admin.js`

## Templates Included

### 1. Single credit card template

Template file:

- `templates/single-credit-card.php`

This is the main full-detail product page for each credit card. It is designed like a long-form editorial landing page with a sticky navigation and modular content sections.

Main single-page sections:

- Hero section with card image, bank name, badges, rating, and quick highlights
- Sticky navigation tabs
- Quick Overview
- Pros & Cons
- About This Card
- Key Features
- Reward Program
- Fees & Charges
- Eligibility Criteria
- Frequently Asked Questions
- Related Articles
- Trending Credit Cards
- Explore Other Card Categories
- Comments

Single template behavior and UI notes:

- Uses featured image as the main card visual
- Shows featured and trending badges when applicable
- Uses dynamic highlight cards for fee, rewards, and key data points
- Builds FAQs from both custom FAQ entries and dynamically generated FAQ content
- Includes FAQ accordion behavior in JavaScript
- Includes sticky in-page navigation and active section highlighting
- Includes a share modal
- Uses large inline CSS for the full page design

### 2. Archive template

Template file:

- `templates/archive-credit-card.php`

This is the credit-card listing page with filters, sort controls, and comparison UI.

Archive template features:

- SEO-focused hero and CTA section
- Desktop left sidebar filters
- Mobile filter modal
- Sort controls
- Compare checkboxes on cards
- Results counter
- Initial query shows 8 cards per page/view load
- Load More button
- No-results state
- Additional educational/SEO content blocks beneath the card listing

Archive filtering supports:

- Bank
- Category
- Network type
- Minimum rating
- Maximum annual fee
- Featured
- Trending

Archive sorting supports:

- Newest first
- Highest rated
- Lowest rated
- Lowest fee
- Highest fee
- Most popular by review count

### 3. Compare page template

Template file:

- `templates/page-compare-cards.php`

This page renders a dedicated credit card comparison experience.

Compare page features:

- Reads card IDs from the `cards` query parameter
- Builds a dynamic comparison page title and description
- Renders breadcrumb navigation
- Shows a top summary card for each selected credit card
- Includes print action
- Includes a detailed comparison table
- Shows empty state when no cards are selected
- Outputs comparison-oriented structured data and FAQ schema

Compare URLs supported by the plugin:

- `/compare-cards/`
- `/compare-cards/?cards=12,34`
- `/compare-cards/12,34`

### 4. Template parts

Reusable partials shipped with the plugin:

- `templates/template-parts/card-item.php`
- `templates/template-parts/compare-table.php`
- `templates/template-parts/filter-section.php`
- `templates/template-parts/pagination.php`

What they do:

- `card-item.php` renders a reusable card tile with image, badges, rating, highlights, key benefits, compare toggle, details button, and apply button.
- `compare-table.php` renders the side-by-side comparison table.
- `filter-section.php` renders a standalone filter UI block.
- `pagination.php` renders accessible pagination links with previous/next and numbered pages.

## Comparison Table Contents

The shipped comparison table includes rows for:

- Card Image
- Bank/Issuer
- Network Type
- Customer Rating
- Annual Fee
- Joining Fee
- Welcome Bonus
- Reward Rate
- Credit Limit
- Interest Rate
- Processing Time
- Minimum Income
- Apply button

## Shortcodes

The plugin registers five shortcodes.

### 1. `[compare-card]`

Purpose:

- Renders a comparison table for specific card IDs

Attributes:

- `ids` required, comma-separated card IDs

Example:

```text
[compare-card ids="12,34,56"]
```

### 2. `[credit-card]`

Purpose:

- Renders a single credit card card-view block

Attributes:

- `id`
- `mode` with supported values `mini` or `full`
- `show_image`
- `show_rating`
- `show_fees`
- `show_benefits`

Example:

```text
[credit-card id="12" mode="mini" show_rating="yes"]
```

Rendering notes:

- `mini` mode renders a compact card block with compare toggle and apply CTA
- `full` mode renders an expanded block with collapsible content sections
- The shortcode includes its own comparison bar markup and JavaScript behavior

### 3. `[credit_card_grid]`

Purpose:

- Queries credit-card posts directly and renders a shortcode grid with optional filters

Attributes:

- `count`
- `bank`
- `category`
- `network_type`
- `featured`
- `min_rating`
- `max_annual_fee`
- `sort_by`
- `sort_order`
- `show_filters`

Example:

```text
[credit_card_grid count="6" bank="hdfc-bank" featured="1" sort_by="rating" sort_order="desc"]
```

### 4. `[ccm_filters]`

Purpose:

- Renders a standalone filter form powered by the custom filter endpoint

Attributes:

- `show_banks`
- `show_networks`
- `show_categories`
- `show_rating`
- `show_fees`
- `ajax`

Example:

```text
[ccm_filters show_banks="1" show_categories="1" ajax="true"]
```

### 5. `[ccm_cards_grid]`

Purpose:

- Fetches cards from the custom REST API and renders a grid

Attributes:

- `limit`
- `bank`
- `network_type`
- `category`
- `featured`
- `trending`
- `min_rating`
- `sort_by`
- `sort_order`
- `show_filters`

Example:

```text
[ccm_cards_grid limit="12" category="travel" sort_by="rating" sort_order="desc"]
```

## REST API Endpoints

### WordPress core REST exposure

Because the custom post type is registered with `show_in_rest => true`, credit cards are also available through the normal WordPress posts controller for that post type.

Expected core endpoint:

- `/wp-json/wp/v2/credit-cards`

### Custom API endpoints

The plugin also registers custom endpoints under `ccm/v1`.

1. List credit cards

- `GET /wp-json/ccm/v1/credit-cards`

Supported query arguments include:

- `bank`
- `network_type`
- `category`
- `min_rating`
- `max_annual_fee`
- `featured`
- `trending`
- `min_income_range`
- `sort_by`
- `sort_order`
- `per_page`
- `page`
- `s`

Response includes:

- `data`
- `pagination`
- `filters_applied`

2. Single credit card

- `GET /wp-json/ccm/v1/credit-cards/{id}`

3. Available filters and facets

- `GET /wp-json/ccm/v1/credit-cards/filters`

Filters payload includes:

- Banks
- Network types
- Categories
- Rating ranges
- Fee ranges
- Income ranges

## Helper Functions And Utilities

Important helper functions include:

- `ccm_get_meta()`
- `ccm_format_currency()`
- `ccm_get_icon()`
- `ccm_get_card_terms()`
- `ccm_get_card_bank()`
- `ccm_get_card_network()`
- `ccm_render_rating()`
- `ccm_sanitize_compare_ids()`
- `ccm_load_template()`
- `ccm_generate_dynamic_faqs()`
- `ccm_get_card_faqs()`
- `ccm_generate_faq_schema()`
- `ccm_get_filters_data()`

Frontend/template helper functions also include:

- `ccm_get_credit_card()`
- `ccm_display_card_info()`
- `ccm_display_filters()`

## Caching And Performance

### Numeric cache table

On activation, the plugin creates a dedicated table:

- `{wp_prefix}credit_card_meta_cache`

Stored columns include:

- `post_id`
- `annual_fee_numeric`
- `min_income_numeric`
- `rating_numeric`
- `review_count_numeric`
- `featured`
- `trending`
- `updated_at`

This table is intended to speed up filtering and sorting operations.

### Filter response caching

The helper `ccm_get_filters_data()` caches filter payloads in a transient:

- Transient key: `ccm_filters_data`
- Cache length: 1 hour

### Conditional asset loading

The frontend class only enqueues CSS and JS where the plugin is actually used, reducing unnecessary asset loading on unrelated pages.

## Activation And Rewrite Rules

On plugin activation the core class:

- Initializes components
- Flushes rewrite rules
- Creates the numeric meta cache table

The plugin also adds rewrite rules for the compare page:

- `^compare-cards/?$`
- `^compare-cards/([^/]+)/?$`

Custom query vars added:

- `credit_card_compare`
- `cards`

## Design Notes

### Single page design

The single template is designed as a polished editorial product page with:

- A strong hero banner
- Sticky sub-navigation
- Multi-section long-form layout
- Highlight cards for top stats
- Dedicated fees, rewards, and eligibility sections
- FAQ accordion
- Related content blocks
- Comment support

### Archive page design

The archive page is designed as a category landing page with:

- SEO copy at the top
- Sidebar filtering
- Mobile modal filters
- Sort controls
- Card grid layout
- Compare selection controls
- Progressive loading via load-more UI

### Comparison page design

The compare template is designed as a decision-support screen with:

- Side-by-side overview blocks
- Detailed feature matrix
- Print action
- Comparison-specific metadata and schema

## Important Implementation Notes

These points are useful if you extend or maintain the plugin:

1. The active category taxonomy used across most runtime code is `card-category`.
2. Some legacy constants and helper references still mention `category`, so if you extend the plugin, use `card-category` consistently unless you are deliberately preserving backward compatibility.
3. The plugin depends on an existing `store` taxonomy being available or attachable.
4. The compare UX uses both server-side compare routes and client-side localStorage compare selections.
5. The plugin mixes direct WP_Query rendering and REST-driven rendering depending on the template or shortcode being used.

## File Map

### Core and setup

- `plugin.php`
- `includes/config.php`
- `includes/class-plugin-core.php`

### Content model and API

- `includes/class-post-types.php`
- `includes/api.php`
- `includes/helper-functions.php`

### Admin and frontend

- `includes/class-admin.php`
- `includes/class-frontend.php`
- `includes/shortcodes.php`

### Templates

- `templates/archive-credit-card.php`
- `templates/single-credit-card.php`
- `templates/page-compare-cards.php`
- `templates/template-parts/card-item.php`
- `templates/template-parts/compare-table.php`
- `templates/template-parts/filter-section.php`
- `templates/template-parts/pagination.php`

### Assets

- `assets/admin.css`
- `assets/admin.js`
- `assets/archive-responsive.css`
- `assets/frontend.css`
- `assets/frontend.js`
- `assets/shared-utilities.css`
- `assets/shortcodes.css`

## Typical Content Workflow

1. Create a new `Credit Card` post in wp-admin.
2. Assign bank/issuer, network type, and card category terms.
3. Fill in fees, rewards, eligibility, pros/cons, features, and FAQs.
4. Upload a featured image for the card artwork.
5. Publish the card.
6. Display cards through the archive, the single template, a grid shortcode, a single-card shortcode, or the compare experience.

## Quick Examples

Single card embed:

```text
[credit-card id="101" mode="full"]
```

Comparison table:

```text
[compare-card ids="101,102,103"]
```

Grid with filters:

```text
[credit_card_grid count="9" featured="1" show_filters="yes"]
```

REST request example:

```text
/wp-json/ccm/v1/credit-cards?bank=hdfc-bank&category=travel&sort_by=rating&sort_order=desc
```

`

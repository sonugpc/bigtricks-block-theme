<?php
/**
 * Bigtricks Theme Functions
 *
 * @package Bigtricks
 */

declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BIGTRICKS_VERSION', '1.0.6' );
define( 'BIGTRICKS_DIR', get_template_directory() );
define( 'BIGTRICKS_URI', get_template_directory_uri() );

// ── GitHub Auto-Updater ───────────────────────────────────────────────────────
// Set BIGTRICKS_GITHUB_OWNER and BIGTRICKS_GITHUB_REPO in wp-config.php.
// Optionally set BIGTRICKS_GITHUB_TOKEN for private repos.
if ( defined( 'BIGTRICKS_GITHUB_OWNER' ) && defined( 'BIGTRICKS_GITHUB_REPO' ) ) {
	require_once BIGTRICKS_DIR . '/inc/class-github-updater.php';
	new Bigtricks_GitHub_Updater(
		constant( 'BIGTRICKS_GITHUB_OWNER' ),
		constant( 'BIGTRICKS_GITHUB_REPO' )
	);
}

// ─────────────────────────────────────────────
// 1. Theme setup
// ─────────────────────────────────────────────

add_action( 'after_setup_theme', function () {
	load_theme_textdomain( 'bigtricks', BIGTRICKS_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ] );
	add_theme_support( 'custom-logo', [
		'height'      => 60,
		'width'       => 200,
		'flex-width'  => true,
		'flex-height' => true,
	] );
	add_theme_support( 'align-wide' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'responsive-embeds' );

	// Editor style for Gutenberg WYSIWYG
	add_editor_style( 'assets/css/editor-style.css' );

	register_nav_menus( [
		'primary' => esc_html__( 'Primary Navigation', 'bigtricks' ),
		'mobile'  => esc_html__( 'Mobile Menu', 'bigtricks' ),
		'footer'  => esc_html__( 'Footer Navigation', 'bigtricks' ),
	] );
} );

// ─────────────────────────────────────────────
// 2. Enqueue assets
// ─────────────────────────────────────────────

add_action( 'wp_enqueue_scripts', function () {
	// Google Fonts: Plus Jakarta Sans + Inter
	wp_enqueue_style(
		'bigtricks-google-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Plus+Jakarta+Sans:wght@400;500;700;800;900&display=swap',
		[],
		null
	);

	// Tailwind CSS (Compiled version)
	wp_enqueue_style(
		'bigtricks-tailwind',
		BIGTRICKS_URI . '/assets/css/bigtricks-tailwind.css',
		[],
		BIGTRICKS_VERSION
	);

	// Lucide icons — dev loads full CDN (all icons), prod loads slim custom bundle.
	// Dev:  define( 'SCRIPT_DEBUG', true ) in wp-config.php  → unpkg CDN, all 1500+ icons.
	// Prod: npm run build:icons regenerates assets/js/lucide-custom.js (78 icons, ~16 KB).
	if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
		wp_enqueue_script(
			'lucide-icons',
			'https://unpkg.com/lucide@latest/dist/umd/lucide.min.js',
			[],
			null,
			true
		);
	} else {
		wp_enqueue_script(
			'lucide-icons',
			BIGTRICKS_URI . '/assets/js/lucide-custom.js',
			[],
			BIGTRICKS_VERSION,
			true
		);
	}

	// Theme main stylesheet (depends on tailwind so it loads after)
	wp_enqueue_style(
		'bigtricks-style',
		BIGTRICKS_URI . '/assets/css/bigtricks.css',
		[ 'bigtricks-tailwind' ],
		BIGTRICKS_VERSION
	);

	// Theme main scripts
	wp_enqueue_script(
		'bigtricks-main',
		BIGTRICKS_URI . '/assets/js/main.js',
		[ 'lucide-icons' ],
		BIGTRICKS_VERSION,
		true
	);

	// Forms JavaScript - only load if contact/advertise form blocks are present
	if ( has_block( 'bigtricks/contact-form' ) || has_block( 'bigtricks/advertise-form' ) ) {
		wp_enqueue_script(
			'bigtricks-forms',
			BIGTRICKS_URI . '/assets/js/forms.js',
			[ 'bigtricks-main' ],
			BIGTRICKS_VERSION,
			true
		);
	}

	wp_localize_script( 'bigtricks-main', 'bigtricksData', [
		'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
		'loadMoreNonce' => wp_create_nonce( 'bigtricks_load_more' ),
		'restUrl'       => esc_url_raw( rest_url() ),
		'siteUrl'       => esc_url( home_url() ),
	] );

	if ( is_singular( 'referral-codes' ) ) {
		wp_enqueue_script(
			'bigtricks-referral-single',
			BIGTRICKS_URI . '/assets/js/referral-single.js',
			[ 'bigtricks-main' ],
			BIGTRICKS_VERSION,
			true
		);

		wp_localize_script(
			'bigtricks-referral-single',
			'bigtricksReferralSingle',
			[
				'isSubmitted' => isset( $_GET['submitted'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['submitted'] ) ),
			]
		);
	}
} );

// Apply dark mode class before first paint.
add_action( 'wp_head', function () {
        // Anti-FOUC: apply dark mode class before first paint.
        // Must be priority 5 so the class is set before any rendering.
        echo '<script>!function(){var s=localStorage.getItem("bt_dark_mode");if(s==="1"||(s===null&&window.matchMedia&&window.matchMedia("(prefers-color-scheme: dark)").matches)){document.documentElement.classList.add("dark")}}</script>' . "\n";
}, 5 );

// Preconnect to Google Fonts domains — eliminates DNS lookup latency from critical path.
// Priority 1 ensures these appear before any stylesheet <link> tags.
add_action( 'wp_head', function () {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 1 );

// Make Google Fonts non-render-blocking using rel="preload" as="style".
// Higher browser fetch priority than the media="print" trick, and the
// W3C-recommended pattern. The noscript fallback covers JS-disabled browsers.
add_filter( 'style_loader_tag', function ( string $html, string $handle ) : string {
	if ( 'bigtricks-google-fonts' !== $handle ) {
		return $html;
	}
	$preload = str_replace( "rel='stylesheet'", "rel='preload' as='style'", $html );
	$preload = str_replace( ' />', ' onload="this.onload=null;this.rel=\'stylesheet\'" />', $preload );
	return $preload . '<noscript>' . $html . '</noscript>' . "\n";
}, 10, 2 );

// In dev mode the Lucide CDN is loaded async so it doesn't hold up the parser.
// The onload callback fires after the script executes (lucide global is set),
// ensuring icons are rendered even if main.js ran first with lucide not yet defined.
add_filter( 'script_loader_tag', function ( string $tag, string $handle ) : string {
	if ( 'lucide-icons' !== $handle ) {
		return $tag;
	}
	if ( ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) {
		return $tag;
	}
	return str_replace( '<script ', '<script async onload="lucide.createIcons()" ', $tag );
}, 10, 2 );



// ─────────────────────────────────────────────
// 3. Post types are registered by plugins:
//    deal           → bigtricks-deals-wordpress
//    referral-codes → referral-code-plugin
//    credit-card    → credit-card-manager
// The 'store' taxonomy is shared across all three.
// ─────────────────────────────────────────────

/**
 * Force theme's single-{cpt}.php template for all custom post types,
 * overriding any template a plugin might try to inject.
 */
function bigtricks_force_cpt_template( string $template ): string {
	$map = [
		'deal'           => 'single-deal.php',
		'credit-card'    => 'single-credit-card.php',
		'referral-codes' => 'single-referral-codes.php',
	];
	foreach ( $map as $cpt => $file ) {
		if ( is_singular( $cpt ) ) {
			$theme_template = locate_template( $file, false, false );
			return ( $theme_template && file_exists( $theme_template ) ) ? $theme_template : $template;
		}
	}
	return $template;
}

// Run late so theme template wins over plugin-provided templates.
add_filter( 'single_template',  'bigtricks_force_cpt_template', 999 );
add_filter( 'template_include', 'bigtricks_force_cpt_template', 999 );


// ─────────────────────────────────────────────
// 6. Widget areas / sidebars
// ─────────────────────────────────────────────

add_action( 'widgets_init', function () {
	register_sidebar( [
		'name'          => esc_html__( 'Right Sidebar', 'bigtricks' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here for the right sidebar.', 'bigtricks' ),
		'before_widget' => '<div id="%1$s" class="widget bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<div class="bg-slate-50 px-6 py-5 border-b border-slate-200 font-black text-slate-900 text-lg">',
		'after_title'   => '</div>',
	] );
} );

// ─────────────────────────────────────────────
// 7. Bigtricks template helper functions
// ─────────────────────────────────────────────

/**
 * Get the type badge HTML — dispatches on the post's actual CPT.
 */
function bigtricks_deal_type_badge( int $post_id ): string {
	$post_type = get_post_type( $post_id );

	switch ( $post_type ) {
		case 'deal':
			return '<div class="bg-red-50 text-red-600 font-black text-xs px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5 border border-red-100">'
				. '<i data-lucide="flame" class="w-3.5 h-3.5 fill-current shrink-0"></i> Deal'
				. '</div>';

		case 'referral-codes':
			return '<div class="bg-emerald-50 text-emerald-600 font-black text-xs px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5 border border-emerald-100">'
				. '<i data-lucide="gift" class="w-3.5 h-3.5 shrink-0"></i> Referral'
				. '</div>';

		case 'credit-card':
			return '<div class="bg-purple-50 text-purple-600 font-black text-xs px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5 border border-purple-100">'
				. '<i data-lucide="credit-card" class="w-3.5 h-3.5 shrink-0"></i> Credit Card'
				. '</div>';

		case 'post':
		default:
			return '<div class="bg-blue-50 text-blue-600 font-black text-xs px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5 border border-blue-100">'
				. '<i data-lucide="book-open" class="w-3.5 h-3.5 shrink-0"></i> Offer'
				. '</div>';
	}
}

/**
 * Get the primary CTA button — dispatches on the post's actual CPT.
 */
function bigtricks_deal_cta_button( int $post_id, string $size = 'normal' ): string {
	$post_type = get_post_type( $post_id );
	$permalink = esc_url( (string) get_permalink( $post_id ) );

	$base_classes = $size === 'large'
		? 'w-full justify-center px-8 py-4 rounded-2xl text-lg font-black flex items-center gap-2 shadow-xl transition-all active:scale-95'
		: 'inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm font-black shadow-md transition-all active:scale-95';

	switch ( $post_type ) {
		case 'deal':
			$offer_url = esc_url( (string) get_post_meta( $post_id, '_btdeals_offer_url', true ) );
			$href      = $offer_url ?: $permalink;
			return '<a href="' . $href . '" target="_blank" rel="noopener noreferrer nofollow" class="' . $base_classes . ' bg-primary-600 hover:bg-primary-700 text-white shadow-primary-200 dark:shadow-none">'
				. 'Get Deal <i data-lucide="external-link" class="w-4 h-4 shrink-0"></i></a>';

		case 'referral-codes':
			$code = sanitize_text_field( (string) get_post_meta( $post_id, 'referral_code', true ) );
			if ( $code ) {
				return '<button class="bt-copy-code ' . $base_classes . ' bg-emerald-500 hover:bg-emerald-600 text-white shadow-emerald-200 border-2 border-emerald-500" data-code="' . esc_attr( $code ) . '">'
					. '<i data-lucide="copy" class="w-4 h-4 shrink-0"></i> ' . esc_html( $code ) . '</button>';
			}
			$referral_link = esc_url( (string) get_post_meta( $post_id, 'referral_link', true ) );
			$href          = $referral_link ?: $permalink;
			return '<a href="' . $href . '" target="_blank" rel="noopener noreferrer nofollow" class="' . $base_classes . ' bg-emerald-500 hover:bg-emerald-600 text-white shadow-emerald-200">'
				. 'Get Referral <i data-lucide="external-link" class="w-4 h-4 shrink-0"></i></a>';

		case 'credit-card':
			$apply_link = esc_url( (string) get_post_meta( $post_id, 'apply_link', true ) );
			$href       = $apply_link ?: $permalink;
			return '<a href="' . $href . '" target="_blank" rel="noopener noreferrer nofollow" class="' . $base_classes . ' bg-purple-600 hover:bg-purple-700 text-white shadow-purple-200">'
				. 'Apply Now <i data-lucide="external-link" class="w-4 h-4 shrink-0"></i></a>';

		case 'post':
		default:
			return '<a href="' . $permalink . '" class="' . $base_classes . ' bg-primary-600 hover:bg-primary-700 text-white shadow-primary-200 dark:shadow-none">'
				. 'Read Article <i data-lucide="chevron-right" class="w-4 h-4 shrink-0"></i></a>';
	}
}

/**
 * Get placeholder image URL if no featured image.
 */
function bigtricks_get_thumbnail_url( int $post_id, string $size = 'medium_large' ): string {
	if ( has_post_thumbnail( $post_id ) ) {
		return esc_url( (string) get_the_post_thumbnail_url( $post_id, $size ) );
	}
	return esc_url( BIGTRICKS_URI . '/assets/images/placeholder.svg' );
}

/**
 * Human-readable relative time for templates/widgets.
 */
function bigtricks_time_ago( int $timestamp ): string {
	$time_ago = human_time_diff( $timestamp, time() );
	return sprintf( __( '%s ago', 'bigtricks' ), $time_ago );
}

/**
 * Get top categories for sidebar/footer.
 */
function bigtricks_get_top_categories( int $limit = 8 ): array {
	$categories = get_categories( [
		'orderby'  => 'count',
		'order'    => 'DESC',
		'number'   => $limit,
		'exclude'  => get_option( 'default_category' ),
		'hide_empty' => true,
	] );

	return is_wp_error( $categories ) ? [] : $categories;
}

/**
 * Check whether breadcrumbs should be shown.
 */
function bigtricks_should_show_breadcrumbs(): bool {
	if ( bigtricks_option( 'bt_show_breadcrumbs', '1' ) !== '1' ) {
		return false;
	}

	if ( is_front_page() || is_home() ) {
		return false;
	}

	return true;
}

/**
 * Build breadcrumb items for the current request.
 */
function bigtricks_get_breadcrumb_items(): array {
	$items = [
		[
			'label' => __( 'Home', 'bigtricks' ),
			'url'   => home_url( '/' ),
		],
	];

	if ( is_singular( 'post' ) ) {
		$items[] = [ 'label' => get_the_title(), 'url' => '' ];
		return $items;
	}

	if ( is_singular( [ 'deal', 'referral-codes', 'credit-card' ] ) ) {
		$post_type = get_post_type();

		if ( $post_type === 'deal' ) {
			$archive_link = get_post_type_archive_link( 'deal' );
			$items[] = [
				'label' => __( 'Deals', 'bigtricks' ),
				'url'   => $archive_link ? $archive_link : home_url( '/loot-deals/' ),
			];
		} elseif ( $post_type === 'referral-codes' ) {
			$archive_link = get_post_type_archive_link( 'referral-codes' );
			$items[] = [
				'label' => __( 'Referral Codes', 'bigtricks' ),
				'url'   => $archive_link ? $archive_link : home_url( '/referral-codes/' ),
			];
		} elseif ( $post_type === 'credit-card' ) {
			$archive_link = get_post_type_archive_link( 'credit-card' );
			$items[] = [
				'label' => __( 'Credit Cards', 'bigtricks' ),
				'url'   => $archive_link ? $archive_link : home_url( '/credit-cards/' ),
			];
		}

		$categories = get_the_category();
		if ( ! empty( $categories ) ) {
			$items[] = [
				'label' => $categories[0]->name,
				'url'   => get_category_link( $categories[0]->term_id ),
			];
		}

		$items[] = [ 'label' => get_the_title(), 'url' => '' ];
		return $items;
	}

	if ( is_category() ) {
		$category = get_queried_object();
		$items[] = [ 'label' => __( 'Categories', 'bigtricks' ), 'url' => home_url( '/category/' ) ];
		$items[] = [ 'label' => $category->name, 'url' => '' ];
		return $items;
	}

	if ( is_tax( 'store' ) ) {
		$term = get_queried_object();
		$items[] = [ 'label' => __( 'Stores', 'bigtricks' ), 'url' => home_url( '/store/' ) ];
		$items[] = [ 'label' => $term->name, 'url' => '' ];
		return $items;
	}

	if ( is_singular( 'store' ) ) {
		$items[] = [ 'label' => __( 'Stores', 'bigtricks' ), 'url' => home_url( '/store/' ) ];
		$items[] = [ 'label' => get_the_title(), 'url' => '' ];
		return $items;
	}

	if ( is_post_type_archive( 'deal' ) ) {
		$items[] = [ 'label' => __( 'Deals', 'bigtricks' ), 'url' => '' ];
		return $items;
	}

	if ( is_post_type_archive( 'referral-codes' ) ) {
		$items[] = [ 'label' => __( 'Referral Codes', 'bigtricks' ), 'url' => '' ];
		return $items;
	}

	if ( is_post_type_archive( 'credit-card' ) ) {
		$items[] = [ 'label' => __( 'Credit Cards', 'bigtricks' ), 'url' => '' ];
		return $items;
	}

	if ( is_page() ) {
		$ancestors = array_reverse( get_post_ancestors( get_the_ID() ) );
		foreach ( $ancestors as $ancestor_id ) {
			$items[] = [
				'label' => get_the_title( $ancestor_id ),
				'url'   => get_permalink( $ancestor_id ),
			];
		}
		$items[] = [ 'label' => get_the_title(), 'url' => '' ];
		return $items;
	}

	if ( is_search() ) {
		$items[] = [ 'label' => __( 'Search', 'bigtricks' ), 'url' => '' ];
		$items[] = [ 'label' => get_search_query(), 'url' => '' ];
		return $items;
	}

	if ( is_tag() ) {
		$tag = get_queried_object();
		$items[] = [ 'label' => __( 'Tags', 'bigtricks' ), 'url' => '' ];
		$items[] = [ 'label' => $tag->name, 'url' => '' ];
		return $items;
	}

	if ( is_author() ) {
		$items[] = [ 'label' => __( 'Author', 'bigtricks' ), 'url' => '' ];
		$items[] = [ 'label' => get_the_author_meta( 'display_name', (int) get_query_var( 'author' ) ), 'url' => '' ];
		return $items;
	}

	if ( is_date() ) {
		$items[] = [ 'label' => __( 'Archives', 'bigtricks' ), 'url' => '' ];
		$items[] = [ 'label' => get_the_archive_title(), 'url' => '' ];
		return $items;
	}

	if ( is_404() ) {
		$items[] = [ 'label' => __( '404', 'bigtricks' ), 'url' => '' ];
		return $items;
	}

	$items[] = [ 'label' => get_the_archive_title(), 'url' => '' ];
	return $items;
}

/**
 * Display breadcrumbs navigation using the component template.
 */
function bigtricks_breadcrumbs(): void {
	if ( ! bigtricks_should_show_breadcrumbs() ) {
		return;
	}

	get_template_part( 'template-parts/breadcrumb' );
}

/**
 * Mobile menu fallback — renders default mobile menu if no menu is assigned.
 */
function bigtricks_mobile_menu_fallback(): void {
	?>
	<ul role="list" class="list-none m-0 p-0">
		<li>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="px-6 py-3 flex items-center gap-3 font-bold text-slate-600 hover:bg-slate-50 border-l-4 border-transparent hover:border-primary-300 transition-colors">
				<i data-lucide="home" class="w-5 h-5 text-primary-500"></i>
				<?php esc_html_e( 'Home', 'bigtricks' ); ?>
			</a>
		</li>
		<li>
			<a href="<?php echo esc_url( home_url( '/store/' ) ); ?>" class="px-6 py-3 flex items-center gap-3 font-bold text-slate-600 hover:bg-slate-50 border-l-4 border-transparent hover:border-primary-300 transition-colors">
				<i data-lucide="shopping-bag" class="w-5 h-5 text-primary-500"></i>
				<?php esc_html_e( 'Stores', 'bigtricks' ); ?>
			</a>
		</li>
		<li>
			<a href="<?php echo esc_url( home_url( '/category/credit-cards/' ) ); ?>" class="px-6 py-3 flex items-center gap-3 font-bold text-slate-600 hover:bg-slate-50 border-l-4 border-transparent hover:border-primary-300 transition-colors">
				<i data-lucide="credit-card" class="w-5 h-5 text-primary-500"></i>
				<?php esc_html_e( 'Credit Cards', 'bigtricks' ); ?>
			</a>
		</li>
		<li>
			<a href="<?php echo esc_url( home_url( '/blog/' ) ); ?>" class="px-6 py-3 flex items-center gap-3 font-bold text-slate-600 hover:bg-slate-50 border-l-4 border-transparent hover:border-primary-300 transition-colors">
				<i data-lucide="book-open" class="w-5 h-5 text-primary-500"></i>
				<?php esc_html_e( 'Blog', 'bigtricks' ); ?>
			</a>
		</li>
	</ul>
	<?php
}

// ─────────────────────────────────────────────
// 8. (Upvote handler removed — upvotes are not used)
// ─────────────────────────────────────────────

// ─────────────────────────────────────────────
// 9. Flush rewrite rules on theme activation
// ─────────────────────────────────────────────

add_action( 'after_switch_theme', function () {
	flush_rewrite_rules();
} );

// ─────────────────────────────────────────────
// 10. AJAX: Load More posts (homepage + category)
// ─────────────────────────────────────────────

add_action( 'wp_ajax_bigtricks_load_more', 'bigtricks_ajax_load_more' );
add_action( 'wp_ajax_nopriv_bigtricks_load_more', 'bigtricks_ajax_load_more' );

function bigtricks_ajax_load_more(): void {
	check_ajax_referer( 'bigtricks_load_more', 'nonce' );

	$page     = isset( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
	$cat      = isset( $_POST['cat'] )  ? absint( $_POST['cat'] )  : 0;
	$store    = isset( $_POST['store'] ) ? absint( $_POST['store'] ) : 0;
        $card_cat = isset( $_POST['card_cat'] ) ? absint( $_POST['card_cat'] ) : 0;
        $type_raw = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : 'all';

        $allowed_types = [ 'all', 'post', 'deal', 'referral-codes', 'credit-card' ];
        $type          = in_array( $type_raw, $allowed_types, true ) ? $type_raw : 'all';

        $post_types = $type === 'all'
                ? [ 'post', 'deal', 'referral-codes', 'credit-card' ]
                : [ $type ];

        $args = [
                'post_type'      => $post_types,
                'post_status'    => 'publish',
                'posts_per_page' => 12,
                'paged'          => $page,
                'orderby'        => 'date',
                'order'          => 'DESC',
        ];

        if ( $cat > 0 ) {
                $args['tax_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery
                        [
                                'taxonomy' => 'category',
                                'field'    => 'term_id',
                                'terms'    => $cat,
                        ],
                ];
        } elseif ( $store > 0 ) {
                $args['tax_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery
                        [
                                'taxonomy' => 'store',
                                'field'    => 'term_id',
                                'terms'    => $store,
                        ],
                ];
        } elseif ( $card_cat > 0 ) {
                $args['tax_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery
                        [
                                'taxonomy' => 'card-category',
                                'field'    => 'term_id',
                                'terms'    => $card_cat,
                        ],
                ];
        }

	// Map CPT slug → template-part filename (without 'template-parts/' prefix)
	$template_map = [
		'post'           => 'card-post',
		'deal'           => 'card-deal',
		'referral-codes' => 'card-referral-code',
		'credit-card'    => 'card-credit-card',
	];

	$query = new WP_Query( $args );

	ob_start();
	while ( $query->have_posts() ) :
		$query->the_post();
		$pid           = get_the_ID();
		$current_type  = get_post_type();
		$template_slug = $template_map[ $current_type ] ?? 'card-post';
		get_template_part( 'template-parts/' . $template_slug, null, [ 'post_id' => $pid ] );
	endwhile;
	wp_reset_postdata();

	$html     = ob_get_clean();
	$has_more = ( $page < $query->max_num_pages );

	wp_send_json_success( [ 'html' => $html, 'has_more' => $has_more ] );
}

// ─────────────────────────────────────────────
// 10b. Modify search query to include all post types
// ─────────────────────────────────────────────

add_action( 'pre_get_posts', function ( WP_Query $query ): void {
	// Only modify main query on search pages (not in admin)
	if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
		$query->set( 'post_type', [ 'post', 'deal', 'referral-codes', 'credit-card' ] );
	}
} );

// ─────────────────────────────────────────────
// 10c. AJAX Search Handler (for autocomplete/instant search)
// ─────────────────────────────────────────────

add_action( 'wp_ajax_bigtricks_ajax_search', 'bigtricks_ajax_search_handler' );
add_action( 'wp_ajax_nopriv_bigtricks_ajax_search', 'bigtricks_ajax_search_handler' );

function bigtricks_ajax_search_handler(): void {
	$search_query = isset( $_POST['s'] ) ? sanitize_text_field( wp_unslash( $_POST['s'] ) ) : '';
	
	if ( empty( $search_query ) || strlen( $search_query ) < 2 ) {
		wp_send_json_success( [ 'results' => [], 'count' => 0 ] );
		return;
	}

	$args = [
		'post_type'      => [ 'post', 'deal', 'referral-codes', 'credit-card' ],
		'post_status'    => 'publish',
		'posts_per_page' => 8, // Limit autocomplete results
		's'              => $search_query,
		'orderby'        => 'relevance',
		'order'          => 'DESC',
	];

	$query   = new WP_Query( $args );
	$results = [];

	// Post type labels
	$type_labels = [
		'post'           => __( 'Article', 'bigtricks' ),
		'deal'           => __( 'Deal', 'bigtricks' ),
		'referral-codes' => __( 'Referral Code', 'bigtricks' ),
		'credit-card'    => __( 'Credit Card', 'bigtricks' ),
	];

	while ( $query->have_posts() ) {
		$query->the_post();
		$pid        = get_the_ID();
		$post_type  = get_post_type();
		$thumb_url  = bigtricks_get_thumbnail_url( $pid, 'thumbnail' );

		$results[] = [
			'id'        => $pid,
			'title'     => get_the_title(),
			'url'       => get_permalink(),
			'excerpt'   => wp_trim_words( get_the_excerpt(), 15, '...' ),
			'type'      => $post_type,
			'type_label' => $type_labels[ $post_type ] ?? $post_type,
			'thumbnail' => $thumb_url,
		];
	}
	wp_reset_postdata();

	wp_send_json_success( [
		'results' => $results,
		'count'   => $query->found_posts,
		'query'   => $search_query,
	] );
}

// ─────────────────────────────────────────────
// 11. Term meta: tag-image for categories + store meta (tag-image, st_link)
// ─────────────────────────────────────────────

add_action( 'init', function () {
	register_term_meta( 'category', 'tag-image', [
		'type'              => 'string',
		'description'       => 'Category icon URL or attachment ID',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
		'auth_callback'     => function () { return current_user_can( 'manage_categories' ); },
		'show_in_rest'      => true,
	] );

	// Store taxonomy: logo image (key: thumb_image)
	register_term_meta( 'store', 'thumb_image', [
		'type'              => 'string',
		'description'       => 'Store logo URL or attachment ID',
		'single'            => true,
		'sanitize_callback' => 'sanitize_text_field',
		'auth_callback'     => function () { return current_user_can( 'manage_categories' ); },
		'show_in_rest'      => true,
	] );

	// Store taxonomy: official website link
	register_term_meta( 'store', 'st_link', [
		'type'              => 'string',
		'description'       => 'Store official website URL',
		'single'            => true,
		'sanitize_callback' => 'esc_url_raw',
		'auth_callback'     => function () { return current_user_can( 'manage_categories' ); },
		'show_in_rest'      => true,
	] );

	// Store taxonomy: featured flag (boolean)
	register_term_meta( 'store', 'featured_store', [
		'type'              => 'boolean',
		'description'       => 'Mark store as featured (prominently displayed)',
		'single'            => true,
		'sanitize_callback' => function ( $value ) { return (bool) $value; },
		'auth_callback'     => function () { return current_user_can( 'manage_categories' ); },
		'show_in_rest'      => true,
	] );
} );

// Admin UI: enqueue WP media library on term edit screens + theme settings page
add_action( 'admin_enqueue_scripts', function ( string $hook ) {
	$media_hooks = [ 'edit-tags.php', 'term.php', 'appearance_page_bigtricks-theme-settings' ];
	if ( ! in_array( $hook, $media_hooks, true ) ) {
		return;
	}
	wp_enqueue_media();
} );

add_action( 'admin_footer-edit-tags.php',                         'bigtricks_term_media_picker_js' );
add_action( 'admin_footer-term.php',                              'bigtricks_term_media_picker_js' );
add_action( 'admin_footer-appearance_page_bigtricks-theme-settings', 'bigtricks_term_media_picker_js' );

function bigtricks_term_media_picker_js(): void {
	?>
	<script>
	var taxonomies = document.querySelector( 'body' );
	if ( taxonomies && typeof wp !== 'undefined' && wp.media ) {
		jQuery(function( $ ){
			// Open media library
			$( 'body' ).on( 'click', '.bt-media-picker-btn', function ( e ) {
				e.preventDefault();
				var $wrap    = $( this ).closest( '.bt-media-picker-wrap' );
				var $input   = $wrap.find( '.bt-media-url-input' );
				var $preview = $wrap.find( '.bt-media-preview' );
				var frame = wp.media( {
					title   : $( this ).data( 'title' ) || 'Select Image',
					button  : { text: 'Use this image' },
					multiple: false,
					library : { type: 'image' },
				} );
				frame.on( 'select', function () {
					var att = frame.state().get( 'selection' ).first().toJSON();
					$input.val( att.url ).trigger( 'change' );
				} );
				frame.open();
			} );
			// Live preview on manual URL paste / clear
			$( 'body' ).on( 'change blur', '.bt-media-url-input', function () {
				var val      = $( this ).val();
				var $preview = $( this ).closest( '.bt-media-picker-wrap' ).find( '.bt-media-preview' );
				if ( val ) {
					$preview.html( '<img src="' + val + '" style="max-height:80px;margin-top:6px;border-radius:8px;display:block;" onerror="this.style.display=\'none\'">' );
				} else {
					$preview.html( '' );
				}
			} );
		});
	}
	</script>
	<?php
}

// Admin UI: category tag-image fields
add_action( 'category_add_form_fields', function () {
	wp_nonce_field( 'bt_cat_meta_nonce', 'bt_cat_meta_nonce_field' );
	?>
	<div class="form-field">
		<label for="bt-tag-image"><?php esc_html_e( 'Category Icon / Image', 'bigtricks' ); ?></label>
		<div class="bt-media-picker-wrap">
			<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
				<input type="text" id="bt-tag-image" name="bt_tag_image" value="" placeholder="https://... or select from Media Library" class="bt-media-url-input" style="flex:1;">
				<button type="button" class="button bt-media-picker-btn" data-title="<?php esc_attr_e( 'Select Category Icon', 'bigtricks' ); ?>">
					<span class="dashicons dashicons-format-image" style="margin-top:3px;"></span>
					<?php esc_html_e( 'Choose Image', 'bigtricks' ); ?>
				</button>
			</div>
			<div class="bt-media-preview"></div>
			<p class="description"><?php esc_html_e( 'Pick from the Media Library or paste an external URL.', 'bigtricks' ); ?></p>
		</div>
	</div>
	<?php
} );

add_action( 'category_edit_form_fields', function ( WP_Term $term ) {
	$val         = get_term_meta( $term->term_id, 'tag-image', true );
	$preview_url = '';
	if ( $val ) {
		$preview_url = is_numeric( $val )
			? (string) wp_get_attachment_image_url( (int) $val, 'medium' )
			: $val;
	}
	wp_nonce_field( 'bt_cat_meta_nonce', 'bt_cat_meta_nonce_field' );
	?>
	<tr class="form-field">
		<th scope="row"><label for="bt-tag-image"><?php esc_html_e( 'Category Icon / Image', 'bigtricks' ); ?></label></th>
		<td>
			<div class="bt-media-picker-wrap">
				<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
					<input type="text" id="bt-tag-image" name="bt_tag_image" value="<?php echo esc_attr( $val ); ?>" placeholder="https://... or select from Media Library" class="large-text bt-media-url-input">
					<button type="button" class="button bt-media-picker-btn" data-title="<?php esc_attr_e( 'Select Category Icon', 'bigtricks' ); ?>">
						<span class="dashicons dashicons-format-image" style="margin-top:3px;"></span>
						<?php esc_html_e( 'Choose Image', 'bigtricks' ); ?>
					</button>
				</div>
				<div class="bt-media-preview">
					<?php if ( $preview_url ) : ?>
					<img src="<?php echo esc_url( $preview_url ); ?>" alt="" style="max-height:80px;margin-top:4px;border-radius:8px;display:block;">
					<?php endif; ?>
				</div>
				<p class="description"><?php esc_html_e( 'Pick from the Media Library or paste an external URL.', 'bigtricks' ); ?></p>
			</div>
		</td>
	</tr>
	<?php
} );

add_action( 'created_category', function ( int $term_id ) {
	bigtricks_save_category_meta( $term_id );
} );

add_action( 'edited_category', function ( int $term_id ) {
	bigtricks_save_category_meta( $term_id );
} );

function bigtricks_save_category_meta( int $term_id ): void {
	if ( ! isset( $_POST['bt_cat_meta_nonce_field'] ) ) return;
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bt_cat_meta_nonce_field'] ) ), 'bt_cat_meta_nonce' ) ) return;
	if ( ! current_user_can( 'manage_categories' ) ) return;

	if ( isset( $_POST['bt_tag_image'] ) ) {
		$val = sanitize_text_field( wp_unslash( $_POST['bt_tag_image'] ) );
		update_term_meta( $term_id, 'tag-image', $val );
	}
}

// ─────────────────────────────────────────────
// 11b. Term meta admin UI for 'store' taxonomy
// ─────────────────────────────────────────────

add_action( 'store_add_form_fields', function () {
	wp_nonce_field( 'bt_store_meta_nonce', 'bt_store_meta_nonce_field' );
	?>
	<div class="form-field">
		<label for="bt-store-thumb-image"><?php esc_html_e( 'Store Logo', 'bigtricks' ); ?></label>
		<div class="bt-media-picker-wrap">
			<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
				<input type="text" id="bt-store-thumb-image" name="bt_store_thumb_image" value="" placeholder="https://... or select from Media Library" class="bt-media-url-input" style="flex:1;">
				<button type="button" class="button bt-media-picker-btn" data-title="<?php esc_attr_e( 'Select Store Logo', 'bigtricks' ); ?>">
					<span class="dashicons dashicons-format-image" style="margin-top:3px;"></span>
					<?php esc_html_e( 'Choose Image', 'bigtricks' ); ?>
				</button>
			</div>
			<div class="bt-media-preview"></div>
			<p class="description"><?php esc_html_e( 'Pick from the Media Library or paste an external image URL.', 'bigtricks' ); ?></p>
		</div>
	</div>
	<div class="form-field">
		<label for="bt-store-st-link"><?php esc_html_e( 'Store Website URL', 'bigtricks' ); ?></label>
		<input type="url" id="bt-store-st-link" name="bt_store_st_link" value="" placeholder="https://storedomain.com" class="large-text">
		<p class="description"><?php esc_html_e( 'Official store website. Used for the "Visit Store" button.', 'bigtricks' ); ?></p>
	</div>
	<div class="form-field">
		<label for="bt-store-featured"><input type="checkbox" id="bt-store-featured" name="bt_store_featured" value="1"> <?php esc_html_e( 'Featured Store', 'bigtricks' ); ?></label>
		<p class="description"><?php esc_html_e( 'Check to display this store with a featured badge and premium placement on the stores page.', 'bigtricks' ); ?></p>
	</div>
	<?php
} );

add_action( 'store_edit_form_fields', function ( WP_Term $term ) {
	$img_val     = get_term_meta( $term->term_id, 'thumb_image', true );
	$link_val    = get_term_meta( $term->term_id, 'st_link', true );
	$featured    = (bool) get_term_meta( $term->term_id, 'featured_store', true );
	$preview_url = '';
	if ( $img_val ) {
		$preview_url = is_numeric( $img_val )
			? (string) wp_get_attachment_image_url( (int) $img_val, 'medium' )
			: $img_val;
	}
	wp_nonce_field( 'bt_store_meta_nonce', 'bt_store_meta_nonce_field' );
	?>
	<tr class="form-field">
		<th scope="row"><label for="bt-store-thumb-image"><?php esc_html_e( 'Store Logo', 'bigtricks' ); ?></label></th>
		<td>
			<div class="bt-media-picker-wrap">
				<div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
					<input type="text" id="bt-store-thumb-image" name="bt_store_thumb_image" value="<?php echo esc_attr( $img_val ); ?>" placeholder="https://... or select from Media Library" class="large-text bt-media-url-input">
					<button type="button" class="button bt-media-picker-btn" data-title="<?php esc_attr_e( 'Select Store Logo', 'bigtricks' ); ?>">
						<span class="dashicons dashicons-format-image" style="margin-top:3px;"></span>
						<?php esc_html_e( 'Choose Image', 'bigtricks' ); ?>
					</button>
				</div>
				<div class="bt-media-preview">
					<?php if ( $preview_url ) : ?>
					<img src="<?php echo esc_url( $preview_url ); ?>" alt="" style="max-height:80px;margin-top:4px;border-radius:8px;display:block;">
					<?php endif; ?>
				</div>
				<p class="description"><?php esc_html_e( 'Pick from the Media Library or paste an external image URL.', 'bigtricks' ); ?></p>
			</div>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="bt-store-st-link"><?php esc_html_e( 'Store Website URL', 'bigtricks' ); ?></label></th>
		<td>
			<input type="url" id="bt-store-st-link" name="bt_store_st_link" value="<?php echo esc_attr( $link_val ); ?>" placeholder="https://storedomain.com" class="large-text">
			<p class="description"><?php esc_html_e( 'Official store website. Used for the "Visit Store" button.', 'bigtricks' ); ?></p>
		</td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="bt-store-featured"><?php esc_html_e( 'Featured Store', 'bigtricks' ); ?></label></th>
		<td>
			<label><input type="checkbox" id="bt-store-featured" name="bt_store_featured" value="1" <?php checked( $featured ); ?>> <?php esc_html_e( 'Highlight this store as featured', 'bigtricks' ); ?></label>
			<p class="description"><?php esc_html_e( 'Featured stores display with a badge and appear first on the stores page.', 'bigtricks' ); ?></p>
		</td>
	</tr>
	<?php
} );

add_action( 'created_store', function ( int $term_id ) {
	bigtricks_save_store_meta( $term_id );
} );

add_action( 'edited_store', function ( int $term_id ) {
	bigtricks_save_store_meta( $term_id );
} );

function bigtricks_save_store_meta( int $term_id ): void {
	if ( ! isset( $_POST['bt_store_meta_nonce_field'] ) ) return;
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bt_store_meta_nonce_field'] ) ), 'bt_store_meta_nonce' ) ) return;
	if ( ! current_user_can( 'manage_categories' ) ) return;

	if ( isset( $_POST['bt_store_thumb_image'] ) ) {
		$val = sanitize_text_field( wp_unslash( $_POST['bt_store_thumb_image'] ) );
		update_term_meta( $term_id, 'thumb_image', $val );
	}
	if ( isset( $_POST['bt_store_st_link'] ) ) {
		$val = esc_url_raw( wp_unslash( $_POST['bt_store_st_link'] ) );
		update_term_meta( $term_id, 'st_link', $val );
	}
	if ( isset( $_POST['bt_store_featured'] ) ) {
		update_term_meta( $term_id, 'featured_store', 1 );
	} else {
		update_term_meta( $term_id, 'featured_store', 0 );
	}
}

// ─────────────────────────────────────────────
// 12. Walker: nav menu with icon support (data-icon attribute on menu item)
// ─────────────────────────────────────────────

if ( ! class_exists( 'Bigtricks_Icon_Nav_Walker' ) ) :

class Bigtricks_Icon_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Dropdown submenu wrapper — shown on hover via group-hover:block.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "\n{$indent}<ul class=\"absolute top-full left-0 min-w-[200px] bg-white dark:bg-slate-900 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 py-2 z-50 hidden group-hover:block\">\n";
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "{$indent}</ul>\n";
	}

	public function start_el( &$output, $data_object, $depth = 0, $args = null, $id = 0 ) {
		$item         = $data_object;
		$indent       = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$classes      = empty( $item->classes ) ? [] : (array) $item->classes;
		$classes[]    = 'menu-item-' . $item->ID;
		$has_children = in_array( 'menu-item-has-children', $classes );
		$class_names  = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );

		// Detect icon from title — supports [icon] prefix notation: "[shopping-bag] Stores"
		$title = $item->title;
		$icon  = '';
		if ( preg_match( '/^\[([a-z0-9\-]+)\]\s*(.+)$/i', $title, $m ) ) {
			$icon  = esc_attr( $m[1] );
			$title = esc_html( trim( $m[2] ) );
		}

		$atts                 = [];
		$atts['title']        = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target']       = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']          = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']         = ! empty( $item->url ) ? $item->url : '';
		$atts['aria-current'] = $item->current ? 'page' : '';

		$is_active  = $item->current || $item->current_item_ancestor;
		$active_cls = $is_active ? ' text-primary-600' : '';

		if ( $depth === 0 ) {
			$atts['class'] = 'flex items-center gap-1.5 hover:text-primary-600 transition-colors font-bold text-slate-600 dark:text-slate-300 text-sm py-1' . $active_cls;
		} else {
			$atts['class'] = 'flex items-center gap-2.5 px-4 py-2.5 text-sm font-bold text-slate-700 dark:text-slate-200 hover:bg-primary-50 dark:hover:bg-slate-800 hover:text-primary-600 dark:hover:text-primary-400 transition-colors w-full whitespace-nowrap';
		}

		$attribs = '';
		foreach ( $atts as $attr => $value ) {
			if ( $value !== '' ) {
				$attribs .= ' ' . $attr . '="' . esc_attr( $value ) . '"';
			}
		}

		$icon_html = '<i data-lucide="' . ( $icon ?: 'link' ) . '" class="w-4 h-4 shrink-0"></i>';
		$chevron   = ( $has_children && $depth === 0 )
			? '<i data-lucide="chevron-down" class="w-3.5 h-3.5 shrink-0 transition-transform duration-200 group-hover:rotate-180"></i>'
			: '';

		// Add 'group' to depth-0 parent items that have children so hover works
		$li_base = $has_children && $depth === 0 ? 'group relative ' : 'relative ';
		$output .= $indent . '<li class="' . $li_base . esc_attr( $class_names ) . '">';
		$output .= '<a' . $attribs . '>' . $icon_html . '<span>' . $title . '</span>' . $chevron . '</a>';
	}
}

endif;

// ─────────────────────────────────────────────
// 12b. Walker: mobile nav menu — same icon support, mobile-optimised layout
// ─────────────────────────────────────────────

if ( ! class_exists( 'Bigtricks_Mobile_Nav_Walker' ) ) :

class Bigtricks_Mobile_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Accordion submenu — toggled by the chevron button via JS.
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$output .= '<ul class="bt-mobile-submenu hidden border-t border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/50">' . "\n";
	}

	public function end_lvl( &$output, $depth = 0, $args = null ) {
		$output .= "</ul>\n";
	}

	public function start_el( &$output, $data_object, $depth = 0, $args = null, $id = 0 ) {
		$item         = $data_object;
		$indent       = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$classes      = empty( $item->classes ) ? [] : (array) $item->classes;
		$classes[]    = 'menu-item-' . $item->ID;
		$has_children = in_array( 'menu-item-has-children', $classes );
		$class_names  = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );

		// Detect icon from title — supports [icon] prefix notation: "[shopping-bag] Stores"
		$title = $item->title;
		$icon  = '';
		if ( preg_match( '/^\[([a-z0-9\-]+)\]\s*(.+)$/i', $title, $m ) ) {
			$icon  = esc_attr( $m[1] );
			$title = esc_html( trim( $m[2] ) );
		}
		if ( ! $icon ) {
			$icon = 'link'; // generic fallback icon
		}

		$is_active  = $item->current || $item->current_item_ancestor;
		$active_cls = $is_active
			? ' border-primary-500 text-primary-600 bg-primary-50 dark:bg-primary-900/20'
			: '';

		$atts = [
			'title'        => ! empty( $item->attr_title ) ? $item->attr_title : '',
			'target'       => ! empty( $item->target ) ? $item->target : '',
			'rel'          => ! empty( $item->xfn ) ? $item->xfn : '',
			'href'         => ! empty( $item->url ) ? $item->url : '',
			'aria-current' => $item->current ? 'page' : '',
		];

		$attribs = '';
		foreach ( $atts as $attr => $value ) {
			if ( $value !== '' ) {
				$attribs .= ' ' . $attr . '="' . esc_attr( $value ) . '"';
			}
		}

		if ( $depth === 0 ) {
			$icon_html = '<i data-lucide="' . $icon . '" class="w-5 h-5 text-primary-500 shrink-0"></i>';
			$link_cls  = 'flex-1 flex items-center gap-3 px-6 py-3 font-bold text-slate-600 dark:text-slate-300 hover:text-primary-600 transition-colors' . $active_cls;

			$output .= $indent . '<li class="border-b border-slate-100 dark:border-slate-800/50 ' . esc_attr( $class_names ) . '">';

			if ( $has_children ) {
				// Flex row: link + separate chevron toggle button
				$output .= '<div class="flex items-center">';
				$output .= '<a' . $attribs . ' class="' . esc_attr( $link_cls ) . '">' . $icon_html . '<span>' . $title . '</span></a>';
				$output .= '<button type="button" class="bt-submenu-toggle px-4 py-3 text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors shrink-0" aria-expanded="false" aria-label="' . esc_attr__( 'Toggle submenu', 'bigtricks' ) . '">';
				$output .= '<i data-lucide="chevron-down" class="w-4 h-4 transition-transform duration-200"></i>';
				$output .= '</button></div>';
			} else {
				$output .= '<a' . $attribs . ' class="' . esc_attr( $link_cls ) . '">' . $icon_html . '<span>' . $title . '</span></a>';
			}
		} else {
			// Depth 1 — indented submenu item
			$icon_html = '<i data-lucide="corner-down-right" class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 shrink-0"></i>';
			$link_cls  = 'flex items-center gap-3 pl-10 pr-6 py-2.5 text-sm font-bold text-slate-600 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-white dark:hover:bg-slate-800 transition-colors w-full';

			$output .= $indent . '<li class="border-b border-slate-100 dark:border-slate-800/50 ' . esc_attr( $class_names ) . '">';
			$output .= '<a' . $attribs . ' class="' . esc_attr( $link_cls ) . '">' . $icon_html . '<span>' . $title . '</span></a>';
		}
	}
}

endif;

// ─────────────────────────────────────────────
// 13. Notification Bell — REST endpoint
// Lazy-fetched by main.js when the bell is first opened.
// Keeping data out of wp_localize_script avoids baking it into
// Cloudflare-cached HTML and eliminates the inline payload on every page.
// ─────────────────────────────────────────────

add_action( 'rest_api_init', function () {
	register_rest_route( 'bigtricks/v1', '/notifications', [
		'methods'             => 'GET',
		'callback'            => 'bigtricks_rest_get_notifications',
		'permission_callback' => '__return_true',
	] );
} );

function bigtricks_rest_get_notifications(): WP_REST_Response {
	return new WP_REST_Response( bt_get_notifications_from_db(), 200 );
}

// ─────────────────────────────────────────────
// 14. Theme Options (Appearance → Theme Settings)
// ─────────────────────────────────────────────

/**
 * Helper: read a single theme option with a default fallback.
 */
function bigtricks_option( string $key, string $default = '' ): string {
	$opts = (array) get_option( 'bigtricks_theme_options', [] );
	return ( isset( $opts[ $key ] ) && $opts[ $key ] !== '' ) ? (string) $opts[ $key ] : $default;
}

/**
 * Register all admin menu pages in a single hook.
 */
add_action( 'admin_menu', function () {
	add_theme_page(
		__( 'Theme Settings', 'bigtricks' ),
		__( 'Theme Settings', 'bigtricks' ),
		'manage_options',
		'bigtricks-theme-settings',
		'bigtricks_theme_settings_page'
	);
	add_options_page(
		__( 'Banners & Alerts', 'bigtricks' ),
		__( 'Banners & Alerts', 'bigtricks' ),
		'manage_options',
		'bt-banners',
		'bt_banners_page'
	);
} );

/**
 * Register settings + sections + fields.
 */
add_action( 'admin_init', function () {
	register_setting(
		'bigtricks_theme',
		'bigtricks_theme_options',
		[
			'sanitize_callback' => 'bigtricks_sanitize_theme_options',
			'default'           => [],
		]
	);

	// ── Section: Branding ──────────────────────────────────────
	add_settings_section( 'bt_branding', __( 'Branding', 'bigtricks' ), function () {
		echo '<p class="description">' . esc_html__( 'Logo, site name, and favicon are managed in Appearance → Customize.', 'bigtricks' ) . '</p>';
	}, 'bigtricks_theme' );

	// ── Section: Single Post ───────────────────────────────────
	add_settings_section( 'bt_post', __( 'Single Post', 'bigtricks' ), function () {
		echo '<p class="description">' . esc_html__( 'Control what appears on individual post/deal pages.', 'bigtricks' ) . '</p>';
	}, 'bigtricks_theme' );

	add_settings_field( 'bt_show_featured_image', __( 'Show Featured Image', 'bigtricks' ),
		function () {
			$val = bigtricks_option( 'bt_show_featured_image', '1' );
			?>
			<label>
				<input type="hidden" name="bigtricks_theme_options[bt_show_featured_image]" value="0">
				<input type="checkbox" name="bigtricks_theme_options[bt_show_featured_image]" value="1" <?php checked( $val, '1' ); ?>>
				<?php esc_html_e( 'Display the hero image at the top of single posts & deals', 'bigtricks' ); ?>
			</label>
			<?php
		},
		'bigtricks_theme', 'bt_post'
	);

	add_settings_field( 'bt_show_social_share', __( 'Show Social Share Bar', 'bigtricks' ),
		function () {
			$val = bigtricks_option( 'bt_show_social_share', '1' );
			?>
			<label>
				<input type="hidden" name="bigtricks_theme_options[bt_show_social_share]" value="0">
				<input type="checkbox" name="bigtricks_theme_options[bt_show_social_share]" value="1" <?php checked( $val, '1' ); ?>>
				<?php esc_html_e( 'Display WhatsApp / Twitter / copy-link share buttons below the post title', 'bigtricks' ); ?>
			</label>
			<?php
		},
		'bigtricks_theme', 'bt_post'
	);

	add_settings_field( 'bt_show_comments', __( 'Show Comments', 'bigtricks' ),
		function () {
			$val = bigtricks_option( 'bt_show_comments', '1' );
			?>
			<label>
				<input type="hidden" name="bigtricks_theme_options[bt_show_comments]" value="0">
				<input type="checkbox" name="bigtricks_theme_options[bt_show_comments]" value="1" <?php checked( $val, '1' ); ?>>
				<?php esc_html_e( 'Display the comments section on single posts & deals', 'bigtricks' ); ?>
			</label>
			<?php
		},
		'bigtricks_theme', 'bt_post'
	);

	// ── Section: Navigation ────────────────────────────────────
	add_settings_section( 'bt_navigation', __( 'Navigation & Display', 'bigtricks' ), function () {
		echo '<p class="description">' . esc_html__( 'Control navigation elements displayed across the site.', 'bigtricks' ) . '</p>';
	}, 'bigtricks_theme' );

	add_settings_field( 'bt_show_breadcrumbs', __( 'Show Breadcrumbs', 'bigtricks' ),
		function () {
			$val = bigtricks_option( 'bt_show_breadcrumbs', '1' );
			?>
			<label>
				<input type="hidden" name="bigtricks_theme_options[bt_show_breadcrumbs]" value="0">
				<input type="checkbox" name="bigtricks_theme_options[bt_show_breadcrumbs]" value="1" <?php checked( $val, '1' ); ?>>
				<?php esc_html_e( 'Display breadcrumb navigation on all pages except homepage', 'bigtricks' ); ?>
			</label>
			<?php
		},
		'bigtricks_theme', 'bt_navigation'
	);

	// ── Section: Social & Community ────────────────────────────
	add_settings_section( 'bt_social', __( 'Social & Community Links', 'bigtricks' ), function () {
		echo '<p class="description">' . esc_html__( 'Used in the footer and share prompts.', 'bigtricks' ) . '</p>';
	}, 'bigtricks_theme' );

	$social_fields = [
		'bt_telegram_url'  => [ __( 'Telegram Channel URL', 'bigtricks' ), 'https://t.me/yourchannel' ],
		'bt_whatsapp_url'  => [ __( 'WhatsApp Group URL', 'bigtricks' ), 'https://chat.whatsapp.com/...' ],
		'bt_twitter_url'   => [ __( 'Twitter / X Profile URL', 'bigtricks' ), 'https://twitter.com/yourhandle' ],
		'bt_instagram_url' => [ __( 'Instagram Profile URL', 'bigtricks' ), 'https://instagram.com/yourhandle' ],
	];
	foreach ( $social_fields as $key => [ $label, $placeholder ] ) {
		add_settings_field( $key, $label,
			function () use ( $key, $placeholder ) {
				?>
				<input type="url" name="bigtricks_theme_options[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( bigtricks_option( $key ) ); ?>" class="regular-text" placeholder="<?php echo esc_attr( $placeholder ); ?>">
				<?php
			},
			'bigtricks_theme', 'bt_social'
		);
	}

} );

/**
 * Sanitize all theme options on save.
 */
function bigtricks_sanitize_theme_options( $input ): array {
	if ( ! is_array( $input ) ) {
		return [];
	}
	$clean = [];
	// URL fields
	$url_keys = [ 'bt_telegram_url', 'bt_whatsapp_url', 'bt_twitter_url', 'bt_instagram_url' ];
	foreach ( $url_keys as $key ) {
		if ( isset( $input[ $key ] ) ) {
			$clean[ $key ] = esc_url_raw( $input[ $key ] );
		}
	}
	// Checkbox / toggle fields (value '1' or '0')
	$toggle_keys = [ 'bt_show_featured_image', 'bt_show_social_share', 'bt_show_comments', 'bt_show_breadcrumbs' ];
	foreach ( $toggle_keys as $key ) {
		$clean[ $key ] = isset( $input[ $key ] ) && $input[ $key ] === '1' ? '1' : '0';
	}
	return $clean;
}



/**
 * Render the settings page.
 */
function bigtricks_theme_settings_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to access this page.', 'bigtricks' ) );
	}
	?>
	<div class="wrap">
		<h1 style="display:flex;align-items:center;gap:10px;">
			<span class="dashicons dashicons-admin-appearance" style="font-size:28px;width:28px;height:28px;"></span>
			<?php esc_html_e( 'Bigtricks Theme Settings', 'bigtricks' ); ?>
		</h1>
		<p class="description" style="margin-top:4px;">
			<?php esc_html_e( 'Customise post display, social links, and other theme options. Logo and site name are managed in Appearance → Customize. Store page stats are configured in stores-config.json.', 'bigtricks' ); ?>
		</p>

		<?php settings_errors( 'bigtricks_theme' ); ?>

		<form method="post" action="options.php">
			<?php
			settings_fields( 'bigtricks_theme' );
			do_settings_sections( 'bigtricks_theme' );
			submit_button( __( 'Save Changes', 'bigtricks' ) );
			?>
		</form>
	</div>
	<?php
}

// ─────────────────────────────────────────────
// 15. Sidebar visibility filter (configurable per page)
// ─────────────────────────────────────────────

/**
 * Filter to control sidebar visibility per page/template.
 *
 * Hook: bigtricks_show_sidebar
 * Default: true (sidebar is shown by default on all pages)
 *
 * Usage Examples:
 *   // Hide sidebar on /stores page only:
 *   // (already applied in page-stores.php template)
 *
 *   // Hide sidebar globally via child theme functions.php:
 *   add_filter( 'bigtricks_show_sidebar', '__return_false' );
 *
 *   // Hide sidebar conditionally:
 *   add_filter( 'bigtricks_show_sidebar', function( $show ) {
 *       if ( is_page( 'stores' ) || is_tax( 'store' ) ) {
 *           return false;
 *       }
 *       return $show;
 *   });
 *
 * @hook bigtricks_show_sidebar
 * @since 1.0.0
 */


// Force comments to be open for referral-codes post type,
// both on frontend and during wp-comments-post.php submission
add_filter( 'comments_open', function( $open, $post_id ) {
	if ( get_post_type( $post_id ) === 'referral-codes' ) {
		return true;
	}
	return $open;
}, 99, 2 );

// ─────────────────────────────────────────────
// 11. Register Custom Gutenberg Blocks
// ─────────────────────────────────────────────

add_action( 'init', function () {
	// Register Contact Form Block
	register_block_type( BIGTRICKS_DIR . '/inc/blocks/contact-form' );

	// Register Advertise Form Block
	register_block_type( BIGTRICKS_DIR . '/inc/blocks/advertise-form' );
} );

// Enqueue block editor assets
add_action( 'enqueue_block_editor_assets', function () {
	// Editor Styles (for block editor appearance)
	wp_enqueue_style(
		'bigtricks-editor-styles',
		BIGTRICKS_URI . '/assets/css/editor-style.css',
		[],
		BIGTRICKS_VERSION
	);
	
	// Contact Form Block Editor Script
	wp_enqueue_script(
		'bigtricks-contact-form-editor',
		BIGTRICKS_URI . '/inc/blocks/contact-form/editor.js',
		[ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-block-editor' ],
		BIGTRICKS_VERSION,
		true
	);

	// Advertise Form Block Editor Script
	wp_enqueue_script(
		'bigtricks-advertise-form-editor',
		BIGTRICKS_URI . '/inc/blocks/advertise-form/editor.js',
		[ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-block-editor' ],
		BIGTRICKS_VERSION,
		true
	);
} );

// ─────────────────────────────────────────────
// 12. Contact Forms AJAX Handler
// ─────────────────────────────────────────────

add_action( 'wp_ajax_bigtricks_submit_form', 'bigtricks_handle_form_submission' );
add_action( 'wp_ajax_nopriv_bigtricks_submit_form', 'bigtricks_handle_form_submission' );

function bigtricks_handle_form_submission(): void {
	$form_type = isset( $_POST['form_type'] ) ? sanitize_text_field( wp_unslash( $_POST['form_type'] ) ) : '';
	$nonce     = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

	// Validate form type
	if ( ! in_array( $form_type, [ 'contact', 'advertise' ], true ) ) {
		wp_send_json_error( [
			'message' => 'Invalid form type.',
		] );
	}

	// Verify nonce
	$nonce_action = $form_type === 'contact' ? 'bigtricks_contact_form' : 'bigtricks_advertise_form';
	if ( ! wp_verify_nonce( $nonce, $nonce_action ) ) {
		wp_send_json_error( [
			'message' => 'Security check failed. Please refresh the page and try again.',
		] );
	}

	if ( $form_type === 'contact' ) {
		bigtricks_handle_contact_form();
	} elseif ( $form_type === 'advertise' ) {
		bigtricks_handle_advertise_form();
	}
}

/**
 * Handle Contact Form Submission
 */
function bigtricks_handle_contact_form(): void {
	// Sanitize and validate inputs
	$name     = isset( $_POST['contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : '';
	$email    = isset( $_POST['contact_email'] ) ? sanitize_email( wp_unslash( $_POST['contact_email'] ) ) : '';
	$whatsapp = isset( $_POST['contact_whatsapp'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_whatsapp'] ) ) : '';
	$message  = isset( $_POST['contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ) ) : '';

	// Validate required fields
	if ( empty( $name ) || empty( $email ) || empty( $message ) ) {
		wp_send_json_error( [
			'message' => 'Please fill in all required fields.',
		] );
	}

	if ( ! is_email( $email ) ) {
		wp_send_json_error( [
			'message' => 'Please enter a valid email address.',
		] );
	}

	// Prepare email
	$admin_email = get_option( 'admin_email' );
	$site_name   = get_bloginfo( 'name' );
	$subject     = sprintf( '[%s] New Contact Form Submission from %s', $site_name, $name );

	$email_body  = "New contact form submission:\n\n";
	$email_body .= "Name: {$name}\n";
	$email_body .= "Email: {$email}\n";
	if ( ! empty( $whatsapp ) ) {
		$email_body .= "WhatsApp: {$whatsapp}\n";
	}
	$email_body .= "\nMessage:\n{$message}\n";
	$email_body .= "\n---\n";
	$email_body .= "Sent from: " . home_url() . "\n";
	$email_body .= "Time: " . current_time( 'mysql' ) . "\n";

	$headers = [
		'Content-Type: text/plain; charset=UTF-8',
		"Reply-To: {$name} <{$email}>",
	];

	// Send email
	$sent = wp_mail( $admin_email, $subject, $email_body, $headers );

	if ( $sent ) {
		wp_send_json_success( [
			'message' => 'Thank you! Your message has been sent successfully.',
			'details' => "We'll get back to you as soon as possible.",
		] );
	} else {
		wp_send_json_error( [
			'message' => 'Failed to send your message. Please try again or contact us directly.',
		] );
	}
}

/**
 * Handle Advertise Form Submission
 */
function bigtricks_handle_advertise_form(): void {
	// Sanitize and validate inputs
	$name        = isset( $_POST['advertise_name'] ) ? sanitize_text_field( wp_unslash( $_POST['advertise_name'] ) ) : '';
	$email       = isset( $_POST['advertise_email'] ) ? sanitize_email( wp_unslash( $_POST['advertise_email'] ) ) : '';
	$phone       = isset( $_POST['advertise_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['advertise_phone'] ) ) : '';
	$whatsapp    = isset( $_POST['advertise_whatsapp'] ) ? sanitize_text_field( wp_unslash( $_POST['advertise_whatsapp'] ) ) : '';
	$company     = isset( $_POST['advertise_company'] ) ? sanitize_text_field( wp_unslash( $_POST['advertise_company'] ) ) : '';
	$website     = isset( $_POST['advertise_website'] ) ? esc_url_raw( wp_unslash( $_POST['advertise_website'] ) ) : '';
	$requirement = isset( $_POST['advertise_requirement'] ) ? sanitize_text_field( wp_unslash( $_POST['advertise_requirement'] ) ) : '';
	$budget      = isset( $_POST['advertise_budget'] ) ? sanitize_text_field( wp_unslash( $_POST['advertise_budget'] ) ) : '';
	$duration    = isset( $_POST['advertise_duration'] ) ? sanitize_text_field( wp_unslash( $_POST['advertise_duration'] ) ) : '';
	$message     = isset( $_POST['advertise_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['advertise_message'] ) ) : '';

	// Validate required fields
	if ( empty( $name ) || empty( $email ) || empty( $phone ) || empty( $company ) || empty( $requirement ) || empty( $message ) ) {
		wp_send_json_error( [
			'message' => 'Please fill in all required fields.',
		] );
	}

	if ( ! is_email( $email ) ) {
		wp_send_json_error( [
			'message' => 'Please enter a valid email address.',
		] );
	}

	// Prepare email
	$admin_email = get_option( 'admin_email' );
	$site_name   = get_bloginfo( 'name' );
	$subject     = sprintf( '[%s] New Advertising Inquiry from %s', $site_name, $company );

	$email_body  = "New advertising inquiry:\n\n";
	$email_body .= "=== CONTACT INFORMATION ===\n";
	$email_body .= "Name: {$name}\n";
	$email_body .= "Email: {$email}\n";
	$email_body .= "Phone: {$phone}\n";
	if ( ! empty( $whatsapp ) ) {
		$email_body .= "WhatsApp: {$whatsapp}\n";
	}

	$email_body .= "\n=== BUSINESS INFORMATION ===\n";
	$email_body .= "Company: {$company}\n";
	if ( ! empty( $website ) ) {
		$email_body .= "Website: {$website}\n";
	}

	$email_body .= "\n=== ADVERTISING REQUIREMENTS ===\n";
	$email_body .= "Type: {$requirement}\n";
	if ( ! empty( $budget ) ) {
		$email_body .= "Budget: {$budget}\n";
	}
	if ( ! empty( $duration ) ) {
		$email_body .= "Duration: {$duration}\n";
	}

	$email_body .= "\nAdditional Details:\n{$message}\n";
	$email_body .= "\n---\n";
	$email_body .= "Sent from: " . home_url() . "\n";
	$email_body .= "Time: " . current_time( 'mysql' ) . "\n";

	$headers = [
		'Content-Type: text/plain; charset=UTF-8',
		"Reply-To: {$name} <{$email}>",
	];

	// Send email
	$sent = wp_mail( $admin_email, $subject, $email_body, $headers );

	if ( $sent ) {
		wp_send_json_success( [
			'message' => 'Thank you for your interest!',
			'details' => "We'll review your inquiry and get back to you within 24 hours.",
		] );
	} else {
		wp_send_json_error( [
			'message' => 'Failed to send your inquiry. Please try again or email us directly.',
		] );
	}
}

// ─────────────────────────────────────────────
// 13. Contact Forms Shortcodes
// ─────────────────────────────────────────────

/**
 * Contact Form Shortcode
 * Usage: [bigtricks_contact_form]
 */
add_shortcode( 'bigtricks_contact_form', function ( $atts ) {
	$atts = shortcode_atts( [
		'title'       => 'Get in Touch',
		'description' => 'Have a question? We\'d love to hear from you. Send us a message and we\'ll respond as soon as possible.',
		'button_text' => 'Send Message',
	], $atts );

	$form_id = 'bt-contact-form-' . wp_rand( 1000, 9999 );

	ob_start();
	?>
	<div class="bigtricks-contact-form">
		<div class="max-w-2xl mx-auto">
			<h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
				<?php echo esc_html( $atts['title'] ); ?>
			</h2>
			<p class="text-gray-600 dark:text-gray-400 mb-8">
				<?php echo esc_html( $atts['description'] ); ?>
			</p>
			<form 
				id="<?php echo esc_attr( $form_id ); ?>" 
				class="bt-contact-form-element space-y-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 md:p-8 shadow-sm"
				data-form-type="contact"
				data-nonce="<?php echo esc_attr( wp_create_nonce( 'bigtricks_contact_form' ) ); ?>"
			>
				<div>
					<label for="<?php echo esc_attr( $form_id ); ?>-name" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
						Your Name <span class="text-red-500">*</span>
					</label>
					<input 
						type="text" 
						id="<?php echo esc_attr( $form_id ); ?>-name" 
						name="contact_name" 
						required
						class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all"
						placeholder="John Doe"
					>
				</div>
				<div>
					<label for="<?php echo esc_attr( $form_id ); ?>-email" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
						Email Address <span class="text-red-500">*</span>
					</label>
					<input 
						type="email" 
						id="<?php echo esc_attr( $form_id ); ?>-email" 
						name="contact_email" 
						required
						class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all"
						placeholder="john@example.com"
					>
				</div>
				<div>
					<label for="<?php echo esc_attr( $form_id ); ?>-whatsapp" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
						WhatsApp Number
					</label>
					<input 
						type="tel" 
						id="<?php echo esc_attr( $form_id ); ?>-whatsapp" 
						name="contact_whatsapp" 
						pattern="[0-9+\-\s]+"
						class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all"
						placeholder="+91 98765 43210"
					>
					<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Optional - Include country code for better response</p>
				</div>
				<div>
					<label for="<?php echo esc_attr( $form_id ); ?>-message" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
						Your Message <span class="text-red-500">*</span>
					</label>
					<textarea 
						id="<?php echo esc_attr( $form_id ); ?>-message" 
						name="contact_message" 
						required
						rows="5"
						class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all resize-none"
						placeholder="Tell us what you need help with..."
					></textarea>
				</div>
				<div>
					<button 
						type="submit" 
						class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
					>
						<span class="submit-text"><?php echo esc_html( $atts['button_text'] ); ?></span>
						<i data-lucide="send" class="w-4 h-4"></i>
					</button>
				</div>
				<div class="form-messages hidden mt-4"></div>
			</form>
		</div>
	</div>
	<?php
	return ob_get_clean();
} );

/**
 * Advertise Form Shortcode
 * Usage: [bigtricks_advertise_form]
 */
add_shortcode( 'bigtricks_advertise_form', function ( $atts ) {
	$atts = shortcode_atts( [
		'title'       => 'Advertise With Us',
		'description' => 'Partner with us to reach thousands of engaged users. Fill out the form below and we\'ll get back to you within 24 hours.',
		'button_text' => 'Submit Inquiry',
	], $atts );

	$form_id = 'bt-advertise-form-' . wp_rand( 1000, 9999 );

	ob_start();
	?>
	<div class="bigtricks-advertise-form">
		<div class="max-w-3xl mx-auto">
			<h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
				<?php echo esc_html( $atts['title'] ); ?>
			</h2>
			<p class="text-gray-600 dark:text-gray-400 mb-8">
				<?php echo esc_html( $atts['description'] ); ?>
			</p>
			<form 
				id="<?php echo esc_attr( $form_id ); ?>" 
				class="bt-advertise-form-element space-y-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 md:p-8 shadow-sm"
				data-form-type="advertise"
				data-nonce="<?php echo esc_attr( wp_create_nonce( 'bigtricks_advertise_form' ) ); ?>"
			>
				<!-- Contact Details -->
				<div class="pb-6 border-b border-gray-200 dark:border-gray-700">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
						<i data-lucide="user" class="w-5 h-5"></i>
						Contact Information
					</h3>
					<div class="grid md:grid-cols-2 gap-6">
						<div>
							<label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
								Your Name <span class="text-red-500">*</span>
							</label>
							<input type="text" name="advertise_name" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all" placeholder="John Doe">
						</div>
						<div>
							<label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
								Email Address <span class="text-red-500">*</span>
							</label>
							<input type="email" name="advertise_email" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all" placeholder="john@company.com">
						</div>
						<div>
							<label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
								Phone Number <span class="text-red-500">*</span>
							</label>
							<input type="tel" name="advertise_phone" required pattern="[0-9+\-\s]+" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all" placeholder="+91 98765 43210">
						</div>
						<div>
							<label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
								WhatsApp Number
							</label>
							<input type="tel" name="advertise_whatsapp" pattern="[0-9+\-\s]+" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all" placeholder="Same as phone">
						</div>
					</div>
				</div>
				<!-- Business Details -->
				<div class="pb-6 border-b border-gray-200 dark:border-gray-700">
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
						<i data-lucide="building-2" class="w-5 h-5"></i>
						Business Information
					</h3>
					<div class="grid md:grid-cols-2 gap-6">
						<div>
							<label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
								Company Name <span class="text-red-500">*</span>
							</label>
							<input type="text" name="advertise_company" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all" placeholder="Your Company Pvt Ltd">
						</div>
						<div>
							<label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
								Company Website
							</label>
							<input type="url" name="advertise_website" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all" placeholder="https://yourcompany.com">
						</div>
					</div>
				</div>
				<!-- Requirements -->
				<div>
					<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
						<i data-lucide="megaphone" class="w-5 h-5"></i>
						Advertising Requirements
					</h3>
					<div class="mb-6">
						<label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
							Type of Advertisement <span class="text-red-500">*</span>
						</label>
						<select name="advertise_requirement" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all">
							<option value="">-- Select Type --</option>
							<option value="Banner Ads">Banner Ads (Display Advertising)</option>
							<option value="Sponsored Posts">Sponsored Posts (Content Marketing)</option>
							<option value="Telegram Post">Telegram Channel Post</option>
							<option value="Product Review">Product Review & Listing</option>
							<option value="Affiliate Partnership">Affiliate Partnership</option>
							<option value="Newsletter Sponsorship">Newsletter Sponsorship</option>
							<option value="Other">Other (Please specify in message)</option>
						</select>
					</div>
					<div class="mb-6">
						<label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
							Monthly Budget Range
						</label>
						<select name="advertise_budget" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all">
							<option value="">-- Select Budget --</option>
							<option value="Under ₹10,000">Under ₹10,000</option>
							<option value="₹10,000 - ₹25,000">₹10,000 - ₹25,000</option>
							<option value="₹25,000 - ₹50,000">₹25,000 - ₹50,000</option>
							<option value="₹50,000 - ₹1,00,000">₹50,000 - ₹1,00,000</option>
							<option value="Above ₹1,00,000">Above ₹1,00,000</option>
							<option value="Custom">Custom (Discuss in message)</option>
						</select>
					</div>
					<div class="mb-6">
						<label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
							Campaign Duration
						</label>
						<select name="advertise_duration" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all">
							<option value="">-- Select Duration --</option>
							<option value="1 Month">1 Month</option>
							<option value="3 Months">3 Months</option>
							<option value="6 Months">6 Months</option>
							<option value="12 Months">12 Months (Annual)</option>
							<option value="One-time">One-time Campaign</option>
							<option value="Ongoing">Ongoing Partnership</option>
						</select>
					</div>
					<div>
						<label class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
							Additional Details <span class="text-red-500">*</span>
						</label>
						<textarea name="advertise_message" required rows="5" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all resize-none" placeholder="Tell us about your advertising goals..."></textarea>
					</div>
				</div>
				<div class="pt-4">
					<button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-lg">
						<span class="submit-text"><?php echo esc_html( $atts['button_text'] ); ?></span>
						<i data-lucide="send" class="w-5 h-5"></i>
					</button>
				</div>
				<div class="form-messages hidden mt-4"></div>
			</form>
		</div>
	</div>
	<?php
	return ob_get_clean();
} );

// ─────────────────────────────────────────────
// 16. Banners & Alerts (Announcement Banner + Notifications)
//     Admin UI at Settings → Banners & Alerts
//     Storage: wp_options (bt_announcement, bt_notifications)
// ─────────────────────────────────────────────

/* -------- Data helpers -------- */

function bt_get_announcement(): array {
	return wp_parse_args(
		(array) get_option( 'bt_announcement', [] ),
		[ 'active' => 0, 'text' => '', 'url' => '', 'image_id' => 0, 'image_url' => '', 'color' => 'primary' ]
	);
}

function bt_get_notifications_from_db(): array {
	$cached = get_transient( 'bt_notifications_cache' );
	if ( false !== $cached ) return (array) $cached;
	$data = (array) get_option( 'bt_notifications', [] );
	set_transient( 'bt_notifications_cache', $data, 5 * MINUTE_IN_SECONDS );
	return $data;
}

function bt_get_carousel_slides(): array {
	$cached = get_transient( 'bt_carousel_cache' );
	if ( false !== $cached ) return (array) $cached;

	$data = (array) get_option( 'bt_carousel_slides', [] );

	set_transient( 'bt_carousel_cache', $data, 5 * MINUTE_IN_SECONDS );
	return $data;
}

/* -------- Admin assets -------- */

add_action( 'admin_enqueue_scripts', function ( string $hook ) {
	if ( 'settings_page_bt-banners' !== $hook ) return;
	wp_enqueue_media();
} );

/* -------- AJAX: Save announcement -------- */

add_action( 'wp_ajax_bt_save_announcement', function () {
	check_ajax_referer( 'bt_banners_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Unauthorized.' );
	}
	update_option( 'bt_announcement', [
		'active'    => absint( $_POST['active'] ?? 0 ),
		'text'      => wp_kses_post( wp_unslash( $_POST['text'] ?? '' ) ),
		'url'       => esc_url_raw( wp_unslash( $_POST['url'] ?? '' ) ),
		'image_id'  => absint( $_POST['image_id'] ?? 0 ),
		'image_url' => esc_url_raw( wp_unslash( $_POST['image_url'] ?? '' ) ),
		'color'     => sanitize_text_field( $_POST['color'] ?? 'primary' ),
	], false );
	wp_send_json_success( __( 'Announcement saved.', 'bigtricks' ) );
} );

/* -------- AJAX: Add notification -------- */

add_action( 'wp_ajax_bt_add_notification', function () {
	check_ajax_referer( 'bt_banners_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Unauthorized.' );
	}
	$title = sanitize_text_field( wp_unslash( $_POST['title'] ?? '' ) );
	if ( ! $title ) {
		wp_send_json_error( __( 'Title is required.', 'bigtricks' ) );
	}
	$notifs = (array) get_option( 'bt_notifications', [] );
	if ( count( $notifs ) >= 20 ) {
		array_shift( $notifs );
	}
	$new = [
		'id'          => (string) time(),
		'title'       => $title,
		'excerpt'     => sanitize_text_field( wp_unslash( $_POST['excerpt'] ?? '' ) ),
		'link'        => esc_url_raw( wp_unslash( $_POST['url'] ?? '' ) ),
		'badge'       => sanitize_text_field( wp_unslash( $_POST['badge'] ?? '' ) ),
		'badge_color' => sanitize_text_field( wp_unslash( $_POST['badge_color'] ?? 'red' ) ),
		'image'       => esc_url_raw( wp_unslash( $_POST['image'] ?? '' ) ),
		'created_at'  => time(),
	];
	array_unshift( $notifs, $new );
	update_option( 'bt_notifications', $notifs, false );
	delete_transient( 'bt_notifications_cache' );
	wp_send_json_success( $new );
} );

/* -------- AJAX: Delete notification -------- */

add_action( 'wp_ajax_bt_delete_notification', function () {
	check_ajax_referer( 'bt_banners_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Unauthorized.' );
	}
	$id     = sanitize_text_field( $_POST['id'] ?? '' );
	$notifs = array_values( array_filter(
		(array) get_option( 'bt_notifications', [] ),
		fn( $n ) => ( $n['id'] ?? '' ) !== $id
	) );
	update_option( 'bt_notifications', $notifs, false );
	delete_transient( 'bt_notifications_cache' );
	wp_send_json_success();
} );

/* -------- AJAX: Add carousel slide -------- */

add_action( 'wp_ajax_bt_add_carousel_slide', function () {
	check_ajax_referer( 'bt_banners_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Unauthorized.' );
	}
	$title = sanitize_text_field( wp_unslash( $_POST['title'] ?? '' ) );
	if ( ! $title ) {
		wp_send_json_error( __( 'Title is required.', 'bigtricks' ) );
	}
	$slides = (array) get_option( 'bt_carousel_slides', [] );
	if ( count( $slides ) >= 5 ) {
		wp_send_json_error( __( 'Maximum 5 slides allowed. Delete one first.', 'bigtricks' ) );
	}
	$new = [
		'id'          => (string) time(),
		'title'       => $title,
		'excerpt'     => sanitize_text_field( wp_unslash( $_POST['excerpt'] ?? '' ) ),
		'link'        => esc_url_raw( wp_unslash( $_POST['link'] ?? '' ) ),
		'image'       => esc_url_raw( wp_unslash( $_POST['image'] ?? '' ) ),
		'badge'       => sanitize_text_field( wp_unslash( $_POST['badge'] ?? '' ) ),
		'badge_color' => sanitize_text_field( wp_unslash( $_POST['badge_color'] ?? 'red' ) ),
		'temperature' => absint( $_POST['temperature'] ?? 0 ),
		'button_text' => sanitize_text_field( wp_unslash( $_POST['button_text'] ?? '' ) ),
	];
	$slides[] = $new;
	update_option( 'bt_carousel_slides', $slides, false );
	delete_transient( 'bt_carousel_cache' );
	wp_send_json_success( $new );
} );

/* -------- AJAX: Delete carousel slide -------- */

add_action( 'wp_ajax_bt_delete_carousel_slide', function () {
	check_ajax_referer( 'bt_banners_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Unauthorized.' );
	}
	$id     = sanitize_text_field( $_POST['id'] ?? '' );
	$slides = array_values( array_filter(
		(array) get_option( 'bt_carousel_slides', [] ),
		fn( $s ) => ( $s['id'] ?? '' ) !== $id
	) );
	update_option( 'bt_carousel_slides', $slides, false );
	delete_transient( 'bt_carousel_cache' );
	wp_send_json_success();
} );

/* -------- AJAX: Reorder carousel slides -------- */

add_action( 'wp_ajax_bt_reorder_carousel_slides', function () {
	check_ajax_referer( 'bt_banners_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Unauthorized.' );
	}
	$order   = array_map( 'sanitize_text_field', (array) ( $_POST['order'] ?? [] ) );
	$slides  = (array) get_option( 'bt_carousel_slides', [] );
	$indexed = [];
	foreach ( $slides as $s ) {
		$indexed[ $s['id'] ] = $s;
	}
	$reordered = [];
	foreach ( $order as $id ) {
		if ( isset( $indexed[ $id ] ) ) {
			$reordered[] = $indexed[ $id ];
		}
	}
	update_option( 'bt_carousel_slides', $reordered, false );
	delete_transient( 'bt_carousel_cache' );
	wp_send_json_success();
} );

/* -------- AJAX: Fetch URL preview -------- */

add_action( 'wp_ajax_bt_fetch_url_preview', function () {
	check_ajax_referer( 'bt_banners_nonce', 'nonce' );
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( 'Unauthorized.' );
	}
	$url = esc_url_raw( wp_unslash( $_POST['url'] ?? '' ) );
	if ( ! $url ) {
		wp_send_json_error( __( 'No URL provided.', 'bigtricks' ) );
	}

	// Try local WP post first (no HTTP roundtrip)
	$post_id = url_to_postid( $url );
	if ( $post_id ) {
		$post  = get_post( $post_id );
		$thumb = get_the_post_thumbnail_url( $post_id, 'medium' )
		       ?: get_post_meta( $post_id, '_btdeals_offer_thumbnail_url', true )
		       ?: get_post_meta( $post_id, '_btdeals_product_thumbnail_url', true )
		       ?: '';
		wp_send_json_success( [
			'title'   => get_the_title( $post_id ),
			'excerpt' => has_excerpt( $post_id )
				? wp_strip_all_tags( get_the_excerpt( $post_id ) )
				: wp_trim_words( wp_strip_all_tags( $post->post_content ?? '' ), 20, '…' ),
			'url'     => get_permalink( $post_id ),
			'image'   => $thumb,
		] );
	}

	// External URL — scrape OpenGraph tags
	$resp = wp_remote_get( $url, [ 'timeout' => 8 ] );
	if ( is_wp_error( $resp ) || 200 !== wp_remote_retrieve_response_code( $resp ) ) {
		wp_send_json_error( __( 'Could not fetch URL.', 'bigtricks' ) );
	}
	$body    = wp_remote_retrieve_body( $resp );
	$title   = $excerpt = $image = '';
	if ( preg_match( '/<meta[^>]+property=["\']og:title["\'][^>]+content=["\'](.*?)["\'][^>]*>/i', $body, $m ) ) {
		$title = html_entity_decode( $m[1] );
	} elseif ( preg_match( '/<title[^>]*>(.*?)<\/title>/is', $body, $m ) ) {
		$title = html_entity_decode( wp_strip_all_tags( $m[1] ) );
	}
	if ( preg_match( '/<meta[^>]+property=["\']og:description["\'][^>]+content=["\'](.*?)["\'][^>]*>/i', $body, $m ) ) {
		$excerpt = html_entity_decode( wp_strip_all_tags( $m[1] ) );
	}
	if ( preg_match( '/<meta[^>]+property=["\']og:image["\'][^>]+content=["\'](.*?)["\'][^>]*>/i', $body, $m ) ) {
		$image = esc_url_raw( $m[1] );
	}
	wp_send_json_success( compact( 'title', 'excerpt', 'image', 'url' ) );
} );

/* -------- Admin page render -------- */

function bt_banners_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Unauthorised', 'bigtricks' ) );
	}
	$tab    = sanitize_text_field( $_GET['tab'] ?? 'announcement' );
	$ann    = bt_get_announcement();
	$notifs = bt_get_notifications_from_db();
	$slides = bt_get_carousel_slides();
	?>
	<div class="wrap" id="bt-banners-wrap">
		<h1 style="display:flex;align-items:center;gap:8px;">
			<span class="dashicons dashicons-megaphone" style="font-size:26px;width:26px;height:26px;margin-top:2px;"></span>
			<?php esc_html_e( 'Banners & Alerts', 'bigtricks' ); ?>
		</h1>
		<nav class="nav-tab-wrapper" style="margin-bottom:0;">
			<a href="?page=bt-banners&tab=announcement"
			   class="nav-tab <?php echo $tab === 'announcement' ? 'nav-tab-active' : ''; ?>">
				&#128226; <?php esc_html_e( 'Announcement Banner', 'bigtricks' ); ?>
			</a>
			<a href="?page=bt-banners&tab=notifications"
			   class="nav-tab <?php echo $tab === 'notifications' ? 'nav-tab-active' : ''; ?>">
				&#128276; <?php esc_html_e( 'Notifications', 'bigtricks' ); ?>
			</a>
			<a href="?page=bt-banners&tab=carousel"
			   class="nav-tab <?php echo $tab === 'carousel' ? 'nav-tab-active' : ''; ?>">
				&#127916; <?php esc_html_e( 'Hero Carousel', 'bigtricks' ); ?>
			</a>
		</nav>
		<div style="background:#fff;border:1px solid #c3c4c7;border-top:none;padding:24px 24px 32px;max-width:860px;">
			<?php if ( $tab === 'announcement' ) : ?>
				<?php bt_announcement_tab( $ann ); ?>
			<?php elseif ( $tab === 'carousel' ) : ?>
				<?php bt_carousel_tab( $slides ); ?>
			<?php else : ?>
				<?php bt_notifications_tab( $notifs ); ?>
			<?php endif; ?>
		</div>
	</div>
	<?php bt_banners_admin_js(); ?>
	<?php
}

/* -------- Announcement tab -------- */

function bt_announcement_tab( array $ann ): void {
	$colors = [
		'primary' => 'Indigo (Primary)',
		'red'     => 'Red',
		'emerald' => 'Emerald',
		'orange'  => 'Orange',
		'slate'   => 'Dark',
	];
	?>
	<h2 style="margin-top:0;"><?php esc_html_e( 'Announcement Banner', 'bigtricks' ); ?></h2>
	<p class="description"><?php esc_html_e( 'A slim full-width bar above the header. Visitors can dismiss it \xe2\x80\x94 won\'t re-appear until you change the message.', 'bigtricks' ); ?></p>
	<div id="bt-ann-notice" class="notice" style="display:none;margin:12px 0;"><p></p></div>
	<table class="form-table" style="max-width:680px;">
		<tr>
			<th><?php esc_html_e( 'Enable', 'bigtricks' ); ?></th>
			<td>
				<label>
					<input type="checkbox" id="bt-ann-active" <?php checked( $ann['active'], 1 ); ?>>
					<?php esc_html_e( 'Show banner on frontend', 'bigtricks' ); ?>
				</label>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Auto-fill from URL', 'bigtricks' ); ?></th>
			<td>
				<div style="display:flex;gap:8px;">
					<input type="url" id="bt-ann-fetch-url" class="regular-text" placeholder="https://..." style="flex:1;">
					<button type="button" id="bt-ann-fetch-btn" class="button"><?php esc_html_e( 'Fetch &amp; Fill', 'bigtricks' ); ?></button>
				</div>
				<p class="description"><?php esc_html_e( 'Paste any post URL to auto-fill message, image and link.', 'bigtricks' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Message', 'bigtricks' ); ?></th>
			<td>
				<textarea id="bt-ann-text" rows="2" style="width:100%;"><?php echo esc_textarea( $ann['text'] ); ?></textarea>
				<p class="description"><?php esc_html_e( 'Basic HTML like <b>bold</b> is allowed.', 'bigtricks' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Link URL', 'bigtricks' ); ?></th>
			<td><input type="url" id="bt-ann-url" class="regular-text" value="<?php echo esc_attr( $ann['url'] ); ?>" placeholder="https://..."></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Image', 'bigtricks' ); ?></th>
			<td>
				<div style="display:flex;gap:10px;align-items:center;">
					<img id="bt-ann-preview" src="<?php echo esc_attr( $ann['image_url'] ); ?>"
					     style="width:48px;height:48px;object-fit:cover;border-radius:6px;border:1px solid #ddd;<?php echo $ann['image_url'] ? '' : 'display:none;'; ?>">
					<input type="hidden" id="bt-ann-image-id"  value="<?php echo esc_attr( (string) $ann['image_id'] ); ?>">
					<input type="hidden" id="bt-ann-image-url" value="<?php echo esc_attr( $ann['image_url'] ); ?>">
					<button type="button" id="bt-ann-upload-btn" class="button"><?php esc_html_e( 'Choose Image', 'bigtricks' ); ?></button>
					<button type="button" id="bt-ann-remove-img" class="button button-link-delete"
					        <?php echo $ann['image_url'] ? '' : 'style="display:none;"'; ?>><?php esc_html_e( 'Remove', 'bigtricks' ); ?></button>
				</div>
				<p class="description"><?php esc_html_e( 'Optional thumbnail shown in the banner.', 'bigtricks' ); ?></p>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Color', 'bigtricks' ); ?></th>
			<td>
				<select id="bt-ann-color">
					<?php foreach ( $colors as $val => $label ) : ?>
						<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $ann['color'], $val ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
	</table>
	<p><button type="button" id="bt-ann-save" class="button button-primary button-large"><?php esc_html_e( 'Save Banner', 'bigtricks' ); ?></button></p>
	<?php
}

/* -------- Notifications tab -------- */

function bt_notifications_tab( array $notifs ): void {
	$badge_colors = [ 'red' => 'Red', 'emerald' => 'Emerald', 'orange' => 'Orange', 'blue' => 'Blue', 'purple' => 'Purple', 'slate' => 'Slate' ];
	$empty = empty( $notifs );
	?>
	<h2 style="margin-top:0;"><?php esc_html_e( 'Notification Items', 'bigtricks' ); ?></h2>
	<p class="description"><?php esc_html_e( 'Appear in the bell icon drawer, newest-first. Max 20.', 'bigtricks' ); ?></p>
	<div id="bt-notif-admin-notice" class="notice" style="display:none;margin:12px 0;"><p></p></div>

	<p id="bt-notif-empty" <?php echo $empty ? '' : 'style="display:none;"'; ?>>
		<?php esc_html_e( 'No notifications yet. Add one below.', 'bigtricks' ); ?>
	</p>
	<table class="wp-list-table widefat fixed striped" id="bt-notif-table"
	       style="max-width:800px;<?php echo $empty ? 'display:none;' : ''; ?>">
		<thead>
			<tr>
				<th style="width:50px;"><?php esc_html_e( 'Img', 'bigtricks' ); ?></th>
				<th><?php esc_html_e( 'Title', 'bigtricks' ); ?></th>
				<th style="width:100px;"><?php esc_html_e( 'Badge', 'bigtricks' ); ?></th>
				<th style="width:80px;"></th>
			</tr>
		</thead>
		<tbody id="bt-notif-tbody">
			<?php foreach ( $notifs as $n ) : ?>
			<tr id="bt-notif-row-<?php echo esc_attr( $n['id'] ); ?>">
				<td><?php if ( ! empty( $n['image'] ) ) : ?>
					<img src="<?php echo esc_url( $n['image'] ); ?>" style="width:38px;height:38px;object-fit:cover;border-radius:4px;">
				<?php else : ?><span style="color:#ccc;">&mdash;</span><?php endif; ?></td>
				<td>
					<strong><?php echo esc_html( $n['title'] ); ?></strong>
					<?php if ( ! empty( $n['excerpt'] ) ) : ?>
						<br><span style="font-size:12px;color:#666;"><?php echo esc_html( $n['excerpt'] ); ?></span>
					<?php endif; ?>
				</td>
				<td><?php echo esc_html( $n['badge'] ); ?></td>
				<td><button type="button" class="button button-link-delete bt-notif-delete"
				            data-id="<?php echo esc_attr( $n['id'] ); ?>"><?php esc_html_e( 'Delete', 'bigtricks' ); ?></button></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<h3 style="margin-top:28px;"><?php esc_html_e( 'Add New Notification', 'bigtricks' ); ?></h3>
	<table class="form-table" style="max-width:680px;">
		<tr>
			<th><?php esc_html_e( 'Auto-fill from URL', 'bigtricks' ); ?></th>
			<td>
				<div style="display:flex;gap:8px;">
					<input type="url" id="bt-nf-fetch-url" class="regular-text" placeholder="https://..." style="flex:1;">
					<button type="button" id="bt-nf-fetch-btn" class="button"><?php esc_html_e( 'Fetch &amp; Fill', 'bigtricks' ); ?></button>
				</div>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Title *', 'bigtricks' ); ?></th>
			<td><input type="text" id="bt-nf-title" class="regular-text" placeholder="<?php esc_attr_e( 'Short deal title\xe2\x80\xa6', 'bigtricks' ); ?>"></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Excerpt', 'bigtricks' ); ?></th>
			<td><input type="text" id="bt-nf-excerpt" class="regular-text" placeholder="<?php esc_attr_e( 'One-line description\xe2\x80\xa6', 'bigtricks' ); ?>"></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Link URL', 'bigtricks' ); ?></th>
			<td><input type="url" id="bt-nf-url" class="regular-text" placeholder="https://..."></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Image', 'bigtricks' ); ?></th>
			<td>
				<div style="display:flex;gap:10px;align-items:center;">
					<img id="bt-nf-preview" src="" style="width:48px;height:48px;object-fit:cover;border-radius:6px;border:1px solid #ddd;display:none;">
					<input type="hidden" id="bt-nf-image-url" value="">
					<button type="button" id="bt-nf-upload-btn" class="button"><?php esc_html_e( 'Choose Image', 'bigtricks' ); ?></button>
					<button type="button" id="bt-nf-remove-img" class="button button-link-delete" style="display:none;"><?php esc_html_e( 'Remove', 'bigtricks' ); ?></button>
				</div>
			</td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Badge Text', 'bigtricks' ); ?></th>
			<td><input type="text" id="bt-nf-badge" class="small-text" placeholder="Hot Deal" maxlength="20"></td>
		</tr>
		<tr>
			<th><?php esc_html_e( 'Badge Color', 'bigtricks' ); ?></th>
			<td>
				<select id="bt-nf-badge-color">
					<?php foreach ( $badge_colors as $val => $label ) : ?>
						<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
			</td>
		</tr>
	</table>
	<p><button type="button" id="bt-nf-add" class="button button-primary button-large"><?php esc_html_e( 'Add Notification', 'bigtricks' ); ?></button></p>
	<?php
}

/* -------- Carousel Slides tab -------- */

function bt_carousel_tab( array $slides ): void {
	$badge_colors = [ 'red' => 'Red', 'emerald' => 'Emerald', 'purple' => 'Purple', 'blue' => 'Blue', 'orange' => 'Orange', 'slate' => 'Slate' ];
	$full         = count( $slides ) >= 5;
	?>
	<h2 style="margin-top:0;"><?php esc_html_e( 'Hero Carousel Slides', 'bigtricks' ); ?></h2>
	<p class="description"><?php esc_html_e( 'The full-width hero carousel on the homepage. Max 5 slides. Use the ↑ ↓ arrows to reorder.', 'bigtricks' ); ?></p>
	<div id="bt-carousel-notice" class="notice" style="display:none;margin:12px 0;"><p></p></div>

	<p id="bt-carousel-empty" <?php echo empty( $slides ) ? '' : 'style="display:none;"'; ?>>
		<?php esc_html_e( 'No slides yet. Add one below.', 'bigtricks' ); ?>
	</p>
	<table class="wp-list-table widefat fixed striped" id="bt-carousel-table"
	       style="max-width:800px;<?php echo empty( $slides ) ? 'display:none;' : ''; ?>">
		<thead>
			<tr>
				<th style="width:50px;"><?php esc_html_e( 'Img', 'bigtricks' ); ?></th>
				<th><?php esc_html_e( 'Title', 'bigtricks' ); ?></th>
				<th style="width:80px;"><?php esc_html_e( 'Badge', 'bigtricks' ); ?></th>
				<th style="width:80px;"><?php esc_html_e( 'Button', 'bigtricks' ); ?></th>
				<th style="width:40px;"><?php esc_html_e( '🌡', 'bigtricks' ); ?></th>
				<th style="width:80px;"><?php esc_html_e( 'Order', 'bigtricks' ); ?></th>
				<th style="width:70px;"></th>
			</tr>
		</thead>
		<tbody id="bt-carousel-tbody">
			<?php foreach ( $slides as $s ) : ?>
			<tr id="bt-cs-row-<?php echo esc_attr( $s['id'] ); ?>" data-id="<?php echo esc_attr( $s['id'] ); ?>">
				<td><?php if ( ! empty( $s['image'] ) ) : ?>
					<img src="<?php echo esc_url( $s['image'] ); ?>" style="width:38px;height:38px;object-fit:cover;border-radius:4px;">
				<?php else : ?><span style="color:#ccc;">&mdash;</span><?php endif; ?></td>
				<td>
					<strong><?php echo esc_html( $s['title'] ); ?></strong>
					<?php if ( ! empty( $s['excerpt'] ) ) : ?>
						<br><span style="font-size:12px;color:#666;"><?php echo esc_html( wp_trim_words( $s['excerpt'], 10 ) ); ?></span>
					<?php endif; ?>
				</td>
				<td><?php echo esc_html( $s['badge'] ); ?></td>
				<td><?php echo esc_html( $s['button_text'] ?? '' ?: '—' ); ?></td>
				<td><?php echo esc_html( (string) ( $s['temperature'] ?? 0 ) ); ?>°</td>
				<td style="white-space:nowrap;">
					<button type="button" class="button button-small bt-cs-up" data-id="<?php echo esc_attr( $s['id'] ); ?>" title="Move up">↑</button>
					<button type="button" class="button button-small bt-cs-down" data-id="<?php echo esc_attr( $s['id'] ); ?>" title="Move down">↓</button>
				</td>
				<td><button type="button" class="button button-link-delete bt-cs-delete" data-id="<?php echo esc_attr( $s['id'] ); ?>"><?php esc_html_e( 'Delete', 'bigtricks' ); ?></button></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php if ( $full ) : ?>
	<p id="bt-carousel-full-notice" style="color:#c00;font-weight:600;"><?php esc_html_e( '5 slides maximum reached. Delete a slide to add a new one.', 'bigtricks' ); ?></p>
	<?php endif; ?>

	<div id="bt-carousel-add-form" <?php echo $full ? 'style="display:none;"' : ''; ?>>
		<h3 style="margin-top:28px;"><?php esc_html_e( 'Add New Slide', 'bigtricks' ); ?></h3>
		<table class="form-table" style="max-width:680px;">
			<tr>
				<th><?php esc_html_e( 'Auto-fill from URL', 'bigtricks' ); ?></th>
				<td>
					<div style="display:flex;gap:8px;">
						<input type="url" id="bt-cs-fetch-url" class="regular-text" placeholder="https://..." style="flex:1;">
						<button type="button" id="bt-cs-fetch-btn" class="button"><?php esc_html_e( 'Fetch &amp; Fill', 'bigtricks' ); ?></button>
					</div>
					<p class="description"><?php esc_html_e( 'Paste any post URL to auto-fill the fields below.', 'bigtricks' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Title *', 'bigtricks' ); ?></th>
				<td><input type="text" id="bt-cs-title" class="regular-text" placeholder="<?php esc_attr_e( 'Slide headline…', 'bigtricks' ); ?>"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Excerpt', 'bigtricks' ); ?></th>
				<td><textarea id="bt-cs-excerpt" rows="2" class="large-text" placeholder="<?php esc_attr_e( 'Short description…', 'bigtricks' ); ?>"></textarea></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Link URL', 'bigtricks' ); ?></th>
				<td><input type="url" id="bt-cs-link" class="regular-text" placeholder="https://..."></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Background Image', 'bigtricks' ); ?></th>
				<td>
					<div style="display:flex;gap:10px;align-items:center;">
						<img id="bt-cs-preview" src="" style="width:80px;height:48px;object-fit:cover;border-radius:6px;border:1px solid #ddd;display:none;">
						<input type="hidden" id="bt-cs-image-url" value="">
						<button type="button" id="bt-cs-upload-btn" class="button"><?php esc_html_e( 'Choose Image', 'bigtricks' ); ?></button>
						<button type="button" id="bt-cs-remove-img" class="button button-link-delete" style="display:none;"><?php esc_html_e( 'Remove', 'bigtricks' ); ?></button>
					</div>
					<p class="description"><?php esc_html_e( 'Recommended: 1200 × 480px or wider.', 'bigtricks' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Badge Text', 'bigtricks' ); ?></th>
				<td><input type="text" id="bt-cs-badge" class="small-text" placeholder="Hot Deal" maxlength="20"></td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Badge Color', 'bigtricks' ); ?></th>
				<td>
					<select id="bt-cs-badge-color">
						<?php foreach ( $badge_colors as $val => $label ) : ?>
							<option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Button Text', 'bigtricks' ); ?></th>
				<td>
					<input type="text" id="bt-cs-button-text" class="regular-text" placeholder="<?php esc_attr_e( 'See Deal', 'bigtricks' ); ?>" maxlength="40">
					<p class="description"><?php esc_html_e( 'CTA button label. Defaults to "See Deal" if left blank.', 'bigtricks' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Temperature', 'bigtricks' ); ?></th>
				<td>
					<input type="number" id="bt-cs-temperature" class="small-text" value="0" min="0" max="999">
					<p class="description"><?php esc_html_e( 'Hot score shown as 🔥 badge (0 = hidden).', 'bigtricks' ); ?></p>
				</td>
			</tr>
		</table>
		<p><button type="button" id="bt-cs-add" class="button button-primary button-large"><?php esc_html_e( 'Add Slide', 'bigtricks' ); ?></button></p>
	</div>
	<?php
}

/* -------- Admin inline JS -------- */

function bt_banners_admin_js(): void {
	$nonce = wp_create_nonce( 'bt_banners_nonce' );
	$ajax  = admin_url( 'admin-ajax.php' );
	?>
	<script>
	(function($){
		var NONCE = <?php echo wp_json_encode( $nonce ); ?>;
		var AJAX  = <?php echo wp_json_encode( $ajax ); ?>;

		function uploader(btnId, prevId, urlId, idId, rmId) {
			var frame;
			$('#'+btnId).on('click', function() {
				if (frame) { frame.open(); return; }
				frame = wp.media({ title: 'Select Image', button: { text: 'Use Image' }, multiple: false });
				frame.on('select', function() {
					var att = frame.state().get('selection').first().toJSON();
					$('#'+prevId).attr('src', att.url).show();
					$('#'+urlId).val(att.url);
					if (idId) $('#'+idId).val(att.id);
					if (rmId) $('#'+rmId).show();
				});
				frame.open();
			});
			if (rmId) {
				$('#'+rmId).on('click', function() {
					$('#'+prevId).attr('src','').hide();
					$('#'+urlId).val('');
					if (idId) $('#'+idId).val('');
					$(this).hide();
				});
			}
		}

		function fetchFill(fetchId, map, btnId) {
			$('#'+btnId).on('click', function() {
				var url = $('#'+fetchId).val().trim();
				if (!url) { alert('Enter a URL first.'); return; }
				var $b = $(this).prop('disabled',true).text('Fetching\u2026');
				$.post(AJAX, { action:'bt_fetch_url_preview', nonce:NONCE, url:url }, function(res) {
					$b.prop('disabled',false).text('Fetch & Fill');
					if (!res.success) { alert(res.data || 'Failed.'); return; }
					var d = res.data;
					if (map.title   && d.title)   $('#'+map.title).val(d.title);
					if (map.excerpt && d.excerpt) $('#'+map.excerpt).val(d.excerpt);
					if (map.url     && d.url)     $('#'+map.url).val(d.url);
					if (map.image   && d.image) {
						$('#'+map.imgPrev).attr('src', d.image).show();
						$('#'+map.image).val(d.image);
						if (map.imgRm) $('#'+map.imgRm).show();
					}
				}).fail(function(){
					$b.prop('disabled',false).text('Fetch & Fill');
					alert('Request failed.');
				});
			});
		}

		function notice(id, msg, type) {
			$('#'+id).removeClass('notice-success notice-error').addClass('notice-'+type)
			         .find('p').text(msg).end().show();
			setTimeout(function(){ $('#'+id).fadeOut(); }, 3500);
		}

		if ($('#bt-ann-save').length) {
			uploader('bt-ann-upload-btn','bt-ann-preview','bt-ann-image-url','bt-ann-image-id','bt-ann-remove-img');
			fetchFill('bt-ann-fetch-url', {
				title:'bt-ann-text', url:'bt-ann-url',
				image:'bt-ann-image-url', imgPrev:'bt-ann-preview', imgRm:'bt-ann-remove-img'
			}, 'bt-ann-fetch-btn');
			$('#bt-ann-save').on('click', function() {
				var $b = $(this).prop('disabled',true).text('Saving\u2026');
				$.post(AJAX, {
					action:'bt_save_announcement', nonce:NONCE,
					active:    $('#bt-ann-active').is(':checked') ? 1 : 0,
					text:      $('#bt-ann-text').val(),
					url:       $('#bt-ann-url').val(),
					image_id:  $('#bt-ann-image-id').val(),
					image_url: $('#bt-ann-image-url').val(),
					color:     $('#bt-ann-color').val()
				}, function(res) {
					$b.prop('disabled',false).text('Save Banner');
					notice('bt-ann-notice', res.success ? res.data : (res.data||'Error.'), res.success ? 'success' : 'error');
				});
			});
		}

		if ($('#bt-nf-add').length) {
			uploader('bt-nf-upload-btn','bt-nf-preview','bt-nf-image-url', null,'bt-nf-remove-img');
			fetchFill('bt-nf-fetch-url', {
				title:'bt-nf-title', excerpt:'bt-nf-excerpt', url:'bt-nf-url',
				image:'bt-nf-image-url', imgPrev:'bt-nf-preview', imgRm:'bt-nf-remove-img'
			}, 'bt-nf-fetch-btn');
			$('#bt-nf-add').on('click', function() {
				var title = $('#bt-nf-title').val().trim();
				if (!title) { alert('Title is required.'); return; }
				var $b = $(this).prop('disabled',true).text('Adding\u2026');
				$.post(AJAX, {
					action:'bt_add_notification', nonce:NONCE,
					title: title, excerpt: $('#bt-nf-excerpt').val(),
					url: $('#bt-nf-url').val(), badge: $('#bt-nf-badge').val(),
					badge_color: $('#bt-nf-badge-color').val(), image: $('#bt-nf-image-url').val()
				}, function(res) {
					$b.prop('disabled',false).text('Add Notification');
					if (!res.success) { notice('bt-notif-admin-notice', res.data||'Error.', 'error'); return; }
					var n = res.data;
					var img = n.image ? '<img src="'+n.image+'" style="width:38px;height:38px;object-fit:cover;border-radius:4px;">' : '<span style="color:#ccc;">&mdash;</span>';
					$('#bt-notif-tbody').prepend(
						'<tr id="bt-notif-row-'+n.id+'"><td>'+img+'</td>'
						+'<td><strong>'+$('<span>').text(n.title).html()+'</strong>'+(n.excerpt ? '<br><span style="font-size:12px;color:#666;">'+$('<span>').text(n.excerpt).html()+'</span>' : '')+'</td>'
						+'<td>'+$('<span>').text(n.badge).html()+'</td>'
						+'<td><button type="button" class="button button-link-delete bt-notif-delete" data-id="'+n.id+'">Delete</button></td></tr>'
					);
					$('#bt-notif-table').show(); $('#bt-notif-empty').hide();
					$('#bt-nf-title,#bt-nf-excerpt,#bt-nf-url,#bt-nf-badge').val('');
					$('#bt-nf-image-url').val(''); $('#bt-nf-preview').hide(); $('#bt-nf-remove-img').hide();
					notice('bt-notif-admin-notice','Notification added!','success');
				});
			});
			$(document).on('click','.bt-notif-delete', function() {
				if (!confirm('Delete this notification?')) return;
				var id = $(this).data('id'), $tr = $('#bt-notif-row-'+id);
				$.post(AJAX, { action:'bt_delete_notification', nonce:NONCE, id:id }, function(res){
					if (res.success) {
						$tr.remove();
						if (!$('#bt-notif-tbody tr').length) { $('#bt-notif-table').hide(); $('#bt-notif-empty').show(); }
					} else { alert('Delete failed.'); }
				});
			});
		}

		/* ── Carousel Slides tab ── */
		if ($('#bt-cs-add').length) {
			uploader('bt-cs-upload-btn','bt-cs-preview','bt-cs-image-url', null,'bt-cs-remove-img');
			fetchFill('bt-cs-fetch-url', {
				title:'bt-cs-title', excerpt:'bt-cs-excerpt', url:'bt-cs-link',
				image:'bt-cs-image-url', imgPrev:'bt-cs-preview', imgRm:'bt-cs-remove-img'
			}, 'bt-cs-fetch-btn');

			$('#bt-cs-add').on('click', function() {
				var title = $('#bt-cs-title').val().trim();
				if (!title) { alert('Title is required.'); return; }
				var $b = $(this).prop('disabled',true).text('Adding\u2026');
				$.post(AJAX, {
					action:'bt_add_carousel_slide', nonce:NONCE,
					title: title, excerpt: $('#bt-cs-excerpt').val(),
					link: $('#bt-cs-link').val(), image: $('#bt-cs-image-url').val(),
					badge: $('#bt-cs-badge').val(), badge_color: $('#bt-cs-badge-color').val(),
					temperature: $('#bt-cs-temperature').val(),
					button_text: $('#bt-cs-button-text').val()
				}, function(res) {
					$b.prop('disabled',false).text('Add Slide');
					if (!res.success) { notice('bt-carousel-notice', res.data||'Error.', 'error'); return; }
					var s = res.data;
					var img = s.image ? '<img src="'+s.image+'" style="width:38px;height:38px;object-fit:cover;border-radius:4px;">' : '<span style="color:#ccc;">&mdash;</span>';
					$('#bt-carousel-tbody').append(
						'<tr id="bt-cs-row-'+s.id+'" data-id="'+s.id+'">'
						+'<td>'+img+'</td>'
						+'<td><strong>'+$('<span>').text(s.title).html()+'</strong>'+(s.excerpt ? '<br><span style="font-size:12px;color:#666;">'+$('<span>').text(s.excerpt.substring(0,60)).html()+'</span>' : '')+'</td>'
						+'<td>'+$('<span>').text(s.badge).html()+'</td>'
						+'<td>'+s.temperature+'°</td>'
						+'<td style="white-space:nowrap;"><button type="button" class="button button-small bt-cs-up" data-id="'+s.id+'" title="Move up">↑</button> <button type="button" class="button button-small bt-cs-down" data-id="'+s.id+'" title="Move down">↓</button></td>'
						+'<td><button type="button" class="button button-link-delete bt-cs-delete" data-id="'+s.id+'">Delete</button></td>'
						+'</tr>'
					);
					$('#bt-carousel-table').show(); $('#bt-carousel-empty').hide();
					// Check if now full (5 slides)
					if ($('#bt-carousel-tbody tr').length >= 5) {
						$('#bt-carousel-add-form').hide();
						if (!$('#bt-carousel-full-notice').length) {
							$('#bt-carousel-add-form').before('<p id="bt-carousel-full-notice" style="color:#c00;font-weight:600;">5 slides maximum reached. Delete a slide to add a new one.</p>');
						} else { $('#bt-carousel-full-notice').show(); }
					}
					$('#bt-cs-title,#bt-cs-excerpt,#bt-cs-link,#bt-cs-badge,#bt-cs-button-text').val('');
					$('#bt-cs-temperature').val('0');
					$('#bt-cs-image-url').val(''); $('#bt-cs-preview').hide(); $('#bt-cs-remove-img').hide();
					notice('bt-carousel-notice','Slide added!','success');
				});
			});

			/* Delete slide */
			$(document).on('click','.bt-cs-delete', function() {
				if (!confirm('Delete this slide?')) return;
				var id = $(this).data('id'), $tr = $('#bt-cs-row-'+id);
				$.post(AJAX, { action:'bt_delete_carousel_slide', nonce:NONCE, id:id }, function(res){
					if (res.success) {
						$tr.remove();
						if (!$('#bt-carousel-tbody tr').length) { $('#bt-carousel-table').hide(); $('#bt-carousel-empty').show(); }
						$('#bt-carousel-add-form').show(); $('#bt-carousel-full-notice').hide();
					} else { alert('Delete failed.'); }
				});
			});

			/* Reorder: move up/down and sync to server */
			function btSaveOrder() {
				var order = [];
				$('#bt-carousel-tbody tr').each(function(){ order.push($(this).data('id')); });
				$.post(AJAX, { action:'bt_reorder_carousel_slides', nonce:NONCE, order:order });
			}
			$(document).on('click','.bt-cs-up', function() {
				var $tr = $(this).closest('tr');
				if ($tr.prev().length) { $tr.prev().before($tr); btSaveOrder(); }
			});
			$(document).on('click','.bt-cs-down', function() {
				var $tr = $(this).closest('tr');
				if ($tr.next().length) { $tr.next().after($tr); btSaveOrder(); }
			});
		}
	})(jQuery);
	</script>
	<?php
}

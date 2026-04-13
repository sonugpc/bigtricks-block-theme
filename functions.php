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

define( 'BIGTRICKS_VERSION', '1.0.0' );
define( 'BIGTRICKS_DIR', get_template_directory() );
define( 'BIGTRICKS_URI', get_template_directory_uri() );

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

	// Tailwind CSS via CDN (Play CDN for prototyping)
	wp_enqueue_script(
		'tailwind-cdn',
		'https://cdn.tailwindcss.com',
		[],
		null,
		false
	);

	// Lucide icons
	wp_enqueue_script(
		'lucide-icons',
		'https://unpkg.com/lucide@latest/dist/umd/lucide.min.js',
		[],
		null,
		true
	);

	// Theme main stylesheet
	wp_enqueue_style(
		'bigtricks-style',
		BIGTRICKS_URI . '/assets/css/bigtricks.css',
		[ 'bigtricks-google-fonts' ],
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

	// Pass data to JS
	$carousel_data = [];
	$carousel_json = BIGTRICKS_DIR . '/carousel-config.json';
	if ( file_exists( $carousel_json ) ) {
		$raw = file_get_contents( $carousel_json ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		if ( $raw ) {
			$parsed = json_decode( $raw, true );
			if ( is_array( $parsed ) ) {
				$carousel_data = array_slice( $parsed, 0, 5 );
			}
		}
	}

	wp_localize_script( 'bigtricks-main', 'bigtricksData', [
		'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
		'loadMoreNonce'=> wp_create_nonce( 'bigtricks_load_more' ),
		'restUrl'      => esc_url_raw( rest_url() ),
		'siteUrl'      => esc_url( home_url() ),
		'carouselData' => $carousel_data,
	] );
} );

// Tailwind config inline (after tailwind CDN loads)
add_action( 'wp_head', function () {
	// Anti-FOUC: apply dark mode class before first paint.
	// Must be priority 5 (before CDN) so the class is set before any rendering.
	echo '<script>!function(){var s=localStorage.getItem("bt_dark_mode");if(s==="1"||(s===null&&window.matchMedia&&window.matchMedia("(prefers-color-scheme: dark)").matches)){document.documentElement.classList.add("dark")}}</script>' . "\n";
	// Pre-set tailwind config BEFORE the CDN loads so it reads darkMode:'class' on init.
	echo '<script>window.tailwind={config:{darkMode:"class",theme:{extend:{fontFamily:{sans:["Plus Jakarta Sans","Inter","sans-serif"],body:["Inter","sans-serif"]},colors:{primary:{50:"#eef2ff",100:"#e0e7ff",200:"#c7d2fe",300:"#a5b4fc",400:"#818cf8",500:"#6366f1",600:"#4f46e5",700:"#4338ca",800:"#3730a3",900:"#312e81",DEFAULT:"#4f46e5"},secondary:{DEFAULT:"#0f172a",hover:"#1e293b"}}}}}}</script>' . "\n";
}, 5 );

// Also set tailwind.config AFTER the CDN loads (priority 20 > scripts at priority 9)
// so even if the CDN overwrites the global, we restore our config.
add_action( 'wp_head', function () {
	?>
	<script>
	if (typeof tailwind !== 'undefined') {
		tailwind.config = {
			darkMode: 'class',
			theme: {
				extend: {
					fontFamily: {
						sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
						body: ['Inter', 'sans-serif'],
					},
					colors: {
						primary: {
							50:  '#eef2ff',
							100: '#e0e7ff',
							200: '#c7d2fe',
							300: '#a5b4fc',
							400: '#818cf8',
							500: '#6366f1',
							600: '#4f46e5',
							700: '#4338ca',
							800: '#3730a3',
							900: '#312e81',
							DEFAULT: '#4f46e5',
						},
						secondary: {
							DEFAULT: '#0f172a',
							hover:   '#1e293b',
						},
					}
				}
			}
		};
	}
	</script>
	<?php
}, 20 );

// ─────────────────────────────────────────────
// 3. Post types are registered by plugins:
//    deal           → bigtricks-deals-wordpress
//    referral-codes → referral-code-plugin
//    credit-card    → credit-card-manager
// The 'store' taxonomy is shared across all three.
// ─────────────────────────────────────────────

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
			return '<a href="' . $href . '" target="_blank" rel="noopener noreferrer nofollow" class="' . $base_classes . ' bg-primary-600 hover:bg-primary-700 text-white shadow-primary-200">'
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
			return '<a href="' . $permalink . '" class="' . $base_classes . ' bg-primary-600 hover:bg-primary-700 text-white shadow-primary-200">'
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

// ─────────────────────────────────────────────
// 8. (Upvote handler removed — upvotes are not used)
// ─────────────────────────────────────────────

// ─────────────────────────────────────────────
// 9. Flush rewrite rules on theme activation
// ─────────────────────────────────────────────

add_action( 'after_switch_theme', function () {
	bigtricks_register_store_cpt_flush();
} );

function bigtricks_register_store_cpt_flush(): void {
	// Re-register CPT then flush
	flush_rewrite_rules();
}

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
	}

	$query = new WP_Query( $args );

	if ( ! $query->have_posts() ) {
		wp_send_json_success( [ 'html' => '', 'has_more' => false ] );
	}

	// Map CPT slug → template-part filename (without 'template-parts/' prefix)
	$template_map = [
		'post'           => 'card-post',
		'deal'           => 'card-deal',
		'referral-codes' => 'card-referral-code',
		'credit-card'    => 'card-credit-card',
	];

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
} );

// Admin UI: enqueue WP media library on term edit screens + shared JS picker
add_action( 'admin_enqueue_scripts', function ( string $hook ) {
	if ( ! in_array( $hook, [ 'edit-tags.php', 'term.php' ], true ) ) {
		return;
	}
	wp_enqueue_media();
} );

add_action( 'admin_footer-edit-tags.php', 'bigtricks_term_media_picker_js' );
add_action( 'admin_footer-term.php',      'bigtricks_term_media_picker_js' );

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
	<?php
} );

add_action( 'store_edit_form_fields', function ( WP_Term $term ) {
	$img_val     = get_term_meta( $term->term_id, 'thumb_image', true );
	$link_val    = get_term_meta( $term->term_id, 'st_link', true );
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
}

// ─────────────────────────────────────────────
// 12. Walker: nav menu with icon support (data-icon attribute on menu item)
// ─────────────────────────────────────────────

if ( ! class_exists( 'Bigtricks_Icon_Nav_Walker' ) ) :

class Bigtricks_Icon_Nav_Walker extends Walker_Nav_Menu {

	public function start_el( &$output, $data_object, $depth = 0, $args = null, $id = 0 ) {
		$item       = $data_object;
		$indent     = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$classes    = empty( $item->classes ) ? [] : (array) $item->classes;
		$classes[]  = 'menu-item-' . $item->ID;
		$class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );

		// Detect icon from title — supports [icon] prefix notation: "[shopping-bag] Stores"
		$title = $item->title;
		$icon  = '';
		if ( preg_match( '/^\[([a-z0-9\-]+)\]\s*(.+)$/i', $title, $m ) ) {
			$icon  = esc_attr( $m[1] );
			$title = esc_html( trim( $m[2] ) );
		}

		$atts              = [];
		$atts['title']     = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target']    = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']       = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']      = ! empty( $item->url ) ? $item->url : '';
		$atts['aria-current'] = $item->current ? 'page' : '';

		$is_active   = $item->current || $item->current_item_ancestor;
		$active_cls  = $is_active ? ' text-primary-600' : '';
		$depth_cls   = $depth === 0
			? 'flex items-center gap-1.5 hover:text-primary-600 transition-colors font-bold text-slate-600 text-sm py-1' . $active_cls
			: 'block px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-primary-50 hover:text-primary-600 transition-colors';

		$atts['class'] = $depth_cls;

		$attribs = '';
		foreach ( $atts as $attr => $value ) {
			if ( $value !== '' ) {
				$attribs .= ' ' . $attr . '="' . esc_attr( $value ) . '"';
			}
		}

		$icon_html = $icon ? '<i data-lucide="' . $icon . '" class="w-4 h-4 shrink-0"></i>' : '';

		$output .= $indent . '<li class="relative ' . esc_attr( $class_names ) . '">';
		$output .= '<a' . $attribs . '>' . $icon_html . '<span>' . $title . '</span></a>';
	}
}

endif;

// ─────────────────────────────────────────────
// 13. Notification / Bell data REST endpoint
// Returns carousel-config.json items as notifications
// ─────────────────────────────────────────────

add_action( 'rest_api_init', function () {
	register_rest_route( 'bigtricks/v1', '/notifications', [
		'methods'             => 'GET',
		'callback'            => 'bigtricks_get_notifications',
		'permission_callback' => '__return_true',
	] );
} );

function bigtricks_get_notifications( WP_REST_Request $request ): WP_REST_Response {
	$carousel_json = BIGTRICKS_DIR . '/carousel-config.json';
	$data          = [];

	if ( file_exists( $carousel_json ) ) {
		$raw = file_get_contents( $carousel_json ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		if ( $raw ) {
			$parsed = json_decode( $raw, true );
			if ( is_array( $parsed ) ) {
				foreach ( $parsed as $item ) {
					$data[] = [
						'id'          => absint( $item['id'] ?? 0 ),
						'title'       => sanitize_text_field( $item['title'] ?? '' ),
						'excerpt'     => sanitize_text_field( $item['excerpt'] ?? '' ),
						'link'        => esc_url_raw( $item['link'] ?? '' ),
						'badge'       => sanitize_text_field( $item['badge'] ?? '' ),
						'badge_color' => sanitize_text_field( $item['badge_color'] ?? '' ),
						'image'       => esc_url_raw( $item['image'] ?? '' ),
					];
				}
			}
		}
	}

	return new WP_REST_Response( $data, 200 );
}

<?php
/**
 * Store Taxonomy Archive — individual store page
 * Shows all posts (deals, offers, referral codes, credit cards) for a single 'store' term.
 *
 * Meta fields used:
 *   tag-image  — store logo (attachment ID or URL)
 *   st_link    — official store website URL
 *
 * @package Bigtricks
 */

get_header();

/* ──────────────────────────────────────────────────────────────
 * Query context
 * ────────────────────────────────────────────────────────────── */
$store_term  = get_queried_object();
$store_id    = $store_term instanceof WP_Term ? (int) $store_term->term_id : 0;
$store_name  = $store_term instanceof WP_Term ? $store_term->name : '';
$store_desc  = $store_term instanceof WP_Term ? $store_term->description : '';

// Store logo (tag-image term meta — URL or attachment ID)
$tag_image  = $store_id ? get_term_meta( $store_id, 'thumb_image', true ) : '';
$logo_url   = '';
if ( $tag_image ) {
	$logo_url = is_numeric( $tag_image )
		? (string) wp_get_attachment_image_url( (int) $tag_image, 'thumbnail' )
		: $tag_image;
}

// Official store URL
$st_link    = $store_id ? esc_url( (string) get_term_meta( $store_id, 'st_link', true ) ) : '';

// Initials fallback colour (deterministic from term ID)
$pastel_colors = [
	'bg-primary-100 text-primary-600',
	'bg-pink-100 text-pink-600',
	'bg-emerald-100 text-emerald-600',
	'bg-orange-100 text-orange-600',
	'bg-purple-100 text-purple-600',
	'bg-cyan-100 text-cyan-600',
	'bg-amber-100 text-amber-700',
	'bg-blue-100 text-blue-700',
];
$color_class = $store_id ? $pastel_colors[ $store_id % count( $pastel_colors ) ] : $pastel_colors[0];

// Active filter type from URL (?type=deal|post|referral-codes|credit-card)
$active_type   = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : 'all'; // phpcs:ignore WordPress.Security.NonceVerification
$allowed_types = [ 'all', 'post', 'deal', 'referral-codes', 'credit-card' ];
if ( ! in_array( $active_type, $allowed_types, true ) ) {
	$active_type = 'all';
}

$query_post_types = $active_type === 'all'
	? [ 'post', 'deal', 'referral-codes', 'credit-card' ]
	: [ $active_type ];

$paged      = ( get_query_var( 'paged' ) ) ? (int) get_query_var( 'paged' ) : 1;
$query_args = [
	'post_type'      => $query_post_types,
	'post_status'    => 'publish',
	'posts_per_page' => 12,
	'paged'          => $paged,
	'orderby'        => 'date',
	'order'          => 'DESC',
	'tax_query'      => [ // phpcs:ignore WordPress.DB.SlowDBQuery
		[
			'taxonomy' => 'store',
			'field'    => 'term_id',
			'terms'    => $store_id,
		],
	],
];

$feed_query  = new WP_Query( $query_args );
$total_posts = $feed_query->found_posts;
$max_pages   = $feed_query->max_num_pages;

// Per-type count badges for filter chips — single query instead of 4 WP_Query calls
$type_counts = [ 'post' => 0, 'deal' => 0, 'referral-codes' => 0, 'credit-card' => 0 ];
if ( $store_id && $store_term instanceof WP_Term ) {
	global $wpdb;
	$ttid = (int) $store_term->term_taxonomy_id;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT p.post_type, COUNT(*) AS cnt
			FROM {$wpdb->posts} p
			INNER JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
			WHERE tr.term_taxonomy_id = %d
			AND p.post_status = 'publish'
			AND p.post_type IN ('post','deal','referral-codes','credit-card')
			GROUP BY p.post_type",
			$ttid
		)
	);
	foreach ( $rows as $row ) {
		$type_counts[ $row->post_type ] = (int) $row->cnt;
	}
}

$type_labels = [
	'all'            => __( 'All', 'bigtricks' ),
	'post'           => __( 'Offers', 'bigtricks' ),
	'deal'           => __( 'Deals', 'bigtricks' ),
	'referral-codes' => __( 'Referral Codes', 'bigtricks' ),
	'credit-card'    => __( 'Credit Cards', 'bigtricks' ),
];

$card_template_map = [
	'post'           => 'card-post',
	'deal'           => 'card-deal',
	'referral-codes' => 'card-referral-code',
	'credit-card'    => 'card-credit-card',
];

/* Term link for filter chip URLs */
$store_base_url = esc_url( (string) get_term_link( $store_term ) );

/* SEO: override document title to be descriptive */
add_filter( 'pre_get_document_title', function () use ( $store_name ) {
	/* translators: %s: store name */
	return sprintf( __( 'Best %s Deals, Offers & Coupons | Bigtricks', 'bigtricks' ), $store_name );
} );
?>

<main class="max-w-[1400px] mx-auto px-4 py-6 md:py-8 flex flex-col lg:flex-row gap-8 flex-1 w-full box-border" id="main-content">

	<div class="flex-1 min-w-0 w-full overflow-hidden">

		<!-- ═══ STORE HERO ═══ -->
		<div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 sm:p-8 mb-8 flex flex-col sm:flex-row items-start sm:items-center gap-6 overflow-hidden relative">
			<!-- Decorative background -->
			<div class="absolute inset-0 bg-gradient-to-br from-primary-50/60 via-white to-purple-50/30 dark:from-slate-800 dark:via-slate-800 dark:to-slate-800 pointer-events-none" aria-hidden="true"></div>

			<!-- Store Logo / Initials -->
			<div class="relative z-10 shrink-0">
				<?php if ( $logo_url ) : ?>
				<div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl border-2 border-white shadow-lg overflow-hidden bg-white flex items-center justify-center">
					<img
						src="<?php echo esc_url( $logo_url ); ?>"
						alt="<?php echo esc_attr( $store_name ); ?> logo"
						class="w-full h-full object-contain mix-blend-multiply"
						loading="eager"
						decoding="async"
						width="112"
						height="112"
					>
				</div>
				<?php else : ?>
				<div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl <?php echo esc_attr( $color_class ); ?> flex items-center justify-center text-3xl sm:text-4xl font-black shadow-md border-2 border-white">
					<?php echo esc_html( mb_strtoupper( mb_substr( $store_name, 0, 2 ) ) ); ?>
				</div>
				<?php endif; ?>
			</div>

			<!-- Store info -->
			<div class="relative z-10 flex-1 min-w-0">
				<div class="flex items-center gap-3 mb-2 flex-wrap">
					<span class="bg-primary-100 text-primary-700 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider border border-primary-200">
						<?php esc_html_e( 'Store', 'bigtricks' ); ?>
					</span>
					<span class="text-slate-500 text-sm font-bold">
						<?php
						/* translators: %s: formatted post count */
						printf(
							/* translators: %s: number of items */
							esc_html( _n( '%s offer & deal', '%s offers & deals', $total_posts, 'bigtricks' ) ),
							esc_html( number_format_i18n( $total_posts ) )
						);
						?>
					</span>
				</div>

				<!-- SEO-optimised h1 -->
				<h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 leading-tight mb-3 break-words">
					<?php
					printf(
						/* translators: %s: store name */
						esc_html__( '%s Offers & Deals', 'bigtricks' ),
						esc_html( $store_name )
					);
					?>
				</h1>

				<?php if ( $store_desc ) : ?>
				<div class="prose prose-slate max-w-none prose-img:rounded-2xl prose-img:shadow-md prose-a:text-primary-600 hover:prose-a:text-primary-800 prose-headings:font-black prose-p:leading-relaxed prose-p:text-slate-600 dark:prose-invert break-words mb-4">
					<?php echo wp_kses_post( do_shortcode( wpautop( $store_desc ) ) ); ?>
				</div>
				<?php endif; ?>

				<!-- Visit Store CTA -->
				<?php if ( $st_link ) : ?>
				<a
					href="<?php echo esc_url( $st_link ); ?>"
					target="_blank"
					rel="noopener noreferrer nofollow"
					class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-black rounded-xl shadow-md shadow-primary-200 dark:shadow-none transition-all active:scale-95"
					aria-label="<?php printf( esc_attr__( 'Visit %s official website', 'bigtricks' ), esc_attr( $store_name ) ); ?>"
				>
					<i data-lucide="external-link" class="w-4 h-4 shrink-0"></i>
					<?php esc_html_e( 'Visit Store', 'bigtricks' ); ?>
				</a>
				<?php endif; ?>
			</div>
		</div><!-- /store hero -->

		<!-- ═══ FILTER CHIPS ═══ -->
		<div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
			<nav class="flex items-center gap-2 overflow-x-auto pb-1" style="scrollbar-width:none;" aria-label="<?php esc_attr_e( 'Filter by content type', 'bigtricks' ); ?>">
				<?php foreach ( $type_labels as $type_key => $type_label ) :
					$is_active  = $type_key === $active_type;
					$filter_url = $type_key === 'all'
						? remove_query_arg( 'type', $store_base_url )
						: add_query_arg( 'type', $type_key, $store_base_url );
					$chip_count = $type_key !== 'all' ? ( $type_counts[ $type_key ] ?? 0 ) : $total_posts;
					?>
				<a
					href="<?php echo esc_url( $filter_url ); ?>"
					class="whitespace-nowrap inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-bold transition-all <?php echo $is_active ? 'bg-primary-600 text-white shadow-md shadow-primary-200 dark:shadow-none' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-300'; ?>"
					aria-current="<?php echo $is_active ? 'true' : 'false'; ?>"
				>
					<?php echo esc_html( $type_label ); ?>
					<?php if ( $chip_count > 0 ) : ?>
					<span class="<?php echo $is_active ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-500'; ?> text-xs font-black px-1.5 py-0.5 rounded-full leading-none">
						<?php echo esc_html( number_format_i18n( $chip_count ) ); ?>
					</span>
					<?php endif; ?>
				</a>
				<?php endforeach; ?>
			</nav>

			<!-- View Toggle -->
			<div class="hidden sm:flex items-center bg-white border border-slate-200 rounded-xl p-1 shadow-sm shrink-0">
				<button id="bt-view-list" class="bt-view-toggle p-1.5 rounded-lg transition-colors bg-primary-50 text-primary-600" data-view="list" aria-label="<?php esc_attr_e( 'List view', 'bigtricks' ); ?>" aria-pressed="true">
					<i data-lucide="list" class="w-4 h-4"></i>
				</button>
				<button id="bt-view-grid" class="bt-view-toggle p-1.5 rounded-lg transition-colors text-slate-400 hover:text-slate-600" data-view="grid" aria-label="<?php esc_attr_e( 'Grid view', 'bigtricks' ); ?>" aria-pressed="false">
					<i data-lucide="layout-grid" class="w-4 h-4"></i>
				</button>
			</div>
		</div>

		<!-- ═══ FEED ═══ -->
		<div
			id="bt-feed-container"
			class="space-y-6"
			data-view="list"
			data-page="1"
			data-max-pages="<?php echo esc_attr( $max_pages ); ?>"
			data-type="<?php echo esc_attr( $active_type ); ?>"
			data-store="<?php echo esc_attr( $store_id ); ?>"
		>
			<?php if ( ! $feed_query->have_posts() ) : ?>
			<div class="bg-white p-12 rounded-3xl border border-slate-200 text-center">
				<div class="bg-slate-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
					<i data-lucide="shopping-bag" class="w-8 h-8 text-slate-400"></i>
				</div>
				<h2 class="text-xl font-bold text-slate-900 mb-2"><?php esc_html_e( 'No posts found', 'bigtricks' ); ?></h2>
				<p class="text-slate-500 mb-4">
					<?php
					printf(
						/* translators: %s: store name */
						esc_html__( 'No %s content matches this filter yet.', 'bigtricks' ),
						esc_html( $store_name )
					);
					?>
				</p>
				<a href="<?php echo esc_url( $store_base_url ); ?>" class="inline-block bg-primary-50 text-primary-700 font-bold px-6 py-2 rounded-full hover:bg-primary-100 transition-colors">
					<?php esc_html_e( 'View All', 'bigtricks' ); ?>
				</a>
			</div>
			<?php endif; ?>

			<?php while ( $feed_query->have_posts() ) :
				$feed_query->the_post();
				$post_id      = get_the_ID();
				$current_type = get_post_type();
				$tpl_slug     = $card_template_map[ $current_type ] ?? 'card-post';
				get_template_part( 'template-parts/' . $tpl_slug, null, [ 'post_id' => $post_id ] );
			endwhile;
			wp_reset_postdata(); ?>
		</div><!-- /#bt-feed-container -->

		<!-- ═══ LOAD MORE ═══ -->
		<?php if ( $max_pages > 1 ) : ?>
		<div class="mt-8 flex justify-center" id="bt-load-more-wrap">
			<button
				id="bt-load-more"
					class="flex items-center gap-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 hover:border-primary-400 text-slate-700 dark:text-slate-300 hover:text-primary-600 font-black px-8 py-4 rounded-2xl shadow-sm hover:shadow-md dark:shadow-slate-900/20 dark:hover:shadow-slate-900/40 transition-all active:scale-95"
				data-page="1"
				data-max-pages="<?php echo esc_attr( $max_pages ); ?>"
				data-store="<?php echo esc_attr( $store_id ); ?>"
				data-type="<?php echo esc_attr( $active_type ); ?>"
				data-nonce="<?php echo esc_attr( wp_create_nonce( 'bigtricks_load_more' ) ); ?>"
			>
				<i data-lucide="refresh-cw" class="w-5 h-5"></i>
				<?php esc_html_e( 'Load More', 'bigtricks' ); ?>
			</button>
		</div>
		<?php endif; ?>

	</div><!-- /main column -->

	<!-- ═══ SIDEBAR ═══ -->
	<?php get_sidebar(); ?>

</main><!-- /#main-content -->

<?php get_footer(); ?>

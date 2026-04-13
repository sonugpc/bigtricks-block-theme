<?php
/**
 * Front Page / Homepage (Feed + Carousel)
 * Carousel data from carousel-config.json (not post-based).
 * Feed uses AJAX load-more instead of page-based pagination.
 *
 * @package Bigtricks
 */

get_header();

// Current category filter from URL
$active_cat  = isset( $_GET['cat'] ) ? absint( $_GET['cat'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
$active_type = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : 'all'; // phpcs:ignore WordPress.Security.NonceVerification
$paged       = 1; // Always page 1 on homepage; more loaded via AJAX

$allowed_types = [ 'all', 'post', 'deal', 'referral-codes', 'credit-card' ];
if ( ! in_array( $active_type, $allowed_types, true ) ) {
	$active_type = 'all';
}

// Determine which post types to query
$query_post_types = $active_type === 'all'
	? [ 'post', 'deal', 'referral-codes', 'credit-card' ]
	: [ $active_type ];

// Build main query args
$query_args = [
	'post_type'      => $query_post_types,
	'post_status'    => 'publish',
	'posts_per_page' => 12,
	'paged'          => $paged,
	'orderby'        => 'date',
	'order'          => 'DESC',
];

if ( $active_cat > 0 ) {
	$query_args['tax_query'] = [ // phpcs:ignore WordPress.DB.SlowDBQuery
		[
			'taxonomy' => 'category',
			'field'    => 'term_id',
			'terms'    => $active_cat,
		],
	];
}

$feed_query  = new WP_Query( $query_args );
$total_posts = $feed_query->found_posts;
$max_pages   = $feed_query->max_num_pages;

// ── Carousel: load from JSON config ──────────────────────
$carousel_posts = [];
$carousel_json  = BIGTRICKS_DIR . '/carousel-config.json';
if ( file_exists( $carousel_json ) ) {
	$raw = file_get_contents( $carousel_json ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	if ( $raw ) {
		$parsed = json_decode( $raw, true );
		if ( is_array( $parsed ) ) {
			$carousel_posts = array_slice( $parsed, 0, 5 ); // max 5 slides
		}
	}
}

$type_labels = [
	'all'            => __( 'All', 'bigtricks' ),
	'post'           => __( 'Offers', 'bigtricks' ),
	'deal'           => __( 'Deals', 'bigtricks' ),
	'referral-codes' => __( 'Referral Codes', 'bigtricks' ),
	'credit-card'    => __( 'Credit Cards', 'bigtricks' ),
];
?>

<main class="max-w-[1400px] mx-auto px-4 py-6 md:py-8 flex flex-col lg:flex-row gap-8 flex-1 w-full box-border" id="main-content">

	<!-- Left Column: Feed -->
	<div class="flex-1 min-w-0 w-full overflow-hidden">

		<!-- ═══ FEATURED CAROUSEL (JSON-config, no posts) ═══ -->
		<?php
		$badge_color_map = [
			'red'    => 'bg-red-500 text-white',
			'emerald'=> 'bg-emerald-500 text-white',
			'purple' => 'bg-purple-600 text-white',
			'blue'   => 'bg-blue-500 text-white',
			'orange' => 'bg-orange-500 text-white',
		];
		?>
		<?php if ( ! empty( $carousel_posts ) && ! $active_cat ) : ?>
		<div
			class="mb-8 relative rounded-3xl overflow-hidden bg-slate-900 h-[300px] sm:h-[380px] shadow-xl bt-carousel"
			data-total="<?php echo count( $carousel_posts ); ?>"
			aria-roledescription="carousel"
			aria-label="<?php esc_attr_e( 'Featured Deals', 'bigtricks' ); ?>"
		>
			<?php foreach ( $carousel_posts as $ci => $cp ) :
				$badge_css = $badge_color_map[ $cp['badge_color'] ?? 'red' ] ?? 'bg-primary-600 text-white';
				?>
			<div
				class="absolute inset-0 transition-opacity duration-700 bt-carousel-slide <?php echo $ci === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0'; ?>"
				role="group"
				aria-roledescription="slide"
				aria-label="<?php echo esc_attr( $ci + 1 ); ?> of <?php echo esc_attr( count( $carousel_posts ) ); ?>"
				aria-hidden="<?php echo $ci === 0 ? 'false' : 'true'; ?>"
			>
				<!-- Background image -->
				<div class="absolute inset-0">
					<img
						src="<?php echo esc_url( $cp['image'] ?? '' ); ?>"
						alt=""
						aria-hidden="true"
						class="w-full h-full object-cover"
						loading="<?php echo $ci === 0 ? 'eager' : 'lazy'; ?>"
						decoding="async"
					>
					<div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/70 to-slate-900/30"></div>
				</div>

				<!-- Content overlay -->
				<div class="absolute inset-0 p-6 sm:p-10 flex flex-col justify-end">
					<!-- Badges -->
					<div class="flex items-center gap-2 mb-3 flex-wrap">
						<span class="<?php echo esc_attr( $badge_css ); ?> text-xs font-black px-3 py-1.5 rounded-full uppercase tracking-wider shadow-lg">
							<?php echo esc_html( $cp['badge'] ?? 'Featured' ); ?>
						</span>
						<?php if ( ( $cp['temperature'] ?? 0 ) > 0 ) : ?>
						<span class="bg-orange-500/90 backdrop-blur-sm text-white text-xs font-black px-3 py-1.5 rounded-full flex items-center gap-1 shadow-lg border border-orange-400/40">
							<i data-lucide="flame" class="w-3 h-3 fill-current"></i> <?php echo esc_html( (int) $cp['temperature'] ); ?>°
						</span>
						<?php endif; ?>
					</div>

					<!-- Title -->
					<h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white leading-tight mb-2 line-clamp-2 drop-shadow-lg" style="text-shadow:0 2px 8px rgba(0,0,0,0.4)">
						<a href="<?php echo esc_url( $cp['link'] ?? '#' ); ?>" class="hover:text-primary-300 transition-colors">
							<?php echo esc_html( $cp['title'] ?? '' ); ?>
						</a>
					</h2>

					<!-- Excerpt -->
					<p class="text-slate-200 text-sm sm:text-base line-clamp-2 mb-5 max-w-2xl leading-relaxed">
						<?php echo esc_html( $cp['excerpt'] ?? '' ); ?>
					</p>

					<!-- CTA -->
					<div class="flex">
						<a href="<?php echo esc_url( $cp['link'] ?? '#' ); ?>" class="bg-white text-slate-900 px-7 py-3 rounded-xl font-black text-sm flex items-center gap-2 hover:bg-primary-50 transition-all shadow-lg active:scale-95">
							<?php esc_html_e( 'See Deal', 'bigtricks' ); ?> <i data-lucide="arrow-right" class="w-4 h-4"></i>
						</a>
					</div>
				</div>
			</div>
			<?php endforeach; ?>

			<!-- Carousel Controls (prev/next) -->
			<div class="absolute top-1/2 -translate-y-1/2 left-3 z-20">
				<button class="bt-carousel-prev w-10 h-10 rounded-full bg-black/30 hover:bg-black/50 backdrop-blur-sm flex items-center justify-center text-white transition-colors border border-white/20" aria-label="<?php esc_attr_e( 'Previous slide', 'bigtricks' ); ?>">
					<i data-lucide="chevron-left" class="w-5 h-5"></i>
				</button>
			</div>
			<div class="absolute top-1/2 -translate-y-1/2 right-3 z-20">
				<button class="bt-carousel-next w-10 h-10 rounded-full bg-black/30 hover:bg-black/50 backdrop-blur-sm flex items-center justify-center text-white transition-colors border border-white/20" aria-label="<?php esc_attr_e( 'Next slide', 'bigtricks' ); ?>">
					<i data-lucide="chevron-right" class="w-5 h-5"></i>
				</button>
			</div>

			<!-- Dot Indicators -->
			<div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 flex gap-1.5 bt-carousel-dots">
				<?php foreach ( $carousel_posts as $ci => $_ ) : ?>
				<button
					class="bt-carousel-dot h-2 rounded-full transition-all duration-300 <?php echo $ci === 0 ? 'bg-white w-6' : 'bg-white/40 w-2'; ?>"
					data-index="<?php echo esc_attr( $ci ); ?>"
					aria-label="<?php echo esc_attr( sprintf( __( 'Go to slide %d', 'bigtricks' ), $ci + 1 ) ); ?>"
				></button>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>

		<!-- ═══ FEED HEADER: Title + Filters + View Toggle ═══ -->
		<div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
			<h1 class="text-2xl font-black text-slate-900 flex items-center gap-3">
				<div class="bg-orange-100 p-2 rounded-xl text-orange-600 shadow-sm">
					<i data-lucide="flame" class="w-6 h-6 fill-current"></i>
				</div>
				<?php echo $active_cat ? esc_html( get_cat_name( $active_cat ) ) : esc_html__( 'Latest Updates', 'bigtricks' ); ?>
			</h1>

			<div class="flex items-center gap-4 justify-between md:justify-end">
				<!-- Type Filter Chips -->
				<div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0 flex-1 md:flex-none" style="scrollbar-width:none;">
					<?php foreach ( $type_labels as $type_key => $type_label ) :
						$is_active   = $type_key === $active_type;
						$filter_url  = add_query_arg( [
							'type' => $type_key === 'all' ? false : $type_key,
							'cat'  => $active_cat ?: false,
						], home_url( '/' ) );
						?>
					<a
						href="<?php echo esc_url( $filter_url ); ?>"
						class="whitespace-nowrap px-4 py-1.5 rounded-full text-sm font-bold transition-all <?php echo $is_active ? 'bg-primary-600 text-white shadow-md shadow-primary-200' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 hover:border-slate-300'; ?>"
						aria-current="<?php echo $is_active ? 'true' : 'false'; ?>"
					>
						<?php echo esc_html( $type_label ); ?>
					</a>
					<?php endforeach; ?>
				</div>

				<!-- View Toggle (list/grid) -->
				<div class="hidden sm:flex items-center bg-white border border-slate-200 rounded-xl p-1 shadow-sm shrink-0">
					<button
						id="bt-view-list"
						class="bt-view-toggle p-1.5 rounded-lg transition-colors bg-primary-50 text-primary-600"
						data-view="list"
						aria-label="<?php esc_attr_e( 'List view', 'bigtricks' ); ?>"
						aria-pressed="true"
					>
						<i data-lucide="list" class="w-4 h-4"></i>
					</button>
					<button
						id="bt-view-grid"
						class="bt-view-toggle p-1.5 rounded-lg transition-colors text-slate-400 hover:text-slate-600"
						data-view="grid"
						aria-label="<?php esc_attr_e( 'Grid view', 'bigtricks' ); ?>"
						aria-pressed="false"
					>
						<i data-lucide="layout-grid" class="w-4 h-4"></i>
					</button>
				</div>
			</div>
		</div>

		<!-- ═══ DEALS CONTAINER ═══ -->
		<div
			id="bt-feed-container"
			class="space-y-6"
			data-view="list"
			data-page="1"
			data-max-pages="<?php echo esc_attr( $max_pages ); ?>"
			data-cat="<?php echo esc_attr( $active_cat ); ?>"
			data-type="<?php echo esc_attr( $active_type ); ?>"
		>

			<?php if ( ! $feed_query->have_posts() ) : ?>
			<div class="bg-white p-12 rounded-3xl border border-slate-200 text-center">
				<div class="bg-slate-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
					<i data-lucide="search" class="w-8 h-8 text-slate-400"></i>
				</div>
				<h3 class="text-xl font-bold text-slate-900 mb-2"><?php esc_html_e( 'No deals found', 'bigtricks' ); ?></h3>
				<p class="text-slate-500 mb-6"><?php esc_html_e( 'Try selecting a different filter or category.', 'bigtricks' ); ?></p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="bg-primary-50 text-primary-700 font-bold px-6 py-2 rounded-full hover:bg-primary-100 transition-colors">
					<?php esc_html_e( 'View All Updates', 'bigtricks' ); ?>
				</a>
			</div>
			<?php endif; ?>

			<?php $post_index = 0; ?>
		<?php
		// Template-part map: CPT slug → template-parts file slug
		$card_template_map = [
			'post'           => 'card-post',
			'deal'           => 'card-deal',
			'referral-codes' => 'card-referral-code',
			'credit-card'    => 'card-credit-card',
		];
		?>
		<?php while ( $feed_query->have_posts() ) :
			$feed_query->the_post();
			$post_id      = get_the_ID();
			$current_type = get_post_type();
			$tpl_slug     = $card_template_map[ $current_type ] ?? 'card-post';
			?>
			<?php if ( $post_index === 3 ) : // In-feed CTA after 3rd post ?>
			<div class="bg-gradient-to-r from-primary-600 via-blue-600 to-cyan-500 rounded-3xl p-6 sm:p-10 text-white shadow-xl shadow-primary-200/50 relative overflow-hidden group">
				<div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
				<div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6 lg:gap-8">
					<div class="text-center lg:text-left">
						<h3 class="text-2xl sm:text-3xl font-black mb-3"><?php esc_html_e( 'Never Miss a Loot! 🚀', 'bigtricks' ); ?></h3>
						<p class="text-primary-100 text-base sm:text-lg font-medium max-w-xl">
							<?php esc_html_e( 'Join our community of over 100,000 smart shoppers. Get instant push notifications for price drops and secret coupons.', 'bigtricks' ); ?>
						</p>
					</div>
					<div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto shrink-0">
						<a href="https://t.me/bigtricks" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto bg-white text-primary-600 font-black py-3.5 px-6 sm:px-8 rounded-2xl shadow-lg hover:scale-105 hover:shadow-xl transition-all flex items-center justify-center gap-3">
							<i data-lucide="send" class="w-5 h-5 text-blue-500"></i>
							<?php esc_html_e( 'Join Telegram', 'bigtricks' ); ?>
						</a>
						<a href="https://wa.me/bigtricks" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto bg-[#25D366] text-white font-black py-3.5 px-6 sm:px-8 rounded-2xl shadow-lg hover:scale-105 hover:shadow-xl transition-all flex items-center justify-center gap-3">
							<i data-lucide="message-circle" class="w-5 h-5"></i>
							<?php esc_html_e( 'WhatsApp', 'bigtricks' ); ?>
						</a>
					</div>
				</div>
			</div>
			<?php endif; ?>

			<?php get_template_part( 'template-parts/' . $tpl_slug, null, [ 'post_id' => $post_id ] ); ?>
			<?php $post_index++; endwhile; wp_reset_postdata(); ?>

		</div><!-- /#bt-feed-container -->

		<!-- AJAX LOAD MORE BUTTON -->
		<?php if ( $max_pages > 1 ) : ?>
		<div class="mt-8 flex justify-center" id="bt-load-more-wrap">
			<button
				id="bt-load-more"
				class="flex items-center gap-3 bg-white border-2 border-slate-200 hover:border-primary-400 text-slate-700 hover:text-primary-600 font-black px-8 py-4 rounded-2xl shadow-sm hover:shadow-md transition-all active:scale-95 group"
				data-page="1"
				data-max-pages="<?php echo esc_attr( $max_pages ); ?>"
				data-cat="<?php echo esc_attr( $active_cat ); ?>"
				data-type="<?php echo esc_attr( $active_type ); ?>"
				data-nonce="<?php echo esc_attr( wp_create_nonce( 'bigtricks_load_more' ) ); ?>"
			>
				<i data-lucide="refresh-cw" class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500"></i>
				<span><?php esc_html_e( 'Load More', 'bigtricks' ); ?></span>
				<?php
				$remaining = $total_posts - $feed_query->post_count;
				if ( $remaining > 0 ) :
				?>
				<span class="bg-slate-100 text-slate-500 text-xs font-bold px-2.5 py-1 rounded-full">
					<?php echo esc_html( $remaining ); ?> <?php esc_html_e( 'more', 'bigtricks' ); ?>
				</span>
				<?php endif; ?>
			</button>
		</div>
		<?php endif; ?>
	</div><!-- /Left Column -->

	<?php get_sidebar(); ?>

</main>

<?php get_footer(); ?>

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

// ── Carousel: load from DB (Settings → Banners & Alerts → Hero Carousel) ──
$carousel_posts = bt_get_carousel_slides();

$type_labels = [
	'all'            => __( 'All', 'bigtricks' ),
	'post'           => __( 'Offers', 'bigtricks' ),
	'deal'           => __( 'Deals', 'bigtricks' ),
	'referral-codes' => __( 'Referral Codes', 'bigtricks' ),
	'credit-card'    => __( 'Credit Cards', 'bigtricks' ),
];
?>
<main>

<div class="max-w-[1400px] mx-auto px-4 py-6 md:py-8 flex flex-col lg:flex-row gap-8 flex-1 w-full box-border" id="main-content">

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
		<?php if ( ! empty( $carousel_posts ) && ! $active_cat ) :
			$slide_count = count( $carousel_posts );
		?>
		<div
			class="mb-8 relative rounded-3xl overflow-hidden bg-slate-900 h-[300px] sm:h-[380px] shadow-xl bt-carousel"
			data-total="<?php echo $slide_count; ?>"
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
				aria-label="<?php echo esc_attr( $ci + 1 ); ?> of <?php echo esc_attr( $slide_count ); ?>"
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
						<?php if ( $ci === 0 ) echo 'fetchpriority="high"'; ?>
						decoding="<?php echo $ci === 0 ? 'sync' : 'async'; ?>"
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
							<a href="<?php echo esc_url( $cp['link'] ?? '#' ); ?>" class="bg-white text-slate-900 px-7 py-3 rounded-xl font-black text-sm flex items-center gap-2 hover:bg-primary-50 transition-all shadow-lg dark:shadow-slate-900/30 active:scale-95">
						<?php echo esc_html( ! empty( $cp['button_text'] ) ? $cp['button_text'] : __( 'See Deal', 'bigtricks' ) ); ?> <i data-lucide="arrow-right" class="w-4 h-4"></i>
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
				<?php for ( $ci = 0; $ci < $slide_count; $ci++ ) : ?>
				<button
					class="bt-carousel-dot h-2 rounded-full transition-all duration-300 <?php echo $ci === 0 ? 'bg-white w-6' : 'bg-white/40 w-2'; ?>"
					data-index="<?php echo esc_attr( $ci ); ?>"
					aria-label="<?php echo esc_attr( sprintf( __( 'Go to slide %d', 'bigtricks' ), $ci + 1 ) ); ?>"
				></button>
				<?php endfor; ?>
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
						class="whitespace-nowrap px-4 py-1.5 rounded-full text-sm font-bold transition-all <?php echo $is_active ? 'bg-primary-600 text-white shadow-md shadow-primary-200 dark:shadow-none' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-300'; ?>"
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
		// Social URLs hoisted outside the loop to avoid repeated DB queries.
		$bt_telegram  = bigtricks_option( 'bt_telegram_url' );
		$bt_whatsapp  = bigtricks_option( 'bt_whatsapp_url' );
		$bt_youtube   = bigtricks_option( 'bt_youtube_url' );
		$bt_facebook  = bigtricks_option( 'bt_facebook_url' );
		$bt_linkedin  = bigtricks_option( 'bt_linkedin_url' );
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
			<?php if ( $post_index === 3 ) : // In-feed CTA after 3rd post
		?>
		<div class="bt-feed-cta bg-gradient-to-r from-primary-600 via-blue-600 to-cyan-500 rounded-3xl p-6 sm:p-10 text-white shadow-xl shadow-primary-200/50 dark:shadow-slate-900/50 relative overflow-hidden group">
			<div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2 pointer-events-none"></div>
			<div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-6 lg:gap-8">
				<div class="text-center lg:text-left">
					<h3 class="text-2xl text-white sm:text-3xl font-black mb-3"><?php esc_html_e( 'Never Miss a Loot! 🚀', 'bigtricks' ); ?></h3>
					<p class="text-primary-100 text-base sm:text-lg font-medium max-w-xl">
						<?php esc_html_e( 'Join our community of over 100,000 smart shoppers. Get instant push notifications for price drops and secret coupons.', 'bigtricks' ); ?>
					</p>
				</div>
				<div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto shrink-0">
					<?php if ( $bt_telegram ) : ?>
					<a href="<?php echo esc_url( $bt_telegram ); ?>" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto bg-white text-primary-600 font-black py-3.5 px-6 sm:px-8 rounded-2xl shadow-lg hover:scale-105 hover:shadow-xl dark:shadow-slate-900/30 dark:hover:shadow-slate-900/50 transition-all flex items-center justify-center gap-3">
						<i data-lucide="send" class="w-5 h-5 text-blue-500"></i>
						<?php esc_html_e( 'Join Telegram', 'bigtricks' ); ?>
					</a>
					<?php endif; ?>
					<?php if ( $bt_whatsapp ) : ?>
					<a href="<?php echo esc_url( $bt_whatsapp ); ?>" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto bg-[#25D366] text-white font-black py-3.5 px-6 sm:px-8 rounded-2xl shadow-lg hover:scale-105 hover:shadow-xl transition-all flex items-center justify-center gap-3">
						<i data-lucide="message-circle" class="w-5 h-5"></i>
						<?php esc_html_e( 'WhatsApp', 'bigtricks' ); ?>
					</a>
					<?php endif; ?>
					<?php if ( $bt_youtube ) : ?>
					<a href="<?php echo esc_url( $bt_youtube ); ?>" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto bg-red-600 text-white font-black py-3.5 px-6 sm:px-8 rounded-2xl shadow-lg hover:scale-105 hover:shadow-xl transition-all flex items-center justify-center gap-3">
						<i data-lucide="youtube" class="w-5 h-5"></i>
						<?php esc_html_e( 'YouTube', 'bigtricks' ); ?>
					</a>
					<?php endif; ?>
					<?php if ( $bt_facebook ) : ?>
					<a href="<?php echo esc_url( $bt_facebook ); ?>" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto bg-blue-600 text-white font-black py-3.5 px-6 sm:px-8 rounded-2xl shadow-lg hover:scale-105 hover:shadow-xl transition-all flex items-center justify-center gap-3">
						<i data-lucide="facebook" class="w-5 h-5"></i>
						<?php esc_html_e( 'Facebook', 'bigtricks' ); ?>
					</a>
					<?php endif; ?>
					<?php if ( $bt_linkedin ) : ?>
					<a href="<?php echo esc_url( $bt_linkedin ); ?>" target="_blank" rel="noopener noreferrer" class="w-full sm:w-auto bg-blue-700 text-white font-black py-3.5 px-6 sm:px-8 rounded-2xl shadow-lg hover:scale-105 hover:shadow-xl transition-all flex items-center justify-center gap-3">
						<i data-lucide="linkedin" class="w-5 h-5"></i>
						<?php esc_html_e( 'LinkedIn', 'bigtricks' ); ?>
					</a>
					<?php endif; ?>
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
				class="flex items-center gap-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 hover:border-primary-400 text-slate-700 dark:text-slate-300 hover:text-primary-600 font-black px-8 py-4 rounded-2xl shadow-sm hover:shadow-md dark:shadow-slate-900/20 dark:hover:shadow-slate-900/40 transition-all active:scale-95 group"
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

</div><!-- /max-w-[1400px] container -->

<!-- ══════════════════════ ABOUT BIGTRICKS SECTION ══════════════════════ -->
<section class="bg-gradient-to-br from-slate-50 via-white to-blue-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 py-16 md:py-24 w-full" aria-label="<?php esc_attr_e( 'About Bigtricks', 'bigtricks' ); ?>">
	<div class="max-w-[1400px] mx-auto px-4">
		
		<!-- Section Header -->
		<div class="text-center mb-12 md:mb-16">
			<div class="inline-flex items-center justify-center gap-3 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 px-6 py-3 rounded-full mb-6 shadow-sm dark:shadow-slate-900/20">
				<i data-lucide="info" class="w-5 h-5"></i>
				<span class="font-black text-sm uppercase tracking-wider"><?php esc_html_e( 'About Us', 'bigtricks' ); ?></span>
			</div>
			<h2 class="text-3xl md:text-5xl font-black text-slate-900 dark:text-white mb-4">
			<?php esc_html_e( 'About Bigtricks – Free Recharge Tricks & Cashback Offers', 'bigtricks' ); ?>
			</h2>
			<p class="text-lg md:text-xl text-slate-600 dark:text-slate-300 max-w-3xl mx-auto font-medium">
				<?php esc_html_e( 'Your trusted source for verified deals, cashback offers, and money-saving tricks since 2015', 'bigtricks' ); ?>
			</p>
		</div>

		<!-- About Content Grid -->
		<div class="grid md:grid-cols-2 gap-6 md:gap-8 mb-12">
			
			<!-- Why Bigtricks.in -->
			<div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-lg border border-slate-200 dark:border-slate-700 hover:shadow-xl transition-shadow">
				<div class="flex items-start gap-4 mb-6">
					<div class="bg-primary-100 dark:bg-primary-900/30 p-3 rounded-2xl shrink-0">
						<i data-lucide="help-circle" class="w-7 h-7 text-primary-600 dark:text-primary-400"></i>
					</div>
					<h3 class="text-2xl font-black text-slate-900 dark:text-white leading-tight"><?php esc_html_e( 'Why Bigtricks.in', 'bigtricks' ); ?></h3>
				</div>
				<div class="text-slate-600 dark:text-slate-300 leading-relaxed space-y-4">
					<p>
						<?php
						printf(
							/* translators: 1: Opening link tag for Telegram, 2: Closing link tag, 3: Opening link tag for deals, 4: Closing link tag, 5: Opening link tag for loot deals, 6: Closing link tag */
							esc_html__( 'If you want to recharge your mobile or shop for something, you must check our website or get connected with us on %1$sTelegram channel%2$s or Email subscription. Every time there is any new Recharge %3$sloot offer%4$s or Shopping %5$sLoot offer%6$s we will notify you.', 'bigtricks' ),
							'<a href="' . esc_url( home_url( '/best-loot-deals-telegram-channel/' ) ) . '" class="text-primary-600 dark:text-primary-400 font-bold hover:underline">',
							'</a>',
							'<a href="' . esc_url( home_url( '/deals' ) ) . '" class="text-primary-600 dark:text-primary-400 font-bold hover:underline">',
							'</a>',
							'<a href="' . esc_url( home_url( '/loot-deals' ) ) . '" class="text-primary-600 dark:text-primary-400 font-bold hover:underline">',
							'</a>'
						);
						?>
					</p>
					<p class="font-semibold text-slate-900 dark:text-white">
						<?php
						printf(
							/* translators: %1$s: Opening link tag, %2$s: Closing link tag */
							esc_html__( 'You just have to be quick and grab the offer! Get connected with us now on %1$stelegram channel%2$s – Fastest way', 'bigtricks' ),
							'<a href="' . esc_url( home_url( '/best-loot-deals-telegram-channel/' ) ) . '" class="text-primary-600 dark:text-primary-400 hover:underline">',
							'</a>'
						);
						?>
					</p>
				</div>
			</div>

			<!-- About Bigtricks -->
			<div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-lg border border-slate-200 dark:border-slate-700 hover:shadow-xl transition-shadow">
				<div class="flex items-start gap-4 mb-6">
					<div class="bg-emerald-100 dark:bg-emerald-900/30 p-3 rounded-2xl shrink-0">
						<i data-lucide="chevron-right" class="w-7 h-7 text-emerald-600 dark:text-emerald-400"></i>
					</div>
					<h3 class="text-2xl font-black text-slate-900 dark:text-white leading-tight"><?php esc_html_e( 'About Bigtricks – Free Recharge Tricks & Cashback Offers', 'bigtricks' ); ?></h3>
				</div>
				<div class="text-slate-600 dark:text-slate-300 leading-relaxed">
					<p>
						<?php
						printf(
							/* translators: 1-12: Opening/closing link tags for various pages */
							esc_html__( 'Bigtricks.in is known for all types of %1$sFree Recharge Tricks%2$s, %3$sPayTM cash tricks%4$s, Online Shopping Discounts & Tricks, Latest %5$srefer and earn%6$s offers to earn money online. With Bigtricks you can get latest %7$sloot deals%8$s as well as instant notifications. Our users save up to Rs.10,000 per month by just using our simple tips & tricks. Our mission is to help online shoppers save their hard-earned money. People here even get free shopping with apps like %9$sAmazon%10$s, %11$sPhonePe%12$s & newly launched apps.', 'bigtricks' ),
							'<a href="' . esc_url( home_url( '/free-recharge-tricks' ) ) . '" class="text-primary-600 dark:text-primary-400 font-bold hover:underline">',
							'</a>',
							'<a href="' . esc_url( home_url( '/instant-free-paytm-cash-apps/' ) ) . '" class="text-primary-600 dark:text-primary-400 font-bold hover:underline">',
							'</a>',
							'<a href="' . esc_url( home_url( '/referral-codes' ) ) . '" class="text-primary-600 dark:text-primary-400 font-bold hover:underline">',
							'</a>',
							'<a href="' . esc_url( home_url( '/loot-deals' ) ) . '" class="text-primary-600 dark:text-primary-400 font-bold hover:underline">',
							'</a>',
							'<a href="' . esc_url( home_url( '/amazon-pay-upi-activation-offers/' ) ) . '" class="text-primary-600 dark:text-primary-400 font-bold hover:underline">',
							'</a>',
							'<a href="' . esc_url( home_url( '/store/phonepe' ) ) . '" class="text-primary-600 dark:text-primary-400 font-bold hover:underline">',
							'</a>'
						);
						?>
					</p>
				</div>
			</div>

			<!-- Only Verified Tricks -->
			<div class="bg-white dark:bg-slate-800 rounded-3xl p-8 shadow-lg border border-slate-200 dark:border-slate-700 hover:shadow-xl transition-shadow">
				<div class="flex items-start gap-4 mb-6">
					<div class="bg-orange-100 dark:bg-orange-900/30 p-3 rounded-2xl shrink-0">
						<i data-lucide="shield-check" class="w-7 h-7 text-orange-600 dark:text-orange-400"></i>
					</div>
					<h3 class="text-2xl font-black text-slate-900 dark:text-white leading-tight"><?php esc_html_e( 'Only Verified Tricks', 'bigtricks' ); ?></h3>
				</div>
				<div class="text-slate-600 dark:text-slate-300 leading-relaxed">
					<p><?php esc_html_e( 'We do not want our followers to waste time on apps that claim false offers. We first verify the post completely and then share it with all. If there is some loot going on currently and we don\'t have proof, we will simply add a tag #unverified so you can try at least and if it worked, it\'s yours.', 'bigtricks' ); ?></p>
				</div>
			</div>

			<!-- Telegram Channel -->
			<div class="bg-gradient-to-br from-blue-500 to-cyan-600 dark:from-blue-600 dark:to-cyan-700 rounded-3xl p-8 shadow-lg text-white hover:shadow-xl transition-shadow">
				<div class="flex items-start gap-4 mb-6">
					<div class="bg-white/20 backdrop-blur-sm p-3 rounded-2xl shrink-0">
						<i data-lucide="send" class="w-7 h-7 text-white"></i>
					</div>
					<h3 class="text-2xl font-black leading-tight"><?php esc_html_e( 'Best Loot Deals & Offers Telegram Channels', 'bigtricks' ); ?></h3>
				</div>
				<div class="text-blue-50 leading-relaxed mb-6">
					<p><?php esc_html_e( 'Our Telegram Channel provides you with 24×7×365 Loot Deals and Instant Offers Update. Join our Best Loot Deals Telegram Channel and never miss any Loot Deal, Free Recharge Trick, and Hidden Deals again.', 'bigtricks' ); ?></p>
				</div>
				<a href="https://t.me/bigtricksin" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-3 bg-white text-blue-600 font-black px-6 py-3 rounded-xl shadow-lg hover:scale-105 transition-transform">
					<i data-lucide="send" class="w-5 h-5"></i>
					<?php esc_html_e( 'Join Telegram Now', 'bigtricks' ); ?>
				</a>
			</div>

		</div><!-- /grid -->


	</div><!-- /max-w-[1400px] -->
</section>
<!-- ════════════════════ END ABOUT BIGTRICKS SECTION ════════════════════ -->

<div class=" mx-auto">
				<!-- ──────────────────── OUR IMPACT SECTION ──────────────────── -->
<section class="bg-slate-900 text-white py-16 mt-8 relative overflow-hidden w-full" aria-label="<?php esc_attr_e( 'Community Impact Stats', 'bigtricks' ); ?>">
	<div class="absolute inset-0 pointer-events-none overflow-hidden">
		<div class="absolute top-1/2 left-0 w-96 h-96 bg-primary-600/20 rounded-full blur-3xl -translate-y-1/2 -translate-x-1/2"></div>
		<div class="absolute top-1/2 right-0 w-96 h-96 bg-cyan-600/20 rounded-full blur-3xl -translate-y-1/2 translate-x-1/2"></div>
	</div>
	<div class="max-w-[1400px] mx-auto relative z-10">
		<div class="text-center mb-12">
			<h2 class="text-3xl md:text-4xl font-black mb-4"><?php esc_html_e( 'The Community Impact', 'bigtricks' ); ?></h2>
			<p class="text-slate-400 font-medium max-w-2xl mx-auto"><?php esc_html_e( 'Join millions of smart shoppers who trust Bigtricks to find the absolute lowest prices on the internet.', 'bigtricks' ); ?></p>
		</div>
		<div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-12 text-center">
			<div class="bg-white/5 backdrop-blur-sm p-6 rounded-3xl border border-white/10 hover:bg-white/10 transition-colors">
				<p class="text-2xl sm:text-3xl md:text-5xl font-black text-primary-400 mb-2">₹3,920+</p>
				<p class="font-bold text-sm sm:text-lg mb-1"><?php esc_html_e( 'Monthly Savings', 'bigtricks' ); ?></p>
				<p class="text-slate-400 text-xs sm:text-sm font-medium"><?php esc_html_e( 'Per User Average', 'bigtricks' ); ?></p>
			</div>
			<div class="bg-white/5 backdrop-blur-sm p-6 rounded-3xl border border-white/10 hover:bg-white/10 transition-colors">
				<p class="text-2xl sm:text-3xl md:text-5xl font-black text-cyan-400 mb-2">10+</p>
				<p class="font-bold text-sm sm:text-lg mb-1"><?php esc_html_e( 'Years Strong', 'bigtricks' ); ?></p>
				<p class="text-slate-400 text-xs sm:text-sm font-medium"><?php esc_html_e( 'Serving Since 2015', 'bigtricks' ); ?></p>
			</div>
			<div class="bg-white/5 backdrop-blur-sm p-6 rounded-3xl border border-white/10 hover:bg-white/10 transition-colors">
				<p class="text-2xl sm:text-3xl md:text-5xl font-black text-emerald-400 mb-2 flex items-center justify-center gap-2">
					<i data-lucide="shield-check" class="w-8 h-8 hidden sm:block"></i>100%
				</p>
				<p class="font-bold text-sm sm:text-lg mb-1"><?php esc_html_e( 'Verified Deals', 'bigtricks' ); ?></p>
				<p class="text-slate-400 text-xs sm:text-sm font-medium"><?php esc_html_e( 'All Offers Tested', 'bigtricks' ); ?></p>
			</div>
			<div class="bg-white/5 backdrop-blur-sm p-6 rounded-3xl border border-white/10 hover:bg-white/10 transition-colors">
				<p class="text-2xl sm:text-3xl md:text-5xl font-black text-orange-400 mb-2">24/7</p>
				<p class="font-bold text-sm sm:text-lg mb-1"><?php esc_html_e( 'Telegram Alerts', 'bigtricks' ); ?></p>
				<p class="text-slate-400 text-xs sm:text-sm font-medium"><?php esc_html_e( 'Instant Updates', 'bigtricks' ); ?></p>
			</div>
		</div>
	</div>
</section>
</div><!-- /max-w-[1400px] container -->

</main>

<?php get_footer(); ?>

<?php
/**
 * Category Archive Template
 * Supports: category description, custom [[tag-image]] meta-field icon, AJAX load more
 *
 * @package Bigtricks
 */

get_header();

$cat_obj     = get_queried_object();
$cat_id      = $cat_obj instanceof WP_Term ? (int) $cat_obj->term_id : 0;
$cat_name    = $cat_obj instanceof WP_Term ? $cat_obj->name : '';
$cat_desc    = $cat_obj instanceof WP_Term ? $cat_obj->description : '';

// Custom term meta: tag-image (stored as attachment URL or ID via term meta 'tag-image')
$tag_image   = $cat_id ? get_term_meta( $cat_id, 'tag-image', true ) : '';
$has_image   = ! empty( $tag_image );

$paged       = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$active_type = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : 'all'; // phpcs:ignore WordPress.Security.NonceVerification
$allowed_types = [ 'all', 'post', 'deal', 'referral-codes', 'credit-card' ];
if ( ! in_array( $active_type, $allowed_types, true ) ) {
	$active_type = 'all';
}

$query_post_types = $active_type === 'all'
	? [ 'post', 'deal', 'referral-codes', 'credit-card' ]
	: [ $active_type ];

$query_args = [
	'post_type'      => $query_post_types,
	'post_status'    => 'publish',
	'posts_per_page' => 12,
	'paged'          => $paged,
	'cat'            => $cat_id,
	'orderby'        => 'date',
	'order'          => 'DESC',
];

$feed_query  = new WP_Query( $query_args );
$total_posts = $feed_query->found_posts;
$max_pages   = $feed_query->max_num_pages;

$type_labels = [
	'all'            => __( 'All', 'bigtricks' ),
	'post'           => __( 'Offers', 'bigtricks' ),
	'deal'           => __( 'Deals', 'bigtricks' ),
	'referral-codes' => __( 'Referral Codes', 'bigtricks' ),
	'credit-card'    => __( 'Credit Cards', 'bigtricks' ),
];

// Pastel colours for initials fallback
$pastel_colors = [
	'bg-primary-100 text-primary-600',
	'bg-pink-100 text-pink-600',
	'bg-emerald-100 text-emerald-600',
	'bg-orange-100 text-orange-600',
	'bg-purple-100 text-purple-600',
	'bg-cyan-100 text-cyan-600',
];
$color_class = $cat_id ? $pastel_colors[ $cat_id % count( $pastel_colors ) ] : $pastel_colors[0];
?>

<main class="max-w-[1400px] mx-auto px-4 py-6 md:py-8 flex flex-col lg:flex-row gap-8 flex-1 w-full box-border" id="main-content">

	<div class="flex-1 min-w-0 w-full overflow-hidden">

		<!-- ═══ CATEGORY HERO ═══ -->
		<div class="bg-white dark:bg-slate-800 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm p-6 sm:p-8 mb-8 flex flex-col sm:flex-row items-start sm:items-center gap-6 overflow-hidden relative">
			<!-- Decorative bg -->
			<div class="absolute inset-0 bg-gradient-to-br from-primary-50/60 via-white to-purple-50/30 dark:from-slate-800 dark:via-slate-800 dark:to-slate-800 pointer-events-none"></div>

			<!-- Category Icon / Image -->
			<div class="relative z-10 shrink-0">
				<?php if ( $has_image ) :
					// tag-image can be attachment ID (int) or direct URL
					$icon_url = is_numeric( $tag_image ) ? wp_get_attachment_image_url( (int) $tag_image, 'thumbnail' ) : esc_url( $tag_image );
					if ( $icon_url ) : ?>
					<div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl border-2 border-white shadow-lg overflow-hidden bg-white flex items-center justify-center p-1">
						<img
							src="<?php echo esc_url( $icon_url ); ?>"
							alt="<?php echo esc_attr( $cat_name ); ?>"
							class="w-full h-full object-contain"
							loading="eager"
							data-no-lazy="1"
							decoding="async"
						>
					</div>
					<?php endif; ?>
				<?php else : ?>
				<div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl <?php echo esc_attr( $color_class ); ?> flex items-center justify-center text-3xl sm:text-4xl font-black shadow-md border-2 border-white shadow-inner-white">
					<?php echo esc_html( mb_strtoupper( mb_substr( $cat_name, 0, 2 ) ) ); ?>
				</div>
				<?php endif; ?>
			</div>

			<!-- Category Info -->
			<div class="relative z-10 flex-1 min-w-0">
				<div class="flex items-center gap-3 mb-2 flex-wrap">
					<span class="bg-primary-100 text-primary-700 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider border border-primary-200">
						<?php esc_html_e( 'Category', 'bigtricks' ); ?>
					</span>
					<span class="text-slate-400 text-sm font-bold">
						<?php printf( esc_html( _n( '%s post', '%s posts', $total_posts, 'bigtricks' ) ), esc_html( number_format_i18n( $total_posts ) ) ); ?>
					</span>
				</div>

				<h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 leading-tight mb-3 break-words">
					<?php echo esc_html( $cat_name ); ?>
				</h1>

				<?php if ( $cat_desc ) : ?>
				<div class="prose prose-slate max-w-none prose-img:rounded-2xl prose-img:shadow-md prose-a:text-primary-600 hover:prose-a:text-primary-800 prose-headings:font-black prose-p:leading-relaxed prose-p:text-slate-600 dark:prose-invert break-words">
					<?php echo wp_kses_post( wpautop( $cat_desc ) ); ?>
				</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- ═══ FILTER CHIPS + VIEW TOGGLE ═══ -->
		<div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
			<div class="flex items-center gap-2 overflow-x-auto pb-1" style="scrollbar-width:none;">
				<?php foreach ( $type_labels as $type_key => $type_label ) :
					$is_active  = $type_key === $active_type;
					$filter_url = add_query_arg( [
						'type' => $type_key === 'all' ? false : $type_key,
					], get_category_link( $cat_id ) );
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
			data-cat="<?php echo esc_attr( $cat_id ); ?>"
		>
			<?php if ( ! $feed_query->have_posts() ) : ?>
			<div class="bg-white p-12 rounded-3xl border border-slate-200 text-center">
				<div class="bg-slate-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
					<i data-lucide="search" class="w-8 h-8 text-slate-400"></i>
				</div>
				<h3 class="text-xl font-bold text-slate-900 mb-2"><?php esc_html_e( 'No posts found', 'bigtricks' ); ?></h3>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="mt-4 inline-block bg-primary-50 text-primary-700 font-bold px-6 py-2 rounded-full hover:bg-primary-100 transition-colors">
					<?php esc_html_e( 'View All', 'bigtricks' ); ?>
				</a>
			</div>
			<?php endif; ?>

			<?php
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
		<?php get_template_part( 'template-parts/' . $tpl_slug, null, [ 'post_id' => $post_id ] ); ?>
		<?php endwhile; wp_reset_postdata(); ?>
		</div><!-- /#bt-feed-container -->

		<!-- LOAD MORE BUTTON -->
		<?php if ( $max_pages > 1 ) : ?>
		<div class="mt-8 flex justify-center" id="bt-load-more-wrap">
			<button
				id="bt-load-more"
					class="flex items-center gap-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 hover:border-primary-400 text-slate-700 dark:text-slate-300 hover:text-primary-600 font-black px-8 py-4 rounded-2xl shadow-sm hover:shadow-md dark:shadow-slate-900/20 dark:hover:shadow-slate-900/40 transition-all active:scale-95"
				data-page="1"
				data-max-pages="<?php echo esc_attr( $max_pages ); ?>"
				data-cat="<?php echo esc_attr( $cat_id ); ?>"
				data-type="<?php echo esc_attr( $active_type ); ?>"
				data-nonce="<?php echo esc_attr( wp_create_nonce( 'bigtricks_load_more' ) ); ?>"
			>
				<i data-lucide="refresh-cw" class="w-5 h-5"></i>
				<span><?php esc_html_e( 'Load More', 'bigtricks' ); ?></span>
				<span class="text-slate-400 text-sm font-bold">(<?php echo esc_html( $total_posts - $feed_query->post_count ); ?> <?php esc_html_e( 'remaining', 'bigtricks' ); ?>)</span>
			</button>
		</div>
		<?php endif; ?>

	</div><!-- /Left Column -->

	<?php get_sidebar(); ?>

</main>

<?php get_footer(); ?>

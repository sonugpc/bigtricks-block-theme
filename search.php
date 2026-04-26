<?php
/**
 * Search Results
 *
 * @package Bigtricks
 */

get_header();
?>

<!-- Search Hero CTA -->
<div class="w-full bg-gradient-to-br from-primary-600 via-blue-600 to-cyan-500 py-12 md:py-16">
	<div class="max-w-[1400px] mx-auto px-4 text-center">
		<h1 class="text-3xl md:text-4xl font-black text-white mb-3">
			<?php
			if ( get_search_query() ) {
				printf(
					/* translators: %s: search term */
					esc_html__( 'Results for "%s"', 'bigtricks' ),
					esc_html( get_search_query() )
				);
			} else {
				esc_html_e( 'Search Bigtricks', 'bigtricks' );
			}
			?>
		</h1>
		<p class="text-primary-100 text-base md:text-lg font-medium mb-8"><?php esc_html_e( 'Find deals, credit cards, referral codes and more', 'bigtricks' ); ?></p>
		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="max-w-2xl mx-auto">
			<label for="search-hero-input" class="sr-only"><?php esc_html_e( 'Search', 'bigtricks' ); ?></label>
			<div class="relative flex items-center">
				<input
					id="search-hero-input"
					type="search"
					name="s"
					value="<?php echo esc_attr( get_search_query() ); ?>"
					placeholder="<?php esc_attr_e( 'Search deals, coupons, credit cards…', 'bigtricks' ); ?>"
					class="w-full pl-6 pr-32 py-4 md:py-5 text-base md:text-lg font-medium rounded-2xl border-0 shadow-2xl focus:outline-none focus:ring-4 focus:ring-white/40 text-slate-900 placeholder:text-slate-400"
					autocomplete="off"
				>
				<button type="submit" class="absolute right-2 bg-primary-600 hover:bg-primary-700 text-white font-black px-5 py-2.5 md:py-3 rounded-xl shadow-lg hover:shadow-xl transition-all active:scale-95 text-sm md:text-base">
					<?php esc_html_e( 'Search', 'bigtricks' ); ?>
				</button>
			</div>
		</form>
	</div>
</div>

<main class="max-w-[1400px] mx-auto px-4 py-6 md:py-8 flex flex-col lg:flex-row gap-8 flex-1 w-full" id="main-content">
	<div class="flex-1 min-w-0">

		<?php
		// Note: post_type is already set to all CPTs via pre_get_posts in functions.php §10b

		// Map CPT slug → template-part filename
		$template_map = [
			'post'           => 'card-post',
			'deal'           => 'card-deal',
			'referral-codes' => 'card-referral-code',
			'credit-card'    => 'card-credit-card',
		];
		?>

		<?php if ( have_posts() ) : ?>
		<p class="text-slate-500 font-medium mb-6">
			<?php
			/* translators: %d: number of results */
			printf( esc_html__( '%d results found', 'bigtricks' ), (int) $GLOBALS['wp_query']->found_posts );
			?>
		</p>
		<div class="space-y-6">
			<?php
			while ( have_posts() ) :
				the_post();
				$pid           = get_the_ID();
				$current_type  = get_post_type();
				$template_slug = $template_map[ $current_type ] ?? 'card-post';
				get_template_part( 'template-parts/' . $template_slug, null, [ 'post_id' => $pid ] );
			endwhile;
			?>
		</div>
		<?php the_posts_pagination(); ?>
		<?php else : ?>
		<div class="bg-white p-12 rounded-3xl border border-slate-200 text-center">
			<div class="bg-slate-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
				<i data-lucide="search" class="w-8 h-8 text-slate-400"></i>
			</div>
			<h3 class="text-xl font-bold text-slate-900 mb-2"><?php esc_html_e( 'No results found', 'bigtricks' ); ?></h3>
			<p class="text-slate-500 mb-6"><?php esc_html_e( 'Try a different search term or browse all deals.', 'bigtricks' ); ?></p>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="bg-primary-50 text-primary-700 font-bold px-6 py-2 rounded-full hover:bg-primary-100 transition-colors">
				<?php esc_html_e( 'Browse All Deals', 'bigtricks' ); ?>
			</a>
		</div>
		<?php endif; ?>
	</div>
	<?php get_sidebar(); ?>
</main>

<?php get_footer(); ?>

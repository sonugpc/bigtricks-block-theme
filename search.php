<?php
/**
 * Search Results
 *
 * @package Bigtricks
 */

get_header();
?>

<main class="max-w-[1400px] mx-auto px-4 py-6 md:py-8 flex flex-col lg:flex-row gap-8 flex-1 w-full" id="main-content">
	<div class="flex-1 min-w-0">
		<h1 class="text-2xl font-black text-slate-900 mb-6 flex items-center gap-3">
			<div class="bg-primary-100 p-2 rounded-xl text-primary-600">
				<i data-lucide="search" class="w-6 h-6"></i>
			</div>
			<?php
			printf(
				/* translators: %s: search term */
				esc_html__( 'Search: "%s"', 'bigtricks' ),
				'<span class="text-primary-600">' . esc_html( get_search_query() ) . '</span>'
			);
			?>
		</h1>

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

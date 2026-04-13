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

		<?php if ( have_posts() ) : ?>
		<p class="text-slate-500 font-medium mb-6">
			<?php
			/* translators: %d: number of results */
			printf( esc_html__( '%d results found', 'bigtricks' ), (int) $GLOBALS['wp_query']->found_posts );
			?>
		</p>
		<div class="space-y-6">
			<?php while ( have_posts() ) :
				the_post();
				$sid         = get_the_ID();
				$thumb_url   = bigtricks_get_thumbnail_url( $sid, 'medium_large' );
				$cat_obj     = get_the_category();
				$cat_name    = ! empty( $cat_obj ) ? $cat_obj[0]->name : '';

				$comments_n  = (int) get_comments_number();
				?>
			<article class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group flex flex-col h-full">
				<div class="flex flex-col sm:flex-row h-full">
					<a href="<?php the_permalink(); ?>" class="sm:w-[240px] shrink-0 bg-slate-50 p-5 flex items-center justify-center sm:border-r border-b sm:border-b-0 border-slate-100 relative" tabindex="-1" aria-hidden="true">
						<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" class="max-h-[160px] max-w-full object-contain mix-blend-multiply group-hover:scale-105 transition-transform duration-500" loading="lazy">
						<div class="absolute top-3 left-3"><?php echo wp_kses_post( bigtricks_deal_type_badge( $sid ) ); ?></div>
					</a>
					<div class="p-5 sm:p-6 lg:p-8 flex-1 flex flex-col justify-between min-w-0">
						<div>
							<?php if ( $cat_name ) : ?>
							<div class="mb-2 flex items-center gap-1.5 text-xs font-bold text-primary-600 uppercase tracking-wider">
								<i data-lucide="tag" class="w-3 h-3"></i> <?php echo esc_html( $cat_name ); ?>
							</div>
							<?php endif; ?>
							<h2 class="font-black text-slate-900 group-hover:text-primary-600 leading-snug mb-3 transition-colors text-lg sm:text-xl line-clamp-2 break-words">
								<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							</h2>
							<div class="text-slate-600 text-sm line-clamp-2 break-words mb-4"><?php the_excerpt(); ?></div>
						</div>
						<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-slate-100">
							<div class="flex items-center gap-4 text-sm font-bold text-slate-500">
								<a href="<?php echo esc_url( get_permalink() . '#comments' ); ?>" class="flex items-center gap-1.5 hover:text-primary-600">
									<i data-lucide="message-square" class="w-4 h-4"></i> <?php echo esc_html( $comments_n ); ?>
								</a>
								<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" class="hidden lg:flex items-center gap-1.5 text-xs text-slate-400">
									<i data-lucide="clock" class="w-4 h-4"></i> <?php echo esc_html( get_the_date( 'M j, Y' ) ); ?>
								</time>
							</div>
							<div><?php echo wp_kses_post( bigtricks_deal_cta_button( $sid ) ); ?></div>
						</div>
					</div>
				</div>
			</article>
			<?php endwhile; ?>
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

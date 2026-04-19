<?php
/**
 * Template Part: Latest Deals Widget
 * 
 * Displays a sidebar widget with latest deals including thumbnails, prices, and discount badges.
 * 
 * @package Bigtricks
 */

// Get current post ID if in the loop
$current_post_id = get_the_ID();
if ( ! $current_post_id ) {
	$current_post_id = isset( $GLOBALS['post'] ) ? $GLOBALS['post']->ID : 0;
}

// Query latest deals
$latest_deals = new WP_Query( array(
	'post_type'      => 'deal',
	'posts_per_page' => 5,
	'post__not_in'   => array( $current_post_id ),
	'orderby'        => 'date',
	'order'          => 'DESC',
) );
?>

<!-- Latest Deals Widget -->
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-soft border border-slate-200 dark:border-slate-800 overflow-hidden">
	<div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
		<h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
			<i data-lucide="zap" class="w-5 h-5 text-primary-600"></i>
			<?php esc_html_e( 'Latest Deals', 'bigtricks' ); ?>
		</h3>
	</div>
	<div class="divide-y divide-slate-100 dark:divide-slate-800">
		<?php
		if ( $latest_deals->have_posts() ) :
			while ( $latest_deals->have_posts() ) : $latest_deals->the_post();
				$deal_id = get_the_ID();
				
				// Thumbnail priority: offer_thumbnail_url > product_thumbnail_url > featured_image
				$thumb_url = get_post_meta( $deal_id, '_btdeals_offer_thumbnail_url', true );
				if ( ! $thumb_url ) {
					$thumb_url = get_post_meta( $deal_id, '_btdeals_product_thumbnail_url', true );
				}
				if ( ! $thumb_url && has_post_thumbnail() ) {
					$thumb_url = get_the_post_thumbnail_url( $deal_id, 'thumbnail' );
				}
				
				// Pricing
				$old_price = floatval( get_post_meta( $deal_id, '_btdeals_offer_old_price', true ) );
				$sale_price = floatval( get_post_meta( $deal_id, '_btdeals_offer_sale_price', true ) );
				$discount = intval( get_post_meta( $deal_id, '_btdeals_discount', true ) );
				$is_expired = (bool) get_post_meta( $deal_id, '_btdeals_is_expired', true );
				$discount_tag = get_post_meta( $deal_id, '_btdeals_discount_tag', true );
				
				// Calculate discount if not set
				if ( ! $discount && $old_price > 0 && $sale_price > 0 ) {
					$discount = intval( round( ( ( $old_price - $sale_price ) / $old_price ) * 100 ) );
				}

				$time_ago = bigtricks_time_ago( get_the_time( 'U' ) );
			?>
				<a href="<?php the_permalink(); ?>" class="flex gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors group<?php echo $is_expired ? ' opacity-60' : ''; ?>">
					<div class="relative w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center shrink-0 overflow-hidden">
						<?php if ( $thumb_url ) : ?>
							<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-contain p-1" loading="lazy">
						<?php endif; ?>
						<?php if ( $discount > 0 ) : ?>
							<div class="absolute top-0 right-0 bg-red-500 text-white text-[10px] font-black px-1.5 py-0.5 rounded-bl-md">
								<?php echo esc_html( $discount ); ?>%
							</div>
						<?php endif; ?>
					</div>
					<div class="flex-1 min-w-0">
						<h4 class="text-sm font-bold text-slate-900 dark:text-white line-clamp-2 mb-1 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
							<?php the_title(); ?>
						</h4>
						<div class="flex items-center justify-between gap-2 mb-1">
							<?php if ( $sale_price > 0 ) : ?>
								<div class="text-base font-black text-emerald-600 dark:text-emerald-500">
									₹<?php echo esc_html( number_format( $sale_price ) ); ?>
								</div>
							<?php endif; ?>
							<span class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
								<i data-lucide="clock" class="w-3 h-3"></i>
								<?php echo esc_html( $time_ago ); ?>
							</span>
						</div>
						<?php if ( $old_price > 0 && $old_price != $sale_price ) : ?>
							<div class="text-xs text-slate-400 dark:text-slate-500 line-through">
								₹<?php echo esc_html( number_format( $old_price ) ); ?>
							</div>
						<?php endif; ?>
					</div>
				</a>
			<?php endwhile;
			wp_reset_postdata();
		else : ?>
			<div class="p-4 text-center text-sm text-slate-500 dark:text-slate-400">
				<?php esc_html_e( 'No deals found', 'bigtricks' ); ?>
			</div>
		<?php endif; ?>
	</div>
	<div class="p-3 bg-slate-50 dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700">
		<a href="<?php echo esc_url( home_url( '/deals/' ) ); ?>" class="text-center text-sm font-bold text-primary-600 hover:text-primary-700 dark:text-primary-500 dark:hover:text-primary-400 transition-colors flex items-center justify-center gap-1">
			<?php esc_html_e( 'View More Deals', 'bigtricks' ); ?>
			<i data-lucide="arrow-right" class="w-4 h-4"></i>
		</a>
	</div>
</div>

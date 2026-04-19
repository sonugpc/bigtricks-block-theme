<?php
/**
 * Template Part: More Referral Codes Widget
 * 
 * Displays a sidebar widget with latest or related referral codes.
 * 
 * @package Bigtricks
 */

$current_post_id = get_the_ID();
if ( ! $current_post_id ) {
	$current_post_id = isset( $GLOBALS['post'] ) ? $GLOBALS['post']->ID : 0;
}

// Query latest referral codes
$latest_codes = new WP_Query( array(
	'post_type'      => 'referral-codes',
	'posts_per_page' => 5,
	'post__not_in'   => array( $current_post_id ),
	'orderby'        => 'date',
	'order'          => 'DESC',
) );
?>

<!-- More Referral Codes Widget -->
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-soft border border-slate-200 dark:border-slate-800 overflow-hidden">
	<div class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
		<h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
			<i data-lucide="share-2" class="w-5 h-5 text-primary-600"></i>
			<?php esc_html_e( 'More Referral Codes', 'bigtricks' ); ?>
		</h3>
	</div>
	
	<div class="divide-y divide-slate-100 dark:divide-slate-800">
		<?php
		if ( $latest_codes->have_posts() ) :
			while ( $latest_codes->have_posts() ) : $latest_codes->the_post();
				$code_id = get_the_ID();
				
				// Get referral data using plugin function
				if ( function_exists( 'rcp_get_referral_data' ) ) {
					$referral_data = rcp_get_referral_data( $code_id );
					$referral_code = sanitize_text_field( (string) ( $referral_data['referral_code'] ?? '' ) );
					$signup_bonus  = sanitize_text_field( (string) ( $referral_data['signup_bonus'] ?? '' ) );
					$app_name      = sanitize_text_field( (string) ( $referral_data['app_name'] ?? '' ) );
				} else {
					$referral_code = sanitize_text_field( (string) get_post_meta( $code_id, 'referral_code', true ) );
					$signup_bonus  = sanitize_text_field( (string) get_post_meta( $code_id, 'signup_bonus', true ) );
					$app_name      = sanitize_text_field( (string) get_post_meta( $code_id, 'app_name', true ) );
				}

				if ( ! $app_name ) {
					$app_name = get_the_title();
				}

				$app_logo = get_the_post_thumbnail_url( $code_id, 'thumbnail' );
			?>
				<a href="<?php the_permalink(); ?>" class="flex gap-3 p-3 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors group">
					<div class="relative w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center shrink-0 overflow-hidden">
						<?php if ( $app_logo ) : ?>
							<img src="<?php echo esc_url( $app_logo ); ?>" alt="<?php echo esc_attr( $app_name ); ?>" class="w-full h-full object-contain p-1" loading="lazy">
						<?php else : ?>
							<i data-lucide="smartphone" class="w-6 h-6 text-slate-300"></i>
						<?php endif; ?>
					</div>
					
					<div class="flex-1 min-w-0">
						<h4 class="text-sm font-bold text-slate-900 dark:text-white line-clamp-2 mb-1 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
							<?php echo esc_html( $app_name ); ?>
						</h4>
						
						<?php if ( $signup_bonus ) : ?>
						<div class="text-[13px] font-semibold text-emerald-600 dark:text-emerald-500 flex items-center gap-1 mb-1 truncate">
							<i data-lucide="gift" class="w-3 h-3"></i>
							<?php echo esc_html( $signup_bonus ); ?>
						</div>
						<?php endif; ?>
						
						<?php if ( $referral_code ) : ?>
						<div class="text-xs text-slate-500 dark:text-slate-400 font-mono inline-block px-1.5 py-0.5 border border-slate-200 dark:border-slate-700 rounded bg-white dark:bg-slate-800/50 max-w-full truncate">
							<?php echo esc_html( $referral_code ); ?>
						</div>
						<?php endif; ?>
					</div>
				</a>
			<?php endwhile;
			wp_reset_postdata();
		else : ?>
			<div class="p-4 text-center text-sm text-slate-500 dark:text-slate-400">
				<?php esc_html_e( 'No other codes found', 'bigtricks' ); ?>
			</div>
		<?php endif; ?>
	</div>
	
	<div class="p-3 bg-slate-50 dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700 font-bold">
		<a href="<?php echo esc_url( get_post_type_archive_link( 'referral-codes' ) ); ?>" class="text-center text-sm font-bold text-primary-600 hover:text-primary-700 dark:text-primary-500 dark:hover:text-primary-400 transition-colors flex items-center justify-center gap-1">
			<?php esc_html_e( 'View All Codes', 'bigtricks' ); ?>
			<i data-lucide="arrow-right" class="w-4 h-4"></i>
		</a>
	</div>
</div>

<?php
/**
 * Template Part: Top Stores Widget
 * 
 * Displays a sidebar widget with top stores based on deal count.
 * Shows store logos and deal counts in a grid layout.
 * 
 * @package Bigtricks
 */

// Query top stores
$top_stores = get_terms( array(
	'taxonomy'   => 'store',
	'number'     => 6,
	'orderby'    => 'count',
	'order'      => 'DESC',
	'hide_empty' => true,
) );
?>

<!-- Top Stores Widget -->
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow-soft border border-slate-200 dark:border-slate-800 overflow-hidden">
	<div class="p-4 border-b border-slate-200 dark:border-slate-800">
		<h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
			<i data-lucide="store" class="w-5 h-5 text-primary-600"></i>
			<?php esc_html_e( 'Top Stores', 'bigtricks' ); ?>
		</h3>
	</div>
	<div class="p-4">
		<?php if ( ! empty( $top_stores ) && ! is_wp_error( $top_stores ) ) : ?>
			<div class="grid grid-cols-2 gap-3">
				<?php foreach ( $top_stores as $store ) :
					$store_logo_url = get_term_meta( $store->term_id, 'thumb_image', true );
					
					// Handle attachment ID
					if ( $store_logo_url && is_numeric( $store_logo_url ) ) {
						$store_logo_url = wp_get_attachment_image_url( (int) $store_logo_url, 'thumbnail' );
					}
				?>
					<a href="<?php echo esc_url( get_term_link( $store ) ); ?>" class="flex flex-col items-center gap-2 p-3 bg-slate-50 dark:bg-slate-800 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors group">
						<?php if ( $store_logo_url ) : ?>
							<img src="<?php echo esc_url( $store_logo_url ); ?>" alt="<?php echo esc_attr( $store->name ); ?>" class="w-12 h-12 object-contain" loading="lazy">
						<?php else : ?>
							<div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
								<i data-lucide="store" class="w-6 h-6 text-primary-600 dark:text-primary-400"></i>
							</div>
						<?php endif; ?>
						<span class="text-xs font-bold text-slate-700 dark:text-slate-300 text-center group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
							<?php echo esc_html( $store->name ); ?>
						</span>
						<span class="text-[10px] text-slate-500 dark:text-slate-400">
							<?php
							printf(
								/* translators: %s: number of deals */
								esc_html( _n( '%s deal', '%s deals', $store->count, 'bigtricks' ) ),
								esc_html( number_format_i18n( $store->count ) )
							);
							?>
						</span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="p-4 text-center text-sm text-slate-500 dark:text-slate-400">
				<?php esc_html_e( 'No stores found', 'bigtricks' ); ?>
			</div>
		<?php endif; ?>
	</div>
</div>

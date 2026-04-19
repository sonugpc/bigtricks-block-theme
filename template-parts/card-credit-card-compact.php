<?php
/**
 * Compact Credit Card Component
 * Smaller, minimal design for related cards sections
 *
 * @package Bigtricks
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Get post ID from args or global
$post_id = $args['post_id'] ?? get_the_ID();

// Meta
$annual_fee    = floatval( get_post_meta( $post_id, 'annual_fee', true ) );
$cashback_rate = sanitize_text_field( get_post_meta( $post_id, 'cashback_rate', true ) ) ?: 'N/A';
$welcome_bonus = sanitize_text_field( get_post_meta( $post_id, 'welcome_bonus', true ) ) ?: 'N/A';
$rating        = (float) get_post_meta( $post_id, 'rating', true );
$is_featured   = (bool) get_post_meta( $post_id, 'featured', true );

// Thumbnail
$thumb_url = has_post_thumbnail( $post_id ) ? get_the_post_thumbnail_url( $post_id, 'medium' ) : '';

// Helper for currency
if ( ! function_exists( 'ccm_format_currency' ) ) {
	function ccm_format_currency( $amount ) {
		if ( empty( $amount ) || $amount === 'N/A' ) return 'N/A';
		$numeric = floatval( $amount );
		return $numeric == 0 ? 'Free' : '₹' . number_format( $numeric );
	}
}

// Taxonomies
$bank_terms = get_the_terms( $post_id, 'store' );
$bank_name  = ! empty( $bank_terms ) && ! is_wp_error( $bank_terms ) ? $bank_terms[0]->name : '';

$permalink = get_permalink( $post_id );
?>
<article class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group">
	<!-- Card Image -->
	<a href="<?php echo esc_url( $permalink ); ?>" class="block bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-900 p-6 border-b border-slate-200 dark:border-slate-700">
		<div class="aspect-[1.6/1] flex items-center justify-center relative">
			<?php if ( $thumb_url ) : ?>
				<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( get_the_title( $post_id ) ); ?>" class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105" loading="lazy" decoding="async">
			<?php else : ?>
				<div class="w-full h-full bg-gradient-to-br from-primary-400 to-purple-500 rounded-xl flex items-center justify-center">
					<span class="text-white font-black text-sm opacity-70"><?php echo esc_html( get_the_title( $post_id ) ); ?></span>
				</div>
			<?php endif; ?>
			
			<?php if ( $is_featured ) : ?>
				<span class="absolute top-2 right-2 bg-amber-400 text-white font-black text-xs px-2 py-1 rounded-full shadow-sm">
					<i data-lucide="trophy" class="w-3 h-3 inline"></i>
				</span>
			<?php endif; ?>
		</div>
	</a>

	<!-- Card Info -->
	<div class="p-4">
		<!-- Title -->
		<a href="<?php echo esc_url( $permalink ); ?>" class="block mb-3">
			<h3 class="font-black text-slate-900 dark:text-white text-sm line-clamp-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors leading-snug">
				<?php echo esc_html( get_the_title( $post_id ) ); ?>
			</h3>
			<?php if ( $bank_name ) : ?>
				<p class="text-xs text-slate-500 dark:text-slate-400 mt-1"><?php echo esc_html( $bank_name ); ?></p>
			<?php endif; ?>
		</a>

		<!-- Rating -->
		<?php if ( $rating > 0 ) : ?>
		<div class="flex items-center gap-1 mb-3">
			<?php
			$full_stars = floor( $rating );
			$has_half = ( $rating - $full_stars ) >= 0.5;
			for ( $i = 0; $i < $full_stars; $i++ ) : ?>
				<i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-current"></i>
			<?php endfor;
			if ( $has_half ) : ?>
				<i data-lucide="star-half" class="w-3.5 h-3.5 text-amber-400 fill-current"></i>
			<?php endif;
			for ( $i = ceil( $rating ); $i < 5; $i++ ) : ?>
				<i data-lucide="star" class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600"></i>
			<?php endfor; ?>
			<span class="text-xs font-black text-slate-700 dark:text-slate-300 ml-1"><?php echo esc_html( number_format( $rating, 1 ) ); ?></span>
		</div>
		<?php endif; ?>

		<!-- Key Stats -->
		<div class="space-y-2 mb-4">
			<div class="flex items-center justify-between text-xs">
				<span class="text-slate-600 dark:text-slate-400">Annual Fee</span>
				<span class="font-black <?php echo $annual_fee == 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-900 dark:text-white'; ?>">
					<?php echo esc_html( ccm_format_currency( $annual_fee ) ); ?>
				</span>
			</div>
			<div class="flex items-center justify-between text-xs">
				<span class="text-slate-600 dark:text-slate-400">Cashback</span>
				<span class="font-black text-emerald-600 dark:text-emerald-400"><?php echo esc_html( $cashback_rate ); ?></span>
			</div>
		</div>

		<!-- CTA -->
		<a href="<?php echo esc_url( $permalink ); ?>" class="block w-full text-center bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-xs font-black transition-colors">
			View Details
		</a>
	</div>
</article>

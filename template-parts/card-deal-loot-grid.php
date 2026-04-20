<?php
/**
 * Card Component: Loot Deal Grid Variant
 *
 * Used by the Deals Archive Template for responsive grid cards.
 *
 * @package Bigtricks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id        = get_the_ID();
$permalink      = esc_url( get_permalink( $post_id ) );
$post_time_ago  = esc_html( bigtricks_time_ago( get_the_time( 'U', $post_id ) ) );

$offer_url      = esc_url( (string) get_post_meta( $post_id, '_btdeals_offer_url', true ) );
$sale_price     = floatval( get_post_meta( $post_id, '_btdeals_offer_sale_price', true ) );
$old_price      = floatval( get_post_meta( $post_id, '_btdeals_offer_old_price', true ) );
$coupon_code    = sanitize_text_field( (string) get_post_meta( $post_id, '_btdeals_coupon_code', true ) );
$is_expired     = (bool) get_post_meta( $post_id, '_btdeals_is_expired', true );
$discount_meta  = intval( get_post_meta( $post_id, '_btdeals_discount', true ) );

$offer_thumb    = esc_url( (string) get_post_meta( $post_id, '_btdeals_offer_thumbnail_url', true ) );
$product_thumb  = esc_url( (string) get_post_meta( $post_id, '_btdeals_product_thumbnail_url', true ) );

// Priority: offer_thumbnail_url > product_thumbnail_url > featured image.
$product_image_url = '';
if ( $offer_thumb ) {
	$product_image_url = $offer_thumb;
} elseif ( $product_thumb ) {
	$product_image_url = $product_thumb;
} elseif ( has_post_thumbnail( $post_id ) ) {
	$product_image_url = (string) get_the_post_thumbnail_url( $post_id, 'large' );
}

if ( '' === $product_image_url ) {
	$product_image_url = BIGTRICKS_URI . '/assets/images/placeholder.svg';
}

$store_terms = get_the_terms( $post_id, 'store' );
$store_name  = ( ! empty( $store_terms ) && ! is_wp_error( $store_terms ) ) ? (string) $store_terms[0]->name : '';
$store_url   = '';
if ( ! empty( $store_terms ) && ! is_wp_error( $store_terms ) ) {
	$maybe_store_url = get_term_link( $store_terms[0] );
	if ( ! is_wp_error( $maybe_store_url ) ) {
		$store_url = esc_url( (string) $maybe_store_url );
	}
}

$category_terms = get_the_terms( $post_id, 'category' );
$category_name  = ( ! empty( $category_terms ) && ! is_wp_error( $category_terms ) ) ? (string) $category_terms[0]->name : '';
$category_url   = '';
if ( ! empty( $category_terms ) && ! is_wp_error( $category_terms ) ) {
	$maybe_category_url = get_term_link( $category_terms[0] );
	if ( ! is_wp_error( $maybe_category_url ) ) {
		$category_url = esc_url( (string) $maybe_category_url );
	}
}

$discount_pct = 0;
if ( $discount_meta > 0 ) {
	$discount_pct = $discount_meta;
} elseif ( $old_price > 0 && $sale_price > 0 && $old_price > $sale_price ) {
	$discount_pct = (int) round( ( ( $old_price - $sale_price ) / $old_price ) * 100 );
}

$discount_badge_class = $discount_pct >= 50
	? 'bg-red-500 text-white'
	: 'bg-emerald-600 text-white';

$variant = $post_id % 2;
$media_wrapper_class = 0 === $variant
	? 'bg-gradient-to-br from-slate-50 via-slate-100 to-slate-50'
	: 'bg-white';
$media_image_class = 0 === $variant
	? 'h-44 w-full object-contain p-4'
	: 'h-44 w-full object-contain p-2';

$destination = $offer_url ?: $permalink;
?>

<article class="group relative overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
	<div class="relative <?php echo esc_attr( $media_wrapper_class ); ?>">
		<?php if ( $coupon_code ) : ?>
			<span class="absolute left-3 top-3 z-20 rounded-r-xl rounded-l-lg bg-gradient-to-r from-blue-600 to-pink-400 px-2.5 py-1 text-xs font-black uppercase tracking-wide text-white">
				<?php echo esc_html( $coupon_code ); ?>
			</span>
		<?php endif; ?>

		<?php if ( $is_expired ) : ?>
			<div class="pointer-events-none absolute inset-0 z-10 flex items-center justify-center bg-slate-950/30">
				<span class="rounded-lg bg-red-500/90 px-3 py-1 text-sm font-semibold uppercase tracking-wide text-white shadow-lg sm:text-base">
					<?php esc_html_e( 'Expired', 'bigtricks' ); ?>
				</span>
			</div>
		<?php endif; ?>

		<a href="<?php echo esc_url( $destination ); ?>" <?php echo $offer_url ? 'target="_blank" rel="noopener noreferrer nofollow"' : ''; ?> class="block" aria-label="<?php the_title_attribute(); ?>">
			<img
				src="<?php echo esc_url( $product_image_url ); ?>"
				alt="<?php the_title_attribute(); ?>"
				class="<?php echo esc_attr( str_replace( 'h-44', 'h-32', $media_image_class ) ); ?>"
				loading="lazy"
				decoding="async"
				onerror="this.onerror=null;this.src='<?php echo esc_url( BIGTRICKS_URI . '/assets/images/placeholder.svg' ); ?>';"
			>
		</a>
	</div>

	<div class="p-3.5 sm:p-4">
		<h3 class="line-clamp-3 text-sm sm:text-base font-semibold leading-snug text-slate-800 transition-colors group-hover:text-primary-700">
			<a href="<?php echo esc_url( $permalink ); ?>" class="focus:outline-none focus:underline">
				<?php the_title(); ?>
			</a>
		</h3>

		<?php if ( $category_name || $store_name ) : ?>
			<div class="mt-1 flex items-center justify-between gap-2 text-xs sm:text-sm">
				<?php if ( $category_name ) : ?>
					<?php if ( $category_url ) : ?>
						<a href="<?php echo esc_url( $category_url ); ?>" class="truncate font-semibold uppercase tracking-wide text-primary-700 hover:underline" aria-label="<?php echo esc_attr( $category_name ); ?>">
							<?php echo esc_html( $category_name ); ?>
						</a>
					<?php else : ?>
						<span class="truncate font-semibold uppercase tracking-wide text-primary-700"><?php echo esc_html( $category_name ); ?></span>
					<?php endif; ?>
				<?php else : ?>
					<span></span>
				<?php endif; ?>

				<?php if ( $store_name ) : ?>
					<?php if ( $store_url ) : ?>
						<a href="<?php echo esc_url( $store_url ); ?>" class="truncate text-right font-medium text-slate-500 hover:underline" aria-label="<?php echo esc_attr( $store_name ); ?>">
							<?php echo esc_html( $store_name ); ?>
						</a>
					<?php else : ?>
						<span class="truncate text-right font-medium text-slate-500"><?php echo esc_html( $store_name ); ?></span>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="mt-3 flex items-end justify-between gap-2">
			<div class="flex flex-wrap items-center gap-2">
				<?php if ( $old_price > 0 ) : ?>
					<span class="text-sm font-medium text-slate-400 line-through">₹<?php echo esc_html( number_format_i18n( $old_price, 0 ) ); ?></span>
				<?php endif; ?>

				<?php if ( $sale_price > 0 ) : ?>
					<span class="text-lg sm:text-xl font-bold text-slate-800">₹<?php echo esc_html( number_format_i18n( $sale_price, 0 ) ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( $discount_pct > 0 ) : ?>
				<span class="inline-flex shrink-0 rounded-lg px-2.5 py-1 text-xs sm:text-sm font-semibold uppercase tracking-wide <?php echo esc_attr( $discount_badge_class ); ?>">
					<?php echo esc_html( $discount_pct ); ?>% OFF
				</span>
			<?php endif; ?>
		</div>

		<div class="mt-3 border-t border-slate-100 pt-2.5">
			<span class="text-xs sm:text-sm font-semibold text-slate-400"><?php echo esc_html( $post_time_ago ); ?></span>
		</div>
	</div>
</article>

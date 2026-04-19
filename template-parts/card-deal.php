<?php
/**
 * Card Component: Deal (CPT: deal — bigtricks-deals plugin)
 *
 * Key meta (from bigtricks-deals plugin, prefix _btdeals_):
 *   _btdeals_offer_url         — affiliate link
 *   _btdeals_offer_sale_price  — sale price
 *   _btdeals_offer_old_price   — original price
 *   _btdeals_coupon_code       — coupon code
 *   _btdeals_brand_logo_url    — brand logo (fallback to featured image)
 *   _btdeals_short_description — short description override
 *   _btdeals_button_text       — custom CTA text
 *   _btdeals_is_expired        — expired flag
 *
 * Taxonomy: store (for store/brand filtering)
 *
 * @package Bigtricks
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_id    = get_the_ID();
$permalink  = esc_url( get_permalink() );
$comments   = (int) get_comments_number();

// Meta
$offer_url   = esc_url( (string) get_post_meta( $post_id, '_btdeals_offer_url', true ) );
$sale_price  = sanitize_text_field( (string) get_post_meta( $post_id, '_btdeals_offer_sale_price', true ) );
$old_price   = sanitize_text_field( (string) get_post_meta( $post_id, '_btdeals_offer_old_price', true ) );
$coupon      = sanitize_text_field( (string) get_post_meta( $post_id, '_btdeals_coupon_code', true ) );
$brand_logo  = esc_url( (string) get_post_meta( $post_id, '_btdeals_brand_logo_url', true ) );
$short_desc  = wp_kses_post( (string) get_post_meta( $post_id, '_btdeals_short_description', true ) );
$btn_text    = sanitize_text_field( (string) get_post_meta( $post_id, '_btdeals_button_text', true ) ) ?: 'Get Deal';
$is_expired  = (bool) get_post_meta( $post_id, '_btdeals_is_expired', true );

// Thumbnail: prefer featured image, fallback to brand logo, then placeholder
$thumb_url = bigtricks_get_thumbnail_url( $post_id, 'medium_large' );
if ( ! has_post_thumbnail( $post_id ) && $brand_logo ) {
	$thumb_url = $brand_logo;
}

// Store taxonomy
$store_terms = get_the_terms( $post_id, 'store' );
$store_name  = ! empty( $store_terms ) && ! is_wp_error( $store_terms ) ? $store_terms[0]->name : '';
$store_link  = ! empty( $store_terms ) && ! is_wp_error( $store_terms ) ? esc_url( get_term_link( $store_terms[0] ) ) : '';

// Category
$cat_obj  = get_the_category();
$cat_name = ! empty( $cat_obj ) ? esc_html( $cat_obj[0]->name ) : '';
$cat_link = ! empty( $cat_obj ) ? esc_url( get_category_link( $cat_obj[0]->term_id ) ) : '';

// Savings calculation
$savings     = '';
$savings_pct = '';
if ( $sale_price && $old_price && is_numeric( str_replace( ',', '', $old_price ) ) && is_numeric( str_replace( ',', '', $sale_price ) ) ) {
	$old_num = (float) str_replace( ',', '', $old_price );
	$new_num = (float) str_replace( ',', '', $sale_price );
	if ( $old_num > $new_num && $old_num > 0 ) {
		$savings_pct = round( ( ( $old_num - $new_num ) / $old_num ) * 100 );
	}
}

$dest_url = $offer_url ?: $permalink;
?>
<article
	class="bt-deal-card bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group flex flex-col h-full w-full<?php echo $is_expired ? ' opacity-70' : ''; ?>"
	data-post-id="<?php echo esc_attr( $post_id ); ?>"
	data-post-type="deal"
>
	<div class="flex flex-col sm:flex-row h-full bt-card-inner">

		<!-- Thumbnail -->
		<a
			href="<?php echo $is_expired ? $permalink : $dest_url; ?>"
			<?php echo ! $is_expired && $offer_url ? 'target="_blank" rel="noopener noreferrer nofollow"' : ''; ?>
			class="bt-card-thumb sm:w-[220px] shrink-0 bg-slate-50 flex items-center justify-center sm:border-r sm:border-b-0 border-b border-slate-100 relative overflow-hidden"
			tabindex="-1"
			aria-hidden="true"
		>
			<img
				src="<?php echo esc_url( $thumb_url ); ?>"
				alt="<?php the_title_attribute(); ?>"
				class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
				loading="lazy"
				decoding="async"
				width="220"
				height="180"
			>
			<!-- Badges row -->
			<div class="absolute top-3 left-3 flex flex-col gap-1.5">
				<span class="bg-red-50 text-red-600 font-black text-xs px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5 border border-red-100">
					<i data-lucide="flame" class="w-3.5 h-3.5 fill-current shrink-0"></i>
					<?php echo $is_expired ? 'Expired' : 'Deal'; ?>
				</span>
				<?php if ( $savings_pct ) : ?>
				<span class="bg-emerald-500 text-white font-black text-xs px-2.5 py-1 rounded-full shadow-sm">
					<?php echo esc_html( $savings_pct ); ?>% OFF
				</span>
				<?php endif; ?>
			</div>
		</a>

		<!-- Content -->
		<div class="p-5 sm:p-6 flex-1 flex flex-col justify-between bg-white relative min-w-0">
			<div>
				<!-- Store / Category breadcrumb -->
				<div class="mb-2 flex items-center gap-2 flex-wrap">
					<?php if ( $store_name ) : ?>
					<div class="flex items-center gap-1.5 text-xs font-bold text-slate-500 uppercase tracking-wider">
						<i data-lucide="shopping-bag" class="w-3 h-3"></i>
					<?php if ( $store_link ) : ?>
					<a href="<?php echo $store_link; ?>" class="hover:text-primary-600 transition-colors hover:underline"><?php echo esc_html( $store_name ); ?></a>
					<?php else : ?>
					<span><?php echo esc_html( $store_name ); ?></span>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<?php if ( $cat_name ) : ?>
				<div class="flex items-center gap-1 text-xs font-bold text-primary-600 uppercase tracking-wider">
					<i data-lucide="tag" class="w-3 h-3"></i>
					<?php if ( $cat_link ) : ?>
					<a href="<?php echo $cat_link; ?>" class="hover:underline"><?php echo esc_html( $cat_name ); ?></a>
					<?php else : ?>
					<?php echo esc_html( $cat_name ); ?>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>

				<h2 class="font-black text-slate-900 group-hover:text-primary-600 leading-snug mb-2 transition-colors break-words text-lg sm:text-xl line-clamp-2">
					<a href="<?php echo $permalink; ?>" class="focus:outline-none focus:underline">
						<span class="absolute inset-0 z-0" aria-hidden="true"></span>
						<?php the_title(); ?>
					</a>
				</h2>

				<!-- Price row -->
				<?php if ( $sale_price || $old_price ) : ?>
				<div class="flex items-baseline gap-3 mb-3">
					<?php if ( $sale_price ) : ?>
					<span class="text-2xl font-black text-primary-600">₹<?php echo esc_html( $sale_price ); ?></span>
					<?php endif; ?>
					<?php if ( $old_price && $sale_price ) : ?>
					<span class="text-base text-slate-400 line-through font-medium">₹<?php echo esc_html( $old_price ); ?></span>
					<?php endif; ?>
				</div>
				<?php elseif ( $short_desc ) : ?>
				<div class="text-slate-600 text-sm line-clamp-2 break-words mb-3"><?php echo wp_kses_post( $short_desc ); ?></div>
				<?php else : ?>
				<div class="text-slate-600 text-sm line-clamp-2 break-words mb-3"><?php the_excerpt(); ?></div>
				<?php endif; ?>
			</div>

			<div class="flex flex-col sm:flex-row flex-wrap sm:items-center justify-between gap-3 mt-auto pt-4 border-t border-slate-100 relative z-10">
				<div class="flex items-center gap-3 text-sm font-bold text-slate-500 flex-wrap">
					<!-- Coupon code display -->
				<?php if ( $coupon ) : ?>
					<button
						class="bt-copy-code flex items-center gap-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-dashed border-emerald-300 px-3 py-1.5 rounded-lg text-xs font-black transition-colors"
						data-code="<?php echo esc_attr( $coupon ); ?>"
						aria-label="<?php esc_attr_e( 'Copy coupon code', 'bigtricks' ); ?>"
					>
						<i data-lucide="copy" class="w-3.5 h-3.5 shrink-0"></i>
						<?php echo esc_html( $coupon ); ?>
					</button>
					<?php endif; ?>
					<?php if ( $comments > 0 ) : ?>
					<a href="<?php echo esc_url( $permalink . '#comments' ); ?>" class="flex items-center gap-1.5 hover:text-primary-600 transition-colors">
						<i data-lucide="message-square" class="w-3.5 h-3.5"></i>
						<span><?php echo esc_html( $comments ); ?></span>
					</a>
					<?php endif; ?>
				</div>
				<div class="w-full sm:w-auto relative z-10">
					<a
						href="<?php echo $dest_url; ?>"
						target="<?php echo $offer_url ? '_blank' : '_self'; ?>"
						rel="<?php echo $offer_url ? 'noopener noreferrer nofollow' : ''; ?>"
						class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-black bg-primary-600 hover:bg-primary-700 text-white shadow-md shadow-primary-200 dark:shadow-none transition-all active:scale-95"
					>
						<?php echo esc_html( $btn_text ); ?> <i data-lucide="external-link" class="w-4 h-4 shrink-0"></i>
					</a>
				</div>
			</div>
		</div>
	</div>
</article>

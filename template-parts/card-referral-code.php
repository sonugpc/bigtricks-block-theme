<?php
/**
 * Card Component: Referral Code (CPT: referral-codes — referral-code-plugin)
 *
 * Key meta:
 *   referral_code               — the code to copy
 *   referral_link               — direct referral link
 *   signup_bonus                — bonus text
 *   app_name                    — app/brand name override
 *   referral_rewards            — rewards description
 *   short_description           — short desc
 *
 * Featured image = app logo
 * Taxonomies: category, store
 *
 * @package Bigtricks
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_id      = get_the_ID();
$permalink    = esc_url( get_permalink() );
$comments     = (int) get_comments_number();

// Meta
$referral_code    = sanitize_text_field( (string) get_post_meta( $post_id, 'referral_code', true ) );
$referral_link    = esc_url( (string) get_post_meta( $post_id, 'referral_link', true ) );
$signup_bonus     = sanitize_text_field( (string) get_post_meta( $post_id, 'signup_bonus', true ) );
$app_name         = sanitize_text_field( (string) get_post_meta( $post_id, 'app_name', true ) );
$referral_rewards = wp_kses_post( (string) get_post_meta( $post_id, 'referral_rewards', true ) );
$short_desc       = wp_kses_post( (string) get_post_meta( $post_id, 'short_description', true ) );

// Thumbnail: featured image = app logo
$thumb_url = bigtricks_get_thumbnail_url( $post_id, 'medium' );

// Display name
$display_name = $app_name ?: get_the_title();

// Destination link
$dest_url = $referral_link ?: $permalink;

// Category
$cat_obj  = get_the_category();
$cat_name = ! empty( $cat_obj ) ? esc_html( $cat_obj[0]->name ) : '';
$cat_link = ! empty( $cat_obj ) ? esc_url( get_category_link( $cat_obj[0]->term_id ) ) : '';

// Store taxonomy
$store_terms = get_the_terms( $post_id, 'store' );
$store_name  = ! empty( $store_terms ) && ! is_wp_error( $store_terms ) ? $store_terms[0]->name : '';
$store_link  = ! empty( $store_terms ) && ! is_wp_error( $store_terms ) ? esc_url( get_term_link( $store_terms[0] ) ) : '';
?>
<article
	class="bt-deal-card bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group flex flex-col h-full w-full"
	data-post-id="<?php echo esc_attr( $post_id ); ?>"
	data-post-type="referral-codes"
>
	<div class="flex flex-col sm:flex-row h-full bt-card-inner">

		<!-- App Logo / Thumbnail -->
		<a
			href="<?php echo $permalink; ?>"
			class="bt-card-thumb sm:w-[200px] shrink-0 bg-gradient-to-br from-emerald-50 to-teal-50 flex items-center justify-center sm:border-r sm:border-b-0 border-b border-slate-100 relative overflow-hidden"
			tabindex="-1"
			aria-hidden="true"
		>
			<img
				src="<?php echo esc_url( $thumb_url ); ?>"
				alt="<?php echo esc_attr( $display_name ); ?> Logo"
				class="w-20 h-20 object-contain rounded-2xl shadow-sm transition-transform duration-500 group-hover:scale-125"
				loading="lazy"
				decoding="async"
				width="96"
				height="96"
			>
			<!-- Type badge -->
			<div class="absolute top-3 left-3">
				<span class="bg-emerald-50 text-emerald-700 font-black text-xs px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5 border border-emerald-200">
					<i data-lucide="gift" class="w-3.5 h-3.5 shrink-0"></i> Referral
				</span>
			</div>
		</a>

		<!-- Content -->
		<div class="p-5 sm:p-6 flex-1 flex flex-col justify-between bg-white relative min-w-0">
			<div>
				<!-- Store & Category breadcrumb -->
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
				<div class="flex items-center gap-1.5 text-xs font-bold text-primary-600 uppercase tracking-wider">
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

				<!-- Signup bonus highlight -->
				<?php if ( $signup_bonus ) : ?>
				<div class="flex items-center gap-2 mb-3 bg-emerald-50 border border-emerald-200 rounded-2xl px-4 py-2.5 w-fit">
					<i data-lucide="star" class="w-4 h-4 text-emerald-600 fill-current shrink-0"></i>
					<span class="text-sm font-black text-emerald-700">Bonus: <span class="text-emerald-800"><?php echo esc_html( $signup_bonus ); ?></span></span>
				</div>
				<?php elseif ( $short_desc ) : ?>
				<div class="text-slate-600 text-sm line-clamp-2 break-words mb-3"><?php echo wp_kses_post( $short_desc ); ?></div>
				<?php else : ?>
				<div class="text-slate-600 text-sm line-clamp-2 break-words mb-3"><?php the_excerpt(); ?></div>
				<?php endif; ?>
			</div>

			<div class="flex flex-col sm:flex-row flex-wrap sm:items-center justify-between gap-3 mt-auto pt-4 border-t border-slate-100 relative z-10">
				<div class="flex items-center gap-3 flex-wrap">
					<!-- Referral code copy chip -->
					<?php if ( $referral_code ) : ?>
					<button
						class="bt-copy-code flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white border border-emerald-500 px-4 py-2 rounded-xl text-sm font-black transition-all active:scale-95 shadow-sm shadow-emerald-200"
						data-code="<?php echo esc_attr( $referral_code ); ?>"
						aria-label="<?php echo esc_attr( sprintf( __( 'Copy referral code: %s', 'bigtricks' ), $referral_code ) ); ?>"
					>
						<i data-lucide="copy" class="w-4 h-4 shrink-0"></i>
						<?php echo esc_html( $referral_code ); ?>
					</button>
					<?php endif; ?>
					<?php if ( $comments > 0 ) : ?>
					<a href="<?php echo esc_url( $permalink . '#comments' ); ?>" class="flex items-center gap-1.5 text-sm font-bold text-slate-500 hover:text-primary-600 transition-colors">
						<i data-lucide="message-square" class="w-4 h-4"></i>
						<span><?php echo esc_html( $comments ); ?></span>
					</a>
					<?php endif; ?>
				</div>
				<div class="w-full sm:w-auto relative z-10">
					<a
						href="<?php echo $dest_url; ?>"
						target="<?php echo $referral_link ? '_blank' : '_self'; ?>"
						rel="<?php echo $referral_link ? 'noopener noreferrer nofollow' : ''; ?>"
						class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-black bg-primary-600 hover:bg-primary-700 text-white shadow-md shadow-primary-200 dark:shadow-none transition-all active:scale-95"
					>
						<?php echo $referral_link ? esc_html__( 'Get Referral', 'bigtricks' ) : esc_html__( 'View Details', 'bigtricks' ); ?>
						<i data-lucide="external-link" class="w-4 h-4 shrink-0"></i>
					</a>
				</div>
			</div>
		</div>
	</div>
</article>

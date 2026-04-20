<?php
/**
 * Card Component: Credit Card (CPT: credit-card — credit-card-manager plugin)
 *
 * Key meta:
 *   annual_fee       — annual fee string
 *   joining_fee      — joining fee string
 *   cashback_rate    — cashback rate (e.g. "1.5%")
 *   reward_rate      — reward rate
 *   welcome_bonus    — welcome bonus text
 *   apply_link       — application URL
 *   rating           — numeric rating (0–5)
 *   review_count     — number of reviews
 *   featured         — boolean
 *   trending         — boolean
 *   gradient         — Tailwind gradient class for card visual
 *   theme_color      — HEX color for accents
 *
 * Taxonomies:
 *   store            — bank/issuer (shared with deals plugin)
 *   network-type     — Visa/Mastercard/etc.
 *   card-category    — Cashback/Travel/etc.
 *
 * @package Bigtricks
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_id     = get_the_ID();
$permalink   = esc_url( get_permalink() );
$comments    = (int) get_comments_number();

// Meta
$annual_fee    = sanitize_text_field( (string) get_post_meta( $post_id, 'annual_fee', true ) );
$joining_fee   = sanitize_text_field( (string) get_post_meta( $post_id, 'joining_fee', true ) );
$cashback_rate = sanitize_text_field( (string) get_post_meta( $post_id, 'cashback_rate', true ) );
$reward_rate   = sanitize_text_field( (string) get_post_meta( $post_id, 'reward_rate', true ) );
$welcome_bonus = sanitize_text_field( (string) get_post_meta( $post_id, 'welcome_bonus', true ) );
$apply_link    = esc_url( (string) get_post_meta( $post_id, 'apply_link', true ) );
$rating        = (float) get_post_meta( $post_id, 'rating', true );
$review_count  = (int) get_post_meta( $post_id, 'review_count', true );
$is_featured   = (bool) get_post_meta( $post_id, 'featured', true );
$is_trending   = (bool) get_post_meta( $post_id, 'trending', true );
$card_gradient = sanitize_text_field( (string) get_post_meta( $post_id, 'gradient', true ) ) ?: 'from-primary-500 to-purple-600';

// Thumbnail
$thumb_url = bigtricks_get_thumbnail_url( $post_id, 'medium_large' );

// Taxonomies
$bank_terms     = get_the_terms( $post_id, 'store' );
$bank_name      = ! empty( $bank_terms ) && ! is_wp_error( $bank_terms ) ? $bank_terms[0]->name : '';
$network_terms  = get_the_terms( $post_id, 'network-type' );
$network_name   = ! empty( $network_terms ) && ! is_wp_error( $network_terms ) ? $network_terms[0]->name : '';
$cat_terms      = get_the_terms( $post_id, 'card-category' );
$card_cat_name  = ! empty( $cat_terms ) && ! is_wp_error( $cat_terms ) ? $cat_terms[0]->name : '';

$dest_url = $apply_link ?: $permalink;
$has_rating = $rating > 0;

// Star display (rounded to nearest half)
$star_filled  = floor( $rating );
$star_half    = ( $rating - $star_filled ) >= 0.5 ? 1 : 0;
$star_empty   = 5 - $star_filled - $star_half;
?>
<article
	class="bt-deal-card bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group flex flex-col h-full w-full"
	data-post-id="<?php echo esc_attr( $post_id ); ?>"
	data-post-type="credit-card"
>
	<div class="flex flex-col sm:flex-row h-full bt-card-inner">

		<!-- Card Visual -->
		<a
			href="<?php echo $permalink; ?>"
			class="bt-card-thumb sm:w-[220px] shrink-0  flex flex-col items-center justify-center sm:border-r sm:border-b-0 border-b border-slate-100 relative overflow-hidden"
			tabindex="-1"
			aria-hidden="true"
		>
			<?php if ( has_post_thumbnail( $post_id ) ) : ?>
			<img
				src="<?php echo esc_url( $thumb_url ); ?>"
				alt="<?php the_title_attribute(); ?>"
				class="w-full h-64 object-contain drop-shadow-2xl transition-transform duration-500 group-hover:scale-125 group-hover:rotate-2"
				loading="lazy"
				decoding="async"
				width="220"
				height="130"
			>
			<?php else : ?>
			<!-- Abstract card placeholder -->
			<div class="w-full max-w-[180px] h-[110px] bg-white/20 rounded-2xl border border-white/30 backdrop-blur-sm flex flex-col justify-between p-4 shadow-xl transition-transform duration-500 group-hover:scale-105">
				<div class="flex justify-between items-start">
					<div class="w-8 h-6 bg-white/40 rounded-md"></div>
					<?php if ( $network_name ) : ?>
					<span class="text-white/80 text-xs font-black tracking-wide"><?php echo esc_html( $network_name ); ?></span>
					<?php endif; ?>
				</div>
				<div class="text-white font-black text-xs truncate opacity-70"><?php echo esc_html( get_the_title() ); ?></div>
			</div>
			<?php endif; ?>

			<!-- Badges -->
			<div class="absolute top-3 left-3 flex flex-col gap-1.5">
				<span class="bg-purple-50 text-purple-700 font-black text-xs px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5 border border-purple-200">
					<i data-lucide="credit-card" class="w-3.5 h-3.5 shrink-0"></i> Credit Card
				</span>
				<?php if ( $is_featured ) : ?>
				<span class="bg-amber-400 text-white font-black text-xs px-2.5 py-1 rounded-full shadow-sm">
					⭐ Featured
				</span>
				<?php elseif ( $is_trending ) : ?>
				<span class="bg-orange-500 text-white font-black text-xs px-2.5 py-1 rounded-full shadow-sm flex items-center gap-1">
					<i data-lucide="trending-up" class="w-3 h-3 shrink-0"></i> Trending
				</span>
				<?php endif; ?>
			</div>
		</a>

		<!-- Content -->
		<div class="p-5 sm:p-6 flex-1 flex flex-col justify-between bg-white relative min-w-0">
			<div>
				<!-- Bank / network breadcrumb -->
				<div class="mb-2 flex items-center gap-2 flex-wrap">
					<?php if ( $bank_name ) : ?>
					<div class="flex items-center gap-1.5 text-xs font-bold text-slate-500 uppercase tracking-wider">
						<i data-lucide="building-2" class="w-3 h-3"></i>
					<?php
					$bank_link = ! empty( $bank_terms ) && ! is_wp_error( $bank_terms ) ? esc_url( get_term_link( $bank_terms[0], 'store' ) ) : '';
					if ( $bank_link ) : ?>
					<a href="<?php echo $bank_link; ?>" class="hover:text-primary-600 transition-colors hover:underline"><?php echo esc_html( $bank_name ); ?></a>
					<?php else : ?>
					<span><?php echo esc_html( $bank_name ); ?></span>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<?php if ( $card_cat_name ) : ?>
				<div class="flex items-center gap-1 text-xs font-bold text-purple-600 uppercase tracking-wider">
					<i data-lucide="tag" class="w-3 h-3"></i>
					<?php
					$cat_link = ! empty( $cat_terms ) && ! is_wp_error( $cat_terms ) ? esc_url( get_term_link( $cat_terms[0], 'card-category' ) ) : '';
					if ( $cat_link ) : ?>
					<a href="<?php echo $cat_link; ?>" class="hover:underline"><?php echo esc_html( $card_cat_name ); ?></a>
					<?php else : ?>
					<span><?php echo esc_html( $card_cat_name ); ?></span>
					<?php endif; ?>
				</div>
				<?php endif; ?>
			</div>

			<!-- Title -->
			<h2 class="text-xl lg:text-2xl font-black text-slate-900 mb-1 leading-tight relative">
				<a href="<?php echo $permalink; ?>" class="focus:outline-none focus:underline">
					<span class="absolute inset-0 z-0" aria-hidden="true"></span>
					<?php the_title(); ?>
				</a>
			</h2>

				<!-- Rating -->
				<?php if ( $has_rating ) : ?>
				<div class="flex items-center gap-2 mb-3">
					<div class="flex items-center gap-0.5" aria-label="Rating: <?php echo esc_attr( $rating ); ?> out of 5">
						<?php for ( $i = 0; $i < $star_filled; $i++ ) : ?>
						<i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-current shrink-0"></i>
						<?php endfor; ?>
						<?php if ( $star_half ) : ?>
						<i data-lucide="star-half" class="w-3.5 h-3.5 text-amber-400 fill-current shrink-0"></i>
						<?php endif; ?>
						<?php for ( $i = 0; $i < $star_empty; $i++ ) : ?>
						<i data-lucide="star" class="w-3.5 h-3.5 text-slate-200 shrink-0"></i>
						<?php endfor; ?>
					</div>
					<span class="text-xs font-black text-slate-700"><?php echo esc_html( number_format( $rating, 1 ) ); ?></span>
					<?php if ( $review_count > 0 ) : ?>
					<span class="text-xs text-slate-400">(<?php echo esc_html( $review_count ); ?>)</span>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<!-- Key stats row -->
				<div class="flex flex-wrap gap-3 mb-2">
					<?php if ( $annual_fee !== '' ) : ?>
					<div class="flex flex-col bg-slate-50 rounded-xl px-3 py-2 border border-slate-100">
						<span class="text-xs text-slate-500 font-medium leading-none mb-0.5">Annual Fee</span>
						<span class="text-sm font-black text-slate-900">
							<?php echo $annual_fee === '0' || $annual_fee === 'Free' ? '<span class="text-emerald-600">Free</span>' : esc_html( '₹' . $annual_fee ); ?>
						</span>
					</div>
					<?php endif; ?>
					<?php if ( $cashback_rate ) : ?>
					<div class="flex flex-col bg-slate-50 rounded-xl px-3 py-2 border border-slate-100">
						<span class="text-xs text-slate-500 font-medium leading-none mb-0.5">Cashback</span>
						<span class="text-sm font-black text-emerald-600"><?php echo esc_html( $cashback_rate ); ?></span>
					</div>
					<?php elseif ( $reward_rate ) : ?>
					<div class="flex flex-col bg-slate-50 rounded-xl px-3 py-2 border border-slate-100">
						<span class="text-xs text-slate-500 font-medium leading-none mb-0.5">Rewards</span>
						<span class="text-sm font-black text-primary-600"><?php echo esc_html( $reward_rate ); ?></span>
					</div>
					<?php endif; ?>
					<?php if ( $welcome_bonus ) : ?>
					<div class="flex flex-col bg-slate-50 rounded-xl px-3 py-2 border border-slate-100">
						<span class="text-xs text-slate-500 font-medium leading-none mb-0.5">Welcome Bonus</span>
						<span class="text-sm font-black text-purple-600 line-clamp-1"><?php echo esc_html( $welcome_bonus ); ?></span>
					</div>
					<?php endif; ?>
				</div>
			</div>

			<div class="flex flex-col sm:flex-row flex-wrap sm:items-center justify-between gap-3 mt-auto pt-4 border-t border-slate-100 relative z-10">
				<div class="flex items-center gap-3 text-sm font-bold text-slate-500">
					<?php if ( $network_name && ! has_post_thumbnail( $post_id ) ) : ?>
					<span class="bg-slate-100 text-slate-600 text-xs font-black px-2.5 py-1 rounded-full"><?php echo esc_html( $network_name ); ?></span>
					<?php endif; ?>
					<?php if ( $comments > 0 ) : ?>
					<a href="<?php echo esc_url( $permalink . '#comments' ); ?>" class="flex items-center gap-1.5 hover:text-purple-600 transition-colors">
						<i data-lucide="message-square" class="w-4 h-4"></i>
						<span><?php echo esc_html( $comments ); ?></span>
					</a>
					<?php endif; ?>
				</div>
				<div class="w-full sm:w-auto relative z-10">
					<a
						href="<?php echo $dest_url; ?>"
						target="<?php echo $apply_link ? '_blank' : '_self'; ?>"
						rel="<?php echo $apply_link ? 'noopener noreferrer nofollow' : ''; ?>"
						class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-black bg-purple-600 hover:bg-purple-700 text-white shadow-md shadow-purple-200 transition-all active:scale-95"
					>
						Apply Now <i data-lucide="external-link" class="w-4 h-4 shrink-0"></i>
					</a>
				</div>
			</div>
		</div>
	</div>
</article>

<?php
/**
 * Card Component: Regular Post (Offer / Blog)
 *
 * Expected context vars (set via set_query_var or passed through locate_template):
 *   $post       — WP_Post object (set_the_post already called)
 *   $post_id    — int
 *   $thumb_url  — string
 *   $permalink  — string
 *
 * @package Bigtricks
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$post_id   = get_the_ID();
$thumb_url = bigtricks_get_thumbnail_url( $post_id, 'medium_large' );
$permalink = esc_url( get_permalink() );
$cat_obj   = get_the_category();
$cat_name  = ! empty( $cat_obj ) ? $cat_obj[0]->name : '';
$cat_link  = ! empty( $cat_obj ) ? esc_url( get_category_link( $cat_obj[0]->term_id ) ) : '';
$comments  = (int) get_comments_number();
?>
<article
	class="bt-deal-card bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group flex flex-col h-full w-full"
	data-post-id="<?php echo esc_attr( $post_id ); ?>"
	data-post-type="post"
>
	<div class="flex flex-col sm:flex-row h-full bt-card-inner">

		<!-- Thumbnail -->
		<a
			href="<?php echo $permalink; ?>"
			class="bt-card-thumb sm:w-[220px] shrink-0 bg-slate-50 p-5 flex items-center justify-center sm:border-r sm:border-b-0 border-b border-slate-100 relative overflow-hidden"
			tabindex="-1"
			aria-hidden="true"
		>
			<img
				src="<?php echo esc_url( $thumb_url ); ?>"
				alt="<?php the_title_attribute(); ?>"
				class="max-h-[180px] max-w-full object-contain mix-blend-multiply transition-transform duration-500 group-hover:scale-105"
				loading="lazy"
				decoding="async"
				width="220"
				height="180"
			>
			<!-- Type Badge -->
			<div class="absolute top-3 left-3">
				<span class="bg-blue-50 text-blue-600 font-black text-xs px-3 py-1.5 rounded-full shadow-sm flex items-center gap-1.5 border border-blue-100">
					<i data-lucide="book-open" class="w-3.5 h-3.5 shrink-0"></i> Offer
				</span>
			</div>
		</a>

		<!-- Content -->
		<div class="p-5 sm:p-6 flex-1 flex flex-col justify-between bg-white relative min-w-0">
			<div>
				<?php if ( $cat_name ) : ?>
				<div class="mb-2 flex items-center gap-1.5 text-xs font-bold text-indigo-600 uppercase tracking-wider">
					<i data-lucide="tag" class="w-3 h-3"></i>
					<?php if ( $cat_link ) : ?>
					<a href="<?php echo $cat_link; ?>" class="hover:underline"><?php echo esc_html( $cat_name ); ?></a>
					<?php else : ?>
					<?php echo esc_html( $cat_name ); ?>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<h2 class="font-black text-slate-900 group-hover:text-indigo-600 leading-snug mb-3 transition-colors break-words text-lg sm:text-xl line-clamp-2">
					<a href="<?php echo $permalink; ?>" class="focus:outline-none focus:underline">
						<span class="absolute inset-0 z-0" aria-hidden="true"></span>
						<?php the_title(); ?>
					</a>
				</h2>

				<div class="text-slate-600 text-sm line-clamp-2 break-words mb-4">
					<?php the_excerpt(); ?>
				</div>
			</div>

			<div class="flex flex-col sm:flex-row flex-wrap sm:items-center justify-between gap-3 mt-auto pt-4 border-t border-slate-100 relative z-10">
				<div class="flex items-center gap-4 text-sm font-bold text-slate-500">
					<span class="flex items-center gap-1.5 text-slate-400">
						<i data-lucide="clock" class="w-3.5 h-3.5"></i>
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" class="text-xs">
							<?php echo esc_html( get_the_date( 'M j, Y' ) ); ?>
						</time>
					</span>
					<?php if ( $comments > 0 ) : ?>
					<a href="<?php echo esc_url( $permalink . '#comments' ); ?>" class="flex items-center gap-1.5 hover:text-indigo-600 transition-colors">
						<i data-lucide="message-square" class="w-3.5 h-3.5"></i>
						<span><?php echo esc_html( $comments ); ?></span>
					</a>
					<?php endif; ?>
				</div>
				<div class="w-full sm:w-auto relative z-10">
					<a
						href="<?php echo $permalink; ?>"
						class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-black bg-indigo-600 hover:bg-indigo-700 text-white shadow-md shadow-indigo-200 transition-all active:scale-95"
					>
						Read Article <i data-lucide="chevron-right" class="w-4 h-4 shrink-0"></i>
					</a>
				</div>
			</div>
		</div>
	</div>
</article>

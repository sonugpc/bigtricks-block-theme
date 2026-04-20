<?php
/**
 * Related Posts Component
 * Reusable related posts section with custom query args
 * 
 * @package Bigtricks
 * 
 * Usage:
 * get_template_part( 'template-parts/related-posts', null, [
 *     'post_id'        => get_the_ID(),
 *     'category'       => 'credit-cards', // optional category slug
 *     'posts_per_page' => 3,
 *     'title'          => 'Related Articles',
 *     'icon'           => 'newspaper',
 * ] );
 */

// Get passed arguments
$post_id        = $args['post_id'] ?? get_the_ID();
$category       = $args['category'] ?? '';
$posts_per_page = $args['posts_per_page'] ?? 3;
$title          = $args['title'] ?? __( 'Related Articles', 'bigtricks' );
$icon           = $args['icon'] ?? 'zap';

// Build query args
$related_args = [
	'post_type'      => 'post',
	'posts_per_page' => $posts_per_page,
	'post__not_in'   => [ $post_id ],
	'orderby'        => 'date',
	'order'          => 'DESC',
	'post_status'    => 'publish',
];

// Add category filter if provided
if ( ! empty( $category ) ) {
	$related_args['category_name'] = $category;
} else {
	// Use post's first category
	$cat_obj = get_the_category( $post_id );
	if ( ! empty( $cat_obj ) ) {
		$related_args['cat'] = $cat_obj[0]->term_id;
	}
}

$related_query = new WP_Query( $related_args );

if ( $related_query->have_posts() ) : ?>
<div class="mt-12">
	<h2 class="text-2xl font-black text-slate-900 dark:text-white mb-6 flex items-center gap-2">
		<i data-lucide="<?php echo esc_attr( $icon ); ?>" class="w-5 h-5 text-primary-500"></i>
		<?php echo esc_html( $title ); ?>
	</h2>
	<div class="grid sm:grid-cols-3 gap-4">
		<?php while ( $related_query->have_posts() ) :
			$related_query->the_post();
			$rel_thumb = has_post_thumbnail() ? get_the_post_thumbnail_url( get_the_ID(), 'medium_large' ) : '';
			if ( empty( $rel_thumb ) && function_exists( 'bigtricks_get_thumbnail_url' ) ) {
				$rel_thumb = bigtricks_get_thumbnail_url( get_the_ID(), 'medium_large' );
			}
			?>
		<a href="<?php the_permalink(); ?>" class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-md hover:-translate-y-1 transition-all duration-300 group block">
			<?php if ( $rel_thumb ) : ?>
			<div class="h-40 bg-slate-100 dark:bg-slate-900 overflow-hidden">
				<img src="<?php echo esc_url( $rel_thumb ); ?>" alt="<?php the_title_attribute(); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy" decoding="async">
			</div>
			<?php endif; ?>
			<div class="p-4">
				<h3 class="font-bold text-slate-900 dark:text-white text-sm line-clamp-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors"><?php the_title(); ?></h3>
				<time class="text-xs text-slate-400 dark:text-slate-500 mt-1 block"><?php echo esc_html( get_the_date( 'M j, Y' ) ); ?></time>
			</div>
		</a>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
	</div>
</div>
<?php endif; ?>

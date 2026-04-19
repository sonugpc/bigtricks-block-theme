<?php
/**
 * Widget: Trending Categories
 * 
 * Displays top categories by post count
 * 
 * @package Bigtricks
 */

declare(strict_types=1);

$categories_count = $args['count'] ?? 8;
$sidebar_cats     = bigtricks_get_top_categories( $categories_count );

if ( empty( $sidebar_cats ) ) {
	return;
}
?>

<!-- Trending Categories -->
<div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
	<div class="bg-slate-50 dark:bg-slate-800 px-6 py-5 border-b border-slate-200 dark:border-slate-700 font-black text-slate-900 dark:text-white flex items-center gap-2 text-lg">
		<i data-lucide="tag" class="w-5 h-5 text-primary-500"></i>
		<?php esc_html_e( 'Trending Categories', 'bigtricks' ); ?>
	</div>
	<ul class="divide-y divide-slate-100 dark:divide-slate-800" role="list">
		<?php foreach ( $sidebar_cats as $scat ) : ?>
			<li>
				<a
					href="<?php echo esc_url( get_category_link( $scat->term_id ) ); ?>"
					class="w-full text-left px-6 py-4 hover:bg-primary-50 dark:hover:bg-slate-800 text-sm font-bold text-slate-700 dark:text-slate-300 flex justify-between items-center group transition-colors"
				>
					<span><?php echo esc_html( $scat->name ); ?></span>
					<div class="bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 group-hover:bg-primary-100 dark:group-hover:bg-primary-900 group-hover:text-primary-600 dark:group-hover:text-primary-400 rounded-full p-1 transition-colors">
						<i data-lucide="chevron-right" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform"></i>
					</div>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="p-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-100 dark:border-slate-700">
		<a href="<?php echo esc_url( home_url( '/categories/' ) ); ?>" class="w-full text-center text-sm font-bold text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition-colors block">
			<?php esc_html_e( 'View All Categories', 'bigtricks' ); ?>
		</a>
	</div>
</div>

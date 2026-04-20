<?php
/**
 * Template Name: Saved Posts Page
 *
 * Frontend placeholder for user saved posts.
 *
 * @package Bigtricks
 */

declare( strict_types=1 );

if ( ! is_user_logged_in() ) {
	wp_safe_redirect( bigtricks_get_login_url( get_permalink() ) );
	exit;
}

get_header();
?>

<main class="max-w-[1400px] mx-auto px-4 py-8 md:py-10 flex-1" id="main-content">
	<div class="mb-8">
		<h1 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white"><?php esc_html_e( 'My Saved posts', 'bigtricks' ); ?></h1>
		<p class="mt-2 text-slate-600 dark:text-slate-400"><?php esc_html_e( 'This page is ready. Connect your save/bookmark logic here to show saved items.', 'bigtricks' ); ?></p>
	</div>

	<div class="rounded-3xl border border-dashed border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 p-8 md:p-10">
		<div class="max-w-3xl">
			<h2 class="text-xl font-black text-slate-900 dark:text-white mb-3"><?php esc_html_e( 'Backend Setup Suggestion', 'bigtricks' ); ?></h2>
			<p class="text-slate-600 dark:text-slate-400 leading-relaxed mb-4">
				<?php esc_html_e( 'Store saved post IDs in user meta (example key: bt_saved_posts). Then query posts with post__in and render with existing card template parts.', 'bigtricks' ); ?>
			</p>
			<p class="text-sm text-slate-500 dark:text-slate-400">
				<?php esc_html_e( 'This keeps the UX fast and works for all post types.', 'bigtricks' ); ?>
			</p>
		</div>
	</div>
</main>

<?php get_footer(); ?>

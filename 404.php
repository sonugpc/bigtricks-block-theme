<?php
/**
 * 404 Not Found
 *
 * @package Bigtricks
 */

get_header();
?>

<main class="max-w-[1400px] mx-auto px-4 py-16 flex-1 w-full flex items-center justify-center" id="main-content">
	<div class="text-center max-w-lg mx-auto">
		<div class="bg-indigo-50 w-28 h-28 rounded-full flex items-center justify-center mx-auto mb-6">
			<i data-lucide="search-x" class="w-12 h-12 text-indigo-400"></i>
		</div>
		<h1 class="text-6xl font-black text-indigo-600 mb-4">404</h1>
		<h2 class="text-2xl font-black text-slate-900 mb-3"><?php esc_html_e( 'Deal Not Found', 'bigtricks' ); ?></h2>
		<p class="text-slate-500 mb-8 font-medium leading-relaxed">
			<?php esc_html_e( "Looks like this deal has expired or the page doesn't exist. Browse our latest deals instead.", 'bigtricks' ); ?>
		</p>
		<div class="flex flex-col sm:flex-row gap-4 justify-center">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white font-black px-8 py-3.5 rounded-2xl shadow-lg shadow-indigo-200 transition-all flex items-center justify-center gap-2">
				<i data-lucide="home" class="w-4 h-4"></i>
				<?php esc_html_e( 'Browse Deals', 'bigtricks' ); ?>
			</a>
			<a href="<?php echo esc_url( home_url( '/store/' ) ); ?>" class="bg-white border border-slate-200 text-slate-700 hover:border-indigo-200 hover:text-indigo-600 font-bold px-8 py-3.5 rounded-2xl transition-all flex items-center justify-center gap-2">
				<i data-lucide="shopping-bag" class="w-4 h-4"></i>
				<?php esc_html_e( 'View Stores', 'bigtricks' ); ?>
			</a>
		</div>
	</div>
</main>

<?php get_footer(); ?>

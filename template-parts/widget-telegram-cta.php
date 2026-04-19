<?php
/**
 * Widget: Telegram CTA
 * 
 * Large promotional banner for Telegram channel
 * 
 * @package Bigtricks
 */

declare(strict_types=1);

$telegram_url = bigtricks_option( 'bt_telegram_url', 'https://t.me/bigtricks' );

// Don't show widget if URL is not set
if ( ! $telegram_url ) {
	return;
}
?>

<!-- Telegram CTA -->
<div class="bg-gradient-to-br from-blue-500 to-cyan-500 dark:from-blue-600 dark:to-cyan-600 rounded-3xl p-8 text-white shadow-xl shadow-blue-200/60 dark:shadow-none relative overflow-hidden group cursor-pointer hover:-translate-y-1 transition-all duration-300">
	<i data-lucide="send" class="absolute -right-6 -bottom-6 w-40 h-40 text-white opacity-20 group-hover:scale-110 group-hover:rotate-12 transition-transform duration-700"></i>
	<div class="relative z-10">
		<span class="bg-white/20 inline-block px-3 py-1 rounded-full text-xs font-black tracking-wider mb-4 border border-white/30 uppercase">
			<?php esc_html_e( 'Must Join', 'bigtricks' ); ?>
		</span>
		<h2 class="font-black text-white text-3xl mb-2 drop-shadow-sm leading-tight"><?php esc_html_e( 'Instant Loot Alerts', 'bigtricks' ); ?></h2>
		<p class="text-blue-50 text-sm mb-6 font-medium leading-relaxed">
			<?php esc_html_e( 'Join 50k+ smart shoppers on Telegram. Get price errors and hidden coupons before they expire!', 'bigtricks' ); ?>
		</p>
		<a
			href="<?php echo esc_url( $telegram_url ); ?>"
			target="_blank"
			rel="noopener noreferrer"
			class="bg-white text-blue-600 font-black py-3.5 px-4 rounded-xl w-full shadow-lg hover:bg-blue-50 transition-colors flex items-center justify-center gap-2"
			aria-label="<?php esc_attr_e( 'Join Telegram Channel', 'bigtricks' ); ?>"
		>
			<i data-lucide="send" class="w-5 h-5"></i>
			<?php esc_html_e( 'Join Telegram Channel', 'bigtricks' ); ?>
		</a>
	</div>
</div>

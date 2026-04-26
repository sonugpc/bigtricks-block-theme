<?php
/**
 * Widget: Follow Us
 * 
 * Social media follow widget for sidebar
 * 
 * @package Bigtricks
 */

declare(strict_types=1);

// Get social URLs from theme settings
$telegram_url  = bigtricks_option( 'bt_telegram_url', 'https://t.me/bigtricksin' );
$whatsapp_url  = bigtricks_option( 'bt_whatsapp_url', 'https://chat.whatsapp.com/your-group' );
$twitter_url   = bigtricks_option( 'bt_twitter_url', 'https://twitter.com/bigtricksin' );
$instagram_url = bigtricks_option( 'bt_instagram_url', 'https://instagram.com/bigtricksin' );
$youtube_url   = bigtricks_option( 'bt_youtube_url', 'https://youtube.com/@bigtricksin' );
$facebook_url  = bigtricks_option( 'bt_facebook_url', 'https://facebook.com/bigtricksin' );
$linkedin_url  = bigtricks_option( 'bt_linkedin_url', 'https://linkedin.com/company/bigtricksin' );

// Only show widget if at least one URL is set
if ( ! $telegram_url && ! $whatsapp_url && ! $twitter_url && ! $instagram_url && ! $youtube_url && ! $facebook_url && ! $linkedin_url ) {
	return;
}
?>

<!-- Social Follow Widget -->
<div class="bg-gradient-to-br from-primary-50 to-purple-50 dark:from-slate-900 dark:to-slate-800 rounded-2xl shadow-soft border border-primary-100 dark:border-slate-800 overflow-hidden">
	<div class="p-4 border-b border-primary-100 dark:border-slate-800">
		<h3 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
			<i data-lucide="users" class="w-5 h-5 text-primary-600"></i>
			<?php esc_html_e( 'Follow Us', 'bigtricks' ); ?>
		</h3>
	</div>
	<div class="p-4 space-y-2">
		<?php if ( $telegram_url ) : ?>
		<!-- Telegram -->
		<a href="<?php echo esc_url( $telegram_url ); ?>" target="_blank" rel="noopener" class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition-all group">
			<div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center shrink-0">
				<i data-lucide="send" class="w-5 h-5 text-white"></i>
			</div>
			<div class="flex-1">
				<div class="text-sm font-bold text-slate-900 dark:text-white">Telegram</div>
				<div class="text-xs text-slate-500 dark:text-slate-400">Join our channel</div>
			</div>
			<i data-lucide="arrow-right" class="w-4 h-4 text-slate-400 dark:text-slate-500 group-hover:translate-x-1 transition-transform"></i>
		</a>
		<?php endif; ?>

		<?php if ( $whatsapp_url ) : ?>
		<!-- WhatsApp -->
		<a href="<?php echo esc_url( $whatsapp_url ); ?>" target="_blank" rel="noopener" class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition-all group">
			<div class="w-10 h-10 bg-[#25D366] rounded-full flex items-center justify-center shrink-0">
				<i data-lucide="message-circle" class="w-5 h-5 text-white"></i>
			</div>
			<div class="flex-1">
				<div class="text-sm font-bold text-slate-900 dark:text-white">WhatsApp</div>
				<div class="text-xs text-slate-500 dark:text-slate-400">Join community</div>
			</div>
			<i data-lucide="arrow-right" class="w-4 h-4 text-slate-400 dark:text-slate-500 group-hover:translate-x-1 transition-transform"></i>
		</a>
		<?php endif; ?>

		<?php if ( $twitter_url ) : ?>
		<!-- Twitter -->
		<a href="<?php echo esc_url( $twitter_url ); ?>" target="_blank" rel="noopener" class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition-all group">
			<div class="w-10 h-10 bg-slate-900 dark:bg-slate-700 rounded-full flex items-center justify-center shrink-0">
				<i data-lucide="twitter" class="w-5 h-5 text-white"></i>
			</div>
			<div class="flex-1">
				<div class="text-sm font-bold text-slate-900 dark:text-white">Twitter</div>
				<div class="text-xs text-slate-500 dark:text-slate-400">Follow updates</div>
			</div>
			<i data-lucide="arrow-right" class="w-4 h-4 text-slate-400 dark:text-slate-500 group-hover:translate-x-1 transition-transform"></i>
		</a>
		<?php endif; ?>

		<?php if ( $instagram_url ) : ?>
		<!-- Instagram -->
		<a href="<?php echo esc_url( $instagram_url ); ?>" target="_blank" rel="noopener" class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition-all group">
			<div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center shrink-0">
				<i data-lucide="instagram" class="w-5 h-5 text-white"></i>
			</div>
			<div class="flex-1">
				<div class="text-sm font-bold text-slate-900 dark:text-white">Instagram</div>
				<div class="text-xs text-slate-500 dark:text-slate-400">Daily deals</div>
			</div>
			<i data-lucide="arrow-right" class="w-4 h-4 text-slate-400 dark:text-slate-500 group-hover:translate-x-1 transition-transform"></i>
		</a>
		<?php endif; ?>

		<?php if ( $youtube_url ) : ?>
		<!-- YouTube -->
		<a href="<?php echo esc_url( $youtube_url ); ?>" target="_blank" rel="noopener" class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition-all group">
			<div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center shrink-0">
				<svg class="w-5 h-5" viewBox="0 0 24 24" fill="white" xmlns="http://www.w3.org/2000/svg"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 0 0 .5 6.2C0 8.1 0 12 0 12s0 3.9.5 5.8a3 3 0 0 0 2.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1C24 15.9 24 12 24 12s0-3.9-.5-5.8zM9.7 15.5V8.5l6.3 3.5-6.3 3.5z"/></svg>
			</div>
			<div class="flex-1">
				<div class="text-sm font-bold text-slate-900 dark:text-white">YouTube</div>
				<div class="text-xs text-slate-500 dark:text-slate-400">Video tutorials</div>
			</div>
			<i data-lucide="arrow-right" class="w-4 h-4 text-slate-400 dark:text-slate-500 group-hover:translate-x-1 transition-transform"></i>
		</a>
		<?php endif; ?>

		<?php if ( $facebook_url ) : ?>
		<!-- Facebook -->
		<a href="<?php echo esc_url( $facebook_url ); ?>" target="_blank" rel="noopener" class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition-all group">
			<div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center shrink-0">
				<i data-lucide="facebook" class="w-5 h-5 text-white"></i>
			</div>
			<div class="flex-1">
				<div class="text-sm font-bold text-slate-900 dark:text-white">Facebook</div>
				<div class="text-xs text-slate-500 dark:text-slate-400">Community updates</div>
			</div>
			<i data-lucide="arrow-right" class="w-4 h-4 text-slate-400 dark:text-slate-500 group-hover:translate-x-1 transition-transform"></i>
		</a>
		<?php endif; ?>

		<?php if ( $linkedin_url ) : ?>
		<!-- LinkedIn -->
		<a href="<?php echo esc_url( $linkedin_url ); ?>" target="_blank" rel="noopener" class="flex items-center gap-3 p-3 bg-white dark:bg-slate-800 rounded-xl hover:shadow-md transition-all group">
			<div class="w-10 h-10 bg-blue-700 rounded-full flex items-center justify-center shrink-0">
				<i data-lucide="linkedin" class="w-5 h-5 text-white"></i>
			</div>
			<div class="flex-1">
				<div class="text-sm font-bold text-slate-900 dark:text-white">LinkedIn</div>
				<div class="text-xs text-slate-500 dark:text-slate-400">Professional network</div>
			</div>
			<i data-lucide="arrow-right" class="w-4 h-4 text-slate-400 dark:text-slate-500 group-hover:translate-x-1 transition-transform"></i>
		</a>
		<?php endif; ?>
	</div>
</div>

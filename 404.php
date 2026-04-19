<?php
/**
 * 404 Not Found
 *
 * @package Bigtricks
 */

get_header();
?>

<main class="max-w-[1400px] mx-auto px-4 py-16 flex-1 w-full flex items-center justify-center" id="main-content">
	<div class="text-center max-w-2xl mx-auto">
		<!-- 404 Icon -->
		<div class="bg-gradient-to-br from-primary-50 to-slate-100 w-32 h-32 rounded-full flex items-center justify-center mx-auto mb-8 shadow-lg">
			<i data-lucide="zap" class="w-16 h-16 text-primary-600"></i>
		</div>

		<!-- Main Heading -->
		<h1 class="text-7xl sm:text-8xl font-black text-transparent bg-clip-text bg-gradient-to-r from-primary-600 to-primary-500 mb-4">404</h1>

		<!-- Subheading -->
		<h2 class="text-3xl sm:text-4xl font-black text-slate-900 mb-4">
			<?php esc_html_e( 'Oops! Wrong Deal! 🎯', 'bigtricks' ); ?>
		</h2>

		<!-- Funny Message -->
		<p class="text-lg sm:text-xl text-slate-600 mb-3 font-semibold leading-relaxed">
			<?php esc_html_e( 'This page took an early exit faster than a flash deal on Black Friday!', 'bigtricks' ); ?>
		</p>

		<!-- Meaningful Call-to-Action -->
		<p class="text-slate-500 mb-10 font-medium leading-relaxed max-w-xl mx-auto">
			<?php esc_html_e( "Don't worry—it happens to the best of us. Head back to score amazing deals, or better yet, join our community to never miss out on the next viral offer!", 'bigtricks' ); ?>
		</p>

		<!-- Homepage Button -->
		<div class="mb-10">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="inline-flex items-center justify-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-black px-8 py-4 rounded-2xl shadow-lg shadow-primary-200 dark:shadow-none transition-all transform hover:scale-105">
				<i data-lucide="home" class="w-5 h-5"></i>
				<?php esc_html_e( 'Visit Homepage', 'bigtricks' ); ?>
			</a>
		</div>

		<!-- Social Links Section -->
		<div class="border-t border-slate-200 pt-10">
			<p class="text-slate-600 font-bold mb-6">
				<?php esc_html_e( '💬 Quick way to stay updated:', 'bigtricks' ); ?>
			</p>
			<div class="flex flex-wrap gap-3 justify-center">
				<?php
				$bt_telegram  = bigtricks_option( 'bt_telegram_url' );
				$bt_whatsapp  = bigtricks_option( 'bt_whatsapp_url' );
				$bt_twitter   = bigtricks_option( 'bt_twitter_url' );
				$bt_instagram = bigtricks_option( 'bt_instagram_url' );
				?>

				<?php if ( $bt_telegram ) : ?>
					<a href="<?php echo esc_url( $bt_telegram ); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-blue-600 transition-all transform hover:scale-105 shadow-md">
						<i data-lucide="send" class="w-4 h-4"></i>
						<?php esc_html_e( 'Telegram', 'bigtricks' ); ?>
					</a>
				<?php endif; ?>

				<?php if ( $bt_whatsapp ) : ?>
					<a href="<?php echo esc_url( $bt_whatsapp ); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#25D366] text-white font-bold rounded-xl hover:bg-[#1faa4f] transition-all transform hover:scale-105 shadow-md">
						<i data-lucide="message-circle" class="w-4 h-4"></i>
						<?php esc_html_e( 'WhatsApp', 'bigtricks' ); ?>
					</a>
				<?php endif; ?>

				<?php if ( $bt_twitter ) : ?>
					<a href="<?php echo esc_url( $bt_twitter ); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-blue-500 text-white font-bold rounded-xl hover:bg-blue-600 transition-all transform hover:scale-105 shadow-md">
						<i data-lucide="share-2" class="w-4 h-4"></i>
						<?php esc_html_e( 'Twitter / X', 'bigtricks' ); ?>
					</a>
				<?php endif; ?>

				<?php if ( $bt_instagram ) : ?>
					<a href="<?php echo esc_url( $bt_instagram ); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-bold rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all transform hover:scale-105 shadow-md">
						<i data-lucide="camera" class="w-4 h-4"></i>
						<?php esc_html_e( 'Instagram', 'bigtricks' ); ?>
					</a>
				<?php endif; ?>
			</div>
			<p class="text-slate-400 text-sm mt-6">
				<?php esc_html_e( '✅ Get instant notifications for flash deals & price drops', 'bigtricks' ); ?>
			</p>
		</div>
	</div>
</main>

<?php get_footer(); ?>

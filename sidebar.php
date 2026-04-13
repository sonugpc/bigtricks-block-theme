<?php
/**
 * Right Sidebar
 *
 * @package Bigtricks
 */
?>
<aside class="w-full lg:w-[380px] shrink-0 space-y-8 hidden lg:block" aria-label="<?php esc_attr_e( 'Sidebar', 'bigtricks' ); ?>">

	<!-- TELEGRAM CTA -->
	<div class="bg-gradient-to-br from-blue-500 to-cyan-500 rounded-3xl p-8 text-white shadow-xl shadow-blue-200/60 relative overflow-hidden group cursor-pointer hover:-translate-y-1 transition-all duration-300">
		<i data-lucide="send" class="absolute -right-6 -bottom-6 w-40 h-40 text-white opacity-20 group-hover:scale-110 group-hover:rotate-12 transition-transform duration-700"></i>
		<div class="relative z-10">
			<span class="bg-white/20 inline-block px-3 py-1 rounded-full text-xs font-black tracking-wider mb-4 border border-white/30 uppercase">
				<?php esc_html_e( 'Must Join', 'bigtricks' ); ?>
			</span>
			<h2 class="font-black text-3xl mb-2 drop-shadow-sm leading-tight"><?php esc_html_e( 'Instant Loot Alerts', 'bigtricks' ); ?></h2>
			<p class="text-blue-50 text-sm mb-6 font-medium leading-relaxed">
				<?php esc_html_e( 'Join 50k+ smart shoppers on Telegram. Get price errors and hidden coupons before they expire!', 'bigtricks' ); ?>
			</p>
			<a
				href="https://t.me/bigtricks"
				target="_blank"
				rel="noopener noreferrer"
				class="bg-white text-blue-600 font-black py-3.5 px-4 rounded-xl w-full shadow-lg hover:bg-blue-50 transition-colors flex items-center justify-center gap-2"
			>
				<i data-lucide="send" class="w-5 h-5"></i>
				<?php esc_html_e( 'Join Telegram Channel', 'bigtricks' ); ?>
			</a>
		</div>
	</div>

	<!-- TRENDING CATEGORIES -->
	<div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
		<div class="bg-slate-50 px-6 py-5 border-b border-slate-200 font-black text-slate-900 flex items-center gap-2 text-lg">
			<i data-lucide="tag" class="w-5 h-5 text-indigo-500"></i>
			<?php esc_html_e( 'Trending Categories', 'bigtricks' ); ?>
		</div>
		<ul class="divide-y divide-slate-100" role="list">
			<?php
			$sidebar_cats = bigtricks_get_top_categories( 8 );
			foreach ( $sidebar_cats as $scat ) :
				?>
				<li>
					<a
						href="<?php echo esc_url( get_category_link( $scat->term_id ) ); ?>"
						class="w-full text-left px-6 py-4 hover:bg-indigo-50 text-sm font-bold text-slate-700 flex justify-between items-center group transition-colors"
					>
						<span><?php echo esc_html( $scat->name ); ?></span>
						<div class="bg-slate-100 text-slate-400 group-hover:bg-indigo-100 group-hover:text-indigo-600 rounded-full p-1 transition-colors">
							<i data-lucide="chevron-right" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform"></i>
						</div>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
		<div class="p-4 bg-slate-50 border-t border-slate-100">
			<a href="<?php echo esc_url( home_url( '/categories/' ) ); ?>" class="w-full text-center text-sm font-bold text-indigo-600 hover:text-indigo-800 transition-colors block">
				<?php esc_html_e( 'View All Categories', 'bigtricks' ); ?>
			</a>
		</div>
	</div>

	<!-- NEWSLETTER / DAILY DEAL DIGEST -->
	<div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 text-center">
		<div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
			<i data-lucide="flame" class="w-7 h-7 text-orange-500"></i>
		</div>
		<h2 class="font-black text-xl text-slate-900 mb-2"><?php esc_html_e( 'Daily Deal Digest', 'bigtricks' ); ?></h2>
		<p class="text-slate-500 text-sm mb-5">
			<?php esc_html_e( 'Get the top 10 handpicked deals delivered to your inbox every morning.', 'bigtricks' ); ?>
		</p>
		<form
			class="flex gap-2"
			action="<?php echo esc_url( home_url( '/' ) ); ?>"
			method="post"
			aria-label="<?php esc_attr_e( 'Newsletter signup', 'bigtricks' ); ?>"
		>
			<?php wp_nonce_field( 'bigtricks_newsletter', 'bt_newsletter_nonce' ); ?>
			<label for="bt-newsletter-email" class="sr-only"><?php esc_html_e( 'Email address', 'bigtricks' ); ?></label>
			<input
				id="bt-newsletter-email"
				type="email"
				name="bt_newsletter_email"
				placeholder="<?php esc_attr_e( 'Email address', 'bigtricks' ); ?>"
				required
				class="w-full bg-slate-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 border border-transparent focus:border-indigo-200"
			>
			<button
				type="submit"
				class="bg-slate-900 text-white px-4 rounded-xl font-bold hover:bg-slate-800 transition-colors shrink-0"
			>
				<?php esc_html_e( 'Go', 'bigtricks' ); ?>
			</button>
		</form>
	</div>

	<!-- WHATSAPP CTA -->
	<div class="bg-gradient-to-br from-[#128C7E] to-[#25D366] rounded-3xl p-8 text-white shadow-xl relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
		<i data-lucide="message-circle" class="absolute -right-4 -bottom-4 w-32 h-32 text-white opacity-20 group-hover:scale-110 transition-transform duration-700"></i>
		<div class="relative z-10">
			<h2 class="font-black text-2xl mb-2 text-white"><?php esc_html_e( 'WhatsApp Community', 'bigtricks' ); ?></h2>
			<p class="text-green-50 text-sm mb-5 font-medium leading-relaxed">
				<?php esc_html_e( '1 lakh+ members receiving exclusive deals daily.', 'bigtricks' ); ?>
			</p>
			<a
				href="https://wa.me/bigtricks"
				target="_blank"
				rel="noopener noreferrer"
				class="bg-white text-[#128C7E] font-black py-3 px-4 rounded-xl w-full shadow-lg hover:bg-green-50 transition-colors flex items-center justify-center gap-2"
			>
				<i data-lucide="message-circle" class="w-5 h-5"></i>
				<?php esc_html_e( 'Join WhatsApp Group', 'bigtricks' ); ?>
			</a>
		</div>
	</div>

	<!-- WordPress Sidebar Widgets (optional) -->
	<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	<?php endif; ?>

	<!-- DOWNLOAD APP WIDGET -->
	<div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden group">
		<div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_rgba(99,102,241,0.3),_transparent_70%)] pointer-events-none"></div>
		<div class="relative z-10">
			<div class="flex items-center gap-3 mb-4">
				<div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center border border-white/20 shrink-0">
					<i data-lucide="smartphone" class="w-6 h-6 text-indigo-300"></i>
				</div>
				<div>
					<h2 class="font-black text-lg leading-tight"><?php esc_html_e( 'Bigtricks App', 'bigtricks' ); ?></h2>
					<p class="text-indigo-300 text-xs font-bold"><?php esc_html_e( 'Instant deal alerts on your phone!', 'bigtricks' ); ?></p>
				</div>
			</div>

			<div class="flex items-center gap-1.5 mb-4 text-yellow-400 text-sm font-bold">
				<?php for ( $s = 0; $s < 5; $s++ ) : ?>
				<i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
				<?php endfor; ?>
				<span class="text-white/70 text-xs ml-1">4.8 (12k reviews)</span>
			</div>

			<ul class="space-y-1.5 mb-5 text-sm text-white/80">
				<?php
				$app_features = [
					__( '🔔 Real-time loot alerts', 'bigtricks' ),
					__( '🏷️ Exclusive app-only coupons', 'bigtricks' ),
					__( '⚡ Price drop notifications', 'bigtricks' ),
					__( '📱 Works offline too', 'bigtricks' ),
				];
				foreach ( $app_features as $feat ) : ?>
				<li class="flex items-center gap-2">
					<span class="w-1.5 h-1.5 bg-indigo-400 rounded-full shrink-0"></span>
					<?php echo esc_html( $feat ); ?>
				</li>
				<?php endforeach; ?>
			</ul>

			<!-- Play Store Button -->
			<a
				href="https://play.google.com/store/apps/details?id=in.bigtricks"
				target="_blank"
				rel="noopener noreferrer"
				class="flex items-center gap-3 bg-white text-slate-900 font-black py-3.5 px-5 rounded-2xl shadow-lg hover:bg-indigo-50 transition-all active:scale-95 group/btn"
				aria-label="<?php esc_attr_e( 'Download Bigtricks on Google Play', 'bigtricks' ); ?>"
			>
				<!-- Play Store SVG icon -->
				<svg class="w-7 h-7 shrink-0" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path fill="#4CAF50" d="M142.5 21.5L333 256 142.5 490.5c-22.1-12.6-37.5-36.7-37.5-61.7V83.2c0-25 15.4-49.1 37.5-61.7z"/><path fill="#FFC107" d="M399.5 204L333 256l66.5 52v.2L449 279c14-8 23-23 23-39s-9-31-23-39l-49.5-29.2z"/><path fill="#FF3D00" d="M142.5 490.5L333 256l56.5 44.2-163 89.6c-24.5 13.5-52.2 14-84 .7z"/><path fill="#03A9F4" d="M142.5 21.5c31.8-13.3 59.5-12.9 84 .7l163 89.6L333 256 142.5 21.5z"/></svg>
				<div>
					<div class="text-xs text-slate-500 font-bold leading-none"><?php esc_html_e( 'GET IT ON', 'bigtricks' ); ?></div>
					<div class="text-base font-black leading-tight"><?php esc_html_e( 'Google Play', 'bigtricks' ); ?></div>
				</div>
				<i data-lucide="arrow-right" class="w-4 h-4 ml-auto text-slate-400 group-hover/btn:translate-x-1 transition-transform"></i>
			</a>
		</div>
	</div>

</aside>

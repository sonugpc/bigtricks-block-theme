<?php
/**
 * Widget: Download App
 * 
 * App download promotional widget with Play Store link
 * 
 * @package Bigtricks
 */

declare(strict_types=1);
?>

<!-- Download App Widget -->
<div class="bg-gradient-to-br from-slate-900 via-primary-950 to-slate-900 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden group">
	<div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_rgba(99,102,241,0.3),_transparent_70%)] pointer-events-none"></div>
	<div class="relative z-10">
		<div class="flex items-center gap-3 mb-4">
			<div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center border border-white/20 shrink-0">
				<i data-lucide="smartphone" class="w-6 h-6 text-primary-300"></i>
			</div>
			<div>
				<h2 class="font-black text-white text-lg leading-tight"><?php esc_html_e( 'Bigtricks App', 'bigtricks' ); ?></h2>
				<p class="text-primary-300 text-xs font-bold"><?php esc_html_e( 'Instant deal alerts on your phone!', 'bigtricks' ); ?></p>
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
				<span class="w-1.5 h-1.5 bg-primary-400 rounded-full shrink-0"></span>
				<?php echo esc_html( $feat ); ?>
			</li>
			<?php endforeach; ?>
		</ul>

		<!-- Play Store Button -->
		<a
			href="https://play.google.com/store/apps/details?id=in.bigtricks"
			target="_blank"
			rel="noopener noreferrer"
			class="flex items-center gap-3 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 font-black py-3.5 px-5 rounded-2xl shadow-lg hover:bg-primary-50 dark:hover:bg-slate-700 dark:shadow-slate-900/30 transition-all active:scale-95 group/btn"
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

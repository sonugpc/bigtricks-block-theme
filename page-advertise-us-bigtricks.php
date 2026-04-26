<?php
/**
 * Template Name: Advertise With Us
 * Template Post Type: page
 *
 * Matches page slug: advertise-us-bigtricks
 *
 * @package Bigtricks
 */

declare( strict_types=1 );

get_header();
?>

<main class="flex-1" id="main-content">

	<!-- ── Hero ─────────────────────────────────────────── -->
	<section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-primary-900 text-white py-20 md:py-28">
		<div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(ellipse at 60% 40%,#6366f1 0%,transparent 60%);"></div>
		<div class="relative max-w-4xl mx-auto px-4 text-center">
			<div class="inline-flex items-center gap-2 bg-primary-500/20 border border-primary-500/30 rounded-full px-4 py-1.5 text-sm font-bold mb-6">
				<i data-lucide="megaphone" class="w-4 h-4 text-primary-300"></i>
				<span class="text-primary-200">Reach India's Deal Hunters</span>
			</div>
			<h1 class="text-4xl md:text-6xl font-black leading-tight mb-5">
				Advertise on<br><span class="text-primary-400">BigTricks</span>
			</h1>
			<p class="text-lg md:text-xl text-white/75 max-w-2xl mx-auto mb-8">
				Connect your brand with India's most engaged deal-hunting community.
				From banner ads to sponsored posts — we have the right package for you.
			</p>
			<a href="#advertise-form" class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-400 text-white font-black px-8 py-4 rounded-2xl text-lg shadow-xl shadow-primary-900/40 transition-all hover:scale-[1.03] active:scale-[0.98]">
				<i data-lucide="send" class="w-5 h-5"></i>
				Get a Media Kit
			</a>
		</div>
	</section>

	<!-- ── Stats Bar ─────────────────────────────────────── -->
	<section class="bg-primary-600">
		<div class="max-w-[1200px] mx-auto px-4">
			<div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-primary-500">
				<?php
				$stats = [
				[ 'icon' => 'users',       'val' => 'Growing',  'label' => 'Active Community' ],
				[ 'icon' => 'send',        'val' => 'Active',   'label' => 'Telegram Channel' ],
				[ 'icon' => 'shield-check','val' => '100%',     'label' => 'Verified Content' ],
				[ 'icon' => 'target',      'val' => 'High',     'label' => 'Purchase Intent' ],
				];
				foreach ( $stats as $s ) : ?>
				<div class="py-7 px-6 text-center text-white">
					<i data-lucide="<?php echo esc_attr( $s['icon'] ); ?>" class="w-5 h-5 mx-auto mb-2 text-primary-200"></i>
					<div class="text-2xl md:text-3xl font-black"><?php echo esc_html( $s['val'] ); ?></div>
					<div class="text-sm text-primary-200 font-medium mt-0.5"><?php echo esc_html( $s['label'] ); ?></div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ── Ad Types ─────────────────────────────────────── -->
	<section class="max-w-[1200px] mx-auto px-4 py-16 md:py-20">
		<div class="text-center mb-12">
			<h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white mb-3">Advertising Options</h2>
			<p class="text-slate-500 dark:text-slate-400 max-w-xl mx-auto">Choose the format that best fits your campaign goals and budget.</p>
		</div>

		<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
			<?php
			$packages = [
				[
					'icon'   => 'layout-grid',
					'color'  => 'primary',
					'title'  => 'Banner Ads',
					'desc'   => 'High-visibility display banners on homepage, category pages, and deal detail pages. Multiple sizes available.',
					'badges' => [ 'Homepage', 'Sidebar', 'In-feed' ],
				],
				[
					'icon'   => 'file-text',
					'color'  => 'emerald',
					'title'  => 'Sponsored Posts',
					'desc'   => 'Native content that looks and reads like a regular BigTricks article — honest, editorial, and high-converting.',
					'badges' => [ 'SEO Indexed', 'Social Boost', 'Permanent' ],
				],
				[
					'icon'   => 'send',
					'color'  => 'blue',
					'title'  => 'Telegram Blasts',
					'desc'   => 'Direct promotional messages pushed to our 3.2L+ Telegram channel subscribers for maximum immediate reach.',
					'badges' => [ '3.2L+ Reach', 'Instant', 'High CTR' ],
				],
				[
					'icon'   => 'star',
					'color'  => 'yellow',
					'title'  => 'Product Reviews',
					'desc'   => 'Detailed, honest reviews of your product or service — published as editorial content with SEO value.',
					'badges' => [ 'SEO Rich', 'Long-form', 'Trust Building' ],
				],
				[
					'icon'   => 'link-2',
					'color'  => 'purple',
					'title'  => 'Affiliate Partnerships',
					'desc'   => 'Performance-based deals — pay only for sales or signups driven by our engaged audience.',
					'badges' => [ 'Pay on Results', 'Tracked', 'ROI Focused' ],
				],
				[
					'icon'   => 'mail',
					'color'  => 'orange',
					'title'  => 'Newsletter Sponsorship',
					'desc'   => 'Sponsored slot in our weekly newsletter delivered to thousands of deal-savvy subscribers.',
					'badges' => [ 'Email', 'Weekly', 'Curated Audience' ],
				],
			];

			$pkg_colors = [
				'primary' => [ 'icon' => 'bg-primary-100 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400', 'badge' => 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-300' ],
				'emerald' => [ 'icon' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400', 'badge' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300' ],
				'blue'    => [ 'icon' => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400', 'badge' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300' ],
				'yellow'  => [ 'icon' => 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400', 'badge' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-900/20 dark:text-yellow-300' ],
				'purple'  => [ 'icon' => 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400', 'badge' => 'bg-purple-50 text-purple-700 dark:bg-purple-900/20 dark:text-purple-300' ],
				'orange'  => [ 'icon' => 'bg-orange-100 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400', 'badge' => 'bg-orange-50 text-orange-700 dark:bg-orange-900/20 dark:text-orange-300' ],
			];

			foreach ( $packages as $pkg ) :
				$clr = $pkg_colors[ $pkg['color'] ];
			?>
			<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl p-6 shadow-sm hover:shadow-md hover:border-primary-200 dark:hover:border-primary-800 transition-all">
				<div class="w-12 h-12 rounded-2xl flex items-center justify-center mb-4 <?php echo esc_attr( $clr['icon'] ); ?>">
					<i data-lucide="<?php echo esc_attr( $pkg['icon'] ); ?>" class="w-5 h-5"></i>
				</div>
				<h3 class="text-lg font-black text-slate-900 dark:text-white mb-2"><?php echo esc_html( $pkg['title'] ); ?></h3>
				<p class="text-sm text-slate-500 dark:text-slate-400 mb-4 leading-relaxed"><?php echo esc_html( $pkg['desc'] ); ?></p>
				<div class="flex flex-wrap gap-1.5">
					<?php foreach ( $pkg['badges'] as $badge ) : ?>
					<span class="text-xs font-bold px-2.5 py-1 rounded-full <?php echo esc_attr( $clr['badge'] ); ?>"><?php echo esc_html( $badge ); ?></span>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- ── Why Advertise ────────────────────────────────── -->
	<section class="bg-slate-50 dark:bg-slate-950/50 border-y border-slate-200 dark:border-slate-800 py-16">
		<div class="max-w-[1200px] mx-auto px-4">
			<div class="text-center mb-10">
				<h2 class="text-3xl font-black text-slate-900 dark:text-white mb-2">Why BigTricks?</h2>
				<p class="text-slate-500 dark:text-slate-400">Our audience comes to <em>buy</em>, not just to browse.</p>
			</div>
			<div class="grid md:grid-cols-3 gap-6">
				<?php
				$whys = [
					[ 'icon' => 'target',        'title' => 'Intent-Driven Audience',  'desc' => 'Our readers are actively looking for the best deals — they arrive ready to click and convert.' ],
					[ 'icon' => 'shield-check',   'title' => 'Brand-Safe Environment', 'desc' => 'Every deal is hand-curated. Your ad sits next to quality, verified content — not spam.' ],
					[ 'icon' => 'bar-chart-2',   'title' => 'Transparent Reporting',  'desc' => 'Full campaign reports with impressions, clicks, CTR, and conversion data. No black boxes.' ],
				];
				foreach ( $whys as $w ) : ?>
				<div class="flex gap-4 items-start p-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
					<div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-xl flex items-center justify-center shrink-0">
						<i data-lucide="<?php echo esc_attr( $w['icon'] ); ?>" class="w-5 h-5"></i>
					</div>
					<div>
						<h3 class="font-black text-slate-900 dark:text-white text-sm mb-1"><?php echo esc_html( $w['title'] ); ?></h3>
						<p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed"><?php echo esc_html( $w['desc'] ); ?></p>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ── Advertise Form ───────────────────────────────── -->
	<section id="advertise-form" class="max-w-[900px] mx-auto px-4 py-16 md:py-20">
		<div class="text-center mb-10">
			<h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white mb-3">Request a Media Kit</h2>
			<p class="text-slate-500 dark:text-slate-400 max-w-lg mx-auto">Fill in the form and our team will send you our rate card and available slots within 24 hours.</p>
		</div>
		<?php echo do_shortcode( '[bigtricks_advertise_form title="" description=""]' ); ?>
	</section>

</main>

<?php get_footer(); ?>

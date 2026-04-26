<?php
/**
 * Template Name: About Us
 * Template Post Type: page
 *
 * Matches page slug: about or about-us
 *
 * @package Bigtricks
 */

declare( strict_types=1 );

get_header();
// Dynamic array icons — keep in sync with PHP arrays below:
// data-lucide="flag"
?>

<main class="flex-1" id="main-content">

	<!-- ── Hero ─────────────────────────────────────────── -->
	<section class="relative overflow-hidden bg-gradient-to-br from-primary-50 via-white to-indigo-50 dark:from-slate-900 dark:via-slate-900 dark:to-primary-950 pt-16 pb-20 md:pt-24 md:pb-28 border-b border-slate-200 dark:border-slate-800">
		<div class="absolute -top-32 -right-32 w-96 h-96 bg-primary-100 dark:bg-primary-900/20 rounded-full blur-3xl opacity-60"></div>
		<div class="absolute -bottom-20 -left-20 w-72 h-72 bg-indigo-100 dark:bg-indigo-900/20 rounded-full blur-3xl opacity-50"></div>
		<div class="relative max-w-3xl mx-auto px-4 text-center">
			<div class="inline-flex items-center gap-2 bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 rounded-full px-4 py-1.5 text-sm font-bold mb-6">
				<i data-lucide="zap" class="w-4 h-4"></i>
				India's #1 Deals Community
			</div>
			<h1 class="text-4xl md:text-6xl font-black text-slate-900 dark:text-white leading-tight mb-5">
				We Help India<br><span class="text-primary-600">Save Smarter</span>
			</h1>
			<p class="text-lg md:text-xl text-slate-500 dark:text-slate-400 max-w-xl mx-auto">
				BigTricks is a community-first platform where real people share the best deals, referral codes, credit card offers, and cashback opportunities — before they disappear.
			</p>
		</div>
	</section>

	<!-- ── Stats ────────────────────────────────────────── -->
	<section class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
		<div class="max-w-[1100px] mx-auto px-4">
			<div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-slate-200 dark:divide-slate-800">
				<?php
				$stats = [
					[ 'val' => 'Since 2015', 'label' => 'Trusted for 10+ Years',  'icon' => 'calendar' ],
					[ 'val' => '100%',        'label' => 'Verified Deals Only',    'icon' => 'shield-check' ],
					[ 'val' => 'Daily',       'label' => 'Fresh Deals & Offers',   'icon' => 'zap' ],
					[ 'val' => 'Free',        'label' => 'Always Free to Join',    'icon' => 'heart' ],
				];
				foreach ( $stats as $s ) : ?>
				<div class="py-8 px-6 text-center">
					<i data-lucide="<?php echo esc_attr( $s['icon'] ); ?>" class="w-5 h-5 mx-auto mb-2 text-primary-500"></i>
					<div class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white"><?php echo esc_html( $s['val'] ); ?></div>
					<div class="text-sm text-slate-400 mt-0.5"><?php echo esc_html( $s['label'] ); ?></div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ── Our Story ────────────────────────────────────── -->
	<section class="max-w-[1100px] mx-auto px-4 py-16 md:py-20 grid md:grid-cols-2 gap-12 items-center">
		<div>
			<span class="text-xs font-black uppercase tracking-widest text-primary-600 dark:text-primary-400">Our Story</span>
			<h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white mt-2 mb-5">
				Started With a Clear Mission
			</h2>
			<div class="space-y-4 text-slate-600 dark:text-slate-400 leading-relaxed">
				<p>
					Bigtricks.in was started in August 2015 with one clear mission: to help people save their hard-earned money through amazing deals, shopping tricks, and exclusive offers.
				</p>
				<p>
					We provide the latest shopping offers, best deals, refer &amp; earn opportunities, and much more — helping you maximise your savings every single day.
				</p>
				<p>
					Every new post on Bigtricks comes with a chance to earn PayTM cash. We run regular cashback offers and exclusive promotions to reward our loyal followers.
				</p>
			</div>
		</div>
		<div class="grid grid-cols-2 gap-4">
			<?php
			$milestones = [
				[ 'year' => '2015', 'event' => 'Bigtricks.in launched in August with a mission to help India save money', 'icon' => 'flag', 'color' => 'primary' ],
				[ 'year' => '2016', 'event' => 'Launched Telegram channel for instant deal alerts and referral codes', 'icon' => 'send', 'color' => 'blue' ],
				[ 'year' => '2019', 'event' => 'Expanded with credit card reviews and exclusive cashback sections', 'icon' => 'credit-card', 'color' => 'emerald' ],
				[ 'year' => '2024', 'event' => 'Growing community of deal hunters and smart savers across India', 'icon' => 'trending-up', 'color' => 'purple' ],
			];
			$ms_colors = [
				'primary' => 'bg-primary-100 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400',
				'blue'    => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
				'emerald' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
				'purple'  => 'bg-purple-100 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
			];
			foreach ( $milestones as $ms ) : ?>
			<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm">
				<div class="w-9 h-9 rounded-xl flex items-center justify-center mb-3 <?php echo esc_attr( $ms_colors[ $ms['color'] ] ); ?>">
					<i data-lucide="<?php echo esc_attr( $ms['icon'] ); ?>" class="w-4 h-4"></i>
				</div>
				<span class="text-xs font-black text-primary-600 dark:text-primary-400"><?php echo esc_html( $ms['year'] ); ?></span>
				<p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed"><?php echo esc_html( $ms['event'] ); ?></p>
			</div>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- ── What We Cover ────────────────────────────────── -->
	<section class="bg-slate-50 dark:bg-slate-950/50 border-y border-slate-200 dark:border-slate-800 py-16">
		<div class="max-w-[1100px] mx-auto px-4">
			<div class="text-center mb-10">
				<h2 class="text-3xl font-black text-slate-900 dark:text-white mb-2">What We Cover</h2>
				<p class="text-slate-500 dark:text-slate-400 max-w-lg mx-auto text-sm">From loot deals to premium credit cards — if it saves you money, you'll find it here.</p>
			</div>
			<div class="grid grid-cols-2 md:grid-cols-4 gap-5">
				<?php
				$categories = [
					[ 'icon' => 'tag',          'title' => 'Loot Deals',       'desc' => 'Crazy-low prices that won\'t last long' ],
					[ 'icon' => 'credit-card',   'title' => 'Credit Cards',     'desc' => 'Best cashback & reward card reviews' ],
					[ 'icon' => 'share-2',       'title' => 'Referral Codes',   'desc' => 'Earn cash by sharing referral links' ],
					[ 'icon' => 'percent',       'title' => 'Cashback Offers',  'desc' => 'Bank, app, and wallet cashback deals' ],
				];
				foreach ( $categories as $cat ) : ?>
				<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 text-center shadow-sm hover:shadow-md transition-shadow">
					<div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-xl flex items-center justify-center mx-auto mb-3">
						<i data-lucide="<?php echo esc_attr( $cat['icon'] ); ?>" class="w-5 h-5"></i>
					</div>
					<h3 class="font-black text-slate-900 dark:text-white text-sm mb-1"><?php echo esc_html( $cat['title'] ); ?></h3>
					<p class="text-xs text-slate-400 leading-relaxed"><?php echo esc_html( $cat['desc'] ); ?></p>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ── Values ───────────────────────────────────────── -->
	<section class="max-w-[1100px] mx-auto px-4 py-16 md:py-20">
		<div class="text-center mb-10">
			<h2 class="text-3xl font-black text-slate-900 dark:text-white mb-2">Our Values</h2>
			<p class="text-slate-500 dark:text-slate-400 text-sm max-w-lg mx-auto">These aren't slogans. They're the reason 5 lakh people choose BigTricks every month.</p>
		</div>
		<div class="grid md:grid-cols-3 gap-6">
			<?php
			$values = [
				[ 'icon' => 'shield-check', 'title' => 'Honesty First',    'desc' => 'We never publish a deal we haven\'t verified. If it\'s expired or fake, it gets removed immediately.' ],
				[ 'icon' => 'users',         'title' => 'Community Driven', 'desc' => 'The best deals come from our readers. We\'re a platform, not just a blog — everyone can contribute.' ],
				[ 'icon' => 'zap',           'title' => 'Speed Matters',    'desc' => 'Flash deals die fast. Our team monitors offers around the clock so you always hear first.' ],
			];
			foreach ( $values as $v ) : ?>
			<div class="relative bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl p-8 shadow-sm overflow-hidden">
				<div class="absolute top-0 right-0 w-32 h-32 bg-primary-50 dark:bg-primary-900/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
				<div class="relative">
					<div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-2xl flex items-center justify-center mb-5">
						<i data-lucide="<?php echo esc_attr( $v['icon'] ); ?>" class="w-6 h-6"></i>
					</div>
					<h3 class="text-xl font-black text-slate-900 dark:text-white mb-2"><?php echo esc_html( $v['title'] ); ?></h3>
					<p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed"><?php echo esc_html( $v['desc'] ); ?></p>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- ── Testimonials ────────────────────────────────── -->
	<section class="bg-slate-50 dark:bg-slate-950/50 border-y border-slate-200 dark:border-slate-800 py-16">
		<div class="max-w-[1100px] mx-auto px-4">
			<div class="text-center mb-10">
				<span class="text-xs font-black uppercase tracking-widest text-primary-600 dark:text-primary-400"><?php esc_html_e( 'Real Wins', 'bigtricks' ); ?></span>
				<h2 class="text-3xl font-black text-slate-900 dark:text-white mt-2 mb-2"><?php esc_html_e( 'What Our Community Is Saying', 'bigtricks' ); ?></h2>
				<p class="text-slate-500 dark:text-slate-400 text-sm max-w-lg mx-auto"><?php esc_html_e( 'Real savings from real people — these are the moments that keep us going.', 'bigtricks' ); ?></p>
			</div>
			<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
				<?php
				$testimonials = [
					[ 'quote' => 'Nice Loot Deal, Got AC for just ₹5699', 'emoji' => '❄️' ],
					[ 'quote' => 'Micromax Phone for just ₹1599', 'emoji' => '📱' ],
					[ 'quote' => 'Paytm Cashback of ₹1500 — unbelievable!', 'emoji' => '💸' ],
					[ 'quote' => 'Got a Free iPhone from the Maggi offer!', 'emoji' => '🎉' ],
				];
				foreach ( $testimonials as $t ) : ?>
				<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-sm flex flex-col gap-4">
					<span class="text-3xl"><?php echo esc_html( $t['emoji'] ); ?></span>
					<p class="text-slate-700 dark:text-slate-300 font-bold text-sm leading-relaxed flex-1">&ldquo;<?php echo esc_html( $t['quote'] ); ?>&rdquo;</p>
					<div class="flex gap-0.5">
						<?php for ( $i = 0; $i < 5; $i++ ) : ?>
						<i data-lucide="star" class="w-3.5 h-3.5 text-amber-400 fill-amber-400"></i>
						<?php endfor; ?>
					</div>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ── CTA ──────────────────────────────────────────── -->
	<section class="bg-primary-600 py-16 text-white text-center">
		<div class="max-w-2xl mx-auto px-4">
			<h2 class="text-3xl md:text-4xl font-black mb-4">Join the BigTricks Community</h2>
			<p class="text-primary-200 mb-8"><?php esc_html_e( 'Get the best deals straight to your Telegram — before they\'re gone.', 'bigtricks' ); ?></p>
			<div class="flex flex-wrap gap-4 justify-center">
				<a href="https://t.me/bigtricks" target="_blank" rel="noopener noreferrer"
				   class="inline-flex items-center gap-2 bg-white text-primary-700 font-black px-7 py-3.5 rounded-2xl hover:bg-primary-50 transition-colors shadow-lg">
					<i data-lucide="send" class="w-5 h-5"></i>
					Join Telegram
				</a>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"
				   class="inline-flex items-center gap-2 border-2 border-white/40 text-white font-black px-7 py-3.5 rounded-2xl hover:bg-white/10 transition-colors">
					<i data-lucide="mail" class="w-5 h-5"></i>
					Contact Us
				</a>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>

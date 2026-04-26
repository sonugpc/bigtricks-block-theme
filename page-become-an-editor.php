<?php
/**
 * Template Name: Become an Editor
 * Template Post Type: page
 *
 * Matches page slug: become-an-editor
 *
 * @package Bigtricks
 */

declare( strict_types=1 );

get_header();
// Dynamic array icons — keep in sync with PHP arrays below:
// data-lucide="edit-3" data-lucide="award" data-lucide="bar-chart-2"
?>

<main class="flex-1" id="main-content">

	<!-- ── Hero ─────────────────────────────────────────── -->
	<section class="relative overflow-hidden bg-gradient-to-br from-emerald-600 via-teal-700 to-primary-800 text-white py-20 md:py-28">
		<div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(ellipse at 30% 60%,#fff 0%,transparent 55%);"></div>
		<div class="relative max-w-3xl mx-auto px-4 text-center">
			<div class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-sm border border-white/20 rounded-full px-4 py-1.5 text-sm font-bold mb-6">
				<i data-lucide="pen-line" class="w-4 h-4"></i>
				We're Looking for Deal Hunters
			</div>
			<h1 class="text-4xl md:text-6xl font-black leading-tight mb-5">
				Become a<br><span class="text-emerald-200">BigTricks Editor</span>
			</h1>
			<p class="text-lg md:text-xl text-white/80 max-w-xl mx-auto mb-8">
				Love finding deals? Turn your passion into a platform. Help thousands of people save money every day — and earn recognition for it.
			</p>
			<a href="#editor-form" class="inline-flex items-center gap-2 bg-white text-emerald-700 font-black px-8 py-4 rounded-2xl text-lg shadow-xl hover:bg-emerald-50 transition-colors hover:scale-[1.03] active:scale-[0.98]">
				<i data-lucide="send" class="w-5 h-5"></i>
				Apply Now
			</a>
		</div>
	</section>

	<!-- ── What Is an Editor ────────────────────────────── -->
	<section class="max-w-[1100px] mx-auto px-4 py-16 md:py-20 grid md:grid-cols-2 gap-14 items-center">
		<div>
			<span class="text-xs font-black uppercase tracking-widest text-emerald-600 dark:text-emerald-400">The Role</span>
			<h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white mt-2 mb-5">What Does an Editor Do?</h2>
			<div class="space-y-4 text-slate-600 dark:text-slate-400 text-sm leading-relaxed">
				<p>A BigTricks editor is the backbone of the platform. You're the person who spots a deal before anyone else, verifies it's real, and publishes it so thousands of readers can act on it before it expires.</p>
				<p>Editors also write short credit card reviews, post referral codes, and occasionally contribute tips and guides. There's no rigid quota — you publish when you have something genuinely worth sharing.</p>
				<p>It's flexible, rewarding, and a great way to build your personal brand in the deal and personal finance space.</p>
			</div>
		</div>
		<div class="space-y-3">
			<?php
			$tasks = [
				[ 'icon' => 'search',       'title' => 'Hunt for Deals',        'desc' => 'Spot loot deals, cashback offers, and time-limited offers across platforms.' ],
				[ 'icon' => 'shield-check', 'title' => 'Verify Before Posting',  'desc' => 'Confirm the deal actually works — price, coupon code, expiry — before publishing.' ],
				[ 'icon' => 'edit-3',       'title' => 'Write Deal Posts',       'desc' => 'Write a short, clear description using our simple template. No journalism degree needed.' ],
				[ 'icon' => 'send',         'title' => 'Share on Telegram',      'desc' => 'Coordinate with the team to push top deals to our 3L+ Telegram community.' ],
			];
			foreach ( $tasks as $t ) : ?>
			<div class="flex gap-4 items-start bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 shadow-sm">
				<div class="w-9 h-9 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
					<i data-lucide="<?php echo esc_attr( $t['icon'] ); ?>" class="w-4 h-4"></i>
				</div>
				<div>
					<p class="font-black text-slate-900 dark:text-white text-sm"><?php echo esc_html( $t['title'] ); ?></p>
					<p class="text-xs text-slate-400 mt-0.5 leading-relaxed"><?php echo esc_html( $t['desc'] ); ?></p>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	</section>

	<!-- ── Benefits ─────────────────────────────────────── -->
	<section class="bg-slate-50 dark:bg-slate-950/50 border-y border-slate-200 dark:border-slate-800 py-16">
		<div class="max-w-[1100px] mx-auto px-4">
			<div class="text-center mb-10">
				<h2 class="text-3xl font-black text-slate-900 dark:text-white mb-2">What You Get</h2>
				<p class="text-slate-500 dark:text-slate-400 text-sm">Beyond the satisfaction of helping people save, there are real perks.</p>
			</div>
			<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
				<?php
				$benefits = [
					[ 'icon' => 'user-check',    'color' => 'primary', 'title' => 'Author Byline',         'desc' => 'Your name and bio on every post you publish — build your personal brand.' ],
					[ 'icon' => 'award',          'color' => 'yellow',  'title' => 'Editor Badge',          'desc' => 'Exclusive \'Verified Editor\' badge on your profile and contributor page.' ],
					[ 'icon' => 'bar-chart-2',   'color' => 'blue',    'title' => 'Performance Dashboard', 'desc' => 'See real-time stats on your posts — views, saves, and deal clicks.' ],
					[ 'icon' => 'send',           'color' => 'emerald', 'title' => 'Telegram Access',       'desc' => 'Join our private editors\' Telegram group for early deals and team discussions.' ],
					[ 'icon' => 'gift',           'color' => 'pink',    'title' => 'Monthly Rewards',       'desc' => 'Top editors receive gift vouchers and exclusive cashback offers each month.' ],
					[ 'icon' => 'linkedin',       'color' => 'blue',    'title' => 'Portfolio Credit',      'desc' => 'Your published articles count as professional portfolio work in finance/deals.' ],
				];
				$benefit_colors = [
					'primary' => 'bg-primary-100 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400',
					'yellow'  => 'bg-yellow-100 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400',
					'blue'    => 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
					'emerald' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400',
					'pink'    => 'bg-pink-100 text-pink-600 dark:bg-pink-900/30 dark:text-pink-400',
				];
				foreach ( $benefits as $b ) : ?>
				<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-2xl p-5 shadow-sm">
					<div class="w-10 h-10 rounded-xl flex items-center justify-center mb-4 <?php echo esc_attr( $benefit_colors[ $b['color'] ] ); ?>">
						<i data-lucide="<?php echo esc_attr( $b['icon'] ); ?>" class="w-5 h-5"></i>
					</div>
					<h3 class="font-black text-slate-900 dark:text-white text-sm mb-1"><?php echo esc_html( $b['title'] ); ?></h3>
					<p class="text-xs text-slate-400 leading-relaxed"><?php echo esc_html( $b['desc'] ); ?></p>
				</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<!-- ── Requirements ─────────────────────────────────── -->
	<section class="max-w-[900px] mx-auto px-4 py-16 md:py-20">
		<div class="grid md:grid-cols-2 gap-8">
			<!-- Must have -->
			<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl p-7 shadow-sm">
				<div class="flex items-center gap-3 mb-5">
					<div class="w-9 h-9 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-xl flex items-center justify-center">
						<i data-lucide="check-circle-2" class="w-5 h-5"></i>
					</div>
					<h3 class="text-lg font-black text-slate-900 dark:text-white">You Need</h3>
				</div>
				<ul class="space-y-2.5">
					<?php
					$needs = [
						'Passion for deals and saving money',
						'Basic written English or Hindi (we edit, not judge)',
						'A smartphone and internet access',
						'At least 5 hours per week to contribute',
						'Attention to detail when verifying deals',
					];
					foreach ( $needs as $n ) : ?>
					<li class="flex items-start gap-2.5 text-sm text-slate-600 dark:text-slate-400">
						<i data-lucide="check" class="w-4 h-4 text-emerald-500 mt-0.5 shrink-0"></i>
						<?php echo esc_html( $n ); ?>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<!-- Nice to have -->
			<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl p-7 shadow-sm">
				<div class="flex items-center gap-3 mb-5">
					<div class="w-9 h-9 bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 rounded-xl flex items-center justify-center">
						<i data-lucide="star" class="w-5 h-5"></i>
					</div>
					<h3 class="text-lg font-black text-slate-900 dark:text-white">Bonus Points</h3>
				</div>
				<ul class="space-y-2.5">
					<?php
					$bonus = [
						'Experience with credit cards or personal finance',
						'Active on deal communities (Telegram, Reddit, etc.)',
						'Can write in both English and Hindi',
						'Background in content writing or blogging',
						'Existing follower base in deals or finance niche',
					];
					foreach ( $bonus as $b ) : ?>
					<li class="flex items-start gap-2.5 text-sm text-slate-600 dark:text-slate-400">
						<i data-lucide="star" class="w-3.5 h-3.5 text-primary-400 mt-1 shrink-0"></i>
						<?php echo esc_html( $b ); ?>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	</section>

	<!-- ── Application Form ─────────────────────────────── -->
	<section id="editor-form" class="bg-slate-50 dark:bg-slate-950/50 border-t border-slate-200 dark:border-slate-800 py-16 md:py-20">
		<div class="max-w-[820px] mx-auto px-4">
			<div class="text-center mb-10">
				<h2 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white mb-3">Apply to Become an Editor</h2>
				<p class="text-slate-500 dark:text-slate-400 max-w-lg mx-auto">We review applications every week. Selected candidates will be invited to a short onboarding call.</p>
			</div>
			<div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-3xl p-8 shadow-sm">
				<?php
				echo do_shortcode(
					'[bigtricks_contact_form title="Apply to Become an Editor" description="Tell us about yourself and why you\'d make a great BigTricks editor." button_text="Submit Application"]'
				);
				?>
				<p class="text-xs text-slate-400 text-center mt-4">
					In the message field, tell us: <strong class="text-slate-500">your deal-hunting experience, favourite categories, and links to any previous writing or social profiles.</strong>
				</p>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>

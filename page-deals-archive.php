<?php
/**
 * Template Name: Deals Archive Template
 * Description: Modern deals archive page with filters, Telegram feed, categories, stores, and CTAs.
 *
 * @package Bigtricks
 */

if ( ! function_exists( 'bigtricks_render_loot_filter_fields' ) ) {
	/**
	 * Render Loot Deals filter fields.
	 *
	 * @param WP_Term[] $stores     Store terms.
	 * @param WP_Term[] $categories Loot child categories.
	 * @param string    $prefix     Unique ID prefix.
	 */
	function bigtricks_render_loot_filter_fields( array $stores, array $categories, string $prefix ): void {
		?>
		<div class="space-y-5">
			<div>
				<label for="<?php echo esc_attr( $prefix ); ?>-search" class="mb-1.5 block text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-300"><?php esc_html_e( 'Search', 'bigtricks' ); ?></label>
				<input id="<?php echo esc_attr( $prefix ); ?>-search" type="search" name="search" placeholder="Search deals" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:border-primary-400 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-400">
			</div>

			<div>
				<label for="<?php echo esc_attr( $prefix ); ?>-store" class="mb-1.5 block text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-300"><?php esc_html_e( 'Store', 'bigtricks' ); ?></label>
				<select id="<?php echo esc_attr( $prefix ); ?>-store" name="store" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 focus:border-primary-400 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100">
					<option value=""><?php esc_html_e( 'All Stores', 'bigtricks' ); ?></option>
					<?php foreach ( $stores as $store ) : ?>
						<option value="<?php echo esc_attr( $store->term_id ); ?>"><?php echo esc_html( $store->name ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="grid grid-cols-2 gap-3">
				<div>
					<label for="<?php echo esc_attr( $prefix ); ?>-min-price" class="mb-1.5 block text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-300"><?php esc_html_e( 'Min Price', 'bigtricks' ); ?></label>
					<input id="<?php echo esc_attr( $prefix ); ?>-min-price" type="number" min="0" name="min_price" placeholder="0" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:border-primary-400 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-400">
				</div>
				<div>
					<label for="<?php echo esc_attr( $prefix ); ?>-max-price" class="mb-1.5 block text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-300"><?php esc_html_e( 'Max Price', 'bigtricks' ); ?></label>
					<input id="<?php echo esc_attr( $prefix ); ?>-max-price" type="number" min="0" name="max_price" placeholder="999999" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:border-primary-400 focus:outline-none dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:placeholder:text-slate-400">
				</div>
			</div>

			<div>
				<p class="mb-1.5 block text-xs font-black uppercase tracking-wide text-slate-500 dark:text-slate-300"><?php esc_html_e( 'Categories', 'bigtricks' ); ?></p>
				<div class="max-h-56 space-y-2 overflow-y-auto rounded-xl border border-slate-200 bg-white p-3 dark:border-slate-600 dark:bg-slate-800">
					<?php foreach ( $categories as $category ) : ?>
						<label class="flex cursor-pointer items-center gap-2.5 rounded-lg px-2 py-1.5 hover:bg-slate-50 dark:hover:bg-slate-700">
							<input type="checkbox" name="categories[]" value="<?php echo esc_attr( $category->term_id ); ?>" class="h-4 w-4 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
							<span class="text-sm font-semibold text-slate-700 dark:text-slate-100"><?php echo esc_html( $category->name ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	}
}

get_header();

$total_deals = (int) wp_count_posts( 'deal' )->publish;

$archive_description = get_the_archive_description();

$loot_deals_parent = get_term_by( 'slug', 'loot-deals', 'category' );
$parent_id = $loot_deals_parent ? (int) $loot_deals_parent->term_id : 0;

$categories = get_terms( [
	'taxonomy'   => 'category',
	'hide_empty' => true,
	'number'     => 0,
	'parent'     => $parent_id,
] );

$stores = get_terms( [
	'taxonomy'   => 'store',
	'hide_empty' => true,
	'number'     => 0,
] );

$popular_stores = get_terms( [
	'taxonomy'   => 'store',
	'hide_empty' => true,
	'number'     => 5,
	'orderby'    => 'count',
	'order'      => 'DESC',
] );

$loot_category_ids = function_exists( 'bigtricks_get_loot_deals_category_ids' )
	? bigtricks_get_loot_deals_category_ids()
	: [];

$loot_query_args = [
	'post_type'      => 'deal',
	'post_status'    => 'publish',
	'posts_per_page' => 12,
	'paged'          => 1,
	'orderby'        => 'date',
	'order'          => 'DESC',
];

$loot_query      = new WP_Query( $loot_query_args );
$loot_total      = (int) $loot_query->found_posts;
$loot_max_pages  = (int) $loot_query->max_num_pages;
$loot_has_more   = $loot_max_pages > 1;
$loot_ajax_nonce = wp_create_nonce( 'bigtricks_load_more' );

?>

<main class="w-screen max-w-[1400px] mx-auto overflow-x-hidden px-4 py-6 md:py-8 space-y-8 md:space-y-10" id="main-content">

	<section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-primary-700 via-primary-600 to-indigo-500 text-white shadow-lg">
		<div class="absolute -top-16 -right-12 h-44 w-44 rounded-full bg-white/10 blur-3xl sm:-top-20 sm:-right-12 sm:h-60 sm:w-60" aria-hidden="true"></div>
		<div class="absolute -bottom-16 -left-16 h-44 w-44 rounded-full bg-indigo-200/20 blur-3xl sm:-bottom-20 sm:-left-20 sm:h-60 sm:w-60" aria-hidden="true"></div>

		<div class="relative p-4 sm:p-7 lg:p-12">
			<div class="max-w-3xl">
				<h1 class="text-2xl sm:text-4xl lg:text-5xl font-black leading-tight text-white">
					Best Loot Deals in India: Daily Online Offers, Coupons &amp; Price Drops
				</h1>
				<p class="mt-3 text-sm sm:text-lg text-white/90 leading-relaxed">
					Discover verified loot deals from top stores, including bank offers, flash sales, cashback coupons, and limited-time online shopping discounts.
				</p>

				<div class="mt-4 grid grid-cols-3 gap-2.5 sm:gap-4">
					<div class="rounded-2xl bg-white/10 border border-white/15 px-4 py-3 backdrop-blur-sm">
						<p class="text-xl sm:text-2xl font-black leading-none"><?php echo esc_html( number_format_i18n( $total_deals ) ); ?>+</p>
						<p class="mt-1 text-[10px] sm:text-xs font-semibold uppercase tracking-wide text-white/80">Active Deals</p>
					</div>
					<div class="rounded-2xl bg-white/10 border border-white/15 px-4 py-3 backdrop-blur-sm">
						<p class="text-xl sm:text-2xl font-black leading-none">50+</p>
						<p class="mt-1 text-[10px] sm:text-xs font-semibold uppercase tracking-wide text-white/80">Top Stores</p>
					</div>
					<div class="rounded-2xl bg-white/10 border border-white/15 px-4 py-3 backdrop-blur-sm">
						<p class="text-xl sm:text-2xl font-black leading-none">24/7</p>
						<p class="mt-1 text-[10px] sm:text-xs font-semibold uppercase tracking-wide text-white/80">Deal Updates</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden dark:border-slate-700 dark:bg-slate-900">
		<div class="border-b border-slate-200 bg-slate-50/70 px-6 py-5 md:px-8 md:py-6 dark:border-slate-700 dark:bg-slate-800/70">
			<h2 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">Browse All Loot Deals</h2>
			<p class="mt-2 text-slate-600 dark:text-slate-300">Filter by store and category to find the best loot deals for your shopping list.</p>
		</div>

		<div class="grid grid-cols-1 xl:grid-cols-[280px_minmax(0,1fr)] gap-6 p-6 md:p-8">
			<aside class="hidden xl:block rounded-2xl border border-slate-200 bg-slate-50/60 p-4 md:p-5 dark:border-slate-700 dark:bg-slate-800/60">
				<form id="bt-loot-filter-form-desktop" class="space-y-5">
					<?php bigtricks_render_loot_filter_fields( is_array( $stores ) ? $stores : [], is_array( $categories ) ? $categories : [], 'desktop' ); ?>
					<div class="grid grid-cols-2 gap-3 pt-1">
						<button type="button" id="bt-loot-clear-desktop" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-black text-slate-700 transition hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
							<?php esc_html_e( 'Clear', 'bigtricks' ); ?>
						</button>
						<button type="button" id="bt-loot-apply-desktop" class="rounded-xl bg-primary-600 px-3 py-2 text-sm font-black text-white transition hover:bg-primary-700">
							<?php esc_html_e( 'Apply', 'bigtricks' ); ?>
						</button>
					</div>
				</form>
			</aside>

			<div class="min-w-0">
				<div class="mb-4 flex items-center justify-between xl:hidden">
					<button type="button" id="bt-open-loot-filters" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-black text-slate-700 shadow-sm hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
						<i data-lucide="sliders-horizontal" class="h-4 w-4"></i>
						<?php esc_html_e( 'Filters', 'bigtricks' ); ?>
					</button>
					<span class="text-xs font-semibold text-slate-500 dark:text-slate-400"><?php esc_html_e( 'App-style drawer filters', 'bigtricks' ); ?></span>
				</div>

				<div id="bt-loot-feed" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
					<?php if ( $loot_query->have_posts() ) : ?>
						<?php
						while ( $loot_query->have_posts() ) {
							$loot_query->the_post();
							get_template_part( 'template-parts/card-deal-loot-grid', null, [ 'post_id' => get_the_ID() ] );
						}
						wp_reset_postdata();
						?>
					<?php endif; ?>
				</div>

				<div id="bt-loot-empty" class="<?php echo $loot_total > 0 ? 'hidden' : ''; ?> mt-5 rounded-2xl border border-slate-200 bg-slate-50 p-8 text-center dark:border-slate-700 dark:bg-slate-800/60">
					<i data-lucide="search-x" class="mx-auto mb-3 h-8 w-8 text-slate-400"></i>
					<p class="text-sm font-semibold text-slate-500 dark:text-slate-300"><?php esc_html_e( 'No loot deals found for the selected filters.', 'bigtricks' ); ?></p>
				</div>

				<div id="bt-loot-load-wrap" class="mt-6 flex justify-center <?php echo $loot_has_more ? '' : 'hidden'; ?>">
					<button
						type="button"
						id="bt-loot-load-more"
						class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-black text-white hover:bg-primary-700"
						data-page="1"
						data-nonce="<?php echo esc_attr( $loot_ajax_nonce ); ?>"
					>
						<i data-lucide="refresh-cw" class="h-4 w-4"></i>
						<?php esc_html_e( 'Load More', 'bigtricks' ); ?>
					</button>
				</div>
				<div id="bt-loot-sentinel" class="h-1"></div>
			</div>
		</div>
	</section>

	<div id="bt-loot-filter-drawer" class="fixed inset-0 z-[90] hidden xl:hidden" aria-hidden="true">
		<div id="bt-loot-filter-overlay" class="absolute inset-0 bg-slate-900/50"></div>
		<div id="bt-loot-filter-panel" class="absolute inset-x-0 bottom-0 max-h-[86vh] translate-y-full rounded-t-3xl bg-white shadow-2xl transition-transform duration-300 dark:bg-slate-900">
			<div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 dark:border-slate-700">
				<h3 class="text-base font-black text-slate-900 dark:text-white"><?php esc_html_e( 'Filter Loot Deals', 'bigtricks' ); ?></h3>
				<button type="button" id="bt-close-loot-filters" class="rounded-lg border border-slate-200 p-2 text-slate-500 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">
					<i data-lucide="x" class="h-4 w-4"></i>
				</button>
			</div>

			<form id="bt-loot-filter-form-mobile" class="flex h-full flex-col">
				<div class="flex-1 overflow-y-auto px-5 py-4 pb-24">
					<?php bigtricks_render_loot_filter_fields( is_array( $stores ) ? $stores : [], is_array( $categories ) ? $categories : [], 'mobile' ); ?>
				</div>
				<div class="sticky bottom-0 grid grid-cols-2 gap-3 border-t border-slate-200 bg-white px-5 py-4 dark:border-slate-700 dark:bg-slate-900">
					<button type="button" id="bt-loot-clear-mobile" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm font-black text-slate-700 transition hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
						<?php esc_html_e( 'Clear', 'bigtricks' ); ?>
					</button>
					<button type="button" id="bt-loot-apply-mobile" class="rounded-xl bg-primary-600 px-3 py-2 text-sm font-black text-white transition hover:bg-primary-700">
						<?php esc_html_e( 'Apply', 'bigtricks' ); ?>
					</button>
				</div>
			</form>
		</div>
	</div>

	<section class="rounded-3xl border border-slate-200 bg-white p-6 md:p-8 shadow-sm dark:border-slate-700 dark:bg-slate-900">
		<div class="flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
			<div>
				<h2 class="text-2xl sm:text-3xl font-black text-slate-900 flex items-center gap-2 dark:text-white">
					<span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse" aria-hidden="true"></span>
					Live Telegram Deals
				</h2>
				<p class="mt-2 text-slate-600 dark:text-slate-300">Latest Telegram messages shown exactly as posted on our channel.</p>
			</div>
		</div>

		<div class="mt-6 rounded-2xl border border-primary-200 bg-gradient-to-r from-primary-50 via-indigo-50 to-white p-5 md:p-6">
			<div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
				<div class="flex items-start gap-4">
					<div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-primary-600 text-white shadow-md">
						<i data-lucide="send" class="h-5 w-5"></i>
					</div>
					<div>
						<h3 class="text-xl font-black text-slate-900">Join Our Telegram for Real-Time Loot Deal Alerts</h3>
						<p class="mt-1 text-slate-600">Track every drop instantly with unedited deal messages, coupon updates, and stock-sensitive offers.</p>
						<div class="mt-3 flex flex-wrap gap-2 text-xs font-bold text-slate-700">
							<span class="rounded-full bg-white border border-slate-200 px-3 py-1">50K+ Active Members</span>
							<span class="rounded-full bg-white border border-slate-200 px-3 py-1">500+ Daily Deals</span>
							<span class="rounded-full bg-white border border-slate-200 px-3 py-1">24/7 Updates</span>
						</div>
					</div>
				</div>

				<a href="https://links.bigtricks.in/tg" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-5 py-3 text-sm font-black text-white shadow-md shadow-primary-200 transition hover:bg-primary-700 active:scale-95" target="_blank" rel="noopener noreferrer">
					Join Telegram Channel
					<i data-lucide="external-link" class="h-4 w-4"></i>
				</a>
			</div>
		</div>

		<div id="bt-telegram-deals-container" class="mt-6">
			<div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center dark:border-slate-700 dark:bg-slate-800/60" id="bt-loading-spinner">
				<div class="h-8 w-8 animate-spin rounded-full border-2 border-primary-200 border-t-primary-600" aria-hidden="true"></div>
				<p class="mt-3 text-sm font-semibold text-slate-600 dark:text-slate-300">Loading latest deals...</p>
			</div>
		</div>
	</section>

	<section class="rounded-3xl border border-slate-200 bg-white p-6 md:p-8 shadow-sm">
		<div class="mb-6">
			<h2 class="text-2xl sm:text-3xl font-black text-slate-900">Shop by Category</h2>
			<p class="mt-2 text-slate-600">Explore deals in your favorite categories.</p>
		</div>

		<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
			<?php
			if ( $categories && ! is_wp_error( $categories ) ) {
				foreach ( $categories as $category ) {
					$deal_count = (int) $category->count;
					?>
					<a href="<?php echo esc_url( get_term_link( $category ) ); ?>" class="group rounded-2xl border border-slate-200 bg-slate-50/60 p-4 transition-all hover:-translate-y-1 hover:border-primary-200 hover:bg-white hover:shadow-md">
						<div class="flex items-start justify-between gap-3">
							<div>
								<h3 class="text-base font-black text-slate-900 group-hover:text-primary-700 transition-colors"><?php echo esc_html( $category->name ); ?></h3>
								<p class="mt-1 text-xs font-semibold text-slate-500">
									<?php
									echo esc_html(
										sprintf(
											_n( '%s deal', '%s deals', $deal_count, 'bigtricks-deals' ),
											number_format_i18n( $deal_count )
										)
									);
									?>
								</p>
							</div>
							<span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-primary-100 text-primary-700">
								<i data-lucide="external-link" class="h-4 w-4"></i>
							</span>
						</div>
					</a>
					<?php
				}
			}
			?>
		</div>
	</section>

	<section class="rounded-3xl border border-slate-200 bg-white p-6 md:p-8 shadow-sm">
		<div class="mb-6 flex items-start justify-between gap-3">
			<div>
				<h2 class="text-2xl sm:text-3xl font-black text-slate-900">Popular Stores</h2>
				<p class="mt-2 text-slate-600">Shop from your favorite brands.</p>
			</div>
			<a href="<?php echo esc_url( home_url( '/stores/' ) ); ?>" class="inline-flex shrink-0 items-center gap-1 rounded-xl border border-slate-300 bg-white px-3 py-1.5 text-xs sm:text-sm font-black text-slate-700 hover:bg-slate-50">
				<?php esc_html_e( 'View All', 'bigtricks' ); ?>
				<i data-lucide="arrow-right" class="h-4 w-4"></i>
			</a>
		</div>

		<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
			<?php
			if ( $popular_stores && ! is_wp_error( $popular_stores ) ) {
				foreach ( $popular_stores as $store ) {
					$store_logo = get_term_meta( $store->term_id, 'thumb_image', true );
					if ( $store_logo && is_numeric( $store_logo ) ) {
						$store_logo = wp_get_attachment_image_url( (int) $store_logo, 'medium' );
					}
					?>
					<a href="<?php echo esc_url( get_term_link( $store ) ); ?>" class="group rounded-2xl border border-slate-200 bg-white p-4 text-center transition-all hover:-translate-y-1 hover:shadow-md hover:border-primary-200">
						<div class="mx-auto mb-3 flex h-16 w-16 items-center justify-center overflow-hidden rounded-full border border-slate-200 bg-slate-50">
							<?php if ( $store_logo ) : ?>
								<img src="<?php echo esc_url( (string) $store_logo ); ?>" alt="<?php echo esc_attr( $store->name ); ?>" class="h-full w-full object-contain" loading="lazy" decoding="async" />
							<?php else : ?>
								<span class="text-lg font-black text-slate-600"><?php echo esc_html( mb_substr( $store->name, 0, 1 ) ); ?></span>
							<?php endif; ?>
						</div>
						<h3 class="text-sm font-black text-slate-900 line-clamp-1 group-hover:text-primary-700 transition-colors"><?php echo esc_html( $store->name ); ?></h3>
					</a>
					<?php
				}
			}
			?>
		</div>
	</section>

	<section class="rounded-3xl border border-slate-200 bg-white p-6 md:p-8 shadow-sm">
		<div class="mb-6">
			<h2 class="text-2xl sm:text-3xl font-black text-slate-900">Why Choose BigTricks Deals?</h2>
			<p class="mt-2 text-slate-600">Experience the best in online shopping deals.</p>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
			<div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5">
				<div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
					<i data-lucide="wallet" class="h-5 w-5"></i>
				</div>
				<h3 class="text-lg font-black text-slate-900">Best Prices Guaranteed</h3>
				<p class="mt-2 text-sm text-slate-600">We compare prices across multiple platforms to ensure you get the absolute best deals available.</p>
			</div>

			<div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5">
				<div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 text-red-700">
					<i data-lucide="zap" class="h-5 w-5"></i>
				</div>
				<h3 class="text-lg font-black text-slate-900">Lightning Fast Updates</h3>
				<p class="mt-2 text-sm text-slate-600">New deals are added multiple times daily, so you never miss out on limited-time offers.</p>
			</div>

			<div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5">
				<div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-700">
					<i data-lucide="shield-check" class="h-5 w-5"></i>
				</div>
				<h3 class="text-lg font-black text-slate-900">Verified &amp; Safe</h3>
				<p class="mt-2 text-sm text-slate-600">All deals are verified and tested to ensure they are legitimate and safe for shopping.</p>
			</div>

			<div class="rounded-2xl border border-primary-200 bg-gradient-to-br from-primary-50 to-indigo-50 p-5">
				<div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary-600 text-white">
					<i data-lucide="smartphone" class="h-5 w-5"></i>
				</div>
				<h3 class="text-lg font-black text-slate-900">Instant Alerts on Android App</h3>
				<p class="mt-2 text-sm text-slate-600">Download our Android app for instant notifications about the best loot deals and exclusive offers.</p>
				<a href="https://play.google.com/store/apps/details?id=in.bigtricks" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-black text-white shadow-md shadow-primary-200 transition hover:bg-primary-700" target="_blank" rel="noopener noreferrer">
					<i data-lucide="arrow-right" class="h-4 w-4"></i>
					Download App
				</a>
			</div>
		</div>
	</section>

	<section class="rounded-3xl border border-slate-200 bg-white p-6 md:p-8 shadow-sm">
		<div class="mb-6">
			<h2 class="text-2xl sm:text-3xl font-black text-slate-900">What Our Users Say</h2>
			<p class="mt-2 text-slate-600">Real experiences from satisfied shoppers.</p>
		</div>

		<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
			<div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5">
				<p class="text-slate-700 leading-relaxed">"BigTricks Deals has saved me thousands of rupees this year! The deals are genuine and the platform is so easy to use."</p>
				<div class="mt-4 flex items-center gap-3">
					<div class="flex h-10 w-10 items-center justify-center rounded-full bg-primary-100 font-black text-primary-700">R</div>
					<div>
						<h3 class="text-sm font-black text-slate-900">Rahul Sharma</h3>
						<p class="text-xs font-semibold text-slate-500">Mumbai, Maharashtra</p>
					</div>
				</div>
			</div>

			<div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5">
				<p class="text-slate-700 leading-relaxed">"I love how they update deals multiple times a day. I have found amazing offers on electronics and fashion items."</p>
				<div class="mt-4 flex items-center gap-3">
					<div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 font-black text-emerald-700">P</div>
					<div>
						<h3 class="text-sm font-black text-slate-900">Priya Patel</h3>
						<p class="text-xs font-semibold text-slate-500">Ahmedabad, Gujarat</p>
					</div>
				</div>
			</div>

			<div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5">
				<p class="text-slate-700 leading-relaxed">"The Telegram channel is a game-changer! I get instant notifications about flash sales and never miss a good deal."</p>
				<div class="mt-4 flex items-center gap-3">
					<div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100 font-black text-amber-700">A</div>
					<div>
						<h3 class="text-sm font-black text-slate-900">Amit Kumar</h3>
						<p class="text-xs font-semibold text-slate-500">Delhi, NCR</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<section class="rounded-3xl border border-slate-200 bg-white p-6 md:p-8 shadow-sm">
		<div class="mb-6">
			<h2 class="text-2xl sm:text-3xl font-black text-slate-900">Stay Updated with Latest Deals</h2>
			<p class="mt-2 text-slate-600">Follow us on your favorite platforms for instant deal alerts.</p>
		</div>

		<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
			<div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5">
				<div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700">
					<i data-lucide="message-circle" class="h-5 w-5"></i>
				</div>
				<h3 class="text-lg font-black text-slate-900">WhatsApp Channel</h3>
				<p class="mt-1 text-sm text-slate-600">Get instant deal notifications on WhatsApp.</p>
				<a href="https://links.bigtricks.in/whatsapp" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-black text-white hover:bg-emerald-700 transition" target="_blank" rel="noopener noreferrer">Join Channel</a>
			</div>

			<div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5">
				<div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary-100 text-primary-700">
					<i data-lucide="send" class="h-5 w-5"></i>
				</div>
				<h3 class="text-lg font-black text-slate-900">Telegram Channel</h3>
				<p class="mt-1 text-sm text-slate-600">Join our Telegram for exclusive deals.</p>
				<a href="https://links.bigtricks.in/tg" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-primary-600 px-4 py-2.5 text-sm font-black text-white hover:bg-primary-700 transition" target="_blank" rel="noopener noreferrer">Join Channel</a>
			</div>

			<div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5">
				<div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-slate-200 text-slate-700">
					<i data-lucide="twitter" class="h-5 w-5"></i>
				</div>
				<h3 class="text-lg font-black text-slate-900">X (Twitter)</h3>
				<p class="mt-1 text-sm text-slate-600">Follow us for real-time deal updates.</p>
				<a href="/bigtricksin" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-slate-800 px-4 py-2.5 text-sm font-black text-white hover:bg-slate-900 transition" target="_blank" rel="noopener noreferrer">Follow Us</a>
			</div>

			<div class="rounded-2xl border border-slate-200 bg-slate-50/70 p-5">
				<div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-700">
					<i data-lucide="smartphone" class="h-5 w-5"></i>
				</div>
				<h3 class="text-lg font-black text-slate-900">Android App</h3>
				<p class="mt-1 text-sm text-slate-600">Download our app for instant deal alerts.</p>
				<a href="https://play.google.com/store/apps/details?id=in.bigtricks" class="mt-4 inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-sm font-black text-white hover:bg-amber-600 transition" target="_blank" rel="noopener noreferrer">Download App</a>
			</div>
		</div>
	</section>

	<section class="rounded-3xl border border-slate-200 bg-white p-6 md:p-8 shadow-sm">
		<div class="flex items-start gap-3 mb-4">
			<div class="mt-1 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-primary-100 text-primary-700">
				<i data-lucide="search" class="h-5 w-5"></i>
			</div>
			<div>
				<h2 class="text-2xl sm:text-3xl font-black text-slate-900">Loot Deals, Coupon Codes &amp; Flash Offers</h2>
				<p class="mt-2 text-slate-600">Bigtricks curates fresh loot deals for electronics, fashion, groceries, home essentials, and app-exclusive offers.</p>
			</div>
		</div>

		<div class="prose prose-slate max-w-none prose-p:leading-relaxed prose-headings:font-black prose-li:my-0">
			<p>Find handpicked loot deals updated throughout the day with price-drop opportunities, coupon combinations, UPI offers, card discounts, and no-cost EMI campaigns. Every listing is designed to help you compare quickly and grab value before stock runs out.</p>
			<ul>
				<li>Daily loot deals across major Indian shopping platforms.</li>
				<li>Coupon-ready offers with extra savings where available.</li>
				<li>Category-first browsing for faster discovery.</li>
				<li>Live Telegram deal stream for real-time updates.</li>
			</ul>
			<?php if ( $archive_description ) : ?>
				<?php echo wp_kses_post( wpautop( $archive_description ) ); ?>
			<?php endif; ?>
		</div>
	</section>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
	initLootDealsArchive();
	fetchTelegramDeals();
});

function initLootDealsArchive() {
	const feed = document.getElementById('bt-loot-feed');
	if (!feed) {
		return;
	}

	const desktopForm = document.getElementById('bt-loot-filter-form-desktop');
	const mobileForm = document.getElementById('bt-loot-filter-form-mobile');
	const applyDesktop = document.getElementById('bt-loot-apply-desktop');
	const clearDesktop = document.getElementById('bt-loot-clear-desktop');
	const applyMobile = document.getElementById('bt-loot-apply-mobile');
	const clearMobile = document.getElementById('bt-loot-clear-mobile');
	const openDrawerBtn = document.getElementById('bt-open-loot-filters');
	const closeDrawerBtn = document.getElementById('bt-close-loot-filters');
	const drawer = document.getElementById('bt-loot-filter-drawer');
	const drawerPanel = document.getElementById('bt-loot-filter-panel');
	const drawerOverlay = document.getElementById('bt-loot-filter-overlay');
	const resultsText = document.getElementById('bt-results-text');
	const loadWrap = document.getElementById('bt-loot-load-wrap');
	const loadBtn = document.getElementById('bt-loot-load-more');
	const sentinel = document.getElementById('bt-loot-sentinel');
	const emptyState = document.getElementById('bt-loot-empty');

	if (!desktopForm || !mobileForm || !loadBtn) {
		return;
	}

	const initialLoadText = loadBtn.innerHTML;
	const nonce = loadBtn.dataset.nonce || (window.bigtricksData ? bigtricksData.loadMoreNonce : '');
	const maxAutoLoads = 2;
	let observer = null;

	const state = {
		search: '',
		store: '',
		minPrice: '',
		maxPrice: '',
		categories: [],
		page: Number(loadBtn.dataset.page || 1),
		hasMore: !loadWrap.classList.contains('hidden'),
		autoLoadCount: 0,
		autoLoadEnabled: true,
		loading: false,
	};

	function collectFormState(form) {
		const categories = Array.from(form.querySelectorAll('input[name="categories[]"]:checked')).map(function(input) {
			return input.value;
		});

		return {
			search: (form.querySelector('input[name="search"]')?.value || '').trim(),
			store: (form.querySelector('select[name="store"]')?.value || '').trim(),
			minPrice: (form.querySelector('input[name="min_price"]')?.value || '').trim(),
			maxPrice: (form.querySelector('input[name="max_price"]')?.value || '').trim(),
			categories,
		};
	}

	function applyStateToForm(form, values) {
		const searchInput = form.querySelector('input[name="search"]');
		const storeInput = form.querySelector('select[name="store"]');
		const minInput = form.querySelector('input[name="min_price"]');
		const maxInput = form.querySelector('input[name="max_price"]');

		if (searchInput) searchInput.value = values.search;
		if (storeInput) storeInput.value = values.store;
		if (minInput) minInput.value = values.minPrice;
		if (maxInput) maxInput.value = values.maxPrice;

		form.querySelectorAll('input[name="categories[]"]').forEach(function(input) {
			input.checked = values.categories.includes(input.value);
		});
	}

	function openDrawer() {
		if (!drawer || !drawerPanel) return;
		drawer.classList.remove('hidden');
		drawer.setAttribute('aria-hidden', 'false');
		document.body.style.overflow = 'hidden';
		requestAnimationFrame(function() {
			drawerPanel.classList.remove('translate-y-full');
		});
	}

	function closeDrawer() {
		if (!drawer || !drawerPanel) return;
		drawerPanel.classList.add('translate-y-full');
		drawer.setAttribute('aria-hidden', 'true');
		setTimeout(function() {
			drawer.classList.add('hidden');
			document.body.style.overflow = '';
		}, 220);
	}

	function updateResultsText(count) {
		if (!resultsText) return;
		const label = Number(count) === 1 ? 'Deal' : 'Deals';
		resultsText.textContent = `${Number(count).toLocaleString('en-IN')} ${label}`;
	}

	function updateLoadState() {
		if (!loadWrap) return;
		const showButton = state.hasMore && !state.autoLoadEnabled;
		loadWrap.classList.toggle('hidden', !showButton);
	}

	function setLoading(isLoading) {
		state.loading = isLoading;
		if (!loadBtn) return;
		if (isLoading) {
			loadBtn.disabled = true;
			loadBtn.innerHTML = '<i data-lucide="loader-2" class="h-4 w-4 animate-spin"></i> Loading...';
			if (window.lucide && typeof window.lucide.createIcons === 'function') {
				window.lucide.createIcons({ nodes: [loadBtn] });
			}
		} else {
			loadBtn.disabled = false;
			loadBtn.innerHTML = initialLoadText;
			if (window.lucide && typeof window.lucide.createIcons === 'function') {
				window.lucide.createIcons({ nodes: [loadBtn] });
			}
		}
	}

	function buildRequestBody(targetPage) {
		const body = new URLSearchParams();
		body.append('action', 'bigtricks_load_more');
		body.append('nonce', nonce);
		body.append('page', String(targetPage));
		body.append('type', 'deal');
		body.append('loot_grid', '1');
		body.append('search', state.search);
		body.append('store', state.store);
		body.append('min_price', state.minPrice);
		body.append('max_price', state.maxPrice);
		state.categories.forEach(function(catId) {
			body.append('categories[]', catId);
		});
		return body;
	}

	function toggleEmptyState(show) {
		if (!emptyState) return;
		emptyState.classList.toggle('hidden', !show);
	}

	function requestDeals(resetFeed, source = 'manual') {
		if (state.loading) return;
		if (!resetFeed && !state.hasMore) return;
		if (!resetFeed && source === 'auto' && !state.autoLoadEnabled) return;

		const targetPage = resetFeed ? 1 : (state.page + 1);
		setLoading(true);

		fetch(bigtricksData.ajaxUrl, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: buildRequestBody(targetPage).toString(),
		})
			.then(function(response) {
				return response.json();
			})
			.then(function(data) {
				if (!data.success) {
					throw new Error('Loot deals request failed');
				}

				const payload = data.data || {};
				const html = payload.html || '';

				if (resetFeed) {
					feed.innerHTML = html;
				} else {
					feed.insertAdjacentHTML('beforeend', html);
				}

				state.page = targetPage;
				state.hasMore = !!payload.has_more;

				if (resetFeed) {
					state.autoLoadCount = 0;
					state.autoLoadEnabled = true;
				} else if (source === 'auto') {
					state.autoLoadCount += 1;
					if (state.autoLoadCount >= maxAutoLoads) {
						state.autoLoadEnabled = false;
						if (observer) {
							observer.disconnect();
						}
					}
				}

				updateLoadState();
				updateResultsText(Number(payload.count || 0));
				toggleEmptyState(resetFeed && !html.trim());

				if (window.lucide && typeof window.lucide.createIcons === 'function') {
					window.lucide.createIcons({ nodes: [feed] });
				}
			})
			.catch(function(error) {
				console.error('Loot deals fetch error:', error);
			})
			.finally(function() {
				setLoading(false);
			});
	}

	function applyFromForm(form) {
		const formState = collectFormState(form);
		state.search = formState.search;
		state.store = formState.store;
		state.minPrice = formState.minPrice;
		state.maxPrice = formState.maxPrice;
		state.categories = formState.categories;

		applyStateToForm(desktopForm, state);
		applyStateToForm(mobileForm, state);
		requestDeals(true);
	}

	function clearFilters() {
		state.search = '';
		state.store = '';
		state.minPrice = '';
		state.maxPrice = '';
		state.categories = [];

		applyStateToForm(desktopForm, state);
		applyStateToForm(mobileForm, state);
		requestDeals(true);
	}

	applyDesktop?.addEventListener('click', function() {
		applyFromForm(desktopForm);
	});

	applyMobile?.addEventListener('click', function() {
		applyFromForm(mobileForm);
		closeDrawer();
	});

	clearDesktop?.addEventListener('click', clearFilters);
	clearMobile?.addEventListener('click', clearFilters);

	loadBtn.addEventListener('click', function() {
		requestDeals(false, 'manual');
	});

	openDrawerBtn?.addEventListener('click', openDrawer);
	closeDrawerBtn?.addEventListener('click', closeDrawer);
	drawerOverlay?.addEventListener('click', closeDrawer);

	document.addEventListener('keydown', function(event) {
		if (event.key === 'Escape') {
			closeDrawer();
		}
	});

	if ('IntersectionObserver' in window && sentinel) {
		observer = new IntersectionObserver(function(entries) {
			entries.forEach(function(entry) {
				if (entry.isIntersecting && !state.loading && state.hasMore && state.autoLoadEnabled) {
					requestDeals(false, 'auto');
				}
			});
		}, { rootMargin: '240px 0px' });

		observer.observe(sentinel);
	} else {
		state.autoLoadEnabled = false;
		updateLoadState();
	}
}

function fetchTelegramDeals() {
	const container = document.getElementById('bt-telegram-deals-container');
	const spinner = document.getElementById('bt-loading-spinner');

	fetch('https://dp.bigtricks.in/tUpdates')
		.then(response => response.json())
		.then(data => {
			if (spinner) {
				spinner.style.display = 'none';
			}

			if (data.data && data.data.length > 0) {
				const scrollContainer = document.createElement('div');
				scrollContainer.className = 'overflow-x-auto pb-2';

				const dealsWrapper = document.createElement('div');
				dealsWrapper.className = 'grid auto-cols-[minmax(300px,420px)] grid-flow-col gap-4';

				data.data.forEach((deal) => {
					const messageHtml = renderTelegramMessageHtml(deal.text || deal.caption || '', deal.entities || deal.caption_entities || []);
					const channelTitle = (deal.chat && deal.chat.title) ? deal.chat.title : 'Bigtricks Telegram';
					const hasEdits = typeof deal.edit_date !== 'undefined' && deal.edit_date !== null;

					const dealCard = document.createElement('article');
					dealCard.className = 'h-[340px] rounded-2xl border border-slate-200 bg-white p-4 shadow-sm flex flex-col';

					dealCard.innerHTML = `
						<div class="space-y-3 flex h-full flex-col">
							<div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-2">
								<span class="inline-flex max-w-[70%] items-center gap-1 truncate rounded-full bg-primary-50 border border-primary-100 px-2 py-0.5 text-[11px] font-black text-primary-700" title="${escapeHtml(channelTitle)}">
									<i data-lucide="send" class="h-3 w-3"></i>
									${escapeHtml(channelTitle)}
								</span>
								<span class="text-xs font-semibold text-slate-500 whitespace-nowrap">${formatPreciseTime(deal.date)}</span>
							</div>
							${hasEdits ? `<span class="inline-flex w-fit items-center rounded-full bg-purple-50 border border-purple-200 px-2 py-0.5 text-[11px] font-bold text-purple-700">Edited</span>` : ''}
							<div class="min-h-0 flex-1 overflow-y-auto rounded-xl border border-slate-100 bg-slate-50/60 p-3">
								<div class="telegram-message prose prose-sm prose-slate max-w-none leading-relaxed break-words" data-role="telegram-message"></div>
							</div>
						</div>
					`;

					const messageNode = dealCard.querySelector('[data-role="telegram-message"]');
					if (messageNode) {
						messageNode.innerHTML = messageHtml || '<p>No message text available for this update.</p>';
					}

					dealsWrapper.appendChild(dealCard);
				});

				scrollContainer.appendChild(dealsWrapper);
				container.appendChild(scrollContainer);

				if (window.lucide && typeof window.lucide.createIcons === 'function') {
					window.lucide.createIcons();
				}
			} else {
				container.innerHTML = '<p class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-center text-sm font-semibold text-slate-600">No deals available at the moment. Check back later!</p>';
			}
		})
		.catch(error => {
			console.error('Error fetching Telegram deals:', error);
			if (spinner) {
				spinner.style.display = 'none';
			}
			container.innerHTML = '<p class="rounded-2xl border border-red-200 bg-red-50 p-6 text-center text-sm font-semibold text-red-700">Unable to load deals. Please try again later.</p>';
		});
}

function escapeHtml(str) {
	return String(str || '')
		.replace(/&/g, '&amp;')
		.replace(/</g, '&lt;')
		.replace(/>/g, '&gt;')
		.replace(/"/g, '&quot;')
		.replace(/'/g, '&#39;');
}

function escapeAttr(str) {
	return escapeHtml(str).replace(/`/g, '&#96;');
}

function normalizeEntityUrl(rawUrl) {
	const url = String(rawUrl || '').trim();
	if (!url) {
		return '';
	}
	if (/^https?:\/\//i.test(url)) {
		return url;
	}
	if (/^(t\.me|telegram\.me|www\.)/i.test(url)) {
		return 'https://' + url;
	}
	return 'https://' + url;
}

function applyEntityMarkup(text, entity) {
	const safeText = escapeHtml(text);
	const type = entity.type;

	if (type === 'url') {
		const href = normalizeEntityUrl(text);
		return `<a href="${escapeAttr(href)}" target="_blank" rel="noopener noreferrer" class="text-primary-700 font-semibold underline underline-offset-2 break-all">${safeText}</a>`;
	}

	if (type === 'text_link') {
		const href = normalizeEntityUrl(entity.url || '');
		if (!href) {
			return safeText;
		}
		return `<a href="${escapeAttr(href)}" target="_blank" rel="noopener noreferrer" class="text-primary-700 font-semibold underline underline-offset-2 break-all">${safeText}</a>`;
	}

	if (type === 'code') {
		return `<code class="rounded bg-amber-100 px-1.5 py-0.5 text-[12px] font-bold text-amber-800">${safeText}</code>`;
	}

	if (type === 'pre') {
		return `<pre class="overflow-x-auto rounded-lg bg-slate-900 p-2 text-[12px] leading-relaxed text-slate-100"><code>${safeText}</code></pre>`;
	}

	if (type === 'bold') {
		return `<strong>${safeText}</strong>`;
	}

	if (type === 'italic') {
		return `<em>${safeText}</em>`;
	}

	if (type === 'underline') {
		return `<span class="underline">${safeText}</span>`;
	}

	if (type === 'strikethrough') {
		return `<span class="line-through">${safeText}</span>`;
	}

	if (type === 'spoiler') {
		return `<span class="rounded bg-slate-200 px-1 text-slate-400 hover:text-slate-700">${safeText}</span>`;
	}

	return safeText;
}

function renderTelegramMessageHtml(message, entities) {
	const text = String(message || '');
	if (!text) {
		return '';
	}

	const sortedEntities = [...(entities || [])]
		.filter(e => Number.isInteger(e.offset) && Number.isInteger(e.length) && e.length > 0)
		.sort((a, b) => a.offset - b.offset || b.length - a.length);

	let html = '';
	let cursor = 0;

	for (const entity of sortedEntities) {
		const start = Math.max(0, entity.offset);
		const end = Math.min(text.length, entity.offset + entity.length);

		if (start < cursor || start >= end) {
			continue;
		}

		html += escapeHtml(text.slice(cursor, start));
		html += applyEntityMarkup(text.slice(start, end), entity);
		cursor = end;
	}

	html += escapeHtml(text.slice(cursor));

	return html.replace(/\n/g, '<br>');
}

function formatPreciseTime(timestamp) {
	if (window.bigtricksUtils && typeof window.bigtricksUtils.formatRelativeTime === 'function') {
		return window.bigtricksUtils.formatRelativeTime(timestamp);
	}

	if (!timestamp) return 'Just now';
	const date = new Date(Number(timestamp) * 1000);
	return date.toLocaleDateString();
}
</script>

<?php
get_footer();
?>
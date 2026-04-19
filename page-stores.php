<?php
/**
 * Store Archive Hub — /stores page
 * Displays all stores (taxonomy terms) with stats and featured flag.
 *
 * Data source (per requirements):
 *   - Taxonomy: store
 *   - Order by: count (number of posts)
 *   - Order: DESC
 *   - Meta: thumb_image, featured_store
 *   - Stats: hardcoded in $stats array below
 *
 * @package Bigtricks
 */

get_header();

// Page stats (hardcoded)
$stats = [
	'stores' => '500',
	'deals'  => '1000',
	'savings' => '₹10,000',
];

/**
 * Query stores ordered by post count (number of deals/offers per store)
 */
$stores_args = [
	'taxonomy'   => 'store',
	'orderby'    => 'count',
	'order'      => 'DESC',
	'hide_empty' => true,
	'number'     => 0, // Fetch all
];

$stores = get_terms( $stores_args );
$total_stores = count( $stores );

// Featured stores: sort featured to top
$featured_stores = [];
$regular_stores  = [];

foreach ( $stores as $store ) {
	$is_featured = (bool) get_term_meta( $store->term_id, 'featured_store', true );
	if ( $is_featured ) {
		$featured_stores[] = $store;
	} else {
		$regular_stores[] = $store;
	}
}

// Merge: featured first, then regular
$sorted_stores = array_merge( $featured_stores, $regular_stores );

/**
 * Sidebar visibility setting: allow filtering to disable sidebar per page
 * Usage in child themes or custom code:
 *   add_filter( 'bigtricks_show_sidebar', '__return_false', 10 );
 */
$show_sidebar = apply_filters( 'bigtricks_show_sidebar', true );

?>

<main class="max-w-[1400px] mx-auto px-4 py-6 md:py-8 flex flex-col lg:flex-row gap-8 flex-1 w-full box-border" id="main-content">

	<div class="flex-1 min-w-0 w-full overflow-hidden">

		<!-- ═══ HERO SECTION ═══ -->
		<div class="bg-gradient-to-br from-primary-600 via-primary-500 to-purple-600 rounded-3xl p-8 sm:p-12 mb-12 text-white relative overflow-hidden shadow-lg">
			<!-- Decorative elements -->
			<div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl -mr-48 -mt-48" aria-hidden="true"></div>
			<div class="absolute bottom-0 left-0 w-72 h-72 bg-purple-300/10 rounded-full blur-3xl -ml-36 -mb-36" aria-hidden="true"></div>

			<!-- Content -->
			<div class="relative z-10 max-w-2xl">
				<h1 class="text-white text-3xl sm:text-4xl lg:text-5xl font-black leading-tight mb-4">
					<?php
					printf(
						/* translators: %s: total number of stores */
						esc_html__( 'Discover Amazing Deals Across %s+ Stores', 'bigtricks' ),
						esc_html( $stats['stores'] ?? '500' )
					);
					?>
				</h1>

				<p class="text-base sm:text-lg text-white/90 leading-relaxed mb-8 max-w-xl">
					<?php esc_html_e( 'Save up to ₹10,000 monthly with verified deals, exclusive offers, and cashback opportunities from your favorite stores.', 'bigtricks' ); ?>
				</p>

				<!-- CTA: Scroll to Stores -->
				<button id="scroll-to-stores" class="inline-flex items-center gap-2 px-6 py-3 bg-white dark:bg-slate-800 text-primary-600 dark:text-primary-400 font-black rounded-xl shadow-lg hover:shadow-xl dark:shadow-slate-900/30 dark:hover:shadow-slate-900/50 transition-all active:scale-95">
					<i data-lucide="arrow-down" class="w-5 h-5"></i>
					<?php esc_html_e( 'Browse Stores', 'bigtricks' ); ?>
				</button>
			</div>
		</div>

		<!-- ═══ STATS SECTION ═══ -->
		<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-12">
			<!-- Stat: Stores -->
			<div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-7 shadow-sm hover:shadow-md transition-shadow">
				<div class="flex items-center justify-between mb-3">
					<span class="text-slate-500 text-sm font-bold uppercase tracking-wide"><?php esc_html_e( 'Stores', 'bigtricks' ); ?></span>
					<div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center">
						<i data-lucide="shopping-bag" class="w-5 h-5 text-primary-600"></i>
					</div>
				</div>
				<div class="text-3xl sm:text-4xl font-black text-slate-900 leading-tight"><?php echo esc_html( $stats['stores'] ?? '500' ); ?>+</div>
				<p class="text-xs text-slate-400 mt-2"><?php esc_html_e( 'Curated merchant partners', 'bigtricks' ); ?></p>
			</div>

			<!-- Stat: Deals -->
			<div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-7 shadow-sm hover:shadow-md transition-shadow">
				<div class="flex items-center justify-between mb-3">
					<span class="text-slate-500 text-sm font-bold uppercase tracking-wide"><?php esc_html_e( 'Deals', 'bigtricks' ); ?></span>
					<div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
						<i data-lucide="zap" class="w-5 h-5 text-red-600"></i>
					</div>
				</div>
				<div class="text-3xl sm:text-4xl font-black text-slate-900 leading-tight"><?php echo esc_html( $stats['deals'] ?? '1000' ); ?>+</div>
				<p class="text-xs text-slate-400 mt-2"><?php esc_html_e( 'Active offers & coupons', 'bigtricks' ); ?></p>
			</div>

			<!-- Stat: Savings -->
			<div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-7 shadow-sm hover:shadow-md transition-shadow">
				<div class="flex items-center justify-between mb-3">
					<span class="text-slate-500 text-sm font-bold uppercase tracking-wide"><?php esc_html_e( 'Savings', 'bigtricks' ); ?></span>
					<div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center">
						<i data-lucide="wallet" class="w-5 h-5 text-emerald-600"></i>
					</div>
				</div>
				<div class="text-2xl sm:text-3xl font-black text-slate-900 leading-tight"><?php echo esc_html( $stats['savings'] ?? '₹10,000' ); ?></div>
				<p class="text-xs text-slate-400 mt-2"><?php esc_html_e( 'Monthly savings potential', 'bigtricks' ); ?></p>
			</div>
		</div>

		<!-- ═══ STORES SECTION ═══ -->
		<div id="bt-feed" class="mb-12">
			<!-- Search Bar -->
			<div class="mb-8">
				<div class="relative">
					<input 
						type="text" 
						id="store-search" 
						placeholder="<?php esc_attr_e( 'Search stores...', 'bigtricks' ); ?>" 
						class="w-full px-5 py-3.5 border-2 border-slate-200 rounded-xl focus:border-primary-600 focus:outline-none transition-colors font-bold text-slate-900 placeholder:text-slate-400"
						aria-label="<?php esc_attr_e( 'Search stores', 'bigtricks' ); ?>"
					>
					<i data-lucide="search" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 pointer-events-none"></i>
				</div>
			</div>

			<div class="flex items-center justify-between mb-6">
				<h2 class="text-2xl sm:text-3xl font-black text-slate-900">
					<?php esc_html_e( 'Explore Our Stores', 'bigtricks' ); ?>
				</h2>
				<span class="text-sm text-slate-500 font-bold">
					<?php
					printf(
						/* translators: %s: number of stores */
						esc_html( _n( '%s Store', '%s Stores', $total_stores, 'bigtricks' ) ),
						esc_html( number_format_i18n( $total_stores ) )
					);
					?>
				</span>
			</div>

			<?php if ( empty( $sorted_stores ) ) : ?>

			<div class="bg-white p-12 rounded-3xl border border-slate-200 text-center">
				<div class="bg-slate-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
					<i data-lucide="shopping-bag" class="w-8 h-8 text-slate-400"></i>
				</div>
				<h3 class="text-xl font-bold text-slate-900 mb-2"><?php esc_html_e( 'No stores found', 'bigtricks' ); ?></h3>
				<p class="text-slate-500 mb-4"><?php esc_html_e( 'Check back soon for more stores!', 'bigtricks' ); ?></p>
			</div>

			<?php else : ?>

			<div id="stores-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
				<?php
				foreach ( $sorted_stores as $store ) {
					$store_id      = (int) $store->term_id;
					$store_name    = $store->name;
					$store_url     = get_term_link( $store );
					$logo_url      = get_term_meta( $store_id, 'thumb_image', true );
					$is_featured   = (bool) get_term_meta( $store_id, 'featured_store', true );

					// Logo URL handling (attachment ID or direct URL)
					if ( $logo_url ) {
						$logo_url = is_numeric( $logo_url )
							? (string) wp_get_attachment_image_url( (int) $logo_url, 'medium_large' )
							: $logo_url;
					}

					// Pastel color fallback for initials
					$pastel_colors = [
						'bg-primary-100 text-primary-600',
						'bg-pink-100 text-pink-600',
						'bg-emerald-100 text-emerald-600',
						'bg-orange-100 text-orange-600',
						'bg-purple-100 text-purple-600',
						'bg-cyan-100 text-cyan-600',
						'bg-amber-100 text-amber-700',
						'bg-blue-100 text-blue-700',
					];
					$logo_bg_class = $pastel_colors[ $store_id % count( $pastel_colors ) ];

					if ( is_wp_error( $store_url ) ) {
						continue;
					}

					?>
				<a 
					href="<?php echo esc_url( $store_url ); ?>" 
					class="group relative bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-lg transition-all duration-300 hover:-translate-y-1 flex flex-col items-center p-5 text-center"
					data-store-name="<?php echo esc_attr( strtolower( $store_name ) ); ?>"
				>
					<!-- Featured Badge -->
					<?php if ( $is_featured ) : ?>
					<div class="absolute top-2 right-2 z-10 flex items-center gap-1 px-2 py-1 bg-amber-50 border border-amber-200 rounded-full shadow-sm">
						<i data-lucide="star" class="w-3 h-3 text-amber-500 fill-current"></i>
						<span class="text-xs font-black text-amber-700 uppercase tracking-wide"><?php esc_html_e( 'Featured', 'bigtricks' ); ?></span>
					</div>
					<?php endif; ?>

					<!-- Store Logo Circle -->
					<div class="w-20 h-20 rounded-full border-2 border-slate-100 bg-white overflow-hidden mb-4 shrink-0 group-hover:border-primary-200 transition-colors shadow-sm">
						<?php if ( $logo_url ) : ?>
						<img
							src="<?php echo esc_url( $logo_url ); ?>"
							alt="<?php echo esc_attr( $store_name ); ?>"
							class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-300"
							loading="lazy"
							decoding="async"
							width="80"
							height="80"
						>
						<?php else : ?>
						<div class="w-full h-full flex items-center justify-center text-2xl font-black <?php echo esc_attr( $logo_bg_class ); ?> rounded-full">
							<?php echo esc_html( mb_strtoupper( mb_substr( $store_name, 0, 2 ) ) ); ?>
						</div>
						<?php endif; ?>
					</div>

					<!-- Store Name -->
					<h3 class="text-sm sm:text-base font-black text-slate-900 line-clamp-1 group-hover:text-primary-600 transition-colors mb-1">
						<?php echo esc_html( $store_name ); ?>
					</h3>

					<!-- Post Count -->
					<p class="text-xs text-slate-600 font-semibold">
						<?php
						printf(
							/* translators: %d: number of posts */
							esc_html( _n( '%d Deal', '%d Deals', (int) $store->count, 'bigtricks' ) ),
							(int) $store->count
						);
						?>
					</p>

				</a><!-- /.store-card -->
				<?php
				} // endforeach stores
				?>
			</div><!-- /#stores-grid -->

			<?php endif; ?>
		</div>

	</div><!-- /main column -->

	<!-- ═══ SIDEBAR (Configurable) ═══ -->
	<?php if ( $show_sidebar ) :
		get_sidebar();
	endif; ?>

</main><!-- /#main-content -->

<script>
// Scroll to stores when "Browse Stores" button clicked
document.addEventListener( 'DOMContentLoaded', function() {
	const scrollBtn = document.getElementById( 'scroll-to-stores' );
	const storesSection = document.getElementById( 'bt-feed' );

	if ( scrollBtn && storesSection ) {
		scrollBtn.addEventListener( 'click', function( e ) {
			e.preventDefault();
			storesSection.scrollIntoView( { behavior: 'smooth', block: 'start' } );
		} );
	}

	// Local search functionality
	const searchInput = document.getElementById( 'store-search' );
	const storesGrid = document.getElementById( 'stores-grid' );

	if ( searchInput && storesGrid ) {
		searchInput.addEventListener( 'input', function() {
			const query = this.value.toLowerCase().trim();
			const storeCards = storesGrid.querySelectorAll( 'a[data-store-name]' );

			storeCards.forEach( function( card ) {
				const storeName = card.getAttribute( 'data-store-name' );
				if ( query === '' || storeName.includes( query ) ) {
					card.style.display = '';
				} else {
					card.style.display = 'none';
				}
			} );
		} );
	}
} );
</script>

<?php
get_footer();
?>

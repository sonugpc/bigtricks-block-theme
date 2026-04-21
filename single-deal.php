<?php
/**
 * Single Deal Template
 * Clean template matching screenshot design
 * 
 * @package Bigtricks
 */

get_header();
?>

<?php get_template_part( 'template-parts/share-popover' ); ?>

<main class="max-w-[1400px] mx-auto px-4 py-6 md:py-8 w-full" id="main-content">

	<?php while ( have_posts() ) : the_post(); 
		// Get deal meta fields (correct field names from plugin)
		$post_id                = get_the_ID();
		$offer_thumbnail_url    = get_post_meta( $post_id, '_btdeals_offer_thumbnail_url', true );
		$product_thumbnail_url  = get_post_meta( $post_id, '_btdeals_product_thumbnail_url', true );
		$offer_url              = get_post_meta( $post_id, '_btdeals_offer_url', true );
		$coupon_code            = get_post_meta( $post_id, '_btdeals_coupon_code', true );
		$old_price              = floatval( get_post_meta( $post_id, '_btdeals_offer_old_price', true ) );
		$sale_price             = floatval( get_post_meta( $post_id, '_btdeals_offer_sale_price', true ) );
		$discount_percent       = intval( get_post_meta( $post_id, '_btdeals_discount', true ) );
		$discount_tag           = get_post_meta( $post_id, '_btdeals_discount_tag', true );
		$expiry_date            = get_post_meta( $post_id, '_btdeals_expiration_date', true );
		$is_expired             = (bool) get_post_meta( $post_id, '_btdeals_is_expired', true );
		$mask_coupon            = (bool) get_post_meta( $post_id, '_btdeals_mask_coupon', true );
		$last_price_updated_at  = get_post_meta( $post_id, '_btdeals_last_price_updated_at', true );
		$product_feature        = get_post_meta( $post_id, '_btdeals_product_feature', true );
		$disclaimer             = get_post_meta( $post_id, '_btdeals_disclaimer', true );
		$verify_label           = get_post_meta( $post_id, '_btdeals_verify_label', true );
		$button_text            = get_post_meta( $post_id, '_btdeals_button_text', true );
		$product_name           = get_post_meta( $post_id, '_btdeals_product_name', true );
		$short_description      = get_post_meta( $post_id, '_btdeals_short_description', true );
		$store_name_meta        = get_post_meta( $post_id, '_btdeals_store', true );
		
		// Calculate savings amount
		$savings = '';
		$savings_amount = 0;

		if ( $old_price > 0 && $sale_price > 0 && $old_price > $sale_price ) {
			$savings_amount = $old_price - $sale_price;
			$savings = '₹' . number_format( $savings_amount );
		}
		// Calculate discount if not provided
		if ( ! $discount_percent && $old_price > 0 && $sale_price > 0 ) {
			$discount_percent = intval( round( ( ( $old_price - $sale_price ) / $old_price ) * 100 ) );
		}
		
		// Get store taxonomy (fallback to meta field if taxonomy not set)
		$stores = get_the_terms( $post_id, 'store' );
		$store_name = '';
		$store_logo = '';
		if ( $stores && ! is_wp_error( $stores ) ) {
			$store = array_shift( $stores );
			$store_name = $store->name;
			$store_logo = get_term_meta( $store->term_id, 'thumb_image', true );
			// Handle attachment ID or direct URL
			if ( $store_logo && is_numeric( $store_logo ) ) {
				$store_logo = wp_get_attachment_image_url( (int) $store_logo, 'medium' );
			}
		} elseif ( $store_name_meta ) {
			$store_name = ucfirst( $store_name_meta );
		}
		
		// Thumbnail priority: offer_thumbnail_url > product_thumbnail_url > featured_image > store_logo
		$product_image_url = '';
		if ( $offer_thumbnail_url ) {
			$product_image_url = $offer_thumbnail_url;
		} elseif ( $product_thumbnail_url ) {
			$product_image_url = $product_thumbnail_url;
		} elseif ( has_post_thumbnail() ) {
			$product_image_url = get_the_post_thumbnail_url( $post_id, 'large' );
		} elseif ( $store_logo ) {
			$product_image_url = $store_logo;
		}
		
		// Get primary category
		$categories = get_the_category();
		$primary_category = '';
		$primary_category_url = '';
		if ( ! empty( $categories ) ) {
			$primary_category = $categories[0]->name;
			$primary_category_url = get_category_link( $categories[0]->term_id );
		}
		
		$post_date_relative = bigtricks_time_ago( get_the_time( 'U' ) );
		$post_date_full = get_the_date( 'F j, Y g:i A' );
		
		// Button text priority: button_text > verify_label > default
		$cta_button_text = '';
		if ( $button_text ) {
			$cta_button_text = $button_text;
		} elseif ( $verify_label ) {
			$cta_button_text = $verify_label;
		} else {
			$cta_button_text = $store_name ? sprintf( __( 'Buy Now at %s', 'bigtricks' ), $store_name ) : __( 'Get This Deal', 'bigtricks' );
		}
		
		// Check if expired (prefer meta field, fallback to date calculation)
		if ( ! $is_expired && $expiry_date ) {
			$expiry_timestamp = strtotime( $expiry_date );
			$is_expired = $expiry_timestamp && $expiry_timestamp < time();
		}
	?>

	<!-- Full Width Deal Card (Top Section) -->
	<div class="bg-white dark:bg-slate-900 rounded-3xl shadow-soft overflow-hidden mb-8<?php echo $is_expired ? ' opacity-75' : ''; ?>">
		<?php if ( $is_expired ) : ?>
		<div class="bg-red-600 text-white py-2 px-6 text-center font-bold text-sm flex items-center justify-center gap-2">
			<i data-lucide="alert-circle" class="w-4 h-4"></i>
			<?php esc_html_e( 'This deal has expired', 'bigtricks' ); ?>
		</div>
		<?php endif; ?>
		<div class="p-6 md:p-8">
			<?php bigtricks_breadcrumbs(); ?>

			<!-- Title -->
			<div class="mb-8">
				<h1 class="text-2xl md:text-3xl lg:text-4xl font-black text-slate-900 dark:text-white leading-tight">
					<?php the_title(); ?>
				</h1>
			</div>

			<!-- Main Deal Layout: Image Left, Info Right (Full Width) -->
			<div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-start mb-8">
				<!-- Left: Product Image -->
				<div class="flex justify-center">
					<?php if ( $product_image_url ) : ?>
						<div class="relative w-full max-w-[200px] sm:max-w-[280px] h-[200px] sm:h-[280px] flex items-center justify-center bg-white rounded-2xl p-4">
						<img src="<?php echo esc_url( $product_image_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" class="max-w-full max-h-full object-contain" loading="eager" data-no-lazy="1" fetchpriority="high" decoding="async">
							<?php if ( $verify_label ) : ?>
							<div class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg text-sm font-bold shadow-lg">
								<i data-lucide="shield-check" class="w-4 h-4"></i>
								<?php echo esc_html( $verify_label ); ?>
							</div>
							<?php endif; ?>
							
							<?php if ( $discount_tag ) : ?>
							<div class="absolute top-3 right-3 inline-flex items-center gap-2 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-3 py-1.5 rounded-full text-xs font-black shadow-lg">
								<i data-lucide="flame" class="w-3.5 h-3.5"></i>
								<?php echo esc_html( $discount_tag ); ?>
							</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<!-- Right: Deal Info -->
				<div class="space-y-6">
					<!-- Pricing Box -->
					<div class="bg-slate-50 dark:bg-slate-800 rounded-2xl p-6">
						<!-- Sale Price -->
						<?php if ( $sale_price > 0 ) : ?>
							<div class="mb-4">
								<div class="flex items-center gap-1.5 mb-1">
									<div class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase">Deal Price</div>
									<!-- Price Info Tooltip -->
									<div class="relative group inline-block">
										<i data-lucide="info" class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 cursor-help"></i>
										<div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block w-64 bg-slate-800 dark:bg-slate-700 text-white text-xs rounded-lg p-3 shadow-xl z-50">
											<div class="font-bold mb-1">Price Information</div>
											<div class="text-slate-300 dark:text-slate-400">
												Price as of <?php echo esc_html( get_the_date( 'M j, Y g:i A' ) ); ?>. Prices may change on the merchant's website. We are not responsible for price changes.
											</div>
											<div class="absolute top-full left-1/2 -translate-x-1/2 w-0 h-0 border-x-4 border-x-transparent border-t-4 border-t-slate-800 dark:border-t-slate-700"></div>
										</div>
									</div>
								</div>
								<span class="text-5xl md:text-6xl font-black text-slate-900 dark:text-white">
									₹<?php echo esc_html( number_format( $sale_price ) ); ?>
								</span>
							</div>
						<?php endif; ?>
						
						<!-- MRP & Discount Row -->
						<?php if ( $old_price > 0 || $discount_percent > 0 || $savings ) : ?>
							<div class="flex items-center gap-3 flex-wrap mb-4">
								<?php if ( $old_price > 0 ) : ?>
									<div class="flex items-center gap-2">
										<span class="text-xs text-slate-500 dark:text-slate-400 font-bold uppercase">MRP:</span>
										<span class="text-xl font-bold text-slate-400 dark:text-slate-500 line-through">
											₹<?php echo esc_html( number_format( $old_price ) ); ?>
										</span>
									</div>
								<?php endif; ?>
								
								<?php if ( $discount_percent > 0 ) : ?>
									<span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-700 px-3 py-1.5 rounded-full text-sm font-black">
										<i data-lucide="percent" class="w-3 h-3"></i>
										<?php echo esc_html( $discount_percent ); ?>% OFF
									</span>
								<?php endif; ?>
								
								<?php if ( $savings ) : ?>
									<span class="inline-flex items-center gap-1 bg-blue-100 text-blue-700 px-3 py-1.5 rounded-full text-sm font-bold">
										<i data-lucide="trending-down" class="w-3 h-3"></i>
										You Save: <?php echo esc_html( $savings ); ?>
									</span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						
						<!-- Store & Meta Info Row -->
						<div class="flex flex-wrap items-center gap-2 pt-3 border-t border-slate-200 dark:border-slate-700">
							<!-- Store -->
							<?php if ( $store_name ) : ?>
								<?php if ( $store_logo ) : ?>
								<?php if ( ! empty( $stores ) && ! is_wp_error( $stores ) ) : ?>
									<a href="<?php echo esc_url( get_term_link( $stores[0] ) ); ?>" class="flex items-center gap-2 bg-white dark:bg-slate-700 px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-600 hover:border-primary-400 transition-colors">
										<img src="<?php echo esc_url( $store_logo ); ?>" alt="<?php echo esc_attr( $store_name ); ?>" class="w-6 h-6 object-contain rounded-full border border-slate-100">
										<span class="text-xs font-bold text-slate-700 dark:text-slate-200"><?php echo esc_html( $store_name ); ?></span>
									</a>
								<?php else : ?>
									<div class="flex items-center gap-2 bg-white dark:bg-slate-700 px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-600">
										<img src="<?php echo esc_url( $store_logo ); ?>" alt="<?php echo esc_attr( $store_name ); ?>" class="w-6 h-6 object-contain rounded-full border border-slate-100">
										<span class="text-xs font-bold text-slate-700 dark:text-slate-200"><?php echo esc_html( $store_name ); ?></span>
									</div>
								<?php endif; ?>
								<?php else : ?>
									<div class="flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-100 dark:bg-slate-700 rounded-lg text-xs text-slate-600 dark:text-slate-400 font-bold">
										<i data-lucide="store" class="w-3.5 h-3.5"></i>
										<?php echo esc_html( $store_name ); ?>
									</div>
								<?php endif; ?>
							<?php endif; ?>
							
							<!-- Category -->
							<?php if ( $primary_category ) : ?>
							<a href="<?php echo esc_url( $primary_category_url ); ?>" class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-primary-100 dark:bg-primary-900/30 hover:bg-primary-200 dark:hover:bg-primary-900/50 text-primary-700 dark:text-primary-400 rounded-lg transition-colors font-bold text-xs">
								<i data-lucide="tag" class="w-3.5 h-3.5"></i>
								<?php echo esc_html( $primary_category ); ?>
							</a>
							<?php endif; ?>
							
							<!-- Posted Time -->
							<span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-lg font-bold text-xs" title="<?php echo esc_attr( $post_date_full ); ?>">
								<i data-lucide="clock" class="w-3.5 h-3.5"></i>
								<?php echo esc_html( $post_date_relative ); ?>
							</span>
						</div>
					</div>

					<!-- Action Buttons Row -->
					<div class="flex gap-3">
				<?php if ( $offer_url ) : ?>
					<a href="<?php echo esc_url( $offer_url ); ?>" target="_blank" rel="nofollow noopener" class="flex-1 text-center bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-xl font-bold text-base transition-all shadow-md hover:shadow-lg dark:shadow-slate-900/30 dark:hover:shadow-slate-900/50">
						<?php echo esc_html( $cta_button_text ); ?>
					</a>
				<?php endif; ?>
				
					<button onclick="openSharePopover(event)" class="flex items-center gap-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 px-4 py-3 rounded-xl transition-colors">
						<i data-lucide="share-2" class="w-5 h-5"></i>
						<span class="font-bold">Share</span>
					</button>
				</div>
				
				<!-- Countdown Timer -->
				<?php if ( $expiry_date && ! $is_expired ) :
					$expiry_timestamp = strtotime( $expiry_date );
				?>
					<div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800 rounded-xl p-4">
						<div class="text-sm font-bold text-orange-700 dark:text-orange-400 mb-3">Deal Expires In</div>
						<div id="countdown-timer" class="grid grid-cols-4 gap-2" data-expiry="<?php echo esc_attr( $expiry_timestamp ); ?>">
							<div class="text-center">
								<div class="text-2xl font-black text-orange-600 dark:text-orange-500 countdown-days">00</div>
								<div class="text-xs text-slate-600 dark:text-slate-400 font-bold">Days</div>
							</div>
							<div class="text-center">
								<div class="text-2xl font-black text-orange-600 dark:text-orange-500 countdown-hours">00</div>
								<div class="text-xs text-slate-600 dark:text-slate-400 font-bold">Hours</div>
							</div>
							<div class="text-center">
								<div class="text-2xl font-black text-orange-600 dark:text-orange-500 countdown-minutes">00</div>
								<div class="text-xs text-slate-600 dark:text-slate-400 font-bold">Mins</div>
							</div>
							<div class="text-center">
								<div class="text-2xl font-black text-orange-600 dark:text-orange-500 countdown-seconds">00</div>
								<div class="text-xs text-slate-600 dark:text-slate-400 font-bold">Secs</div>
							</div>
						</div>
					</div>
				<?php endif; ?>
				</div>
			</div>
		</div>
	</div>

	<!-- Content Area with Sidebar -->
	<div class="lg:flex lg:gap-8">
		<!-- Main Content -->
		<div class="flex-1 lg:max-w-[900px]">
			<div class="bg-white dark:bg-slate-900 rounded-3xl shadow-soft overflow-hidden p-6 md:p-8 mb-8">
				<!-- Meta Bar -->
				<div class="flex flex-wrap items-center gap-3 text-sm text-slate-500 dark:text-slate-400 mb-6 pb-6 border-b border-slate-200 dark:border-slate-700">
					<div class="flex items-center gap-2 font-bold">
						<i data-lucide="calendar" class="w-4 h-4"></i>
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
							<?php echo esc_html( get_the_date() ); ?>
						</time>
					</div>
					
					<?php
					$categories = get_the_category();
					if ( $categories ) :
						$category = $categories[0];
					?>
						<div class="flex items-center gap-2 font-bold">
							<i data-lucide="folder" class="w-4 h-4"></i>
							<a href="<?php echo esc_url( get_category_link( $category->term_id ) ); ?>" class="hover:text-primary-600 transition-colors">
								<?php echo esc_html( $category->name ); ?>
							</a>
						</div>
					<?php endif; ?>
					
					<?php if ( $store_name ) : ?>
						<div class="flex items-center gap-2 font-bold">
							<i data-lucide="store" class="w-4 h-4"></i>
							<span><?php echo esc_html( $store_name ); ?></span>
						</div>
					<?php endif; ?>
				</div>

				<!-- Coupon Code Box -->
				<?php if ( $coupon_code ) : ?>
					<div class="mb-8 bg-gradient-to-r from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 border-2 border-orange-200 dark:border-orange-800 rounded-2xl p-6">
						<p class="text-sm font-bold text-orange-700 dark:text-orange-400 mb-2 uppercase tracking-wider flex items-center gap-2">
							<i data-lucide="ticket" class="w-4 h-4"></i>
							<?php esc_html_e( 'Use Coupon Code', 'bigtricks' ); ?>
						</p>
						<div class="flex items-center justify-between gap-4">
							<?php if ( $mask_coupon ) : ?>
								<button class="bt-reveal-code text-2xl md:text-3xl font-black text-orange-900 dark:text-orange-300 tracking-widest bg-orange-100 dark:bg-orange-900/40 px-4 py-2 rounded-lg hover:bg-orange-200 dark:hover:bg-orange-900/60 transition-all" data-code="<?php echo esc_attr( $coupon_code ); ?>">
									<span class="masked-code"><?php echo esc_html( str_repeat( '•', min( strlen( $coupon_code ), 8 ) ) ); ?></span>
									<span class="revealed-code hidden"><?php echo esc_html( $coupon_code ); ?></span>
								</button>
								<button class="bt-reveal-action px-6 py-2 rounded-xl font-bold flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white transition-all" data-code="<?php echo esc_attr( $coupon_code ); ?>">
									<i data-lucide="eye" class="w-4 h-4"></i>
									<span class="reveal-text"><?php esc_html_e( 'Reveal', 'bigtricks' ); ?></span>
									<span class="copy-text hidden"><?php esc_html_e( 'Copy', 'bigtricks' ); ?></span>
								</button>
							<?php else : ?>
								<code class="text-2xl md:text-3xl font-black text-orange-900 dark:text-orange-300 tracking-widest">
									<?php echo esc_html( $coupon_code ); ?>
								</code>
								<button class="bt-copy-code px-6 py-2 rounded-xl font-bold flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white transition-all" data-code="<?php echo esc_attr( $coupon_code ); ?>">
									<i data-lucide="copy" class="w-4 h-4"></i>
									<?php esc_html_e( 'Copy', 'bigtricks' ); ?>
								</button>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>

				<!-- Product Features Section -->
				<?php if ( $product_feature ) : ?>
					<div class="mb-8 bg-blue-50 dark:bg-transparent rounded-2xl p-6 border border-blue-100 dark:border-slate-700">
						<h2 class="text-xl font-black text-slate-900 dark:text-slate-100 mb-4 flex items-center gap-2">
							<i data-lucide="list-checks" class="w-5 h-5 text-blue-500 dark:text-blue-400"></i>
							<?php esc_html_e( 'Product Features', 'bigtricks' ); ?>
						</h2>
						<div class="prose prose-slate dark:prose-invert max-w-none">
							<?php echo wp_kses_post( $product_feature ); ?>
						</div>
					</div>
				<?php endif; ?>

				<!-- Post Content -->
				<?php if ( get_the_content() ) : ?>
					<div class="prose prose-lg prose-slate max-w-none mb-8">
						<?php the_content(); ?>
					</div>
				<?php endif; ?>

				<!-- Price History CTA Block -->
				<?php if ( $store_name ) : ?>
					<div class="mb-8">
						<a href="<?php echo esc_url( home_url( '/price-history/?store=' . urlencode( strtolower( str_replace( ' ', '-', $store_name ) ) ) . '&pid=' . $post_id ) ); ?>" class="block bg-transparent border-2 border-purple-200 dark:border-slate-700 rounded-2xl p-6 hover:shadow-lg dark:hover:shadow-slate-900/30 transition-all group hover:bg-purple-50/50 dark:hover:bg-slate-800/50">
							<div class="flex items-center justify-between gap-4">
								<div class="flex items-center gap-4">
									<div class="w-12 h-12 bg-purple-500 dark:bg-purple-600 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform">
										<i data-lucide="trending-up" class="w-6 h-6 text-white"></i>
									</div>
									<div>
										<h3 class="text-lg font-black text-slate-900 dark:text-slate-100 mb-1"><?php esc_html_e( 'Track Price History', 'bigtricks' ); ?></h3>
										<p class="text-sm text-slate-600 dark:text-slate-400"><?php esc_html_e( 'See historical prices and get the best deal alerts', 'bigtricks' ); ?></p>
									</div>
								</div>
								<i data-lucide="chevron-right" class="w-6 h-6 text-purple-500 dark:text-purple-400 group-hover:translate-x-1 transition-transform"></i>
							</div>
						</a>
					</div>
				<?php endif; ?>

				<!-- Tags -->
				<?php $tags = get_the_tags(); if ( $tags ) : ?>
					<div class="mt-8 pt-6 border-t border-slate-200 flex flex-wrap gap-2">
						<?php foreach ( $tags as $tag ) : ?>
							<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="bg-slate-100 text-slate-600 text-xs font-bold px-3 py-1.5 rounded-full hover:bg-primary-100 hover:text-primary-700 transition-colors">
								#<?php echo esc_html( $tag->name ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<!-- Disclaimer -->
				<?php if ( $disclaimer ) : ?>
					<div class="mt-8 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg p-6">
						<h3 class="text-sm font-black text-yellow-800 mb-2 flex items-center gap-2 uppercase">
							<i data-lucide="alert-triangle" class="w-4 h-4"></i>
							<?php esc_html_e( 'Important Note', 'bigtricks' ); ?>
						</h3>
						<div class="text-sm text-yellow-900">
							<?php echo wp_kses_post( $disclaimer ); ?>
						</div>
					</div>
				<?php endif; ?>

				<!-- Comments -->
				<?php if ( comments_open() || get_comments_number() ) : ?>
					<div id="comments" class="mt-8">
						<?php comments_template(); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Custom Deal Sidebar -->
	<aside class="lg:w-80 shrink-0 space-y-6">
		<?php
		// Latest Deals Widget
		get_template_part( 'template-parts/widget-latest-deals' );
		
		// Top Stores Widget
		get_template_part( 'template-parts/widget-top-stores' );
		?>

		<?php get_template_part( 'template-parts/widget-follow-us' ); ?>
	</aside>
	</div><!-- /.lg:flex -->

	<?php
	$related_args = array(
		'post_type'      => 'deal',
		'posts_per_page' => 4,
		'post__not_in'   => array( $post_id ),
		'orderby'        => 'date',
		'order'          => 'DESC',
	);
	
	// Try to get deals from same store first
	if ( $stores && ! is_wp_error( $stores ) ) {
		$related_args['tax_query'] = array(
			array(
				'taxonomy' => 'store',
				'field'    => 'term_id',
				'terms'    => $stores[0]->term_id,
			),
		);
	} elseif ( $categories ) {
		$related_args['cat'] = $categories[0]->term_id;
	}
	
	$related_query = new WP_Query( $related_args );
	
	// Fallback to recent deals if no related found
	if ( ! $related_query->have_posts() ) {
		$related_args = array(
			'post_type'      => 'deal',
			'posts_per_page' => 4,
			'post__not_in'   => array( $post_id ),
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
		$related_query = new WP_Query( $related_args );
	}
	?>

	<?php if ( $related_query->have_posts() ) : ?>
		<div class="mt-12">
			<!-- Header Section (Like Screenshot 2) -->
			<div class="flex items-center justify-between mb-6">
				<h2 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white flex items-center gap-3">
					<span class="inline-block w-1.5 h-8 bg-primary-600 rounded-full"></span>
					<?php esc_html_e( 'Related Loot', 'bigtricks' ); ?>
				</h2>
				<a href="<?php echo esc_url( home_url( '/deals/' ) ); ?>" class="text-primary-600 hover:text-primary-700 font-bold text-sm flex items-center gap-1">
					<?php esc_html_e( 'View All Deals', 'bigtricks' ); ?>
					<i data-lucide="arrow-right" class="w-4 h-4"></i>
				</a>
			</div>

			<!-- Deals Grid -->
			<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
				<?php while ( $related_query->have_posts() ) : $related_query->the_post(); 
					$rel_post_id = get_the_ID();
					
					// Meta fields for related deal (correct field names)
					$rel_offer_thumbnail_url = get_post_meta( $rel_post_id, '_btdeals_offer_thumbnail_url', true );
					$rel_product_thumbnail_url = get_post_meta( $rel_post_id, '_btdeals_product_thumbnail_url', true );
					$rel_sale_price = floatval( get_post_meta( $rel_post_id, '_btdeals_offer_sale_price', true ) );
					$rel_old_price = floatval( get_post_meta( $rel_post_id, '_btdeals_offer_old_price', true ) );
					$rel_discount = intval( get_post_meta( $rel_post_id, '_btdeals_discount', true ) );
					$rel_discount_tag = get_post_meta( $rel_post_id, '_btdeals_discount_tag', true );
					$rel_is_expired = (bool) get_post_meta( $rel_post_id, '_btdeals_is_expired', true );
					
					// Thumbnail priority: offer_thumbnail_url > product_thumbnail_url > featured_image > store_logo
					$rel_thumb = '';
					if ( $rel_offer_thumbnail_url ) {
						$rel_thumb = $rel_offer_thumbnail_url;
					} elseif ( $rel_product_thumbnail_url ) {
						$rel_thumb = $rel_product_thumbnail_url;
					} elseif ( has_post_thumbnail( $rel_post_id ) ) {
						$rel_thumb = get_the_post_thumbnail_url( $rel_post_id, 'medium' );
					} else {
						// Try store logo
						$rel_store_terms = get_the_terms( $rel_post_id, 'store' );
						if ( $rel_store_terms && ! is_wp_error( $rel_store_terms ) ) {
							$rel_store_logo = get_term_meta( $rel_store_terms[0]->term_id, 'thumb_image', true );
							if ( $rel_store_logo && is_numeric( $rel_store_logo ) ) {
								$rel_store_logo = wp_get_attachment_image_url( (int) $rel_store_logo, 'medium' );
							}
							if ( $rel_store_logo ) {
								$rel_thumb = $rel_store_logo;
							}
						}
					}
					
					// Calculate discount if not in meta
					if ( ! $rel_discount && $rel_old_price > 0 && $rel_sale_price > 0 ) {
						$rel_discount = intval( round( ( ( $rel_old_price - $rel_sale_price ) / $rel_old_price ) * 100 ) );
					}
					
					// Get store
					$rel_store_terms = get_the_terms( $rel_post_id, 'store' );
					$rel_store_name = '';
					$rel_store_logo = '';
					if ( ! empty( $rel_store_terms ) && ! is_wp_error( $rel_store_terms ) ) {
						$rel_store_name = $rel_store_terms[0]->name;
						$rel_store_logo = get_term_meta( $rel_store_terms[0]->term_id, 'thumb_image', true );
						if ( $rel_store_logo && is_numeric( $rel_store_logo ) ) {
							$rel_store_logo = wp_get_attachment_image_url( (int) $rel_store_logo, 'thumbnail' );
						}
					}
					
					// Get category
					$rel_cats = get_the_category( $rel_post_id );
					$rel_cat_name = '';
					if ( ! empty( $rel_cats ) ) {
						$rel_cat_name = $rel_cats[0]->name;
					}
					
					// Relative time
					$rel_time_ago = bigtricks_time_ago( get_the_time( 'U', $rel_post_id ) );
				?>
					<a href="<?php the_permalink(); ?>" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300 group block<?php echo $rel_is_expired ? ' opacity-60' : ''; ?>">
						<div class="relative h-32 sm:h-40 bg-slate-50 dark:bg-slate-800 flex items-center justify-center p-3 sm:p-4">
							<?php if ( $rel_thumb ) : ?>
								<img src="<?php echo esc_url( $rel_thumb ); ?>" alt="<?php the_title_attribute(); ?>" class="max-w-full max-h-full w-auto h-auto object-contain group-hover:scale-110 transition-transform duration-300" loading="lazy">
							<?php endif; ?>
							<?php if ( $rel_is_expired ) : ?>
							<div class="absolute top-3 right-3 bg-slate-500 text-white font-black text-xs px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1">
								<i data-lucide="x-circle" class="w-3 h-3"></i>
								Expired
							</div>
							<?php elseif ( $rel_discount_tag ) : ?>
							<div class="absolute top-3 left-3 bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 font-black text-xs px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1">
								<i data-lucide="flame" class="w-3 h-3"></i>
								<?php echo esc_html( $rel_discount_tag ); ?>
							</div>
							<?php endif; ?>
							<?php if ( $rel_discount > 0 ) : ?>
							<div class="absolute top-3 right-3 bg-red-500 text-white font-black text-xs px-3 py-1.5 rounded-full shadow-lg flex items-center gap-1">
								<i data-lucide="percent" class="w-3 h-3"></i>
								<?php echo esc_html( $rel_discount ); ?>% OFF
							</div>
						<?php endif; ?>
					</div>
					<div class="p-4">
						<h3 class="font-bold text-slate-900 dark:text-white text-sm line-clamp-2 mb-3 group-hover:text-primary-600 transition-colors min-h-[40px]">
							<?php the_title(); ?>
						</h3>
						
						<!-- Meta Info -->
						<div class="flex flex-wrap items-center gap-2 mb-3 text-xs">
							<!-- Category -->
							<?php if ( $rel_cat_name ) : ?>
							<span class="inline-flex items-center gap-1 px-2 py-1 bg-primary-50 text-primary-700 rounded-md font-bold">
								<i data-lucide="tag" class="w-3 h-3"></i>
								<?php echo esc_html( $rel_cat_name ); ?>
							</span>
							<?php endif; ?>
							
							<!-- Store -->
							<?php if ( $rel_store_name ) : ?>
							<span class="inline-flex items-center gap-1 px-2 py-1 bg-slate-100 text-slate-700 rounded-md font-bold">
								<?php if ( $rel_store_logo ) : ?>
								<img src="<?php echo esc_url( $rel_store_logo ); ?>" alt="<?php echo esc_attr( $rel_store_name ); ?>" class="w-3 h-3 object-contain">
								<?php else : ?>
								<i data-lucide="store" class="w-3 h-3"></i>
								<?php endif; ?>
								<?php echo esc_html( $rel_store_name ); ?>
							</span>
							<?php endif; ?>
							
							<!-- Time -->
							<span class="inline-flex items-center gap-1 px-2 py-1 bg-slate-100 text-slate-500 rounded-md font-bold">
								<i data-lucide="clock" class="w-3 h-3"></i>
								<?php echo esc_html( $rel_time_ago ); ?>
							</span>
						</div>
							<?php if ( $rel_sale_price > 0 ) : ?>
								<div class="space-y-1">
									<div class="text-xl font-black text-emerald-600">
										₹<?php echo esc_html( number_format( $rel_sale_price ) ); ?>
									</div>
									<?php if ( $rel_old_price > 0 && $rel_old_price > $rel_sale_price ) : ?>
										<div class="flex items-center gap-2">
											<span class="text-xs text-slate-500 font-bold">MRP:</span>
											<span class="text-sm text-slate-400 line-through">
												₹<?php echo esc_html( number_format( $rel_old_price ) ); ?>
											</span>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</div>
					</a>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			</div>
		</div>
	<?php endif; ?>

	<?php endwhile; ?>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
	// Reveal coupon code functionality
	document.querySelectorAll('.bt-reveal-action').forEach(btn => {
		let isRevealed = false;
		btn.addEventListener('click', function(e) {
			e.preventDefault();
			const code = this.dataset.code;
			
			if (!isRevealed) {
				// First click: Reveal the code
				const parent = this.parentElement;
				const revealBtn = parent.querySelector('.bt-reveal-code');
				const masked = revealBtn.querySelector('.masked-code');
				const revealed = revealBtn.querySelector('.revealed-code');
				const revealText = this.querySelector('.reveal-text');
				const copyText = this.querySelector('.copy-text');
				const icon = this.querySelector('[data-lucide]');
				
				masked.classList.add('hidden');
				revealed.classList.remove('hidden');
				revealText.classList.add('hidden');
				copyText.classList.remove('hidden');
				icon.setAttribute('data-lucide', 'copy');
				lucide.createIcons({ nodes: [revealBtn] });
				
				isRevealed = true;
			} else {
				// Subsequent clicks: Copy the code
				navigator.clipboard.writeText(code).then(() => {
					const self = this;
					const originalHTML = self.innerHTML;
					self.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> Copied!';
					lucide.createIcons({ nodes: [self] });
					setTimeout(() => {
						self.innerHTML = originalHTML;
						lucide.createIcons({ nodes: [self] });
					}, 2000);
				}).catch(err => {
					console.error('Failed to copy:', err);
					alert('Failed to copy code. Please copy manually: ' + code);
				});
			}
		});
	});

	// Copy coupon code button
	document.querySelector('.bt-copy-code')?.addEventListener('click', function() {
		const code = this.dataset.code;
		const copyBtn = this;
		navigator.clipboard.writeText(code).then(() => {
			const originalHTML = copyBtn.innerHTML;
			copyBtn.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> Copied!';
			lucide.createIcons({ nodes: [copyBtn] });
			setTimeout(() => {
				copyBtn.innerHTML = originalHTML;
				lucide.createIcons({ nodes: [copyBtn] });
			}, 2000);
		});
	});

	// Countdown timer
	const countdownElem = document.getElementById('countdown-timer');
	if (countdownElem) {
		const expiryTimestamp = parseInt(countdownElem.dataset.expiry) * 1000;
		
		const updateCountdown = () => {
			const now = new Date().getTime();
			const distance = expiryTimestamp - now;
			
			if (distance < 0) {
				countdownElem.innerHTML = '<div class="col-span-4 text-red-600 font-black text-center">Deal Expired</div>';
				return;
			}
			
			const days = Math.floor(distance / (1000 * 60 * 60 * 24));
			const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			const seconds = Math.floor((distance % (1000 * 60)) / 1000);
			
			const daysElem = countdownElem.querySelector('.countdown-days');
			const hoursElem = countdownElem.querySelector('.countdown-hours');
			const minutesElem = countdownElem.querySelector('.countdown-minutes');
			const secondsElem = countdownElem.querySelector('.countdown-seconds');
			
			if (daysElem) daysElem.textContent = String(days).padStart(2, '0');
			if (hoursElem) hoursElem.textContent = String(hours).padStart(2, '0');
			if (minutesElem) minutesElem.textContent = String(minutes).padStart(2, '0');
			if (secondsElem) secondsElem.textContent = String(seconds).padStart(2, '0');
		};
		
		updateCountdown();
		setInterval(updateCountdown, 1000);
	}
});
</script>

<?php get_footer(); ?>

<?php
/**
 * Single Credit Card Template
 * Modern design with Tailwind CSS following theme standards
 * 
 * Custom post type: credit-card (credit-card-manager plugin)
 * Displays comprehensive credit card details
 *
 * @package Bigtricks
 */

get_header();
?>

<?php get_template_part( 'template-parts/share-popover' ); ?>

<main class="max-w-[1400px] mx-auto px-4 py-6 md:py-8" id="main-content">
	<?php
	if ( ! function_exists( 'ccm_get_meta_array' ) ) {
		function ccm_get_meta_array( int $card_id, string $meta_key ): array {
			$raw = get_post_meta( $card_id, $meta_key, true );
			if ( ! is_array( $raw ) ) {
				return [];
			}
			return array_values(
				array_filter(
					$raw,
					static function ( $item ) {
						return is_scalar( $item ) && '' !== trim( (string) $item );
					}
				)
			);
		}
	}

	if ( ! function_exists( 'ccm_format_currency' ) ) {
		function ccm_format_currency( $amount ) {
			if ( empty( $amount ) || $amount === 'N/A' ) {
				return 'N/A';
			}
			$numeric = floatval( $amount );
			if ( $numeric == 0 ) {
				return 'Free';
			}
			return '₹' . number_format( $numeric );
		}
	}

	if ( ! function_exists( 'ccm_get_card_faqs' ) ) {
		function ccm_get_card_faqs( int $card_id ): array {
			$custom_faqs = get_post_meta( $card_id, 'custom_faqs', true );
			if ( ! is_array( $custom_faqs ) ) {
				return [];
			}
			return array_values(
				array_filter(
					$custom_faqs,
					static function ( $faq ) {
						return is_array( $faq )
							&& ! empty( trim( (string) ( $faq['question'] ?? '' ) ) )
							&& ! empty( trim( (string) ( $faq['answer'] ?? '' ) ) );
					}
				)
			);
		}
	}
	?>

	<?php while ( have_posts() ) : the_post();
		$post_id = get_the_ID();

		// Get all meta fields (no prefix for credit cards)
		$card_name           = get_the_title();
		$card_image          = has_post_thumbnail() ? get_the_post_thumbnail_url( $post_id, 'large' ) : '';
		$rating              = (float) get_post_meta( $post_id, 'rating', true );
		$review_count        = (int) get_post_meta( $post_id, 'review_count', true );
		$annual_fee          = floatval( get_post_meta( $post_id, 'annual_fee', true ) );
		$joining_fee         = floatval( get_post_meta( $post_id, 'joining_fee', true ) );
		$welcome_bonus       = sanitize_text_field( get_post_meta( $post_id, 'welcome_bonus', true ) ) ?: 'N/A';
		$reward_type         = sanitize_text_field( get_post_meta( $post_id, 'reward_type', true ) );
		$reward_conversion_rate = sanitize_text_field( get_post_meta( $post_id, 'reward_conversion_rate', true ) );
		$reward_conversion_value = floatval( get_post_meta( $post_id, 'reward_conversion_value', true ) );
		$cashback_rate       = sanitize_text_field( get_post_meta( $post_id, 'cashback_rate', true ) ) ?: 'N/A';
		$credit_limit        = sanitize_text_field( get_post_meta( $post_id, 'credit_limit', true ) ) ?: 'N/A';
		$processing_time     = sanitize_text_field( get_post_meta( $post_id, 'processing_time', true ) ) ?: '7-10 days';
		$min_income          = sanitize_text_field( get_post_meta( $post_id, 'min_income', true ) ) ?: 'N/A';
		$min_age             = sanitize_text_field( get_post_meta( $post_id, 'min_age', true ) ) ?: '21-65 years';
		$apply_link          = esc_url( get_post_meta( $post_id, 'apply_link', true ) ) ?: '#';
		$featured            = (bool) get_post_meta( $post_id, 'featured', true );
		$trending            = (bool) get_post_meta( $post_id, 'trending', true );
		
		// Array fields
		$pros                = ccm_get_meta_array( $post_id, 'pros' );
		$cons                = ccm_get_meta_array( $post_id, 'cons' );
		$best_for            = ccm_get_meta_array( $post_id, 'best_for' );
		$features            = ccm_get_meta_array( $post_id, 'features' );
		$rewards             = ccm_get_meta_array( $post_id, 'rewards' );
		$fees                = ccm_get_meta_array( $post_id, 'fees' );
		$eligibility         = ccm_get_meta_array( $post_id, 'eligibility' );
		$documents           = ccm_get_meta_array( $post_id, 'documents' );
		
		// Taxonomies
		$bank_terms     = get_the_terms( $post_id, 'store' );
		$bank_name      = ! empty( $bank_terms ) && ! is_wp_error( $bank_terms ) ? $bank_terms[0]->name : '';
		$bank_link      = ! empty( $bank_terms ) && ! is_wp_error( $bank_terms ) ? get_term_link( $bank_terms[0] ) : '';
		
		$network_terms  = get_the_terms( $post_id, 'network-type' );
		$network_type   = ! empty( $network_terms ) && ! is_wp_error( $network_terms ) ? $network_terms[0]->name : 'Visa/Mastercard';
		$network_name   = $network_type; // Alias for compatibility
		
		$category_terms = get_the_terms( $post_id, 'card-category' );
		$category_name  = ! empty( $category_terms ) && ! is_wp_error( $category_terms ) ? $category_terms[0]->name : '';
	?>

	<!-- Hero Section -->
	<div class="bg-white dark:bg-slate-900 rounded-3xl shadow-soft overflow-hidden mb-8">
		<div class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-slate-900 p-6 md:p-8">
			<?php 
			// Breadcrumbs
			if ( function_exists( 'bigtricks_breadcrumbs' ) ) {
				bigtricks_breadcrumbs();
			}
			?>

			<!-- Share Button -->
			<div class="flex justify-end mb-6">
				<button onclick="openSharePopover(event)" class="inline-flex items-center gap-2 bg-white/50 hover:bg-white dark:bg-slate-800/50 dark:hover:bg-slate-800 px-3 py-2 rounded-xl transition-colors">
					<i data-lucide="share-2" class="w-5 h-5 text-slate-600 dark:text-slate-400"></i>
				</button>
			</div>

			<!-- Main Hero Content -->
			<div class="flex flex-col lg:flex-row gap-8 items-center lg:items-start">
				
				<!-- Card Image -->
				<div class="w-full lg:w-[200px] shrink-0">
					<div class="aspect-[1.6/1] flex items-center justify-center relative overflow-hidden group">
						<?php if ( $card_image ) : ?>
							<img src="<?php echo esc_url( $card_image ); ?>" alt="<?php echo esc_attr( $card_name ); ?>" class="w-full h-full object-contain transition-transform duration-500 group-hover:scale-105" loading="eager">
						<?php else : ?>
							<div class="text-slate-900 dark:text-white font-bold text-xl text-center">
								<?php echo esc_html( $card_name ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<!-- Card Info -->
				<div class="flex-1 w-full lg:w-auto">
					<!-- Card Title -->
				<h1 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white mb-2 leading-tight">
					<?php the_title(); ?>
				</h1>
				
				<?php if ( $bank_name ) : ?>
				<div class="mb-4">
					<a href="<?php echo esc_url( $bank_link ); ?>" class="inline-flex items-center gap-2 text-sm text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-bold transition-colors">
						<i data-lucide="building-2" class="w-4 h-4"></i>
						<?php echo esc_html( $bank_name ); ?>
					</a>
				</div>
				<?php endif; ?>
					<!-- Rating & Badges -->
					<?php if ( $rating > 0 ) : ?>
						<div class="flex flex-wrap items-center gap-3 mb-6">
							<div class="flex items-center gap-1">
								<?php
								$full_stars = floor( $rating );
								$has_half = ( $rating - $full_stars ) >= 0.5;
								for ( $i = 0; $i < $full_stars; $i++ ) : ?>
									<i data-lucide="star" class="w-5 h-5 text-amber-400 fill-current"></i>
								<?php endfor; 
								if ( $has_half ) : ?>
									<i data-lucide="star-half" class="w-5 h-5 text-amber-400 fill-current"></i>
								<?php endif;
								for ( $i = ceil( $rating ); $i < 5; $i++ ) : ?>
									<i data-lucide="star" class="w-5 h-5 text-slate-300 dark:text-slate-600"></i>
								<?php endfor; ?>
							</div>
							<span class="text-lg font-black text-slate-900 dark:text-white"><?php echo esc_html( number_format( $rating, 1 ) ); ?></span>
							<?php if ( $review_count > 0 ) : ?>
								<span class="text-slate-500 dark:text-slate-400">(<span class="font-bold"><?php echo esc_html( number_format( $review_count ) ); ?></span> reviews)</span>
							<?php endif; ?>
							
							<?php if ( $featured ) : ?>
								<span class="bg-amber-400 text-white font-black text-xs px-3 py-1.5 rounded-full shadow-sm inline-flex items-center gap-1.5">
									<i data-lucide="trophy" class="w-3.5 h-3.5"></i>
									Featured
								</span>
							<?php endif; ?>
							<?php if ( $trending ) : ?>
								<span class="bg-orange-500 text-white font-black text-xs px-3 py-1.5 rounded-full shadow-sm inline-flex items-center gap-1.5">
									<i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
									Trending
								</span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					
					<!-- Key Highlights Grid -->
					<div class="grid grid-cols-2 gap-4 mb-6">
						<div class="bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-200 dark:border-slate-700">
							<div class="text-xs text-slate-600 dark:text-slate-400 font-medium mb-1">Annual Fee</div>
							<div class="text-lg font-black text-slate-900 dark:text-white">
								<?php echo esc_html( ccm_format_currency( $annual_fee ) ); ?>
							</div>
						</div>
						
						<div class="bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-200 dark:border-slate-700">
							<div class="text-xs text-slate-600 dark:text-slate-400 font-medium mb-1">Welcome Bonus</div>
							<div class="text-lg font-black text-primary-600 dark:text-primary-400 line-clamp-2"><?php echo esc_html( $welcome_bonus ); ?></div>
						</div>
						
						<div class="bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-200 dark:border-slate-700">
							<div class="text-xs text-slate-600 dark:text-slate-400 font-medium mb-1">Cashback</div>
							<div class="text-lg font-black text-emerald-600 dark:text-emerald-400"><?php echo esc_html( $cashback_rate ); ?></div>
						</div>
						
						<div class="bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm rounded-xl p-4 border border-slate-200 dark:border-slate-700">
							<div class="text-xs text-slate-600 dark:text-slate-400 font-medium mb-1">Processing Time</div>
							<div class="text-lg font-black text-blue-600 dark:text-blue-400"><?php echo esc_html( $processing_time ); ?></div>
						</div>
					</div>

					<!-- Apply Now CTA -->
					<a href="<?php echo esc_url( $apply_link ); ?>" target="_blank" rel="noopener noreferrer nofollow" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-8 py-4 rounded-2xl text-lg font-black shadow-xl shadow-primary-200 dark:shadow-none transition-all hover:scale-105 active:scale-95">
						<i data-lucide="credit-card" class="w-5 h-5"></i>
						Apply Now
						<i data-lucide="external-link" class="w-4 h-4"></i>
					</a>
				</div>
			</div>
		</div>
	</div>

	<!-- Quick Overview Section -->
	<section id="quick-overview" class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-8 mb-6">
		<h2 class="text-2xl font-black text-slate-900 dark:text-white mb-6 flex items-center gap-3">
			<i data-lucide="layout-grid" class="w-6 h-6 text-purple-600"></i>
			Quick Overview
		</h2>
		<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
			<div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
				<div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
					<i data-lucide="wallet" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
				</div>
				<div>
					<div class="text-sm text-slate-600 dark:text-slate-400 font-medium">Annual Fee</div>
					<div class="text-base font-black text-slate-900 dark:text-white">
						<span class="<?php echo $annual_fee == 0 ? 'text-emerald-600 dark:text-emerald-400' : ''; ?>"><?php echo esc_html( ccm_format_currency( $annual_fee ) ); ?></span>
					</div>
				</div>
			</div>
			
			<div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
				<div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
					<i data-lucide="plus-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
				</div>
				<div>
					<div class="text-sm text-slate-600 dark:text-slate-400 font-medium">Joining Fee</div>
					<div class="text-base font-black text-slate-900 dark:text-white">
						<span class="<?php echo $joining_fee == 0 ? 'text-emerald-600 dark:text-emerald-400' : ''; ?>"><?php echo esc_html( ccm_format_currency( $joining_fee ) ); ?></span>
					</div>
				</div>
			</div>
			
			<div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
				<div class="p-2 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
					<i data-lucide="gift" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
				</div>
				<div>
					<div class="text-sm text-slate-600 dark:text-slate-400 font-medium">Welcome Bonus</div>
					<div class="text-base font-black text-slate-900 dark:text-white line-clamp-2"><?php echo esc_html( $welcome_bonus ); ?></div>
				</div>
			</div>
			
			<?php if ( ! empty( $reward_type ) ) : ?>
			<div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
				<div class="p-2 bg-amber-100 dark:bg-amber-900/30 rounded-lg">
					<i data-lucide="sparkles" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
				</div>
				<div>
					<div class="text-sm text-slate-600 dark:text-slate-400 font-medium">Reward Type</div>
					<div class="text-base font-black text-slate-900 dark:text-white"><?php echo esc_html( $reward_type ); ?></div>
				</div>
			</div>
			<?php endif; ?>
			
			<div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
				<div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
					<i data-lucide="percent" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
				</div>
				<div>
					<div class="text-sm text-slate-600 dark:text-slate-400 font-medium">Cashback Rate</div>
					<div class="text-base font-black text-emerald-600 dark:text-emerald-400"><?php echo esc_html( $cashback_rate ); ?></div>
				</div>
			</div>
			
			<div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
				<div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg">
					<i data-lucide="badge-check" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
				</div>
				<div>
					<div class="text-sm text-slate-600 dark:text-slate-400 font-medium">Credit Limit</div>
					<div class="text-base font-black text-slate-900 dark:text-white"><?php echo esc_html( $credit_limit ); ?></div>
				</div>
			</div>
			
			<div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
				<div class="p-2 bg-teal-100 dark:bg-teal-900/30 rounded-lg">
					<i data-lucide="banknote" class="w-5 h-5 text-teal-600 dark:text-teal-400"></i>
				</div>
				<div>
					<div class="text-sm text-slate-600 dark:text-slate-400 font-medium">Min. Income</div>
					<div class="text-base font-black text-slate-900 dark:text-white"><?php echo esc_html( $min_income ); ?></div>
				</div>
			</div>
			
			<div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
				<div class="p-2 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg">
					<i data-lucide="user-check" class="w-5 h-5 text-cyan-600 dark:text-cyan-400"></i>
				</div>
				<div>
					<div class="text-sm text-slate-600 dark:text-slate-400 font-medium">Age Requirement</div>
					<div class="text-base font-black text-slate-900 dark:text-white"><?php echo esc_html( $min_age ); ?></div>
				</div>
			</div>
			
			<div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl">
				<div class="p-2 bg-pink-100 dark:bg-pink-900/30 rounded-lg">
					<i data-lucide="network" class="w-5 h-5 text-pink-600 dark:text-pink-400"></i>
				</div>
				<div>
					<div class="text-sm text-slate-600 dark:text-slate-400 font-medium">Network</div>
					<div class="text-base font-black text-slate-900 dark:text-white"><?php echo esc_html( $network_name ); ?></div>
				</div>
			</div>
		</div>
	</section>
	
	<!-- Perks & Tradeoffs (Pros & Cons) Section - Full Width -->
	<?php if ( ! empty( $pros ) || ! empty( $cons ) ) : ?>
	<section id="pros-cons" class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8 mb-6">
		<h2 class="text-2xl font-black text-slate-900 dark:text-white mb-6 flex items-center gap-3">
			<i data-lucide="list-checks" class="w-6 h-6 text-purple-600"></i>
			Perks &amp; Tradeoffs
		</h2>
		<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
			<!-- Pros -->
			<?php if ( ! empty( $pros ) ) : ?>
			<div>
				<h3 class="text-lg font-black text-emerald-600 dark:text-emerald-400 mb-4 flex items-center gap-2">
					<i data-lucide="thumbs-up" class="w-5 h-5"></i>
					Pros
				</h3>
				<ul class="space-y-3">
					<?php foreach ( $pros as $pro ) : ?>
						<li class="flex items-start gap-3 p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg border border-emerald-200 dark:border-emerald-800">
							<i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5"></i>
							<span class="text-sm text-slate-700 dark:text-slate-300"><?php echo esc_html( $pro ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>
			
			<!-- Cons -->
			<?php if ( ! empty( $cons ) ) : ?>
			<div>
				<h3 class="text-lg font-black text-red-600 dark:text-red-400 mb-4 flex items-center gap-2">
					<i data-lucide="thumbs-down" class="w-5 h-5"></i>
					Cons
				</h3>
				<ul class="space-y-3">
					<?php foreach ( $cons as $con ) : ?>
						<li class="flex items-start gap-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
							<i data-lucide="x-circle" class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0 mt-0.5"></i>
							<span class="text-sm text-slate-700 dark:text-slate-300"><?php echo esc_html( $con ); ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>
		</div>
	</section>
	<?php endif; ?>
	
	<!-- About The Card (Blog Content) with Sidebar -->
	<?php if ( get_the_content() ) : ?>
	<div class="lg:flex lg:gap-8 mb-6">
		<!-- Blog Content -->
		<div class="flex-1">
			<section id="about-the-card" class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8">
				<h2 class="text-2xl font-black text-slate-900 dark:text-white mb-6 flex items-center gap-3">
					<i data-lucide="file-text" class="w-6 h-6 text-purple-600"></i>
					About The Card
				</h2>
				<div class="prose prose-slate dark:prose-invert max-w-none prose-headings:font-black prose-a:text-purple-600 hover:prose-a:text-purple-700 prose-img:rounded-xl">
					<?php 
					the_content();
					wp_link_pages( array(
						'before' => '<div class="page-links">',
						'after'  => '</div>',
					) );
					?>
				</div>
			</section>
		</div>

		<!-- Sidebar -->
		<aside class="lg:w-[340px] shrink-0 space-y-8 mt-8 lg:mt-0">
			<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
				<?php dynamic_sidebar( 'sidebar-1' ); ?>
			<?php else : ?>
				<?php
				get_template_part( 'template-parts/widget-top-stores' );
				get_template_part( 'template-parts/widget-follow-us' );
				get_template_part( 'template-parts/widget-telegram-cta' );
				?>
			<?php endif; ?>
		</aside>
	</div>
	<?php endif; ?>
	
	<!-- Full Width Sections -->
	<div class="space-y-6">

		<!-- Features Section (if exists) -->
		<?php if ( ! empty( $features ) ) : ?>
		<section id="features" class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8">
			<h2 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white mb-6 flex items-center gap-3">
				<i data-lucide="gift" class="w-6 h-6 text-purple-600"></i>
				Key Features
			</h2>
				
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<?php foreach ( $features as $feature ) : 
						$feature_title = is_array( $feature ) ? ( $feature['title'] ?? $feature ) : $feature;
						$feature_desc = is_array( $feature ) ? ( $feature['description'] ?? '' ) : '';
					?>
						<div class="flex items-start gap-4 bg-slate-50 dark:bg-slate-800 p-4 rounded-xl">
							<div class="flex-shrink-0 w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
								<i data-lucide="gift" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
							</div>
							<div class="flex-1">
								<h4 class="font-bold text-slate-900 dark:text-white mb-1"><?php echo esc_html( $feature_title ); ?></h4>
								<?php if ( $feature_desc ) : ?>
									<p class="text-sm text-slate-600 dark:text-slate-400"><?php echo esc_html( $feature_desc ); ?></p>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>
		
	<!-- Best For Section (Chips) -->
		<?php if ( ! empty( $best_for ) ) : ?>
		<section id="best-for" class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8">
				<h2 class="text-2xl font-black text-slate-900 dark:text-white mb-6 flex items-center gap-3">
					<i data-lucide="target" class="w-6 h-6 text-purple-600"></i>
					Best For
				</h2>
				<div class="flex flex-wrap gap-3">
					<?php foreach ( $best_for as $item ) : ?>
						<span class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-purple-100 to-blue-100 dark:from-purple-900/30 dark:to-blue-900/30 border border-purple-200 dark:border-purple-700 rounded-full text-sm font-black text-purple-900 dark:text-purple-200 shadow-sm">
							<i data-lucide="check" class="w-4 h-4"></i>
							<?php echo esc_html( $item ); ?>
						</span>
					<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>

	<!-- Rewards Section -->
		<?php if ( ! empty( $rewards ) || ! empty( $cashback_rate ) || ! empty( $welcome_bonus ) ) : ?>
		<section id="rewards" class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8">
				<h2 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white mb-6">Reward Program</h2>
				
				<!-- Reward Type Info -->
				<?php if ( ! empty( $reward_type ) || ! empty( $reward_conversion_rate ) ) : ?>
				<div class="bg-primary-50 dark:bg-primary-900/20 border-l-4 border-primary-600 p-4 rounded-lg mb-6">
					<h3 class="text-lg font-bold text-primary-900 dark:text-primary-400 mb-2">
						<?php echo ! empty( $reward_type ) ? esc_html( $reward_type ) . ' ' : ''; ?>Rewards
					</h3>
					<?php if ( ! empty( $reward_conversion_rate ) ) : ?>
						<div class="text-sm text-slate-700 dark:text-slate-300 mb-1">
							<span class="font-bold">Conversion Rate:</span> <?php echo esc_html( $reward_conversion_rate ); ?>
						</div>
					<?php endif; ?>
					<?php if ( $reward_conversion_value > 0 ) : ?>
						<div class="text-sm text-emerald-700 dark:text-emerald-400 font-bold">
							<span class="opacity-75">Value:</span> 
							<?php 
							if ( $reward_conversion_value == 1 ) {
								echo '1:1 (Full Value)';
							} else {
								echo esc_html( number_format( $reward_conversion_value, 2 ) ) . ' Rupees per unit';
							}
							?>
						</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				
				<?php if ( ! empty( $rewards ) ) : ?>
					<div class="space-y-3">
						<?php foreach ( $rewards as $reward ) : 
							$reward_category = is_array( $reward ) ? ( $reward['category'] ?? 'Reward Category' ) : 'Reward Category';
							$reward_rate = is_array( $reward ) ? ( $reward['rate'] ?? $reward ) : $reward;
							$reward_desc = is_array( $reward ) ? ( $reward['description'] ?? '' ) : '';
						?>
							<div class="flex items-center justify-between bg-slate-50 dark:bg-slate-800 p-4 rounded-xl">
								<div class="flex-1">
									<div class="font-bold text-slate-900 dark:text-white"><?php echo esc_html( $reward_category ); ?></div>
									<?php if ( $reward_desc ) : ?>
										<div class="text-sm text-slate-600 dark:text-slate-400 mt-1"><?php echo esc_html( $reward_desc ); ?></div>
									<?php endif; ?>
								</div>
								<div class="text-right">
									<div class="text-lg font-black text-emerald-600 dark:text-emerald-400">
										<?php echo esc_html( $reward_rate ); ?>
									</div>
									<?php if ( ! empty( $reward_type ) && strtolower( $reward_type ) !== 'cashback' ) : ?>
										<div class="text-xs text-slate-500 dark:text-slate-400"><?php echo esc_html( $reward_type ); ?></div>
									<?php endif; ?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<div class="space-y-3">
						<div class="flex items-center justify-between bg-slate-50 dark:bg-slate-800 p-4 rounded-xl">
							<div class="font-bold text-slate-900 dark:text-white">Default Reward Rate</div>
							<div class="text-right">
								<div class="text-lg font-black text-emerald-600 dark:text-emerald-400">
									<?php echo esc_html( $cashback_rate ); ?>
								</div>
								<?php if ( ! empty( $reward_type ) ) : ?>
									<div class="text-xs text-slate-500 dark:text-slate-400">in <?php echo esc_html( $reward_type ); ?></div>
								<?php endif; ?>
							</div>
						</div>
						<div class="flex items-center justify-between bg-slate-50 dark:bg-slate-800 p-4 rounded-xl">
							<div class="font-bold text-slate-900 dark:text-white">Welcome Bonus</div>
							<div class="text-lg font-black text-emerald-600 dark:text-emerald-400">
								<?php echo esc_html( $welcome_bonus ); ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
			</section>
			<?php endif; ?>

		<!-- Fees Section -->
			<?php if ( ! empty( $fees ) || $annual_fee > 0 || $joining_fee > 0 ) : ?>
			<section id="fees" class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8">
				<h2 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white mb-6">Fees & Charges</h2>
				
				<div class="space-y-3">
					<?php if ( ! empty( $fees ) ) : ?>
						<?php foreach ( $fees as $fee ) : 
							$fee_type = is_array( $fee ) ? ( $fee['type'] ?? 'Fee' ) : 'Fee';
							$fee_amount = is_array( $fee ) ? ( $fee['amount'] ?? $fee ) : $fee;
						?>
							<div class="flex items-center justify-between bg-slate-50 dark:bg-slate-800 p-4 rounded-xl">
								<div class="font-bold text-slate-900 dark:text-white"><?php echo esc_html( $fee_type ); ?></div>
								<div class="text-lg font-black text-red-600 dark:text-red-400">
									<?php echo esc_html( $fee_amount ); ?>
								</div>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<div class="flex items-center justify-between bg-slate-50 dark:bg-slate-800 p-4 rounded-xl">
							<div class="font-bold text-slate-900 dark:text-white">Joining Fee</div>
							<div class="text-lg font-black <?php echo $joining_fee > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400'; ?>">
								<?php echo esc_html( ccm_format_currency( $joining_fee ) ); ?>
							</div>
						</div>
						<div class="flex items-center justify-between bg-slate-50 dark:bg-slate-800 p-4 rounded-xl">
							<div class="font-bold text-slate-900 dark:text-white">Annual Fee</div>
							<div class="text-lg font-black <?php echo $annual_fee > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400'; ?>">
								<?php echo esc_html( ccm_format_currency( $annual_fee ) ); ?>
							</div>
						</div>
					<?php endif; ?>
			</div>
		</section>
		<?php endif; ?>

	<!-- Eligibility Section -->
		<?php if ( ! empty( $eligibility ) || ! empty( $documents ) ) : ?>
		<section id="eligibility" class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8">
				<h2 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white mb-6">Eligibility Criteria</h2>
				
				<div class="space-y-3 mb-8">
					<?php if ( ! empty( $eligibility ) ) : ?>
						<?php foreach ( $eligibility as $criterion ) : 
							$criteria_label = is_array( $criterion ) ? ( $criterion['criteria'] ?? 'Criteria' ) : 'Criteria';
							$criteria_value = is_array( $criterion ) ? ( $criterion['value'] ?? $criterion ) : $criterion;
						?>
							<div class="flex items-center justify-between bg-slate-50 dark:bg-slate-800 p-4 rounded-xl">
								<div class="font-bold text-slate-900 dark:text-white"><?php echo esc_html( $criteria_label ); ?></div>
								<div class="text-sm font-bold text-slate-700 dark:text-slate-300"><?php echo esc_html( $criteria_value ); ?></div>
							</div>
						<?php endforeach; ?>
					<?php else : ?>
						<div class="flex items-center justify-between bg-slate-50 dark:bg-slate-800 p-4 rounded-xl">
							<div class="font-bold text-slate-900 dark:text-white">Minimum Income</div>
							<div class="text-sm font-bold text-slate-700 dark:text-slate-300"><?php echo esc_html( $min_income ); ?></div>
						</div>
						<div class="flex items-center justify-between bg-slate-50 dark:bg-slate-800 p-4 rounded-xl">
							<div class="font-bold text-slate-900 dark:text-white">Age Requirement</div>
							<div class="text-sm font-bold text-slate-700 dark:text-slate-300">21-65 years</div>
						</div>
						<div class="flex items-center justify-between bg-slate-50 dark:bg-slate-800 p-4 rounded-xl">
							<div class="font-bold text-slate-900 dark:text-white">Employment</div>
							<div class="text-sm font-bold text-slate-700 dark:text-slate-300">Salaried/Self-employed</div>
						</div>
					<?php endif; ?>
				</div>
				
				<?php if ( ! empty( $documents ) ) : ?>
				<div>
					<h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
						<i data-lucide="file-text" class="w-5 h-5 text-primary-600"></i>
						Required Documents
					</h3>
					<ul class="space-y-2">
						<?php foreach ( $documents as $document ) : ?>
							<li class="flex items-start gap-2 text-slate-700 dark:text-slate-300">
								<span class="mt-1">📄</span>
								<span><?php echo esc_html( $document ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</section>
		<?php endif; ?>

	<!-- FAQ Section -->
		<?php
		$card_faqs = ccm_get_card_faqs( $post_id );
		if ( ! empty( $card_faqs ) ) : ?>
		<section id="faq" class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8">
				<h2 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white mb-6">Frequently Asked Questions</h2>

				<div class="space-y-3">
					<?php foreach ( $card_faqs as $index => $faq ) :
						$faq_question = sanitize_text_field( (string) ( $faq['question'] ?? '' ) );
						$faq_answer   = wp_kses_post( (string) ( $faq['answer'] ?? '' ) );
						if ( '' === $faq_question || '' === trim( wp_strip_all_tags( $faq_answer ) ) ) {
							continue;
						}
					?>
						<details class="group bg-slate-50 dark:bg-slate-800 rounded-xl overflow-hidden">
							<summary class="flex items-center justify-between cursor-pointer p-4 font-bold text-slate-900 dark:text-white hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
								<span><?php echo esc_html( $faq_question ); ?></span>
								<i data-lucide="chevron-down" class="w-5 h-5 text-slate-500 transition-transform group-open:rotate-180"></i>
							</summary>
							<div class="px-4 pb-4 text-slate-700 dark:text-slate-300">
								<?php echo $faq_answer; ?>
							</div>
						</details>
					<?php endforeach; ?>
			</div>
		</section>
		<?php endif; ?>

	<!-- Bottom CTA -->
		<section class="bg-gradient-to-br from-primary-600 to-primary-700 rounded-3xl shadow-xl overflow-hidden p-8 md:p-12 text-center">
				<h3 class="text-2xl md:text-3xl font-black text-white mb-3">Ready to Apply for <?php echo esc_html( $card_name ); ?>?</h3>
				<p class="text-primary-100 mb-6 max-w-2xl mx-auto">Join thousands of satisfied customers and start earning rewards today. Apply online in just 5 minutes!</p>
				<a href="<?php echo esc_url( $apply_link ); ?>" target="_blank" rel="nofollow noopener" class="inline-flex items-center gap-2 bg-white text-primary-700 px-8 py-4 rounded-xl font-black text-lg hover:bg-primary-50 transition-all shadow-lg hover:shadow-xl dark:shadow-slate-900/30 dark:hover:shadow-slate-900/50">
					Apply Now - Get Instant Approval
					<i data-lucide="zap" class="w-5 h-5"></i>
			</a>
		</section>

		<!-- Related Articles Section - Full Width -->
		<section id="related-articles" class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8">
			<?php 
			get_template_part( 'template-parts/related-posts', null, [
				'post_id'        => $post_id,
				'category'       => 'credit-card-bill-payment-offers',
				'posts_per_page' => 3,
				'title'          => __( 'Related Articles', 'bigtricks' ),
				'icon'           => 'newspaper',
			] );
			?>
		</section>

		<!-- Similar Cards Section - Full Width -->
		<section id="similar-cards" class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8">
			<div class="flex items-center justify-between mb-6">
				<h2 class="text-2xl font-black text-slate-900 dark:text-white flex items-center gap-3">
					<i data-lucide="credit-card" class="w-6 h-6 text-purple-600"></i>
					Similar Cards
				</h2>
				<a href="<?php echo esc_url( home_url( '/credit-card/' ) ); ?>" class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 font-bold text-sm transition-colors">
					View All
					<i data-lucide="arrow-right" class="w-4 h-4"></i>
				</a>
			</div>

			<?php
			// Build smart query for similar cards
			$similar_args = array(
				'post_type'      => 'credit-card',
				'posts_per_page' => 3,
				'post_status'    => 'publish',
				'post__not_in'   => array( $post_id ),
				'orderby'        => 'meta_value_num',
				'meta_key'       => 'rating',
				'order'          => 'DESC',
			);

			// Try to get cards from same category first
			if ( ! empty( $category_terms ) && ! is_wp_error( $category_terms ) ) {
				$similar_args['tax_query'] = array(
					array(
						'taxonomy' => 'card-category',
						'field'    => 'term_id',
						'terms'    => $category_terms[0]->term_id,
					),
				);
			} elseif ( ! empty( $bank_terms ) && ! is_wp_error( $bank_terms ) ) {
				// If no category, try same bank
				$similar_args['tax_query'] = array(
					array(
						'taxonomy' => 'store',
						'field'    => 'term_id',
						'terms'    => $bank_terms[0]->term_id,
					),
				);
			}

			$similar_cards = get_posts( $similar_args );

			if ( ! empty( $similar_cards ) ) : ?>
				<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
					<?php foreach ( $similar_cards as $card ) : ?>
						<?php get_template_part( 'template-parts/card-credit-card-compact', null, [ 'post_id' => $card->ID ] ); ?>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<p class="text-slate-500 dark:text-slate-400 text-center py-8">No similar credit cards found.</p>
			<?php endif; ?>
		</section>

		<!-- Explore Card Categories Section - Full Width -->
		<section id="more-categories" class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8">
				<h2 class="text-2xl font-black text-slate-900 dark:text-white mb-6 flex items-center gap-3">
					<i data-lucide="grid-3x3" class="w-6 h-6 text-purple-600"></i>
					Explore Card Categories
				</h2>

				<?php
				$card_categories = get_terms( array(
					'taxonomy'   => 'card-category',
					'hide_empty' => true,
					'number'     => 6,
				) );

				if ( ! empty( $card_categories ) && ! is_wp_error( $card_categories ) ) : ?>
					<div class="grid grid-cols-2 md:grid-cols-4 gap-4">
						<?php foreach ( $card_categories as $category ) : ?>
							<a href="<?php echo esc_url( get_term_link( $category ) ); ?>" class="flex flex-col items-center justify-center p-6 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-purple-300 dark:hover:border-purple-700 hover:shadow-lg transition-all group">
								<i data-lucide="tag" class="w-8 h-8 text-purple-600 dark:text-purple-400 mb-3 group-hover:scale-110 transition-transform"></i>
								<span class="text-sm font-black text-slate-900 dark:text-white text-center mb-1"><?php echo esc_html( $category->name ); ?></span>
								<span class="text-xs text-slate-500 dark:text-slate-400"><?php echo esc_html( $category->count ); ?> cards</span>
							</a>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<p class="text-slate-500 dark:text-slate-400 text-center py-8">No card categories found.</p>
			<?php endif; ?>
		</section>

	<!-- Comment Section -->
		<?php if ( comments_open() || get_comments_number() ) : ?>
		<section id="comments" class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 md:p-8">
			<?php comments_template(); ?>
		</section>
		<?php endif; ?>
	</div>

	<?php endwhile; ?>

</main>

<?php get_footer(); ?>

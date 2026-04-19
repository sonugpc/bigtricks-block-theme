<?php
/**
 * Single Post (Deal detail)
 *
 * @package Bigtricks
 */

get_header();
?>

<?php get_template_part( 'template-parts/share-popover' ); ?>

<main class="max-w-[1400px] mx-auto px-4 py-6 md:py-8 flex flex-col lg:flex-row gap-8 flex-1 w-full" id="main-content">

	<!-- Single Post Column -->
	<div class="flex-1 min-w-0 w-full overflow-hidden">
		<?php while ( have_posts() ) :
			the_post();
			$post_id           = get_the_ID();
			$post_type         = get_post_type();
			$thumb_url         = bigtricks_get_thumbnail_url( $post_id, 'large' );
			$cat_obj           = get_the_category();
			$cat_name          = ! empty( $cat_obj ) ? $cat_obj[0]->name : '';
			$cat_link          = ! empty( $cat_obj ) ? get_category_link( $cat_obj[0]->term_id ) : '';
			$comments_num      = (int) get_comments_number();
			$is_regular_post   = ( $post_type === 'post' );

			// Deal-specific meta (bigtricks-deals plugin)
			$deal_offer_url = $post_type === 'deal'
				? esc_url( (string) get_post_meta( $post_id, '_btdeals_offer_url', true ) ) : '';
			$deal_coupon    = $post_type === 'deal'
				? sanitize_text_field( (string) get_post_meta( $post_id, '_btdeals_coupon_code', true ) ) : '';

			// Referral-specific meta (referral-code-plugin)
			$referral_code  = $post_type === 'referral-codes'
				? sanitize_text_field( (string) get_post_meta( $post_id, 'referral_code', true ) ) : '';
			$referral_link  = $post_type === 'referral-codes'
				? esc_url( (string) get_post_meta( $post_id, 'referral_link', true ) ) : '';
			$signup_bonus   = $post_type === 'referral-codes'
				? sanitize_text_field( (string) get_post_meta( $post_id, 'signup_bonus', true ) ) : '';

			// Credit card meta (credit-card-manager plugin)
			$apply_link     = $post_type === 'credit-card'
				? esc_url( (string) get_post_meta( $post_id, 'apply_link', true ) ) : '';

			// Fake share count (stable per post)
			$fake_share = ( ( $post_id * 137 + 50 ) % 951 ) + 50;
			?>

		<article class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden w-full relative" itemscope itemtype="https://schema.org/Article">

			<!-- Hero Image -->
			<?php if ( bigtricks_option( 'bt_show_featured_image', '1' ) === '1' ) : ?>
			<div class="w-full h-64 sm:h-[400px] bg-slate-50 flex items-center justify-center p-8 border-b border-slate-100 relative group overflow-hidden">
				<img
					src="<?php echo esc_url( $thumb_url ); ?>"
					alt="<?php the_title_attribute(); ?>"
					class="max-h-full max-w-full object-contain mix-blend-multiply transition-transform duration-500 group-hover:scale-105"
					loading="eager"
					decoding="async"
					itemprop="image"
				>
				<?php if ( $post_type === 'deal' ) : ?>
				<div class="absolute top-6 left-6 bg-red-50 text-red-600 font-black text-sm px-4 py-2 rounded-full shadow-md flex items-center gap-2 border border-red-100 backdrop-blur-sm">
					<i data-lucide="flame" class="w-4 h-4 fill-current"></i> Deal
				</div>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<!-- Content Area -->
			<div class="p-6 sm:p-10 lg:p-12">
				<?php bigtricks_breadcrumbs(); ?>

				<!-- Title -->
				<h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white leading-tight mb-4 break-words" itemprop="name">
					<?php the_title(); ?>
				</h1>

				<!-- Unified Meta + Share Row -->
				<div class="flex flex-wrap items-center gap-2 sm:gap-3 text-slate-500 font-bold text-sm mb-8 pb-8 border-b border-slate-100 dark:border-slate-800">
					<div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
						<i data-lucide="clock" class="w-4 h-4"></i>
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>" itemprop="datePublished">
							<?php echo esc_html( get_the_date( 'M j, Y' ) ); ?>
						</time>
					</div>
					<a href="#comments" class="flex items-center gap-1.5 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-primary-50 hover:text-primary-600 transition-colors" aria-label="<?php esc_attr_e( 'View comments', 'bigtricks' ); ?>">
						<i data-lucide="message-square" class="w-4 h-4"></i>
						<?php echo esc_html( $comments_num ); ?>
					</a>
					<?php if ( bigtricks_option( 'bt_show_social_share', '1' ) === '1' ) : ?>
					<button onclick="openSharePopover(event)" class="ml-auto inline-flex items-center gap-1.5 bg-primary-50 hover:bg-primary-100 text-primary-700 px-3 py-1.5 rounded-lg text-xs font-black border border-primary-200 transition-colors" aria-label="<?php esc_attr_e( 'Share this post', 'bigtricks' ); ?>">
						<i data-lucide="share-2" class="w-3.5 h-3.5"></i>
						<?php esc_html_e( 'Share', 'bigtricks' ); ?>
						<span class="text-primary-300">|</span>
						<?php echo esc_html( number_format_i18n( $fake_share ) ); ?>
					</button>
					<?php else : ?>
					<span class="ml-auto flex items-center gap-1.5 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200 text-slate-500" aria-label="<?php esc_attr_e( 'Share count', 'bigtricks' ); ?>">
						<i data-lucide="trending-up" class="w-4 h-4"></i>
						<?php echo esc_html( number_format_i18n( $fake_share ) ); ?>
					</span>
					<?php endif; ?>
				</div>

				<!-- Primary CTA (large) — hidden for regular blog posts -->
				<?php if ( ! $is_regular_post ) : ?>
				<div class="mb-10 flex flex-col sm:flex-row gap-4">
					<?php echo wp_kses_post( bigtricks_deal_cta_button( $post_id, 'large' ) ); ?>
				</div>
				<?php endif; ?>

				<!-- Referral Code Box -->
				<?php if ( $referral_code ) : ?>
				<div class="mb-10 bg-emerald-50 border border-emerald-200 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
					<div>
						<p class="text-sm font-bold text-emerald-700 mb-1 uppercase tracking-wider">
							<?php esc_html_e( 'Referral Code', 'bigtricks' ); ?>
						</p>
						<code class="text-2xl font-black text-emerald-900 tracking-widest">
							<?php echo esc_html( $referral_code ); ?>
						</code>
						<?php if ( $signup_bonus ) : ?>
						<p class="text-xs text-emerald-600 font-bold mt-1"><?php echo esc_html( $signup_bonus ); ?></p>
						<?php endif; ?>
					</div>
					<button
						class="bt-copy-code px-6 py-3 rounded-xl font-black flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white shadow-md shadow-emerald-200 transition-all active:scale-95 border-2 border-emerald-500 shrink-0"
						data-code="<?php echo esc_attr( $referral_code ); ?>"
					>
						<i data-lucide="copy" class="w-4 h-4"></i>
						<?php esc_html_e( 'Copy Code', 'bigtricks' ); ?>
					</button>
				</div>
				<?php endif; ?>

				<!-- Deal Coupon Code Box -->
				<?php if ( $deal_coupon ) : ?>
				<div class="mb-10 bg-orange-50 border border-orange-200 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4">
					<div>
						<p class="text-sm font-bold text-orange-700 mb-1 uppercase tracking-wider">
							<?php esc_html_e( 'Coupon Code', 'bigtricks' ); ?>
						</p>
						<code class="text-2xl font-black text-orange-900 tracking-widest">
							<?php echo esc_html( $deal_coupon ); ?>
						</code>
					</div>
					<button
						class="bt-copy-code px-6 py-3 rounded-xl font-black flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white shadow-md shadow-orange-200 transition-all active:scale-95 border-2 border-orange-500 shrink-0"
						data-code="<?php echo esc_attr( $deal_coupon ); ?>"
					>
						<i data-lucide="copy" class="w-4 h-4"></i>
						<?php esc_html_e( 'Copy Code', 'bigtricks' ); ?>
					</button>
				</div>
				<?php endif; ?>

				<!-- Post Content -->
				<div
					class="prose prose-lg prose-slate max-w-none prose-img:rounded-3xl prose-img:shadow-md prose-a:text-primary-600 hover:prose-a:text-primary-800 prose-headings:font-black prose-p:leading-relaxed break-words"
					itemprop="articleBody"
				>
					<?php the_content(); ?>
				</div>

				<!-- Tags -->
				<?php $tags = get_the_tags(); if ( $tags ) : ?>
				<div class="mt-10 pt-8 border-t border-slate-100 flex flex-wrap gap-2">
					<?php foreach ( $tags as $tag ) : ?>
					<a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" class="bg-slate-100 text-slate-600 text-xs font-bold px-3 py-1.5 rounded-full hover:bg-primary-100 hover:text-primary-700 transition-colors">
						#<?php echo esc_html( $tag->name ); ?>
					</a>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>

				<!-- Related Posts -->
				<?php 
				$cat_obj = get_the_category( $post_id );
				get_template_part( 'template-parts/related-posts', null, [
					'post_id'        => $post_id,
					'category'       => ! empty( $cat_obj ) ? $cat_obj[0]->slug : '',
					'posts_per_page' => 3,
					'title'          => __( 'Related Deals', 'bigtricks' ),
					'icon'           => 'zap',
				] );
				?>

				<!-- Comments -->
				<?php if ( bigtricks_option( 'bt_show_comments', '1' ) === '1' && ( comments_open() || get_comments_number() ) ) : ?>
				<div id="comments" class="mt-12">
					<?php comments_template(); ?>
				</div>
				<?php endif; ?>

			</div><!-- /Content Area -->
		</article>

		<?php endwhile; ?>
	</div><!-- /Single Post Column -->

	<?php get_sidebar(); ?>

</main>

<?php get_footer(); ?>

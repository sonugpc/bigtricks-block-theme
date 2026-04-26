<?php
if ( ! function_exists( 'bigtricks_extract_referral_submission' ) ) {
	/**
	 * Extract code or URL submission from comment meta/content.
	 */
	function bigtricks_extract_referral_submission( $comment, bool $expects_code ): string {
		$stored_value = sanitize_text_field( (string) get_comment_meta( $comment->comment_ID, 'user_referral_code', true ) );

		if ( $expects_code ) {
			if ( $stored_value !== '' ) {
				return $stored_value;
			}

			preg_match( '/(?:Code:|code:)\s*([A-Za-z0-9\-_]+)/i', (string) $comment->comment_content, $matches );
			return isset( $matches[1] ) ? sanitize_text_field( $matches[1] ) : '';
		}

		if ( $stored_value !== '' && wp_http_validate_url( $stored_value ) ) {
			return esc_url_raw( $stored_value );
		}

		preg_match( '#https?://[^\s<>"]+#i', (string) $comment->comment_content, $matches );
		if ( ! empty( $matches[0] ) && wp_http_validate_url( $matches[0] ) ) {
			return esc_url_raw( $matches[0] );
		}

		return '';
	}
}

// Handle referral code/link submission.
if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['referral_code_submission'] ) ) {
	$nonce = sanitize_text_field( wp_unslash( $_POST['referral_nonce'] ?? '' ) );
	if ( wp_verify_nonce( $nonce, 'referral_comment_nonce' ) && is_user_logged_in() ) {
		$requested_post_id = absint( wp_unslash( $_POST['comment_post_ID'] ?? 0 ) );
		$current_post_id   = get_queried_object_id();

		if ( $requested_post_id > 0 && $requested_post_id === $current_post_id && 'referral-codes' === get_post_type( $requested_post_id ) ) {
			$raw_submission   = sanitize_text_field( wp_unslash( $_POST['user_referral_code'] ?? '' ) );
			$comment          = sanitize_textarea_field( wp_unslash( $_POST['comment'] ?? '' ) );
			$expects_code     = '' !== sanitize_text_field( (string) get_post_meta( $requested_post_id, 'referral_code', true ) );
			$normalized_value = '';

			if ( $expects_code ) {
				if ( preg_match( '/^[A-Za-z0-9\-_]+$/', $raw_submission ) ) {
					$normalized_value = $raw_submission;
				}
			} elseif ( wp_http_validate_url( $raw_submission ) ) {
				$normalized_value = esc_url_raw( $raw_submission );
			}

			if ( '' !== $normalized_value ) {
				$current_user = wp_get_current_user();
				$commentdata  = array(
					'comment_post_ID'      => $requested_post_id,
					'comment_content'      => $comment ? $comment : 'User shared a referral code.',
					'comment_type'         => '',
					'comment_author'       => $current_user->display_name,
					'comment_author_email' => $current_user->user_email,
					'user_id'              => $current_user->ID,
				);

				$comment_id = wp_new_comment( $commentdata, true );

				if ( $comment_id && ! is_wp_error( $comment_id ) ) {
					add_comment_meta( $comment_id, 'user_referral_code', $normalized_value, true );
					wp_safe_redirect( get_permalink( $requested_post_id ) . '?submitted=1#user-codes' );
					exit;
				}
			}
		}
	}
}
/**
 * Single Referral Code Template
 * Modern template matching theme design with enhanced UX
 *
 * Custom post type: referral-codes (referral-code-plugin)
 * Drives signup and code copying with community features.
 *
 * @package Bigtricks
 */

get_header();
?>

<?php get_template_part( 'template-parts/share-popover' ); ?>

<main class="max-w-[1400px] mx-auto px-4 py-6 md:py-8" id="main-content">
	<?php get_template_part( 'template-parts/breadcrumb' ); ?>

	<?php while ( have_posts() ) : the_post();
		$post_id = get_the_ID();

		// Get referral data using plugin function
		if ( function_exists( 'rcp_get_referral_data' ) ) {
			$referral_data   = rcp_get_referral_data( $post_id );
			$referral_code   = sanitize_text_field( (string) ( $referral_data['referral_code'] ?? '' ) );
			$referral_link   = esc_url_raw( (string) ( $referral_data['referral_link'] ?? '' ) );
			$signup_bonus    = sanitize_text_field( (string) ( $referral_data['signup_bonus'] ?? '' ) );
			$referral_rewards = sanitize_text_field( (string) ( $referral_data['referral_rewards'] ?? '' ) );
			$app_name        = sanitize_text_field( (string) ( $referral_data['app_name'] ?? '' ) );
			$usage_count     = absint( $referral_data['usage_count'] ?? 0 );
		} else {
			// Fallback to direct meta calls
			$referral_code   = sanitize_text_field( (string) get_post_meta( $post_id, 'referral_code', true ) );
			$referral_link   = esc_url_raw( (string) get_post_meta( $post_id, 'referral_link', true ) );
			$signup_bonus    = sanitize_text_field( (string) get_post_meta( $post_id, 'signup_bonus', true ) );
			$referral_rewards = sanitize_text_field( (string) get_post_meta( $post_id, 'referral_rewards', true ) );
			$app_name        = sanitize_text_field( (string) get_post_meta( $post_id, 'app_name', true ) );
			$usage_count     = absint( get_post_meta( $post_id, 'referral_code_usage_count', true ) );
		}

		if ( ! $app_name ) {
			$app_name = get_the_title();
		}

		$app_logo = get_the_post_thumbnail_url( $post_id, 'thumbnail' );
		$short_description = wp_kses_post( (string) get_post_meta( $post_id, 'short_description', true ) );
		$rcp_faqs = (array) get_post_meta( $post_id, 'rcp_faqs', true );

		$categories = get_the_category();
		$category_name = ! empty( $categories ) ? $categories[0]->name : 'Referral Program';
		$category_url  = ! empty( $categories ) ? get_category_link( (int) $categories[0]->term_id ) : '';
		$expects_code_submission = ! empty( $referral_code );
		$is_submitted            = isset( $_GET['submitted'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['submitted'] ) );
	?>

	<!-- Full Width Hero -->
	<div class="bg-white dark:bg-slate-900 rounded-3xl shadow-soft overflow-hidden mb-8 border-t-4 border-primary-500 max-w-full w-full">
		<div class="p-6 md:p-8 flex flex-col lg:flex-row gap-6 md:gap-8 items-start lg:items-center max-w-full overflow-hidden w-full">
			
			<!-- Left: Logo -->
			<div class="w-24 h-24 md:w-32 md:h-32 shrink-0 bg-white rounded-2xl shadow-sm border border-slate-100 dark:border-slate-800 flex items-center justify-center overflow-hidden p-2 mx-auto lg:mx-0">
				<?php if ( $app_logo ) : ?>
					<img src="<?php echo esc_url( $app_logo ); ?>" alt="<?php echo esc_attr( $app_name ); ?>" class="w-full h-full object-contain">
				<?php else : ?>
					<i data-lucide="smartphone" class="w-10 h-10 text-slate-300"></i>
				<?php endif; ?>
			</div>

			<!-- Middle: Title & Meta -->
			<div class="flex-1 flex flex-col items-center lg:items-start text-center lg:text-left w-full max-w-full overflow-hidden space-y-4">
				<h1 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white leading-tight break-words max-w-full">
					<?php the_title(); ?>
				</h1>
				
				<div class="flex flex-wrap items-center justify-center lg:justify-start gap-3 w-full">
					<?php if ( $category_name ) : ?>
						<?php if ( $category_url && ! is_wp_error( $category_url ) ) : ?>
							<a href="<?php echo esc_url( $category_url ); ?>" class="px-4 py-1.5 border border-primary-500 text-primary-600 dark:text-primary-400 rounded-full text-sm font-bold bg-primary-50/50 dark:bg-primary-900/20 max-w-full truncate hover:underline">
								<?php echo esc_html( $category_name ); ?>
							</a>
						<?php else : ?>
							<div class="px-4 py-1.5 border border-primary-500 text-primary-600 dark:text-primary-400 rounded-full text-sm font-bold bg-primary-50/50 dark:bg-primary-900/20 max-w-full truncate">
								<?php echo esc_html( $category_name ); ?>
							</div>
						<?php endif; ?>
					<?php endif; ?>

					<div class="flex items-center justify-center flex-wrap gap-1.5 px-4 py-1.5 border border-slate-200 dark:border-slate-700 rounded-full text-sm font-bold text-slate-700 dark:text-slate-300 bg-white dark:bg-slate-800">
						<i data-lucide="tag" class="w-4 h-4 text-amber-500"></i>
						<span>4.5/5</span>
						<div class="flex text-amber-500 ml-1">
							<i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
							<i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
							<i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
							<i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
							<i data-lucide="star" class="w-3.5 h-3.5 fill-current"></i>
						</div>
					</div>

					<?php if ( $signup_bonus ) : ?>
						<div class="inline-flex items-center justify-center lg:justify-start gap-2 text-emerald-600 dark:text-emerald-400 font-bold bg-emerald-50 dark:bg-emerald-900/20 px-3 py-1.5 rounded-full text-sm border border-emerald-100 dark:border-emerald-800/50 max-w-full">
							<i data-lucide="gift" class="w-4 h-4 shrink-0"></i>
							<span class="truncate">Bonus: <?php echo esc_html( $signup_bonus ); ?></span>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- Right: Actions -->
			<div class="w-full lg:w-[340px] shrink-0 flex flex-col items-center lg:items-end gap-3 mt-4 lg:mt-0 max-w-full overflow-hidden">
				
				<div class="w-full border-2 border-primary-500 rounded-xl flex items-center justify-between overflow-hidden bg-white dark:bg-slate-900 shadow-sm relative group p-1 pl-4 max-w-full">
					<div class="flex-1 font-black text-[15px] sm:text-lg text-slate-900 dark:text-white tracking-wide truncate max-w-[calc(100%-60px)]">
						<?php echo esc_html( $referral_code ?: 'Direct Link' ); ?>
					</div>
					<?php if ( $referral_code ) : ?>
					<button class="bg-primary-500 hover:bg-primary-600 text-white p-3 transition-colors flex items-center justify-center gap-2 bt-copy-code active:bg-primary-700 rounded-lg shrink-0" data-code="<?php echo esc_attr( $referral_code ); ?>" title="Copy Code">
						<i data-lucide="copy" class="w-5 h-5"></i>
					</button>
					<?php endif; ?>
				</div>

				<?php if ( $usage_count > 0 && function_exists( 'rcp_format_usage_count' ) ) : ?>
				<div class="w-full flex items-center justify-center lg:justify-end gap-2 text-sm font-medium text-slate-500 dark:text-slate-400 mb-1 pr-1">
					<i data-lucide="users" class="w-4 h-4"></i>
					<span><?php echo rcp_format_usage_count( $usage_count ); ?> people used this code</span>
				</div>
				<?php endif; ?>
				
				<?php if ( $referral_link ) : ?>
					<a href="<?php echo esc_url( $referral_link ); ?>" target="_blank" rel="nofollow noopener" class="w-full bg-primary-500 hover:bg-primary-600 text-white py-3.5 rounded-xl font-bold text-center transition-colors shadow-md hover:shadow-lg dark:shadow-slate-900/30 dark:hover:shadow-slate-900/50 text-lg flex items-center justify-center gap-2 active:scale-[0.98]">
					<span>Visit <?php echo esc_html( $app_name ?: 'Website' ); ?></span>
				</a>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<!-- Two Column Layout: Main Content + Sidebar -->
	<div class="flex flex-col lg:flex-row lg:gap-8 xl:gap-12 mt-8">
		<!-- Main Details Column -->
		<div class="flex-1 lg:max-w-[900px] space-y-8 w-full overflow-hidden">

			<!-- Short Description Section -->
			<?php if ( ! empty( $short_description ) ) : ?>
			<div class="bg-white dark:bg-slate-900 rounded-3xl shadow-soft p-6 md:p-8 overflow-hidden prose dark:prose-invert prose-emerald max-w-none">
				<div class="text-lg font-bold mb-8 p-6 bg-emerald-50 dark:bg-emerald-900/10 rounded-2xl border-l-4 border-emerald-500 text-slate-800 dark:text-slate-300 not-prose">
					<?php echo apply_filters( 'the_content', $short_description ); ?>
				</div>
			</div>
			<?php endif; ?>

			<!-- Content Section -->
			<div class="bg-white dark:bg-slate-900 rounded-3xl shadow-soft p-6 md:p-8 overflow-hidden prose dark:prose-invert prose-emerald max-w-none">
				<h2 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white mb-6 flex items-center gap-3 not-prose">
					<div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
						<i data-lucide="file-text" class="w-5 h-5 text-slate-600 dark:text-slate-400"></i>
					</div>
					About <?php echo esc_html( $app_name ); ?> Referral Program
				</h2>

				<div class="entry-content">
					<?php the_content(); ?>
				</div>
			</div>

			<!-- FAQs Section -->
			<?php if ( ! empty( $rcp_faqs ) && is_array( $rcp_faqs ) && ! empty( $rcp_faqs[0]['question'] ) ) : ?>
			<div class="bg-white dark:bg-slate-900 rounded-3xl shadow-soft p-6 md:p-8">
				<h3 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white mb-8 flex items-center gap-3">
					<div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
						<i data-lucide="help-circle" class="w-6 h-6 text-blue-600 dark:text-blue-500"></i>
					</div>
					Frequently Asked Questions
				</h3>

				<div class="space-y-4" itemscope itemtype="https://schema.org/FAQPage">
					<?php foreach ( $rcp_faqs as $index => $faq ) :
						if ( empty( $faq['question'] ) || empty( $faq['answer'] ) ) continue;

						$question = str_replace(
							array( '{{app_name}}', '{{referral_code}}' ),
							array( $app_name, $referral_code ),
							$faq['question']
						);
						$answer = str_replace(
							array( '{{app_name}}', '{{referral_code}}', '{{signup_bonus}}', '{{referral_link}}', '{{referral_rewards}}' ),
							array( $app_name, $referral_code, $signup_bonus, $referral_link, $referral_rewards ),
							$faq['answer']
						);
					?>
					<details class="group border border-slate-200 dark:border-slate-700 rounded-2xl bg-slate-50 dark:bg-slate-800/30 open:bg-white dark:open:bg-slate-800 transition-colors shadow-sm" <?php echo $index === 0 ? 'open' : ''; ?> itemscope itemprop="mainEntity" itemtype="https://schema.org/Question">
						<summary class="flex items-center justify-between cursor-pointer p-6 font-bold text-slate-900 dark:text-white marker:content-none text-lg select-none" itemprop="name">
							<span class="pr-6"><?php echo esc_html( $question ); ?></span>
							<span class="shrink-0 bg-white dark:bg-slate-700 w-8 h-8 rounded-full flex items-center justify-center border border-slate-200 dark:border-slate-600 transition-transform duration-300 group-open:-rotate-180 shadow-sm">
								<i data-lucide="chevron-down" class="w-5 h-5 text-slate-500 dark:text-slate-400"></i>
							</span>
						</summary>
						<div class="referral-faq-answer p-6 pt-0 text-slate-600 dark:text-slate-400 leading-relaxed border-t border-slate-100 dark:border-slate-700 mt-2 prose dark:prose-invert" itemscope itemprop="acceptedAnswer" itemtype="https://schema.org/Answer">
							<div itemprop="text">
								<?php echo wp_kses_post( wpautop( $answer ) ); ?>
							</div>
						</div>
					</details>
					<?php endforeach; ?>
				</div>
			</div>
			<?php endif; ?>


			   <!-- Combined Official & User Codes Section with Tabs -->
			   <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-soft p-6 md:p-8 overflow-hidden">
				   <div class="flex overflow-x-auto border-b border-slate-200 dark:border-slate-700 mb-8 whitespace-nowrap scrollbar-hide">
					   <button class="tab-btn active flex-1 py-4 px-6 text-center font-black text-slate-700 dark:text-slate-300 border-b-2 border-primary-500 dark:border-primary-400 transition-colors shrink-0" onclick="showTab('official-codes', this)">
						   <?php echo $referral_code ? 'Official Code' : 'Official Link'; ?>
					   </button>
					   <button class="tab-btn flex-1 py-4 px-6 text-center font-black text-slate-500 dark:text-slate-500 border-b-2 border-transparent transition-colors shrink-0" onclick="showTab('user-codes', this)">
						   <?php echo $referral_code ? 'User Submitted Codes' : 'User Submitted Links'; ?>
					   </button>
				   </div>
				   <!-- Official Code Tab -->
				   <div id="official-codes" class="tab-content active">
					   <div class="space-y-6">
						   <?php if ( $referral_code || $referral_link ) : ?>
						   <div class="bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-900/20 dark:to-yellow-900/20 rounded-2xl p-6 border border-amber-200 dark:border-amber-800/50">
							   <div class="flex items-start gap-4">
								   <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/40 rounded-full flex items-center justify-center shrink-0">
									   <i data-lucide="crown" class="w-6 h-6 text-amber-600 dark:text-amber-500"></i>
								   </div>
								   <div class="flex-1">
									   <div class="flex items-center gap-2 mb-2">
										   <h4 class="font-black text-amber-900 dark:text-amber-400"><?php echo $referral_code ? 'Official Code' : 'Official Link'; ?></h4>
										   <span class="text-sm text-amber-700 dark:text-amber-500"><?php echo get_the_modified_date( 'M j, Y' ); ?></span>
									   </div>
									   <?php if ( $referral_code ) : ?>
									   <div class="bg-white dark:bg-slate-900 rounded-xl p-4 mb-4 border-2 border-dashed border-amber-300 dark:border-amber-700">
										   <div class="flex items-center justify-between">
											   <div class="font-mono font-black text-xl text-slate-800 dark:text-amber-400 break-all"><?php echo esc_html( $referral_code ); ?></div>
											   <button class="bt-copy-code ml-4 bg-amber-500 hover:bg-amber-600 text-white p-3 rounded-xl transition-colors" data-code="<?php echo esc_attr( $referral_code ); ?>">
												   <i data-lucide="copy" class="w-5 h-5"></i>
											   </button>
										   </div>
									   </div>
									   <?php endif; ?>
									   <?php if ( $referral_link ) : ?>
									   <div class="bg-white dark:bg-slate-900 rounded-xl p-4 mb-4 border-2 border-dashed border-amber-300 dark:border-amber-700">
										   <div class="flex items-center justify-between">
											   <a href="<?php echo esc_url( $referral_link ); ?>" target="_blank" rel="noopener" class="font-mono font-black text-xl text-blue-600 dark:text-blue-400 break-all hover:underline"><?php echo esc_html( $referral_link ); ?></a>
											   <a href="<?php echo esc_url( $referral_link ); ?>" target="_blank" rel="noopener" class="ml-4 bg-amber-500 hover:bg-amber-600 text-white p-3 rounded-xl transition-colors">
												   <i data-lucide="external-link" class="w-5 h-5"></i>
											   </a>
										   </div>
									   </div>
									   <?php endif; ?>
									   <p class="text-amber-800 dark:text-amber-300 leading-relaxed">
										   Official referral <?php echo $referral_code ? 'code' : 'link'; ?> with guaranteed signup bonus and best rates.
									   </p>
								   </div>
							   </div>
						   </div>
						   <?php else : ?>
						   <div class="text-center py-12">
							   <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
								   <i data-lucide="building" class="w-8 h-8 text-slate-400"></i>
							   </div>
							   <h4 class="text-xl font-black text-slate-900 dark:text-white mb-2">No official codes available</h4>
							   <p class="text-slate-600 dark:text-slate-400">Check back later for official referral codes.</p>
						   </div>
						   <?php endif; ?>
					   </div>
				   </div>
				   <!-- User Submitted Codes Tab -->
				   <div id="user-codes" class="tab-content" style="display:none;">
					   <div class="space-y-6">
						   <?php
						   if ( function_exists( 'rcp_get_cached_comments' ) ) {
							   $comments = rcp_get_cached_comments( get_the_ID() );
						   } else {
							   $comments = get_comments( array(
								   'post_id' => get_the_ID(),
								   'status'  => 'approve',
								   'number'  => 10,
								   'order'   => 'DESC',
							   ) );
						   }

						   // Find if there are any user-submitted codes
						   $has_user_codes = false;
						   if ( $comments ) {
							   foreach ( $comments as $comment ) {
								   $user_referral_code = bigtricks_extract_referral_submission( $comment, $expects_code_submission );
								   if ( ! empty( $user_referral_code ) ) {
									   $has_user_codes = true;
									   break;
								   }
							   }
						   }

						   // Render user codes list
						   if ( $comments && $has_user_codes ) :
							   foreach ( $comments as $comment ) :
								   $user_referral_code = bigtricks_extract_referral_submission( $comment, $expects_code_submission );
								   if ( empty( $user_referral_code ) ) continue;
								   $user_usage_count = (int) get_comment_meta( $comment->comment_ID, 'user_code_usage_count', true );
						   ?>
						   <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-6 border border-slate-200 dark:border-slate-700">
							   <div class="flex items-start justify-between mb-4">
								   <div class="flex items-center gap-3">
									   <div class="w-10 h-10 bg-slate-200 dark:bg-slate-700 rounded-full flex items-center justify-center">
										   <i data-lucide="user" class="w-5 h-5 text-slate-600 dark:text-slate-400"></i>
									   </div>
									   <div>
										   <div class="font-bold text-slate-900 dark:text-white"><?php echo esc_html( $comment->comment_author ); ?></div>
										   <div class="text-sm text-slate-500 dark:text-slate-400"><?php echo date( 'M j, Y', strtotime( $comment->comment_date ) ); ?></div>
									   </div>
								   </div>
							   </div>
							   <?php if ( $user_referral_code ) : ?>
							   <div class="bg-white dark:bg-slate-900 rounded-xl p-4 mb-4 border-2 border-dashed border-emerald-300 dark:border-emerald-700">
								   <div class="flex items-center justify-between">
									   <div class="flex-1">
										   <?php if ( $expects_code_submission ) : ?>
										   <div class="font-mono font-black text-lg text-slate-800 dark:text-emerald-400 break-all"><?php echo esc_html( $user_referral_code ); ?></div>
										   <?php else : ?>
										   <a href="<?php echo esc_url( $user_referral_code ); ?>" target="_blank" rel="noopener" class="font-mono font-black text-lg text-blue-600 dark:text-blue-400 break-all hover:underline"><?php echo esc_html( $user_referral_code ); ?></a>
										   <?php endif; ?>
									   </div>
									   <?php if ( $expects_code_submission ) : ?>
									   <button class="bt-copy-code ml-4 bg-emerald-500 hover:bg-emerald-600 text-white p-3 rounded-xl transition-colors" data-code="<?php echo esc_attr( $user_referral_code ); ?>" data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>">
										   <i data-lucide="copy" class="w-5 h-5"></i>
									   </button>
									   <?php else : ?>
									   <a href="<?php echo esc_url( $user_referral_code ); ?>" target="_blank" rel="noopener" class="ml-4 bg-blue-500 hover:bg-blue-600 text-white p-3 rounded-xl transition-colors">
										   <i data-lucide="external-link" class="w-5 h-5"></i>
									   </a>
									   <?php endif; ?>
								   </div>
								   <?php if ( $user_usage_count > 0 && function_exists( 'rcp_format_usage_count' ) ) : ?>
								   <div class="flex items-center gap-2 mt-3 text-sm text-slate-600 dark:text-slate-400">
									   <i data-lucide="users" class="w-4 h-4"></i>
									   <span><?php echo rcp_format_usage_count( $user_usage_count ); ?> people used this code</span>
								   </div>
								   <?php endif; ?>
							   </div>
							   <?php endif; ?>
							   <?php if ( $comment->comment_content ) : ?>
							   <div class="text-slate-600 dark:text-slate-400 leading-relaxed">
								   <?php echo wp_kses_post( $comment->comment_content ); ?>
							   </div>
							   <?php endif; ?>
						   </div>
						   <?php endforeach; ?>
					   <?php else : ?>
						   <div class="text-center py-12">
							   <div class="w-16 h-16 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-4">
								   <i data-lucide="message-square" class="w-8 h-8 text-slate-400"></i>
							   </div>
							   <h4 class="text-xl font-black text-slate-900 dark:text-white mb-2">No codes shared yet</h4>
							   <p class="text-slate-600 dark:text-slate-400">Be the first to share your referral code with the community!</p>
						   </div>
					   <?php endif; ?>
					   </div>
				   </div>
			   </div>

			<!-- Submit Referral Code Section -->
			<?php get_template_part( 'template-parts/referral-submit', null, [ 'referral_code' => $referral_code, 'is_submitted' => $is_submitted ] ); ?>

			<!-- Comments -->
			<?php
			$comments_open_filter = '__return_true';
			add_filter( 'comments_open', $comments_open_filter, 99 );

			$referral_comments_filter = static function ( $comments, $filter_post_id = 0 ) use ( $post_id, $expects_code_submission ) {
				if ( absint( $filter_post_id ) !== absint( $post_id ) ) {
					return $comments;
				}

				return array_values(
					array_filter(
						$comments,
						static function ( $comment ) use ( $expects_code_submission ) {
							return '' === bigtricks_extract_referral_submission( $comment, $expects_code_submission );
						}
					)
				);
			};
			add_filter( 'comments_array', $referral_comments_filter, 10, 2 );
			
			// Always show comments area for referral codes
			echo '<div class="bg-white dark:bg-slate-900 rounded-3xl shadow-soft p-6 md:p-8 mt-8" id="commentsArea">';
			echo '<h3 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white mb-6 flex items-center gap-3"><div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center"><i data-lucide="message-square" class="w-5 h-5 text-blue-600 dark:text-blue-500"></i></div> Discussion</h3>';
			echo '<p class="text-slate-600 dark:text-slate-400 mb-8 text-sm">Have a question or want to share your experience? Drop a comment below. <br><strong>Note:</strong> Please use the dedicated submission form above to share your referral codes!</p>';
			comments_template();
			remove_filter( 'comments_open', $comments_open_filter, 99 );
			remove_filter( 'comments_array', $referral_comments_filter, 10 );
			echo '</div>';
			?>
		</div>

		<!-- Sidebar -->
		<aside class="w-full lg:w-80 xl:w-[320px] shrink-0 space-y-6 mt-8 lg:mt-0">
			<?php
			// Latest Deals Widget
			get_template_part( 'template-parts/widget-more-referral-codes' );

			// Top Stores Widget
			get_template_part( 'template-parts/widget-top-stores' );
			?>

			<?php get_template_part( 'template-parts/widget-follow-us' ); ?>
		</aside>
	</div>

	<?php endwhile; ?>
</main>

<?php get_footer(); ?>

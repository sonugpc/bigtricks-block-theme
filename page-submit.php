<?php
/**
 * Template Name: Submit Content
 * Template Post Type: page
 *
 * Frontend submission hub for Offer posts, Deal posts, and Referral Code posts.
 *
 * @package Bigtricks
 */

declare( strict_types=1 );

if ( ! is_user_logged_in() ) {
	wp_safe_redirect( bigtricks_get_login_url( get_permalink() ) );
	exit;
}

get_header();

$current_type = isset( $_GET['type'] ) ? sanitize_text_field( wp_unslash( $_GET['type'] ) ) : 'offer'; // phpcs:ignore WordPress.Security.NonceVerification
$allowed      = [ 'offer', 'deal', 'referral' ];
if ( ! in_array( $current_type, $allowed, true ) ) {
	$current_type = 'offer';
}

$submit_error = isset( $_GET['submit_error'] ) ? sanitize_text_field( wp_unslash( $_GET['submit_error'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
$submitted    = isset( $_GET['submitted'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['submitted'] ) ); // phpcs:ignore WordPress.Security.NonceVerification

$error_labels = [
	'invalid_nonce'    => __( 'Security check failed. Please refresh and submit again.', 'bigtricks' ),
	'invalid_type'     => __( 'Invalid submission type.', 'bigtricks' ),
	'missing_required' => __( 'Please fill in all required fields.', 'bigtricks' ),
	'insert_failed'    => __( 'We could not save your submission. Please try again.', 'bigtricks' ),
];

$categories = get_categories( [
	'hide_empty' => false,
	'orderby'    => 'name',
	'order'      => 'ASC',
] );

$stores = get_terms( [
	'taxonomy'   => 'store',
	'hide_empty' => false,
	'orderby'    => 'name',
	'order'      => 'ASC',
] );
?>

<main class="max-w-[1400px] mx-auto px-4 py-8 md:py-10 flex-1" id="main-content">
	<div class="mb-6 md:mb-8">
		<h1 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white">
			<?php esc_html_e( 'Submit Content', 'bigtricks' ); ?>
		</h1>
		<p class="mt-2 text-slate-600 dark:text-slate-400 max-w-3xl">
			<?php esc_html_e( 'Choose what you want to submit. All entries are saved as pending and can be reviewed in WordPress admin before publishing.', 'bigtricks' ); ?>
		</p>
	</div>

	<?php if ( $submitted ) : ?>
		<div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-5 py-4 font-semibold flex items-start gap-3">
			<i data-lucide="check-circle-2" class="w-5 h-5 mt-0.5 shrink-0"></i>
			<div>
				<p><?php esc_html_e( 'Submission received. Our team will review it shortly.', 'bigtricks' ); ?></p>
				<p class="text-sm text-emerald-700/90 mt-1"><?php esc_html_e( 'You can track all your submissions in Dashboard.', 'bigtricks' ); ?></p>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( $submit_error && isset( $error_labels[ $submit_error ] ) ) : ?>
		<div class="mb-6 rounded-2xl border border-red-200 bg-red-50 text-red-700 px-5 py-4 font-semibold flex items-start gap-3">
			<i data-lucide="alert-circle" class="w-5 h-5 mt-0.5 shrink-0"></i>
			<p><?php echo esc_html( $error_labels[ $submit_error ] ); ?></p>
		</div>
	<?php endif; ?>

	<div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
		<div class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60 p-3 flex flex-wrap gap-2" role="tablist" aria-label="Submission types">
			<button type="button" data-submit-tab="offer" class="bt-submit-tab px-4 py-2.5 rounded-xl font-bold text-sm transition-colors"><?php esc_html_e( 'Submit Offer', 'bigtricks' ); ?></button>
			<button type="button" data-submit-tab="deal" class="bt-submit-tab px-4 py-2.5 rounded-xl font-bold text-sm transition-colors"><?php esc_html_e( 'Submit Loot Deal', 'bigtricks' ); ?></button>
			<button type="button" data-submit-tab="referral" class="bt-submit-tab px-4 py-2.5 rounded-xl font-bold text-sm transition-colors"><?php esc_html_e( 'Submit Referral Code', 'bigtricks' ); ?></button>
		</div>

		<div class="p-5 md:p-8">
			<section data-submit-panel="offer" class="bt-submit-panel space-y-5" hidden>
				<h2 class="text-2xl font-black text-slate-900 dark:text-white"><?php esc_html_e( 'Submit Offer', 'bigtricks' ); ?></h2>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="space-y-5">
					<input type="hidden" name="action" value="bigtricks_submit_frontend_content">
					<input type="hidden" name="submission_type" value="offer">
					<?php wp_nonce_field( 'bigtricks_submit_frontend', 'bigtricks_submit_nonce' ); ?>

					<div class="grid md:grid-cols-2 gap-5">
						<div class="md:col-span-2">
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Title *', 'bigtricks' ); ?></label>
							<input type="text" name="post_title" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div class="md:col-span-2">
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Description / Content *', 'bigtricks' ); ?></label>
							<textarea name="post_content" rows="6" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3"></textarea>
						</div>
						<div class="md:col-span-2">
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Short Excerpt (Optional)', 'bigtricks' ); ?></label>
							<textarea name="post_excerpt" rows="3" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3"></textarea>
						</div>
						<div class="md:col-span-2">
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Categories (Optional)', 'bigtricks' ); ?></label>
							<select name="post_categories[]" multiple class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3 min-h-[140px]">
								<?php foreach ( $categories as $cat ) : ?>
									<option value="<?php echo esc_attr( (string) $cat->term_id ); ?>"><?php echo esc_html( $cat->name ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>

					<button type="submit" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-black px-6 py-3 rounded-xl">
						<i data-lucide="send" class="w-4 h-4"></i>
						<?php esc_html_e( 'Submit Offer', 'bigtricks' ); ?>
					</button>
				</form>
			</section>

			<section data-submit-panel="deal" class="bt-submit-panel space-y-5" hidden>
				<h2 class="text-2xl font-black text-slate-900 dark:text-white"><?php esc_html_e( 'Submit Loot Deal', 'bigtricks' ); ?></h2>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="space-y-5">
					<input type="hidden" name="action" value="bigtricks_submit_frontend_content">
					<input type="hidden" name="submission_type" value="deal">
					<?php wp_nonce_field( 'bigtricks_submit_frontend', 'bigtricks_submit_nonce' ); ?>

					<div class="grid md:grid-cols-2 gap-5">
						<div class="md:col-span-2">
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Deal Title *', 'bigtricks' ); ?></label>
							<input type="text" name="post_title" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div class="md:col-span-2">
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Deal Content *', 'bigtricks' ); ?></label>
							<textarea name="post_content" rows="6" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3"></textarea>
						</div>
						<div>
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Offer URL *', 'bigtricks' ); ?></label>
							<input type="url" name="deal_offer_url" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div>
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Store (Optional)', 'bigtricks' ); ?></label>
							<select name="store_term_id" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
								<option value="0"><?php esc_html_e( 'Select store', 'bigtricks' ); ?></option>
								<?php if ( ! is_wp_error( $stores ) ) : ?>
									<?php foreach ( $stores as $store ) : ?>
										<option value="<?php echo esc_attr( (string) $store->term_id ); ?>"><?php echo esc_html( $store->name ); ?></option>
									<?php endforeach; ?>
								<?php endif; ?>
							</select>
						</div>
						<div>
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Sale Price', 'bigtricks' ); ?></label>
							<input type="number" step="0.01" name="deal_sale_price" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div>
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Old Price', 'bigtricks' ); ?></label>
							<input type="number" step="0.01" name="deal_old_price" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div>
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Coupon Code', 'bigtricks' ); ?></label>
							<input type="text" name="deal_coupon_code" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div>
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Discount %', 'bigtricks' ); ?></label>
							<input type="number" min="0" max="100" name="deal_discount" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div>
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Discount Tag', 'bigtricks' ); ?></label>
							<input type="text" name="deal_discount_tag" placeholder="Hot Deal" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div>
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Button Text', 'bigtricks' ); ?></label>
							<input type="text" name="deal_button_text" placeholder="Get Deal" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div>
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Verify Label', 'bigtricks' ); ?></label>
							<input type="text" name="deal_verify_label" placeholder="Verified Deal" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div>
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Offer Thumbnail URL', 'bigtricks' ); ?></label>
							<input type="url" name="deal_offer_thumbnail_url" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div>
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Product Thumbnail URL', 'bigtricks' ); ?></label>
							<input type="url" name="deal_product_thumbnail_url" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div>
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Expiration Date', 'bigtricks' ); ?></label>
							<input type="datetime-local" name="deal_expiration_date" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div>
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Store Name (Fallback)', 'bigtricks' ); ?></label>
							<input type="text" name="deal_store_name" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div class="md:col-span-2">
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Product Features (Optional HTML)', 'bigtricks' ); ?></label>
							<textarea name="deal_product_feature" rows="3" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3"></textarea>
						</div>
						<div class="md:col-span-2">
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Disclaimer (Optional HTML)', 'bigtricks' ); ?></label>
							<textarea name="deal_disclaimer" rows="3" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3"></textarea>
						</div>
						<div class="md:col-span-2 grid sm:grid-cols-2 gap-3">
							<label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 px-3 py-2.5 font-semibold text-sm">
								<input type="checkbox" name="deal_mask_coupon" value="1" class="rounded border-slate-300">
								<?php esc_html_e( 'Mask coupon code until click', 'bigtricks' ); ?>
							</label>
							<label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 dark:border-slate-700 px-3 py-2.5 font-semibold text-sm">
								<input type="checkbox" name="deal_is_expired" value="1" class="rounded border-slate-300">
								<?php esc_html_e( 'Mark as expired', 'bigtricks' ); ?>
							</label>
						</div>
					</div>

					<button type="submit" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-black px-6 py-3 rounded-xl">
						<i data-lucide="send" class="w-4 h-4"></i>
						<?php esc_html_e( 'Submit Loot Deal', 'bigtricks' ); ?>
					</button>
				</form>
			</section>

			<section data-submit-panel="referral" class="bt-submit-panel space-y-5" hidden>
				<h2 class="text-2xl font-black text-slate-900 dark:text-white"><?php esc_html_e( 'Submit Referral Code', 'bigtricks' ); ?></h2>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="space-y-5">
					<input type="hidden" name="action" value="bigtricks_submit_frontend_content">
					<input type="hidden" name="submission_type" value="referral">
					<?php wp_nonce_field( 'bigtricks_submit_frontend', 'bigtricks_submit_nonce' ); ?>

					<div class="grid md:grid-cols-2 gap-5">
						<div class="md:col-span-2">
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Post Title *', 'bigtricks' ); ?></label>
							<input type="text" name="post_title" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div>
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'App Name', 'bigtricks' ); ?></label>
							<input type="text" name="ref_app_name" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div>
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Referral Code', 'bigtricks' ); ?></label>
							<input type="text" name="ref_referral_code" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div class="md:col-span-2">
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Referral Link', 'bigtricks' ); ?></label>
							<input type="url" name="ref_referral_link" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div>
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Signup Bonus', 'bigtricks' ); ?></label>
							<input type="text" name="ref_signup_bonus" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div>
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Referral Rewards', 'bigtricks' ); ?></label>
							<input type="text" name="ref_referral_rewards" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3">
						</div>
						<div class="md:col-span-2">
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Short Description', 'bigtricks' ); ?></label>
							<textarea name="ref_short_description" rows="3" class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3"></textarea>
						</div>
						<div class="md:col-span-2">
							<label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-2"><?php esc_html_e( 'Post Content *', 'bigtricks' ); ?></label>
							<textarea name="post_content" rows="6" required class="w-full rounded-xl border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-4 py-3"></textarea>
						</div>
					</div>

					<button type="submit" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white font-black px-6 py-3 rounded-xl">
						<i data-lucide="send" class="w-4 h-4"></i>
						<?php esc_html_e( 'Submit Referral Code', 'bigtricks' ); ?>
					</button>
				</form>
			</section>
		</div>
	</div>
</main>

<script>
(function () {
	const active = <?php echo wp_json_encode( $current_type ); ?>;
	const tabs = document.querySelectorAll('.bt-submit-tab');
	const panels = document.querySelectorAll('.bt-submit-panel');

	function setTab(type) {
		tabs.forEach(function (tab) {
			const selected = tab.dataset.submitTab === type;
			tab.classList.toggle('bg-primary-600', selected);
			tab.classList.toggle('text-white', selected);
			tab.classList.toggle('bg-white', !selected);
			tab.classList.toggle('text-slate-700', !selected);
		});

		panels.forEach(function (panel) {
			panel.hidden = panel.dataset.submitPanel !== type;
		});
	}

	tabs.forEach(function (tab) {
		tab.addEventListener('click', function () {
			setTab(tab.dataset.submitTab);
		});
	});

	setTab(active);
})();
</script>

<?php get_footer(); ?>

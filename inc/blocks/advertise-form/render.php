<?php
/**
 * Advertise Form Block Template
 *
 * @package Bigtricks
 * @var array $attributes Block attributes
 * @var string $content Block content
 * @var WP_Block $block Block instance
 */

declare( strict_types=1 );

$form_title        = isset( $attributes['formTitle'] ) ? esc_html( $attributes['formTitle'] ) : 'Advertise With Us';
$form_description  = isset( $attributes['formDescription'] ) ? esc_html( $attributes['formDescription'] ) : 'Partner with us to reach thousands of engaged users.';
$submit_button     = isset( $attributes['submitButtonText'] ) ? esc_html( $attributes['submitButtonText'] ) : 'Submit Inquiry';
$success_message   = isset( $attributes['successMessage'] ) ? esc_html( $attributes['successMessage'] ) : 'Thank you for your interest!';
$show_title        = isset( $attributes['showTitle'] ) ? (bool) $attributes['showTitle'] : true;
$show_description  = isset( $attributes['showDescription'] ) ? (bool) $attributes['showDescription'] : true;

// Generate unique form ID and escape once
$form_id         = 'bt-advertise-form-' . uniqid();
$escaped_form_id = esc_attr( $form_id );
?>

<div class="bigtricks-advertise-form py-8">
	<div class="max-w-3xl mx-auto">
		<?php if ( $show_title && ! empty( $form_title ) ) : ?>
			<h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3">
				<?php echo $form_title; ?>
			</h2>
		<?php endif; ?>

		<?php if ( $show_description && ! empty( $form_description ) ) : ?>
			<p class="text-gray-600 dark:text-gray-400 mb-8">
				<?php echo $form_description; ?>
			</p>
		<?php endif; ?>

		<form 
			id="<?php echo $escaped_form_id; ?>" 
			class="bt-advertise-form-element space-y-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 md:p-8 shadow-sm"
			data-form-type="advertise"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'bigtricks_advertise_form' ) ); ?>"
		>
			<!-- Contact Details Section -->
			<div class="pb-6 border-b border-gray-200 dark:border-gray-700">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
					<i data-lucide="user" class="w-5 h-5"></i>
					Contact Information
				</h3>
				
				<div class="grid md:grid-cols-2 gap-6">
					<!-- Name Field -->
					<div>
						<label for="<?php echo $escaped_form_id; ?>-name" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
							Your Name <span class="text-red-500">*</span>
						</label>
						<input 
							type="text" 
							id="<?php echo $escaped_form_id; ?>-name" 
							name="advertise_name" 
							required
							class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all"
							placeholder="John Doe"
						>
					</div>

					<!-- Email Field -->
					<div>
						<label for="<?php echo $escaped_form_id; ?>-email" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
							Email Address <span class="text-red-500">*</span>
						</label>
						<input 
							type="email" 
							id="<?php echo $escaped_form_id; ?>-email" 
							name="advertise_email" 
							required
							class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all"
							placeholder="john@company.com"
						>
					</div>

					<!-- Phone Field -->
					<div>
						<label for="<?php echo $escaped_form_id; ?>-phone" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
							Phone Number <span class="text-red-500">*</span>
						</label>
						<input 
							type="tel" 
							id="<?php echo $escaped_form_id; ?>-phone" 
							name="advertise_phone" 
							required
							pattern="[0-9+\-\s]+"
							class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all"
							placeholder="+91 98765 43210"
						>
					</div>

					<!-- WhatsApp Number Field -->
					<div>
						<label for="<?php echo $escaped_form_id; ?>-whatsapp" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
							WhatsApp Number
						</label>
						<input 
							type="tel" 
							id="<?php echo $escaped_form_id; ?>-whatsapp" 
							name="advertise_whatsapp" 
							pattern="[0-9+\-\s]+"
							class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all"
							placeholder="Same as phone"
						>
						<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Optional - Leave blank if same as phone</p>
					</div>
				</div>
			</div>

			<!-- Business Details Section -->
			<div class="pb-6 border-b border-gray-200 dark:border-gray-700">
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
					<i data-lucide="building-2" class="w-5 h-5"></i>
					Business Information
				</h3>

				<div class="grid md:grid-cols-2 gap-6">
					<!-- Company Name -->
					<div>
						<label for="<?php echo $escaped_form_id; ?>-company" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
							Company Name <span class="text-red-500">*</span>
						</label>
						<input 
							type="text" 
							id="<?php echo $escaped_form_id; ?>-company" 
							name="advertise_company" 
							required
							class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all"
							placeholder="Your Company Pvt Ltd"
						>
					</div>

					<!-- Website -->
					<div>
						<label for="<?php echo $escaped_form_id; ?>-website" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
							Company Website
						</label>
						<input 
							type="url" 
							id="<?php echo $escaped_form_id; ?>-website" 
							name="advertise_website" 
							class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all"
							placeholder="https://yourcompany.com"
						>
					</div>
				</div>
			</div>

			<!-- Advertising Requirements Section -->
			<div>
				<h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
					<i data-lucide="megaphone" class="w-5 h-5"></i>
					Advertising Requirements
				</h3>

				<!-- Requirement Type -->
				<div class="mb-6">
					<label for="<?php echo $escaped_form_id; ?>-requirement" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
						Type of Advertisement <span class="text-red-500">*</span>
					</label>
					<select 
						id="<?php echo $escaped_form_id; ?>-requirement" 
						name="advertise_requirement" 
						required
						class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all"
					>
						<option value="">-- Select Type --</option>
						<option value="Banner Ads">Banner Ads (Display Advertising)</option>
						<option value="Sponsored Posts">Sponsored Posts (Content Marketing)</option>
						<option value="Telegram Post">Telegram Channel Post</option>
						<option value="Product Review">Product Review & Listing</option>
						<option value="Affiliate Partnership">Affiliate Partnership</option>
						<option value="Newsletter Sponsorship">Newsletter Sponsorship</option>
						<option value="Other">Other (Please specify in message)</option>
					</select>
				</div>

				<!-- Budget Range -->
				<div class="mb-6">
					<label for="<?php echo $escaped_form_id; ?>-budget" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
						Monthly Budget Range
					</label>
					<select 
						id="<?php echo $escaped_form_id; ?>-budget" 
						name="advertise_budget" 
						class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all"
					>
						<option value="">-- Select Budget --</option>
						<option value="Under ₹10,000">Under ₹10,000</option>
						<option value="₹10,000 - ₹25,000">₹10,000 - ₹25,000</option>
						<option value="₹25,000 - ₹50,000">₹25,000 - ₹50,000</option>
						<option value="₹50,000 - ₹1,00,000">₹50,000 - ₹1,00,000</option>
						<option value="Above ₹1,00,000">Above ₹1,00,000</option>
						<option value="Custom">Custom (Discuss in message)</option>
					</select>
					<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Optional - Helps us provide better recommendations</p>
				</div>

				<!-- Campaign Duration -->
				<div class="mb-6">
					<label for="<?php echo $escaped_form_id; ?>-duration" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
						Campaign Duration
					</label>
					<select 
						id="<?php echo $escaped_form_id; ?>-duration" 
						name="advertise_duration" 
						class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all"
					>
						<option value="">-- Select Duration --</option>
						<option value="1 Month">1 Month</option>
						<option value="3 Months">3 Months</option>
						<option value="6 Months">6 Months</option>
						<option value="12 Months">12 Months (Annual)</option>
						<option value="One-time">One-time Campaign</option>
						<option value="Ongoing">Ongoing Partnership</option>
					</select>
				</div>

				<!-- Additional Message -->
				<div>
					<label for="<?php echo $escaped_form_id; ?>-message" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
						Additional Details <span class="text-red-500">*</span>
					</label>
					<textarea 
						id="<?php echo $escaped_form_id; ?>-message" 
						name="advertise_message" 
						required
						rows="5"
						class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all resize-none"
						placeholder="Tell us about your advertising goals, target audience, preferred timeline, or any specific requirements..."
					></textarea>
				</div>
			</div>

			<!-- Submit Button -->
			<div class="pt-4">
				<button 
					type="submit" 
					class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-4 px-6 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-lg"
				>
					<span class="submit-text"><?php echo $submit_button; ?></span>
					<i data-lucide="send" class="w-5 h-5"></i>
				</button>
			</div>

			<!-- Messages Container -->
			<div class="form-messages hidden mt-4"></div>
		</form>

		<!-- Trust Indicators -->
		<div class="mt-6 flex flex-wrap items-center justify-center gap-6 text-sm text-gray-600 dark:text-gray-400">
			<div class="flex items-center gap-2">
				<i data-lucide="shield-check" class="w-4 h-4 text-green-600"></i>
				<span>Secure & Confidential</span>
			</div>
			<div class="flex items-center gap-2">
				<i data-lucide="clock" class="w-4 h-4 text-blue-600"></i>
				<span>24-hour Response Time</span>
			</div>
			<div class="flex items-center gap-2">
				<i data-lucide="users" class="w-4 h-4 text-purple-600"></i>
				<span>Trusted by 100+ Brands</span>
			</div>
		</div>
	</div>
</div>

<?php
/**
 * Contact Form Block Template
 *
 * @package Bigtricks
 * @var array $attributes Block attributes
 * @var string $content Block content
 * @var WP_Block $block Block instance
 */

declare( strict_types=1 );

$form_title        = isset( $attributes['formTitle'] ) ? esc_html( $attributes['formTitle'] ) : 'Get in Touch';
$form_description  = isset( $attributes['formDescription'] ) ? esc_html( $attributes['formDescription'] ) : 'Have a question? We\'d love to hear from you.';
$submit_button     = isset( $attributes['submitButtonText'] ) ? esc_html( $attributes['submitButtonText'] ) : 'Send Message';
$success_message   = isset( $attributes['successMessage'] ) ? esc_html( $attributes['successMessage'] ) : 'Thank you! Your message has been sent successfully.';
$show_title        = isset( $attributes['showTitle'] ) ? (bool) $attributes['showTitle'] : true;
$show_description  = isset( $attributes['showDescription'] ) ? (bool) $attributes['showDescription'] : true;

// Generate unique form ID and escape once
$form_id         = 'bt-contact-form-' . uniqid();
$escaped_form_id = esc_attr( $form_id );
?>

<div class="bigtricks-contact-form py-8">
	<div class="max-w-2xl mx-auto">
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
			class="bt-contact-form-element space-y-6 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 md:p-8 shadow-sm"
			data-form-type="contact"
			data-nonce="<?php echo esc_attr( wp_create_nonce( 'bigtricks_contact_form' ) ); ?>"
		>
			<!-- Name Field -->
			<div>
				<label for="<?php echo $escaped_form_id; ?>-name" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
					Your Name <span class="text-red-500">*</span>
				</label>
				<input 
					type="text" 
					id="<?php echo $escaped_form_id; ?>-name" 
					name="contact_name" 
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
					name="contact_email" 
					required
					class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all"
					placeholder="john@example.com"
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
					name="contact_whatsapp" 
					pattern="[0-9+\-\s]+"
					class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all"
					placeholder="+91 98765 43210"
				>
				<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Optional - Include country code for better response</p>
			</div>

			<!-- Query/Message Field -->
			<div>
				<label for="<?php echo $escaped_form_id; ?>-message" class="block text-sm font-semibold text-gray-900 dark:text-white mb-2">
					Your Message <span class="text-red-500">*</span>
				</label>
				<textarea 
					id="<?php echo $escaped_form_id; ?>-message" 
					name="contact_message" 
					required
					rows="5"
					class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-900 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-600 focus:border-transparent transition-all resize-none"
					placeholder="Tell us what you need help with..."
				></textarea>
			</div>

			<!-- Submit Button -->
			<div>
				<button 
					type="submit" 
					class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
				>
					<span class="submit-text"><?php echo $submit_button; ?></span>
					<i data-lucide="send" class="w-4 h-4"></i>
				</button>
			</div>

			<!-- Messages Container -->
			<div class="form-messages hidden mt-4"></div>
		</form>
	</div>
</div>

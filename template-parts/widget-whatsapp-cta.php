<?php
/**
 * Widget: WhatsApp CTA
 * 
 * Large promotional banner for WhatsApp community
 * 
 * @package Bigtricks
 */

declare(strict_types=1);

$whatsapp_url = bigtricks_option( 'bt_whatsapp_url', 'https://wa.me/bigtricks' );

// Don't show widget if URL is not set
if ( ! $whatsapp_url ) {
	return;
}
?>

<!-- WhatsApp CTA -->
<div class="bg-gradient-to-br from-[#128C7E] to-[#25D366] rounded-3xl p-8 text-white shadow-xl relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
	<i data-lucide="message-circle" class="absolute -right-4 -bottom-4 w-32 h-32 text-white opacity-20 group-hover:scale-110 transition-transform duration-700"></i>
	<div class="relative z-10">
		<h2 class="font-black text-2xl mb-2 text-white"><?php esc_html_e( 'WhatsApp Community', 'bigtricks' ); ?></h2>
		<p class="text-green-50 text-sm mb-5 font-medium leading-relaxed">
			<?php esc_html_e( '1 lakh+ members receiving exclusive deals daily.', 'bigtricks' ); ?>
		</p>
		<a
			href="<?php echo esc_url( $whatsapp_url ); ?>"
			target="_blank"
			rel="noopener noreferrer"
			class="bg-white text-[#128C7E] font-black py-3 px-4 rounded-xl w-full shadow-lg hover:bg-green-50 transition-colors flex items-center justify-center gap-2"
			aria-label="<?php esc_attr_e( 'Join WhatsApp Group', 'bigtricks' ); ?>"
		>
			<i data-lucide="message-circle" class="w-5 h-5"></i>
			<?php esc_html_e( 'Join WhatsApp Group', 'bigtricks' ); ?>
		</a>
	</div>
</div>

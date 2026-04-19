<?php
/**
 * Right Sidebar
 *
 * @package Bigtricks
 */
?>
<aside class="w-full lg:w-[380px] shrink-0 space-y-8 hidden lg:block" aria-label="<?php esc_attr_e( 'Sidebar', 'bigtricks' ); ?>">

	<?php
	// Telegram CTA
	get_template_part( 'template-parts/widget-telegram-cta' );

	// Trending Categories
	get_template_part( 'template-parts/widget-trending-categories', null, [ 'count' => 8 ] );

	// Newsletter
	get_template_part( 'template-parts/widget-newsletter' );

	// WhatsApp CTA
	get_template_part( 'template-parts/widget-whatsapp-cta' );

	// WordPress Sidebar Widgets (optional)
	if ( is_active_sidebar( 'sidebar-1' ) ) :
		dynamic_sidebar( 'sidebar-1' );
	endif;

	// Download App Widget
	get_template_part( 'template-parts/widget-download-app' );
	?>

</aside>

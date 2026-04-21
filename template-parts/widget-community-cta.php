<?php
/**
 * Widget: Community CTA
 *
 * Promotional banner for Telegram and WhatsApp communities
 *
 * @package Bigtricks
 */

declare(strict_types=1);

$default_telegram_url = bigtricks_option( 'bt_telegram_url', 'https://telegram.dog/+0k3Gk4JPe_FjN2Zl' );
$default_whatsapp_url = bigtricks_option( 'bt_whatsapp_url', 'https://links.bigtricks.in/whatsapp' );

$widget_args = wp_parse_args(
	(array) ( $args ?? [] ),
	[
		'telegram_url'     => $default_telegram_url,
		'whatsapp_url'     => $default_whatsapp_url,
		'telegram_members' => '28.5K',
		'whatsapp_members' => 'Active community',
	]
);
?>

<div class="bg-black rounded-xl p-5 text-white my-5">
	<div class="flex items-center gap-5 justify-between flex-wrap md:flex-nowrap">
		<div class="flex items-center gap-5 flex-1">
			<i data-lucide="send" class="w-8 h-8 text-blue-400"></i>
			<div class="flex-1">
				<p class="text-base !text-white md:text-lg font-medium mb-1">Join Our Community Channels</p>
				<p class="text-sm !text-white">Save Upto Rs.10,000 Per month with exclusive offers and deals. Get instant alerts!</p>
			</div>
		</div>
		<div class="flex flex-col gap-2 mt-4 md:mt-0">
			<a href="<?php echo esc_url( $widget_args['telegram_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="text-white">
				<span class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center gap-2 transition-colors">
					<i data-lucide="send" class="w-4 h-4"></i>
					<span class="hidden md:inline">Join Telegram</span>
				</span>
			</a>
			<small class="text-xs text-white flex items-center gap-1">
				<span class="text-green-400">●</span> <?php echo esc_html( $widget_args['telegram_members'] ); ?> members
			</small>

			<a href="<?php echo esc_url( $widget_args['whatsapp_url'] ); ?>" target="_blank" rel="noopener noreferrer" class="text-white">
				<span class="bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded-lg inline-flex items-center gap-2 transition-colors">
					<i data-lucide="message-circle" class="w-4 h-4"></i>
					<span class="hidden md:inline">Join WhatsApp</span>
				</span>
			</a>
			<small class="text-xs text-white flex items-center gap-1">
				<span class="text-green-400">●</span> <?php echo esc_html( $widget_args['whatsapp_members'] ); ?>
			</small>
		</div>
	</div>
</div>
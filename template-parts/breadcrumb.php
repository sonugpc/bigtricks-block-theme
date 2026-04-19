<?php
/**
 * Breadcrumb component.
 *
 * @package Bigtricks
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$breadcrumb_items = bigtricks_get_breadcrumb_items();

if ( count( $breadcrumb_items ) < 2 ) {
	return;
}
?>

<nav aria-label="<?php esc_attr_e( 'Breadcrumbs', 'bigtricks' ); ?>" class="mb-3">
	<div class="flex flex-wrap items-center gap-1.5 text-xs font-bold tracking-wide text-slate-500">
		<?php foreach ( $breadcrumb_items as $index => $item ) : ?>
			<?php $is_last = ( $index === count( $breadcrumb_items ) - 1 ); ?>

			<?php if ( ! empty( $item['url'] ) && ! $is_last ) : ?>
				<a href="<?php echo esc_url( $item['url'] ); ?>" class="uppercase text-slate-500 hover:text-primary-600 transition-colors">
					<?php echo esc_html( (string) $item['label'] ); ?>
				</a>
			<?php else : ?>
				<span class="uppercase text-slate-700">
					<?php echo esc_html( (string) $item['label'] ); ?>
				</span>
			<?php endif; ?>

			<?php if ( ! $is_last ) : ?>
				<span class="text-slate-400 px-0.5" aria-hidden="true">
					<svg class="w-3.5 h-3.5" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M7 4L13 10L7 16" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
				</span>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
</nav>

<?php
/**
 * Widget: Newsletter / Daily Deal Digest
 * 
 * Email newsletter signup form
 * 
 * @package Bigtricks
 */

declare(strict_types=1);
?>

<!-- Newsletter / Daily Deal Digest -->
<div class="bg-white dark:bg-slate-900 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-800 p-6 text-center">
	<div class="w-16 h-16 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
		<i data-lucide="flame" class="w-7 h-7 text-orange-500 dark:text-orange-400"></i>
	</div>
	<h2 class="font-black text-xl text-slate-900 dark:text-white mb-2"><?php esc_html_e( 'Daily Deal Digest', 'bigtricks' ); ?></h2>
	<p class="text-slate-500 dark:text-slate-400 text-sm mb-5">
		<?php esc_html_e( 'Get the top handpicked deals delivered to your inbox every day.', 'bigtricks' ); ?>
	</p>
	<form
		class="flex gap-2"
		action="https://api.follow.it/subscription-form/RWNsNDNqRVFlL3ozR2I2TEFGRk5yZEIyMnhEbW1jNVllSGQrRlhUYUZFWW03N0Z4NTFWa25BMnRVV0tDeCttcjBuSEZURXlFYUwwZys3N0JBa2hPWHoxeGZnRmJ5ajRJQjd5cWNtMjdHT1NqNzMzbkUwRUxhc3F6WFVkTEFNWml8dnFtdHViSnpzQWFNSVQ2K01TbjB2Yy9ra0xtd1VsWGNsZFlvSXgrWENUND0=/8"
		method="post"
		target="_blank"
		rel="noopener noreferrer"
		aria-label="<?php esc_attr_e( 'Newsletter signup', 'bigtricks' ); ?>"
	>
		<label for="bt-newsletter-email" class="sr-only"><?php esc_html_e( 'Email address', 'bigtricks' ); ?></label>
		<input
			id="bt-newsletter-email"
			type="email"
			name="email"
			placeholder="<?php esc_attr_e( 'Email address', 'bigtricks' ); ?>"
			required
			class="w-full bg-slate-100 dark:bg-slate-800 rounded-xl px-4 py-2.5 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 border border-transparent focus:border-primary-200 dark:focus:border-primary-700"
		>
		<button
			type="submit"
			class="bg-primary-600 dark:bg-primary-500 text-white px-4 rounded-xl font-bold hover:bg-primary-700 dark:hover:bg-primary-600 transition-colors shrink-0"
		>
			<?php esc_html_e( 'Go', 'bigtricks' ); ?>
		</button>
	</form>
</div>

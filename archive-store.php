<?php
/**
 * Stores Archive — lists 'store' taxonomy terms.
 * The store CPT has been removed; 'store' is now a shared taxonomy
 * used by the deal, referral-codes, and credit-card post types.
 *
 * @package Bigtricks
 */

get_header();

$pastel_colors = [
	'bg-amber-100 text-amber-700',
	'bg-blue-100 text-blue-700',
	'bg-rose-100 text-rose-700',
	'bg-emerald-100 text-emerald-700',
	'bg-purple-100 text-purple-700',
	'bg-orange-100 text-orange-700',
	'bg-cyan-100 text-cyan-700',
	'bg-teal-100 text-teal-700',
	'bg-primary-100 text-primary-700',
	'bg-pink-100 text-pink-700',
];

// All store taxonomy terms ordered by count
$store_terms = get_terms( [
	'taxonomy'   => 'store',
	'hide_empty' => false,
	'orderby'    => 'count',
	'order'      => 'DESC',
] );
?>

<main class="max-w-[1400px] mx-auto px-4 py-8 flex-1 w-full" id="main-content">

	<header class="mb-8">
		<h1 class="text-2xl font-black text-slate-900 flex items-center gap-3 mb-2">
			<div class="bg-primary-100 p-2 rounded-xl text-primary-600">
				<i data-lucide="shopping-bag" class="w-6 h-6"></i>
			</div>
			<?php esc_html_e( 'Top Stores', 'bigtricks' ); ?>
		</h1>
		<p class="text-slate-500 font-medium"><?php esc_html_e( 'Browse deals, referral codes, and credit cards by store or brand.', 'bigtricks' ); ?></p>
	</header>

	<?php if ( ! empty( $store_terms ) && ! is_wp_error( $store_terms ) ) : ?>
	<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 xl:grid-cols-5 gap-4 md:gap-6">
		<?php $store_i = 0; foreach ( $store_terms as $term ) :
			$term_link  = esc_url( get_term_link( $term ) );
			$icon_meta  = get_term_meta( $term->term_id, 'thumb_image', true );
			$has_icon   = ! empty( $icon_meta );
			$icon_url   = '';
			if ( $has_icon ) {
				$icon_url = is_numeric( $icon_meta )
					? esc_url( (string) wp_get_attachment_image_url( (int) $icon_meta, 'thumbnail' ) )
					: esc_url( $icon_meta );
			}
			$pastel   = $pastel_colors[ $store_i % count( $pastel_colors ) ];
			$initials = strtoupper( mb_substr( $term->name, 0, 2 ) );
			$count    = (int) $term->count;
			$store_i++;
			?>
		<a
			href="<?php echo $term_link; ?>"
			class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group flex flex-col items-center p-6 text-center"
			aria-label="<?php echo esc_attr( $term->name ); ?>"
		>
			<!-- Logo / Initials Circle -->
			<div class="w-20 h-20 rounded-full flex items-center justify-center mb-4 transition-all duration-300 group-hover:scale-105 group-hover:ring-4 group-hover:ring-primary-100 <?php echo $icon_url ? 'bg-slate-50' : esc_attr( $pastel ); ?> overflow-hidden">
				<?php if ( $icon_url ) : ?>
				<img
					src="<?php echo $icon_url; ?>"
					alt="<?php echo esc_attr( $term->name ); ?>"
					class="w-full h-full object-contain p-2 mix-blend-multiply"
					loading="lazy"
					decoding="async"
				>
				<?php else : ?>
				<span class="text-2xl font-black select-none"><?php echo esc_html( $initials ); ?></span>
				<?php endif; ?>
			</div>

			<h2 class="font-black text-slate-900 text-sm leading-tight line-clamp-2 group-hover:text-primary-600 transition-colors">
				<?php echo esc_html( $term->name ); ?>
			</h2>

			<span class="mt-2 text-xs font-bold px-2 py-1 rounded-full <?php echo $count > 0 ? 'text-primary-600 bg-primary-50' : 'text-slate-400 bg-slate-50'; ?>">
				<?php if ( $count > 0 ) :
					/* translators: %d: number of posts */
					printf( esc_html( _n( '%d Post', '%d Posts', $count, 'bigtricks' ) ), esc_html( $count ) );
					else :
						esc_html_e( 'Browse', 'bigtricks' );
					endif;
				?>
			</span>
		</a>
		<?php endforeach; ?>
	</div>

	<?php else : ?>
	<div class="bg-white p-12 rounded-3xl border border-slate-200 text-center">
		<div class="bg-slate-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
			<i data-lucide="shopping-bag" class="w-8 h-8 text-slate-400"></i>
		</div>
		<h3 class="text-xl font-bold text-slate-900 mb-2"><?php esc_html_e( 'No stores yet.', 'bigtricks' ); ?></h3>
		<p class="text-slate-500"><?php esc_html_e( 'Stores will appear once content is added.', 'bigtricks' ); ?></p>
	</div>
	<?php endif; ?>

</main>

<?php get_footer(); ?>

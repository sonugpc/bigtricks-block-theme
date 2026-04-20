<?php
/**
 * Template Name: Dashboard Page
 *
 * Frontend user dashboard for submission tracking.
 *
 * @package Bigtricks
 */

declare( strict_types=1 );

if ( ! is_user_logged_in() ) {
	wp_safe_redirect( bigtricks_get_login_url( get_permalink() ) );
	exit;
}

get_header();

$current_user_id = get_current_user_id();
$status_counts   = [];
$tracked_status  = [ 'publish', 'pending', 'draft', 'future' ];

foreach ( $tracked_status as $status_slug ) {
	$count_query = new WP_Query( [
		'post_type'      => [ 'post', 'deal', 'referral-codes' ],
		'post_status'    => $status_slug,
		'posts_per_page' => 1,
		'author'         => $current_user_id,
		'no_found_rows'  => false,
	] );
	$status_counts[ $status_slug ] = (int) $count_query->found_posts;
}

$recent_query = new WP_Query( [
	'post_type'      => [ 'post', 'deal', 'referral-codes' ],
	'post_status'    => [ 'publish', 'pending', 'draft', 'future' ],
	'posts_per_page' => 10,
	'author'         => $current_user_id,
	'orderby'        => 'date',
	'order'          => 'DESC',
] );

$type_labels = [
	'post'           => __( 'Offer Post', 'bigtricks' ),
	'deal'           => __( 'Loot Deal', 'bigtricks' ),
	'referral-codes' => __( 'Referral Code', 'bigtricks' ),
];
?>

<main class="max-w-[1400px] mx-auto px-4 py-8 md:py-10 flex-1" id="main-content">
	<div class="mb-8">
		<h1 class="text-3xl md:text-4xl font-black text-slate-900 dark:text-white"><?php esc_html_e( 'Dashboard', 'bigtricks' ); ?></h1>
		<p class="mt-2 text-slate-600 dark:text-slate-400"><?php esc_html_e( 'Track your submitted posts and review status from one place.', 'bigtricks' ); ?></p>
	</div>

	<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
		<div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-5">
			<p class="text-sm font-semibold text-slate-500 dark:text-slate-400"><?php esc_html_e( 'Published', 'bigtricks' ); ?></p>
			<p class="text-3xl font-black mt-1 text-slate-900 dark:text-white"><?php echo esc_html( (string) ( $status_counts['publish'] ?? 0 ) ); ?></p>
		</div>
		<div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-5">
			<p class="text-sm font-semibold text-slate-500 dark:text-slate-400"><?php esc_html_e( 'Pending Review', 'bigtricks' ); ?></p>
			<p class="text-3xl font-black mt-1 text-amber-600"><?php echo esc_html( (string) ( $status_counts['pending'] ?? 0 ) ); ?></p>
		</div>
		<div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-5">
			<p class="text-sm font-semibold text-slate-500 dark:text-slate-400"><?php esc_html_e( 'Drafts', 'bigtricks' ); ?></p>
			<p class="text-3xl font-black mt-1 text-slate-900 dark:text-white"><?php echo esc_html( (string) ( $status_counts['draft'] ?? 0 ) ); ?></p>
		</div>
		<div class="rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 p-5">
			<p class="text-sm font-semibold text-slate-500 dark:text-slate-400"><?php esc_html_e( 'Scheduled', 'bigtricks' ); ?></p>
			<p class="text-3xl font-black mt-1 text-slate-900 dark:text-white"><?php echo esc_html( (string) ( $status_counts['future'] ?? 0 ) ); ?></p>
		</div>
	</div>

	<div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 overflow-hidden">
		<div class="px-5 md:px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex flex-wrap gap-3 items-center justify-between">
			<h2 class="text-xl font-black text-slate-900 dark:text-white"><?php esc_html_e( 'My Recent Submissions', 'bigtricks' ); ?></h2>
			<a href="<?php echo esc_url( bigtricks_get_submit_page_url() ); ?>" class="inline-flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-xl font-bold text-sm">
				<i data-lucide="plus" class="w-4 h-4"></i>
				<?php esc_html_e( 'Submit New', 'bigtricks' ); ?>
			</a>
		</div>

		<?php if ( $recent_query->have_posts() ) : ?>
			<div class="divide-y divide-slate-100 dark:divide-slate-800">
				<?php while ( $recent_query->have_posts() ) : $recent_query->the_post(); ?>
					<?php
					$post_type = get_post_type() ?: 'post';
					$status    = get_post_status() ?: 'draft';
					?>
					<div class="px-5 md:px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
						<div>
							<h3 class="font-bold text-slate-900 dark:text-white text-base leading-tight"><?php the_title(); ?></h3>
							<div class="mt-1 text-sm text-slate-500 dark:text-slate-400 flex flex-wrap items-center gap-2">
								<span><?php echo esc_html( $type_labels[ $post_type ] ?? ucfirst( $post_type ) ); ?></span>
								<span>•</span>
								<span><?php echo esc_html( get_the_date() ); ?></span>
							</div>
						</div>
						<div class="flex items-center gap-2">
							<span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-200 uppercase tracking-wide"><?php echo esc_html( $status ); ?></span>
							<?php if ( 'publish' === $status ) : ?>
								<a href="<?php echo esc_url( (string) get_permalink() ); ?>" class="text-primary-600 hover:text-primary-800 font-semibold text-sm"><?php esc_html_e( 'View', 'bigtricks' ); ?></a>
							<?php endif; ?>
						</div>
					</div>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		<?php else : ?>
			<div class="px-5 md:px-6 py-10 text-center">
				<p class="text-slate-600 dark:text-slate-400"><?php esc_html_e( 'No submissions yet. Start by submitting your first offer, deal, or referral code.', 'bigtricks' ); ?></p>
			</div>
		<?php endif; ?>
	</div>
</main>

<?php get_footer(); ?>

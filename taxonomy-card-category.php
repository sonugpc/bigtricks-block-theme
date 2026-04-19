<?php
/**
 * Card Category Taxonomy Archive
 * Shows all credit cards for a specific card-category term.
 *
 * @package Bigtricks
 */

get_header();

/* ──────────────────────────────────────────────────────────────
 * Query context
 * ────────────────────────────────────────────────────────────── */
$term        = get_queried_object();
$term_id     = $term instanceof WP_Term ? (int) $term->term_id : 0;
$term_name   = $term instanceof WP_Term ? $term->name : '';
$term_desc   = $term instanceof WP_Term ? $term->description : '';

// Initials fallback colour (deterministic from term ID)
$pastel_colors = [
        'bg-primary-100 text-primary-600',
        'bg-pink-100 text-pink-600',
        'bg-emerald-100 text-emerald-600',
        'bg-orange-100 text-orange-600',
        'bg-purple-100 text-purple-600',
        'bg-cyan-100 text-cyan-600',
        'bg-amber-100 text-amber-700',
        'bg-blue-100 text-blue-700',
];
$color_class = $term_id ? $pastel_colors[ $term_id % count( $pastel_colors ) ] : $pastel_colors[0];

$paged      = ( get_query_var( 'paged' ) ) ? (int) get_query_var( 'paged' ) : 1;
$query_args = [
        'post_type'      => 'credit-card',
        'post_status'    => 'publish',
        'posts_per_page' => 12,
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => [ // phpcs:ignore WordPress.DB.SlowDBQuery
                [
                        'taxonomy' => 'card-category',
                        'field'    => 'term_id',
                        'terms'    => $term_id,
                ],
        ],
];

$feed_query  = new WP_Query( $query_args );
$total_posts = $feed_query->found_posts;
$max_pages   = $feed_query->max_num_pages;

/* SEO: override document title to be descriptive */
add_filter( 'pre_get_document_title', function () use ( $term_name ) {
        /* translators: %s: card category name */
        return sprintf( __( 'Best %s Credit Cards | Bigtricks', 'bigtricks' ), $term_name );
} );
?>

<main class="max-w-[1400px] mx-auto px-4 py-6 md:py-8 flex flex-col lg:flex-row gap-8 flex-1 w-full box-border" id="main-content">

        <div class="flex-1 min-w-0 w-full overflow-hidden">

                <!-- ═══ HERO ═══ -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 mb-8 flex flex-col sm:flex-row items-start sm:items-center gap-6 overflow-hidden relative">
                        <!-- Decorative background -->
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-50/60 via-white to-primary-50/30 pointer-events-none" aria-hidden="true"></div>

                        <!-- Icon/Initials -->
                        <div class="relative z-10 shrink-0">
                                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl <?php echo esc_attr( $color_class ); ?> flex items-center justify-center shadow-md border-2 border-white">
                                        <i data-lucide="credit-card" class="w-10 h-10 sm:w-12 sm:h-12 opacity-80"></i>
                                </div>
                        </div>

                        <!-- Info -->
                        <div class="relative z-10 flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2 flex-wrap">
                                        <span class="bg-purple-100 text-purple-700 text-xs font-black px-3 py-1 rounded-full uppercase tracking-wider border border-purple-200">
                                                <?php esc_html_e( 'Category', 'bigtricks' ); ?>
                                        </span>
                                        <span class="text-slate-500 text-sm font-bold">
                                                <?php
                                                printf(
                                                        /* translators: %s: number of items */
                                                        esc_html( _n( '%s card', '%s cards', $total_posts, 'bigtricks' ) ),
                                                        esc_html( number_format_i18n( $total_posts ) )
                                                );
                                                ?>
                                        </span>
                                </div>

                                <!-- SEO-optimised h1 -->
                                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 leading-tight mb-3 break-words">
                                        <?php
                                        printf(
                                                /* translators: %s: term name */
                                                esc_html__( 'Best %s Credit Cards', 'bigtricks' ),
                                                esc_html( $term_name )
                                        );
                                        ?>
                                </h1>

                                <?php if ( $term_desc ) : ?>
                                <p class="text-slate-500 text-sm sm:text-base leading-relaxed max-w-2xl">
                                        <?php echo wp_kses_post( wpautop( $term_desc ) ); ?>
                                </p>
                                <?php endif; ?>
                        </div>
                </div><!-- /hero -->

                <!-- ═══ FILTER CHIPS & VIEW TOGGLE ═══ -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-end mb-6 gap-4">
                        <!-- View Toggle -->
                        <div class="flex items-center bg-white border border-slate-200 rounded-xl p-1 shadow-sm shrink-0">
                                <button id="bt-view-list" class="bt-view-toggle p-1.5 rounded-lg transition-colors bg-primary-50 text-primary-600" data-view="list" aria-label="<?php esc_attr_e( 'List view', 'bigtricks' ); ?>" aria-pressed="true">
                                        <i data-lucide="list" class="w-4 h-4"></i>
                                </button>
                                <button id="bt-view-grid" class="bt-view-toggle p-1.5 rounded-lg transition-colors text-slate-400 hover:text-slate-600" data-view="grid" aria-label="<?php esc_attr_e( 'Grid view', 'bigtricks' ); ?>" aria-pressed="false">
                                        <i data-lucide="layout-grid" class="w-4 h-4"></i>
                                </button>
                        </div>
                </div>

                <!-- ═══ FEED ═══ -->
                <div
                        id="bt-feed-container"
                        class="space-y-6"
                        data-view="list"
                        data-page="1"
                        data-max-pages="<?php echo esc_attr( $max_pages ); ?>"
                        data-type="credit-card"
                >
                        <?php if ( ! $feed_query->have_posts() ) : ?>
                        <div class="bg-white p-12 rounded-3xl border border-slate-200 text-center">
                                <div class="bg-slate-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i data-lucide="credit-card" class="w-8 h-8 text-slate-400"></i>
                                </div>
                                <h2 class="text-xl font-bold text-slate-900 mb-2"><?php esc_html_e( 'No cards found', 'bigtricks' ); ?></h2>
                                <p class="text-slate-500 mb-4">
                                        <?php
                                        printf(
                                                /* translators: %s: term name */
                                                esc_html__( 'We haven\'t ranked any %s cards yet.', 'bigtricks' ),
                                                esc_html( $term_name )
                                        );
                                        ?>
                                </p>
                        </div>
                        <?php endif; ?>

                        <?php while ( $feed_query->have_posts() ) :
                                $feed_query->the_post();
                                get_template_part( 'template-parts/card-credit-card', null, [ 'post_id' => get_the_ID() ] );
                        endwhile;
                        wp_reset_postdata(); ?>
                </div><!-- /#bt-feed-container -->

                <!-- ═══ LOAD MORE ═══ -->
                <?php if ( $max_pages > 1 ) : ?>
                <div class="mt-8 flex justify-center" id="bt-load-more-wrap">
                        <button
                                id="bt-load-more"
                                class="flex items-center gap-3 bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-700 hover:border-primary-400 text-slate-700 dark:text-slate-300 hover:text-primary-600 font-black px-8 py-4 rounded-2xl shadow-sm hover:shadow-md dark:shadow-slate-900/20 dark:hover:shadow-slate-900/40 transition-all active:scale-95"
                                data-page="1"
                                data-max-pages="<?php echo esc_attr( $max_pages ); ?>"
                                data-card-cat="<?php echo esc_attr( $term_id ); ?>"
                                data-type="credit-card"
                                data-nonce="<?php echo esc_attr( wp_create_nonce( 'bigtricks_load_more' ) ); ?>"
                        >
                                <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                                <?php esc_html_e( 'Load More', 'bigtricks' ); ?>
                        </button>
                </div>
                <?php endif; ?>

        </div><!-- /main column -->

        <!-- ═══ SIDEBAR ═══ -->
        <?php get_sidebar(); ?>

</main><!-- /#main-content -->

<?php get_footer(); ?>
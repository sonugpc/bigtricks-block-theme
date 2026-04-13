<?php
/**
 * Default page template
 *
 * @package Bigtricks
 */

get_header();
?>

<main class="max-w-[1400px] mx-auto px-4 py-8 md:py-12 flex flex-col lg:flex-row gap-8 flex-1 w-full" id="main-content">
	<div class="flex-1 min-w-0">
		<?php while ( have_posts() ) :
			the_post();
			?>
		<article class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
			<?php if ( has_post_thumbnail() ) : ?>
			<div class="w-full h-64 sm:h-80 bg-slate-50 overflow-hidden">
				<?php the_post_thumbnail( 'large', [ 'class' => 'w-full h-full object-cover' ] ); ?>
			</div>
			<?php endif; ?>
			<div class="p-8 md:p-12">
				<h1 class="text-3xl md:text-4xl font-black text-slate-900 mb-8">
					<?php the_title(); ?>
				</h1>
				<div class="prose prose-lg prose-slate max-w-none prose-headings:font-black prose-a:text-primary-600 hover:prose-a:text-primary-800 break-words">
					<?php the_content(); ?>
				</div>
			</div>
		</article>
		<?php endwhile; ?>
	</div>
	<?php get_sidebar(); ?>
</main>

<?php get_footer(); ?>

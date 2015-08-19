<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); ?>

<div class="content-search">

	<header class="page-header">
		<h1 class="page-title"><?php pll_e('Paieškos rezultatai raktažodžiams'); ?>:</h1>
		<p class="search-query"> <?php echo get_search_query(); ?></p>
	</header>

	<div class="page-content">
	<?php if (have_posts()) : ?>

		<?php while (have_posts()) : the_post(); ?>

			<?php get_template_part('content', get_post_format()); ?>

		<?php endwhile; ?>

		<?php kcsite_nice_pagination(); ?>

	<?php else : ?>

		<article id="post-0" class="post no-results not-found">
			<div class="entry-content">
				<p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'kcsite' ); ?></p>
				<?php get_search_form(); ?>
			</div><!-- .entry-content -->
		</article><!-- #post-0 -->

	<?php endif; ?>


	</div><!-- .page-content -->
</div>

<?php get_footer(); ?>
<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 */


get_header(); ?>

<div class="content-page page-template-404">

<!-- 	<div class="page-header clearfix">
		<h2 class="fancy-title"><?php pll_e('Puslapiai'); ?></h2>
		<a href="" class="btn-block"><?php pll_e('Puslapiai'); ?></a>
	</div>
	<hr class="double-hr"/> -->

	<div class="page-content">

		<?php //start including slightly changed content-page.php ?>

		<?php
		global $kcsite_post_metabox;
		$kcsite_post_metabox->the_meta();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class('single-page clearfix'); ?>>
		 	<header class="entry-header">
				<h1 class="fancy-title"><?php echo of_get_option_by_lang('kcsite_404_page_title'); ?></h1>
			</header>
			<hr class="double-hr entry-header-hr"/>

			<div class="entry-content">
				<?php echo of_get_option_by_lang('kcsite_404_page_content'); ?>
			</div>

			<footer class="entry-meta">
				<?php edit_post_link( __( 'Edit', 'kcsite' ), '<span class="edit-link">', '</span>' ); ?>
			</footer><!-- .entry-meta -->
		</article><!-- #post-<?php the_ID(); ?> -->


		<?php // EOF ?>
		
	</div><!-- .page-content -->

</div>

<?php get_footer(); ?>
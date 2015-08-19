<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 */

/**
 * Can be used for news categories views. Not prepared now (not used)
 */


get_header(); ?>

<div class="content-page page-template-index">

<!-- 	<div class="page-header clearfix">
		<h2 class="fancy-title"><?php pll_e('Įdomūs faktai'); ?></h2>
		<a href="" class="btn-block"><?php pll_e('Įdomūs faktai'); ?></a>
	</div>
	<hr class="double-hr"/> -->
	
	<div class="page-content in-post-list">

	 	<header class="entry-header">
			<h1 class="fancy-title"><?php the_title(); ?></h1>
		</header>
		<hr class="double-hr entry-header-hr"/>
		
		<?php if (have_posts()) { ?>

			<?php 
			global $k;
			$k = 0;
			while (have_posts()) {
				$k++;
				the_post();
				get_template_part('content', get_post_format());
			} 
			unset($k);
			?>

			<?php kcsite_nice_pagination('', 2); ?>

		<?php } ?>

	</div><!-- .page-content -->

</div>

<?php get_footer(); ?>
<?php
/**
 * The template for displaying all pages.
 *
 */

get_header(); ?>

<div class="content-page">

<!-- 	<div class="page-header clearfix">
		<h2 class="fancy-title"><?php pll_e('Puslapiai'); ?></h2>
		<a href="" class="btn-block"><?php pll_e('Puslapiai'); ?></a>
	</div>
	<hr class="double-hr"/> -->
	<div class="page-content">
		<?php 
		the_post();
		get_template_part('content', 'page'); 	
		?>
	</div><!-- .page-content -->

</div>

<?php get_footer(); ?>
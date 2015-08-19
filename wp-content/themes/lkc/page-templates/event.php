<?php
/*
Template Name: Renginys
*/

get_header(); ?>

<div class="content-page">

	<div class="page-content">
		<?php 
		the_post();
		get_template_part('content', 'page'); 	
		?>
	</div><!-- .page-content -->

</div>

<?php get_footer(); ?>
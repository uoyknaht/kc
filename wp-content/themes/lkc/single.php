<?php
/**
 * The Template for displaying all single posts.
 *
 */

get_header(); ?>

<div class="content-single">

	<div class="page-content">

		<?php 
		global $post;
		if($post->post_type == 'education-resource' && !current_user_can('see_education_resource')){ ?>
			<div class="alert alert-error"><?php pll_e('Jūs neturite teisės matyti šios informacijos');?></div>
		<?php } else {
			the_post();
			get_template_part('content', 'single'); 	
		}?>
	</div><!-- .page-content -->
</div>

<?php get_footer(); ?>
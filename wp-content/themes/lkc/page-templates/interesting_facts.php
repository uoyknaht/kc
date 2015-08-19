<?php
/*
Template Name: Idomus faktai
*/

get_header(); ?>

<div class="content-page page-template-interesting-facts">

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
		
		<?php 
		global $post;
		global $more;

		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		$args = array('post_type' => 'interesting-fact', 'orderby' => 'date', 'order' => 'DESC', 'paged' => $paged, 'posts_per_page' => get_option('posts_per_page'), 'post_status' => 'publish');			
		$the_query = new WP_Query( $args );
		?>					

		<?php 		
		while ($the_query->have_posts()){ 	
			$the_query->the_post();				
			$more = 0;
			get_template_part('content', get_post_format());
		}		
		?>

		<?php 
		kcsite_nice_pagination($the_query->max_num_pages);
		wp_reset_query();
		wp_reset_postdata();
		?>

	</div><!-- .page-content -->

</div>

<?php get_footer(); ?>
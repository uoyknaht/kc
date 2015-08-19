<?php
/*
Template Name: Filmų registras
*/

get_header();

global $lkc_film_register_metabox;
$lkc_film_register_metabox->the_meta();

// print_r($lkc_film_register_metabox);exit;

 ?>

<div class="content-page page-template-film-register">

	
	<div class="page-content">

	 	<header class="entry-header">
			<h1 class="fancy-title"><?php the_title(); ?></h1>
		</header>
		<hr class="double-hr entry-header-hr"/>

		labas
















		<?php 


		// global $post;
		
		// $args = array('limit' => 5, 'post_type' => 'post', 'orderby' => 'date', 'order' => 'DESC', 'paged' => $paged, 'posts_per_page' => get_option('posts_per_page'), 'post_status' => 'publish', 'offset' => 2 , 'lang' => pll_current_language('slug'));			
		// $the_query = new WP_Query( $args );
	
		// $i=0;	
		// while ($the_query->have_posts()){ 	
		// 	$the_query->the_post();
				
		// 	get_template_part('content', get_post_format());		

		// }


		?>

		<?php 
		// kcsite_nice_pagination($the_query->max_num_pages);
		
		 		
		// $max = $the_query->max_num_pages > 3 ? 3 : $the_query->max_num_pages;
		// kcsite_nice_pagination($max);


		wp_reset_query();
		wp_reset_postdata();
		?>

	</div><!-- .page-content -->

</div>

<?php get_footer(); ?>
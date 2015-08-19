<?php
/*
Template Name: Naujienos
The same as news_industry.php template, only queries different posts and featured news category 
*/

get_header(); ?>

<div class="content-page page-template-news">

	<div class="page-content in-post-list">

	 	<header class="entry-header">
			<h1 class="fancy-title"><?php the_title(); ?></h1>
		</header>
		<hr class="double-hr entry-header-hr"/>

		<?php 
		global $post;
		global $more;
		global $staticVars;
		global $g_featuredNewsCategoryId;

		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

		if($paged == 1) {			
			$g_featuredNewsCategoryId = $staticVars['ordinaryNewTypeSlug'];
			get_template_part('template-parts/featured-news');
		}

		//$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
		//$offset = get_option('posts_per_page')*($paged - 1) + 2;
		//echo $offset;
		// $args = array('post_type' => 'post', 'orderby' => 'date', 'order' => 'DESC', 'paged' => $paged, 'posts_per_page' => get_option('posts_per_page'), 'post_status' => 'publish', 'offset' => $offset /*, 'lang' => pll_current_language('slug')*/);			
		
		$args = array('limit' => 5, 'post_type' => 'post', 'new-type' => $staticVars['ordinaryNewTypeSlug'], 'orderby' => 'date', 'order' => 'DESC', 'paged' => $paged, 'posts_per_page' => get_option('posts_per_page'), 'post_status' => 'publish');			
		// $args = array('limit' => 5, 'post_type' => 'post', 'new-type' => 'iprastos-naujienos', 'orderby' => 'date', 'order' => 'DESC', 'paged' => $paged, 'posts_per_page' => get_option('posts_per_page'), 'post_status' => 'publish');			
		

		// $args = array('limit' => 5, 'post_type' => 'post', 'new-type' => 49, 'orderby' => 'date', 'order' => 'DESC', 'paged' => $paged, 'posts_per_page' => get_option('posts_per_page'), 'post_status' => 'publish');			
		$the_query = new WP_Query( $args );
		?>					

		<?php 	
		$i=0;	
		while ($the_query->have_posts()){ 	
			$the_query->the_post();
			$i++;
			if($paged < 2 && $i<3) {
				//do not show first two posts as they are featured and are already shown.
				//Could not exclude them from query without breaking pagination
			} else {
				
				$more = 0;
				get_template_part('content', get_post_format());		
			}
		}		
		?>

		<?php 
		// kcsite_nice_pagination($the_query->max_num_pages);
		
		/*
		 * for news page we need only 3 paged pages, older can be reached through archive
		 */		
		$max = $the_query->max_num_pages > 3 ? 3 : $the_query->max_num_pages;
		kcsite_nice_pagination($max);


		?>
		<div class="clearfix newsarchive-btn-wrap">
			<a href="<?php echo $staticVars['newsArchiveUrl'];?>" class="btn-block fr"><?php pll_e('Naujienų archyvas');?></a>
		</div>
		<?php



		wp_reset_query();
		wp_reset_postdata();
		?>

	</div><!-- .page-content -->

</div>

<?php get_footer(); ?>
<?php
/*
Template Name: Namų puslapis
*/

get_header(); ?>

<div class="page-template-home">
<div class="home-cycles-wrap">

	<?php
	global $post;
	$lang = pll_current_language('slug');
	$cat = $lang == 'lt' ? 'pirma-eile' : 'pirma-eile-'.$lang;
	$args = array('post_type' => 'home-slideshow', 'home-slideshow-category' => $cat,  'orderby' => 'menu_order', 'order' => 'ASC', 'lang' => $lang);
	$slideshowPosts = get_posts($args);
	?>
	<div class="cycle-first-row-wrap">
		<ul class="cycle-first-row">
		<?php 		
		foreach($slideshowPosts as $post) : setup_postdata($post); ?>
			<li class="hs-cycle-item">
				
				<?php 
				$post_thumbnail = kcsite_get_post_thumbnail('home-slideshow', false, false, true);
				if($post_thumbnail) echo $post_thumbnail; 
				?>	

				<div class="hs-cycle-item-overlay">
					<div class="hs-cycle-item-overlay-bg"></div>
					<div class="hs-cycle-item-overlay-inner">
						<h3 class="hs-cycle-item-title"><?php the_title(); ?></h3>
						<div class="hs-cycle-item-content"><?php the_content(); ?></div>
					</div>
				</div>
			</li>			
		<?php endforeach; ?>
		</ul>
	</div>

	<?php
	global $post;
	$lang = pll_current_language('slug');
	$cat = $lang == 'lt' ? 'antra-eile' : 'antra-eile-'.$lang;
	$args = array('post_type' => 'home-slideshow', 'home-slideshow-category' => $cat,  'orderby' => 'menu_order', 'order' => 'ASC', 'lang' => $lang);
	$slideshowPosts = get_posts($args);
	?>

	<div class="cycle-second-row-wrap">
		<ul class="cycle-second-row">
		<?php 		
		foreach($slideshowPosts as $post) : setup_postdata($post); ?>		
			<li class="hs-cycle-item">

				<?php 
				$post_thumbnail = kcsite_get_post_thumbnail('home-slideshow', false, false, true);
				if($post_thumbnail) echo $post_thumbnail; 
				?>
		
				<div class="hs-cycle-item-overlay">
					<div class="hs-cycle-item-overlay-bg"></div>
					<div class="hs-cycle-item-overlay-inner">
						<h3 class="hs-cycle-item-title"><?php the_title(); ?></h3>
						<div class="hs-cycle-item-content"><?php the_content(); ?></div>
					</div>
				</div>
			</li>	
		<?php endforeach; ?>
		</ul>
	</div>

	<div class="area-to-animate-home-bubles"></div>


	
</div><!-- .home-cycles-wrap -->
</div>

<?php get_footer(); ?>
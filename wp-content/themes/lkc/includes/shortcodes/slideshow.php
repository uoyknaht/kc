<?php

	add_shortcode('kcsite_slideshow', 'kcsite_slideshow_handler');
	
	function kcsite_slideshow_handler($atts, $content = null) {
		extract(shortcode_atts(array(
			"cat" => '',
			"cat_en" => '',
			"id" => 'slideshow',
			"fx" => 'fade',
			"speed" => 500,
			"timeout" => 4000,
			"pause" => 1,					
			"show_nav" => 0,
			"show_pager" => 0,
			"show_image" => 1,
			"show_title" => 0,
			"title_as_link" => 1,
			"show_content" => 0,
			"show_edit" => 0,

		), $atts));
		
		ob_start();
		?>

		<script type="text/javascript">
		jQuery(document).ready(function(){	
				jQuery('#<?php echo $id;?>-cycle').cycle({
					<?php if($show_nav == true) {?>			
					    next:   '#<?php echo $id;?>-cycle-next', 
					    prev:   '#<?php echo $id;?>-cycle-prev',
				    <?php } ?>
				    <?php if($show_pager == true) {?>		
				    pager:  '#<?php echo $id;?>-cycle-pager',
					pagerAnchorBuilder: function(index, el) {
				       return '<li><a></a></li>'; 
				   },
				   <?php } ?>	
					fx:    '<?php echo $fx;?>', 
					speed:  <?php echo $speed;?>,			
					timeout: <?php echo $timeout;?>,
					pause : <?php echo $pause;?>
				});	
			});
		</script>

		<?php
/*		if(pll_current_language('slug') == 'en'){
			$the_cat = $cat_en;
		} else {
			$the_cat = $cat;
		}*/

		global $post;
		//can't find out how to filter by category
		$args = array('numberposts' => -1, 'post_type' => 'slideshow', /*'category_name' => 'headline',*/  'orderby' => 'menu_order', 'order' => 'ASC'/*, 'lang' => pll_current_language('slug')*/);
		$slideshow_posts = get_posts($args);
		// echo $the_cat;
		//print_r($slideshow_posts);
		?>

		<div id="<?php echo $id;?>-cycle-wrap" class="clearfix">
		 
		 	<?php if($show_pager == true) {?>
			<ul id="<?php echo $id;?>-cycle-pager"></ul>	
			<?php } ?>

			<?php if($show_nav == true) {?>
			<a id="<?php echo $id;?>-cycle-next" href=""></a>
			<a id="<?php echo $id;?>-cycle-prev" href=""></a>
			<?php } ?>
		 
			<ul id="<?php echo $id;?>-cycle">

			<?php 		
			foreach( $slideshow_posts as $post ) :	setup_postdata($post); 			
			?>
				<li class="<?php echo $id;?>-cycle-item">
				
					<?php if($show_image == true) {?>
					<a href="<?php the_permalink() ?>">
					<?php 
						if (has_post_thumbnail($post->ID)) {
							echo get_the_post_thumbnail($post->ID,'slideshow');
						} else { ?>					
							<!-- <img class="hs-image" src="<?php echo get_template_directory_uri()?>/images/slideshow-default.png" />							 -->
						<?php 	
						}
					?>			
					</a>
					<?php } ?>
		
					
					<?php if($show_title == true) {?>
						<?php if($title_as_link == 1) {?>
							<a href="<?php the_permalink() ?>">
						<?php } ?>
								<h2><?php the_title()?></h2>
						<?php if($title_as_link == 1) {?>
							</a>
						<?php } ?>
					<?php } ?>
	
					<?php if($show_content == true) {?>
					<div class="<?php echo $id;?>-cycle-item-content">
						<?php the_excerpt();?>			
					</div>
					<?php } ?>
							
					<?php if($show_edit == true) {?>	
					<footer class="entry-meta">
					<?php edit_post_link( __( 'Edit', 'kcsite' ), '<span class="edit-link">', '</span>' ); ?>
					</footer>
					<?php } ?>			
				</li>

			<?php 
			endforeach;
			?>
				
			</ul>
		</div><!-- #slideshow -->

		<?php
		$output = ob_get_contents();
		ob_end_clean();

		return do_shortcode($output);		
	}
?>
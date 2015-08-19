<?php
/**
 * The template for displaying content in the single.php template
 * Used for news and interesting facts
 */
?>

<?php
global $kcsite_post_metabox;
$kcsite_post_metabox->the_meta();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('single-article clearfix'); ?>>
 	<header class="entry-header">
		<h1 class="fancy-title">
			<?php if(get_post_type() == 'interesting-fact'){
				pll_e('Įdomūs faktai');
			} else {
				the_title();				
			}
			?>
		</h1>

	</header>
	<hr class="double-hr entry-header-hr"/>

	<?php if(get_post_type() != 'tribe_events' && get_post_type() != 'education-resource'){ ?>
		<div class="entry-date"><?php kcsite_posted_on();?></div>
	<?php } ?>

	<?php if($kcsite_post_metabox->get_the_value('subtitle')) { ?>
		<h2 class="sub-title"><?php echo $kcsite_post_metabox->get_the_value('subtitle'); ?></h2>
	<?php } ?>	

	<div class="entry-content">

		<?php 			
		if (has_post_thumbnail()) {		
			if(!$kcsite_post_metabox->get_the_value('hide_feat_img_insingle') && get_post_type() != 'tribe_events') {
				$size = !$kcsite_post_metabox->get_the_value('show_not_cropped') ? 'medium-cropped' : 'medium'; 
				$post_thumbnail = kcsite_get_post_thumbnail($size, 'single-post-view-post-thumbnail');
				if($post_thumbnail){ ?>
					<!-- <p><a href="<?php the_permalink();?>"><?php echo $post_thumbnail; ?></a></p> -->
					<p><?php echo $post_thumbnail; ?></p>
				<?php }				
			}
		}
		?>


		<?php the_content(); ?>

		<?php get_template_part('template-parts/entry-meta'); ?>

		<?php 
		if(get_post_type() != 'education-resource'){ 
			kcsite_referer_btn(); 
		}
		?>

		<?php //wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'kcsite' ) . '</span>', 'after' => '</div>' ) ); ?>
	</div>

	

</article><!-- #post-<?php the_ID(); ?> -->
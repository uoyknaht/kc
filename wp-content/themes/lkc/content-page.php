<?php
/**
 * The template used for displaying page content in page.php
 *
 */
?>

<?php
global $kcsite_post_metabox;
$kcsite_post_metabox->the_meta();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('single-article clearfix'); ?>>
 	<header class="entry-header">
		<h1 class="fancy-title"><?php the_title(); ?></h1>
		<?php if($kcsite_post_metabox->get_the_value('subtitle') && get_post_type() != 'tribe_events') { ?>
			<h2 class="sub-title"><?php echo $kcsite_post_metabox->get_the_value('subtitle'); ?></h2>
		<?php } ?>
	</header>
	<hr class="double-hr entry-header-hr"/>

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

		<?php //wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'kcsite' ) . '</span>', 'after' => '</div>' ) ); ?>
	</div>

	<?php get_template_part('template-parts/entry-meta'); ?>

</article><!-- #post-<?php the_ID(); ?> -->
<?php
/*
Template Name: Naujienlaiskis
*/

get_header(); ?>

<div class="content-page page-template-polls-newsletter">

<!-- 	<div class="page-header clearfix">
		<h2 class="fancy-title"><?php pll_e('Puslapiai'); ?></h2>
		<a href="" class="btn-block"><?php pll_e('Puslapiai'); ?></a>
	</div> -->
	

	<div class="page-content">

		<?php //start including slightly changed content-page.php ?>

		<?php
		global $kcsite_post_metabox;
		$kcsite_post_metabox->the_meta();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class('single-page clearfix'); ?>>
		 	<header class="entry-header">
				<h1 class="fancy-title"><?php the_title(); ?></h1>
				<?php if($kcsite_post_metabox->get_the_value('subtitle')) { ?>
					<h2 class="sub-title"><?php echo $kcsite_post_metabox->get_the_value('subtitle'); ?></h2>
				<?php } ?>
			</header>
			<hr class="double-hr entry-header-hr"/>

			<div class="entry-content">

				<?php //the_widget('ALO_Easymail_Widget'); ?>
				<?php echo do_shortcode('[ALO-EASYMAIL-PAGE]');?>
				<br><br>

			</div>

			<?php get_template_part('template-parts/entry-meta'); ?>

		</article><!-- #post-<?php the_ID(); ?> -->


		<?php // EOF ?>
		
	</div><!-- .page-content -->

</div>

<?php get_footer(); ?>
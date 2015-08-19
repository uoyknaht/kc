<?php
/*
Template Name: Archyvas (naujienų)
The same as archive_industry_news.php, only query a bit differs
*/

get_header(); ?>

<div class="content-page page-template-archive">

	<div class="page-content">

		<?php //start including slightly changed content-page.php ?>

		<?php
		global $kcsite_post_metabox;
		$kcsite_post_metabox->the_meta();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class('single-article clearfix'); ?>>
		 	<header class="entry-header">
				<h1 class="fancy-title"><?php the_title(); ?></h1>
				<?php if($kcsite_post_metabox->get_the_value('subtitle')) { ?>
					<h2 class="sub-title"><?php echo $kcsite_post_metabox->get_the_value('subtitle'); ?></h2>
				<?php } ?>
			</header>
			<hr class="double-hr entry-header-hr"/>

			<div class="entry-content">
				<?php global $staticVars; ?>
				<?php //smart_archives(array('format' => 'fancy'), array('lang' => pll_current_language('slug'), 'post_type' => 'industry-new')); ?>
				<?php smart_archives(array('format' => 'fancy'), array('lang' => pll_current_language('slug'), 'post_type' => 'post', 'new-type' => $staticVars['ordinaryNewTypeSlug'])); ?>

			</div>

			<?php get_template_part('template-parts/entry-meta'); ?>
			
		</article><!-- #post-<?php the_ID(); ?> -->


		<?php // EOF ?>
		
	</div><!-- .page-content -->

</div>

<?php get_footer(); ?>
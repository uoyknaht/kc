<?php
/*
Template Name: Apklausa
*/

get_header(); ?>

<div class="content-page page-template-poll">

<!-- 	<div class="page-header clearfix">
		<h2 class="fancy-title"><?php pll_e('Puslapiai'); ?></h2>
		<a href="" class="btn-block"><?php pll_e('Puslapiai'); ?></a>
	</div>
	<hr class="double-hr"/> -->

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

				<?php 
				if (function_exists('vote_poll')) {
					get_poll();
				} 
				?>
				<br><br>

			</div>

			<footer class="entry-meta">
				<?php edit_post_link( __( 'Edit', 'kcsite' ), '<span class="edit-link">', '</span>' ); ?>
			</footer><!-- .entry-meta -->
		</article><!-- #post-<?php the_ID(); ?> -->


		<?php // EOF ?>
		
	</div><!-- .page-content -->

</div>

<?php get_footer(); ?>
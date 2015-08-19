<?php
/*
Template Name: Naujienlaiskio patvirtinimas
*/

get_header(); ?>

<div class="content-page page-template-poll">


	<div class="page-content">

		<?php //start including slightly changed content-page.php ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class('single-page clearfix'); ?>>
		 	<header class="entry-header">
				<h1 class="fancy-title"><?php the_title(); ?></h1>
			</header>
			<hr class="double-hr entry-header-hr"/>

			<div class="entry-content">






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
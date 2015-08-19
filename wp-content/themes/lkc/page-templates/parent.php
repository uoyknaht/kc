<?php
/**
 * Template Name: Tėvinis puslapis
 */

get_header(); ?>

<div class="content">

	<?php 
	global $post;
	the_post(); 
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</header>

		<div class="entry-content">
			<?php the_content(); ?>
			<ul>
				<?php wp_list_pages('title_li=&child_of='.$post->ID); ?>
			</ul>
			<?php //wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'kcsite' ) . '</span>', 'after' => '</div>' ) ); ?>
		</div>

		<footer class="entry-meta">
			<?php edit_post_link( __( 'Edit', 'kcsite' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-meta -->
	</article><!-- #post-<?php the_ID(); ?> -->

</div>

<?php get_footer(); ?>
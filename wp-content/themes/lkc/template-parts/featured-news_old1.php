<?php
			
global $post;
global $more;	

$args = array('numberposts' => 2, 'post_type' => 'post', 'orderby' => 'date', 'order' => 'DESC', 'post_status' => 'publish', 'nopaging ' => true, 'posts_per_page' => 2);
$the_query = new WP_Query($args);
//print_r($the_query);
?>					

<div class="feat-news clearfix">
<?php 	
$i=0;	
while ($the_query->have_posts()){ 				
	$the_query->the_post();
	$more = 0;
	$i++;
	$additionalClass = $i == 1 ? ' first' : '';

	global $kcsite_post_metabox;
	$kcsite_post_metabox->the_meta();

	$post_thumbnail = kcsite_get_post_thumbnail('feat-thumbnail');
	$articleClass = $post_thumbnail ? ' has-post-thumbnail' : '';
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class('in-post-list clearfix'.$articleClass.$additionalClass); ?>>
	
		<h2 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title(); ?></a></h2>
		
		<?php if(get_post_type() != 'tribe_events' && get_post_type() != 'interesting-fact'){ ?>
			<div class="entry-date"><?php kcsite_posted_on();?></div>
		<?php } ?>		

		<?php if($post_thumbnail){ ?>
			<?php if(!$kcsite_post_metabox->get_the_value('hide_feat_img') && get_post_type() != 'tribe_events') { ?>
				<a href="<?php the_permalink();?>">
					<?php echo $post_thumbnail; ?>
				</a>
			<?php } ?>
		<?php } ?>

		<div class="entry-excerpt">				
			<?php 
			if (is_search()) {
				the_excerpt();
			} else {
				if($post->post_excerpt) { 
					the_excerpt(); 
				} else {
					//the_excerpt();
					//the_content(__('Read more <span class="meta-nav">&raquo;</span>', 'kcsite'));
					the_content('');
				}
			}
			?>
			<a href="<?php the_permalink(); ?>" class="btn-block read-more"><?php pll_e('Skaityti daugiau'); ?></a>
		</div>

	</article><!-- #post-<?php the_ID(); ?> -->

<?php 
} 
wp_reset_query();
wp_reset_postdata();
?>		
				
</div>
<hr class="double-hr"/>
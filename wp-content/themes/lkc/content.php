<?php
/**
 * The default template for displaying content (categories, tags blog page, search, news blog)
 *
 */

global $kcsite_post_metabox;
$kcsite_post_metabox->the_meta();

$post_thumbnail = kcsite_get_post_thumbnail('thumbnail');
// $articleClass = ($post_thumbnail && !$kcsite_post_metabox->get_the_value('hide_feat_img') && get_post_type() != 'tribe_events' ) ? ' has-post-thumbnail' : '';
$articleClass = (!$kcsite_post_metabox->get_the_value('hide_feat_img') && get_post_type() != 'tribe_events' ) ? ' has-post-thumbnail' : '';
if(get_post_type() == 'tribe_events') $articleClass = '';
if(get_post_type() == 'interesting-fact') $articleClass = '';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('in-post-list clearfix'.$articleClass); ?>>
	<?php 
	if(!$kcsite_post_metabox->get_the_value('hide_feat_img') && get_post_type() != 'tribe_events') {
		if (has_post_thumbnail()) {	
			if($post_thumbnail){ ?>
				<a href="<?php the_permalink();?>">
					<?php echo $post_thumbnail; ?>
				</a>
			<?php 
			}
		} else {
			if(get_post_type() != 'interesting-fact'){ ?>
				<a href="<?php the_permalink();?>">
					<img src="<?php echo get_template_directory_uri();?>/img/default-thumb.jpg" width="88" height="82" class="post-thumbnail">
				</a>
			<?php 
			}
		}
	} ?>

	 <div class="entry-excerpt">
		<h2 class="entry-title"><a href="<?php the_permalink();?>"><?php the_title(); ?></a></h2>
		
		<?php if(get_post_type() != 'tribe_events' && get_post_type() != 'interesting-fact'){ ?>
			<div class="entry-date"><?php kcsite_posted_on();?></div>
		<?php } ?>

		<?php 
		if (is_search()) {
			the_excerpt();
			//the_advanced_excerpt();
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
		<?php if(get_post_type() != 'tribe_events'){?>
		<a href="<?php the_permalink(); ?>" class="btn-block read-more"><?php pll_e('Skaityti daugiau'); ?></a>
		<?php } ?>
	</div>

</article><!-- #post-<?php the_ID(); ?> -->
<hr class="double-hr"/>

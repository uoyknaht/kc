<div class="sidebar-right">

	<?php 
	if(current_user_can('educator')){
		$current_user = wp_get_current_user();
	// 	if($current_user->ID;	
	// }
	?>
	<aside class="widget">
		<header>
			<h3 class="widget-title"> <?php pll_e('Vartotojo informacija');?> &nbsp; <i class="icon-user"></i></h3>
		</header>
		<div class="entry-content">
			<ul>
				<li><?php pll_e('Prisijungėte su el. paštu');?> <a href="mailto:<?php echo $current_user->user_email;?>"><?php echo $current_user->user_email;?></a></li>
				<!-- <li><a href=""><?php pll_e('Jūsų profilis');?></a></li> -->
				<li><a href="<?php echo wp_logout_url( add_query_arg('action', 'logout', get_permalink()) ); ?>"><?php pll_e('Atsijungti');?></a></li>
			</ul>
		</div>
	</aside>
	<?php } ?>


    <?php 
    wp_reset_postdata();   
    global $staticVars;
    global $g_lithuanianFilms;

    if(get_the_ID() == $staticVars['lithuanianFilmsSearchPageId'] || get_post_type() == $g_lithuanianFilms->cptSlug) {
        echo do_shortcode('[lithuanian_films_paieskos_forma]');
    }

	the_widget('TribeEventsListWidget', 
	array("title" => pll__('Artimiausi renginiai')), 
	array('before_title' => '<header class="upcoming-events-header"><h3 class="widget-title">', 'after_title' => '</h3></header>','before_widget' => '<aside class="widget clearfix upcoming-events">', 'after_widget' => '</aside>')); 
	?> 

	<hr class="double-hr" />

	<?php kcsite_sidebar_poll() ?>

	<ul class="sidebar-right-banners widget">
		<?php

		$lang_slug = kcsite_get_lang_slug();
		$banners_number_option = of_get_option('kcsite_right_sidebar_banners_number'.$lang_slug);
		$banners_number = intval((is_numeric($banners_number_option) && $banners_number_option > 0) ? $banners_number_option : 0);

		for($i=0; $i<$banners_number; $i++){ ?>
			<?php if(!of_get_option('kcsite_rb_hide_'.$lang_slug.$i)){ ?>
			<li>
				<a href="<?php echo esc_url(of_get_option('right_sidebar_banner_link_'.$lang_slug.$i));?>" <?php if(of_get_option('right_sidebar_banner_link_new_window_'.$lang_slug.$i) == 1) echo 'target="_blank"';?>>
					<?php if(of_get_option('kcsite_rb_red_border_'.$lang_slug.$i)){ ?>
						<span class="red-border banner-red-border"></span>
					<?php } ?>					
					<img src="<?php echo of_get_option('right_sidebar_banner_img_path_'.$lang_slug.$i);?>"/>
				</a>
			</li>
			<?php }	?>
		<?php }	?>
	</ul>


	<?php 
	if(is_active_sidebar('sidebar-r')){
		dynamic_sidebar('sidebar-r');
	} 
	?> 

	<div class="widget">
		<?php //echo do_shortcode('[lkc_newsletter_subscribe_form]'); ?>
	</div>

</div>
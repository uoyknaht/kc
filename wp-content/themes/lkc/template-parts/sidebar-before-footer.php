<div class="sidebar-before-footer clearfix">
	<div class="wrap">
		<?php if(is_front_page()){ ?>
			<?php /*
			<div class="sidebar-banners-before-footer">

				<ul class="home-baners">
				<?php
				$lang_slug = kcsite_get_lang_slug();
				$banners_number_option = of_get_option('kcsite_home_banners_number'.$lang_slug);
				$banners_number = intval((is_numeric($banners_number_option) && $banners_number_option > 0) ? $banners_number_option : 0);

				for($i=0; $i<$banners_number; $i++){ ?>
					<?php if(!of_get_option('kcsite_hb_hide_'.$lang_slug.$i)){ ?>
					<li class="help-inline">
						<a href="<?php echo esc_url(of_get_option('home_banner_link_'.$lang_slug.$i));?>" <?php if(of_get_option('home_banner_link_new_window_'.$lang_slug.$i) == 1) echo 'target="_blank"';?>>
							<?php if(of_get_option('kcsite_hb_red_border_'.$lang_slug.$i)){ ?>
								<span class="red-border banner-red-border"></span>
							<?php } ?>
							<img src="<?php echo of_get_option('home_banner_img_path_'.$lang_slug.$i);?>" class="help-inline" />
						</a>
					</li>
					<?php }	?>
				<?php }	?>
				</ul>

			</div>
			*/ ?>
		<?php } else { ?> 				

			<?php wp_nav_menu(array('theme_location' => 'secondary', 'menu_class' => 'bottom-nav clearfix', 'container' => false, 'fallback_cb' => 'fallback_nav_menu')) ; ?>

		<?php } ?> 	
	</div>
</div>
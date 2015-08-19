<div class="col-left">


    <?php 
    // lithuanian films menu:

    // global $staticVars;
    // global $g_lithuanianFilms;

    // if(get_the_ID() == $staticVars['lithuanianFilmsSearchProducablePageId'] || get_the_ID() == $staticVars['lithuanianFilmsSearchPageId'] || get_post_type() == $g_lithuanianFilms->cptSlug) {
    //     // echo do_shortcode('[lithuanian_films_paieskos_forma]');
    //     wp_nav_menu(array('theme_location' => 'left_col_films_menu', 'menu_class' => 'sub-nav lith-film-sub-nav', 'container' => false)) ;
    // }

    ?>



	<?php 
    // left menu:

	//menu becomes inactive on single post (not page). Need to help him
	$additional_sub_nav_class = false;
	if(is_single() && get_post_type() == 'post') {
		$additional_sub_nav_class = ' sub-nav-active-single-new';
	} elseif(is_single() && get_post_type() == 'interesting-fact') {
		$additional_sub_nav_class = ' sub-nav-active-interesting-fact';
	} 

	$top_nav = wp_nav_menu(array('theme_location' => 'primary', 'menu_class' => 'sub-nav'.$additional_sub_nav_class, 'container' => false, 'fallback_cb' => 'fallback_nav_menu', 'echo' => false));
	$bottom_nav = wp_nav_menu(array('theme_location' => 'secondary', 'menu_class' => 'sub-nav', 'container' => false, 'fallback_cb' => 'fallback_nav_menu', 'echo' => false));

	//show navs only if they have active items
	//show only one of the navs if both have same pages
	if (strpos($top_nav,'current-menu-item') !== false) {
	    echo $top_nav;
	} elseif(strpos($bottom_nav,'current-menu-item')){
		echo $bottom_nav;
	} elseif(is_single() && get_post_type() == 'interesting-fact'){
		
		//as 'interesting-fact' is not a page and does not have parent pages, we need to to manually show left menu	
		$menu_slug = 'pagrindinis-meniu';
		$menu_items = wp_get_nav_menu_items($menu_slug);

		//get parent page ID by checking which page uses interesting fatcs page template
		$pages = get_pages(array(
		    'meta_key' => '_wp_page_template',
		    'meta_value' => 'page-templates/interesting_facts.php',
		    'lang' => pll_current_language('slug')
		    //'hierarchical' => 0
		));
		$parent_page_id = $pages[0]->ID;

		$facts_menu_item_id = 154; //backup if will not be found, so js makes no error
		foreach((array)$menu_items as $key => $item){			
			if($item->object_id == $parent_page_id){
				$facts_menu_item_id = $item->ID;
			}
		}

		?>
		<script type="text/javascript">
		jQuery(document).ready(function(){

	        var factsMenuItem = jQuery('.col-left .sub-nav').find('.menu-item-<?php echo $facts_menu_item_id;?>');

	        factsMenuItem.addClass('current-menu-item');
	        factsMenuItem.parents('li:eq(0)').addClass('current-menu-parent current-menu-ancestor');
	        factsMenuItem.parents('li:eq(1)').addClass('current-menu-ancestor');
		})
		</script>

		<?php
		 echo $top_nav;

	}



	?>
	
</div>
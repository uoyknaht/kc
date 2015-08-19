<?PHP
/**
 * Remove standard image sizes so that these sizes are not
 * created during the Media Upload process
 *
 * Tested with WP 3.2.1
 *
 * Hooked to intermediate_image_sizes_advanced filter
 * See wp_generate_attachment_metadata( $attachment_id, $file ) in wp-admin/includes/image.php
 *
 * @param $sizes, array of default and added image sizes
 * @return $sizes, modified array of image sizes
 * @author Ade Walker http://www.studiograsshopper.ch
 */
function sgr_filter_image_sizes( $sizes) {
		
	unset( $sizes['thumbnail']);
	unset( $sizes['medium']);
	unset( $sizes['large']);
	
	return $sizes;
}
add_filter('intermediate_image_sizes_advanced', 'sgr_filter_image_sizes');




// set the post excerpt length to 40 words.
function kcsite_excerpt_length( $length ) {
	return 40;
}
add_filter('excerpt_length', 'kcsite_excerpt_length');



// "Continue Reading" link for excerpts    not used
function kcsite_continue_reading_link() {
	return ' <a class="excerpt-readmore" href="'. esc_url( get_permalink() ).'">'.__('Read more').'  &raquo;</a>';
}

// replacer "[...]" (appended to automatically generated excerpts) with an ellipsis and kcsite_continue_reading_link().
function kcsite_auto_excerpt_more( $more ) {
	return '&hellip;' . kcsite_continue_reading_link();
}



// adds 'last' class to the very last menu item
function add_last_item_class($strHTML) {
	$intPos = strripos($strHTML,'menu-item');
	printf("%s last %s",
		substr($strHTML,0,$intPos),
		substr($strHTML,$intPos,strlen($strHTML))
	);
}
add_filter('wp_nav_menu','add_last_item_class');



//need to make sub-nav active on single interesting-fact view
function nav_menu_add_classes2($items, $args) {

	//print_r($items);
	global $post;
	$i=0;
	$j = false;
	$a = false;
	foreach($items as $item){	
		$i++;
		if(is_single() && get_post_type() == 'interesting-fact'){

			//get parent page ID by checking which page uses interesting fatcs page template
			$pages = get_pages(array(
			    'meta_key' => '_wp_page_template',
			    'meta_value' => 'page-templates/interesting_facts.php',
			    'lang' => pll_current_language('slug')
			    //'hierarchical' => 0
			));
			$parent_page_id = $pages[0]->ID;

			if($item->object_id == $parent_page_id){
				// $item->classes[] = 'current_page_parent';
				$item->classes[] = 'current_menu_item';
				$j = $i-1;
				$a = $item->ID;
			}
		}
	}
	if($j){
		// $items[$j]->classes[] = 'current_page_parent';
		$items[1]->classes[] = 'current_page_ancestor';
	}
	if($a){
		echo $a;
	}


    return $items;
}
add_filter('wp_nav_menu_objects', 'nav_menu_add_classes2', 10, 2);



/**
 * On saving post, if featured image is not set, set as featured first image
 *
 * If the the image is an attachment we just set the id as the post thumbnail,
 * if not then we get the first image from the post content , upload it using media_sideload_image and then we set the thumbnail
 * 
 * @param (int) $post_id post id
 * 
 * @return Void
 */
function set_featured_image_on_save($post_id){
    $attachments = get_posts(array('numberposts' => '1', 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC'));
    if(sizeof($attachments) > 0){
        set_post_thumbnail($post_id, $attachments[0]->ID);
    }
}

/**
 * runs on post save and check's if we already have a post thumbnail, if not it gets one
 * 
 * @param  (int) $post_id 
 * @return Void
 */
function auto_set_post_image($post_id) {
    // verify if this is an auto save routine. 
      // If it is our form has not been submitted, so we dont want to do anything
      if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
          return;

    // Check permissions
    if ( 'page' == $_POST['post_type'] ){
    	//we do not need auto feat image on pages
        //if ( !current_user_can( 'edit_page', $post_id ) )
        return;
    }else{
        if ( !current_user_can( 'edit_post', $post_id ) )
            return;
    }

    // OK, we're authenticated: we need to find and save the data

    //check if we have a post thumnail set already
    $attch = get_post_meta($post_id,"_thumbnail_id",true);
    if (empty($attch)){
        set_featured_image_on_save($post_id);
    }
}
add_action('save_post', 'auto_set_post_image');



//All in one seo pack takes care of this 
function kcsite_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title = get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'twentytwelve' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'kcsite_wp_title', 10, 2 );





function add_theme_styles() {
	wp_register_style('dynamic-style', get_template_directory_uri() . '/css/styles.php');
	wp_enqueue_style('dynamic-style');		
}
add_action('wp_print_styles', 'add_theme_styles');


// custom admin login logo
function kcsite_custom_login_logo() {
	echo '<style type="text/css">
	h1 a { background-image: url('.get_bloginfo('template_directory').'/images/custom-wpadmin-login-logo.png) !important; background-size: auto !important; }
	</style>';
}
add_action('login_head', 'kcsite_custom_login_logo');




// Create the function to output the contents of our Dashboard Widget
function kcsite_dashboard_widget_text() { ?>
			<style type="text/css">	
			#ie-dashboard-warning {
				background: #910800;
				color: #ebebeb;
				padding: 5px 5px;
			}
			#ie-dashboard-warning ul {
				list-style: square;
				margin-left:20px;
			}
			#ie-dashboard-warning img {
				float: right;
			}
			#ie-dashboard-warning .get-chrome a {
				font-size: 18px;
				color: #ffffff;
				text-decoration: underline;
			}
			</style>

		<p>Dokumentaciją, kaip valdyti svetainę, rasite <strong><a href="" target="_blank">ČIA</a></strong></p>
		<!--[if lte IE 8]>
		<div id="ie-dashboard-warning">
			<img src="http://s.wordpress.org/images/browsers/ie.png"/>
			<p>Jūs naudojate pasenusią Internet Explorer naršyklę.
			Pasenusios naršyklės naudojimas yra blogai, nes:</p>
			<ul>
				<li>tai nesaugu jūsų kompiuteriui;</li>
				<li>svetainė veikia daug lėčiau nei galėtų;</li>
				<li>negalėsite naudotis kai kuriomis labai patogiomis funkcijomis (pvz, įkelti paveikslėlius tempiant juos su pele).</li>
			</ul>
			<p class="get-chrome">
				<a href="https://www.google.com/intl/lt/chrome/browser/" target="_blank">Atsisiųskite modernią ir greitą interneto naršyklę Google Chrome!</a>
			</p>
			<div style="clear: both;"></div
		</div>
		<![endif]-->
<?php
} 

function example_add_dashboard_widgets() {
	wp_add_dashboard_widget('kcsite_dashboard_welcome_widget', 'Sveiki prisijungę prie kultūros centro svetainės!', 'kcsite_dashboard_widget_text');	
} 
add_action('wp_dashboard_setup', 'example_add_dashboard_widgets' );





?>
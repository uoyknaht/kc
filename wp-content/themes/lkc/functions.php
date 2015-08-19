<?php
/**
 * Functions
 */

$includes_path = TEMPLATEPATH . '/includes/';
include_once $includes_path.'shortcode-setup.php';
include_once $includes_path.'metaboxes-setup.php';

if(!session_id()) session_start();
include_once $includes_path.'class.messages.php';

// include_once $includes_path.'roles_init.php';


// Education class!
include_once $includes_path.'education/education.class.php';
$education = new Education();
$education->registerHooks();
$education->registerStrings();
// init these only once:
// $education->createTables();
// $education->initRoles();



// theme setup
add_action('after_setup_theme', 'kcsite_theme_setup' );
if(!function_exists('kcsite_theme_setup')){
function kcsite_theme_setup() {

	//define global static (hardcoded) vars	
	global $staticVars;
	$siteurl = get_bloginfo('siteurl');

	$calendarOptions = get_option('tribe_events_calendar_options');
	$calendarSlug = $calendarOptions['eventsSlug'];


	if(pll_current_language('slug') == 'en'){
		$staticVars = array(
			'siteUrl' => $siteurl,
			'sitetreeUrl' => get_permalink(804),
			// 'eventsUrl' => get_permalink(397),
			'eventsUrl' => $siteurl.'/en/'.$calendarSlug,
			'educationResourceUrl' => get_permalink(kcsite_getPageByTempate('education_list.php')),
			'newsUrl' => get_permalink(177),
			'industryNewsUrl' => get_permalink(4109),
			'keysListUrl' => get_permalink(599),
			'newsArchiveUrl' => get_permalink(181),		//used in news blog page for news arhive btn
			'industryNewsArchiveUrl' => get_permalink(4108),	
			// 'newsCategoryID' => 33,
			// 'industryNewsCategoryID' => 34,
			'ordinaryNewTypeID' => 37,
			'industryNewTypeID' => 39,
            'filmRegisterSearchPageId' => 4393,
            'lithuanianFilmsSearchPageId' => 7717, // not set yet
			'lithuanianFilmsSearchProducablePageId' => 7719, // not set yet
		);

	} else {
		$staticVars = array(
			'siteUrl' => $siteurl,
			'sitetreeUrl' => get_permalink(458),
			// 'eventsUrl' => get_permalink(595),
			'eventsUrl' => $siteurl.'/'.$calendarSlug,
			'educationResourceUrl' => get_permalink(kcsite_getPageByTempate('education_list.php')),
			'newsUrl' => get_permalink(2),
			'industryNewsUrl' => get_permalink(4106),
			'keysListUrl' => get_permalink(597),
			'newsArchiveUrl' => get_permalink(86),
			'industryNewsArchiveUrl' => get_permalink(4107),	
			// 'newsCategoryID' => 31,
			// 'industryNewsCategoryID' => 32,		
			'ordinaryNewTypeID' => 36,
			'industryNewTypeID' => 38,				
            'filmRegisterSearchPageId' => 4393,
            'lithuanianFilmsSearchPageId' => 7713,
			'lithuanianFilmsSearchProducablePageId' => 7715,
		);
	}

	load_theme_textdomain('kcsite', TEMPLATEPATH . '/languages');
	$locale = get_locale();
	$locale_file = TEMPLATEPATH . "/languages/$locale.php";
	if (is_readable($locale_file))	require_once( $locale_file );

	add_editor_style();

	// Add default posts and comments RSS feed links to <head>.
	//add_theme_support( 'automatic-feed-links' );

	register_nav_menu('primary', __('Primary Menu', 'kcsite'));
    register_nav_menu('secondary', __('Secondary Menu', 'kcsite'));
	register_nav_menu('left_col_films_menu', __('Filmų meniu', 'kcsite'));

	function fallback_nav_menu(){}

	//add_theme_support('post-formats'/*, array( 'aside', 'link', 'gallery', 'status', 'quote', 'image')*/);
	add_theme_support('post-thumbnails');	
	add_image_size('home-slideshow', 627, 338, true); 	
	add_image_size('medium-cropped', 505, 175, true);  /* for single post view, cropped to be narrower */	
	add_image_size('feat-thumbnail', 243, 160, true); /* for two featured news in news list */	
	//set_post_thumbnail_size() To set a custom post thumbnail size.

	// register sidebars
	function kcsite_widgets_init() {			
		register_sidebar( array(
			'name' => __( 'Left sidebar', 'kcsite' ),
			'id' => 'sidebar-l',
			'before_widget' => '<aside id="%1$s" class="widget clearfix %2$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));		
		register_sidebar( array(
			'name' => __( 'Right sidebar', 'kcsite' ),
			'id' => 'sidebar-r',
			'before_widget' => '<aside id="%1$s" class="widget clearfix %2$s">',
			'after_widget' => "</aside>",
			'before_title' => '<h3 class="widget-title">',
			'after_title' => '</h3>',
		));
/*
		register_sidebar( array(
			'name' => __('Banners Before Footer', 'kcsite'),
			'id' => 'sidebar-banners-before-footer',
			'before_widget' => '<aside id="%1$s" class="widget %2$s help-inline">',
			'after_widget' => "</aside>",
			//'before_title' => '<h3 class="widget-title">',
			//'after_title' => '</h3>',
		) );*/
	}
	add_action( 'widgets_init', 'kcsite_widgets_init' );

}
} // theme_setup

function kcsite_fill_static_vars_with_dynamic_data(){
	global $staticVars;
	$ordinaryNewType = get_term_by('id', $staticVars['ordinaryNewTypeID'], 'new-type');
	$industryNewType = get_term_by('id', $staticVars['industryNewTypeID'], 'new-type');
	$staticVars['ordinaryNewTypeSlug'] = $ordinaryNewType->slug;
	$staticVars['industryNewTypeSlug'] = $industryNewType->slug;
}
add_action( 'wp_loaded', 'kcsite_fill_static_vars_with_dynamic_data' );


//========================================================================================================
//========================================================================================================
// UTILITY FUNCTIONS
//========================================================================================================
//========================================================================================================

/**
 * get page by template file name
 * @param $filename Full file name with extension
 * @param $path optional  path to template file
 * @param $field optional  if passed, will return not all page info in array, but that field value
 */
function kcsite_getPageByTempate($filename, $field = null, $path = 'page-templates/'){
	// page-templates/education_login.php

	$pages = get_pages(array(
	    'meta_key' => '_wp_page_template',
	    'meta_value' => $path . $filename,
	    'hierarchical' => 0
	));
	if($field != null){
		// print_r($pages[0]);exit;
		// print_r($pages[0]->$field);exit;
		return $pages[0]->$field;
	} else {
		return $pages[0];
	}
	// print_r($pages);exit;

}


function kcsite_sitetree(){

	echo '<div class="kcsite-sitetree">';

	//Pages
/*	$args = array(
		'title_li' => '',
		'post_type' => 'page'
	);	
	
	echo '<h2 class="sitetree-section-title">'.pll__('Puslapiai').'</h2>';
	echo '<ul>';
	wp_list_pages($args);
	echo '</ul>';*/

	wp_nav_menu(array('theme_location' => 'primary', 'menu_class' => 'tree-nav', 'container' => false, 'fallback_cb' => 'fallback_nav_menu', 'echo' => true));	
	wp_nav_menu(array('theme_location' => 'secondary', 'menu_class' => 'tree-nav', 'container' => false, 'fallback_cb' => 'fallback_nav_menu', 'echo' => true));

	//CPT Interesting Facts

	//Categories
/*	echo '<ul>';
	wp_list_categories();
	echo '</ul>';*/

	//Archive Pages

	//News

	echo '</div>';
}

function kcsite_get_post_thumbnail($thumbnail_size = 'thumbnail', $css_class = 'post-thumbnail', $title = true, $show_first_if_no_post_thumbnail = false){
	global $post;

	$post_thumbnail = false;
	if($title) $title = get_the_title();

	if (has_post_thumbnail()) {
		$post_thumbnail = get_the_post_thumbnail(get_the_ID(), $thumbnail_size, array('class' => $css_class, 'title' => $title));
	} else {
		if($show_first_if_no_post_thumbnail){
		    $attachments = get_posts(array('numberposts' => '1', 'post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order', 'order' => 'ASC'));		   
		    if(sizeof($attachments) > 0){
		    	$post_thumbnail = wp_get_attachment_image($attachments[0]->ID, $thumbnail_size, false, array('class' => $css_class, 'title' => get_the_title()));
		    }			
		}
	}

	return $post_thumbnail;
}

function kcsite_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'twentyeleven' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'twentyeleven' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment clearfix">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php
						$avatar_size = 68;
						if ( '0' != $comment->comment_parent )
							$avatar_size = 39;?>

						<div class="comment-avatar">
							<?php echo get_avatar( $comment, $avatar_size ); ?>
						</div>

						<div class="comment-author-link">
						<?php echo get_comment_author_link(); ?>
						</div>

						<div class="comment-date">
							<?php echo get_comment_time('Y.m.d');?>
						</div>
						<div class="comment-time-date">
							<?php echo get_comment_time('H:i:s');?>
						</div>

						<?php
					?>

					<?php edit_comment_link( __( 'Edit', 'kcsite' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .comment-author .vcard -->

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'kcsite' ); ?></em>
					<br />
				<?php endif; ?>

			</footer>

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'kcsite' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}




function kcsite_auto_excerpt_more($more) {
	return '... ';
}
add_filter('excerpt_more', 'kcsite_auto_excerpt_more');

function kcsite_excerpt_read_more_link($output) {
	global $post;
 	$output = str_replace('</p>', ' <a class="excerpt-readmore" href="'. esc_url( get_permalink() ).'">'. pll__('Skaityti plačiau').'</a></p>', $output );
	return $output;
}
add_filter('the_excerpt', 'kcsite_excerpt_read_more_link');

//http://wordpress.org/support/topic/two-different-excerpt-lengths
//http://pastebin.com/aUZuQrZy
function kcsite_excerpt($excerpt_length = 55, $id = false, $echo = true) {

	$text = '';

	if($id) {
		$the_post = & get_post($my_id = $id);
		$text = ($the_post->post_excerpt) ? $the_post->post_excerpt : $the_post->post_content;
	} else {
		global $post;
		$text = ($post->post_excerpt) ? $post->post_excerpt : get_the_content('');
	}

	$text = strip_shortcodes( $text );
	$text = apply_filters('the_content', $text);
	$text = str_replace(']]>', ']]&gt;', $text);
	$text = strip_tags($text);
	
	$excerpt_more = '... <a class="excerpt-readmore" href="'. esc_url(get_permalink()).'">'. pll__('Skaityti plačiau').'</a>';
	$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
	if (count($words) > $excerpt_length) {
		array_pop($words);
		$text = implode(' ', $words);
		$text = $text . $excerpt_more;
	} else {
		$text = implode(' ', $words);
	}
	if($echo)
		echo apply_filters('the_content', $text);
	else
		return $text;
}

function kcsite_get_excerpt($excerpt_length = 55, $id = false, $echo = false) {
	return my_excerpt($excerpt_length, $id, $echo);
}

// adds 'last' class only to parent level last menu item
function nav_menu_add_classes($items, $args) {
    //Add first item class
    $items[1]->classes[] = 'first';

    //Add last item class
    $i = count($items);
    while($items[$i]->menu_item_parent != 0 && $i > 0) {
        $i--;
    }
    $items[$i]->classes[] = 'last';

    return $items;
}
add_filter('wp_nav_menu_objects', 'nav_menu_add_classes', 10, 2);

/*function menu_set_has_children( $sorted_menu_items, $args ) {
    $last_top = 0;
    foreach ( $sorted_menu_items as $key => $obj ) {
        // it is a top lv item?
        if ( 0 == $obj->menu_item_parent ) {
            // set the key of the parent
            $last_top = $key;
        } else {
            $sorted_menu_items[$last_top]->classes['dropdown'] = 'dropdown';
        }
    }
    return $sorted_menu_items;
}
add_filter( 'wp_nav_menu_objects', 'menu_set_has_children', 10, 2 );
*/


//http://www.kriesi.at/archives/how-to-build-a-wordpress-post-pagination-without-plugin
// $range - how many links before and after will be displayed
function kcsite_nice_pagination($pages = '', $range = 2) {
     $showitems = ($range * 2)+1; 
       
     global $paged; if(empty($paged)) $paged = 1; 

     if($pages == '') {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages) {
             $pages = 1;
         }
     }    
     if(1 != $pages) {
         echo "<div class=\"pagination-wrap clearfix\"><nav class=\"pagination clearfix\">";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."' class=\"dots-left\">...</a>";
         //if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."' class=\"dots-left\">...</a>";
 
         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
             }
         }
 
         //if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\" class=\"dots-right\">...</a>";
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."' class=\"dots-left\">...</a>";
         echo "</nav></div>\n";
     }
}

//Displays navigation to next/previous pages when applicable.
function kcsite_content_nav( $nav_id ) {
	global $wp_query;

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo $nav_id; ?>" class="navigation" role="navigation">
			<h3 class="assistive-text"><?php _e( 'Post navigation', 'twentytwelve' ); ?></h3>
			<div class="nav-previous alignleft"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twentytwelve' ) ); ?></div>
			<div class="nav-next alignright"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twentytwelve' ) ); ?></div>
		</nav><!-- #<?php echo $nav_id; ?> .navigation -->
	<?php endif;
}

function kcsite_posted_on() {
/*	printf( __( '<span class="sep">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a><span class="by-author"> <span class="sep"> by </span> <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'twentyeleven' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'twentyeleven' ), get_the_author() ) ),
		get_the_author()
	);*/
//check if tade is not overriden manually
	global $kcsite_post_metabox;
	$kcsite_post_metabox->the_meta();
	if($kcsite_post_metabox->get_the_value('kcsite_date_override') != '') {
		$kcsite_post_metabox->the_value('kcsite_date_override');
	} else {
		if(pll_current_language('slug') == 'en'){
			echo get_the_date('F') . ' '. get_the_date('d') .', '. get_the_date('Y');
		} else {
			echo get_the_date('Y') . ' m. ' . get_the_date('F') .' '. get_the_date('d') . ' d.';
		}
	}
}


//remove jumping to the spot where the <--more--> tag
function remove_more_jump_link($link) { 
	$offset = strpos($link, '#more-');
	if ($offset) {
		$end = strpos($link, '"',$offset);
	}
	if ($end) {
		$link = substr_replace($link, '', $offset, $end-$offset);
	}
	return $link;
}
add_filter('the_content_more_link', 'remove_more_jump_link');


//options framework
/*
 * Helper function to return the theme option value. If no value has been saved, it returns $default.
 * Needed because options are saved as serialized strings.
 *
 * This code allows the theme to work without errors if the Options Framework plugin has been disabled.
 */
if (!function_exists('of_get_option')) {
	function of_get_option($name, $default = false) {
	    $optionsframework_settings = get_option('optionsframework');
	    // Gets the unique option id
	    $option_name = $optionsframework_settings['id'];
	    if ( get_option($option_name) ) {
	        $options = get_option($option_name);
	    }
	    if ( isset($options[$name]) ) {
	        return $options[$name];
	    } else {
	        return $default;
	    }
	}
}

/*
_Plugin Name: Really Simple Breadcrumb
_Plugin URI: http://www.bcm-websolutions.de
_Description: This is a really simple WP Plugin which lets you use Breadcrumbs for Pages!
_Version: 1.0
*/
function simple_breadcrumb() {
    global $post;
    global $staticVars;
    // print_r(has_term($staticVars['industryNewTypeID'], 'new-type', $post));exit;
    $sep = ' / ';
	echo '<a href="'.pll_home_url().'">'.pll__('Lietuvos kino centras').'</a>'.$sep;
	//echo '<a href="'.pll_home_url().'">'.get_bloginfo('name').'</a>'.$sep;

/*	if ( is_category() || is_single() ) {
		the_category(', ');
		if ( is_single() ) {
			echo $sep;
			the_title();
		}*/

	if (is_single()) {
		if(get_post_type() == 'post'){
			if(has_term($staticVars['industryNewTypeID'], 'new-type', $post)){
				$news_link = $staticVars['industryNewsUrl'];
				$news_link_text = pll__('Industrijos naujienos');
			} else {
				$news_link = $staticVars['newsUrl'];
				$news_link_text = pll__('Naujienos');
			}
			echo '<a href="'.$news_link.'">';
			echo $news_link_text;
			echo "</a>";
		} elseif(get_post_type() == 'interesting-fact') {

			$pages = get_pages(array(
			    'meta_key' => '_wp_page_template',
			    'meta_value' => 'page-templates/interesting_facts.php',
			    'lang' => pll_current_language('slug')
			    //'hierarchical' => 0
			));
			//print_r($pages);

			echo '<a href="'.get_permalink($pages[0]->ID).'">';
			pll_e('Įdomūs faktai');
			echo "</a>";
		} elseif(get_post_type() == 'tribe_events'){
			$url = $staticVars['eventsUrl'];
			echo '<a href="'.$url.'">'.pll__('Renginiai').'</a>';
		} elseif(get_post_type() == 'education-resource'){
			$url = $staticVars['educationResourceUrl'];
			$title = get_the_title(kcsite_getPageByTempate('education_list.php'));
			echo '<a href="'.$url.'">'.$title.'</a>';
		} elseif(get_post_type() == 'film'){
            $url = get_permalink($staticVars['filmRegisterSearchPageId']);
            $title = get_the_title($staticVars['filmRegisterSearchPageId']);
            echo '<a href="'.$url.'">'.$title.'</a>';
        } elseif(get_post_type() == 'lithfilm'){
			$url = get_permalink($staticVars['lithuanianFilmsSearchPageId']);
			$title = get_the_title($staticVars['lithuanianFilmsSearchPageId']);
			echo '<a href="'.$url.'">'.$title.'</a>';
		}
		echo $sep;
		the_title();

	} elseif(!is_single() && get_post_type() == 'tribe_events'){ //event calendar view
		pll_e('Renginiai');
	} elseif ( is_page() && $post->post_parent ) {
		
		//checkiing home page is not needed, but dos make no difference, so left
		$home = get_page_by_title('home');
		//show all ancestors (parent pages)
		for ($i = count($post->ancestors)-1; $i >= 0; $i--) {
			if (($home->ID) != ($post->ancestors[$i])) {
				echo '<a href="';
				echo get_permalink($post->ancestors[$i]); 
				echo '">';
				echo get_the_title($post->ancestors[$i]);
				echo "</a>".$sep;
			}
		}
		echo the_title();
	} elseif (is_page()) {
		echo the_title();
	} elseif (is_404()) {
		echo of_get_option_by_lang('kcsite_404_page_title');
	}
}

function kcsite_referer_btn(){
	$referer = wp_get_referer();
	if($referer) { ?>
	<a href="<?php echo $referer; ?>" class="btn-block btn-referer"><?php pll_e('Atgal');?></a>
	<?php }	
}

/**
 * If we have static url without locale, make it have corresponding to the language locale in the url
 * ex. localhost/lkc/renginiai => localhost/lkc/en/renginiai
 */
function kcsite_multilingualizeUrl($url){
	
	//$url = 'http://www.lkc.srv7.321.lt/renginiai/2013-02/';

	$locale = kcsite_get_lang_slug();
	if($locale == '') return $url;

	$siteUrl = get_bloginfo('siteurl');
	//$siteUrl = 'http://www.lkc.srv7.321.lt';
	$secondPart = substr($url, (strlen($siteUrl)-3)); //bloginfo return url with locale already
/*	echo $siteUrl;
	echo '---';
	echo $secondPart;
	echo '---';
	echo $url; exit;*/
	//return $siteUrl . '/' . kcsite_get_lang_slug() . $secondPart;
	return $siteUrl . $secondPart;
	//return $siteUrl;
}
//kcsite_multilingualizeUrl(tribe_get_previous_month_link());

// for emails to set html type
function set_html_content_type(){
	return 'text/html';
}

//========================================================================================================
//========================================================================================================
// CUSTOMIZE DASHBOARD
//========================================================================================================
//========================================================================================================

// Disable the Admin Bar
add_filter( 'show_admin_bar', '__return_false' );

//add custom admin stylesheet
function kcsite_add_admin_stylesheet() {
    echo '<link rel="stylesheet" type="text/css" href="'.get_template_directory_uri().'/css/admin/admin-custom.css?2">';
}
add_action('admin_head', 'kcsite_add_admin_stylesheet');
add_action('login_head', 'kcsite_add_admin_stylesheet');

// minimize dashboard for Semi admins
if(current_user_can('semi_admin')) {	

	// add custom admin stylesheet for semi admins only
	function kcsite_add_semi_admin_stylesheet() {
	    echo '<link rel="stylesheet" type="text/css" media="all" href="'.get_bloginfo('stylesheet_directory').'/css/admin/admin-custom-semi-admin.css?4" />';
	}
	add_action('admin_head', 'kcsite_add_semi_admin_stylesheet');

	//http://wp.tutsplus.com/tutorials/creative-coding/customizing-your-wordpress-admin/
	function remove_menus() {
		remove_menu_page('edit-tags.php?taxonomy=category');
		remove_menu_page('link-manager.php');
		remove_menu_page('edit-comments.php');
		remove_menu_page('plugins.php');
		remove_menu_page('tools.php');
		remove_menu_page('users.php');
		remove_menu_page('separator1');
		remove_menu_page('separator2');
		remove_menu_page('separator-last');
		remove_menu_page('wpcf7');
		//remove_menu_page('themes.php');
			//remove_submenu_page('index.php', 'update-core.php');
			remove_submenu_page('themes.php', 'themes.php');
			remove_submenu_page('themes.php', 'theme-editor.php');
			remove_submenu_page('themes.php', 'widgets.php');

		remove_submenu_page('options-general.php', 'options-writing.php');
		remove_submenu_page('options-general.php', 'options-reading.php');
		remove_submenu_page('options-general.php', 'options-media.php');
		remove_submenu_page('options-general.php', 'options-permalink.php');
		remove_submenu_page('options-general.php', 'options-discussion.php');
		remove_submenu_page('options-general.php', 'velvet-blues-update-urls.php');
		//part of menus hidden in admin-custom-semi-admin.css
	}	
	add_action('admin_menu', 'remove_menus' );
		

		
	// remove dashboard footer
	//function modify_footer_admin () {}
	add_filter('admin_footer_text', create_function('$a', "return null;"));
		
	// remove update notice
	add_filter('pre_site_transient_update_core', create_function('$a', "return null;"));
			
	// Remove pesky dashboard meta boxes 
	function kcsite_remove_dashboard_widgets() {
		// remove_meta_box('yoast_db_widget', 'dashboard', 'normal'); // Breadcrumbs
		remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');  // incoming links
		//remove_meta_box('dashboard_plugins', 'dashboard', 'normal');   // plugins
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');   // plugins
		//remove_meta_box('dashboard_quick_press', 'dashboard', 'normal');  // quick press
	 	remove_meta_box('dashboard_recent_drafts', 'dashboard', 'normal');  // recent drafts
	 	remove_meta_box('dashboard_primary', 'dashboard', 'normal');   // wordpress blog
	 	remove_meta_box('dashboard_secondary', 'dashboard', 'normal');   // other wordpress news
	 	remove_meta_box('alo-easymail-widget', 'dashboard', 'normal');   // other wordpress news
	 	remove_meta_box('tribe_dashboard_widget', 'dashboard', 'normal');   // other wordpress news
	}	
	add_action('admin_init', 'kcsite_remove_dashboard_widgets');

	function kcsite_remove_browse_happy() {
	   global $wp_meta_boxes;
	   unset($wp_meta_boxes['dashboard']['normal']['high']['dashboard_browser_nag']);
	}
	add_action('wp_dashboard_setup', 'kcsite_remove_browse_happy');

	//reorder admin menus for semi admin
	function kcsite_custom_menu_order($menu_ord) {
	   if (!$menu_ord) return true;
	   return array('index.php', 'edit.php', 'edit.php?post_type=tribe_events', 'edit.php?post_type=page', 'edit.php?post_type=interesting-fact', 'edit.php?post_type=education-resource', 'edit.php?post_type=film', 'edit.php?post_type=lithfilm', 'edit.php?post_type=home-slideshow', 'edit.php?post_type=bubble', 'wp-polls/polls-manager.php', 'wpsqt-menu', 'edit.php?post_type=newsletter', /*'wpcf7',*/ 'upload.php', 'themes.php', 'options-general.php');
	}
	add_filter('custom_menu_order', 'kcsite_custom_menu_order');
	add_filter('menu_order', 'kcsite_custom_menu_order');

}


//========================================================================================================
//========================================================================================================
// CUSTOM POST TYPES
//========================================================================================================
//========================================================================================================


function cpt_slideshow_init() {

	$labels = array(
		'name' => _x(__('Home Slideshow', 'kcsite'), 'post type general name'),
		'singular_name' => _x('Products', 'post type singular name'),
		//'add_new' => _x('Add New', 'Slide'),
		//'add_new_item' => __('Add New Slide', 'kcsite'),
		//'edit_item' => __('Edit Slide', 'kcsite'),
		//'new_item' => __('New Slide', 'kcsite'),
		//'view_item' => __('View Slide', 'kcsite', 'kcsite'),
		//'search_items' => __('Search Slides', 'kcsite'),
		'not_found' =>  __('No Items found'),
		'not_found_in_trash' => __('No Items found in Trash'),
		'parent_item_colon' => ''
	);
	 $args = array(
		'labels' => $labels,
		'public' => false,
		'exclude_from_search' =>true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		//'menu_position' => 5,
		//'menu_icon' => '',
		'hierarchical' => true,
		'supports' => array('thumbnail','title', 'editor', 'custom-fields', 'excerpt'),
		// 'rewrite' => true,		
		// 'query_var' => true,
	);
	register_post_type('home-slideshow', $args);
	
	register_taxonomy(
		'home-slideshow-category', 
		'home-slideshow',
		array(
			'hierarchical' => true, 
			'label' =>  __('Categories'), 
			'singular_name' => __('Category'),
			'query_var' => 'home-slideshow-category'
		)
	);

	$labels = array(
		'name' => _x(__('Interesting facts', 'kcsite'), 'post type general name'),
		'singular_name' => _x('Interesting fact', 'post type singular name'),
	);
	 $args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' => false,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		'hierarchical' => false,
		//'has_archive' => true,
		'supports' => array('thumbnail','title', 'editor', 'custom-fields', 'excerpt'),
		'rewrite' => array('slug'=>'idomus-faktai/faktas','with_front'=> false),
	);
	register_post_type('interesting-fact', $args);

	$labels = array(
		'name' => _x(__('Burbulai', 'kcsite'), 'post type general name'),
		'singular_name' => _x('Burbulai', 'post type singular name'),
		'not_found' =>  __('No Items found'),
		'not_found_in_trash' => __('No Items found in Trash'),
		'parent_item_colon' => ''
	);
	 $args = array(
		'labels' => $labels,
		'public' => false,
		'exclude_from_search' =>true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'show_in_nav_menus' => false,
		//'menu_position' => 5,
		//'menu_icon' => '',
		'hierarchical' => true,
		'supports' => array('title', 'editor', 'page-attributes', 'custom-fields',),  //page-attributes for reordering to work, but not needed here
		// 'supports' => array('title', 'editor'),
		// 'rewrite' => true,		
		// 'query_var' => true,
	);
	register_post_type('bubble', $args);

    register_taxonomy(
      'new-type', 
      'post',
      array(
        'label' =>  'Naujienos tipas', 
        'labels' => array(
          'name' => 'Naujienos tipas',
          'singular_name' => 'Naujienos tipas',
          'menu_name' => 'Naujienos tipas',
        ),
        'hierarchical' => true, 
        // 'query_var' => 'film-type'
      )
    );


}
add_action('init', 'cpt_slideshow_init');

//========================================================================================================
//========================================================================================================
// CHANGE WORDPRESS BEHAVIOR
//========================================================================================================
//========================================================================================================

// load scripts
function load_theme_scripts() {  
	if (!is_admin()) {  
		/*wp_deregister_script('jquery');  
		wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js');  
		wp_enqueue_script('jquery');  */
		//wp_enqueue_script('fancybox', get_template_directory_uri() . '/js/jquery.fancybox.min.js', array('jquery'));        
		// wp_enqueue_script('mousewheel', get_template_directory_uri() . '/js/jquery.mousewheel.min.js', array('jquery'));        
		//wp_enqueue_script('respond', get_template_directory_uri() . '/js/respond.min.js', array('jquery') );
		//wp_enqueue_script('cycle', get_template_directory_uri() . '/js/jquery.cycle.js', array('jquery') );
		
		//wp_enqueue_script('colorbox', get_template_directory_uri() . '/js/jquery.colorbox.js', array('jquery'));        
		//wp_enqueue_script('anythingslider', get_template_directory_uri() . '/js/jquery.anythingslider.min.js', array('jquery'));
		//wp_enqueue_script('superfish', get_template_directory_uri() . '/js/jquery.superfish.js', array('jquery'));
		//wp_enqueue_script('hoverIntent', get_template_directory_uri() . '/js/jquery.hoverIntent.min.js', array('jquery'));
		//wp_enqueue_script('supersubs', get_template_directory_uri() . '/js/jquery.supersubs.min.js', array('jquery'));
		//wp_enqueue_script('selectivizr', get_template_directory_uri() . '/js/selectivizr.min.js', array('jquery'));
		//wp_enqueue_script('hotkeys', get_template_directory_uri() . '/js/jquery.hotkeys2.js', array('jquery'));
        // wp_enqueue_script('jquery-ui', get_template_directory_uri(). '/js/jquery-ui.custom/js/jquery-ui.custom.min.js',array('jquery'), '1.1', false);
        wp_enqueue_script('plugins', get_template_directory_uri() . '/js/plugins.js', array('jquery'), 6 );
		wp_enqueue_script('app', get_template_directory_uri() . '/js/app.js', array('jquery'), 6 );
	
		global $staticVars;
		wp_localize_script('app', 'jsVars',  array( 
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'keysListUrl' => $keysListUrl,
			'siteUrl' => $staticVars['siteUrl'],
			'sitetreeUrl' => $staticVars['sitetreeUrl'],
			'eventsUrl' => $staticVars['eventsUrl'],
			'newsUrl' => $staticVars['newsUrl'],
			'keysListUrl' => $staticVars['keysListUrl'],
			'validateMessageRequired' => pll__("Privalomas laukelis"),
			'validateMessageEmail' => pll__("Neteisingas el. pašto formatas"),
			'validateMessageNumber' => pll__("Prašome įvesti skaičių"),
		));		
	}  
}  
add_action('init', 'load_theme_scripts'); 


function kcsite_hs_columns_in_wp_list_table(){

	function hs_custom_post_column_th( $column ) {
	    $column['home-slideshow-category'] = __('Category');
	    //print_r($column);exit;
	    return $column;
	}

	function hs_custom_post_column_td($column_name, $post_id) {
	        $taxonomy = 'home-slideshow-category';
	        $post_type = get_post_type($post_id);
	        $terms = get_the_terms($post_id, $taxonomy);

	        if (!empty($terms) ) {
	            foreach ( $terms as $term )
	            $post_terms[] ="<a class='cpt-category-column-content' href='edit.php?post_type={$post_type}&{$taxonomy}={$term->slug}'> " .esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'edit')) . "</a>";
	            echo join('', $post_terms );
	        }
	         //else echo '<i>No Location Set. </i>';
	}

	function hs_sortable_columns($columns) {
		$columns['home-slideshow-category'] = __('Category');
		return $columns;
	}

    add_filter( 'manage_edit-home-slideshow_columns', 'hs_custom_post_column_th');
    add_action( 'manage_home-slideshow_posts_custom_column', 'hs_custom_post_column_td', 10, 2);
    add_filter( 'manage_edit-home-slideshow_sortable_columns', 'hs_sortable_columns' );
}
add_action('admin_init', 'kcsite_hs_columns_in_wp_list_table');



// START events calendar: adding filter by event start date

function kcsite_add_events_date_filter() {

	global $wpdb, $wp_locale;

	$months = $wpdb->get_results( $wpdb->prepare( "
		SELECT DISTINCT YEAR( meta_value ) AS year, MONTH( meta_value ) AS month
		FROM $wpdb->postmeta
		WHERE meta_key = %s
		ORDER BY meta_value ASC
	", '_EventStartDate' ) );

	$month_count = count( $months );

	if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
		return;

	$m = isset( $_GET['_EventStartDate'] ) ? (int) $_GET['_EventStartDate'] : 0;
	?>

	<select id="EventStartDate-filter" name='_EventStartDate'>
		<option<?php selected( $m, 0 ); ?> value='0'><?php _e( 'Pasirinkite renginio datą' ); ?></option>
	<?php
	foreach ( $months as $arc_row ) {
		if ( 0 == $arc_row->year )
			continue;

		$month = zeroise( $arc_row->month, 2 );
		$year = $arc_row->year;

		printf( "<option %s value='%s'>%s</option>\n",
			selected( $m, $year . $month, false ),
			esc_attr( $arc_row->year . $month ),
			/* translators: 1: month name, 2: 4-digit year */
			sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
		);
	}
	?>
	</select>
	<?php
}

function kcsite_events_date_request($request) {
    if( isset($_GET['_EventStartDate']) && !empty($_GET['_EventStartDate']) ) {
        $request['meta_key'] = '_EventStartDate';
        $year = substr($_GET['_EventStartDate'], 0, 4);
        $month = substr($_GET['_EventStartDate'], 4, 2);
        $meta_value = $year . '-' . $month;
        $request['meta_value'] = $meta_value;
        $request['meta_compare'] = 'like';
    }
    return $request;
}

//another way
function meta_filter_posts( $query ) {
	if( isset($_GET['_EventStartDate']) && !empty($_GET['_EventStartDate']) ) {
		// $query is the WP_Query object, set is simply a method of the WP_Query class that sets a query var parameter
		$query->set( 'meta_key', '_EventStartDate' );
		$query->set( 'meta_value', '2013-02-10 08:00:00' );
	}
	return $query;
}

if( is_admin() && isset($_GET['post_type']) && $_GET['post_type'] == 'tribe_events' ) {
    add_filter('request', 'kcsite_events_date_request');
    add_filter('restrict_manage_posts', 'kcsite_add_events_date_filter');
	//add_filter( 'pre_get_posts', 'meta_filter_posts' );
}
//EOF

/*function tribe_make_admin_countries_list_multilingual(){
	global $polylang;
	global $post;
	$lang = $polylang->get_post_language($post->ID)->slug;
	// $lang = get_the_terms($post->ID, 'language');
	// $lang = $lang[0]->slug;
	// echo $lang;exit;

	if($lang != 'lt' && $post->post_type == 'tribe_events' && $_GET['action'] == 'edit'){
		// echo 'labas'; exit;
		add_filter('locale','change_locale_to_en', 10); 
	}
}
// add_action('admin_init', 'tribe_make_admin_countries_list_multilingual');
add_action('admin_head', 'tribe_make_admin_countries_list_multilingual');

function change_locale_to_en($locale) {
    $locale = 'en_US';
    // echo 5; exit;
    return $locale;
}*/




//remove lt letters during upload process as upload gets broken other way (if lt letters exists in filename)
function kcsite_remove_lt_leters($filename) {
    $letters = array('ą','č','ę','ė','į','š','ų','ū','ž','Ą','Č','Ę','Ė','Į','Š','Ų','Ū','Ž');
    $replace = array('a','c','e','e','i','s','u','u','z','A','C','E','E','I','S','U','U','Z');
    $filename = str_replace($letters, $replace, $filename);
    return $filename;
}
add_filter('sanitize_file_name', 'kcsite_remove_lt_leters', 10);

function modify_post_mime_types($post_mime_types){  
    $post_mime_types['application/pdf'] = array( __( 'PDF' ), __( 'Tvarkyti PDF' ), _n_noop( 'PDF <span class="count">(%s)</span>', 'PDF <span class="count">(%s)</span>' ) );  
    return $post_mime_types;  
}   
add_filter( 'post_mime_types', 'modify_post_mime_types' ); 

//tinymce
add_filter('tiny_mce_before_init', 'kcsite_add_custom_classes');
function kcsite_add_custom_classes($arr_options) {
	$arr_options['theme_advanced_styles'] = "be rėmelio=no-border;";
	//$arr_options['theme_advanced_buttons2_add_before'] = "styleselect";
	return $arr_options;
}




//========================================================================================================
//========================================================================================================
// MULTILINGUAL
//========================================================================================================
//========================================================================================================

function of_get_option_by_lang($option){
	if(pll_current_language('slug') != 'lt'){
		$option = $option.'_'.pll_current_language('slug');
	}
	return of_get_option($option);
}

function kcsite_lang_body_class($classes) {
	$classes[] = 'lang-'.pll_current_language('slug');
	if(isset($_SESSION['disabled_version']) && $_SESSION['disabled_version'] == 1) {
		$classes[] = 'disabled-version';
	}
	return $classes;
}
add_filter('body_class', 'kcsite_lang_body_class', 10, 2);

/**
 * Function to get current language slug
 * Return nothing if language is lithuanian, slug if other language
 * @ can be added preceding slash
 * @ can be passed current language
 */
function kcsite_get_lang_slug($preceding_slash = false, $lang = false){
	$slash1 = $preceding_slash == true ? '/' : '';
	//$lith_output = '';
	if($lang) {
		$lang_slug = $lang;
	} else {
		$lang_slug = pll_current_language('slug');
	}
	return $lang_slug == 'lt' ? '' : $slash1.$lang_slug;
}

/**
 * Function to get url to 'The Events Calendar' calendar view 
 */
/*function get_events_calendar_url($lang = false){
	$calendar_options = get_option('tribe_events_calendar_options');
	$slug = $calendar_options['eventsSlug'];
	if($lang){
		$lang = kcsite_get_lang_slug(true, $lang);
	}
	//$lang = pll_current_language('slug') == 'lt' ? '' : '/'.pll_current_language('slug');
	$url = get_bloginfo('siteurl').$lang.'/'.$slug;
	return $url;
}*/

function kcsite_change_lang_switcher_output($output, $args) {
	if ($args['dropdown']) return $output;
		$output = str_replace(array('<li>', '<li class="lang-item lang-item-12">', '</li>'), '', $output);
		$output = str_replace('<a', '<a title="'.pll__('Pakeisti kalbą').'" class="mini-nav-en"', $output); //**  $wpdb->  *


/*		if(pll_current_language('slug') == 'en'){
			//url to en events calendar is like lithuanian. So make it like en
			$url_lt = get_events_calendar_url('lt');
			$url_en = get_events_calendar_url('en');
			echo $url_lt;
			echo $url_en;
			$output = str_replace($url_lt, $url_en, $output);
		}*/
	return $output;
}
add_filter('pll_the_languages', 'kcsite_change_lang_switcher_output', 10, 2);

pll_register_string('LKC', 'Pakeisti kalbą');
pll_register_string('LKC', 'Neįgaliesiems');
pll_register_string('LKC', 'Versija neįgaliesiems');
pll_register_string('LKC', 'Įprasta versija');
pll_register_string('LKC', 'Lietuvos kino centro Facebook profilis');
pll_register_string('LKC', 'El. paštas');
pll_register_string('LKC', 'Svetainės medis');
pll_register_string('LKC', 'Įveskite žodį ar frazę');
pll_register_string('LKC', 'Rasti');
pll_register_string('LKC', 'Skaityti plačiau');
pll_register_string('LKC', 'Skaityti daugiau');
pll_register_string('LKC', 'Atgal');
pll_register_string('LKC', 'Lietuvos kino centras');
pll_register_string('LKC', 'Renginiai');
pll_register_string('LKC', 'Artimiausi renginiai');
pll_register_string('LKC', 'Artėjančių renginių šiuo metu nėra');
pll_register_string('LKC', 'Visas kalendorius');
pll_register_string('LKC', 'Grįžti į renginių kalendorių');
pll_register_string('LKC', 'Paieškos rezultatai raktažodžiams');
pll_register_string('LKC', 'Pagal jūsų paieškos kriterijus nieko nerasta');
pll_register_string('LKC', 'Naujienos');
pll_register_string('LKC', 'Puslapiai');
pll_register_string('LKC', 'Įdomūs faktai');
pll_register_string('LKC', 'Titulinis puslapis');
pll_register_string('LKC', 'Naujienų archyvas');
pll_register_string('LKC', 'Industrijos naujienų archyvas');
pll_register_string('LKC', 'Apklausa');

pll_register_string('LKC', 'Prašome įvesti skaičių');






//========================================================================================================
//========================================================================================================
// MISC
//========================================================================================================
//========================================================================================================

//disabled version
function kcsite_session_start() {
	if (!session_id()){
		session_start();
	}
}
add_action('init', 'kcsite_session_start');

function kcsite_toggle_disabled_version() {

	if(isset($_GET['disabled_version']) && $_GET['disabled_version'] == 1) {
		$_SESSION['disabled_version'] = 1;
	} elseif (isset($_GET['disabled_version']) && $_GET['disabled_version'] == 0) {
		unset($_SESSION['disabled_version']);
		//$_SESSION['disabled_version'] = 0;
	}
}
add_action('init', 'kcsite_toggle_disabled_version');


// alo easy newsletter
/*
function custom_easymail_new_subscriber_is_added($subscriber, $user_id=false){

	//polylang is fired later than ALO hooks. Made lang detection via cookie
	// $lang = pll_current_language('slug');  
	//$lang = $_COOKIE['wordpress_polylang'];

	$lang = pll_current_language('slug');
	if(empty($lang)) $lang = $_COOKIE['wordpress_polylang'];
	if(empty($lang)) $lang = 'lt';

	global $wpdb;
    $wpdb->update($wpdb->prefix.'easymail_subscribers', array('lang' => $lang), array('ID' => $subscriber->ID));
}
//add_action('alo_easymail_new_subscriber_added',  'custom_easymail_new_subscriber_is_added', 10, 2 );
*/


function kcsite_alo_easymail_get_language(){
	
	//return pll_current_language('slug');     //doesn't always work
	//return substr(get_locale(), 0, 2);     //doesn't always work

	$lang = pll_current_language('slug');
	if(empty($lang)) $lang = $_COOKIE['wordpress_polylang'];
	if(empty($lang)) $lang = 'lt';
	
	return $lang;
}
add_filter ( 'alo_easymail_multilang_get_language', 'kcsite_alo_easymail_get_language' );

/* poll */

function kcsite_sidebar_poll(){

	if (function_exists('get_poll')){
		$poll = get_poll(null, false);
		if($poll != 'Šiuo metų aktyvių apklausų nėra' && $poll != '' && pll_current_language('slug') == 'lt'){ //default template text if no polls ?>	

		<aside class="widget wp-polls-embeded-widget">
			<header>
				<h3 class="widget-title"><?php echo pll_e('Apklausa');?></h3>
			</header>
			<div class="widget-content">
				<?php echo $poll; ?>
			</div>
		</aside>

		<hr class="double-hr" />

		<?php }
	}
}

/* quiz */

function kcsite_compare_quiz_results($quiz_id){

	global $wpdb;

	$results = $wpdb->get_results(
		$wpdb->prepare( "SELECT * 
             FROM `".WPSQT_TABLE_RESULTS."` 
             WHERE item_id = %d
             ORDER BY id DESC" 
			, $quiz_id
		), ARRAY_A	
	);
	// print_r($results);exit;

	if(count($results) > 1){

		$currentResult = $results[0];
		unset($results[0]);

		$sum = '';
		foreach($results as $val){
			$sum = $sum + $val['percentage'];
		}
		$average = round($sum / count($results), 2);

		ob_start();
		?>
		    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
		    <script type="text/javascript">

		      // Load the Visualization API and the piechart package.
		      google.load('visualization', '1.0', {'packages':['corechart']});

		      // Set a callback to run when the Google Visualization API is loaded.
		      google.setOnLoadCallback(drawChart);

		      // Callback that creates and populates a data table,
		      // instantiates the pie chart, passes in the data and
		      // draws it.
		      function drawChart() {

		        // Create the data table.
		        var data = new google.visualization.DataTable();
		        data.addColumn('string', 'Topping');
		        data.addColumn('number', 'Balas, %');
		        data.addRows([
		          ['Jūsų rezultatas', <?php echo $currentResult['percentage'];?>],
		          ['Vidutinis kitų viktorinos dalyvių rezultatas', <?php echo $average;?>]
		        ]);

		        // Set chart options
		        var options = {

		        	title: 'Rezultatų palyginimas',
		            width: 500,
		            height: 300,
		            isStacked: true,
		            legend: {
		            	position: 'none'
		            },
		            bar: {groupWidth: 50},
		            vAxis: {maxValue: 100}
		            //colors:['red','#004411']
		        };

		        // Instantiate and draw our chart, passing in some options.
		        var chart = new google.visualization.ColumnChart (document.getElementById('chart_div'));
		        chart.draw(data, options);
		      }
		    </script>

		    <div id="chart_div"></div>


		<?php
		$resultCompare = ob_get_clean();

	} else {
		$resultCompare = '<p>Jūs esate pirmasis dalyvavęs šioje viktorinoje, todėl palyginti jūsų rezultato su kitais dalyviais negalime.</p>';
	}

	return $resultCompare;
}

/*** naujienu dubliavimas ***/

// taxonomy 'new-type' is registered earliew in code, in register_cpt function
// the main news, called LKC news, are named ordinaryNews or News throughout the code

/**
 * attach taxonomy new-type = 'ordinary-new' to all current news
 * need to execute this function in frontend twice, one for lt, one for en langs
 */
function kcsite_update_posts_cats(){
	global $staticVars;
	$args = array(
		'post_type' => 'post', 
		'post_status' => 'any',
		'numberposts' => 1000,
		'lang' => pll_current_language('slug'),
		'tax_query' => array(
			array(
				'taxonomy' => 'new-type',
				'field' => 'id',
				'terms' => array($staticVars['ordinaryNewTypeID']),
				'operator' => 'NOT IN'
			)
		)
	);			
	$results = get_posts($args);
	foreach ($results as $key => $val) {
		wp_set_post_terms($val->ID, array($staticVars['ordinaryNewTypeID']), 'new-type');
	}	
	// iprastos lt - 89, iprastos en - 15
}
// add_action('wp_head', 'kcsite_update_posts_cats');


/**
 * add filter by new type in posts admin list table
 */ 
function kcsite_add_new_type_filter() {

	$taxonomyObject = get_terms('new-type', array('hide_empty' => 0));
	$options = array(array('ID' => 0, 'name' => pll__('Naujienos tipas')));
	$i=1;
	foreach ($taxonomyObject as $key => $val) {
		$options[$i]['slug'] = $val->slug; 
		$options[$i]['name'] = $val->name; 
		$i++;
	}
	// print_r($taxonomyObject);

	$m = isset( $_GET['_NewType'] ) ? $_GET['_NewType'] : 0;
	?>

	<select id="new-type-filter" name='_NewType'>
	<?php
	foreach ($options as $val) {
		printf( "<option %s value='%s'>%s</option>\n",
			selected( $m, $val['slug'], false ),
			esc_attr( $val['slug']),
			$val['name']
		);
	}
	?>
	</select>
	<?php
}

function kcsite_new_type_request($request) {
    if( isset($_GET['_NewType']) && !empty($_GET['_NewType']) ) {
        $request['new-type'] = esc_attr($_GET['_NewType']);
    }
    // print_r($request);exit;
    return $request;
}

global $pagenow;
if (( $pagenow == 'edit.php' ) && (!isset($_GET['post_type'])) || $_GET['post_type'] == 'post') {
    add_filter('request', 'kcsite_new_type_request');
    add_filter('restrict_manage_posts', 'kcsite_add_new_type_filter');
	//add_filter( 'pre_get_posts', 'meta_filter_posts' );
}

function kcsite_new_type_columns_in_wp_list_table(){

	function hs_new_type_column_th( $column ) {
	    $column['new-type'] = __('Naujienos tipas');
	    return $column;
	}

	function hs_new_type_column_td($column_name, $post_id) {
	        $taxonomy = 'new-type';
	        $post_type = get_post_type($post_id);
	        $terms = get_the_terms($post_id, $taxonomy);

	        if (!empty($terms) ) {
	            foreach ( $terms as $term )
	            $post_terms[] ="<a class='cpt-category-column-content' href='edit.php?post_type={$post_type}&{$taxonomy}={$term->slug}'> " .esc_html(sanitize_term_field('name', $term->name, $term->term_id, $taxonomy, 'edit')) . "</a>";
	            echo join('', $post_terms );
	        }
	         //else echo '<i>No Location Set. </i>';
	}

	function hs_new_type_sortable_columns($columns) {
		$columns['new-type'] = __('Naujienos tipas');
		return $columns;
	}

    add_filter( 'manage_edit-post_columns', 'hs_new_type_column_th');
    add_action( 'manage_post_posts_custom_column', 'hs_new_type_column_td', 10, 2);
    add_filter( 'manage_edit-post_sortable_columns', 'hs_new_type_sortable_columns' );
}
add_action('admin_init', 'kcsite_new_type_columns_in_wp_list_table');

/**
 * unset category filter in admin list table as category is not used
 */
function kcsite_no_category_dropdown() {
    add_filter( 'wp_dropdown_cats', '__return_false' );
}
add_action( 'load-edit.php', 'kcsite_no_category_dropdown' );



// add_action('admin_notices', 'kcsite_new_type_duplicate_admin_notice');
// function kcsite_new_type_duplicate_admin_notice() {
//     global $pagenow;
//     if ( $pagenow == 'edit.php' ) {
//         echo '<div class="updated"><p>'; 
//         echo 'Dėmesio, įgalinta naujienų dubliavimo funkcija! Daugiau informacijos rasite <a href="'.WP_CONTENT_URL.'/uploads/naujienu_dublikavimas.doc">ČIA</a>';
//         echo "</p></div>";
// }	}

// add_action('admin_init', 'example_nag_ignore');



/*** EOF naujienu dubliavimas ***/




?>
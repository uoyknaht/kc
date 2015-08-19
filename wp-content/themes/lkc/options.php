<?php
/**
 * A unique identifier is defined to store the options in the database and reference them from the theme.
 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.
 * If the identifier changes, it'll appear as if the options have been reset.
 *
 */

function optionsframework_option_name() {

	// This gets the theme name from the stylesheet (lowercase and without spaces)
	$themename = get_option( 'stylesheet' );
	$themename = preg_replace("/\W/", "_", strtolower($themename) );

	$optionsframework_settings = get_option('optionsframework');
	$optionsframework_settings['id'] = $themename;
	update_option('optionsframework', $optionsframework_settings);

	// echo $themename;
}

// need to override what submenu it creates
/*function optionsframework_add_page() {
	$of_page = add_theme_page(__('Theme Options2', 'optionsframework'), __('Theme Options', 'optionsframework'), 'edit_theme_options', 'options-framework','optionsframework_page');

	// Load the required CSS and javscript
	add_action('admin_enqueue_scripts', 'optionsframework_load_scripts');
	add_action( 'admin_print_styles-' . $of_page, 'optionsframework_load_styles' );
}*/

/**
 * Defines an array of options that will be used to generate the settings page and be saved in the database.
 * When creating the 'id' fields, make sure to use all lowercase and no spaces.
 *
 */

function optionsframework_options() {

	// Test data
	$test_array = array(
		'one' => __('One', 'options_check'),
		'two' => __('Two', 'options_check'),
		'three' => __('Three', 'options_check'),
		'four' => __('Four', 'options_check'),
		'five' => __('Five', 'options_check')
	);

	// Multicheck Array
	$multicheck_array = array(
		'one' => __('French Toast', 'options_check'),
		'two' => __('Pancake', 'options_check'),
		'three' => __('Omelette', 'options_check'),
		'four' => __('Crepe', 'options_check'),
		'five' => __('Waffle', 'options_check')
	);

	// Multicheck Defaults
	$multicheck_defaults = array(
		'one' => '1',
		'five' => '1'
	);

	// Background Defaults
	$background_defaults = array(
		'color' => '',
		'image' => '',
		'repeat' => 'repeat',
		'position' => 'top center',
		'attachment'=>'scroll' );

	// Typography Defaults
	$typography_defaults = array(
		'size' => '15px',
		'face' => 'georgia',
		'style' => 'bold',
		'color' => '#bada55' );
		
	// Typography Options
	$typography_options = array(
		'sizes' => array( '6','12','14','16','20' ),
		'faces' => array( 'Helvetica Neue' => 'Helvetica Neue','Arial' => 'Arial' ),
		'styles' => array( 'normal' => 'Normal','bold' => 'Bold' ),
		'color' => false
	);

	// Pull all the categories into an array
	$options_categories = array();
	$options_categories_obj = get_categories();
	foreach ($options_categories_obj as $category) {
		$options_categories[$category->cat_ID] = $category->cat_name;
	}
	
	// Pull all tags into an array
	$options_tags = array();
	$options_tags_obj = get_tags();
	foreach ( $options_tags_obj as $tag ) {
		$options_tags[$tag->term_id] = $tag->name;
	}

	// Pull all the pages into an array
	$options_pages = array();
	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');
	$options_pages[''] = 'Select a page:';
	foreach ($options_pages_obj as $page) {
		$options_pages[$page->ID] = $page->post_title;
	}

	// If using image radio buttons, define a directory path
	$imagepath =  get_template_directory_uri() . '/img/';

	$options = array();


//==================================================================

	$wp_editor_settings = array(
		'wpautop' => false, // Default
		'textarea_rows' => 15,
		'tinymce' => array( 'plugins' => 'wordpress' )
	);

	$options[] = array(
		'name' => __('Texts', 'kcsite'),
		'type' => 'heading');

	
	$options[] = array(
		'name' => __('Contacts information in footer', 'kcsite'),
		'desc' => false,
		'id' => 'kcsite_footer_contacts_text',
		'type' => 'editor',
		'settings' => $wp_editor_settings);	

	$options[] = array(
		'name' => __('Contacts information in footer (in english)', 'kcsite'),
		'desc' => false,
		'id' => 'kcsite_footer_contacts_text_en',
		'type' => 'editor',
		'settings' => $wp_editor_settings);

	$options[] = array(
		'name' => __('Link to Facebook Page in Header', 'kcsite'),
		'desc' => false,
		'id' => 'kcsite_header_fb_link',
		'std' => '',
		'class' => '',
		'type' => 'text');

	$options[] = array(
		'name' => __('Email Link in Header', 'kcsite'),
		'desc' => false,
		'id' => 'kcsite_header_email_link',
		'std' => '',
		'class' => '',
		'type' => 'text');

	$options[] = array(
		'name' => __('404 Page Title', 'kcsite'),
		'desc' => false,
		'id' => 'kcsite_404_page_title',
		'std' => '',
		'class' => '',
		'type' => 'text');

	$options[] = array(
		'name' => __('404 Page Content', 'kcsite'),
		'desc' => false,
		'id' => 'kcsite_404_page_content',
		'type' => 'editor',
		'settings' => $wp_editor_settings);

	$options[] = array(
		'name' => __('404 Page Title  (in english)', 'kcsite'),
		'desc' => false,
		'id' => 'kcsite_404_page_title_en',
		'std' => '',
		'class' => '',
		'type' => 'text');

	$options[] = array(
		'name' => __('404 Page Content  (in english)', 'kcsite'),
		'desc' => false,
		'id' => 'kcsite_404_page_content_en',
		'type' => 'editor',
		'settings' => $wp_editor_settings);


//==================================================================

	$options[] = array(
		'name' => __('Settings', 'kcsite'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('Google Analytics Code', 'kcsite'),
		'id' => 'kcsite_ga_code',
		//'std' => '1',
		'class' => 'mini',
		'type' => 'text');

/*	$options[] = array(
		'desc' => __('Show categories titles on single new page?', 'kcsite'),
		//'desc' => __('Numeric values only', 'kcsite'),
		'id' => 'kcsite_show_categories_in_single_new',
		//'std' => '0',
		// 'class' => 'mini',
		'type' => 'checkbox');*/

/*	$options[] = array(
		'desc' => __('Show tags titles on single new page?', 'kcsite'),
		//'desc' => __('Numeric values only', 'kcsite'),
		'id' => 'kcsite_show_tags_in_single_new',
		//'std' => '0',
		// 'class' => 'mini',
		'type' => 'checkbox');*/

	$options[] = array(
		'desc' => __('Show edit post link for admins?', 'kcsite'),
		//'desc' => __('Numeric values only', 'kcsite'),
		'id' => 'kcsite_show_admin_links',
		//'std' => '0',
		// 'class' => 'mini',
		'type' => 'checkbox');


//==================================================================  Home banners
	
	$options[] = array(
		'name' => __('Home banners', 'kcsite'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('Number of home banners', 'kcsite'),
		'id' => 'kcsite_home_banners_number',
		'std' => '3',
		'class' => 'mini',
		'type' => 'text');

	$banners_number_option = of_get_option('kcsite_home_banners_number');
	$banners_number = intval((is_numeric($banners_number_option) && $banners_number_option > 0) ? $banners_number_option : 0);

	for($i=0; $i<$banners_number; $i++){

		$options[] = array(
			'desc' => '<hr class="admin-section-hr" />',
			'type' => 'info');

		$options[] = array(
			'name' => false,
			'desc' => sprintf(__('Recommended size: %s', 'kcsite'), '200 x 110px'),
			'id' => 'home_banner_img_path_'.$i,
			'type' => 'upload');

		$options[] = array(
			'name' => __('Link', 'kcsite'),
			'desc' => false,
			'id' => 'home_banner_link_'.$i,
			'type' => 'text');		

		$options[] = array(
			'desc' => __('Open in new window?', 'kcsite'),
			'id' => 'home_banner_link_new_window_'.$i,
			'std' => '1',
			'type' => 'checkbox');

		$options[] = array(
			'desc' => __('Show red border on top?', 'kcsite'),
			'id' => 'kcsite_hb_red_border_'.$i,
			'type' => 'checkbox');

		$options[] = array(
			'desc' => __('Nerodyti banerio', 'kcsite'),
			'id' => 'kcsite_hb_hide_'.$i,
			'type' => 'checkbox');	

	}

//==================================================================  Home banners en
	
	$options[] = array(
		'name' => __('Home banners', 'kcsite').' (en)',
		'type' => 'heading');

	$options[] = array(
		'name' => __('Number of home banners', 'kcsite'),
		'id' => 'kcsite_home_banners_numberen',
		'std' => '3',
		'class' => 'mini',
		'type' => 'text');

	$banners_number_option = of_get_option('kcsite_home_banners_numberen');
	$banners_number = intval((is_numeric($banners_number_option) && $banners_number_option > 0) ? $banners_number_option : 0);

	for($i=0; $i<$banners_number; $i++){

		$options[] = array(
			'desc' => '<hr class="admin-section-hr" />',
			'type' => 'info');

		$options[] = array(
			'name' => false,
			'desc' => sprintf(__('Recommended size: %s', 'kcsite'), '200 x 110px'),
			'id' => 'home_banner_img_path_en'.$i,
			'type' => 'upload');

		$options[] = array(
			'name' => __('Link', 'kcsite'),
			'desc' => false,
			'id' => 'home_banner_link_en'.$i,
			'type' => 'text');		

		$options[] = array(
			'desc' => __('Open in new window?', 'kcsite'),
			'id' => 'home_banner_link_new_window_en'.$i,
			'std' => '1',
			'type' => 'checkbox');

		$options[] = array(
			'desc' => __('Show red border on top?', 'kcsite'),
			'id' => 'kcsite_hb_red_border_en'.$i,
			'type' => 'checkbox');

		$options[] = array(
			'desc' => __('Nerodyti banerio', 'kcsite'),
			'id' => 'kcsite_hb_hide_en'.$i,
			'type' => 'checkbox');	

	}


//================================================================== Right side banners
	
	$options[] = array(
		'name' => __('Right sidebar banners', 'kcsite'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('Number of right sidebar banners', 'kcsite'),
		'id' => 'kcsite_right_sidebar_banners_number',
		'std' => '0',
		'class' => 'mini',
		'type' => 'text');

	$banners_number_option = of_get_option('kcsite_right_sidebar_banners_number');
	$banners_number = intval((is_numeric($banners_number_option) && $banners_number_option > 0) ? $banners_number_option : 0);

	for($i=0; $i<$banners_number; $i++){

		$options[] = array(
			'desc' => '<hr class="admin-section-hr" />',
			'type' => 'info');

		$options[] = array(
			'name' => false,
			'desc' => sprintf(__('Recommended size: %s', 'kcsite'), '240 x 110px'),
			'id' => 'right_sidebar_banner_img_path_'.$i,
			'type' => 'upload');

		$options[] = array(
			'name' => __('Link', 'kcsite'),
			'id' => 'right_sidebar_banner_link_'.$i,
			'type' => 'text');		

		$options[] = array(
			'desc' => __('Open in new window?', 'kcsite'),
			'id' => 'right_sidebar_banner_link_new_window_'.$i,
			'std' => '1',
			'type' => 'checkbox');

		$options[] = array(
			'desc' => __('Show red border on top?', 'kcsite'),
			'id' => 'kcsite_rb_red_border_'.$i,
			'type' => 'checkbox');	

		$options[] = array(
			'desc' => __('Nerodyti banerio', 'kcsite'),
			'id' => 'kcsite_rb_hide_'.$i,
			'type' => 'checkbox');	
	}


//================================================================== Right side banners en
	
	$options[] = array(
		'name' => __('Right sidebar banners', 'kcsite').' (en)',
		'type' => 'heading');

	$options[] = array(
		'name' => __('Number of right sidebar banners', 'kcsite'),
		'id' => 'kcsite_right_sidebar_banners_numberen',
		'std' => '0',
		'class' => 'mini',
		'type' => 'text');

	$banners_number_option = of_get_option('kcsite_right_sidebar_banners_numberen');
	$banners_number = intval((is_numeric($banners_number_option) && $banners_number_option > 0) ? $banners_number_option : 0);

	for($i=0; $i<$banners_number; $i++){

		$options[] = array(
			'desc' => '<hr class="admin-section-hr" />',
			'type' => 'info');

		$options[] = array(
			'name' => false,
			'desc' => sprintf(__('Recommended size: %s', 'kcsite'), '240 x 110px'),
			'id' => 'right_sidebar_banner_img_path_en'.$i,
			'type' => 'upload');

		$options[] = array(
			'name' => __('Link', 'kcsite'),
			'id' => 'right_sidebar_banner_link_en'.$i,
			'type' => 'text');		

		$options[] = array(
			'desc' => __('Open in new window?', 'kcsite'),
			'id' => 'right_sidebar_banner_link_new_window_en'.$i,
			'std' => '1',
			'type' => 'checkbox');

		$options[] = array(
			'desc' => __('Show red border on top?', 'kcsite'),
			'id' => 'kcsite_rb_red_border_en'.$i,
			'type' => 'checkbox');	

		$options[] = array(
			'desc' => __('Nerodyti banerio', 'kcsite'),
			'id' => 'kcsite_rb_hide_en'.$i,
			'type' => 'checkbox');	

	}

	



//==================================================================

/*
	$options[] = array(
		'name' => __('Side Banners', 'kcsite'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('Number of banners', 'kcsite'),
		'desc' => __('Numeric values only', 'kcsite'),
		'id' => 'kcsite_banners_number',
		'std' => '1',
		'class' => 'mini',
		'type' => 'text');

	$options[] = array(
		'name' => __('Upload images for banners.', 'kcsite'),
		'desc' => '',
		'type' => 'info');

	$banners_number = is_numeric(of_get_option('kcsite_banners_number')) ? intval(of_get_option('kcsite_banners_number')) : 1;

	for($i=0; $i<$banners_number; $i++){
		$options[] = array(
			'name' => false,
			'desc' => false,
			'id' => 'banner_img_path_'.$i,
			'type' => 'upload');	

		$options[] = array(
			'name' => __('Banner link', 'kcsite'),
			'desc' => false,
			'id' => 'banner_link_'.$i,
			'type' => 'text');		

		$options[] = array(
			'name' => __('Open in new window?', 'kcsite'),
			'desc' => __('', 'options_check'),
			'id' => 'banner_link_new_window_'.$i,
			'std' => '1',
			'type' => 'checkbox');		
	}

//==================================================================



	$options[] = array(
		'name' => __('Advanced Settings', 'kcsite'),
		'type' => 'heading');


	$options[] = array(
		'name' => __('Input Text', 'options_check'),
		'desc' => __('A text input field.', 'options_check'),
		'id' => 'example_text',
		'std' => 'Default Value',
		'type' => 'text');

	$options[] = array(
		'name' => __('Textarea', 'options_check'),
		'desc' => __('Textarea description.', 'options_check'),
		'id' => 'example_textarea',
		'std' => 'Default Text',
		'type' => 'textarea');

	$options[] = array(
		'name' => __('Input Select Small', 'options_check'),
		'desc' => __('Small Select Box.', 'options_check'),
		'id' => 'example_select',
		'std' => 'three',
		'type' => 'select',
		'class' => 'mini', //mini, tiny, small
		'options' => $test_array);

	$options[] = array(
		'name' => __('Input Select Wide', 'options_check'),
		'desc' => __('A wider select box.', 'options_check'),
		'id' => 'example_select_wide',
		'std' => 'two',
		'type' => 'select',
		'options' => $test_array);

	$options[] = array(
		'name' => __('Select a Category', 'options_check'),
		'desc' => __('Passed an array of categories with cat_ID and cat_name', 'options_check'),
		'id' => 'example_select_categories',
		'type' => 'select',
		'options' => $options_categories);
		
	$options[] = array(
		'name' => __('Select a Tag', 'options_check'),
		'desc' => __('Passed an array of tags with term_id and term_name', 'options_check'),
		'id' => 'example_select_tags',
		'type' => 'select',
		'options' => $options_tags);

	$options[] = array(
		'name' => __('Select a Page', 'options_check'),
		'desc' => __('Passed an pages with ID and post_title', 'options_check'),
		'id' => 'example_select_pages',
		'type' => 'select',
		'options' => $options_pages);

	$options[] = array(
		'name' => __('Input Radio (one)', 'options_check'),
		'desc' => __('Radio select with default options "one".', 'options_check'),
		'id' => 'example_radio',
		'std' => 'one',
		'type' => 'radio',
		'options' => $test_array);

	$options[] = array(
		'name' => __('Example Info', 'options_check'),
		'desc' => __('This is just some example information you can put in the panel.', 'options_check'),
		'type' => 'info');

	$options[] = array(
		'name' => __('Input Checkbox', 'options_check'),
		'desc' => __('Example checkbox, defaults to true.', 'options_check'),
		'id' => 'example_checkbox',
		'std' => '1',
		'type' => 'checkbox');



	$options[] = array(
		'name' => __('Check to Show a Hidden Text Input', 'options_check'),
		'desc' => __('Click here and see what happens.', 'options_check'),
		'id' => 'example_showhidden',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => __('Hidden Text Input', 'options_check'),
		'desc' => __('This option is hidden unless activated by a checkbox click.', 'options_check'),
		'id' => 'example_text_hidden',
		'std' => 'Hello',
		'class' => 'hidden',
		'type' => 'text');

	$options[] = array(
		'name' => "Example Image Selector",
		'desc' => "Images for layout.",
		'id' => "example_images",
		'std' => "2c-l-fixed",
		'type' => "images",
		'options' => array(
			'1col-fixed' => $imagepath . '1col.png',
			'2c-l-fixed' => $imagepath . '2cl.png',
			'2c-r-fixed' => $imagepath . '2cr.png')
	);

	$options[] = array(
		'name' =>  __('Example Background', 'options_check'),
		'desc' => __('Change the background CSS.', 'options_check'),
		'id' => 'example_background',
		'std' => $background_defaults,
		'type' => 'background' );

	$options[] = array(
		'name' => __('Multicheck', 'options_check'),
		'desc' => __('Multicheck description.', 'options_check'),
		'id' => 'example_multicheck',
		'std' => $multicheck_defaults, // These items get checked by default
		'type' => 'multicheck',
		'options' => $multicheck_array);

	$options[] = array(
		'name' => __('Colorpicker', 'options_check'),
		'desc' => __('No color selected by default.', 'options_check'),
		'id' => 'example_colorpicker',
		'std' => '',
		'type' => 'color' );
		
	$options[] = array( 'name' => __('Typography', 'options_check'),
		'desc' => __('Example typography.', 'options_check'),
		'id' => "example_typography",
		'std' => $typography_defaults,
		'type' => 'typography' );
		
	$options[] = array(
		'name' => __('Custom Typography', 'options_check'),
		'desc' => __('Custom typography options.', 'options_check'),
		'id' => "custom_typography",
		'std' => $typography_defaults,
		'type' => 'typography',
		'options' => $typography_options );

	$options[] = array(
		'name' => __('Text Editor', 'options_check'),
		'type' => 'heading' );
*/










	/**
	 * For $settings options see:
	 * http://codex.wordpress.org/Function_Reference/wp_editor
	 *
	 * 'media_buttons' are not supported as there is no post to attach items to
	 * 'textarea_name' is set by the 'id' you choose
	 */

/*	$wp_editor_settings = array(
		'wpautop' => true, // Default
		'textarea_rows' => 5,
		'tinymce' => array( 'plugins' => 'wordpress' )
	);
	
	$options[] = array(
		'name' => __('Default Text Editor', 'options_check'),
		'desc' => sprintf( __( 'You can also pass settings to the editor.  Read more about wp_editor in <a href="%1$s" target="_blank">the WordPress codex</a>', 'options_check' ), 'http://codex.wordpress.org/Function_Reference/wp_editor' ),
		'id' => 'example_editor',
		'type' => 'editor',
		'settings' => $wp_editor_settings );*/

	return $options;
}
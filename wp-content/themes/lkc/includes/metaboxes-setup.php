<?php
/**
 * include WP Alchemy metaboxes class
 */
 $metaboxes_path = TEMPLATEPATH . '/includes/metaboxes/';

if(!class_exists('WPAlchemy_MetaBox')){
	include_once  $metaboxes_path . 'wpalchemy/MetaBox.php';
}
if (is_admin()) wp_enqueue_style('wpalchemy-metabox',  $metaboxes_path . 'metaboxes/meta.css');

/* individual metaboxes: */
//include_once $metaboxes_path.'kcsite-home-slideshow-spec.php';
include_once $metaboxes_path.'kcsite-post-spec.php';
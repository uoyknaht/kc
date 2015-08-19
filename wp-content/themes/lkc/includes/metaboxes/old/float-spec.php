<?php

$float_metabox = new WPAlchemy_MetaBox(array
(
	'id' => '_float_meta',
	'title' => 'Float',
	'template' => get_stylesheet_directory() . '/metaboxes/float-meta.php',
	'types' => array('post', 'sharpinsights', 'sharpbuzz', 'selectedarticles', 'case-studies' ),
	'hide_title' => TRUE,
	'lock' => WPALCHEMY_LOCK_BOTTOM,
	'mode' => WPALCHEMY_MODE_EXTRACT,
	'prefix' => '_fl_'
));
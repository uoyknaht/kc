<?php

$related_metabox = new WPAlchemy_MetaBox(array
(
	'id' => '_related_meta',
	'title' => 'Related Articles',
	'template' => get_stylesheet_directory() . '/metaboxes/related-meta.php',
	'types' => array('sharpinsights', 'case-studies'),
	'mode' => WPALCHEMY_MODE_EXTRACT,
	'prefix' => '_re_'
));

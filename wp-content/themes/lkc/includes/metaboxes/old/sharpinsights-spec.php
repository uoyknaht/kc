<?php

$sh_metabox = new WPAlchemy_MetaBox(array
(
	'id' => '_sharpinsights_meta',
	'title' => 'SharpInsights tagline',
	'template' => get_stylesheet_directory() . '/metaboxes/sharpinsights-meta.php',
	'types' => array('sharpinsights'),
));

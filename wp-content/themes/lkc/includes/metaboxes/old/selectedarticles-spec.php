<?php

$selectedArticles_metabox = new WPAlchemy_MetaBox(array
(
	'id' => '_selectedarticles_meta',
	'title' => 'Selected Article taglines',
	'template' => get_stylesheet_directory() . '/metaboxes/selectedarticles-meta.php',
	'types' => array('selectedarticles'),
));

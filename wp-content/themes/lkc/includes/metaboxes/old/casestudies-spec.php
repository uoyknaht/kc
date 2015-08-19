<?php

$casestudies_metabox = new WPAlchemy_MetaBox(array
(
	'id' => '_casestudies_meta',
	'title' => 'Case Studies tagline',
	'template' => get_stylesheet_directory() . '/metaboxes/casestudies-meta.php',
	'types' => array('case-studies'),
));

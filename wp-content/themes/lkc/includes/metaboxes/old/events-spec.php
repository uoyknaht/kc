<?php

$events_metabox = new WPAlchemy_MetaBox(array
(
	'id' => '_events_meta',
	'title' => 'Event information',
	'template' => get_stylesheet_directory() . '/metaboxes/events-meta.php',
	'types' => array('speeches2'),
));

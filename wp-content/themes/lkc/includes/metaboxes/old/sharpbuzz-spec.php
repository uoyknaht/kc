<?php

$sharbuzz_metabox = new WPAlchemy_MetaBox(array
(
	'id' => '_sharbuzz_meta',
	'title' => 'SharpBuzz meta information',
	'template' => get_stylesheet_directory() . '/metaboxes/sharpbuzz-meta.php',
	'types' => array('sharpbuzz'),
));
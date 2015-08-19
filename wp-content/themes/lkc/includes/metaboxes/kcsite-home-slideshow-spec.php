<?php
$kcsite_hs_metabox = new WPAlchemy_MetaBox(array(
	'id' => '_kcsite_home_slideshow_meta',
	'title' => __('Slideshow Link', 'kcsite'),
	'template' => dirname(__FILE__).'/kcsite-home-slideshow-meta.php',
	'types' => array('home-slideshow'),
	'mode' => WPALCHEMY_MODE_ARRAY,
	'prefix' => '_hs_'
));
<?php
$kcsite_post_metabox = new WPAlchemy_MetaBox(array(
	'id' => '_kcsite_post_meta',
	// 'title' => __('Additional Post Settings', 'kcsite'),
	'title' => 'Papildomi įrašo nustatymai',
	'template' => dirname(__FILE__).'/kcsite-post-meta.php',
	'types' => array('post', 'page', 'interesting-fact'),
	'mode' => WPALCHEMY_MODE_EXTRACT,
	'prefix' => '_kcsite_'
));
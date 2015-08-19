<?php
	/* Shortcode */
	add_shortcode('heading_title', 'ts_heading_title');
	
	/* -----------------------------------------------------------------
		Heading Title
	----------------------------------------------------------------- */
	function ts_heading_title($atts, $content = null) {
		extract(shortcode_atts(array(
		), $atts));
		$content =ts_remove_autop($content);
		$output = '<h1 class="title_pattern uppercase"><span>'.$content.'</span></h1>';
		return do_shortcode($output);
	}
?>
<?php
	/* Shortcode */
	add_shortcode('pdf_link', 'ts_pdf_link');
	
	/* -----------------------------------------------------------------
		Link with pdf icon
	----------------------------------------------------------------- */
	function ts_pdf_link($atts, $content = null) {
		extract(shortcode_atts(array(
		), $atts));
		$content =ts_remove_autop($content);
		//$output = '<span class="pdf-link">'.$content.'</span>';
		//$output = '<div class="pdf-link-wrapper clearfix"><img src="'.get_template_directory_uri().'/images/cachk/pdf-icon-32.png" class="pdf-link-icon"/><span class="pdf-link">'.$content.'</span></div>';
		$output = '<table class="pdf-link-wrapper"><tr><td class="pdf-link-icon"><img src="'.get_template_directory_uri().'/images/cachk/pdf-icon-32.png"/></td><td class="pdf-link">'.$content.'</td></tr></table>';
		return do_shortcode($output);
	}
?>
<?php
	/* Shortcode */
	add_shortcode('pre', 'ts_pre');
	
	/* -----------------------------------------------------------------
		Pre
	----------------------------------------------------------------- */
	function ts_pre($atts, $content) {
	
		$return_html = '<pre>'.strip_tags($content).'</pre>';
		
		return $return_html;
	}

?>
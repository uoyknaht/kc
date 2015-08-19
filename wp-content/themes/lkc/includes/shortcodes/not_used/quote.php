<?php
	/* Pullquote &amp; Blockquote */
	add_shortcode( 'pullquote', 'ts_pullquote' );
	add_shortcode( 'blockquote', 'ts_blockquote' );
	
	/* -----------------------------------------------------------------
		Pullquote
	----------------------------------------------------------------- */
	function ts_pullquote($atts, $content = null) {
		extract(shortcode_atts(array(
					"position" => 'left'
		), $atts));
		
		$content =ts_remove_autop($content);
		
			$output = '<span class="pullquote-'.$position.'">'.$content.'</span>';
			
		return do_shortcode($output);
	}
	
	
 	/* -----------------------------------------------------------------
		Blockquote
	----------------------------------------------------------------- */
	function ts_blockquote($atts, $content = null) {
		$content =ts_remove_autop($content);
		$output = '<blockquote>'.$content.'</blockquote>';
		return do_shortcode($output);
	}

?>
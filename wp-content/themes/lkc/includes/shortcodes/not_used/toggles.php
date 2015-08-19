<?php
	/* Toggle Shortcode */
	add_shortcode('toggles', 'ts_toggles');
	add_shortcode('toggle', 'ts_toggle');
	
	/* -----------------------------------------------------------------
		Toggle
	----------------------------------------------------------------- */
	function ts_toggle($atts, $content = null) {
		
		extract(shortcode_atts(array(
			'title' => 'Unnamed'
		), $atts));
		
		$output = '
				<h2 class="trigger"><span>'.$title.'</span></h2>
				<div class="toggle_container">
					<div class="block">'.ts_remove_wpautop($content).'</div>
				</div>';
			
		return do_shortcode($output);
		
	}
	
	
	/* -----------------------------------------------------------------
		Toggles container
	----------------------------------------------------------------- */
	function ts_toggles($atts, $content = null) {
		$output = '<div id="toggle">'.ts_remove_wpautop($content).'</div>';
		return do_shortcode($output);
		
	}
?>
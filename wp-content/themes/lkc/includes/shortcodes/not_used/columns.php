<?php 
	/* Columns Shortcode */
	add_shortcode('one_half', 'ts_one_half');
	add_shortcode('one_third', 'ts_one_third');
	add_shortcode('one_fourth', 'ts_one_fourth');
	add_shortcode('one_fifth', 'ts_one_fifth');
	add_shortcode('one_sixth', 'ts_one_sixth');
	
	add_shortcode('two_third', 'ts_two_third');
	add_shortcode('two_fourth', 'ts_two_fourth');
	add_shortcode('two_fifth', 'ts_two_fifth');
	add_shortcode('two_sixth', 'ts_two_sixth');
	
	
	add_shortcode('three_fourth', 'ts_three_fourth');
	add_shortcode('three_fifth', 'ts_three_fifth');
	add_shortcode('three_sixth', 'ts_three_sixth');
	
	add_shortcode('four_fifth', 'ts_four_fifth');
	add_shortcode('four_sixth', 'ts_four_sixth');
	
	add_shortcode('five_sixth', 'ts_five_sixth');
	
	
	
	/* -----------------------------------------------------------------
		Columns shortcodes
	----------------------------------------------------------------- */
	function ts_one_half($atts, $content = null) {
		extract(shortcode_atts(array(
					"class" => ''
		), $atts));
		$content = ts_remove_autop($content);
		
		$content = ($content);
		$output = '<div class="one_half '.$class.'">' . $content . '</div>';
		
		return do_shortcode($output);
		
	}
	

	function ts_one_third($atts, $content = null) {
		extract(shortcode_atts(array(
					"class" => ''
		), $atts));
		$content = ts_remove_autop($content);
		
		$content = ($content);
		$output = '<div class="one_third '.$class.'">' . $content . '</div>';
		
		return do_shortcode($output);
		
	}
	
	
	function ts_one_fourth($atts, $content = null) {
		extract(shortcode_atts(array(
					"class" => ''
		), $atts));
		$content = ts_remove_autop($content);
		
		$content = ($content);
		$output = '<div class="one_fourth '.$class.'">' . $content . '</div>';
		
		return do_shortcode($output);
		
	}
	
	
	function ts_one_fifth($atts, $content = null) {
		extract(shortcode_atts(array(
					"class" => ''
		), $atts));
		$content = ts_remove_autop($content);
		
		$content = ($content);
		$output = '<div class="one_fifth '.$class.'">' . $content . '</div>';
		
		return do_shortcode($output);
		
	}
	
	function ts_one_sixth($atts, $content = null) {
		extract(shortcode_atts(array(
					"class" => ''
		), $atts));
		$content = ts_remove_autop($content);
		
		$content = ($content);
		$output = '<div class="one_sixth '.$class.'">' . $content . '</div>';
		
		return do_shortcode($output);
		
	}
	
	function ts_two_third($atts, $content = null) {
		extract(shortcode_atts(array(
					"class" => ''
		), $atts));
		$content = ts_remove_autop($content);
		
		$content = ($content);
		$output = '<div class="two_third '.$class.'">' . $content . '</div>';
		
		return do_shortcode($output);
		
	}
	
	function ts_two_fourth($atts, $content = null) {
		extract(shortcode_atts(array(
					"class" => ''
		), $atts));
		$content = ts_remove_autop($content);
		
		$content = ($content);
		$output = '<div class="two_fourth '.$class.'">' . $content . '</div>';
		
		return do_shortcode($output);
		
	}
	
	function ts_two_fifth($atts, $content = null) {
		extract(shortcode_atts(array(
					"class" => ''
		), $atts));
		$content = ts_remove_autop($content);
		
		$content = ($content);
		$output = '<div class="two_fifth '.$class.'">' . $content . '</div>';
		
		return do_shortcode($output);
		
	}
	
	function ts_two_sixth($atts, $content = null) {
		extract(shortcode_atts(array(
					"class" => ''
		), $atts));
		$content = ts_remove_autop($content);
		
		$content = ($content);
		$output = '<div class="two_sixth '.$class.'">' . $content . '</div>';
		
		return do_shortcode($output);
		
	}
	
	function ts_three_fourth($atts, $content = null) {
		extract(shortcode_atts(array(
					"class" => ''
		), $atts));
		$content = ts_remove_autop($content);
		
		$content = ($content);
		$output = '<div class="three_fourth '.$class.'">' . $content . '</div>';
		
		return do_shortcode($output);
		
	}
	
	function ts_three_fifth($atts, $content = null) {
		extract(shortcode_atts(array(
					"class" => ''
		), $atts));
		$content = ts_remove_autop($content);
		
		$content = ($content);
		$output = '<div class="three_fifth '.$class.'">' . $content . '</div>';
		
		return do_shortcode($output);
		
	}
	
	function ts_three_sixth($atts, $content = null) {
		extract(shortcode_atts(array(
					"class" => ''
		), $atts));
		$content = ts_remove_autop($content);
		
		$content = ($content);
		$output = '<div class="three_sixth '.$class.'">' . $content . '</div>';
		
		return do_shortcode($output);
		
	}
	
	function ts_four_fifth($atts, $content = null) {
		extract(shortcode_atts(array(
					"class" => ''
		), $atts));
		$content = ts_remove_autop($content);
		
		$content = ($content);
		$output = '<div class="four_fifth '.$class.'">' . $content . '</div>';
		
		return do_shortcode($output);
		
	}
	
	
	function ts_four_sixth($atts, $content = null) {
		extract(shortcode_atts(array(
					"class" => ''
		), $atts));
		$content = ts_remove_autop($content);
		
		$content = ($content);
		$output = '<div class="four_sixth '.$class.'">' . $content . '</div>';
		
		return do_shortcode($output);
		
	}
	
	function ts_five_sixth($atts, $content = null) {
		extract(shortcode_atts(array(
					"class" => ''
		), $atts));
		$content = ts_remove_autop($content);
		
		$content = ($content);
		$output = '<div class="five_sixth '.$class.'">' . $content . '</div>';
		
		return do_shortcode($output);
		
	}
?>
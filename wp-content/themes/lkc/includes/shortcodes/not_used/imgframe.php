<?php
	/* Imgframe Shortcode */
	add_shortcode( 'imgframe', 'ts_imgframe' );
	
	/* -----------------------------------------------------------------
		Imgframe
	----------------------------------------------------------------- */
	function ts_imgframe($atts, $content = null) {
		extract(shortcode_atts(array(
					"size" => '',
					"path" => '',
					"align" => ''
		), $atts));
		$content = ts_remove_autop($content);
		
		if($path!=""){
			if($size=="x-small"){
				$output = '<div class="'.$align.'"><img src="'.$path.'" alt="" class="frame" /><span class="shadowimg70"></span></div>';
			}elseif($size=="small"){
				$output = '<div class="'.$align.'"><img src="'.$path.'" alt="" class="frame" /><span class="shadowimg220"></span></div>';
			}elseif($size=="medium"){
				$output = '<div class="'.$align.'"><img src="'.$path.'" alt="" class="frame" /><span class="shadowimg300"></span></div>';
			}elseif($size=="large"){
				$output = '<div class="'.$align.'"><img src="'.$path.'" alt="" class="frame" /><span class="shadowimg610"></span></div>';
			}
		}

		return do_shortcode($output);
		
	}

?>
<?php
	/* Dropcap Shortcode */
	add_shortcode( 'dropcap', 'ts_dropcap' );
	
	/* -----------------------------------------------------------------
		Dropcaps
	----------------------------------------------------------------- */
	function ts_dropcap($atts, $content = null) {
		extract(shortcode_atts(array(
					"type" => ''
		), $atts));
		$content =ts_remove_autop($content);
		if($type=="circle"){
			$output = '<span class="dropcap2">'.$content.'</span>';
		}elseif($type=="square"){
			$output = '<span class="dropcap3">'.$content.'</span>';
		}else{
			$output = '<span class="dropcap1">'.$content.'</span>';
		}		
		return do_shortcode($output);
	}

?>
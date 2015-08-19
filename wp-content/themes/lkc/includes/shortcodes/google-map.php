<?php

function kcsite_google_map($atts, $content = null) {
	extract(shortcode_atts(array(
		"width" => '520',
		"height" => '400',
		//"src" => ''
		"lat" => '54.684351',
		"lng" => '25.264219',
		'zoom' => 16,
		//'language' => 'Zigmanto Sierakausko g. 15, LT-03105 Vilnius',
		'language' => 'lt',
		'text' => 'Zigmanto Sierakausko g. 15, LT-03105 Vilnius'
		), $atts));

	ob_start();
	?>

	<script>
		function initialize(){
			var mapOptions = {
				center:new google.maps.LatLng(<?php echo $lat;?>,<?php echo $lng;?>),
				zoom: <?php echo $zoom; ?>,
				mapTypeId:google.maps.MapTypeId.ROADMAP,
				panControl:false,
				streetViewControl:false
			};
			var map = new google.maps.Map(document.getElementById("google-map"),mapOptions);

			var popup = new google.maps.InfoWindow({
			    content: "<?php echo htmlspecialchars($text);?>",
			    //content: "<?php echo $text;?>",
			    map: map
			});
			var marker = new google.maps.Marker({
			    position: new google.maps.LatLng(<?php echo $lat;?>, <?php echo $lng;?>),
			    map: map
			    //icon: '<?php echo get_template_directory_uri(); ?>/img/marker.png'
			});

			google.maps.event.addListener(marker, 'click', function(e) {
			    // you can use event object as 'e' here
			    popup.open(map, this);
			});

			marker.setMap(map);
		}
		function loadScript() {
		  var script = document.createElement("script");
		  script.type = "text/javascript";
		  script.src = "http://maps.googleapis.com/maps/api/js?v=3.2&sensor=false&callback=initialize&language=<?php echo $language;?>";
		  document.body.appendChild(script);
		}

		window.onload = loadScript;
		//google.maps.event.addDomListener(window, 'load', initialize);
	</script>

	<div id="google-map" style=" width: <?php echo $width;?>px; height: <?php echo $height;?>px"></div>


	<?php
	$output = ob_get_contents();
	ob_end_clean();

	return do_shortcode($output);	

	//return '<div class="google-map"><iframe width="'.$width.'" height="'.$height.'" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="'.$src.'&amp;output=embed"></iframe></div>';
}
add_shortcode("googlemap", "kcsite_google_map");

//<iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps?f=q&amp;source=s_q&amp;hl=lt&amp;geocode=&amp;q=Kauno+gatv%C4%97+27,+Klaip%C4%97da&amp;aq=&amp;sll=37.0625,-95.677068&amp;sspn=45.014453,68.291016&amp;ie=UTF8&amp;hq=&amp;hnear=Kauno+gatv%C4%97+27,+Klaip%C4%97da,+Klaip%C4%97dos+miesto+savivaldyb%C4%97+91159,+Lietuvos+Respublika&amp;t=m&amp;ll=55.697518,21.161127&amp;spn=0.01693,0.036478&amp;z=14&amp;output=embed"></iframe><br /><small><a href="https://maps.google.com/maps?f=q&amp;source=embed&amp;hl=lt&amp;geocode=&amp;q=Kauno+gatv%C4%97+27,+Klaip%C4%97da&amp;aq=&amp;sll=37.0625,-95.677068&amp;sspn=45.014453,68.291016&amp;ie=UTF8&amp;hq=&amp;hnear=Kauno+gatv%C4%97+27,+Klaip%C4%97da,+Klaip%C4%97dos+miesto+savivaldyb%C4%97+91159,+Lietuvos+Respublika&amp;t=m&amp;ll=55.697518,21.161127&amp;spn=0.01693,0.036478&amp;z=14" style="color:#0000FF;text-align:left">Žiūrėti didesnį žemėlapio vaizdą</a></small>
?>
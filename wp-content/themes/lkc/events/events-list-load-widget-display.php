<?php 
/**
 * This is the template for the output of the events list widget. 
 * All the items are turned on and off through the widget admin.
 * There is currently no default styling, which is highly needed.
 *
 * You can customize this view by putting a replacement file of the same name (events-list-load-widget-display.php) in the events/ directory of your theme.
 *
 * @return string
 */

// Vars set:
// '$event->AllDay',
// '$event->StartDate',
// '$event->EndDate',
// '$event->ShowMapLink',
// '$event->ShowMap',
// '$event->Cost',
// '$event->Phone',

// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }

$event = array();
$tribe_ecp = TribeEvents::instance();
reset($tribe_ecp->metaTags); // Move pointer to beginning of array.
foreach($tribe_ecp->metaTags as $tag){
	$var_name = str_replace('_Event','',$tag);
	$event[$var_name] = tribe_get_event_meta( $post->ID, $tag, true );
}

$event = (object) $event; //Easier to work with.

ob_start();
if ( !isset($alt_text) ) { $alt_text = ''; }
//$alt_text = $alt_text =='' ? 'clearfix' : $alt_text.' clearfix';
post_class($alt_text,$post->ID);
$class = ob_get_contents();
ob_end_clean();
?>
<li class="clearfix">
	<div class="event-date">
		<?php
			
		$timestamp = strtotime($event->StartDate);
		$date = getDate($timestamp);
		$dayNo = $date['mday'];
		$monthNo = $date['mon'];
		global $wp_locale;
		$month = $wp_locale->get_month($monthNo);
		$monthAbrrev =  $wp_locale->get_month_abbrev($month);
		?>

		<?php echo $dayNo;?><br>
		<?php echo $monthAbrrev; ?>
			
	</div>
	<div class="event-content">
		<h4 class="event-title"><a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a></h4>
	</div>
</li>
<?php $alt_text = ( empty( $alt_text ) ) ? 'alt' : ''; ?>
<?php
/**
 * Grid view template.  This file loads the TEC month view, specifically the 
 * month view navigation.  The actual rendering if the calendar happens in the 
 * table.php template.
 *
 * You can customize this view by putting a replacement file of the same name (gridview.php) in the events/ directory of your theme.
 */

// Don't load directly
if ( !defined('ABSPATH') ) { die('-1'); }
$tribe_ecp = TribeEvents::instance();
?>	
<div id="tribe-events-content" class="grid" data-title="<?php wp_title(); ?>">
	<div id='tribe-events-calendar-header' class="clearfix">
		<span class='tribe-events-month-nav'>
			<span class='tribe-events-prev-month'>
				<a href='<?php echo kcsite_multilingualizeUrl(tribe_get_previous_month_link()); ?>' class="tribe-pjax tribe-month-navigator help-inline">
				<?php //echo tribe_get_previous_month_text(); ?>
				</a>
			</span>

			<?php //tribe_month_year_dropdowns( "tribe-events-" ); ?>
			<?php 
			if(pll_current_language('slug') == 'lt'){
				tribe_month_year_dropdowns( "tribe-events-" );
			} else {
				// making correct form action attribute for foreign languages
				ob_start();
				tribe_month_year_dropdowns( "tribe-events-" );
				$form = ob_get_clean();

				$tribeOptions = get_option('tribe_events_calendar_options', array());
				$eventsSlug = $tribeOptions['eventsSlug'];

				// works in localhost:
				// $needle = get_bloginfo('siteurl') . '/' . $eventsSlug;
				// $replacement = get_bloginfo('siteurl') . '/' . pll_current_language('slug') . '/' . $eventsSlug;
				
				// works online:
				$needle = substr(get_bloginfo('siteurl'), 0, strlen(get_bloginfo('siteurl')) - 3) . '/' . $eventsSlug;
				$replacement = get_bloginfo('siteurl') . '/' . $eventsSlug;

				$form = str_replace($needle , $replacement, $form);
				echo $form;
			}
			?>
			<span class='tribe-events-next-month'>
				<a href='<?php echo kcsite_multilingualizeUrl(tribe_get_next_month_link()); ?>' class="tribe-pjax tribe-month-navigator help-inline">
				<?php //echo tribe_get_next_month_text(); ?>
				</a>
           <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" class="ajax-loading" id="ajax-loading" alt="" style='display: none'/>
			</span>
		</span>

		<span class='tribe-events-calendar-buttons'>
			<a class='tribe-events-button-off' href='<?php echo tribe_get_listview_link(); ?>'><?php _e('Event List', 'tribe-events-calendar'); ?></a>
			<a class='tribe-events-button-on' href='<?php echo tribe_get_gridview_link(); ?>'><?php _e('Calendar', 'tribe-events-calendar'); ?></a>
		</span>
	</div><!-- tribe-events-calendar-header -->
	<?php tribe_calendar_grid(); // See the views/table.php template for customization ?>
  <?php if( function_exists( 'tribe_get_ical_link' ) ): ?>
     <a title="<?php esc_attr_e('iCal Import', 'tribe-events-calendar'); ?>" class="ical" href="<?php echo tribe_get_ical_link(); ?>"><?php _e('iCal Import', 'tribe-events-calendar'); ?></a>
  <?php endif; ?>
	<?php if (tribe_get_option('donate-link', false) == true) { ?>
		<p class="tribe-promo-banner"><?php echo apply_filters('tribe_promo_banner', sprintf( __('Calendar powered by %sThe Events Calendar%s', 'tribe-events-calendar'), '<a href="http://tri.be/wordpress-events-calendar/">', '</a>' ) ); ?></p>
	<?php } ?>
</div>

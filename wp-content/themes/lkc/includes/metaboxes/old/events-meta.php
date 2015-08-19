<div class="my_meta_control">
	<p>
		<label style="display: inline-block; width: 90px; margin-top: 0px;">Event Date</label>	
		<span style="display: inline-block">Month</span>
		<?php $mb->the_field('s_month'); ?>
		<select name="<?php $mb->the_name(); ?>">
		<?php $months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'); ?>		
		<?php for ($i=0; $i < 13; $i++) { ?>
			<option value="<?php echo $months[$i] ?>" <?php $mb->the_select_state($months[$i]); ?>><?php echo $months[$i] ?></option>
		<?php } ?>
		</select>
		
		<span style="display: inline-block">&nbsp;&nbsp;Day</span>
		<?php $mb->the_field('s_day'); ?>
		<select name="<?php $mb->the_name(); ?>">
		<?php for ($i =1; $i < 32; $i++) { ?>
			<option value="<?php echo $i ?>" <?php $mb->the_select_state($i); ?>><?php echo $i ?></option>
		<?php } ?>
		</select>		
		
		<span style="display: inline-block">&nbsp;&nbsp;Year</span>
		<?php $mb->the_field('s_year'); ?>
		<select name="<?php $mb->the_name(); ?>">
		<?php 		
		$curYear = date('Y'); 
		for ($i = 2000; $i < $curYear + 5; $i++) {
		?>
			<option value="<?php echo $i ?>" <?php $mb->the_select_state($i); ?>><?php echo $i ?></option>
		<?php } ?>
		</select>
	</p>
	<p>
		<label style="display: inline-block; width: 90px; margin-top: 0px;">Event Location</label>
		<?php $mb->the_field('location'); ?>
		<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"  style="display: inline; width: 250px;"  />
	</p>
	<label for="sticky_event" style="display: inline-block; width: 90px; margin-top: 0px;">Sticky?</label>
	<?php $items = array('sticky_event'); ?>
	<?php while ($mb->have_fields('sticky_event', count($items))): ?>	
		<?php $item = $items[$mb->get_the_index()]; ?>		
		<input type="checkbox" name="<?php $mb->the_name(); ?>" value="<?php echo $item; ?>"<?php $mb->the_checkbox_state($item); ?> id="sticky_event" /> 
	<?php endwhile; ?>	
</div>
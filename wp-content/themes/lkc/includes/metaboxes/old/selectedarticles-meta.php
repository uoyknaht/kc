<div class="my_meta_control">
	<p>
		<label style="display: inline-block; width: 90px; margin-top: 0px;">Publish Date</label>
		<?php $mb->the_field('sa_date'); ?>
		<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" style="display: inline; width: 250px;"  />
		<span style="display: inline-block">f.e. September, 2011. Or a tagline can be used instead.<i></i></span>
	</p>
	<p>
		<label style="display: inline-block; width: 90px; margin-top: 0px;">Publication</label>
		<?php $mb->the_field('sa_publication'); ?>
		<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"  style="display: inline; width: 250px;"  />
	</p>
	<label for="sticky_selected_article" style="display: inline-block; width: 90px; margin-top: 0px;">Sticky?</label>
	<?php $items = array('sticky'); ?>
	<?php while ($mb->have_fields('sticky', count($items))): ?>	
		<?php $item = $items[$mb->get_the_index()]; ?>		
		<input type="checkbox" name="<?php $mb->the_name(); ?>" value="<?php echo $item; ?>"<?php $mb->the_checkbox_state($item); ?> id="sticky_selected_article" /> 
	<?php endwhile; ?>	
</div>
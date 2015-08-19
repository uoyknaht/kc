<div class="my_meta_control">
	<p>
		<label for="show_in_float" style="display: inline-block; width: 200px; margin-top: 0px;">Show this post in pop-out alert?</label>

			<?php $mb->the_field('show_in_float'); ?>	
			<input type="checkbox" name="<?php $mb->the_name(); ?>" value="show_in_float" <?php echo $mb->is_value('show_in_float') ? 'checked="checked"' : ''; ?> id="show_in_float" /> 
			<span style="display: inline-block; font-size: 11px;" ></span>

	</p>	
</div>
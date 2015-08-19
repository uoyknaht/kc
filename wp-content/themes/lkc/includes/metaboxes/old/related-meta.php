<div class="my_meta_control">

	<?php while($mb->have_fields_and_multi('related_articles')): ?>
		<?php $mb->the_group_open(); ?>
		<p>
			<label style="display: inline-block; margin-top: 0px;">Article Title</label>
			<?php $mb->the_field('related_title'); ?>
			<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"  style="display: inline; width: 250px;"  />
			&nbsp;&nbsp;&nbsp;&nbsp;
			<label style="display: inline-block; margin-top: 0px;">Article Link</label>
			<?php $mb->the_field('related_link'); ?>
			<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"  style="display: inline; width: 250px;"  />
			&nbsp;&nbsp;&nbsp;&nbsp;
			<label style="display: inline-block; margin-top: 0px;">Open in new window?</label>
			<?php $mb->the_field('related_new_window'); ?>
			<input type="checkbox" name="<?php $mb->the_name(); ?>" value="1" <?php echo $mb->is_value('1') ? 'checked="checked"' : ''; ?> id="related_new_window" style="display: inline;"  />
			&nbsp;&nbsp;&nbsp;&nbsp;

			<a style="display: inline-block; margin-top: 0px;" href="#" class="dodelete button">Remove Article</a>
		</p>
		<?php $mb->the_group_close(); ?>
	<?php endwhile; ?>
 
	<p style="margin-bottom:15px; padding-top:5px;"><a href="#" class="docopy-related_articles button">Add One More Article</a></p>	
	
</div>
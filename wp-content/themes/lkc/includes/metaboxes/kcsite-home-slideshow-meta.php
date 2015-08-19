<div class="my_meta_control">
	<p>
		<label><?php _e('Slideshow Link', 'kcsite');?></label><br/>
		<?php $mb->the_field('hs_link'); ?>
		<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" style="width: 300px;"/>
		<!-- <br><span class="metabox-explanation"><?php //_e('Useful if you want to keep the posts order, but change shown date', 'kcsite');?></span> -->

	</p>
	<p>
		<?php $metabox->the_field('hs_link_target'); ?>
    	<label><input type="checkbox" name="<?php $metabox->the_name(); ?>" value="1"<?php if ($metabox->get_the_value()) echo ' checked="checked"'; ?>/> <?php _e('Open in New Window', 'kcsite');?>?</label>
	</p>


	<?php /*
	<p>
		<label><?php _e('Cut the post and show "read more" link in news list?');?></label><br/>
		<?php $values = array('Yes','No'); ?>
		<?php foreach ($values as $i => $value): ?>
			<?php 
			$mb->the_field('kcsite_show_read_more_link'); 
			if(is_null($mb->get_the_value()))	$mb->meta[$mb->name] = 'Yes';
			?>
			<label>
				<input type="radio" name="<?php $mb->the_name(); ?>" value="<?php echo $value; ?>"<?php $mb->the_radio_state($value); ?>/> <?php echo $value; ?>
			</label>
		<?php endforeach; ?>
	</p>
	*/ ?>
	<?php /*
	<p>
		<label><?php _e('Show featured image in news list?', 'kcsite');?></label><br/>
		<?php 
		$values = array('Yes', 'No'); 
		?>
		<?php foreach ($values as $i => $value): ?>
			<?php 
			$mb->the_field('kcsite_show_featured_image'); 
			if(is_null($mb->get_the_value()))	$mb->meta[$mb->name] = 'Yes';
			?>
			<label>
				<input type="radio" name="<?php $mb->the_name(); ?>" value="<?php echo $value; ?>"<?php $mb->the_radio_state($value); ?>/> <?php echo $value; ?>
			</label>
		<?php endforeach; ?>
	</p>
	*/ ?>
	<?php /*
	<p>
		<label><?php _e('Show uploaded images gallery?');?></label><br/>
		<?php $values = array('Yes','No'); ?>
		<?php foreach ($values as $i => $value): ?>
			<?php 
			$mb->the_field('kcsite_show_image_gallery'); 
			if(is_null($mb->get_the_value()))	$mb->meta[$mb->name] = 'Yes';
			?>
			<label>
				<input type="radio" name="<?php $mb->the_name(); ?>" value="<?php echo $value; ?>"<?php $mb->the_radio_state($value); ?>/> <?php echo $value; ?>
			</label>
		<?php endforeach; ?>
		<br><span class="metabox-explanation"><?php _e('You can also insert gallery into post by placing "[gallery]" tag');?></span>
	</p>
	*/ ?>

</div>
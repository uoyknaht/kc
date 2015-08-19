<div class="kcsite-meta-control post-type-<?php echo get_post_type(); ?>">
	
	<p class="kcsite-posts-metabox-subtitle">
		<label><?php _e('Post Subtitle:', 'kcsite');?></label><br/>
		<?php $mb->the_field('subtitle'); ?>
		<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" style="width: 310px;" />
		<span class="metabox-explanation"><?php _e('Shown below post title', 'kcsite');?></span>
	</p>	
	<div style="background-color: #dddddd; padding: 1px 5px; border-radius:4px;">
	<p>Jeigu <strong>yra parinktas</strong> specialusis paveikslėlis:</p>
	<p class="kcsite-posts-metabox-hide-feat-img">
		<?php $metabox->the_field('hide_feat_img'); ?>
		<label>
			<input type="checkbox" name="<?php $metabox->the_name(); ?>" value="1"<?php if ($metabox->get_the_value()) echo ' checked="checked"'; ?>/> 
			<?php _e('Hide featured image in news list', 'kcsite');?>
		</label>
	</p>	
	<p class="kcsite-posts-metabox-hide-feat-img-in-single">
		<?php $metabox->the_field('hide_feat_img_insingle'); ?>
		<label>
			<input type="checkbox" name="<?php $metabox->the_name(); ?>" value="1"<?php if ($metabox->get_the_value()) echo ' checked="checked"'; ?>/> 
			<?php _e('Hide featured image in single view', 'kcsite');?>
		</label>
	</p>	
	<p class="kcsite-posts-metabox-hide-feat-img-in-single">
		<?php $metabox->the_field('show_not_cropped'); ?>
		<label>
			<input type="checkbox" name="<?php $metabox->the_name(); ?>" value="1"<?php if ($metabox->get_the_value()) echo ' checked="checked"'; ?>/> 
			<?php _e('Show not cropped image in single view', 'kcsite');?>
		</label>
		<br><span class="metabox-explanation"><?php _e('If cropped image does not look good, you can show the original proportions image', 'kcsite');?></span>
	</p>
	</div>

	<?php /*
	<table class="kcsite-posts-metabox-show-in-home-page">
		<tr>
			<td>
				<p>
					<?php $metabox->the_field('show_in_homepage'); ?>
			    	<label><input type="checkbox" name="<?php $metabox->the_name(); ?>" value="1"<?php if ($metabox->get_the_value()) echo ' checked="checked"'; ?>/> <?php _e('Show in Home Page', 'kcsite');?>?</label>
					<br><span class="metabox-explanation"><?php _e('If you select more than one post, the newest will be shown', 'kcsite');?></span>
				</p>

			</td>
			<td>
				<p>
					<?php $metabox->the_field('homepage_excerpt_length'); ?>
					<label><?php _e('Excerpt Length in Words (optional)', 'kcsite');?> :</label>
					<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" style="width: 50px;"/>
				</p>
			</td>
		</tr>
	</table>
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

</div>
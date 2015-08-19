<div class="my_meta_control">
	<p>
		<label style="display: inline-block; width: 150px; margin-top: 0px;">Sharpbuzz tagline</label>
		<?php $mb->the_field('sharpbuzz_tagline'); ?>
		<input style="display: inline; width: 50%;" type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
	</p>
	
		<label style="display: inline-block; width: 150px; margin-top: 0px;">Link to video</label>
		<?php $mb->the_field('sharpbuzz_video'); ?>
		<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" style="display: inline; width: 50%;" />
		<span style="display: inline-block;" >&nbsp;&nbsp;&nbsp;
			<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/youtube_icon.png"  style="vertical-align: middle;"/>&nbsp;&nbsp;&nbsp;
			<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/vimeo_icon.png"  style="vertical-align: middle;"/></span>
			<span style="display: inline-block; font-size: 11px; font-style: italic; margin-left: 165px;" >
	Video link will not be used if there is mp3 file uploaded. So if you want to attach audio to a post, upload mp3 file. If you want to attach video, enter the link.
	</span>
	</p>	
	
	<p>
		<label for="use_featured_image" style="display: inline-block; width: 150px; margin-top: 0px;">Use featured image as thumbnail</label>
			<?php $mb->the_field('use_featured_image'); ?>	
			<input  type="checkbox" name="<?php $mb->the_name(); ?>" value="use_featured_image" <?php echo $mb->is_value('use_featured_image') ? 'checked="checked"' : ''; ?> id="use_featured_image" /> 
			<span style="display: inline-block; font-size: 11px; font-style: italic;" >By default, for audio a default thumbnail is shown, for video - a thumbnail from a video. You can use a featured image for thumbnail. If you check this but set no featured image, the default thumbnail will be shown.</span>
		
	</p>
	
</div>
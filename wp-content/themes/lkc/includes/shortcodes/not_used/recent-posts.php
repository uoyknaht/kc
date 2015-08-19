<?php
	/* Recent Posts */
	add_shortcode( 'recent_posts', 'ts_recentposts' );
	
	function ts_recentposts($atts, $content = null) {
		extract(shortcode_atts(array(
					"title" => '',
					"cat" => '',
					"longchar" =>130
		), $atts));
		
			$content =ts_remove_autop($content);
			$output  ='';
			if($title!=""){
			$output  .='<h2 class="title_pattern uppercase"><span>'.$title.'</span></h2>';
			}

			$i=1;
			query_posts("showposts=4&category_name=" . $cat);
			global $post;
			
			$output.='<ul id="recentpost">';
			
			while (have_posts()) : the_post();
			if(($i%4)==0){
			$addclass ="last";
			}else{
			$addclass ="";
			}
			$excerpt = get_the_excerpt(); 
			
			//get_comment
			$num_comments = get_comments_number(); // for some reason get_comments_number only returns a numeric value displaying the number of comments
			 if ( comments_open() ){
				  if($num_comments == 0){
					  $comments = __('No Comments','templatesquare');
				  }
				  elseif($num_comments > 1){
					  $comments = $num_comments. __(' Comments','templatesquare');
				  }
				  else{
					   $comments ="1 Comment";
				  }
			 $write_comments = '<a href="' . get_comments_link() .'">'. $comments.'</a>';
			 }
			else{$write_comments =  __('Comments off','templatesquare');}
			
			//get blog post thumb
			$custom = get_post_custom($post->ID);
			$cf_thumb = (isset($custom["thumb"][0]))? $custom["thumb"][0] : "";
			
			if($cf_thumb!=""){
				$thumb = '<img src='. $cf_thumb .' alt="" width="210" height="158" class="frame"/><span class="shadowimg220"></span>';
			}elseif(has_post_thumbnail($post->ID) ){
				$thumb = get_the_post_thumbnail($post->ID, 'blog-post-thumb', array('alt' => '', 'class' => 'frame')).'<span class="shadowimg220"></span>';
			}else{
				$thumb ="";
			}
			
			$output  .='<li class="'.$addclass.'">';
			$output  .= $thumb;
			$output  .='<div class="entry-date">'.get_the_time('d/m/y').' - '.$write_comments.'</div>';
			$output  .='<h5 class="colortext"><a href="'.get_permalink().'">'.get_the_title().'</a></h5>';
			$output  .='<span>'.ts_string_limit_char($excerpt,$longchar).'</span>';
			$output  .='</li>';
			
			 $i++; $addclass=""; endwhile; wp_reset_query();
			 
			 $output.='</ul>';
			 $output.='<div class="clear"></div>';
			 
			 return do_shortcode($output);
} 
?>
<?php
	//Count all posts from post type 'portfolio'.	
	function ts_portfolio_getnumposts($cat){
		global $wpdb;
		$qryString = "
			SELECT	Count(*) as totpost FROM ".$wpdb->posts." a 
			INNER 	JOIN ".$wpdb->term_relationships." b ON a.ID = b.object_id 
			INNER 	JOIN ".$wpdb->term_taxonomy." c ON b.term_taxonomy_id = c.term_taxonomy_id
			INNER	JOIN ".$wpdb->terms."  d ON c.term_id = d.term_id
			WHERE 	a.post_type = 'portfolio-view'
		";
		if(strlen($cat)>0){
			$qryString .= " AND	d.slug = '".$cat."'";
		}
		$numposts = $wpdb->get_var($wpdb->prepare($qryString));
		return $numposts;
	}
	

	/* Recent Posts */
	add_shortcode( 'portfolio', 'ts_portfolio_shortcode' );
	
	function ts_portfolio_shortcode($atts, $content = null) {
		extract(shortcode_atts(array(
					"title" => '',
					"col" => '',
					"cat" => '',
					"showpost" => '4',
					"showtitle" => '',
					"showdesc" => '',
					"moretext" => '',
					"showseparator" => '',
					"showpaging" => 'yes',
					"longdesc" => '200'
					
					
		), $atts));
		
			$content =ts_remove_autop($content);
			
			$category = get_term_by( 'slug', $cat, 'portfoliocat' );
			$category = $category->term_id;
			
			$output ='<div class="pf-shortcode">';
			if($title!=""){
				$output .=' <h2 class="title_pattern uppercase"><span>'.$title.'</span></h2>';
			}

			if($showpaging=="yes"){
				$paged = (get_query_var('paged'))? get_query_var('paged') : 1 ;
				$output .= ts_portfolio($col, $category, $showpost, $showtitle, $showdesc, $moretext, $showseparator, $paged, $longdesc);
			}else{
				$output .= ts_portfolio($col, $category, $showpost, $showtitle, $showdesc, $moretext, $showseparator, '', $longdesc);
			}
			
			if($showpaging=="yes"){
			
				//Get total of all posts from post type 'portfoliio'.
				$numposts = ts_portfolio_getnumposts($cat);
				
				//Count the total page.
				$num_page = floor($numposts/$showpost)+1;
				$num_page = ($numposts%$showpost!=0)? $num_page : $num_page - 1;
				
				 if ( $num_page > 1 ) :
					 if(function_exists('wp_pagenavi')) {
						 ob_start();
						 wp_pagenavi();
						  $output .= ob_get_contents();
						  ob_end_clean();
					  }else{
						$output .='<div id="nav-below" class="navigation nav2">
							<div class="nav-previous">'.get_next_posts_link( __( '<span class="button"><span class="meta-nav">&larr;</span> Prev</span>', 'templatesquare' ) ).'</div>
							<div class="nav-next">'.get_previous_posts_link( __( '<span class="button">Next <span class="meta-nav">&rarr;</span></span>', 'templatesquare' ) ).'</div>
						</div><!-- #nav-below -->';
					 }
				endif; 
			
			}
			
			$output .='</div>';//end pf-shortcode
			
			 wp_reset_query();

			return do_shortcode($output);
} 
?>
<?php
function wpsqt_is_section($section) {
	return ( isset($_GET['section']) && $_GET['section'] == $section);
}

if ( isset($_GET['id']) ){
	global $wpdb;
	
	$quizName = $wpdb->get_var(
					$wpdb->prepare("SELECT name FROM `".WPSQT_TABLE_QUIZ_SURVEYS."` WHERE id = %s", array($_GET['id']))
							 );
	$quizType = $wpdb->get_var(
					$wpdb->prepare("SELECT type FROM `".WPSQT_TABLE_QUIZ_SURVEYS."` WHERE id = %s", array($_GET['id']))
							 );

	if (isset($_POST['new-page'])) {
		$shortcode = '[wpsqt type="'.$quizType.'" name="'.$quizName.'"]';
		$post = array(
			'post_author' => get_current_user_id(),
			'post_content' => $shortcode,
			'post_name' => str_replace(' ', '-', strtolower($quizName)),
			'post_title' => $_POST['quiz-name'],
			'post_status' => 'publish',
			'post_type' => 'page',
		);
		wp_insert_post($post);
	}
	
	?>
	<div>
		<ul class="subsubsub">
			<li><strong><?php echo $quizName; ?> :</strong></li> 
			<?php if (isset($_GET['subsection'])) { ?>
				<li><a href="<?php echo WPSQT_URL_MAIN; ?>&section=edit&subsection=<?php esc_html_e($_GET["subsection"], 'wp-survey-and-quiz-tool'); ?>&id=<?php esc_html_e($_GET["id"], 'wp-survey-and-quiz-tool'); ?>"<?php if ( wpsqt_is_section('edit') ) { ?> class="current"<?php }?>>Edit</a> | </li> 
				<li><a href="<?php echo WPSQT_URL_MAIN; ?>&section=sections&subsection=<?php esc_html_e($_GET["subsection"], 'wp-survey-and-quiz-tool'); ?>&id=<?php esc_html_e($_GET["id"], 'wp-survey-and-quiz-tool'); ?>"<?php if ( wpsqt_is_section('sections') ) { ?> class="current"<?php }?>>Sections</a> | </li> 
				<li><a href="<?php echo WPSQT_URL_MAIN; ?>&section=questions&subsection=<?php esc_html_e($_GET["subsection"], 'wp-survey-and-quiz-tool'); ?>&id=<?php esc_html_e($_GET["id"], 'wp-survey-and-quiz-tool'); ?>"<?php if ( wpsqt_is_section('questions') ) { ?> class="current"<?php }?>>Questions</a> | </li>  
				<li><a href="<?php echo WPSQT_URL_MAIN; ?>&section=form&subsection=<?php esc_html_e($_GET['subsection'], 'wp-survey-and-quiz-tool'); ?>&id=<?php esc_html_e($_GET["id"], 'wp-survey-and-quiz-tool'); ?>"<?php if ( wpsqt_is_section('form') ) { ?> class="current"<?php }?>>Form</a> | </li> 
				<li><a href="<?php echo WPSQT_URL_MAIN; ?>&section=results&subsection=<?php esc_html_e($_GET["subsection"], 'wp-survey-and-quiz-tool'); ?>&id=<?php esc_html_e($_GET["id"], 'wp-survey-and-quiz-tool'); ?>"<?php if ( wpsqt_is_section('results') ) { ?> class="current"<?php }?>>Results</a></li> 
			<?php } ?>
			<li style="padding-left: 30px;">Shortcode: <pre style="display: inline;">[wpsqt name="<?php echo $quizName; ?>" type="<?php echo $quizType; ?>"]</pre></li>
		</ul>

		<form action="<?php echo WPSQT_URL_MAIN; ?>&section=edit&subsection=<?php esc_html_e($_GET["subsection"], 'wp-survey-and-quiz-tool'); ?>&id=<?php echo $_GET['id']; ?>" method="post">
			<input type="submit" name="new-page" value="Insert Into New Page" class="button-secondary" style="float:right;" />
			<input type="hidden" name="quiz-name" value="<?php echo $quizName; ?>" />
		</form>
		<div style="clear:both;"></div>
	</div>
	<?php 						 
}?>

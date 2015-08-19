<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="ie6 lt-ie7 lt-ie8 lt-ie9 ie" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html class="ie7 lt-ie8 lt-ie9 ie" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie8 lt-ie9 ie" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 9]>
<html class="ie9 ie" <?php language_attributes(); ?>>
<![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html class="not-ie" <?php language_attributes(); ?>> <!--<![endif]-->
	<head>
		<meta charset="<?php bloginfo('charset'); ?>" />
		<title><?php wp_title( '-', true, 'right' ); ?></title>
		<link rel="shortcut icon" href="<?php echo get_bloginfo('stylesheet_directory');?>/images/favicon.ico" />
		<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri(); ?>/css/app.css?19" />
		<!--[if lte IE 6]><link rel="stylesheet" type="text/css" media="all" href="<?php echo get_template_directory_uri(); ?>/css/lte-ie6.css" /><![endif]-->	
		<!--[if lt IE 9]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->	
		<?php wp_head(); ?>

		<?php if(of_get_option('kcsite_ga_code')) get_template_part('template-parts/ga_code');		 ?>

	</head>	
	<body <?php body_class(); ?>>					


			<div id="header">
				
				<div class="wrap">

					<a href="<?php echo pll_home_url(); ?>" id="logo"></a>
					
					<ul class="mini-nav clearfix">
						<?php global $staticVars; ?>

						<?php pll_the_languages(array('display_names_as' => 'slug', 'hide_current' => 1 )); ?>
						<!-- <a href="<?php echo get_permalink(); ?>" class="mini-nav-en">EN</a> -->
						<a href="<?php echo of_get_option('kcsite_header_fb_link'); ?>" title="<?php pll_e('Lietuvos kino centro Facebook profilis');?>" target="_blank" class="mini-nav-fb"><i class="icon-fb"></i></a>
						<a href="mailto:<?php echo of_get_option('kcsite_header_email_link'); ?>" title="<?php pll_e('El. paštas');?>" class="mini-nav-email"><i class="icon-email"></i></a>
						
						<a href="<?php echo $staticVars['sitetreeUrl']; ?>" title="<?php pll_e('Svetainės medis');?>" class="mini-nav-tree"><i class="icon-tree"></i></a>
						<!-- <a href="<?php bloginfo('rss2_url'); ?>" class="mini-nav-rss">rss</a> -->
						
						<?php if(isset($_SESSION['disabled_version']) && $_SESSION['disabled_version'] == 1) { ?>
						    <a href="<?php the_permalink(); ?>?disabled_version=0" title="<?php pll_e('Įprasta versija'); ?>" class="mini-nav-disabled-people"><i class="icon-disabled-people"></i> <?php pll_e('Įprasta versija'); ?></a>
						<?php } else { ?>
							<a href="<?php the_permalink(); ?>?disabled_version=1" title="<?php pll_e('Versija neįgaliesiems'); ?>" class="mini-nav-disabled-people"><i class="icon-disabled-people"></i> <?php pll_e('Neįgaliesiems'); ?></a>
						<?php } ?>
					</ul>

					<?php get_search_form(); ?>

					<div class="blazon"></div>

				</div>
			</div>
 			<div class="main-nav-wrap">
 				<div class="wrap">
 					<a href="<?php echo pll_home_url(); ?>" id="home-link" title="<?php pll_e('Titulinis puslapis');?>"><span id="home-link-icon"></span></a>
					<?php wp_nav_menu(array('theme_location' => 'primary', 'menu_class' => 'main-nav sf-menu clearfix', 'container' => 'false', 'fallback_cb' => 'fallback_nav_menu')) ?>
 				</div>
			</div>

			<div class="content-wrap">

				<?php if(!is_front_page()){ ?>
				<div class="wrap">
				
					<?php get_template_part('template-parts/breadcrumb');?>

					<div class="col-wrap clearfix">

						<?php get_template_part('template-parts/col-left');?>

						<div class="col-right clearfix">

							<div class="contentbar">
								<?php get_template_part('template-parts/flash');?>
				<?php } ?>




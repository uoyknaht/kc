<?php
/*
Template Name: Edukacija - prisijungimas
*/

get_header(); ?>

<div class="content-page page-template-education-login">

	<div class="page-content">
		<?php 
		the_post();
		get_template_part('content', 'page'); 	
		?>
	</div><!-- .page-content -->

	<div class="entry-content">

	<?php 
	if(current_user_can('see_education_resource')){ ?>

		<div class="alert alert-info">
			<p><?php pll_e('Jūs esate prisijungę');?>.</p>
			<ul>
				<?php $pageID = kcsite_getPageByTempate('education_list.php', 'ID'); ?>
				<li><a href="<?php echo get_permalink($pageID);?>"><?php pll_e('Eiti į mokymo resursų puslapį');?></a></li>
				<li><a href="<?php echo wp_logout_url( add_query_arg('do', 'logout', get_permalink()) ); ?>"><?php pll_e('Atsijungti');?></a></li>
			</ul>
		</div>

	<?php } else if(is_user_logged_in()) {	?>
		<div class="alert">Jūs esate prisijungęs, bet jums nesuteikta teisė naudotis edukacijos resursais. Prašome susisiekti su administracija.</div>
	<?php } else {	?>

		<ul class="tabs clearfix">
			<li <?php if(!isset($_GET['do']) || $_GET['do'] == 'logout' || $_GET['do'] == 'login') echo 'class="active"';?>><a href="<?php echo get_permalink();?>"><?php pll_e('Prisijungti');?></a></li>
			<li <?php if(isset($_GET['do']) && $_GET['do'] == 'register') echo 'class="active"';?>><a href="<?php echo add_query_arg('do', 'register', get_permalink());?>"><?php pll_e('Registruotis');?></a></li>
			<!-- <li <?php if(isset($_GET['do']) && $_GET['do'] == 'forgot') echo 'class="active"';?>><a href="<?php echo add_query_arg('do', 'forgot', get_permalink());?>"><?php pll_e('Pamiršote kodą?');?></a></li> -->
		</ul>


		<?php

		// http://wordpress.stackexchange.com/questions/7134/front-end-register-form
		if(isset($_GET['do']) && $_GET['do'] == 'register'){

			if(defined('REGISTER_EDUCATOR_FAIL')) { ?>
				<div class="alert alert-error">
					<p><?php pll_e('Formoje yra klaidų. Prašome jas pataisyti ir bandyti dar kartą'); ?></p>
					<ul class="form-errors-list">
						<?php foreach(unserialize(REGISTER_EDUCATOR_FAIL) as $val) { ?>
							<li class="error"><?php echo $val;?></li>
						<?php } ?>
					</ul>
				</div>
			<?php } elseif(defined('REGISTER_EDUCATOR_SUCCESS')){ ?>
				<div class="alert alert-success">
					<?php pll_e('Registracija edukacijai sėkminga'); ?>
				</div>
			<?php } ?>

			<form method="post" action="<?php echo add_query_arg('do', 'register', $_SERVER['REQUEST_URI']); ?>" id="registerform">
				<table style="width: auto;" class="no-border">
					<?php 
					global $educatorRegisterFields;
					// print_r($educatorRegisterFields);
					foreach ($educatorRegisterFields as $key => $val) { 
						if($val['showInForm'] == true){	?>
							<tr <?php if($val['error']) echo 'class="error"'; ?>>
								<td><label for="educator-form-field-<?php echo $key;?>" class="required"><?php echo $val['label'];?></label></td>
								<?php $validationClass = 'required'; if($key == 'user_email') $validationClass .= ' email'; ?>
								<?php $value = isset($val['POSTvalue']) ? $val['POSTvalue'] : ''; ?>
								<td><input type="text" name="<?php echo $key;?>" value="<?php echo $value;?>" id="educator-form-field-<?php echo $key;?>" class="<?php echo $validationClass; ?>" /></td>
							</tr>						
						<?php }
					} ?>

					<tr>
						<td></td>
						<td><input type="submit" value="Registruotis" /></td>
					</tr>

				</table>

			</form>
			<?php

		} /*elseif(isset($_GET['do']) && $_GET['do'] == 'forgot'){

		}*/ else {

			/*if(isset($_GET['do']) && $_GET['do'] == 'login' && isset($_GET['result']) && $_GET['result'] == 'failed' ){ ?>

				<div class="alert alert-error">
					<?php pll_e('Neteisingas el. paštas ir/arba kodas');?>
				</div>

			<?php }*/

			wp_login_form(array(
				'label_username' => pll__('El. paštas'),
				'label_password' => pll__('Kodas'),
				'label_remember' => pll__('Prisiminti mane'),
			)); ?>

		<?php } ?>

		<script type="text/javascript">
			jQuery.extend(jQuery.validator.messages, {
			  required: '<?php pll_e("Privalomas laukelis") ?>',
			  email: '<?php pll_e("Neteisingas el. pašto formatas") ?>'
			})

			jQuery('#loginform').validate({
			    rules: {
					'log': {
			              required: true,
			              email: true
			        },
					'pwd': {
			              required: true
			        }        
			    },
				highlight: function(element, errorClass, validClass ) {
				     jQuery(element).closest('p').addClass('error');
				},	
				unhighlight: function(element, errorClass, validClass ) {
				   jQuery(element).closest('p').removeClass('error');
				}
			});

			jQuery('#registerform').validate({
				highlight: function(element, errorClass, validClass ) {
					jQuery(element).closest('td').addClass('error');
				},	
				unhighlight: function(element, errorClass, validClass ) {
					jQuery(element).closest('td').removeClass('error');
				}			
			});
		</script>

	<?php } // if logged in
	?>

	</div><!-- .entry-content -->

</div>

<?php get_footer(); ?>
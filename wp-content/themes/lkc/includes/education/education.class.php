<?php

/** 
 * Class Education
 *
 * Constants used in class, but not declared
 *  - capability 'see_education_resource'
 *  - CPT 'education-resource'
 *
 *
 * Other code changes across site that are needed for this to work, appart from this class
 *  - addition in breadcrumb
 *  - education page templates
 *  - capability check in content-single.php if user can see single education resource
 *
 * Hardcodes
 *  - mediaelement.min.css controls.svg => controls.png because svg not visible when watching on server (in localhost ok)
 *
 *
 * To do when putting on website
 *   - flush permalinks   no://// add above mentioned lines to htacces
 *   - init tables
 *   - init roles
 *
 *
 *
 */

class Education {

    /**
     * Education options, managed through Wordpress API
     */  
    var $options;

    /**
     * Education table, where stats are saved
     */     
    var $table;

    /**
     * Directory where files of this semi plugin are stored
     */
    var $education_dir;

    /**
     * Directory where movies are kept (direcotry name)
     */
    var $movie_dir;

    /**
     * Directory where movies are kept (path)
     */
    var $movie_dir_path;

    /**
     * Directory where movies are kept (dir)
     */    
    var $movie_dir_url;

    /**
     * Education register form fields. Used in many places
     */
    var $formFields = array(
    	'first_name' => array('metaType' => 'main', 'label' => 'Jūsų vardas', 'showInForm' => true),
    	'last_name' => array('metaType' => 'main', 'label' => 'Pavardė', 'showInForm' => true),
    	'position' => array('metaType' => 'meta', 'label' => 'Pareigos', 'showInForm' => true),
    	'user_email' => array('metaType' => 'main', 'label' => 'El. pašto adresas', 'showInForm' => true),
    	'telephone' => array('metaType' => 'meta', 'label' => 'Telefonas', 'showInForm' => true),
    	'organization' => array('metaType' => 'meta', 'label' => 'Mokykla/įstaiga/organizacija', 'showInForm' => true),
    	'organization_address' => array('metaType' => 'meta', 'label' => 'Mokyklos / įstaigos /<br> organizacijos adresas', 'showInForm' => true),
    	'organization_telephone' => array('metaType' => 'meta', 'label' => 'Mokyklos / įstaigos / <br> organizacijos telefonas', 'showInForm' => true),
    	'status' => array('metaType' => 'meta', 'label' => 'Patvirtintas', 'showInForm' => false),
    	);

    /*
     * Status when user registers but is not confirmed, or when it is deactivated
     */
    const STATUS_USER_DISABLED = 1;

    /*
     * Status when user is enabled (confirmed that it has right to see education resources)
     */
    const STATUS_USER_ENABLED = 2;


    function __construct() {

    	global $educatorRegisterFields;
    	$educatorRegisterFields = $this->formFields;

    	global $wpdb;
    	$this->table = $wpdb->prefix . 'lkc_education_resource_stats';

    	global $education_dir;
    	$this->education_dir = get_template_directory_uri().'/includes/education/';
    	$education_dir = $this->education_dir;

    	$this->movie_dir = 'edukacijos_filmai';
    	$this->movie_dir_path = WP_CONTENT_DIR . '/' . $this->movie_dir . '/';
    	$this->movie_dir_url = WP_CONTENT_URL . '/' . $this->movie_dir . '/';
    }


    /* Initial functions
    ----------------------------------- */

    function registerHooks() {
    	add_action('init', array(&$this, 'create_cpt'));
    	add_action('init', array(&$this, 'registerFrontendAssets'));
    	add_action('init', array(&$this, 'addHtaccessRules'));
    	add_action('init', array(&$this, 'singleEducationResourceHead'));

    	add_action('admin_init', array(&$this, 'init_options'));
    	add_action('admin_menu', array(&$this, 'admin_pages'));
    	add_action('admin_head', array(&$this, 'adminStyles'));

    	add_action('wp_authenticate', array(&$this, 'login_with_email_address'));
    	add_action('wp_login_failed', array(&$this, 'fix_wp_redirect_on_login_failed'));
    	add_action('template_redirect', array(&$this, 'registration'));
    	add_action('wp_login', array(&$this, 'afterLogin'));

    	add_action('wp_ajax_ajaxGetTimesRemainToDownload', array(&$this, 'ajaxGetTimesRemainToDownload'));
    	add_action('wp_ajax_nopriv_ajaxGetTimesRemainToDownload', array(&$this, 'ajaxGetTimesRemainToDownload'));

    	add_shortcode("edukacijos_failai",  array(&$this, 'shortcodeResourceList'));
    	add_shortcode("edukacijos_filmas",  array(&$this, 'shortcodeMovie'));

    	add_filter("attachment_fields_to_edit", array(&$this, 'educationAttachmentsMetaFieldsToEdit'), null, 2);
    	add_filter("attachment_fields_to_save", array(&$this, 'educationAttachmentsMetaFieldsToSave'), null , 2);

    	global $pagenow;
    	if ($pagenow == 'edit.php' && $_GET['post_type'] == 'education-resource' && $_GET['page'] == 'education-resource-stats') {
    		add_filter('admin_init', array(&$this, 'addStatsQuery'));
    	}

        // add_action('wp_logout', array(&$this, 'afterLogout')); // not tested fully, disabled
        // add_action('add_meta_boxes', array(&$this, 'addMetaBoxes'));
        // $this->addEmailFilterToStatsListPage();
        // add_action('wp_ajax_ajaxIncrementStats', array(&$this, 'ajaxIncrementStats'));
        // add_action('wp_ajax_nopriv_ajaxIncrementStats', array(&$this, 'ajaxIncrementStats'));
    }

    function registerStrings(){    
    	pll_register_string('Edukacija', 'Prašome užpildyti visus laukelius!');
    	pll_register_string('Edukacija', 'Neteisingas el. pašto formatas');
    	pll_register_string('Edukacija', 'Toks el. pašto adresas jau užregistruotas');
    	pll_register_string('Edukacija', 'Nepavyko jūsų užregistruoti. Prašome pabandyti dar kartą');
    	pll_register_string('Edukacija', 'Formoje yra klaidų. Prašome jas pataisyti ir bandyti dar kartą');
    	pll_register_string('Edukacija', 'Registracija edukacijai sėkminga');
    	pll_register_string('Edukacija', 'Atsijungti');
    	pll_register_string('Edukacija', 'Jūs prisijungėte');
    	pll_register_string('Edukacija', 'Jūs atsijungėte');
    	pll_register_string('Edukacija', 'Neteisingas el. paštas ir/arba kodas');
        // pll_register_string('Edukacija', 'Registracija sėkminga. Į jūsų nurodytą el. paštą išsiuntėme kodą, su kuriuo galėsite prisijungti. Atminkite, kad jūsų registracija dar turi patvirtinti Edukacija administratoriai');

    	pll_register_string('Edukacija', 'El. paštas');
    	pll_register_string('Edukacija', 'Kodas');
    	pll_register_string('Edukacija', 'Prisiminti mane');
    	pll_register_string('Edukacija', 'Eiti į mokymo resursų puslapį');
    	pll_register_string('Edukacija', 'Jūs esate prisijungę');
    	pll_register_string('Edukacija', 'Privalomas laukelis');
    	pll_register_string('Edukacija', 'Jūs neturite teisės matyti šios informacijos');
    	pll_register_string('Edukacija', 'Jūs jau išnaudojote atsisiuntimų limitą');
    	pll_register_string('Edukacija', 'Jūs jau išnaudojote peržiūrų limitą');
    	pll_register_string('Edukacija', 'Atsisiųsti');
    	pll_register_string('Edukacija', 'liko peržiūrų');
    	pll_register_string('Edukacija', 'Failo atsisiuntimas prasidės po');
    	pll_register_string('Edukacija', 'sek.');
        // pll_register_string('LKC', 'Liko kartų');
    }

    /* DB stats
    ----------------------------------- */

    function initRoles(){
    	add_role('educator', 'Educator', array(
    		'switch_themes' => false,
    		'edit_themes' => false,
    		'activate_plugins' => false,
    		'edit_plugins' => false,
    		'edit_users' => false,
    		'edit_files' => false,
    		'manage_options' => false,
    		'moderate_comments' => false,
    		'manage_categories' => false,
    		'manage_links' => false,
    		'upload_files' => false,
    		'import' => false,
    		'unfiltered_html' => false,
    		'edit_posts' => false,
    		'edit_others_posts' => false,
    		'edit_published_posts' => false,
    		'publish_posts' => false,
    		'edit_pages' => false,
    		'read' => false,
    		'level_true0' => false,
    		'level_9' => false,
    		'level_8' => false,
    		'level_7' => false,
    		'level_6' => false,
    		'level_5' => false,
    		'level_4' => false,
    		'level_3' => false,
    		'level_2' => false,
    		'level_true' => false,
    		'level_0' => false,
    		'edit_others_pages' => false,
    		'edit_published_pages' => false,
    		'publish_pages' => false,
    		'delete_pages' => false,
    		'delete_others_pages' => false,
    		'delete_published_pages' => false,
    		'delete_posts' => false,
    		'delete_others_posts' => false,
    		'delete_published_posts' => false,
    		'delete_private_posts' => false,
    		'edit_private_posts' => false,
    		'read_private_posts' => false,
    		'delete_private_pages' => false,
    		'edit_private_pages' => false,
    		'read_private_pages' => false,
    		'delete_users' => false,
    		'create_users' => false,
    		'unfiltered_upload' => false,
    		'edit_dashboard' => false,
    		'update_plugins' => false,
    		'delete_plugins' => false,
    		'install_plugins' => false,
    		'update_themes' => false,
    		'install_themes' => false,
    		'update_core' => false,
    		'list_users' => false,
    		'remove_users' => false,
    		'add_users' => false,
    		'promote_users' => false,
    		'edit_theme_options' => false,
    		'delete_themes' => false,
    		'export' => false,
    		));

$role = get_role( 'semi_admin' );
$role->add_cap( 'see_education_resource' );
$role = get_role( 'administrator' );
$role->add_cap( 'see_education_resource' );
}

function createTables() {
	global $wpdb;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	if( defined( 'DB_COLLATE' ) && constant( 'DB_COLLATE' ) != '' ) {
		$collate = constant( 'DB_COLLATE' );
	} else {
		$collate = constant( 'DB_CHARSET' );
	}

        // Create the table structure
	$sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}lkc_education_resource_stats ( 
		ID int(11) unsigned NOT NULL auto_increment , 
		resource_id int(11) NOT NULL, 
		type varchar(20) NOT NULL, 
		user_id int(11) NOT NULL, 
		created datetime NULL,
		PRIMARY KEY  (ID) 
		) DEFAULT CHARSET=".$collate.";

";      

dbDelta($sql);
}


function create_cpt() {

	$labels = array(
		'menu_name' => __('Mokymo resursai', 'kcsite'),
		'name' => _x(__('Mokymo resursai', 'kcsite'), 'post type general name'),
		'singular_name' => _x('Mokymo resursas', 'post type singular name'),
		'parent_item_colon' => ''
		);
	$args = array(
		'labels' => $labels,
		'public' => true,
		'exclude_from_search' =>true,
            // 'publicly_queryable' => false,
		'show_ui' => true,
		'show_in_nav_menus' => false,
            //'menu_position' => 5,
            //'menu_icon' => '',
		'hierarchical' => false,
		'supports' => array('title', 'editor', 'thumbnail'),
		'rewrite' => array('slug' => 'mokymo-resursas', 'with_front' => true),
            // 'rewrite' => true,       
            // 'query_var' => true,
		);
	register_post_type('education-resource', $args);
}

function admin_pages() {
        //add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
	add_submenu_page('edit.php?post_type=education-resource', 'Užsiregistravę vartotojai', 'Vartotojai', 'manage_options', 'education-resource-users', array(&$this, 'admin_page_users'));
	add_submenu_page('edit.php?post_type=education-resource', 'Edukacijos statistika', 'Statistika',  'manage_options', 'education-resource-stats', array(&$this, 'admin_page_stats'));
	add_submenu_page('edit.php?post_type=education-resource', 'Edukacijos nustatymai', __('Settings'),  'manage_options', 'education-resource-settings', array(&$this, 'admin_page_settings'));
}

function addHtaccessRules(){
	global $wp_rewrite;

        // disable direct download of education movies
        #RewriteCond %{REQUEST_URI} wp-content/uploads/edukacijos_filmai.*
        #RewriteRule . index.php [L]
        // jei vinoj eilutej, veikia: RewriteRule wp-content/uploads/edukacijos_filmai/sabonis.mp4 http://localhost/lkc3  

	$wp_rewrite->add_external_rule( 'wp-content/uploads/'.$this->movie_dir.'.*', '', 'bottom' );
}


    /* Options init and options page
    ----------------------------------- */

    function default_options() {
    	$options = array(
    		'user_confirmation_email_subject' => 'Registracijos patvirtinimas',
    		'user_confirmation_email_message' => '',
    		);
    	return $options;
    }

    function init_options() {
    	$options = get_option('lkc_education_options');
    	if (false === $options ) {
    		$options = $this->default_options();
    	}
    	$this->options = $options;
    	update_option('lkc_education_options', $options);
        //print_r($options);

    	register_setting('lkc_education_options', 'lkc_education_options');  
    }


    function admin_page_settings(){ ?>

    <div class="wrap">

    	<div id="icon-users" class="icon32"><br/></div>
    	<h2><?php echo __('Edukacijos nustatymai', 'lkc-newsletetr');?></h2>

    	<?php if (isset( $_GET['settings-updated'])) {
                //echo $this->flashHtml(__('Settings updated'));
    	} ?>

    	<form action="options.php" method="post">

    		<?php

                // http://wordpress.stackexchange.com/questions/26607/add-section-add-settings-section-to-a-custom-page-add-submenu-page


                //@options_group This should match the group name used in register_setting().
                //Output nonce, action, and option_page fields for a settings page. 
                //Please note that this function must be called inside of the form tag for the options page.
    		settings_fields('lkc_education_options');

                //add_settings_section( $id, $title, $callback, $page );
                //@ $page (string) (required) The menu page on which to display this section. Should match $menu_slug from Function Reference/add theme page
                //add_settings_section('main_settings', 'Main settings', array(&$this, 'main_settings_header_html'), 'lkc_newsletter');
    		add_settings_section('lkc_education_options_section1', 'Failų atsisiuntimo nustatymai', array(&$this, 'main_settings_header_html'), 'education-resource-settings');

    		add_settings_field('resource_file_allowed_download_count', __('Leidžiama kartų atsisiųsti mokymo resursų failą', 'lkc-newsletter'), array(&$this, 'resource_file_allowed_download_count_html'), 'education-resource-settings', 'lkc_education_options_section1');
    		add_settings_field('resource_movie_allowed_download_count', __('Leidžiama kartų peržiūrėti filmą', 'lkc-newsletter'), array(&$this, 'resource_movie_allowed_download_count_html'), 'education-resource-settings', 'lkc_education_options_section1');


    		add_settings_section('lkc_education_options_section2', 'Vartotojo patvirtinimo el. laiško nustatymai', array(&$this, 'main_settings_header_html'), 'education-resource-settings');

                    //should be foreach loop here, will do when make classes extend one parent class
    		add_settings_field('user_confirmation_email_subject', __('Vartotojo patvirtinimo el. laiško tema', 'lkc-newsletter'), array(&$this, 'user_confirmation_email_subject_html'), 'education-resource-settings', 'lkc_education_options_section2');
    		add_settings_field('user_confirmation_email_message', __('Vartotojo patvirtinimo el. laiško tekstas', 'lkc-newsletter'), array(&$this, 'user_confirmation_email_message_html'), 'education-resource-settings', 'lkc_education_options_section2');


                //print out all added sections with add_settings_section()
                // do_settings_sections('lkc_education_options');
                // do_settings_sections('main_settings');
    		do_settings_sections('education-resource-settings');

    		?>

    		<p class="submit">
    			<input type="submit" class="button-primary" value="<?php _e('Save') ?>" />
    			<!-- <input name="" type="submit" class="button-secondary" value="<?php _e('Restore defaults', 'lkc-newsletter'); ?>" /> -->
    		</p>

    	</form>


    </div>
    <?php
}

function main_settings_header_html(){
	?>
	<!-- <p><?php _e('Edukacijos nustatymai', 'lkc-newsletter'); ?></p> -->
	<?php
}

function resource_file_allowed_download_count_html(){       
	$options = get_option('lkc_education_options'); ?>
	<input type="number" name="lkc_education_options[resource_file_allowed_download_count]" value="<?php echo $options['resource_file_allowed_download_count']; ?>" class="regular-text" />
	<?php
}

function resource_movie_allowed_download_count_html(){       
	$options = get_option('lkc_education_options'); ?>
	<input type="number" name="lkc_education_options[resource_movie_allowed_download_count]" value="<?php echo $options['resource_movie_allowed_download_count']; ?>" class="regular-text" />
	<?php
}

function user_confirmation_email_subject_html(){       
	$options = get_option('lkc_education_options'); ?>
	<input type="text" name="lkc_education_options[user_confirmation_email_subject]" value="<?php echo $options['user_confirmation_email_subject']; ?>" class="regular-text" />
	<?php
}

function user_confirmation_email_message_html(){       
	$options = get_option('lkc_education_options'); ?>
	<textarea name="lkc_education_options[user_confirmation_email_message]" class="kcsite-textarea"><?php echo $options['user_confirmation_email_message']; ?></textarea>
	<br>
	Galite naudoti trumpinį "[kodas]". Jis bus pakeistas vartotojo kodu.
	<?php
}  


    /* Other admin pages
    ----------------------------------- */

    /**
     * Education users list and single user edit page
     */
    function admin_page_users(){

    	if(isset($_GET['do']) && $_GET['do'] == 'edit'){

            // this is single user add or edit page

    		if(isset($_GET['ID'])){
    			$action == 'edit';
    			$id = $_GET['ID'];
    			$user = get_userdata($id);
    			$title = 'Redaguoti vartotoją';
    		} else {
                // add action not done yet, will do if there will be such request from client
    			$action == 'add';
    			$title = 'Pridėti vartotoją';
    		} ?>


    		<div class="wrap">

    			<div id="icon-users" class="icon32"><br/></div>
    			<h2>
    				<?php echo $title; ?>
    				<!-- <a href="edit.php?post_type=education-resource&page=education-resource-users" class="add-new-h2"> <?php echo __('Add new');?></a> -->
    			</h2>                

    			<?php if($user == false){ ?>

    			<div class="alert alert-error alert-single">
    				Tokio vartotojo nėra
    			</div>

    			<?php } else {

    				if(isset($_POST['submit_edit_user'])){

    					unset($_POST['submit_edit_user']);
    					$errors = array();
    					foreach($_POST as $key => $val){
    						$this->formFields[$key]['POSTvalue'] = $_POST[$key];
    						if(empty($val)){
    							$this->formFields[$key]['error'] = 'Privalomas laukelis';
    							$errors[0] = 'Visi laukeliai privalomi';
    						} 
    					}
    					if(!empty($errors)){ ?>

    					<div class="alert alert-error">
    						<p><?php pll_e('Formoje yra klaidų. Prašome jas pataisyti ir bandyti dar kartą'); ?></p>
    						<ul class="form-errors-list">
    							<?php foreach($errors as $val) { ?>
    							<li class="error"><?php echo $val;?></li>
    							<?php } ?>
    						</ul>
    					</div>

    					<?php } else {

    						foreach($_POST as $key => $val){
    							if($user->$key != $val){
    								if($this->formFields[$key]['metaType'] == 'main'){
    									wp_update_user(array('ID' => $id, $key => $val));
    								} else if($this->formFields[$key]['metaType'] == 'meta'){
    									update_user_meta($id, $key, $val);                      
    								}
    							}
    						} ?>

    						<div class="alert alert-success">Vartotojo duomenys atnaujinti</div>
    						<?php } ?>


    						<?php } elseif(isset($_POST['submit_toggle_user_status'])){
    							if($user->status == self::STATUS_USER_DISABLED){
    								update_user_meta($id, 'status', self::STATUS_USER_ENABLED);
    								$user->add_cap('see_education_resource');
                            // $user->add_role('educator');
    							} else {
    								update_user_meta($id, 'status', self::STATUS_USER_DISABLED);
    								$user->remove_cap('see_education_resource');
                            // $user->remove_role('educator');
    							} ?>
    							<div class="alert alert-success">
    								Vartotojo statusas pakeistas
    							</div>
    							<?php } elseif(isset($_POST['submit_send_confirmation_to_user'])){   
    								$email = $_POST['confirmation_email_email'];
    								$subject = $_POST['confirmation_email_subject'];

    								if(isset($_POST['confirmation_email_new_password'])){
    									$newPassword = $this->generateEducatorPassword();
    									wp_set_password($newPassword, $user->ID);
    								} else {
    									$newPassword = '';
    								}
                        // $message = nl2br(str_replace('[kodas]', $newPassword, $_POST['confirmation_email_message']));
    								$message = str_replace('[kodas]', $newPassword, $_POST['confirmation_email_message']);
                        // print_r($message);exit;
                        // $headers[] = 'From: '.get_bloginfo('name').' '.get_bloginfo('admin_email');
    								$headers = 'From: '.get_bloginfo('name').' <'.get_bloginfo('admin_email').'>' . "\r\n";
    								if(wp_mail($email, $subject, $message, $headers)){ ?>
    								<div class="alert alert-success">El. laiškas išsiųstas</div>
    								<?php } else { ?>
    								<div class="alert alert-error">Nepavyko išsiųsti el. laiško</div>
    								<?php } ?>
    								<?php } ?>

    								<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

    									<table class="form-table">

    										<tr>
    											<th><label for="educator-form-field-user_login"><?php _e('Username');?> (sugeneruotas automatiškai)</label></th>
    											<td><input type="text" value="<?php echo $user->user_login?>" id="educator-form-field-user_login" class="regular-text disabled" disabled="disabled" /></td>
    										</tr> 

    										<?php 
    										foreach ($this->formFields as $key => $val) {
    											if($val['showInForm'] == true){ ?>
    											<tr <?php if(isset($val['error'])) echo 'class="error"'; ?>>
    												<th><label for="educator-form-field-<?php echo $key;?>" class="required"><?php echo $val['label'];?></label></th>
    												<?php $validationClass = 'regular-text required'; if($key == 'user_email') $validationClass .= ' email'; ?>
    												<?php $value = isset($val['POSTvalue']) ? $val['POSTvalue'] : $user->$key;; ?>
    												<?php //$value = $user->$key; ?>
    												<td>
    													<input type="text" name="<?php echo $key;?>" value="<?php echo $value;?>" id="educator-form-field-<?php echo $key;?>" class="<?php echo $validationClass; ?>" />
    													<?php if(isset($val['error'])) echo '<br><label class="error">'.$val['error'].'</label>';?>
    												</td>
    											</tr>                       
    											<?php } ?>
    											<?php } ?>

    											<tr>
    												<th><label for="educator-form-field-educator_password">Kodas</label></th>
    												<td><input type="password" name="" value="<?php echo $user->user_pass?>" id="educator-form-field-educator_password" class="regular-text disabled" disabled="disabled" /></td>
    											</tr> 

    										</table>

    										<p class="submit">
    											<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" name="submit_edit_user" />
    										</p>

    									</form>

    									<hr/>

    									<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

    										<?php if($user->status == self::STATUS_USER_ENABLED){ ?>
    										<p>
    											Vartotojas patvirtintas
    											<input type="submit" class="button-primary" value="Deaktyvuoti" name="submit_toggle_user_status" />
    										</p>

    										<hr/>

    										<p>
    											Išsiųsti vartotojui el. laiška su pranešimu apie jo patvirtinimą ir kodu.
    											Galite naudoti trumpinį "[kodas]". Jis bus pakeistas naujai sugeneruotu vartotojo kodu. <br>
    											<span style="font-size: 10px;">Šį laiško teksto šabloną galite pakeisti edukacijos 
    												<a href="edit.php?post_type=education-resource&page=education-resource-settings">nustatymuose</a>
    											</span>
    										</p>
    										<?php 
    										$options = get_option('lkc_education_options');
    										$email = $user->user_email;
    										$subject = $options['user_confirmation_email_subject'];
    										$message = $options['user_confirmation_email_message'];
    										?>
    										<p>
    											Kam: <span class="metabox-explanation">(galite pakeisti į savo kad pažiūrėti kaip laiškas atrodys)</span> <br>
    											<input type="text" class="regular-text" value="<?php echo $email;?>" name="confirmation_email_email" />
    										</p>
    										<p>
    											Tema: <br>
    											<input type="text" class="regular-text" value="<?php echo $subject;?>" name="confirmation_email_subject" />
    										</p>
    										<p>
    											Žinutė <br>
    											<textarea name="confirmation_email_message" class="kcsite-textarea"><?php echo $message;?></textarea>
    										</p>
    										<p>
    											<input type="checkbox" value="1" name="confirmation_email_new_password" />
    											&nbsp; Sugeneruoti vartotojui naują slaptažodį? <br>
    											<span class="metabox-explanation"> Nepažymėjus varnelės, trumpinys "[kodas]" neveiks ir vartotojui naujas slaptažodis nebus sugeneruotas ir nusiųstas </span>
    										</p>
    										<p>
    											<input type="submit" class="button-primary" value="Siųsti" name="submit_send_confirmation_to_user" />
    										</p>                         
    										<?php } else { ?>
    										<p>
    											Vartotojas nepatvirtintas
    											<input type="submit" class="button-primary" value="Patvirtinti"  name="submit_toggle_user_status" />
    										</p>
    										<?php } ?>

    									</form>

    									<?php } 


                //sanity_nonce(); // outputs a hidden nonce field ?>

            </div>




            <?php } else {

            // this is users list

            	require_once('education-users-list-table.class.php');

            	$wp_list_table = new EducationUsersListTable();
            	$wp_list_table->prepare_items();

            	?>
            	<div class="wrap">

            		<div id="icon-users" class="icon32"><br/></div>
            		<h2>
            			<?php echo __('Mokymų resursų vartotojai', 'lkc-newsletetr');?>
            			<!-- <a href="edit.php?post_type=newsletter2&page=lkc-newsletter-add-subscriber" class="add-new-h2"> <?php echo __('Kurti naują');?></a> -->
            		</h2>

            		<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
            		<!-- <form id="movies-filter" method="get"> -->

            		<form method="post">
            			<!-- <input type="hidden" name="page" value="ttest_list_table"> -->

            			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
            			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
            			<!-- Now we can render the completed list table -->
            			<?php $wp_list_table->search_box( 'search', 'search_id' ); ?>
            			<?php $wp_list_table->display() ?>
            		</form>

            	</div>
            	<?php

            }
        }

    /**
     * 
     */
    function isFileEducationResource($id){

    }

	/**
	 * handle filtering in stats form. 
	 * Semi hack - using redirect to add query paramaters in stats method="POST" form
	 * hooked with 'template_redirect' hook
	 */   
	function addStatsQuery() {
		$filters = array('_UserEmail', 'm', '_resourceParentID', '_resourceID');
		$redirect = false;
		$queryString = $_SERVER['QUERY_STRING'];
		foreach ($filters as $key => $val) {
			if(isset($_POST[$val])){
				$redirect = true;
				if(!empty($_POST[$val])){
					$queryString = add_query_arg(array($val => $_REQUEST[$val]), $queryString);
				} else {
					$queryString = remove_query_arg($val, $queryString);
				}
			}
		}
		if($redirect){
			wp_redirect('edit.php?' . $queryString);
			exit;
		}
	}


    /**
     * Education statistics page
     */
    function admin_page_stats(){
    	require_once('education-stats-list-table.class.php');
    	$wp_list_table = new EducationStatsListTable();
    	$wp_list_table->prepare_items();          
    	?>
    	<div class="wrap">

    		<div id="icon-users" class="icon32"><br/></div>
    		<h2><?php echo __('Edukacijos statistika', 'lkc-newsletetr');?></h2>

    		<form method="post">
    			<!-- For plugins, we also need to ensure that the form posts back to our current page -->
    			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />

    			<div style="margin-bottom: -32px; margin-top: 20px;">

    				<?php 
    				$this->months_dropdown();
    				$this->userEmailDropdownFilter();
    				$this->resourceParentDropdownFilter(); 
    				if(isset($_REQUEST['_resourceParentID']) && !empty($_REQUEST['_resourceParentID'])){
    					$this->resourceDropdownFilter($_REQUEST['_resourceParentID']); 
    				}
				// print_r($_REQUEST['_resourceParentID']);exit;

    				?>
    				<?php submit_button( __( 'Filter' ), 'button', false, false, array( 'id' => 'post-query-submit' ) ); ?>
    			</div>

    			<?php //$wp_list_table->search_box( 'search', 'search_id' ); ?>
    			<?php //$this->search_box2( 'search', 'search_id' ); ?>
    			<?php $wp_list_table->display() ?>
    		</form>

    	</div>
    	<?php
    }

	/**
	 * create dropdown filter by date in stats table
	 */ 
	function months_dropdown() {
		global $wpdb, $wp_locale;

		$months = $wpdb->get_results( $wpdb->prepare( "SELECT DISTINCT YEAR( created ) AS year, MONTH( created ) AS month FROM {$this->table} ORDER BY created DESC") );
			// FROM $wpdb->posts

		$month_count = count( $months );

		if ( !$month_count || ( 1 == $month_count && 0 == $months[0]->month ) )
			return;

		// $m = isset( $_GET['m'] ) ? (int) $_GET['m'] : 0;
		$m = isset( $_REQUEST['m'] ) ? $_REQUEST['m'] : 0;
		?>
		<select name='m'>
			<option<?php selected( $m, 0 ); ?> value='0'><?php _e( 'Data...' ); ?></option>
			<?php
			foreach ( $months as $arc_row ) {
				if ( 0 == $arc_row->year )
					continue;

				$month = zeroise( $arc_row->month, 2 );
				$year = $arc_row->year;

				printf( "<option %s value='%s'>%s</option>\n",
				// selected( $m, $year . '-' . $month, false ),
					selected( $m, $year . '-' . $month, false ),
					esc_attr( $arc_row->year . '-' . $month ),
					sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
					);
			}
			?>
		</select>
		<?php
	}


	/**
	 * create dropdown filter by user email in stats table
	 */ 
	function userEmailDropdownFilter() {
		$name = '_UserEmail';
		$the_query = new WP_User_Query(array('capability' => 'see_education_resource', 'orderby' => 'user_email'));
		$data = $the_query->get_results();

		$options = array(array('ID' => 0, 'name' => pll__('El. paštas...')));
		$i=1;
		foreach ($data as $key => $val) {
			$options[$i]['slug'] = $val->user_email; 
			$options[$i]['name'] = $val->user_email; 
			$i++;
		}
		$m = isset( $_GET[$name] ) ? $_GET[$name] : 0;
		?>
		<select id="user-email-filter2" name="<?php echo $name; ?>">
			<?php
			foreach ($options as $val) {
				printf( "<option %s value='%s'>%s</option>\n",
					selected( $m, $val['slug'], false ),
					esc_attr( $val['slug']),
					$val['name']
					);
			}
			?>
		</select>
		<?php
	}

	/**
	 * create dropdown filter by resource parent (posts that holds resource files) in stats table
	 */ 
	function resourceParentDropdownFilter() {
		$name = '_resourceParentID';
		$the_query = new WP_Query(array(
			'post_type' => 'education-resource', 
			'orderby' => 'title',
			'order' => 'asc'
			));
		$data = $the_query->posts;
		// print_r($data); exit;

		$options = array(array('ID' => 0, 'name' => pll__('Resurso įrašas...')));
		$i=1;
		foreach ($data as $key => $val) {
			$options[$i]['slug'] = $val->ID; 
			$options[$i]['name'] = $val->post_title; 
			$i++;
		}
		$m = isset( $_GET[$name] ) ? $_GET[$name] : 0;
		?>
		<select id="filter-<?php echo $name; ?>" name="<?php echo $name; ?>">
			<?php
			foreach ($options as $val) {
				printf( "<option %s value='%s'>%s</option>\n",
					selected( $m, $val['slug'], false ),
					esc_attr( $val['slug']),
					$val['name']
					);
			}
			?>
		</select>
		<?php
	}

	/**
	 * create dropdown filter by resource in stats table
	 */ 
	function resourceDropdownFilter($parent_id) {
		$name = '_resourceID';
		// $the_query = new WP_Query(array(
		// 	'post_type' => 'attachment', 
		// 	'post_status' => 'inherit', 
		// 	'meta_query' =>array(
		// 		'key' => '_education_file',
		// 		'value' => 1
		// 	)
		// ));
		$the_query = new WP_Query(array(
			'post_type' => 'attachment', 
			'post_parent' => $parent_id, 
			'post_status' => 'inherit', 
			'orderby' => 'title',
			'order' => 'asc'			
			));
		$data = $the_query->posts;
		// print_r($data); exit;

		$options = array(array('ID' => 0, 'name' => pll__('Resursas...')));
		$i=1;
		foreach ($data as $key => $val) {
			$options[$i]['slug'] = $val->ID; 
			$options[$i]['name'] = $val->post_title; 
			$i++;
		}
		$m = isset( $_GET[$name] ) ? $_GET[$name] : 0;
		?>
		<select id="filter-<?php echo $name; ?>" name="<?php echo $name; ?>">
			<?php
			foreach ($options as $val) {
				printf( "<option %s value='%s'>%s</option>\n",
					selected( $m, $val['slug'], false ),
					esc_attr( $val['slug']),
					$val['name']
					);
			}
			?>
		</select>
		<?php
	}




	/**
	 * create dropdown of fields that can be selected to make custom search
	 * prefix in options names are used, they have to be the same as in prepare_items() method
	 */ 
/*	function searchByDropdown() {
		$name = '_searchBy';
		$options = array(
			array('slug' => 'u.user_email', 'name' => pll__('El. paštas')),
			array('slug' => 'u.first_name', 'name' => pll__('Vardas')),
			array('slug' => 'u.last_name', 'name' => pll__('Pavardė')),
			array('slug' => 's.resource_id', 'name' => pll__('Pavardė')),

		);
		$m = isset( $_GET[$name] ) ? $_GET[$name] : 0;
		?>
		<select name="<?php echo $name; ?>">
		<?php
		foreach ($options as $val) {
			printf( "<option %s value='%s'>%s</option>\n",
				selected( $m, $val['slug'], false ),
				esc_attr( $val['slug']),
				$val['name']
			);
		}
		?>
		</select>
		<?php
	}
*/


    /* Utility functions
    ----------------------------------- */

    function generateEducatorPassword(){
    	return wp_generate_password(20, false);
    }



    /* Login form
    ----------------------------------- */

    function login_with_email_address($username) {
    	$user = get_user_by_email($username);
    	if(!empty($user->user_login))
    		$username = $user->user_login;
    	return $username;
    }

    function fix_wp_redirect_on_login_failed( $username ) {
       $referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?
       // if there's a valid referrer, and it's not the default log-in screen
       if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
          // wp_redirect( $referrer . '?do=login&result=failed' );  // let's append some information (login=failed) to the URL for the theme to use

       	$flash = new Messages();
       	$flash->add('e', pll__('Neteisingas el. paštas ir/arba kodas'));
       	wp_redirect(add_query_arg(array('do' => 'login', 'result' => 'failed'), $referrer));

       	exit;
       }
   }

   function afterLogin() {
        // $referrer = $_SERVER['HTTP_REFERER'];
        // if (!empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin')) {
        //     if(isset($_GET['do']) && $_GET['do'] == 'logout'){
        //         remove_query_arg('do');
        //     }
        //     add_query_arg(array('do' => 'login', 'result' => 'success'), $_SERVER['HTTP_REFERER']);
        // }

   	$flash = new Messages();
   	$flash->add('s', pll__('Jūs prisijungėte'));

   }
   function afterLogOut() {
   	$flash = new Messages();
   	$flash->add('s', pll__('Jūs atsijungėte'));
   }




    /* Register form
    ----------------------------------- */

    /**
     * Used for registration form
     */
    function registration(){

    	if(isset($_GET['do']) && $_GET['do'] == 'register'){

    		if(isset($_POST) && !empty($_POST)){

    			$userData = array();
    			$errors = array();

                // first error check (empty fields)
    			foreach($this->formFields as $key => $val){
    				if($val['showInForm'] == true){                        
    					if(empty($_POST[$key])){
    						$errors[0] = pll__('Prašome užpildyti visus privalomus laukelius!');
    						$this->formFields[$key]['error'] = 1;
    					} else {
    						$userData[$key] = array('value' => sanitize_text_field($_POST[$key]), 'metaType' => $val['metaType']);
    					}   
    					$this->formFields[$key]['POSTvalue'] = $_POST[$key];
    				}
    			}

                // if no errors, going further
                // but if some data is not valid, errors array is still populated
    			if(empty($erorrs)){

                    // generate new username
    				$count = 0;
    				$success = 0;
    				$first_name = kcsite_remove_lt_leters($userData['first_name']['value']);
    				$last_name = kcsite_remove_lt_leters($userData['last_name']['value']);
    				while($success == 0){
    					$username = $count == 0 ? $first_name . '_' . $last_name : $first_name . '_' . $last_name . '_' . $count;
                        // echo $username;exit;
    					$userData['user_login'] = array('value' => $username, 'metaType' => 'main');
    					if(username_exists($userData['user_login']['value']) === null){
    						$success = 1;
    					}
    					$count++;
    				}

    				require_once(ABSPATH.WPINC.'/registration.php');

                    //finalize main data
    				$userData['user_login']['value'] = sanitize_user($userData['user_login']['value']);
    				$userData['user_email']['value'] = apply_filters('user_registration_email', $userData['user_email']['value']);
    				$userData['role'] = array('value' => 'educator', 'metaType' => 'main');
    				$userData['user_pass'] = array('value' => $this->generateEducatorPassword(), 'metaType' => 'main');  
                    // $userData['educator_password'] = array('value' => $userData['user_pass']['value'], 'metaType' => 'meta');
    				$userData['status'] = array('value' => self::STATUS_USER_DISABLED, 'metaType' => 'meta');

    				if(!validate_username($userData['user_login']['value'])){
    					$errors[] = pll__('Nepavyko jūsų užregistruoti. Prašome pabandyti dar kartą');
    				} 
    				if((isset($userData['user_email']['value'])) && !is_email($userData['user_email']['value'])) {
    					$errors[] = pll__('Neteisingas el. pašto formatas');
    					$this->formFields['user_email']['error'] = 1;
    				} else if(email_exists($userData['user_email']['value'])) {
    					$errors[] = pll__('Toks el. pašto adresas jau užregistruotas');
    					$this->formFields['user_email']['error'] = 1;
    				}

    				if(empty($errors)){
    					$mainData = array();
    					$metaData = array();
    					foreach ($userData as $key => $val) {
    						if($val['metaType'] == 'main'){
    							$mainData[$key] = $val['value'];
    						} else {
    							$metaData[$key] = $val['value'];
    						}
    					}

    					$user_id = wp_insert_user($mainData);
    					if(is_int($user_id)){
    						foreach($metaData as $key => $val){
    							add_user_meta($user_id, $key, $val, true);                      
    						}
    						foreach ($this->formFields as $key => $val) {
    							unset($this->formFields[$key]['POSTvalue']);
    						}

    						define('REGISTER_EDUCATOR_SUCCESS', $userData['main']['user_email']);

                            // send notification to admin
    						$user_email = $mainData['user_email'];
    						$first_name = $mainData['first_name'];
    						$last_name = $mainData['last_name'];

    						$email = get_bloginfo('admin_email');
                            // $email = 'paulius.repsys@gmail.com';
    						$subject = $first_name . ' ' . $last_name . ' užsiregistravo edukacijai';
    						$message = "<p>Sveiki,</p>";
    						$message .= "<p>LKC edukacijos puslapyje užsiregistravo naujas vartotojas: <br/>" . $first_name . " " . $last_name . " (el. paštas: " .$user_email . ") </p>";
    						$message .= "<p>Patvirtinti vartotoją galite šiuo adresu: <br/>".home_url()."/wp-admin/edit.php?post_type=education-resource&page=education-resource-users&do=edit&ID=".$user_id."</p>";

    						$headers = 'From: '.get_bloginfo('name').' <'.get_bloginfo('admin_email').'>' . "\r\n";
    						add_filter( 'wp_mail_content_type', 'set_html_content_type' );
    						wp_mail($email, $subject, $message, $headers);
    						remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
    					} else {
    						$errors[] = pll__('Nepavyko jūsų užregistruoti. Prašome pabandyti dar kartą');
    					}
    				} 

    			}

                // finally check if there are errors
                // if there are, means registration was not successful, user not registered
    			if(!empty($errors)){         
    				define('REGISTER_EDUCATOR_FAIL', serialize($errors));
    			} 

	            // set fields var with POST and errors values to global var, available in view
    			global $educatorRegisterFields;
    			$educatorRegisterFields = $this->formFields;
    		}
    	}
    }


    /* Single education resource page. 
        This code could be placed in single-education-resource.php
        ----------------------------------- */

        function singleEducationResourceHead(){
        	global $post;
        	if(current_user_can('see_education_resource') && $post->post_type = 'education-resource'){

        		$current_user = wp_get_current_user();
        		$user_id = $current_user->ID;

        		if(isset($_POST['download'])){
        			$id = $_POST['download'];
                // $scrollTop = isset($_POST['scrollTop']) ? $_POST['scrollTop'] : 0;
        			$timesRemain = $this->getTimesRemainToDownload($id, $user_id, 'file');
        			if($timesRemain > 0){
        				$this->incrementStats($id, $user_id, 'file');
        				header('Refresh: 3;url='.$_SERVER['REQUEST_URI'].'?download='.$id);
                    // header('Refresh: 2;url='.$_SERVER['REQUEST_URI'].'?download='.$id.'&scrollTop='.$scrollTop);
        			}
        		}

        		if(isset($_GET['download'])){
        			$id = $_GET['download'];
        			$attachment = get_post($id);
        			$file = wp_get_attachment_url($id);
        			$filename = basename($file);

        			$timesRemain = $this->getTimesRemainToDownload($id, $user_id, 'file');
        			if($timesRemain > -1){
        				header("Cache-Control: public");
        				header("Content-Description: File Transfer");
        				header("Content-Disposition: attachment; filename=$filename");
        				header("Content-Type: " . $attachment->post_mime_type);
        				header("Content-Transfer-Encoding: binary");
        				readfile($file);
        				exit;                           
        			} else {
        				wp_redirect(remove_query_arg('download'));
        				exit;
        			}       
        		}
        	}

        	wp_enqueue_style('mediaelementjs');
        	wp_enqueue_script('mediaelementjs');    
        }



    /* DB stats
    ----------------------------------- */

    function incrementStats($resource_id, $user_id = false, $type){
    	if(!$user_id){           
    		$current_user = wp_get_current_user();
    		$user_id = $current_user->ID;
    	}
    	date_default_timezone_set('Europe/Vilnius');
    	global $wpdb;
    	$wpdb->insert(
    		$this->table, 
    		array( 
    			'resource_id' => $resource_id, 
    			'type' => $type, 
    			'user_id' => $user_id,
    			'created' => date('Y-m-d H:i:s')
    			)
    		);     
    }

    function getResourceStats($resource_id, $user_id, $type, $return = 'count'){
    	global $wpdb;

    	$stats = $wpdb->get_results($wpdb->prepare("SELECT * FROM $this->table WHERE resource_id = %d AND user_id = %d AND type = %s", $resource_id, $user_id, $type), ARRAY_A);
    	if($return == 'count'){
    		$stats = count($stats);
    	}
    	return $stats;
    }

    /**
     * return integer if there are times left, false if all downloads are used
     */
    function getTimesRemainToDownload($resource_id, $user_id = false, $type){
    	if(!$user_id){           
    		$current_user = wp_get_current_user();
    		$user_id = $current_user->ID;
    	}
    	$timesDownloaded = $this->getResourceStats($resource_id, $user_id, $type, 'count');
    	$options = get_option('lkc_education_options');
    	$timesAllowed = $options['resource_'.$type.'_allowed_download_count'];

    	if(($timesAllowed - $timesDownloaded) > 0){
    		return $timesAllowed - $timesDownloaded;
    	} else {
    		return 0;
    	}
    }


    /* Delete actions
    ----------------------------------- */

    function deleteUsers($ids){
    	global $wpdb;
    	if(!is_array($ids)) $ids = array($ids);

    	foreach($ids as $id){
    		wp_delete_user($id);
    	}

    	$ids = implode(',', $ids);
    	$wpdb->query($wpdb->prepare("DELETE FROM $this->table WHERE user_id IN($ids)"));
    }





    /* Admin CSS
    ----------------------------------- */

    function registerFrontendAssets(){
    	wp_register_style('mediaelementjs', $this->education_dir . 'assets/mediaelement/mediaelementplayer.min.css');
    	wp_register_script('mediaelementjs',$this->education_dir . 'assets/mediaelement/mediaelement-and-player.min.js', array('jquery'), 1 );
    }

    function adminStyles(){ ?>

    <style type="text/css">
    .compat-field-education_movie .field input[type="checkbox"],
    .compat-field-education_file .field input[type="checkbox"] {
    	width: auto;
    	position: relative;
    	top:7px;
    }
    .compat-item .compat-field-education_movie .label,
    .compat-item .compat-field-education_movie_path .label,
    .compat-item .compat-field-education_file .label {
    	width: 30%;
    	min-width: auto;
    	position: relative;
    	top:7px;
    }
    .compat-item .compat-field-education_movie .label span,
    .compat-item .compat-field-education_movie_path .label span,
    .compat-item .compat-field-education_file .label span {
    	padding-bottom: 5px;
    	padding-top:0;
    }
    </style>

    <?php }


    /* Attachments meta fields
    ----------------------------------- */

    function educationAttachmentsMetaFieldsToEdit($form_fields, $post) {
    	$parent = get_post($post->post_parent);

        // movie
    	if($parent != null && $parent->post_type == 'education-resource' && substr($post->post_mime_type, 0, 5) == 'image'){

    		$field_value = get_post_meta($post->ID, '_education_movie', true);
    		$checked = $field_value == 1 ? 'checked' : '';

    		$form_fields["education_movie"] = array(
    			"label" => __("Naudoti kaip filmo paveiksliuką?"),
    			"input" => "html",
                // "html" => "<input type='checkbox' value='1' {$checked} name='attachments[{$post->ID}][_education_movie]' id='attachments[{$post->ID}][_education_movie]' />",
    			"html" => "<input type='checkbox' value='1' {$checked} name='attachments[{$post->ID}][education_movie]' id='attachments[{$post->ID}][education_movie]' />",
    			"helps" => __('"Vienam įrašui (straipsniui) galite parinkti tik vieną tokį paveiksliuką)'),
    			);

    		$field_value = get_post_meta($post->ID, '_education_movie_path', true);
    		$form_fields["education_movie_path"] = array(
    			"label" => __("Nuoroda iki filmo"),
    			"input" => "text",
    			"value" => $field_value ? $field_value : '',
    			"helps" => __('Įrašykite filmo pavadinimą, kurį įkėlėte į serverio katalogą "edukacijos_filmai" '),
    			);
    	}

        // files
    	if($parent != null && $parent->post_type == 'education-resource' && substr($post->post_mime_type, 0, 5) != 'image'){

    		$field_value = get_post_meta($post->ID, '_education_file', true);
    		$checked = $field_value == 1 ? 'checked' : '';

    		$form_fields["education_file"] = array(
    			"label" => __("Mokymo resursas?"),
    			"input" => "html",
    			"html" => "<input type='checkbox' value='1' {$checked} name='attachments[{$post->ID}][education_file]' id='attachments[{$post->ID}][education_file]' />",
    			);
    	}

    	return $form_fields;
    }

    function educationAttachmentsMetaFieldsToSave($post, $attachment) {
    	if(isset($attachment['education_movie'])){
    		update_post_meta($post['ID'], '_education_movie', 1);   
    	} else {
    		update_post_meta($post['ID'], '_education_movie', 0);   
    	}
    	if(isset($attachment['education_movie_path'])){
    		update_post_meta($post['ID'], '_education_movie_path', $attachment['education_movie_path']);
    	}
    	if(isset($attachment['education_file'])){
    		update_post_meta($post['ID'], '_education_file', 1);    
    	} else {
    		update_post_meta($post['ID'], '_education_file', 0);    
    	}
    	return $post;
    }


    /* Shortcodes
    ----------------------------------- */

    function shortcodeResourceList($atts, $content = null) {

    	if(current_user_can('see_education_resource')){

    		global $post;
    		$current_user = wp_get_current_user();
    		$user_id = $current_user->ID;
    		$type = 'file';

    		$args = array('meta_key' => '_education_file', 'meta_value' => 1, 'post_type' => 'attachment', 'orderby' => 'menu_order', 'order' => 'ASC', 'post_parent' => $post->ID);           

    		$attachments = get_posts( $args );
            // print_r($attachments);exit;

    		if(!empty($attachments)){ 
    			ob_start(); 

    			if(isset($_POST['download'])){ 
    				?>
    				<div id="count-down-alert" class="alert alert-success"><?php pll_e('Failo atsisiuntimas prasidės po');?> <span id="count-down">3</span> <?php pll_e('sek.');?></div>
    				<script type="text/javascript">
    				jQuery(document).ready(function(){
    					var v = jQuery('#count-down'),
    					i = 3;
    					setInterval(function(){
    						i--;
    						if(i>0){
    							v.html(i);
    						} else if(i == 0) {
    							jQuery('#count-down-alert').css('overflow', 'hidden').animate({
    								opacity: 0,
    								height: 0,
    								marginBottom: 0,
    								paddingTop: 0,
    								paddingBottom: 0
    							}, 1000);
    						}
    					}, 1000);
    				});
    				</script>

    				<?php 
    			} ?>

    			<ul class="resource-list">
    				<?php foreach ($attachments as $key => $val) {
    					$resource_id = $val->ID;
    					$timesRemain = $this->getTimesRemainToDownload($resource_id, $user_id, $type);?>
    					<li>
    						<?php echo $val->post_title; ?> | 
    						<?php if($timesRemain > 0){ ?>
    						<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    							<input type="hidden" name="download" value="<?php echo $resource_id;?>">
    							<input type="hidden" name="scrollTop" value="">
    							<button type="submit" class="text-button"><?php pll_e('Atsisiųsti');?></button>
    						</form>
    						<?php } else {
    							$onclick = 'alert(&quot;'.pll__('Jūs jau išnaudojote atsisiuntimų limitą').'&quot;); return false;'; ?>
    							<span onclick="<?php echo $onclick;?>"><?php pll_e('Atsisiųsti');?></span>
    							<?php } ?>
    							| (<?php pll_e('liko kartų');?>: <span id="times-remain-file-id-<?php echo $resource_id;?>"><?php echo $timesRemain;?></span>)
    						</li>                
    						<?php } ?>
    					</ul>
    					<script type="text/javascript">
    					jQuery(document).ready(function(){
    						jQuery('.resource-list form .text-button').click(function(){
    							jQuery(this).prev('input[name="scrollTop"]').val(jQuery(window).scrollTop());
    							jQuery(this).closest('form').submit();
    							return false;
    						});
    					});
    					</script>

    					<?php
    					$output = ob_get_contents();
    					ob_end_clean();
    				} else {
    					$output = '';
    				}

    				return do_shortcode($output);   
    			}
    		}

    		function shortcodeMovie($atts, $content = null) {

    			if(current_user_can('see_education_resource')){

    				extract(shortcode_atts(array(
    					"width" => '520',
    					"height" => '320',
    					), $atts));

    				global $post;
            // $education = new Education();
    				$current_user = wp_get_current_user();
    				$user_id = $current_user->ID;
    				$type = 'movie';

    				$args = array('meta_key' => '_education_movie', 'meta_value' => 1, 'post_type' => 'attachment', 'orderby' => 'menu_order', 'order' => 'ASC', 'post_parent' => $post->ID);           
    				$image = get_posts($args);

    				if($image == null){
    					$output = current_user_can('manage_options') ? '<p>Neparinktas joks filmo paveikslėlis</p>' : '';
    				} else if(count($image) > 1) {
    					$output = current_user_can('manage_options') ? '<p>Filmui parinktas daugiau nei vienas paveiksėlis</p>' : '';
    				} else {
    					$image = $image[0];
    					$resource_id = $image->ID;
    					$movie_path = get_post_meta($resource_id, '_education_movie_path', true);
                // echo $this->movie_dir . $movie_path;
    					if(empty($movie_path)){
    						$output = current_user_can('manage_options') ? '<p>Nenurodytas filmo failo pavadinimas</p>' : '';
    					} else if(!file_exists($this->movie_dir_path . $movie_path)){
    						$output = current_user_can('manage_options') ? '<p>Pateikta nuoroda iki filmo failo klaidinga. Filmas nerastas</p>' : '';           
    					} else {

    						ob_start();
    						$timesRemain = $this->getTimesRemainToDownload($resource_id, $user_id, 'movie'); ?>

    						<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
    							<div id="movie-frame">

    								<?php if(isset($_POST['movie_trigger']) && $timesRemain > 0){
    									$this->incrementStats($resource_id, $user_id, $type);
    									$timesRemain--; ?>
    									<video id="movie-frame-video" src="<?php echo $this->movie_dir_url . $movie_path;?>" width="<?php echo $width;?>" height="<?php echo $height;?>"></video>

    									<script type="text/javascript">
                                    <!--//--><![CDATA[//><!--  
                                    // jQuery(document).ready(function(){
                                    	var frame = jQuery('#movie-frame-video');

                                                                                
                                        frame.mediaelementplayer({
                                            /*@cc_on
                                            @if (@_jscript_version == 9)
                                            @end
                                            @*/
                                                        // mode: 'shim',
                                    		success: function(mediaElement, domObject) {
                                    			mediaElement.play();
                                    		}                             
                                    	});
                                    	var newScrollTop = frame.offset().top + (frame.height()/2) - (jQuery(window).height()/2);
                                    	jQuery('html, body').animate({ scrollTop : newScrollTop }, 500);
                                    // })                     
                                    //--><!]]>
                                    </script>                 
                                    <?php } else if(isset($_POST['movie_trigger'])) { // can happen if refreshed page when watching movie last allowed time ?>
                                    <div class="alert alert-error"><?php pll_e('Jūs jau išnaudojote peržiūrų limitą'); ?></div>
                            <?php } else { // no $_POST, show image with button to trigger movie view
                                // the_post_thumbnail($post->ID, 'medium-cropped');
                            	echo wp_get_attachment_image($resource_id, 'medium');
                                // echo wp_get_attachment_image($resource_id, array(100, 300));

                            	if($timesRemain > 0){ ?>
                            	<button type="submit" id="movie-trigger" name="movie_trigger">
                            		<span class="a"><?php pll_e('Žiūrėti filmą');?></span>
                            	</button>
                            	<?php } else { 
                            		$onclick = 'alert(&quot;'.pll__('Jūs jau išnaudojote peržiūrų limitą').'&quot;); return false;';?>
                            		<div id="movie-trigger" onclick="<?php echo $onclick;?>">
                            			<span class="a"><?php pll_e('Žiūrėti filmą');?></span>
                            		</div>
                            		<?php } ?>

                            		<?php } ?>
                            	</div>
                                <?php // IE7 and IE8 starts showing film only when it is fully loaded. There is no such problem if testing locally  ?>
                                <?php if(isset($_POST['movie_trigger']) && $timesRemain > 0){ ?>
                                <!--[if lte IE 8]>
                                    <p style="font-size: 11px; color: #999; text-align: center; padding-bottom: 0;"><?php pll_e('Filmas bus pradėtas rodyti, kai įsikels į video grotuvą. Jei galite, prašome naudotis kita interneto naršykle');?></p>
                                <![endif]-->
                                <?php } ?>
                                <p class="times-remain-under">(<?php pll_e('liko peržiūrų');?>: <?php echo $timesRemain;?>)</p>
                            </form>

                            <?php
                            $output = ob_get_contents();
                            ob_end_clean();
                        }
                    } 

                    return do_shortcode($output);   
                }
            }



    /* Meta boxes
    ----------------------------------- */
/*
    function addMetaBoxes(){
        // add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
        add_meta_box('education-resource-stats', __( 'Edukacijos resursų, priklausančių šiam įrašui, statistika', 'lkc-newsletter' ), array(&$this, 'metaBoxHtml'), 'education-resource');
    }
*/
/*
    function metaBoxHtml(){ 

        global $post, $wpdb;

        // files
        $args = array('meta_key' => '_education_file', 'meta_value' => 1, 'post_type' => 'attachment', 'orderby' => 'menu_order', 'order' => 'ASC', 'post_parent' => $post->ID);           
        $files = get_posts($args);
        $filesStats = array();
        foreach($files as $key => $val){
            $files[$key]->downloadsCount = count($wpdb->get_results("SELECT * FROM $this->table WHERE resource_id = {$val->ID} ", ARRAY_A));
        }

        // movie
        $args = array('meta_key' => '_education_movie', 'meta_value' => 1, 'post_type' => 'attachment', 'orderby' => 'menu_order', 'order' => 'ASC', 'post_parent' => $post->ID);           
        $movies = get_posts($args);
        foreach($movies as $key => $val){
            $movies[$key]->watchCount = count($wpdb->get_results("SELECT * FROM $this->table WHERE resource_id = {$val->ID} ", ARRAY_A));
        }
        ?>

        <table>
            <tr>
                <td>Failo pavadinimas</td>
                <td>Atsisiuntimų kiekis</td>
            </tr>
            <?php foreach ($files as $key => $val) { ?>
            <tr>
                <td><?php echo $val->post_title;?></td>
                <td><?php echo $val->downloadsCount;?></td>
            </tr>
            <?php } ?>
        </table>

    <?php }

*/
}
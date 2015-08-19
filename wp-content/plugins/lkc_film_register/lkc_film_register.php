<?php
/*
Plugin Name: LKC film register
Plugin URI: 
Description: 
Version: 1.0
Author: Infoface
Author URI: http://infoface.lt
License: 
*/

/** 
 * Class FilmRegister
 *
 * Other code changes across site that are needed for this to work, appart from this class
 *  - addition in breadcrumb
 *
 * Things that are taken from theme, so would not work with other themes
 *  - validation messages from jsVars
 *  - styles in app.css
 */

class FilmRegister {

    /**
     * Film register options, managed through Wordpress API  // not used for now
     */  
    var $options;

    /**
     * Film custom post type slug
     */     
    var $cptSlug = 'film';

    /**
     * Plugin slug
     */     
    var $pluginName = 'lkc_film_register';

    /**
     * 
     */     
    var $pluginUrl;

    /**
     * CPT film fields array
     */
    var $fields;

    /**
     * Various data and data methods received from FilmRegisterData class
     */
    var $data;

    /**
     * Table from old database, from which films were imported
     */
    var $oldFilmTable = 'film_register_old_films_table';
    
    /**
     * Table from old database, from which films licenses info were imported
     */
    var $oldFilmLicensesTable = 'film_register_old_licenses_table';
        
    /**
     * Table from old database, from which films countries info were imported
     */
    var $oldFilmCountriesTable = 'film_register_old_countries_table';
    
    /**
     * Meta field which shows that this film was imported, not added via WP admin
     */
    var $importedMetaField = '_lkc_film_imported';
    
    /**
     * Meta field which shows that this film's country was hardcoded when importing, could not be mapped with countries array
     */
    var $hardcodedCountryMetaField = '_lkc_film_country_hardcoded';


    function __construct() {
        include_once('film-register-data.class.php');
        $this->data = new FilmRegisterData();
        $this->fields = $this->data->fields;

        global $g_filmRegisterFields;
        $g_filmRegisterFields = $this->fields;
        $this->pluginUrl = plugins_url( null, __FILE__ );
        // $this->pluginUrl = plugins_url( 'images/wordpress.png' , __FILE__ );
        // $this->pluginUrl =  WP_PLUGIN_URL . plugin_basename(__FILE__);
    }

    /**
     * Method to start plugin
     */
    function init(){
      $this->registerHooks();
    }

    /**
     * Need to populate $this->fields data after init hook
     * only then custom taxonomies are available
     */
    function afterInit(){
      $this->fields = $this->data->populateDynamicData();
      global $g_filmRegisterFields;
      $g_filmRegisterFields = $this->fields;
    }

    function registerHooks() {
        add_action('init', array(&$this, 'create_cpt'));
        add_action('init', array(&$this, 'metaboxSetup'));
        
        add_action('wp_loaded', array(&$this, 'afterInit'));
        add_action('admin_init', array(&$this, 'enqueueBackendAssets'));
        // add_action('admin_init', array(&$this, 'init_options'));
        add_action('admin_menu', array(&$this, 'admin_pages'));
        add_action('template_redirect', array(&$this, 'filmRegisterRedirect'));
        // add_action('init', array(&$this, 'handleFilmArchiveStatus'));
        // add_action('pre_get_posts', array(&$this, 'handleFilmArchiveStatus'));
        add_shortcode("film_register_search",  array(&$this, 'shortcodeFilmRegisterSearch')); 
        add_filter("film_register_search",  array(&$this, 'shortcodeFilmRegisterSearch')); 
        add_filter('save_post', array(&$this, 'managePostArchivedStatus'));
        add_filter('display_post_states', array(&$this, 'postStateArchived'));

        // need to add this, but breaks plugin. Need to investigate
        // if(!is_admin()){
        //     add_action('posts_clauses', array(&$this, 'posts_clauses_with_tax'), 10, 2);
        // }

        global $pagenow;
        if ($pagenow == 'edit.php' &&  $_GET['post_type'] == 'film') {
            add_filter('request', array(&$this, 'filterByArchiveRequest'));
            add_filter('restrict_manage_posts', array(&$this, 'addFilterByArchive'));
        }

        add_action('wp_ajax_filmRegisterSearchAutocompleteRequest', array(&$this, 'filmRegisterSearchAutocompleteResponse'));
        add_action( 'wp_ajax_nopriv_filmRegisterSearchAutocompleteRequest', array(&$this, 'filmRegisterSearchAutocompleteResponse'));

        add_action('wp_ajax_getNewFieldRowRequest', array(&$this, 'getNewFieldRowResponse'));
        add_action( 'wp_ajax_nopriv_getNewFieldRowRequest', array(&$this, 'getNewFieldRowResponse'));
    }


    function registerStrings(){    
        pll_register_string('Filmų registras', 'nuo');
        pll_register_string('Filmų registras', 'iki');
        pll_register_string('Filmų registras', 'Paieškos rezultatai');
        pll_register_string('Filmų registras', 'Pagal pasirinktus paieškos kriterijus filmų nerasta');
        pll_register_string('Filmų registras', 'Ieškoti');
        pll_register_string('Filmų registras', 'Prašome įvesti bent 1 paieškos kriterijų');
        pll_register_string('Filmų registras', 'identifikavimo kodą');
        pll_register_string('Filmų registras', 'pagaminimo datą');
        pll_register_string('Filmų registras', 'pavadinimą');
        pll_register_string('Filmų registras', 'rikiuoti pagal');
        pll_register_string('Filmų registras', 'Rikiuoti didėjimo tvarka');
        pll_register_string('Filmų registras', 'Rikiuoti mažėjimo tvarka');
        pll_register_string('Filmų registras', 'Įrašykite');
        pll_register_string('Filmų registras', 'Pasirinkite');
        pll_register_string('Filmų registras', 'Pridėti');
        pll_register_string('Filmų registras', 'Pašalinti');
        pll_register_string('Filmų registras', 'Grįžti į filmų paiešką');
        pll_register_string('Filmų registras', 'rodyti');
        pll_register_string('Filmų registras', 'visus');
        pll_register_string('Filmų registras', 'Filmas nepaskelbtas, negaliojantis arba išregistruotas');
    }

    function create_cpt() {

        $labels = array(
            'menu_name' => __('Filmų registras', 'kcsite'),
            'name' => _x(__('Filmų registras', 'kcsite'), 'post type general name'),
            'singular_name' => _x('Filmas', 'post type singular name'),
            'parent_item_colon' => ''
        );
         $args = array(
            'labels' => $labels,
            'public' => true,
            'exclude_from_search' => true,
            // 'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_nav_menus' => false,
            //'menu_position' => 5,
            //'menu_icon' => '',
            'hierarchical' => false,
            'supports' => array('title', 'editor'/*, 'thumbnail'*/),
            'rewrite' => array('slug' => 'filmu-registras2', 'with_front' => true),
            'has_archive' => true, 
            // 'rewrite' => true,       
            // 'query_var' => true,
        );
        register_post_type($this->cptSlug, $args);

        register_taxonomy(
          $this->fields['type']['taxonomySlug'], 
          $this->cptSlug,
          array(
            'label' =>  'Filmų rūšys', 
            'labels' => array(
              'name' => 'Filmų rūšys',
              'singular_name' => 'Filmo rūšis',
              'menu_name' => 'Filmų rūšys',
              'all_items' => 'Visos rūšys',
              'edit_item' => 'Redaguoti rūšį',
              'view_item' => 'Žiūrėti rūšį',
              'update_item' => 'Atnaujinti rūšį',
              'add_new_item' => 'Kurti naują rūšį',
              'new_item_name' => 'Naujos rūšies pavadinimas',
              'parent_item' => 'Tėvinė rūšis',
              'parent_item_colon' => 'Tėvinė rūšis:',
              'search_items' => 'Ieškoti rūšyse',
            ),
            'hierarchical' => true, 
            // 'query_var' => 'film-type'
          )
        );

        register_taxonomy(
          $this->fields['genre']['taxonomySlug'], 
          // 'film-genre', 
          $this->cptSlug,
          array(
            'label' =>  'Filmų žanrai', 
            'labels' => array(
              'name' => 'Filmų žanrai',
              'singular_name' => 'Filmo žanras',
              'menu_name' => 'Filmų žanrai',
              'all_items' => 'Visi žanrai',
              'edit_item' => 'Redaguoti žanrą',
              'view_item' => 'Žiūrėti žanrą',
              'update_item' => 'Atnaujinti žanrą',
              'add_new_item' => 'Kurti naują žanrą',
              'new_item_name' => 'Naujos žanro pavadinimas',
              'parent_item' => 'Tėvinė žanras',
              'parent_item_colon' => 'Tėvinė žanras:',
              'search_items' => 'Ieškoti žanruose',
            ),
            'hierarchical' => true, 
            // 'query_var' => 'film-genre'
          )
        );
        register_taxonomy(
          $this->fields['index']['taxonomySlug'], 
          $this->cptSlug,
          array(
            'label' =>  'Filmų indeksai', 
            'labels' => array(
              'name' => 'Filmų indeksai',
              'singular_name' => 'Filmo indeksas',
              'menu_name' => 'Filmų indeksai',
              'all_items' => 'Visi indeksai',
              'edit_item' => 'Redaguoti indeksą',
              'view_item' => 'Žiūrėti indeksą',
              'update_item' => 'Atnaujinti indeksą',
              'add_new_item' => 'Kurti naują indeksą',
              'new_item_name' => 'Naujo indekso pavadinimas',
              'parent_item' => 'Tėvinis indeksas',
              'parent_item_colon' => 'Tėvinis indeksas:',
              'search_items' => 'Ieškoti indeksuose',
            ),
            'hierarchical' => true, 
          )
        );  
        register_taxonomy(
          $this->fields['license_type']['taxonomySlug'], 
          $this->cptSlug,
          array(
            'label' =>  'Licenzijų rūšys', 
            'labels' => array(
              'name' => 'Licenzijų rūšys',
              'singular_name' => 'Licenzijų rūšis',
              'menu_name' => 'Licenzijų rūšys',
              'all_items' => 'Visos licenzijų rūšys',
              'edit_item' => 'Redaguoti rūšį',
              'view_item' => 'Žiūrėti rūšį',
              'update_item' => 'Atnaujinti rūšį',
              'add_new_item' => 'Kurti naują rūšį',
              'new_item_name' => 'Naujos rūšies pavadinimas',
              'parent_item' => 'Tėvinė rūšis',
              'parent_item_colon' => 'Tėvinė rūšis:',
              'search_items' => 'Ieškoti rūšyse',
            ),
            'hierarchical' => true, 
          )
        );
        register_taxonomy(
          $this->fields['rights_given']['taxonomySlug'], 
          $this->cptSlug,
          array(
            'label' =>  'Suteiktos teisės', 
            'labels' => array(
              'name' => 'Suteiktos teisės',
              'singular_name' => 'Suteiktos teisė',
              'menu_name' => 'Suteiktos teisės',
              'all_items' => 'Visos suteiktos teisės',
              'edit_item' => 'Redaguoti teisę',
              'view_item' => 'Žiūrėti teisę',
              'update_item' => 'Atnaujinti teisę',
              'add_new_item' => 'Kurti naują teisę',
              'new_item_name' => 'Naujos teisės pavadinimas',
              'parent_item' => 'Tėvinė teisė',
              'parent_item_colon' => 'Tėvinė teisė:',
              'search_items' => 'Ieškoti teisėse',
            ),
            'hierarchical' => true, 
          )
        );
        // register_taxonomy(
        //   $this->fields['format']['taxonomySlug'], 
        //   $this->cptSlug,
        //   array(
        //     'label' =>  'Filmų formatai', 
        //     'labels' => array(
        //       'name' => 'Filmų formatai',
        //       'singular_name' => 'Filmo formatas',
        //       'menu_name' => 'Filmų formatai',
        //       'all_items' => 'Visi formatai',
        //       'edit_item' => 'Redaguoti formatą',
        //       'view_item' => 'Žiūrėti formatą',
        //       'update_item' => 'Atnaujinti formatą',
        //       'add_new_item' => 'Kurti naują formatą',
        //       'new_item_name' => 'Naujas formatas',
        //       'parent_item' => 'Tėvinis formatas',
        //       'parent_item_colon' => 'Tėvinis formatas:',
        //       'search_items' => 'Ieškoti formatuose',
        //     ),
        //     'hierarchical' => true, 
        //   )
        // );

    }

    function admin_pages() {
        //add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );    
        // add_submenu_page('edit.php?post_type='.$this->cptSlug, 'Filmų registro archyvas', 'Archyvas', 'manage_options', 'film-register-archive', array(&$this, 'admin_page_film_register_archive'));
        // add_submenu_page('edit.php?post_type='.$this->cptSlug, 'Filmų registro archyvas', 'Archyvas', 'manage_options', add_query_arg(array('archived' => 1), 'edit.php?post_type='.$this->cptSlug));
        // add_submenu_page('edit.php?post_type='.$this->cptSlug, 'Filmų registro archyvas', 'Archyvas', 'manage_options', 'edit.php?post_type='.$this->cptSlug);
        // add_submenu_page('edit.php?post_type=education-resource', 'Edukacijos statistika', 'Statistika',  'manage_options', 'education-resource-stats', array(&$this, 'admin_page_stats'));
        // add_submenu_page('edit.php?post_type='.$this->cptSlug, 'Filmų registro nustatymai', __('Settings'),  'manage_options', 'film-register-settings', array(&$this, 'admin_page_settings'));
        add_submenu_page('edit.php?post_type='.$this->cptSlug, 'Hackinimas', __('Hackinimas'),  'administrator', 'import', array(&$this, 'admin_page_import'));
    }


    /* Meta fields, implemented with WPAlchemy class
    ----------------------------------- */

    function metaboxSetup(){ 
      if(!class_exists('WPAlchemy_MetaBox')){
        include_once 'vendors/wpalchemy/MetaBox.php';
      }

      global $lkc_film_metabox;
      $lkc_film_metabox = new WPAlchemy_MetaBox(array(
        'id' => '_lkc_film_meta',
        'title' => 'Filmo duomenys',
        'template' => dirname(__FILE__).'/metaboxes/lkc-film-meta.php',
        'types' => array($this->cptSlug),
        'mode' => WPALCHEMY_MODE_EXTRACT,
        'prefix' => '_lkc_film_',
        'save_filter' => array(&$this, 'interruptPostSave')
      ));
    }

    /* Exporting films to documents
    ----------------------------------- */
    
    function handleExport($post_id) {

        if(isset($_POST['filmExportType'])){

            $type = $_POST['filmExportType'];

            $file = plugin_dir_path( __FILE__ ) . 'document_templates/' . $type . '.docx';
            $postData = get_post($post_id, ARRAY_A);
            $slug = $postData['post_name'];
            $fileOut = plugin_dir_path( __FILE__ ) . 'document_templates/tmp/' . $type . '-' . $slug . '.docx';

            $filename = basename($fileOut);

            if(!file_exists($file)){
                return;
            }

            require_once( plugin_dir_path( __FILE__ ) . 'vendors/phpword/PHPWord.php');

            $PHPWord = new PHPWord();

            $document = $PHPWord->loadTemplate($file);

            $fields = array(
                'register_date',
                'identity_code',
                'title_orig',
                'title',
                'first_record_producer',
                'application_provider',
                'country',
                'produce_date',
                'director',
                'type',
                'genre',
                'index',
                'duration',
            );

            if($type == 'filmo-indekso-pazyma'){
            } else if ($type == 'iregistruoto-filmo-israsas'){
                $allRights = get_terms(array('film-right'), array('fields' => 'all', 'hide_empty' => false));
                $filmRights = wp_get_post_terms($post_id, 'film-right', array('fields' => 'all'));
            }

            ini_set('default_charset', 'utf-8');
            foreach ($fields as $val) {
                $value = $this->outputField($val);
                if ($val == 'title_orig' && $value == '') {
                    $value = $this->outputField('title');
                }
                $document->setValue($val, $value);
            }

            if(isset($allRights)){

                $table = '<w:tbl>';
                $table .= '<w:tblPr>';
                    $table .= '<w:tblW w:w = "5000" w:type="pct"/>';
                    //$table .= '<w:tblCellSpacing w:w="50" w:type="dxa"/>';
                    // $table .= '<w:tblCellPadding w:w="250" w:type="dxa"/>';

                    $table .= '<w:tblCellMar>
                    <w:top w:w="50" w:type="dxa"/>
                    <w:start w:w="50" w:type="dxa"/>
                    <w:bottom w:w="50" w:type="dxa"/>
                    <w:end w:w="50" w:type="dxa"/>
                    </w:tblCellMar>';

                    $table .= '<w:tblBorders>
                    <w:top w:val="single" w:sz="1" w:space="0" w:color="cccccc" />
                    <w:bottom w:val="single" w:sz="1" w:space="0" w:color="cccccc" />
                    <w:start w:val="single" w:sz="1" w:space="0" w:color="cccccc" />
                    <w:end w:val="single" w:sz="1" w:space="0" w:color="cccccc" />
                    <w:insideH w:val="single" w:sz="1" w:space="0" w:color="cccccc" />
                    <w:insideV w:val="single" w:sz="1" w:space="0" w:color="cccccc" />
                    </w:tblBorders>';
                $table .= '</w:tblPr>';

                // foreach($allRights as $key => $val){
                //     $table .= '<w:tr>';
                //         $table .= '<w:tc><w:p><w:r><w:t>'; 
                //             $table .= $val->name; 
                //         $table .= '</w:t></w:r></w:p></w:tc>'; 
                //         $table .= '<w:tc><w:p><w:r><w:t>';
                //             $result = 'Ne';
                //             foreach ($filmRights as $key2 => $val2) {
                //                 if($val->term_id == $val2->term_id){
                //                     $result = 'Taip';
                //                     break;
                //                 }
                //             }
                //             $table .= $result;
                //         $table .= '</w:t></w:r></w:p></w:tc>';
                //     $table .= '</w:tr>';
                // } 
                foreach($allRights as $key => $val){
                    foreach ($filmRights as $key2 => $val2) {
                        if($val->term_id == $val2->term_id){
                            $table .= '<w:tr>';
                                $table .= '<w:tc><w:p><w:r><w:t>'; 
                                    $table .= $val->name; 
                                $table .= '</w:t></w:r></w:p></w:tc>'; 
                                $table .= '<w:tc><w:p><w:r><w:t>';
                                    $table .= 'Taip';
                                $table .= '</w:t></w:r></w:p></w:tc>';
                            $table .= '</w:tr>';
                            break;
                        }
                    }
                } 
                $table .= '</w:tbl>'; 


                $document->setValue('rights', $table);
            }

            $document->save($fileOut);
            unset($document);        

            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-Disposition: attachment; filename=$filename");
            header('Content-Type: application/vnd.ms-word; charset=utf-8');
            header("Content-Transfer-Encoding: binary");
            readfile($fileOut);
            unlink($fileOut);
            exit;   
        }
    }

    /* Templates
    ----------------------------------- */

    // http://stackoverflow.com/questions/4647604/wp-use-file-in-plugin-directory-as-custom-page-template
    function filmRegisterRedirect(){ 
      global $wp;
      $plugindir = dirname( __FILE__ );

      if ($wp->query_vars["post_type"] == $this->cptSlug) {
        $templatefilename = 'single-'.$this->cptSlug.'.php';
        if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
            $return_template = TEMPLATEPATH . '/' . $templatefilename;
        } else {
            $return_template = $plugindir . '/views/templates/' . $templatefilename;
        }
        $this->do_redirect($return_template);
 
      } /*elseif ($wp->query_vars["pagename"] == 'somepagename') {
        $templatefilename = 'page-somepagename.php';
        if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
            $return_template = TEMPLATEPATH . '/' . $templatefilename;
        } else {
            $return_template = $plugindir . '/themefiles/' . $templatefilename;
        }
        $this->do_redirect($return_template);
      }*/
    }

    function do_redirect($url) {
        global $post, $wp_query;
        if (have_posts()) {
            include($url);
            die();
        } else {
            $wp_query->is_404 = true;
        }
    }


    /* Assets
    ----------------------------------- */

    function enqueueBackendAssets(){

      if (is_admin()){
        wp_register_style('jquery-ui', $this->pluginUrl . '/vendors/jquery-ui.custom/css/smoothness/jquery-ui.custom.min.css');  
        wp_enqueue_style('jquery-ui'); 

        wp_register_script('jquery-ui', $this->pluginUrl . '/vendors/jquery-ui.custom/js/jquery-ui.custom.min.js',array('jquery'));  
        wp_enqueue_script('jquery-ui'); 
       
        // does'nt work
        // wp_enqueue_script('jquery-ui-core'); 
        // wp_enqueue_script('jquery-ui-widget'); 
        // wp_enqueue_script('jquery-ui-datepicker'); 


        wp_register_script('jquery-ui-datepicker-lt', $this->pluginUrl . '/vendors/jquery-ui.custom/js/jquery.ui.datepicker-lt.js',array('jquery-ui'));  
        wp_enqueue_script('jquery-ui-datepicker-lt'); 

        wp_register_style('jquery-chosen', $this->pluginUrl . '/vendors/chosen/chosen.min.css');  
        wp_enqueue_style('jquery-chosen'); 
        
        wp_register_script('jquery-chosen', $this->pluginUrl . '/vendors/chosen/chosen.jquery.min.js', array('jquery'));  
        wp_enqueue_script('jquery-chosen'); 


        wp_enqueue_script('lkc-film-register-backend', $this->pluginUrl . '/assets/js/backend.js', array('jquery-ui'));  
      } else {
        wp_enqueue_style('lkc-film-register-frontend', $this->pluginUrl . '/assets/css/frontend.css');  

      }
    }


    /* Frontend views
    ----------------------------------- */

    function refererBtn($refererPageId = null){
      if($refererPageId == null){
        global $staticVars;
        $refererPageId = $staticVars['filmRegisterSearchPageId'];
      }

      $refererUrl = get_permalink($refererPageId);
      $wpRefererUrl = wp_get_referer();
      $finalRefererUrl = strpos($wpRefererUrl, $refererUrl) === false ? $refererUrl : $wpRefererUrl ;

      return '<a href="'.$finalRefererUrl.'" class="btn-block btn-referer">'.pll__('Grįžti į filmų paiešką').'</a>';
    }

    function getHtmlRemoveRowIcon(){
      ob_start(); ?>
      <a href="#" title="<?php pll_e('Pašalinti');?>" data-name="<?php echo $key;?>" class="remove-row-btn"><i class="icon-x"></i></a>
      <?php return ob_get_clean();
    }

    /**
     * Outputs field's value, $post needs to be set
     */
    function outputField($key){
      global $post;
      
      if($this->fields[$key]['metaType'] == 'main'){
        if($key == 'title'){
          $key = 'post_title';
        } 
        $output = $post->$key;
        //echo $output;
        // echo get_the_title();
        // echo ' ...';
        // exit;
      } elseif($this->fields[$key]['metaType'] == 'meta'){
        $output = get_post_meta($post->ID, '_lkc_film_'.$key, true);
        if(is_array($output)) {
            $stringifiedOutput = '';
            foreach ($output as $a => $b) {
                $stringifiedOutput .=  $this->fields[$key]['dropdownOptions'][$b];
                if($a+1 != count($output)) $stringifiedOutput .= ', ';
            }
            $output = $stringifiedOutput;
        } elseif($this->fields[$key]['inputType'] == 'date' && $output != '' && !is_array($output) && strlen($output) > 10){
            $output = date('Y-m-d', strtotime($output));
        }
      } elseif($this->fields[$key]['metaType'] == 'taxonomy'){
        $key = $this->fields[$key]['taxonomySlug'];
        $terms = wp_get_post_terms( $post->ID, $key, $args );
        $output = '';
        foreach ($terms as $a => $b) {
          $output .= $b->name;
          if($a+1 != count($terms)) $output .= ', ';
        }
      }
      if(!is_array($output)) $output = ucfirst($output);
      return $output;
    }


    /**
     * outputs searh form single input field html
     * @param $options array of key values html attributes of input field, 'name' attribute is required
     */
    function sihf_output($options = array()){

      if(!is_array($options) || !isset($options['name'])) return;
      
      $key = $options['name'];
      $type = $this->fields[$key]['searchInputHtmlFunction'];

      $default = array(
        'placeholder' => pll__('Įrašykite'), 
        'placeholderFrom' => pll__('nuo'), 
        'placeholderTo' => pll__('iki'),
      );
      $options = array_merge($default, $options);

      if(!isset($options['class'])) {
        $options['class'] = '';
      }
      if(!empty($this->fields[$key]['class'])){
        $options['class'] .= $this->fields[$key]['class'];
      }

      if($this->fields[$key]['searchInputHtmlFunction'] == 'sihf_text_between'){
        $options['nameFrom'] = $key.'_from';
        $options['nameTo'] = $key.'_to';
        $options['class'] .= ' input-mini';
      }

      if($this->fields[$key]['searchInputHtmlFunction'] == 'sihf_text_between_related_with_other_field'){
        if($this->fields[$key]['searchRelatedFieldFirst'] == true){
            $options['nameFrom'] = $key;
            $options['nameTo'] = $this->fields[$key]['searchRelatedFieldKey'];
        }
        $options['class'] .= ' input-mini';
      }
      extract($options);


      ob_start();
      if($type == 'sihf_text'){ ?>
        <input type="text" name="<?php echo $name;?>" value="<?php echo $_GET[$name]; ?>" class="<?php echo $class; ?>" placeholder="<?php echo $placeholder; ?>" />
      <?php } elseif($type == 'sihf_text_between'){ ?>
        <input type="text" name="<?php echo $nameFrom;?>" value="<?php echo $_GET[$nameFrom]; ?>" placeholder="<?php echo $placeholderFrom; ?>" class="<?php echo $class; ?>" />
        <input type="text" name="<?php echo $nameTo;?>" value="<?php echo $_GET[$nameTo]; ?>" placeholder="<?php echo $placeholderTo; ?>" class="<?php echo $class; ?>" />
      <?php } elseif($type == 'sihf_text_between_related_with_other_field'){ ?>
        <input type="text" name="<?php echo $nameFrom;?>" value="<?php echo $_GET[$nameFrom]; ?>" placeholder="<?php echo $placeholderFrom; ?>" class="<?php echo $class; ?>" />
        <input type="text" name="<?php echo $nameTo;?>" value="<?php echo $_GET[$nameTo]; ?>" placeholder="<?php echo $placeholderTo; ?>" class="<?php echo $class; ?>" />
      <?php } elseif($type == 'sihf_checkbox'){ ?>     
        <input type="checkbox" name="<?php echo $name;?>" value="1" <?php if ($_GET[$name] == 1) echo 'checked="checked"'; ?> />
      <?php } elseif($type == 'sihf_radio'){ ?>     
          <?php foreach ($this->fields[$name]['radioOptions'] as $radioOptionKey => $radioOptionVal) { ?>
                <input type="radio" name="<?php echo $name;?>" value="<?php echo $radioOptionKey;?>" <?php if ($_GET[$name] == $radioOptionKey) echo 'checked="checked"'; ?> /> <?php echo $radioOptionVal; ?>
          <?php } ?>          
      <?php } elseif($type == 'sihf_select'){ ?>
        <select name="<?php echo $name;?>" class="<?php echo $class;?>">
          <?php foreach ($this->fields[$name]['dropdownOptions'] as $dropdownOptionKey => $dropdownOptionVal) { ?>
            <option value="<?php echo $dropdownOptionKey; ?>"<?php if($_GET[$name] == $dropdownOptionKey) echo 'selected="selected"' ?>><?php echo $dropdownOptionVal;?></option>
          <?php } ?>          
        </select>
      <?php } 
      $output = ob_get_clean();
      return $output;
    }

    /**
     * Main plugin function. Outputs film search and listing view
     */
    function shortcodeFilmRegisterSearch($atts, $content = null) {

        wp_enqueue_style('jquery-ui', $this->pluginUrl . '/vendors/jquery-ui.custom/css/smoothness/jquery-ui.custom.min.css');  
        wp_enqueue_script('jquery-ui', $this->pluginUrl . '/vendors/jquery-ui.custom/js/jquery-ui.custom.min.js',array('jquery'), '1.0', true);  
        wp_enqueue_script('jquery-ui-datepicker-lt', $this->pluginUrl . '/vendors/jquery-ui.custom/js/jquery.ui.datepicker-lt.js',array('jquery-ui'), '1.0', true);  
        wp_enqueue_script('lkc-film-register-frontend', $this->pluginUrl . '/assets/js/frontend.js', array('jquery-ui'), '1.1', true);

        ob_start(); ?>

        <div class="film-register-search-form-wrap">
          <hr class="single-hr"/>
          <h2 class="fancy-title"> <?php pll_e('Paieškos forma'); ?> </h2>

          <form method="get" action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="film-register-search-form clearfix">
            <input type="hidden" name="do" value="search"/>   
            
            <table class="no-border">

              <?php 
              $shownFields = array();
              foreach ($this->fields as $key => $val) {
                if($val['searchInFrontEnd'] == true){ ?>
                <tr>
                  <td class="first"><label>
                    <?php 
                    $label = isset($val['labelFirst']) ? $val['labelFirst'] : $val['label'];
                    echo $label; 
                    ?>
                  </label></td>
                  <td class="second">
                    <?php 
                    $options = array();
                    $options['name'] = $key;
                    echo $this->sihf_output($options);
                    ?>
                  </td>
                </tr>
                <?php 
                $shownFields[] = $key;
                }
              } 


              // additional row - merging shower and first_record_producer as applier ("paraiskos teikejas"):
            if(isset($this->fields['applier'])){
            $key = 'applier';
            $val = $this->fields[$key];
            $shownFields[] = $key;
            ?>
                <tr>
                  <td class="first"><label>
                    <?php 
                    $label = isset($val['labelFirst']) ? $val['labelFirst'] : $val['label'];
                    echo $label; 
                    ?>
                  </label></td>
                  <td class="second">
                    <?php 
                    $options = array();
                    $options['name'] = $key;
                    echo $this->sihf_output($options);
                    ?>
                  </td>
                </tr>

            <?php }

              // show dynamically added options. Doing it here, so they appear in the bottom, not among for all users visible fields
              $currentDynamicFields = array();
              foreach ($this->fields as $key => $val) {
                if(!in_array($key, $shownFields) 
                    && ($val['searchHandleType'] != 'between_related_with_other_field' || ($val['searchHandleType'] == 'between_related_with_other_field' && $val['searchRelatedFieldFirst'] == true)) ){

                  if($val['searchInFrontEnd'] == false || current_user_can('administrator') || current_user_can('semi_admin')){
                    if(
                      isset($_GET[$key]) 
                      && !empty($_GET[$key])
                      || isset($_GET[$key.'_from'])
                      && !empty($_GET[$key.'_from'])
                      || isset($_GET[$key.'_to'])
                      && !empty($_GET[$key.'_to'])
                    ){
                      $currentDynamicFields[] = $key;
                     ?>

                    <tr>
                      <td class="first"><label>
                        <?php 
                        $label = isset($val['labelFirst']) ? $val['labelFirst'] : $val['label'];
                        echo $label; 
                        ?>
                      </label></td>
                      <td class="second">
                        <?php 
                        $options = array();
                        $options['name'] = $key;
                        echo $this->sihf_output($options);
                        ?>
                        <?php echo $this->getHtmlRemoveRowIcon(); ?>
                      </td>
                    </tr>
                  <?php }
                  }
                }
              }?>

              <?php if(current_user_can('administrator') || current_user_can('semi_admin')){ ?>
                <tr id="dynamic-search-fields-add-wrap">
                  <td class="first">Pridėkite dar vieną kriterijų</td>
                  <td class="second">
                    <select>
                      <option value="0"><?php echo pll_e('Pasirinkite'); ?></option>
                      <?php foreach ($this->fields as $key => $val) {
                        if($val['searchInFrontEnd'] == false){ ?>
                          <option value="<?php echo $key; ?>" data-name="<?php echo $key; ?>" <?php if(in_array($key, $currentDynamicFields)) echo 'disabled="disabled"'  ?>><?php echo $val['label']; ?></option>
                          <?php }
                        }?>
                    </select>
                    <a href="#" class="btn-block"><?php echo pll_e('Pridėti'); ?></a>
                  </td>
                </tr>
              <?php } ?>

            </table>

            <input type="submit" class="" value="<?php pll_e('Ieškoti'); ?>" />
          
            <br>
            <br>

            <?php 

            // http://www.wpbeginner.com/wp-tutorials/how-to-create-advanced-search-form-in-wordpress-for-custom-post-types/
            // http://scribu.net/wordpress/advanced-metadata-queries.html

            if(isset($_GET['do']) && $_GET['do'] == 'search'){

              $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
              $args = array(
                'post_type' => $this->cptSlug,
                'posts_per_page' => 20,
                'paged' => $paged,
                'offset' => ($paged - 1) * 20,
                'numberposts' => -1,
              );

              if(isset($_GET['show_amount'])){
                $args['posts_per_page'] = $_GET['show_amount'];
              }

              if(isset($_GET['orderby']) && $_GET['orderby'] == 'title'){
                $args['orderby'] = 'title';
              } else if(isset($_GET['orderby']) && $_GET['orderby'] != 'title' && isset($this->fields[$_GET['orderby']])){
                $key = $_GET['orderby'];
                if($this->fields[$key]['metaType'] == 'meta'){
                    $args['orderby'] = 'meta_value';
                    $args['meta_key'] = '_lkc_film_' . $_GET['orderby'];
                } else if($this->fields[$key]['metaType'] == 'taxonomy'){


                } 
              } else { // default ordering
                $args['orderby'] = 'meta_value';
                $args['meta_key'] = '_lkc_film_identity_code';
              }

              if(isset($_GET['order']) && strtoupper($_GET['order'] == 'DESC')){
                $args['order'] = 'DESC';
              } else {
                $args['order'] = 'ASC';
              }

              foreach ($this->fields as $key => $val) {              
                 
                if($val['searchInFrontEnd'] == true || current_user_can('administrator') || current_user_can('semi_admin')){
                  if(
                    isset($_GET[$key]) 
                    && !empty($_GET[$key])
                    || isset($_GET[$key.'_from'])
                    && !empty($_GET[$key.'from'])
                    || isset($_GET[$key.'_to'])
                    && !empty($_GET[$key.'_to'])
                  ){


                    if($val['searchHandleType'] == 's'){
                      $args['s'] = $_GET['title'];
            
                    // handle additionaly added search row - applier   // if any other such fields is going to be added, wpuld write separate method
                    } elseif($val['searchHandleType'] == 'metaQueryLike' && $key == 'applier'){
                      $args['meta_query']['relation'] = 'OR';
                      $args['meta_query'][] = array(
                        'key' => '_lkc_film_shower',
                        'value' => $_GET[$key],
                        'compare' => 'LIKE'
                      );
                      $args['meta_query'][] = array(
                        'key' => '_lkc_film_first_record_producer',
                        'value' => $_GET[$key],
                        'compare' => 'LIKE'
                      );
                    } elseif($val['searchHandleType'] == 'metaQueryLike'){
                      $args['meta_query'][] = array(
                        'key' => '_lkc_film_'.$key,
                        'value' => $_GET[$key],
                        'compare' => 'LIKE'
                      );
                    } elseif ($val['searchHandleType'] == 'metaQueryEqual'){
                      $args['meta_query'][] = array(
                        'key' => '_lkc_film_'.$key,
                        'value' => $_GET[$key],
                      );
                    }  elseif ($val['searchHandleType'] == 'metaQueryInArray'){  // multiple select
                      $args['meta_query'][] = array(
                        'key' => '_lkc_film_'.$key,
                        'value' => $_GET[$key],
                        'compare' => 'LIKE'  // should be 'IN' probably, but for now 'LIKE' works
                      );
                    }  elseif ($val['searchHandleType'] == 'taxonomy'){
                      $args[$val['taxonomySlug']] = $_GET[$key];

                    }  elseif ($val['searchHandleType'] == 'between'){
                      if(isset($_GET[$key.'_from']) && !empty($_GET[$key.'_from'])){       
                        $args['meta_query'][] = array(
                          'key' => '_lkc_film_'.$key,
                          'value' => $_GET[$key.'_from'],
                          'compare' => '>='
                        );
                      }

                      if(isset($_GET[$key.'_to']) && !empty($_GET[$key.'_to'])){
                        $args['meta_query'][] = array(
                          'key' => '_lkc_film_'.$key,
                          'value' => $_GET[$key.'_to'],
                          'compare' => '<='
                        );
                      }

                    }  elseif ($val['searchHandleType'] == 'between_related_with_other_field'){
                        if($val['searchRelatedFieldFirst'] == true){
                            $compare = '>=';
                        } else {
                            $compare = '<=';
                        }
                        $args['meta_query'][] = array(
                          'key' => '_lkc_film_'.$key,
                          'value' => $_GET[$key],
                          'compare' => $compare
                        );
                    } 

                  }
                }
              }

              // print_r($args);exit;
              $the_query = new WP_Query( $args );
              
              // $args2 = $args;
              // $args2['posts_per_page'] = -1;
              // unset($args2['offset']);
              // unset($args2['paged']);
              // $the_query2 = new WP_Query( $args2 );
              // print_r($the_query2);exit;

            // set ordering params for view display
            if(isset($_GET['order']) && strtoupper($_GET['order'] == 'DESC')){
              $orderAlt = 'ASC';
              $orderAltTitle = pll__('Rikiuoti didėjimo tvarka');
              $orderIcon = 'desc';
            } else {
              $orderAlt = 'DESC';
              $orderAltTitle = pll__('Rikiuoti mažėjimo tvarka');                      
              $orderIcon = 'asc';
            }

            $listTableFields = $this->data->getListTableFields();

              ?>

              <hr class="single-hr"/>
              <div class="film-register-search-results-header clearfix">
                <h2 class="fancy-title">
                    <?php pll_e('Paieškos rezultatai'); ?>
                    <?php /*if($the_query2) { ?>
                        <span class="film-count">(<?php echo $the_query2->post_count;?>)</span>
                    <?php }*/ ?>
                </h2>

                <?php if($the_query && $the_query->post_count > 0) { ?>
                    <div class="print-wrap">
                        <a href="#" class="js-print-btn" data-div="film-list-print-wrap"> <i class="icon-print"></i></a>
                    </div>
                <?php } ?>
                
                <?php if($the_query && $the_query->post_count > 1) { ?>

                    <div class="show-amount-wrap">
                        <span><?php pll_e('rodyti'); ?></span>
                        <select name="show_amount">
                          <?php $amounts = array(20 => 20, 50 => 50, 100 => 100, -1 => pll__('visus'));
                          foreach ($amounts as $key => $val) { ?>
                          <option value="<?php echo $key?>"<?php if($_GET['show_amount'] == $key || !isset($_GET['show_amount']) && $key == 20 ) echo 'selected="selected"' ?>><?php echo $val;?></option>
                          <?php } ?>
                      </select>
                    </div>

                    <div class="orderby-wrap">
                        <span><?php pll_e('rikiuoti pagal'); ?></span>
                        <select name="orderby" class="orderby">
                            <?php foreach ($listTableFields as $key => $val) {
                                if($val['metaType'] != 'taxonomy'){ ?>
                                    <option value="<?php echo $key?>"<?php if($_GET['orderby'] == $key || !isset($_GET['orderby']) && $key == 'identity_code' ) echo 'selected="selected"' ?>><?php echo strtolower($val['label']);?></option>
                                <?php }
                            } ?>
                        </select>
                        <a href="<?php echo add_query_arg(array('order' => $orderAlt,), $_SERVER['REQUEST_URI']);?>" class="order-direction" title="<?php echo $orderAltTitle;?>">
                          <i class="icon-order-<?php echo $orderIcon;?>"></i> 
                        </a>
                    </div>
                <?php } ?>

              </div><!-- .film-register-search-results-header -->

              <?php if($the_query === false) { ?>
                <p><?php pll_e('Prašome įvesti bent 1 paieškos kriterijų'); ?></p>
              <?php } elseif($the_query->post_count < 1) { ?>
                <p><?php pll_e('Pagal pasirinktus paieškos kriterijus filmų nerasta'); ?></p>
              <?php } else { ?>
              <div id="film-list-print-wrap">
                  <table class="film-register-list-table film-print-table">
                  	<tr>
                  	<?php 
                  	foreach($listTableFields as $key => $val){
              			$orderLink = add_query_arg(array('orderby' => $key), $_SERVER['REQUEST_URI']); 
                        $orderIcon  = '';
                        if(isset($_GET['orderby']) && $_GET['orderby'] == $key || !isset($_GET['orderby']) && $key == 'identity_code'){
                  			if(isset($_GET['order']) && strtoupper($_GET['order'] == 'DESC')){
                                $orderLink = add_query_arg(array('order' => 'ASC'), $orderLink);
                                $orderIcon = 'desc';
                            } else {
                                $orderLink = add_query_arg(array('order' => 'DESC'), $orderLink);
                                $orderIcon = 'asc';
                  			}
                  		}
              			?>
              			<th>
                            <?php if($val['metaType'] != 'taxonomy'){ ?>
                                <a href="<?php echo $orderLink; ?>">
                            <?php } ?>
                            <?php echo $val['label'];?>
                            <?php if($orderIcon != '') { ?>
                                <i class="icon-order-<?php echo $orderIcon;?>"></i> 
                            <?php } ?>
                            <?php if($val['metaType'] != 'taxonomy'){ ?>
                                </a>
                            <?php } ?>
                        </th>
                  	<?php } ?>
                  	</tr>
                    <?php while ($the_query->have_posts()){   
                        $the_query->the_post();
    					?>
                        <tr>
                        <?php foreach($listTableFields as $key => $val){ ?>
                          <td>
                            <?php if($key == 'title'){ ?>
                            <a href="<?php the_permalink();?>">
                            <?php } ?>
                                <?php echo $this->data->getFieldData(get_the_ID(), $key); ?>
                            <?php if($key == 'title'){ ?>
                            </a>
                            <?php } ?>
                            </td>
                        <?php } ?>
                        </tr>
                      <?php } ?>
                  	</table>
                </div>

                <?php 
                kcsite_nice_pagination($the_query->max_num_pages);

                wp_reset_query();
                wp_reset_postdata();
              }
            }
            ?>
          </form> 
        </div> <!-- film-register-search-form-wrap -->

        <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('.js-print-btn').click(function(){
                handlePrintBtnClick(jQuery(this), '<?php echo $this->pluginUrl;?>/assets/css/print.css');
            });
        });
        </script>

        <?php $output = ob_get_clean();

      return do_shortcode($output); 
    }


/*  not used as doesn't work as expected

    //allows queries to be sorted by taxonomy term name
    // http://www.jrnielsen.com/wp-query-orderby-taxonomy-term-name/
    function posts_clauses_with_tax( $clauses, $wp_query ) {
        global $wpdb;
        //array of sortable taxonomies
        // print_r($wp_query);exit;
        $taxonomies = $this->data->getFilmTaxonomies();
        if (isset($wp_query->query['orderby']) && in_array($wp_query->query['orderby'], $taxonomies)) {
            $clauses['join'] .= "
                LEFT OUTER JOIN {$wpdb->term_relationships} AS rel2 ON {$wpdb->posts}.ID = rel2.object_id
                LEFT OUTER JOIN {$wpdb->term_taxonomy} AS tax2 ON rel2.term_taxonomy_id = tax2.term_taxonomy_id
                LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
            ";
            $clauses['where'] .= " AND (taxonomy = '{$wp_query->query['orderby']}' OR taxonomy IS NULL)";
            $clauses['groupby'] = "rel2.object_id";
            $clauses['orderby']  = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
            $clauses['orderby'] .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
        }
        return $clauses;
    }
*/

    function pagination($pages = '', $range = 2) {
         $showitems = ($range * 2)+1; 
           
         global $paged; if(empty($paged)) $paged = 1; 

         if($pages == '') {
             global $wp_query;
             $pages = $wp_query->max_num_pages;
             if(!$pages) {
                 $pages = 1;
             }
         }    
         if(1 != $pages) {
             echo "<div class=\"pagination-wrap clearfix\"><nav class=\"pagination clearfix\">";
             if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."' class=\"dots-left\">...</a>";
             //if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."' class=\"dots-left\">...</a>";
     
             for ($i=1; $i <= $pages; $i++)
             {
                 if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
                 {
                     echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
                 }
             }
     
             //if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\" class=\"dots-right\">...</a>";
             if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."' class=\"dots-left\">...</a>";
             echo "</nav></div>\n";
         }
    }

    /* Ajax methods
    ----------------------------------- */

    function filmRegisterSearchAutocompleteResponse() {

        if(isset($_GET['term']) && !empty($_GET['term'])){

        $term = $_GET['term'];

        function title_like_posts_where($where, &$wp_query) {
            global $wpdb;
            if ($post_title_like = $wp_query->get('post_title_like')) {
                $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql(like_escape($post_title_like)) . '%\'';
                // $where .= ' AND ' . $wpdb->posts . '.post_title LIKE pirm%';
            }
            return $where;
        }

          $args = array(
              'post_type' => $this->cptSlug,
              'orderby' => 'title',
              'order' => 'ASC',
              'post_title_like' => $term,
          );
        add_filter( 'posts_where', 'title_like_posts_where', 10, 2 );
          $the_query = new WP_Query($args);
          $results =  $the_query->posts;     
        remove_filter( 'posts_where', 'title_like_posts_where');

        $response = array();
        foreach ($results as $key => $val) {
          $response[$key]['ID'] = $val->ID;
          $response[$key]['value'] = $val->post_title;
          $response[$key]['permalink'] = get_permalink($val->ID);
        }

        echo json_encode($response);
      }
        exit;
    }


    function getNewFieldRowResponse() {
        if(isset($_GET['newOption']) && !empty($_GET['newOption'])){
          $key = $_GET['newOption'];

          ob_start(); ?>
          <tr class="added-dynamically" data-name="<?php echo $key;?>">
            <td><?php echo $this->fields[$key]['label'];?></td>
            <td>
              <?php echo $this->sihf_output(array('name' => $key)); ?>
              <?php echo $this->getHtmlRemoveRowIcon(); ?>
            </td>
          </tr>
          <?php
          $output = ob_get_clean();
          echo $output;
        }
        exit;
    }

    /* Film register archive
    ----------------------------------- */

    /**
     * Check if film should be archived
     */
    function shouldBeArchivedBeforeSaving($_POST){
        // print_r($_POST);exit;
      if(isset($_POST['_lkc_film_meta']['invalid']) && $_POST['_lkc_film_meta']['invalid'] == 1 
        || isset($_POST['_lkc_film_meta']['unregistered']) && $_POST['_lkc_film_meta']['unregistered'] == 1 ){
        return true;
      }
      return false;
    }

    /**
     * Check if film is archived
     */
    function isArchived($post_id){
      if(get_post_meta($post_id, '_lkc_film_archived', true) == 1){
        return true;
      }
      return false;
    }


    /**
     * Mark film as archived on save
     */
    function managePostArchivedStatus($post_id){
      if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
      }
      // if (!wp_verify_nonce($_POST[$this->pluginName.'_noncename'], plugin_basename(__FILE__))){
      // if (!wp_verify_nonce($_POST['_lkc_film_meta_nonce'], plugin_basename(__FILE__))){
      //   return;
      // }

      if (wp_is_post_revision($post_id)){
        return;
      }

      if (!current_user_can('edit_post', $post_id) || $_POST['post_type'] != $this->cptSlug){
        return;
      }

      if($this->shouldBeArchivedBeforeSaving($_POST)){
        update_post_meta($post_id, '_lkc_film_archived', 1);
      } else {
        update_post_meta($post_id, '_lkc_film_archived', 0);
      }

    }

    /**
     * Checks if film has identity code, in other words - if ever was published
     */
    function hasIdentityCode($post_id){
        if(get_post_meta($post_id, '_lkc_film_identity_code', true)){
            return true;
        }
        return false;
    }

    /**
     * Checks if film is checked as ready and should be published
     * @param $post_id
     * @param $meta - $meta fields array available in WPAlchemy filter
     */
    function isReady($post_id, $meta = array()){
        if($this->data->getFieldData($post_id, 'ready') || isset($meta['ready'])){
            return true;
        }
        return false;
    }



    /**
     * Update last (in other words newest, biggest) identity code value
     */
    function getLastIdentityCode(){
        $lastFilmIdentityCode = get_option('lkc_film_last_identity_code');
        if(!$lastFilmIdentityCode){
            // this situation should happen only once in the beggining of use of plugin
            $args = array(
                'post_type' => $this->cptSlug, 
                'post_status' => 'any', 
                'orderby' => 'meta_value', 
                'order' => 'DESC', 
                'meta_key' => '_lkc_film_identity_code', 
                'posts_per_page' => 1,
            );
           
            $the_query = new WP_Query( $args );
            $lastFilm = $the_query->posts[0];
            $lastFilmIdentityCode = $this->data->getFieldData($lastFilm->ID, 'identity_code');
        }

        return $lastFilmIdentityCode;
    }

    /**
     * Update incremental identity code value in options table with last biggest current value
     */
    function updateLastIdentityCode($newIdentityCode){
        return update_option('lkc_film_last_identity_code', $newIdentityCode);
    }

    /**
     * Return new identity code, 1 unit bigger than last film's identity code
     */
    function getNextIdentityCode(){
        $lastFilmIdentityCode = $this->getLastIdentityCode();
        $lastFilmIdentityCodeInt = intval(substr($lastFilmIdentityCode, 1));
        $lastFilmIdentityCodeIntNext = $lastFilmIdentityCodeInt + 1;
        $lastFilmIdentityCodeIntNextFinal = 'F' . str_pad($lastFilmIdentityCodeIntNext, 5, "0", STR_PAD_LEFT);

        return $lastFilmIdentityCodeIntNextFinal;
    }


    /**
     * Utility function, checks if film is invalid
     * Useful as in future this condition may change. So this function should be used as wrapper
     * @param $post_id
     */
    function isInvalid($post_id){
        if($this->data->getFieldData($post_id, 'invalid')){
            return true;
        }
        return false;
    }

    /**
     * Utility function, checks if film is unregistered
     * Useful as in future this condition may change. So this function should be used as wrapper
     * @param $post_id
     */
    function isUnregistered($post_id){
        if($this->data->getFieldData($post_id, 'unregistered')){
            return true;
        }
        return false;
    }

    /**
     * Utility function, checks if film is should be visible in frontend
     * Post should be accessible for users only if are visible
     * @param $post_id
     */
    function visibleInFrontend($post_id){
        if($this->isReady($post_id) && !$this->isInvalid($post_id) && !$this->isUnregistered($post_id)){
            return true;
        }
        return false;
    }

	function interruptPostSave($meta, $post_id){
		// print_r($_POST);
		// exit;

    	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        	return;
      	}

      	if (wp_is_post_revision($post_id)){
        	return;
      	}

      	if (!current_user_can('edit_post', $post_id) || $_POST['post_type'] != $this->cptSlug){
        	return;
      	}

        if(isset($_POST['filmExportType'])){ 
            $this->handleExport($post_id);
        }

      	$today = date('Y-m-d H:i:s');

        // check if film is first time saved as ready
        // if yes, give him 'identity_code' and 'register_date' meta fields
        if($this->isReady($post_id, $meta) && !$this->hasIdentityCode($post_id)){
            // first time saving ready post
            $newIdentityCode = $this->getNextIdentityCode();
            $this->updateLastIdentityCode($newIdentityCode);
            $identityCode = $newIdentityCode;

            $registerDate = $today;
        } else if($this->hasIdentityCode($post_id)){
            // just keeping the same value
            $identityCode = $this->data->getFieldData($post_id, 'identity_code');
            $registerDate = $this->data->getFieldData($post_id, 'register_date');
        }

        if(isset($identityCode)){
            $meta['identity_code'] = $identityCode;        
            $meta['register_date'] = $registerDate;        
        }

        // insert on first save no matter if film is marked as ready
      	$dataEnterDate = $this->data->getFieldData($post_id, 'data_enter_date');
      	if(!$dataEnterDate){
			$meta['data_enter_date'] = $today;
      	} else {
			$meta['data_enter_date'] = $dataEnterDate;
      	}


        // fields that are strictly dependant on checkboxes, if they are checked or not
        if(isset($meta['invalid'])  && !$this->data->getFieldData($post_id, 'invalid')){
            $meta['data_declared_invalid_date'] = $today;
        } else if(isset($meta['invalid'])  && $this->data->getFieldData($post_id, 'invalid')){
            $meta['data_declared_invalid_date'] = $this->data->getFieldData($post_id, 'data_declared_invalid_date');
        }
        if(isset($meta['unregistered'])  && !$this->data->getFieldData($post_id, 'unregistered')){
            $meta['unregister_date'] = $today;
        } else if(isset($meta['unregistered'])  && $this->data->getFieldData($post_id, 'unregistered')){
            $meta['unregister_date'] = $this->data->getFieldData($post_id, 'unregister_date');
        }

        // fields that should be updated every time saving post post:


        // check if film is national
        $meta['is_national'] = false;
        $condition1 = false;
        $condition2 = false;

        if ($meta['country'] && in_array('lt', $meta['country'])) {
            $condition1 = true;
        } else if ($meta['country'] && $meta['country'] == 'lt') {
            $condition1 = true;
        }

        if ($meta['film_original_language'] && in_array('lt', $meta['film_original_language'])) {
            $condition2 = true;
        } else if ($meta['film_original_language'] && $meta['film_original_language'] == 'lt') {
            $condition2 = true;
        } else if ($meta['film_subtitle_language'] && in_array('lt', $meta['film_subtitle_language'])) {
            $condition2 = true;
        } else if ($meta['film_subtitle_language'] && $meta['film_subtitle_language'] == 'lt') {
            $condition2 = true;
        }

        if ($condition1 && $condition2) {
            $meta['is_national'] = 1;
        } else {
            $meta['is_national'] = 0;
        }

        // check if film is full metter
        $meta['is_full_meter'] = false;

        if ($meta['duration'] && $meta['duration'] > 60) {
            $meta['is_full_meter'] = true;
        }

	    return $meta;

	}


    /**
     * Add archived state near title in admin list table if film is archived
     */
    function postStateArchived(){
      global $post;

      if($this->isArchived($post->ID)){
        $states[] = __('Archyve');
      }
      return $states;
    }

  /**
   * add filter by new type in posts admin list table
   */ 
  function addFilterByArchive() {

    $options = array(
      array('slug' => 0, 'name' => 'Archyvas...'),
      array('slug' => 'no', 'name' => 'Nearchyvuoti'),
      array('slug' => 'yes', 'name' => 'Archyvuoti'),
    );

    $m = isset( $_GET['_Archived'] ) ? $_GET['_Archived'] : 0;
    ?>

    <select id="archived-filter" name='_Archived'>
    
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

  function filterByArchiveRequest($request) {
      if(isset($_GET['_Archived']) && $_GET['_Archived'] == 'yes') {
        $request['meta_query'][] = array(
          'key' => '_lkc_film_archived',
          'value' => 1,
        );
      } elseif(isset($_GET['_Archived']) && $_GET['_Archived'] == 'no'){
        $request['meta_query'][] = array(
          'key' => '_lkc_film_archived',
          'value' => 0,
        );
      }
      return $request;
  }


    /* Methods for importing records from old DB
    ----------------------------------- */

    function admin_page_import(){
    	$this->editDB();
    }

    function editDB(){
    	// echo 'Šis meniu punktas matomas tik admin. Čia galima kažką pahackinti jeigu reikia.';

        // $this->fixCompositor();


    }

    function fixMultipleSelects(){

        // these fields got imported with not lowercase letters and then multiple selects do not work. Fixing
        // country
        // film_original_language
        // film_subtitle_language

        $args = array(
            'post_type' => $this->cptSlug,
            'posts_per_page' => -1,
        );
        $the_query = new WP_Query( $args );

        while ($the_query->have_posts()){   
            $the_query->the_post();
            update_post_meta(get_the_ID(), '_lkc_film_compositor', $compositor);
        }


        wp_reset_query();
        wp_reset_postdata();

    }


/*    function fixCompositor() {
        global $wpdb;

        $oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable LIMIT 400 OFFSET 1200");   //1200

        foreach ($oldData as $key => $val) {

            $identityCode = $val->kod;
            $compositor = $this->changeHieroglifs($val->muzika);

            $relevandPostInNewDB = get_posts(array('post_type' => $this->cptSlug, 'meta_key' => '_lkc_film_identity_code', 'meta_value' => $identityCode));
            $relevandPostInNewDB = $relevandPostInNewDB[0];
            $relevandPostInNewDBID = $relevandPostInNewDB->ID;
            
            update_post_meta($relevandPostInNewDBID, '_lkc_film_compositor', $compositor);
        }

    }*/


/*
    function importAll(){
    	global $wpdb;

        // testavimui:
    	//  jav - id= 652
        // daug saliu -   kod = F00235
        // daug licenziju -   kod = F00205
    	// nera lt pavadinimo-   kod = F00480
    	// valstybe ihardcodinta kurios net nera - 201

        // $oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable LIMIT 1 OFFSET 201");
        //$oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable WHERE kod='F00205'  ");
        //$oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable WHERE kod='F00480'  ");
        // $oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable WHERE kod='F00235'  ");
        // print_r($oldData);exit;
        //$oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable order by kod ASC LIMIT 1 OFFSET 100");
    	



        // real import
        // $oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable LIMIT 5 OFFSET 0");  //5
        //$oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable LIMIT 45 OFFSET 5");  //50
        // $oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable LIMIT 50 OFFSET 50");   //100
        // $oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable LIMIT 100 OFFSET 100");   //200
        // $oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable LIMIT 100 OFFSET 200");   //300
        // $oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable LIMIT 100 OFFSET 300");   //400
        // $oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable LIMIT 100 OFFSET 400");   //500
        // $oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable LIMIT 100 OFFSET 500");   //600
        //$oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable LIMIT 100 OFFSET 600");   //700
        // $oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable LIMIT 100 OFFSET 700");   //800
        //$oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable LIMIT 100 OFFSET 800");   //900
        // $oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable LIMIT 100 OFFSET 900");   //1000
        //$oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable LIMIT 100 OFFSET 1000");   //1100
        // $oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable LIMIT 100 OFFSET 1100");   //1200
        $oldData = $wpdb->get_results("SELECT * FROM $this->oldFilmTable LIMIT 100 OFFSET 1200");   //1200




 		$taxonomyFields = $this->getTaxonomyTypeFields();

    	foreach ($oldData as $key => $val) {
    		// print_r($val);exit;

            $title = $val->pav_lt != '' ? $val->pav_lt : $val->pav;
            // $title = str_replace('Ð', 'Š', $title);
            $title = $this->changeHieroglifs($title);

    		$film = array(
    			'post_title' => $title,
    			'post_content' => $this->changeHieroglifs($val->anotacija),
    			'post_status' => 'publish',
    			'post_date' => $val->laikas,
    			'post_type' => $this->cptSlug,
    		);

    		$post_id = wp_insert_post($film, false);

    		if($post_id){
	    		$metaFields = array();
	    		foreach ($this->fields as $key2 => $val2) {
	    			if(isset($this->fields[$key2]['legacyField'])){
			    		$legacyField = $this->fields[$key2]['legacyField'];
                        if($val2['metaType'] == 'taxonomy' && $key2 != 'rights_given'){
                            $terms = $this->mapLegacyTaxonomy($val2['taxonomySlug'], $val->$legacyField);
                            wp_set_object_terms($post_id, $terms, $val2['taxonomySlug']);
		    			} else if($val2['metaType'] == 'taxonomy' && $key2 == 'rights_given'){
			    		
                            $map = $this->getLicensesMap();
                            $terms = array();
                            foreach ($map as $key3 => $val3) {
                                $dbResult = $wpdb->get_results("SELECT $key3 FROM $this->oldFilmLicensesTable WHERE filmas = '$val->kod'", ARRAY_A);
                                $legacyFieldValue = $dbResult[0][$key3];
                                if($legacyFieldValue == 1){
                                    $terms[] = $val3;
                                }
                            }
			    			wp_set_object_terms($post_id, $terms, $val2['taxonomySlug']);
		    			
                        } elseif($val2['metaType'] == 'meta'){
		    				if(isset($val2['legacyFieldInTableLicenses']) && $val2['legacyFieldInTableLicenses'] == true){
		    					// print_r($val2->$legacyField);exit;
		    					$dbResult = $wpdb->get_results("SELECT $legacyField FROM $this->oldFilmLicensesTable WHERE filmas = '$val->kod'", ARRAY_A);
		    					$insertData = $dbResult[0][$legacyField];
                            }
                            else if($key2 == 'country') {
                                $country = $this->mapCountries($val->$legacyField);
                                $insertData = $country['value']; // wrapping in array to support multiple select
                                if($country['hardcoded'] == true){
                                    //add additional meta data so we know that country is hardcoded and search should be held carefully for such records
                                    update_post_meta($post_id, $this->hardcodedCountryMetaField, 1, true);
                                    $metaFields[] = $this->hardcodedCountryMetaField;
                                }
                            } else {
                                $insertData = $val->$legacyField;
		    				}

                            $insertData = $this->changeHieroglifs($insertData);


	    					update_post_meta($post_id, '_lkc_film_' . $key2, $insertData, true);
	    					$metaFields[] = '_lkc_film_' . $key2;
		    			}
                
                    }
                }

	    		
	    		// additional field to easily separate imported films from added via WP admin
                update_post_meta($post_id, $this->importedMetaField, 1);
                $metaFields[] = $this->importedMetaField;
                
                update_post_meta($post_id, '_lkc_film_ready', 1);
                $metaFields[] = '_lkc_film_ready';	    		
                
                update_post_meta($post_id, '_lkc_film_license_territory', array('lt'));
	    		$metaFields[] = '_lkc_film_license_territory'; // all films has Lithuania territory

	    		// by default not archived, need to find out when they should be archived
	    		update_post_meta($post_id, '_lkc_film_archived', 0);



	    		// needed for WP Alchemy to fill meta fields in admin
	    		update_post_meta($post_id, '_lkc_film_meta_fields', $metaFields);
    		}
    	}
    }
*/
    function changeHieroglifs($field){

        $hieroglifs = array(
            'À' => 'Ą',
            'È' => 'Č',
            'Æ' => 'Ę',
            'Ë' => 'Ė',
            'Á' => 'Į',
            'Ð' => 'Š',
            'Ø' => 'Ų',
            'Û' => 'Ū',
            'Þ' => 'Ž',
            'à' => 'a',
            'è' => 'č',
            'æ' => 'ę',
            'ë' => 'ė',
            'á' => 'į',
            'ð' => 'š',
            'ø' => 'ų',
            'û' => 'ū',
            'þ' => 'ž',
        );

        foreach ($hieroglifs as $key => $val) {
            $field = str_replace($key, $val, $field);
        }

        return $field;
    }



    /**
     * returns array of fields that are taxonomy type
     */
    function getTaxonomyTypeFields(){
    	$fields = array();

		foreach ($this->fields as $key => $val) {
			if($val2['metaType'] == 'taxonomy'){
				$fields[$key] = $val;
			}
		}

		return $fields;
    }

    function mapLegacyTaxonomy($taxonomyField, $legacyFieldValue){
    	$legacyFields = array(

    		$this->fields['type']['taxonomySlug'] => array(
    			'legacyField' => 'rusis',
    			'mapping' => array(
	    			'VAI' => 40,
	    			'DOK' => 41,
	    			'ANIM' => 42,
	    			'v-d' => 61,
	    		),
    		),

    		$this->fields['genre']['taxonomySlug'] => array(
    			'legacyField' => 'zanras',
    			'mapping' => array(
	    			'DRA' => 43,
	    			'KOM' => 44,
	    			'TRA' => 62,
	    			'TRKO' => 63,
	    			'NUOT' => 45,
	    			'SIAU' => 46,
	    			'VEIK' => 47,
	    			'FANT' => 48,
	    			'MOKS' => 49,
	    			'ERO' => 50,
	    			'drama-kom' => 64,
	    			'Vx-nuot-t' => 65,
	    			'fa-drama' => 66,
	    			'Fd' => 66,
	    			'fa-nuo' => 67,
	    			'fa-n' => 67,
	    			'ro-kom' => 68,
	    			'drama-s' => 69,
	    			'fan-v-tr' => 70,
	    			'EPAS' => 71,
	    			'Trileris' => 72,
	    			'Mokslo' => 49,
	    			'MuzD' => 73,
	    			'Musicl' => 74,
	    			'FaTr' => 75,
	    			'Kr-v-d' => 76,
	    			'irko' => 77,
	    			'KrimKo' => 78,
	    			'JKo' => 79,
	    			'NuotKom' => 80,
	    			'karine' => 81,
	    			'mi-tr' => 82,
	    			'Musikl' => 83,
	    			'Po' => 84,
	    			'bio-d' => 85,
	    			'ID' => 86,
	    			'S-Tr' => 87,
	    			'dok' => 88,
	    			'fnk' => 89,
	    			'FunCom' => 90,
	    			'meno' => 91,
	    			'bio-' => 104
	    		),
    		),

    		$this->fields['index']['taxonomySlug'] => array(
    			'legacyField' => 'zanras',
    			'mapping' => array(
	    			'S' => 51,
	    			'N-16' => 52,
	    			'T' => 53,
	    			'N-7' => 54,
	    			'N-13' => 55,
                    'V' => 56,
	    			'A' => 125,
	    		),
    		),

    		$this->fields['license_type']['taxonomySlug'] => array(
    			'legacyField' => 'zanras',
    			'mapping' => array(
	    			1 => 57,
	    			0 => 58,

	    		),
    		),

    		$this->fields['format']['taxonomySlug'] => array(
    			'legacyField' => 'zanras',
    			'mapping' => array(
	    			'DVD' => 92,
	    			'VHS' => 93,
	    			35 => 94,
	    			'm16' => 95,
	    			'Sp, Dvd' => 96,
	    			'35+' => 97,
	    			'lichter' => 98,
	    			'd-v' => 99,
	    			'35+d+v' => 100,
	    			'beta' => 101,
	    			'BLUE-RAY' => 102,
	    			'DCP' => 103,
	    		),
    		),
    	);

    	return $legacyFields[$taxonomyField]['mapping'][$legacyFieldValue];
    }


    function getLicensesMap(){
        $licensesMap = array(
          'rodyti_kt' => 105, // Viešas rodymas kino teatruose 
          'rodyti_kt_70mm' => 106, // Viešas rodymas kino teatruose 70 mm formatu
          'rodyti_kt_35mm' => 107, // Viešas rodymas kino teatruose 35 mm formatu
          'rodyti_kt_16mm' => 108, // Viešas rodymas kino teatruose 16 mm formatu
          'rodyti_vs' => 109, // Viešas rodymas video salėse
          'rodyti_vs_vhs' => 110, // Viešas rodymas video salėse VHS formatu
          'rodyti_vs_vcd' => 111, // Viešas rodymas video salėse VCD formatu
          'rodyti_vs_dvd' => 112, // Viešas rodymas video salėse DVD formatu
          'rodyti_vs_kita' => 113, // Viešas rodymas video salėse kitais formatais
          'rodyti_vs_kiti' => 113, // Viešas rodymas video salėse kitais formatais
          // 'rodyti_vs_kita_txt' => , //
          'platinti_pard' => 114, // Platinimas: pardavinėti
          // 'plat_media_pard' => , // 
          'platinti_nuoma' => 115, // Platinimas: nuomoti
          // 'plat_media_nuoma' => , // 
          'platinti_panauda' => 116, // Platinimas: teikti panaudai
          // 'plat_media_panauda' => , // 
          'platinti_kita' => 117, // Platinimas: kita
          // 'plat_media_kita' => , // 
          'skelbti_tv' => 118, // TV transliacija
          'skelbti_tv_pay' => 119, // TV transliacija mokama
          'skelbti_tv_free' => 120, // TV transliacija nemokama
          'skelbti_tv_kita' => 121, // TV transliacija kita
          // 'skelbti_tv_kita_txt' => , //  
          'skelbti_kabel' => 122, // Kabelinė transliacija
          'skelbti_internet' => 123, // Interneto transliacija
          'skelbti_internet_kita' => 124, // Interneto transliacija kita
          // 'skelbti_internet_kita_txt' => , // 
        ); 

        return $licensesMap;    
    }

    function mapCountries($legacyFieldValue){
        $countriesMap = array(
            'LT' => 'lt',
            'JAV' => 'us',
            'JAV2' => 'us',
            'Jav' => 'us',
            'Jav-' => 'us',
            'jav-' => 'us',
            'USA' => 'us',
            'DE' => 'de',
            'RU' => 'ru',
            'PL' => 'pl',
            'Ja' => 'jp',
            'UK' => 'gb',
            'sve' => 'se',
            'EE' => 'ee',
            'CA' => 'ca',
            'BELGIUM' => 'be',
            'Indija' => 'in',
            'Isp' => 'es',
            'kaz' => 'kz',
            'France' => 'fr',
            'Fran' => 'fr',
            'lat' => 'lv',
            'Zel' => 'nz',
            'New Zelan' => 'nz',
            'Kinija' => 'cn',
            'Nor' => 'no',
            'KOR' => 'kr',
            'SOUTH KOR' => 'kr',
            'Ven' => 'hu',
            'BRAZIL' => 'br',
            'Danija' => 'dk',
            'NY' => 'nl',
            'Kolumbija' => 'co',
            // '' => '',
        );

        if(isset($countriesMap[$legacyFieldValue])){
            return array('value' => array($countriesMap[$legacyFieldValue]), 'hardcoded' => false);
        } else {
            global $wpdb;
            $legacyCountry = $wpdb->get_results("SELECT pav FROM $this->oldFilmCountriesTable WHERE kod = '$legacyFieldValue'");
            if(isset($legacyCountry[0])){
                return array('value' => $legacyCountry[0]->pav, 'hardcoded' => true);
            } else {
                return array('value' => $legacyFieldValue, 'hardcoded' => true);
            }
        }
    }

    // mapped country fields:
    // INSERT INTO salys VALUES ('LT','Lietuva','Lithuania');
    // INSERT INTO salys VALUES ('DE','Vokietija','Germany');
    // INSERT INTO salys VALUES ('RU','Rusija','Russia');
    // INSERT INTO salys VALUES ('PL','Lenkija','Poland');
    // INSERT INTO salys VALUES ('JAV2','Jungtinës Amerikos Valstijios','USA');
    // INSERT INTO salys VALUES ('JAV','United States of America','Jungtinës Amerikos Valstijos');
    // INSERT INTO salys VALUES ('USA','JAV','USA');
    // INSERT INTO salys VALUES ('Ja','Japonija','Japan');
    // INSERT INTO salys VALUES ('jav','Jungtinës Amerikos Valstijos','United States Of America');
    // INSERT INTO salys VALUES ('Jav-','Jungtinës Amerikos Valstijos','United States Of America');
    // INSERT INTO salys VALUES ('UK','Didþioji Britanija','England');
    // INSERT INTO salys VALUES ('sve','Ðvedija','Sweden');
    // INSERT INTO salys VALUES ('CA','Kanada','Canada');
    // INSERT INTO salys VALUES ('BELGIUM','BELGIJA','BELGIUM');
    // INSERT INTO salys VALUES ('EE','Estija','Estonia');
    // INSERT INTO salys VALUES ('Indija','Indija','India');
    // INSERT INTO salys VALUES ('Isp','Ispanija','Spain');
    // INSERT INTO salys VALUES ('kaz','Kazachstanas','Kazakstan');
    // INSERT INTO salys VALUES ('France','Prancûzija','France');
    // INSERT INTO salys VALUES ('lat','Latvija','Latvia');
    // INSERT INTO salys VALUES ('Zel','Naujoji Zelandija','New Zeland');
    // INSERT INTO salys VALUES ('Kinija','Kinija','China');
    // INSERT INTO salys VALUES ('Nor','Novegija','Norway');
    // INSERT INTO salys VALUES ('KOR','Pietø Korëja, Japonija','South Korea');
    // INSERT INTO salys VALUES ('Ven','Vengrija','Hungary');
    // INSERT INTO salys VALUES ('BRAZIL','BRAZILIJA','BRAZIL');
    // INSERT INTO salys VALUES ('Danija','Danija','Denmark');
    // INSERT INTO salys VALUES ('NY','Nyderlandai/Pietø Afrika/Airija','Holland');
    // INSERT INTO salys VALUES ('SOUTH KOR','Pietø Korëja','SOUTH KOREA');
    // INSERT INTO salys VALUES ('Kolumbija','Kolumbija, Kosta Rika','Colombia, Costa Rica');
    // INSERT INTO salys VALUES ('New Zelan','Naujoji Zelandija','New Zeland');


    // did not map because weere hardcoded:
    // INSERT INTO salys VALUES ('JAV, KA','JAV, KANADA','USA, CANADA');
    // INSERT INTO salys VALUES ('JAV/Mx','Jungtinës Amerikos Valstijos/Meksika','USA/Mexico');
    // INSERT INTO salys VALUES ('UK-Germ','Didþioji Britanija/Vokietija','Great Britain/Germany');
    // INSERT INTO salys VALUES ('Pr-Isp','Prancûzija, Ispanija','France/Spain');
    // INSERT INTO salys VALUES ('Pr-Ka-Be-','Prancûzija/Kanada/Belgija/JK','France/Canada/Belgium/UK');
    // INSERT INTO salys VALUES ('JAV/Kan','JAV, Kanada','USA, Canada');
    // INSERT INTO salys VALUES ('Jav, Vok','JAV, Vokietija','USA, Germany');
    // INSERT INTO salys VALUES ('Jav-Ital','JAV, Italija','USA, Italy');
    // INSERT INTO salys VALUES ('UK-PRA','DIDÞIOJI BRITANIJA, PRANCÛZIJA','UK, FRANCE');
    // INSERT INTO salys VALUES ('UK-German','Didþioji Britanija, Vokietija','UK, Germany');
    // INSERT INTO salys VALUES ('JAV/G','JAV, Vokietija','USA, Germany');
    // INSERT INTO salys VALUES ('F-M-UK','Prancûzija, Meksika, Anglija','France, Mexico, United Kingdom');
    // INSERT INTO salys VALUES ('Jav-Ir','JAV, AIRIJA','USA, Ireland');
    // INSERT INTO salys VALUES ('Fr-Sp','Prancûzija, Ispanija','France, Spain');
    // INSERT INTO salys VALUES ('Au-Uk-Fr','Australija, Anglija, Prancûzija','Australia, England, France');
    // INSERT INTO salys VALUES ('Fr-It-UK','PRANCÛZIJA, ITALIJA, DIDÞIOJI BRITANIJA','France, Italy, UK');
    // INSERT INTO salys VALUES ('Ju-Fr','Jugoslavija, Prancûzija','Jugoslavia, France');
    // INSERT INTO salys VALUES ('Ela','JAV, Airija, Didþioji Britanija','USA, Ireland, UK');
    // INSERT INTO salys VALUES ('ciuidad','JAV, Brazilija, Prancûzija','USA, Brazil, France');
    // INSERT INTO salys VALUES ('Fr-UK','Prancûzija, Didþioji Britanija','France, United Kingdom');
    // INSERT INTO salys VALUES ('SpyBound','PRANCÛZIJA/ ITALIJA/ ISPANIJA','France, Italy, Spain');
    // INSERT INTO salys VALUES ('Jav, czek','JAV, Èekija','USA / Czech Republic');
    // INSERT INTO salys VALUES ('vok-tu','Vokietija, Turkija','Germany, Turkey');
    // INSERT INTO salys VALUES ('alien','Èekija,Kanada, Vokietija, JAV','Czekh Republic, Canada, Germany, USA');
    // INSERT INTO salys VALUES ('Term','Anglø, rusø, prancûzø, bulgarø','English, Russian, French, Bulgarian');
    // INSERT INTO salys VALUES ('odskan','Danija, Ðvedija','Denmark, Sweden');
    // INSERT INTO salys VALUES ('alex','JAV, Didþioji Britanija, Vokietija, Olandija','USA, UK, Germany, Neverlands');
    // INSERT INTO salys VALUES ('dot','Didþioji Britanija, Ispanija, JAV','UK, Spain, USA');
    // INSERT INTO salys VALUES ('usa-vok-f','JAV, Prancûzija, Vokietija','USA, France, Germany');
    // INSERT INTO salys VALUES ('piet-kore','Pietø korëja','South Korea');
    // INSERT INTO salys VALUES ('jav-uk','JAV, Didþioji Britanija','USA, UK');
    // INSERT INTO salys VALUES ('jav-jap','JAV, Japonija','USA, Japan');
    // INSERT INTO salys VALUES ('us-neth-u','DIDÞIOJI BRITANIJA-JAV-OLANDIJA','The Netherlands, UK, USA');
    // INSERT INTO salys VALUES ('oushen','Australija, JAV','Australia, USA');
    // INSERT INTO salys VALUES ('jav-uk-de','Didþioji Britanija, JAV, Vokietija','Germany, UK, USA');
    // INSERT INTO salys VALUES ('UK-Lux','Didþioji Britanija, Liuksemburgas','Luxemburg, UK');
    // INSERT INTO salys VALUES ('jav-veng','JAV, Vengrija','Hungary, USA');
    // INSERT INTO salys VALUES ('merkant','Didþioji Britanij, Italija, JAV, Liuksemburgas','UK, Italy, Luxembourg, USA');
    // INSERT INTO salys VALUES ('2046','Honkongas, Kinija, Prancûzija, Vokietija','HonKong, China, France, Germany');
    // INSERT INTO salys VALUES ('evil2','Didþioji Britanija/Kanada/Prancûzija/Vokietija','UK, Canada, France, Germany');
    // INSERT INTO salys VALUES ('Au-Vok','Austrija, Vokietija','Austria, Germany');
    // INSERT INTO salys VALUES ('F-I-I','Ispanija, Italija, Prancûzija','France, Italy, Spain');
    // INSERT INTO salys VALUES ('jav-af','JAV/Pietø Afrikos Respublika','South Africa, USA');
    // INSERT INTO salys VALUES ('reix','Austrija, Italija, Vokietija','Austria, Germany,Italy');
    // INSERT INTO salys VALUES ('jav-pr','JAV/Prancûzija','France, USA');
    // INSERT INTO salys VALUES ('boogie','JAV, Naujoji Zelandija, Vokietija','Germany, New Zealand, USA');
    // INSERT INTO salys VALUES ('uk-usa-ca','Didþioji Britanija, JAV, Kanada','Canada, UK, USA');
    // INSERT INTO salys VALUES ('aviator','JAV, JAPONIJA, VOKIETIJA','Germany, Japan, USA');
    // INSERT INTO salys VALUES ('hon-kin','HONKONGAS/KINIJA','China, Honkong');
    // INSERT INTO salys VALUES ('Sp-usa','Ispanija/JAV','Spain, USA');
    // INSERT INTO salys VALUES ('dead-land','JAV, Kanada, Prancûzija,','USA, Canada, France');
    // INSERT INTO salys VALUES ('manderlay','Danija, Ðvedija, Nyderlandai, Prancûzija, Vokietija, JAV','Denmark, Sweden, Netherlands, France, germany, USA');
    // INSERT INTO salys VALUES ('villa','Austrija, Ðveicarija','Austria/Switzerland');
    // INSERT INTO salys VALUES ('gabriele','Vokietija, Prancûzija, Italija','Germany, France, Italy');
    // INSERT INTO salys VALUES ('byke','JAV, Vokietija, Anglija, Argentina, Èilë, Peru, Prancûzija','USA, Germany, UK, Argentine, Chile, Peru, France');
    // INSERT INTO salys VALUES ('PL-Ru-It','Lenkija, Rusija, Italija','Poland, Russia, Italy');
    // INSERT INTO salys VALUES ('Ru-It-Fr','Rusija, Italija, Prancûzija','Russia, France, Italy');
    // INSERT INTO salys VALUES ('Pkor-Ja','Pietø Korëja, Japonija','South Korea, Japan');
    // INSERT INTO salys VALUES ('UK-CZ-F-I','Anglija, Èekijos Respublika, Prancûzija, Italija','United Kingdom, Czech Republic, France, Italy');
    // INSERT INTO salys VALUES ('It','Italija','Italian');
    // INSERT INTO salys VALUES ('D-I-J-Uk','Vokietija, Ispanija, JAV, Didþioji Britanija','Germany, Spain, United States of America, Great Britain');
    // INSERT INTO salys VALUES ('DE-UK-JAV','Vokietija, Anglija, Jungtinës Amerikos Valstijos','Germany, United Kingdom, United States of America');
    // INSERT INTO salys VALUES ('Èek','Èekija','Czech');

    // INSERT INTO salys VALUES ('Serb-Fran','Serbija, Prancûzija','Serbian, France');

    // INSERT INTO salys VALUES ('FR, UK, C','Prancûzija, Didþioji Britanija, Èekija','France, UK, Chezch');
    // INSERT INTO salys VALUES ('USA-HK','Hong Kongas, JAV','Hong Kong-USA');
    // INSERT INTO salys VALUES ('CAN-UK','Kanada, Anglija','Canada, UK');
    // INSERT INTO salys VALUES ('Èek-Slov','Èekija, Slovakija','Czech, Slovakia');

    // INSERT INTO salys VALUES ('RU-FRA','Rusija, Prancûzija','Russian, France');
    // INSERT INTO salys VALUES ('RU-UKR','Rusija, Ukraina','Russian, Ukraine');
    // INSERT INTO salys VALUES ('BR-IT-FR','Didþioji Britanija, Italija, Prancûzija','United Kingdom, Italy, France');
    // INSERT INTO salys VALUES ('UK-TH','UK, Tailandas','UK, Thailand');

    // INSERT INTO salys VALUES ('FR-IT','Prancûzija-Italija','France-Italy');
    // INSERT INTO salys VALUES ('F-D-G-I','Suomija, Danija, Vokietija, Airija','Finland, Denmark, Germany, Ireland');

}
global $FilmRegister;
$FilmRegister = new FilmRegister();
$FilmRegister->init();
?>
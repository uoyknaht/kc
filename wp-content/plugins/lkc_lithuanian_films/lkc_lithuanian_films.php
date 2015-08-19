<?php
/*
Plugin Name: LKC lithuanian films
Plugin URI: 
Description: 
Version: 1.0
Author: Infoface
Author URI: http://infoface.lt
License: 
*/

/** 
 * Class LithuanianFilms
 *
 * Other code changes across site that are needed for this to work, appart from this class
 *  - static vars which page id holds the films list shortcode
 *  - jsVars.ajaxUrl
 *  - admin settings page styles in lkc theme admin.custom.css
 *
 * Things that are taken from theme, so would not work with other themes
 *  - styles in app.css
 */

class LithuanianFilms {

    /**
     * Plugin films custom post type slug
     */     
    var $cptSlug = 'lithfilm';

    /**
     * Plugin slug
     */     
    var $pluginSlug = 'lkc_lithuanian_films';

    /**
     * Plugin meta fields prefix
     */     
    var $mfp = '_lkc_lithfilm_';

    /**
     * Plugin options, managed through Wordpress API
     */  
    var $options;
    var $optionsSlug = '_lkc_lithfilm_options';
    var $optionSlugSeparateHomeFilmsByType = 'separateHomeFilmsByTypes';
    var $optionSlugHomeFilmsWhichYear = 'homeFilmsWhichYear';
    var $settingsPageSlug = 'lithfilm-settings';


    /**
     * Meta field which shows that this film was imported, not added via WP admin
     */
    var $importedMetaField = '_lkc_lithfilm_imported';


    ///////////////////////////////////////////////////////////////////////
    // Data related variables and methods
    ///////////////////////////////////////////////////////////////////////

    /**
     * CPT fields, kept in DB, array
     */

    var $fields = array(
        'title' => array(
          'metaType' => 'main', 
          'label' => 'Pavadinimas', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'text',
          'searchHandleType' => 's',
          'searchInputHtmlType' => 'text',
          'class' => 'input-block-level',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
        //   'backEndExplanation' => '',
          // 'showInExcerpt' => false,
          // 'showInMetaContent' => false,
        ),
        'content' => array(
          'metaType' => 'main', 
          'label' => 'Aprašymas', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => false,
          'inputType' => 'text',
          'searchHandleType' => 's',
          'searchInputHtmlType' => 'text',
          'class' => '',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
        //   'backEndExplanation' => '',
        ),
        'production_status' => array(
          'metaType' => 'meta', 
          'label' => 'Statusas', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'radio',
          'searchHandleType' => 'metaQueryEqual',
          'searchInputHtmlType' => 'select',
          'class' => 'select2',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
        //   'backEndExplanation' => '',
          'inputOptions' => array(),
        ),
        'production_date' => array(
          'metaType' => 'meta', 
          'label' => 'Pagaminimo metai', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'text',
          'searchHandleType' => 'metaQueryBetween',
          'searchInputHtmlType' => 'textBetween',
          'class' => '',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
        //   'backEndExplanation' => '',
        ),
        'duration_type' => array(
          'metaType' => 'meta', 
          'label' => 'Tipas pagal trukmę', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'radio',
          'searchHandleType' => 'metaQueryEqual',
          'searchInputHtmlType' => 'select',
          'class' => 'select2',
          'placeholder' => '',
          // 'disabledForEdit' => true,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
          'backEndExplanation' => 'Filmas automatiškai išsaugomas kaip ilgametražis jei jo trukmė ilgesnė nei 60 min.',
          'inputOptions' => array(),
        ),
        'duration' => array(
          'metaType' => 'meta', 
          'label' => 'Trukmė min.', // search is executed by converting minutes to seconds
          'showInFrontEnd' => false, 
          'showInSearchForm' => true,
          'inputType' => 'textTimeFormat',
          'searchHandleType' => 'metaQueryTimeBetween',
          'searchInputHtmlType' => 'textBetween',
          'class' => 'input-mini',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
          'backEndExplanation' => 'Privalomas formatas: HH:MM:SS',
        ),
        'is_colored' => array(
          'metaType' => 'meta', 
          'label' => 'Spalvotas/nespalvotas', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'checkbox',
          'multipleCheckbox' => true,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlType' => 'select',
          'class' => 'select2',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
        //   'backEndExplanation' => '',
          'inputOptions' => array(),
        ),        
        'coproduction_country' => array(
          'metaType' => 'meta', 
          'label' => 'Bendragamintojų valstybė', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'select',
          'multipleSelect' => true,
          'searchHandleType' => 'metaQueryLike',
          // 'searchHandleType' => 'metaQueryLikeMultipleSelect',
          'searchInputHtmlType' => 'select',
          'class' => 'select2',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
        //   'backEndExplanation' => '',
          'inputOptions' => array(),
        ),
        'original_language' => array(
          'metaType' => 'meta', 
          //'metaFieldMultiple' => true, 
          'label' => 'Originalo kalba', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'select',
          'multipleSelect' => true,
          'searchHandleType' => 'metaQueryLike',
          // 'searchHandleType' => 'metaQueryLikeMultipleSelect',
          'searchInputHtmlType' => 'select',
          'class' => 'select2',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
        //   'backEndExplanation' => '',
        ),
        'excerpt' => array(
          'metaType' => 'meta', 
          'label' => 'Tituliniame puslapyje rodoma informacija', 
          'showInFrontEnd' => false, 
          'showInSearchForm' => false,
          'inputType' => 'text',
          'searchHandleType' => '',
          'searchInputHtmlType' => '',
          'class' => '',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
          'backEndExplanation' => 'Jei paliksite tuščią, bus rodoma standartinė informacija',
        ),        
        // 'co_produce_company' => array(
        //   'metaType' => 'taxonomyNotHierarchical', 
        //   'label' => 'Ko-prodiuserinė įmonė', 
        //   'showInFrontEnd' => true, 
        //   'showInSearchForm' => true,
        //   'inputType' => 'text',
        //   'searchHandleType' => 'taxonomyNotHierarchical',
        //   'searchInputHtmlType' => 'multipleSelect',
        //   'class' => '',
        //   'placeholder' => '',
        //   'disabledForEdit' => false,
        //   'icon' =>  false,
        //   'multilingualUsingPolylang' => false,
        // //   'backEndExplanation' => '',
        //   'taxonomySlug' => 'lithfilm-co_produce_company',
        //   'taxonomyPluralName' => 'Ko-prodiuserinės įmonės',          
        // ),
        'type' => array(
          'metaType' => 'taxonomy', 
          'label' => 'Rūšis', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'multipleSelect',
          'searchHandleType' => 'taxonomy',
          'searchInputHtmlType' => 'multipleSelect',
          'class' => '',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => true,
        //   'backEndExplanation' => '',
          'taxonomySlug' => 'lithfilm-type',
          'inputOptions' => array(),
          'legacyTaxonomy' => 'film-categories'
        ),
        'genre' => array(
          'metaType' => 'taxonomy', 
          'label' => 'Žanras', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'multipleSelect',
          'searchHandleType' => 'taxonomy',
          'searchInputHtmlType' => 'multipleSelect',
          'class' => '',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => true,
        //   'backEndExplanation' => '',
          'taxonomySlug' => 'lithfilm-genre',
          'inputOptions' => array(),
        ),
        'show_rights' => array(
          'metaType' => 'taxonomyNotHierarchical', 
          'metaFieldMultiple' => true, 
          'label' => 'Rodymo teisės', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'text',
          'searchHandleType' => 'taxonomyNotHierarchical',
          'searchInputHtmlType' => 'multipleSelect',
          'class' => '',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => true,
        //   'backEndExplanation' => '',
          'taxonomySlug' => 'lithfilm-show-rights',
          'taxonomyPluralName' => 'Rodymo teisės',
        ),
        'asset_rights' => array(
          'metaType' => 'taxonomyNotHierarchical', 
          'metaFieldMultiple' => true, 
          'label' => 'Turtinės teisės', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'text',
          'searchHandleType' => 'taxonomyNotHierarchical',
          'searchInputHtmlType' => 'multipleSelect',
          'class' => '',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => true,
        //   'backEndExplanation' => '',
          'taxonomySlug' => 'lithfilm-asset-ights',
          'taxonomyPluralName' => 'Turtinės teisės',
        ),        
        'format_and_subtitles' => array(
          'metaType' => 'taxonomyNotHierarchical', 
          'metaFieldMultiple' => true, 
          'label' => 'Formatas ir titrai', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'text',
          'searchHandleType' => 'taxonomyNotHierarchical',
          'searchInputHtmlType' => 'multipleSelect',
          'class' => '',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
        //   'backEndExplanation' => '',
          'taxonomySlug' => 'lithfilm-format_and_subtitles',
          'taxonomyPluralName' => 'Formatas ir titrai',
        ),
        'director' => array(
          'metaType' => 'taxonomyNotHierarchical', 
          'metaFieldMultiple' => true, 
          'label' => 'Režisierius', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'text',
          'searchHandleType' => 'taxonomyNotHierarchical',
          'searchInputHtmlType' => 'multipleSelect',
          'class' => '',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
        //   'backEndExplanation' => '',
          'taxonomySlug' => 'lithfilm-director',
          'taxonomyPluralName' => 'Režisieriai',
          'legacyTaxonomy' => 'film-director'
        ),
        'actor' => array(
          'metaType' => 'taxonomyNotHierarchical', 
          'metaFieldMultiple' => true, 
          'label' => 'Aktorius', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'text',
          'searchHandleType' => 'taxonomyNotHierarchical',
          'searchInputHtmlType' => 'multipleSelect',
          'class' => '',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
        //   'backEndExplanation' => '',
          'taxonomySlug' => 'lithfilm-actor',
          'taxonomyPluralName' => 'Aktoriai',
        ),
        'producer' => array(
          'metaType' => 'taxonomyNotHierarchical', 
          'label' => 'Prodiuseris', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'text',
          'searchHandleType' => 'taxonomyNotHierarchical',
          'searchInputHtmlType' => 'multipleSelect',
          'class' => '',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
        //   'backEndExplanation' => '',
          'taxonomySlug' => 'lithfilm-producer',
          'taxonomyPluralName' => 'Prodiuseriai',          
          'legacyTaxonomy' => 'film-producer'
        ),
        'produce_company' => array(
          'metaType' => 'taxonomyNotHierarchical', 
          'label' => 'Filmo prodiuserinė įmonė', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'text',
          'searchHandleType' => 'taxonomyNotHierarchical',
          'searchInputHtmlType' => 'multipleSelect',
          'class' => '',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
        //   'backEndExplanation' => '',
          'taxonomySlug' => 'lithfilm-produce_company',
          'taxonomyPluralName' => 'Filmo prodiuserinės įmonės',           
          'legacyTaxonomy' => 'film-production'
        ),
        'scenarist' => array(
          'metaType' => 'taxonomyNotHierarchical', 
          'label' => 'Scenaristas', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'text',
          'searchHandleType' => 'taxonomyNotHierarchical',
          'searchInputHtmlType' => 'multipleSelect',
          'class' => '',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
        //   'backEndExplanation' => '',
          'taxonomySlug' => 'lithfilm-scenarist',
          'taxonomyPluralName' => 'Scenaristai',            
        ),
        'operator' => array(
          'metaType' => 'taxonomyNotHierarchical', 
          'label' => 'Operatorius', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'text',
          'searchHandleType' => 'taxonomyNotHierarchical',
          'searchInputHtmlType' => 'multipleSelect',
          'class' => '',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
        //   'backEndExplanation' => '',
          'taxonomySlug' => 'lithfilm-operator',
          'taxonomyPluralName' => 'Operatoriai',            
        ),
        'montator' => array(
          'metaType' => 'taxonomyNotHierarchical', 
          'label' => 'Montuotojas', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'text',
          'searchHandleType' => 'taxonomyNotHierarchical',
          'searchInputHtmlType' => 'multipleSelect',
          'class' => '',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
        //   'backEndExplanation' => '',
          'taxonomySlug' => 'lithfilm-montator',
          'taxonomyPluralName' => 'Montuotojai',            
        ),
        'compozitor' => array(
          'metaType' => 'taxonomyNotHierarchical', 
          'label' => 'Kompozitorius', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'text',
          'searchHandleType' => 'taxonomyNotHierarchical',
          'searchInputHtmlType' => 'multipleSelect',
          'class' => '',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
        //   'backEndExplanation' => '',
          'taxonomySlug' => 'lithfilm-compozitor',
          'taxonomyPluralName' => 'Kompozitoriai',            
        ),
        'artist' => array(
          'metaType' => 'taxonomyNotHierarchical', 
          'label' => 'Dailininkas', 
          'showInFrontEnd' => true, 
          'showInSearchForm' => true,
          'inputType' => 'text',
          'searchHandleType' => 'taxonomyNotHierarchical',
          'searchInputHtmlType' => 'multipleSelect',
          'class' => '',
          'placeholder' => '',
          'disabledForEdit' => false,
          'icon' =>  false,
          'multilingualUsingPolylang' => false,
        //   'backEndExplanation' => '',
          'taxonomySlug' => 'lithfilm-artist',
          'taxonomyPluralName' => 'Dailininkai',            
        ),
    );

    function populateProductionStatusInputOptions(){
        $options = array(
            '' => pll__('Pasirinkite') . '...',
            'producible' => pll__('Gaminamas'),
            'produced' => pll__('Pagamintas'),
        );

        return $options;
    }

    function populateDurationTypeInputOptions(){
        $options = array(
            '' => pll__('Pasirinkite') . '...',
            'full_meter' => pll__('Ilgametražis'),
            'short_meter' => pll__('Trumpametražis'),
        );

        return $options;
    }


    function populateColoredInputOptions(){
        $options = array(
            '' => pll__('Pasirinkite') . '...',
            'colored' => pll__('Spalvotas'),
            'bw' => pll__('Nespalvotas'),
        );

        return $options;
    }

    function populateCoproductionCountryInputOptions(){

        include_once('data.php');
        
        $options = array(
            // '' => pll__('Ko-produkcijos valstybė') . '...',
            '' => pll__('Pasirinkite') . '...',
        );

        // error check just in case
        if (in_array(pll_current_language('slug'), array('lt', 'en'))) {
            $langSlug = pll_current_language('slug');
        } else {
            $langSlug = 'lt';
        }

        $countriesList = $countries[$langSlug];

        $options = array_merge($options, $countriesList);

        return $options;
    }

    function populateLanguageInputOptions(){

        include('data.php');
        
        $options = array(
            '' => pll__('Pasirinkite') . '...',
        );

        // error check just in case
        if (in_array(pll_current_language('slug'), array('lt', 'en'))) {
            $langSlug = pll_current_language('slug');
        } else {
            $langSlug = 'lt';
        }

        $languages = $languages[$langSlug];

        $options = array_merge($options, $languages);

        return $options;
    }
    // function populateOriginalLanguageCountryInputOptions(){

    //     include_once('data.php');
        
    //     $options = array(
    //         // '' => pll__('Ko-produkcijos valstybė') . '...',
    //         '' => pll__('Pasirinkite') . '...',
    //     );

    //     $options = array_merge($options, $countryList);

    //     return $options;
    // }

    function getDropdownOptionsFromTaxonomy($taxonomy){
        $taxonomyObject = get_terms($taxonomy, array('hide_empty' => 0));
        // $options = array('' => pll__('Pasirinkite') . '...');
        $options = array();
        foreach ($taxonomyObject as $key => $val) {
            $options[$val->slug] = $val->name; 
        }
        return $options;
    }


    ///////////////////////////////////////////////////////////////////////
    // Plugin initialization methods
    ///////////////////////////////////////////////////////////////////////

    function __construct() {
        $this->pluginUrl = plugins_url( null, __FILE__ );
        $this->options = get_option($this->optionsSlug);
    }

    /**
     * Method to start plugin
     */
    function init(){
      $this->registerHooks();
      // $this->registerImportHooks();
    }

    /**
     * Need to populate $this->fields data after init hook
     * only then custom taxonomies are available
     */
    function afterInit(){
        $this->populateDynamicFieldsData();
    }

    function registerHooks() {
        add_action('init', array(&$this, 'createCpt'));
        add_action('init', array(&$this, 'metaboxSetup'));
        add_action('init', array(&$this, 'registerStrings'));
        // add_action('admin_init', array(&$this, 'registerStrings'));
        
        add_action('wp_loaded', array(&$this, 'afterInit'));
        add_action('admin_init', array(&$this, 'enqueueBackendAssets'));
        add_action('admin_init', array(&$this, 'initOptions'));
        add_action('admin_menu', array(&$this, 'additionalAdminMenuPages'));
        add_action('template_redirect', array(&$this, 'templateRedirect'));

        add_shortcode("lithuanian_films_paieskos_forma",  array(&$this, 'shortcodeSearchFrom')); 
        add_shortcode("lithuanian_films_sarasas",  array(&$this, 'shortcodeFilmsList')); 
        add_shortcode("lithuanian_films_gaminami_sarasas",  array(&$this, 'shortcodeFilmsProducibleList')); 
        add_shortcode("lithuanian_films_filmo_informacija",  array(&$this, 'shortcodeSingleFilm')); 
        
        // add_filter("film_register_search",  array(&$this, 'shortcodeFilmRegisterSearch')); 

        // need to add this, but breaks plugin. Need to investigate
        // if(!is_admin()){
        //     add_action('posts_clauses', array(&$this, 'posts_clauses_with_tax'), 10, 2);
        // }


        add_action('wp_ajax_lithfilmsSearchAutocompleteResponse', array(&$this, 'lithfilmsSearchAutocompleteResponse'));
        add_action('wp_ajax_nopriv_lithfilmsSearchAutocompleteResponse', array(&$this, 'lithfilmsSearchAutocompleteResponse'));

        // add_action('wp_ajax_filterCraftListResponse', array(&$this, 'filterCraftListResponse'));
        // add_action('wp_ajax_nopriv_filterCraftListResponse', array(&$this, 'filterCraftListResponse'));

        // add_action('wp_ajax_filterCraftsMapResponse', array(&$this, 'filterCraftsMapResponse'));
        // add_action('wp_ajax_nopriv_filterCraftsMapResponse', array(&$this, 'filterCraftsMapResponse'));
    }

    function populateDynamicFieldsData() {
        $this->fields['production_status']['inputOptions'] = $this->populateProductionStatusInputOptions();
        $this->fields['duration_type']['inputOptions'] = $this->populateDurationTypeInputOptions();
        $this->fields['is_colored']['inputOptions'] = $this->populateColoredInputOptions();     
        
        $this->fields['coproduction_country']['inputOptions'] = $this->populateCoproductionCountryInputOptions();
        $this->fields['original_language']['inputOptions'] = $this->populateLanguageInputOptions();

        // populated on demand:
        // $this->fields[$this->cptSlug . '-type']['dropdownOptions'] = $this->getDropdownOptionsFromTaxonomy($this->cptSlug . '-type');
        // $this->fields[$this->cptSlug . '-genre']['dropdownOptions'] = $this->getDropdownOptionsFromTaxonomy($this->cptSlug . '-genre');

        foreach ($this->fields as $key => $val) {
            $this->fields[$key]['label'] = pll__($val['label']);
            $this->fields[$key]['showInSearchForm'] = $this->options['showInSearchForm_' . $key];
            $this->fields[$key]['showInExcerpt'] = $this->options['showInExcerpt_' . $key];
            $this->fields[$key]['showInMetaContent'] = $this->options['showInMetaContent_' . $key];
        }
    }

    function createCpt() {

        $labels = array(
            'menu_name' => __('Lithuanian films', 'kcsite'),
            'name' => _x(__('Lithuanian films', 'kcsite'), 'post type general name'),
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
            'supports' => array('title', 'editor', 'thumbnail'),
            'rewrite' => array('slug' => 'lithuanian-films', 'with_front' => true),
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

        foreach ($this->fields as $key => $val) {
            if ($val['metaType'] === 'taxonomyNotHierarchical') {

                register_taxonomy(
                  $val['taxonomySlug'], 
                  $this->cptSlug,
                  array(
                    'label' =>  $val['label'], 
                    'labels' => array(
                      'name' => $val['taxonomyPluralName'],
                      'singular_name' => $val['label'],
                      // 'menu_name' => 'Filmų žanrai',
                      // 'all_items' => 'Visi žanrai',
                      // 'edit_item' => 'Redaguoti žanrą',
                      // 'view_item' => 'Žiūrėti žanrą',
                      // 'update_item' => 'Atnaujinti žanrą',
                      // 'add_new_item' => 'Kurti naują žanrą',
                      // 'new_item_name' => 'Naujos žanro pavadinimas',
                      // 'parent_item' => 'Tėvinė žanras',
                      // 'parent_item_colon' => 'Tėvinė žanras:',
                      // 'search_items' => 'Ieškoti žanruose',
                    ),
                    'hierarchical' => false, 
                    // 'show_admin_column' => true,
                    // 'show_ui' => false, 
                    // 'query_var' => 'film-genre'
                  )
                );

            }     
        }


    }

    ///////////////////////////////////////////////////////////////////////
    // Localization
    ///////////////////////////////////////////////////////////////////////

    function registerStrings(){    
        pll_register_string('Lithuanian films', 'Pasirinkite');
        pll_register_string('Lithuanian films', 'Ilgametražis');
        pll_register_string('Lithuanian films', 'Trumpametražis');
        pll_register_string('Lithuanian films', 'Spalvotas');
        pll_register_string('Lithuanian films', 'Nespalvotas');
        pll_register_string('Lithuanian films', 'Gaminamas');
        pll_register_string('Lithuanian films', 'Pagamintas');
        pll_register_string('Lithuanian films', 'Paieškos forma');
        pll_register_string('Lithuanian films', 'Filtruoti');
        pll_register_string('Lithuanian films', 'nuo');
        pll_register_string('Lithuanian films', 'iki');
        pll_register_string('Lithuanian films', 'Pagal pasirinktus paieškos kriterijus filmų nerasta');
        pll_register_string('Lithuanian films', 'Rasta filmų');

        foreach ($this->fields as $key => $val) {
            pll_register_string('Lithuanian films', $val['label']);
        }
    }


    ///////////////////////////////////////////////////////////////////////
    // Meta fields, implemented with WPAlchemy class
    ///////////////////////////////////////////////////////////////////////

    function metaboxSetup(){ 
      if(!class_exists('WPAlchemy_MetaBox')){
        include_once 'vendors/wpalchemy/MetaBox.php';
      }

      global $g_lithuanianFilmsMetabox;
      $g_lithuanianFilmsMetabox = new WPAlchemy_MetaBox(array(
        'id' => $this->mfp . 'meta',
        'title' => 'Filmo duomenys',
        'template' => dirname(__FILE__).'/metaboxes/lkc-film-meta.php',
        'types' => array($this->cptSlug),
        'mode' => WPALCHEMY_MODE_EXTRACT,
        'prefix' => $this->mfp,
        'save_filter' => array(&$this, 'metaboxSaveFilter'),
      ));
    }

    function convertTimeToSeconds($time) {
        $pattern = '/^(?:(?:([01]?\d|2[0-3]):)?([0-5]?\d):)?([0-5]?\d)$/';
        if (preg_match($pattern, $time)) {
            $timeArray = explode(':', $time);
            $seconds = $timeArray[0]*60*60 + $timeArray[1]*60 + $timeArray[2];
            return $seconds;
        }
        return false;
    }

    function convertSecondsToTime($sec, $padHours = true) {
        // return date('h:i:s', mktime(0,0, round($seconds) % (24*3600)));        

        // http://snipplr.com/view/4688/

        $hms = "";
        
        // there are 3600 seconds in an hour, so if we
        // divide total seconds by 3600 and throw away
        // the remainder, we've got the number of hours
        $hours = intval(intval($sec) / 3600); 

        // add to $hms, with a leading 0 if asked for
        $hms .= ($padHours) 
              ? str_pad($hours, 2, "0", STR_PAD_LEFT). ':'
              : $hours. ':';
         
        // dividing the total seconds by 60 will give us
        // the number of minutes, but we're interested in 
        // minutes past the hour: to get that, we need to 
        // divide by 60 again and keep the remainder
        $minutes = intval(($sec / 60) % 60); 

        // then add to $hms (with a leading 0 if needed)
        $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';

        // seconds are simple - just divide the total
        // seconds by 60 and keep the remainder
        $seconds = intval($sec % 60); 

        // add to $hms, again with a leading 0 if needed
        $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

        return $hms;        
    }

    function convertSecondsToMinutes($sec) {
        $min = round($sec/60);

        return $min;
    }

    function convertDateToYear($date) {
        if ($date) {
            $year = date('Y', strtotime($date));
            return $year;
        }

        return '';
    }

    function convertDateToTimeStamp($date) {
        return strtotime($date);
    }

    function convertTimeStampToDate($timestamp) {
        // print_r($timestamp);exit;
        return date('Y-m-d', $timestamp);
    }

    function checkIfFullMeter($durationInSec) {
        if ($durationInSec / 60 > 60) {
            return true;
        }
        return false;
    }

    function metaboxSaveFilter($meta, $post_id){

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (wp_is_post_revision($post_id)){
            return;
        }

        if (!current_user_can('edit_post', $post_id) || $_POST['post_type'] != $this->cptSlug){
            return;
        }

        // manage duration field:

        if(isset($meta['duration']) && !empty($meta['duration'])){ 
            $duration = $meta['duration'];
            $convertedDuration = $this->convertTimeToSeconds($duration);
            if ($convertedDuration) {
                $meta['duration'] = $convertedDuration;

                // automatic check not needed
                // $typeByDuration = $this->checkIfFullMeter($convertedDuration) ? 'full_meter' : 'short_meter';
                // $meta['duration_type'] = $typeByDuration;
            } else {
                $meta['duration'] = 'Netinkamas formatas: ' . $duration;
            }
        }

        // if(isset($meta['production_date']) && !empty($meta['production_date'])){ 
        //     $date = $meta['production_date'];
        //     $convertedDate = $this->convertDateToTimeStamp($date);
        //     if ($convertedDate) {
        //         $meta['production_date'] = $convertedDate;
        //     } else {
        //         $meta['production_date'] = 'Netinkamas formatas: ' . $date;
        //     }
        // }




        return $meta;
    }

    function generateMetaFieldsForAdmin ($key, $val, $mb) {

        if(isset($val['disabledForEdit']) && $val['disabledForEdit'] == true) {
            $disabledAttr = 'disabled="disabled"';
            $disabledClass = 'disabled';
        } else {
            $disabledAttr = '';
            $disabledClass = '';
        }

        if(isset($val['multipleSelect']) && $val['multipleSelect'] == true){
            $mb->the_field($key, WPALCHEMY_FIELD_HINT_SELECT_MULTI); 
        } else if(isset($val['multipleCheckbox']) && $val['multipleCheckbox'] == true){
            $mb->the_field($key, WPALCHEMY_FIELD_HINT_CHECKBOX_MULTI); 
        } else {
            $mb->the_field($key); 
        } ?>

            <?php

            switch ($val['inputType']) {
                case 'text':
                    ?> 
                    <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" class="input-type-<?php echo $val['inputType'];?> regular-text <?php echo $disabledClass;?>" <?php echo $disabledAttr;?> />
                    <?php
                    break;
                case 'textTimeFormat':
                    $value = $mb->get_the_value();
                    if ($value) $value = $this->convertSecondsToTime($value);
                    ?> 
                    <input type="text" name="<?php $mb->the_name(); ?>" value="<?php echo $value; ?>" class="input-type-<?php echo $val['inputType'];?> regular-text <?php echo $disabledClass;?>" <?php echo $disabledAttr;?> />
                    <?php
                    break;

                case 'date':
                    $value = $mb->get_the_value();                    
                    if ($value) $value = $this->convertTimeStampToDate($value);
                    ?> 
                    <input type="text" name="<?php $mb->the_name(); ?>" value="<?php echo $value; ?>" class="input-type-<?php echo $val['inputType'];?> <?php echo $disabledClass;?>" <?php echo $disabledAttr;?> />
                    <?php
                    break;

                case 'select':
                    ?>
                    <select name="<?php $mb->the_name(); ?>" class="input-type-<?php echo $val['inputType'];?> chosen <?php echo $disabledClass;?>" <?php if(isset($val['multipleSelect']) && $val['multipleSelect'] == true) echo 'multiple="multiple"';?> <?php echo $disabledAttr;?>>
                        <!-- <option value="">---</option> -->
                        <?php 
                        $i = 0;
                        foreach ($val['inputOptions'] as $key2 => $val2) {
                            if($i == 0) {
                                $selectedState = '';
                            } else {
                                $selectedState = $mb->get_the_select_state($key2);
                            }
                            $i++;
                         ?>
                            <option value="<?php echo $key2?>"<?php echo $selectedState; ?>><?php echo $val2;?></option>
                        <?php } ?>
                    </select>
                    <?php
                    break;

                case 'radio':
                case 'checkbox':
                    foreach ($val['inputOptions'] as $key2 => $val2){
                        if($i == 0) {
                            $selectedState = '';
                        } else {
                            $selectedState = $mb->get_the_checkbox_state($key2);
                        }
                        $i++;                        
                        if ($key2 != '') { ?>
                        <?php $mb->the_field($key); ?>
                        <input type="<?php echo $val['inputType'];?>" name="<?php $mb->the_name(); ?><?php if (isset($val['multipleCheckbox']) && $val['multipleCheckbox'] == true) echo '[]';?>" value="<?php echo $key2; ?>" class="<?php echo $disabledClass;?>" <?php echo $disabledAttr;?> <?php echo $selectedState; ?>/> <?php echo $val2; ?>
                    <?php }
                    }
                    break;

                // in case there multiple checkboxes, would need to think how to change input name for every checkbox
                /*case 'checkboxMultiple': 
                    foreach ($val['checkboxOptions'] as $key2 => $val2){ ?>
                        <?php $mb->the_field($key); ?>
                        <input type="checkbox" name="<?php $mb->the_name(); ?>" value="<?php echo $key2; ?>" class="<?php echo $disabledClass;?>" <?php if ($mb->get_the_value()) echo 'checked="checked"'; ?>/> <?php echo $val2; ?>
                    <?php }
                    break;*/
                case 'comprehensiveDuration':
                    ?> 
                    <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" class="input-type-<?php echo $val['inputType'];?> regular-text <?php echo $disabledClass;?>" <?php echo $disabledAttr;?> />
                    <?php


                
                default:
                    break;
            }

  
    }

    ///////////////////////////////////////////////////////////////////////
    // View output related methods
    ///////////////////////////////////////////////////////////////////////

    // http://stackoverflow.com/questions/4647604/wp-use-file-in-plugin-directory-as-custom-page-template
    function templateRedirect(){ 
      global $wp;
      $plugindir = dirname( __FILE__ );

      if ($wp->query_vars["post_type"] == $this->cptSlug) {
        $templatefilename = 'single-'.$this->cptSlug.'.php';
        if (file_exists(TEMPLATEPATH . '/' . $templatefilename)) {
            $return_template = TEMPLATEPATH . '/' . $templatefilename;
        } else {
            $return_template = $plugindir . '/views/templates/' . $templatefilename;
        }
        $this->doRedirect($return_template);
 
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

    function doRedirect($url) {
        global $post, $wp_query;
        if (have_posts()) {
            include($url);
            die();
        } else {
            $wp_query->is_404 = true;
        }
    }

    function enqueueFrontendAssets(){

      if (!is_admin()){
        // wp_register_style('jquery-chosen', $this->pluginUrl . '/vendors/chosen/chosen.min.css');  
        // wp_enqueue_style('jquery-chosen'); 
        
        // wp_register_script('jquery-chosen', $this->pluginUrl . '/vendors/chosen/chosen.jquery.min.js', array('jquery'));  
        // wp_enqueue_script('jquery-chosen'); 
        
        // wp_enqueue_script('lkc-lithuanian-films-frontend', $this->pluginUrl . '/assets/js/frontend.js', array('jquery-ui')); 
      }
    }

    function enqueueBackendAssets(){

      if (is_admin()){
        // wp_register_style('jquery-ui', $this->pluginUrl . '/vendors/jquery-ui.custom/css/smoothness/jquery-ui.custom.min.css');  
        // wp_enqueue_style('jquery-ui'); 

        // wp_register_script('jquery-ui', $this->pluginUrl . '/vendors/jquery-ui.custom/js/jquery-ui.custom.min.js',array('jquery'));  
        // wp_enqueue_script('jquery-ui'); 
        // wp_enqueue_script('jquery-ui-core'); 

        // wp_register_script('jquery-ui-datepicker-lt', $this->pluginUrl . '/vendors/jquery-ui.custom/js/jquery.ui.datepicker-lt.js',array('jquery-ui'));  
        // wp_enqueue_script('jquery-ui-datepicker-lt'); 

        wp_register_style('jquery-chosen', $this->pluginUrl . '/vendors/chosen/chosen.min.css');  
        wp_enqueue_style('jquery-chosen'); 
        
        wp_register_script('jquery-chosen', $this->pluginUrl . '/vendors/chosen/chosen.jquery.min.js', array('jquery'));  
        wp_enqueue_script('jquery-chosen'); 

        // wp_enqueue_script('lkc-lithuanian-films-backend', $this->pluginUrl . '/assets/js/backend.js', array('jquery-ui')); 
      }
    }

    /**
     * Creates button for single film page, that can take back to the previous submit search page with filtering options kept
     */    
    function refererBtn($refererPageId = null){
      if($refererPageId == null){
        global $staticVars;
        $refererPageId = $staticVars['lithuanianFilmsSearchPageId'];
      }

      $refererUrl = get_permalink($refererPageId);
      $wpRefererUrl = wp_get_referer();
      $finalRefererUrl = strpos($wpRefererUrl, $refererUrl) === false ? $refererUrl : $wpRefererUrl ;

      return '<a href="'.$finalRefererUrl.'" class="btn btn-block btn-referer">&laquo; '.pll__('Grįžti į sąrašą').'</a>';
    }

    /**
     * Returns field's value of a given post
     */
    function outputField($post_id, $key){
      
        if($this->fields[$key]['metaType'] == 'main'){
            $output = get_post_field('post_'.$key, $post_id);
        } elseif($this->fields[$key]['metaType'] == 'meta'){
            $output = get_post_meta($post_id, $this->mfp.$key, true);
            if ($output){

                // print_r($output);
                if(is_array($output)) {
                    $stringifiedOutput = '';
                    foreach ($output as $a => $b) {
                        $stringifiedOutput .=  $this->fields[$key]['inputOptions'][$b];
                        if($a+1 != count($output)) $stringifiedOutput .= ', ';
                    }
                    $output = $stringifiedOutput;
                } elseif($this->fields[$key]['inputType'] == 'date' && $output != '' && !is_array($output) && strlen($output) > 10){
                    $output = date('Y-m-d', strtotime($output));
                } elseif(isset($this->fields[$key]['inputOptions'])){
                    $output = $this->fields[$key]['inputOptions'][$output];
                }
                // } elseif($key == 'email'){
                //     if($output != false){
                //         $output = '<a href="mailto:'.$output.'">'.$output.'</a>';
                //     }
                // } elseif($key == 'website'){
                //     if($output != false){
                //         $output = '<a href="http://'.$output.'" target="_blank">'.$output.'</a>';
                //     }
                // } elseif($key == 'production_status'){
                //     $output = 
                // }
            }
        } elseif($this->fields[$key]['metaType'] == 'taxonomy' || $this->fields[$key]['metaType'] == 'taxonomyNotHierarchical'){
            $key = $this->fields[$key]['taxonomySlug'];
            $terms = wp_get_post_terms( $post_id, $key);
            $output = '';
            foreach ($terms as $a => $b) {
              $output .= $b->name;
              if($a+1 != count($terms)) $output .= ', ';
            }
        }
          // if(!is_array($output)) $output = ucfirst($output);
        return $output;
    }


    /**
     * outputs searh form single input field html
     * @param $key
     */
    function outputSearchInput($key){

        $name = $key;

        if($this->fields[$key]['searchInputHtmlType'] == 'textBetween'){
            $this->fields[$key]['nameFrom'] = $key.'_from';
            $this->fields[$key]['placeholderFrom'] = pll__('nuo');
            $this->fields[$key]['nameTo'] = $key.'_to';
            $this->fields[$key]['placeholderTo'] = pll__('iki');
            $this->fields[$key]['class'] .= ' input-mini';
        }

        extract($this->fields[$key]);

        ob_start();

        switch ($searchInputHtmlType) {

            case 'text': ?>
                <input type="text" name="<?php echo $name;?>" value="<?php echo $_GET[$name]; ?>" class="<?php echo $class; ?>" placeholder="<?php echo $placeholder; ?>" />
                <?php break;

            case 'textBetween': ?>
                <input type="text" name="<?php echo $nameFrom;?>" value="<?php echo $_GET[$nameFrom]; ?>" placeholder="<?php echo $placeholderFrom; ?>" class="<?php echo $class; ?>" />
                <input type="text" name="<?php echo $nameTo;?>" value="<?php echo $_GET[$nameTo]; ?>" placeholder="<?php echo $placeholderTo; ?>" class="<?php echo $class; ?>" />
                <?php break;

            case 'select':
            case 'combobox':
            case 'multipleSelect': ?>
                <?php 
                if ($metaType == 'taxonomy' || $metaType == 'taxonomyNotHierarchical' ) {
                    $inputOptions = $this->getDropdownOptionsFromTaxonomy($taxonomySlug);
                } 
                ?>
                <select name="<?php echo $name;?><?php if($searchInputHtmlType == 'multipleSelect') echo '[]';?>" class="<?php echo $class;?> <?php if($searchInputHtmlType == 'multipleSelect') echo 'select2'; ?>" <?php if($searchInputHtmlType == 'multipleSelect') echo 'multiple="multiple"';?> data-placeholder="<?php pll_e('Pasirinkite');?>...">
                  <?php foreach ($inputOptions as $dropdownOptionKey => $dropdownOptionVal) { ?>
                    <option value="<?php echo $dropdownOptionKey; ?>"<?php if(  $_GET[$name] == $dropdownOptionKey || is_array($_GET[$name]) && in_array($dropdownOptionKey, $_GET[$name])    ) echo 'selected="selected"' ?>><?php echo $dropdownOptionVal;?></option>
                  <?php } ?>          
                </select>
                <?php break;
            case 'checkbox': ?>
                <?php foreach ($this->fields[$name]['inputOptions'] as $dropdownOptionKey => $dropdownOptionVal) {
                    if($dropdownOptionKey != ''){ ?>
                        <label class="checkbox">
                            <input type="checkbox" name="<?php echo $key; ?>[]" value="<?php echo $dropdownOptionKey; ?>" <?php if(isset($_GET[$name]) && in_array($dropdownOptionKey, $_GET[$name])) echo 'checked="checked"' ?>>
                            <?php echo $dropdownOptionVal;?>
                        </label>
                    <?php }
                }
                break;
            case 'radio': ?>
                <?php foreach ($this->fields[$name]['inputOptions'] as $dropdownOptionKey => $dropdownOptionVal) {
                    if($dropdownOptionKey != ''){ ?>
                        <label class="radio">
                            <input type="radio" name="<?php echo $key; ?>[]" value="<?php echo $dropdownOptionKey; ?>" <?php if(isset($_GET[$name]) && in_array($dropdownOptionKey, $_GET[$name])) echo 'checked="checked"' ?>>
                            <?php echo $dropdownOptionVal;?>
                        </label>
                    <?php }
                }
                break;
/*            case 'combobox': ?>
                <select name="<?php echo $name;?>" class="<?php echo $class;?> combobox">
                  <?php foreach ($inputOptions as $dropdownOptionKey => $dropdownOptionVal) { ?>
                    <option value="<?php echo $dropdownOptionKey; ?>"<?php if($_GET[$name] == $dropdownOptionKey) echo 'selected="selected"' ?>><?php echo $dropdownOptionVal;?></option>
                  <?php } ?>          
                </select>
                <?php break; */               
        }

      $output = ob_get_clean();

      return $output;
    }

    /**
     * outputs searh form
     * @param $key
     */    
    function outputSearchForm(){
        global $staticVars; 
        $formAction = get_post_type() == $this->cptSlug ? get_permalink($staticVars['lithuanianFilmsSearchPageId']) /* cia gal ne sita reikia naudoti */ :  $_SERVER['REQUEST_URI'];

        ob_start(); ?>

        <aside class="widget">
            <header class="header">
                <h3 class="widget-title"><?php pll_e('Paieškos forma'); ?> </h3>
            </header>

            <div class="content entry-content">

                <form method="get" action="<?php echo $formAction; ?>" class="lithfilms-search-form clearfix">
                    <input type="hidden" name="do" value="search"/>   

                    <?php 
                    foreach ($this->fields as $key => $val) {
                        //echo $key .' ';
                        if($val['showInSearchForm'] == true){ ?>
                            <div class="form-row form-row-<?php echo $key;?>">
                                <?php if (!isset($val['hideLabelInSearchForm']) || $val['hideLabelInSearchForm'] == false) { ?>
                                    <label><?php echo $val['label'];?>:</label>
                                <?php } ?>
                                <?php echo $this->outputSearchInput($key); ?>
                            </div>
                        <?php }
                    } ?>

                    <div class="form-row">
                        <input type="submit" class="btn" value="<?php pll_e('Filtruoti'); ?>" />
                    </div>
                </form>
            </div>
        </aside>

        <script type="text/javascript">//<![CDATA[

        jQuery(document).ready(function($){

            var form = $('.lithfilms-search-form');
            var autocompleteSpinner = form.find('.spinner');
            var ajaxLoader = $('.ajax-loader');
            var ajaxHolder = $('#craft-list-wrap-ajax-holder');

            form.find('input[name=title]').autocomplete({
                source: function(request, response){  
                    request.lang = '<?php echo pll_current_language("slug");?>';
                    $.getJSON(jsVars.ajaxUrl + "?action=lithfilmsSearchAutocompleteResponse", request, function(data) {                          
                        response(data);
                    });
                },
                select: function(event, ui) {
                    if (typeof ui.item.permalink != 'undefined') { 
                        window.location = ui.item.permalink;
                        return false;
                    }       
                },
                minLength: 3,
                search: function(event, ui) { 
                    autocompleteSpinner.show();
                },
                response: function(event, ui) {
                    autocompleteSpinner.hide();
                }
            });

            form.delegate('.input-type-date', 'focusin', function(){
                jQuery(this).datepicker({ 
                  dateFormat: "yy-mm-dd",
                  changeMonth: true,
                  changeYear: true
                  // onClose: function() {
                  //   jQuery('.input-type-date').valid();
                  // }   
                });
            });            

        });

        //]]> </script>

        <?php $output = ob_get_clean();

        return $output;
    }

    /**
     * outputs [filtered] films list
     */
    function outputFilmsList($the_query){ 
    // print_r($the_query);exit; 
        global $staticVars;
        ob_start(); ?>

       <?php if($the_query->post_count < 1) { ?>
            <div class="empty-lithfilm-output-list">
                <p><?php pll_e('Pagal pasirinktus paieškos kriterijus filmų nerasta'); ?></p>
            </div>
        <?php } else { ?>

            <?php 
            if (!isset($_GET['do']) && $this->options[$this->optionSlugSeparateHomeFilmsByType] && get_the_ID() == $staticVars['lithuanianFilmsSearchPageId']) {
                // home landing page, need to show all posts separated by film type

                $output = array();

                while ($the_query->have_posts()){   
                    $the_query->the_post();

                    $postTaxonomies = wp_get_post_terms(get_the_ID(), $this->fields['type']['taxonomySlug']);
                    foreach ($postTaxonomies as $key2 => $val2) {
                        // print_r($val2);exit;
                        if (!isset($output[$val2->term_id])){
                            // doing nice hack:
                            if(pll_current_language('slug') == 'en'){
                                $name = $val2->name . ' films';
                            } else {
                                $name = substr($val2->name, 0, -1) . 'ai filmai';
                            }
                            $output[$val2->term_id] = array('name' => $name, 'content' => '');
                        }
                        
                        $output[$val2->term_id]['content'] .= $this->outputSingleFilmInList();
                    }
                }
                ksort($output);
                // print_r($output);exit;

                // 1.  Pakeisti titulinio puslapio filmų išdėstymo tvarką į:
                // a.  Vaidybiniai
                // b.  Dokumentiniai
                // c.  Animaciniai
                // d.  Eksperimentiniai

                // no other way but hardcode
                $output2 = array();

                if (pll_current_language('slug') == 'en') {
                    $sortedIds = array(
                        130, // vaidybinis
                        129, // dokumentinis
                        128, // animacinis
                        135, // eksperimentinis
                    );
                } else {
                    $sortedIds = array(
                        40, // vaidybinis
                        41, // dokumentinis
                        42, // animacinis
                        134, // eksperimentinis
                    );
                }

                foreach ($sortedIds as $val) {
                    $output2[] = $output[$val]; 
                    unset($output[$val]);
                }

                // if remains types unmentioned in the sorted order
                if (!empty($output)){
                    foreach ($output as $val) {
                        $output2[] = $val; 
                    }                    
                }

                foreach ($output2 as $key => $val) { ?>
                    <h2 class="lithfilm-type-heading"><?php echo $val['name']; ?></h2>
                    <ul class="lithfilm-list-wrap clearfix">
                        <?php echo $val['content']; ?>
                    </ul>
                <?php }
            } else {
                ?>

                <div class="lith-film-list-header">
                    <p><?php echo pll__('Rasta filmų') . ': ' . $the_query->post_count; ?></p>
                </div>

                <ul class="lithfilm-list-wrap clearfix">
                    <?php //print_r($the_query);exit; ?>
                <?php while ($the_query->have_posts()){
                    $the_query->the_post();
                    echo $this->outputSingleFilmInList();
                } ?>
                </ul>
            <?php }

            ?>



        <?php } ?>

        <?php //kcsite_nice_pagination($the_query->max_num_pages); ?>

        <?php $output = ob_get_clean();

        return $output;
    }

    function outputSingleFilmInList(){
        ob_start();

        $post_thumbnail = $this->getFirstPostImg(get_the_ID(), 'thumbnail', array('class' => 'post-thumbnail')); ?>
        <li class="clearfix <?php if($post_thumbnail != false) echo 'has-post-thumbnail';?>">
            <a href="<?php the_permalink();?>" class="not-entry-meta clearfix">
                <?php if($post_thumbnail) { ?>
                    <?php echo $post_thumbnail; ?>
                <?php } ?>
                <div class="right">
                    <h3><?php echo $this->outputField(get_the_ID(), 'title'); ?></h3>
                    <div class="meta-info">
                        <?php echo $this->outputExcerpt(); ?>
                    </div>
                </div>
            </a>
            <?php //get_template_part('template-parts/entry-meta'); ?>
            <?php //edit_post_link( __('Edit', 'kcsite'), '<span class="edit-link">', '</span>' ); ?>
        </li>

        <?php 
        $output = ob_get_clean();

        return $output;
    }

    /**
     * outputs film info in single page content
     */
    function outputSingleFilm(){

        global $post; 
        $post_thumbnail = $this->getFirstPostImg(get_the_ID(), 'feat-thumbnail', array('class' => 'post-thumbnail'), true);
      
        ?>

        <div class="lithfilm-single-film-wrap">
            <div class="lithfilm-single-film-header clearfix <?php if ($post_thumbnail) echo 'has-post-thumbnail'; ?>">

                <?php if($post_thumbnail) { ?>
                        <?php echo $post_thumbnail; ?>
                <?php } ?>  

                <h3><?php echo $this->outputField(get_the_ID(), 'title'); ?></h3>
                <?php echo $this->outputExcerpt(); ?>
            </div>

            <div class="lithfilm-single-film-content clearfix">
                <div class="left">
                    <?php the_content(); ?>
                </div>
                <div class="right">
                    <?php echo $this->outputMetaContent(); ?>
                </div>
            </div>

            <?php
            // if ($gallery) {
            //     echo do_shortcode($gallery);
            // }
            ?>

            <?php echo $this->refererBtn(); ?>


        </div>



        <?php
    }

    /**
     * Convenience function. Return post thumbnail if it exists, otherwise first image if it exists, otherwise false
     */
    function getFirstPostImg($post_id, $size = 'thumbnail', $attr = null, $wrapInLink = false){
        $img = get_the_post_thumbnail($post_id, $size, $attr);
        if($img != ''){
            if ($wrapInLink) {
                $postThumbID = get_post_thumbnail_id($post_id);
                $src = wp_get_attachment_image_src($postThumbID, 'full');
                $src = $src[0];
            }
        } else {
            $args = array(
                'numberposts' => 1,
                'post_parent' => $post_id,
                'post_type' => 'attachment',
                'post_mime_type' => 'image',
                'orderby' => 'menu_order ASC, ID',
                'order' => 'DESC',
            );

            $attachments = get_posts($args);

            if(!empty($attachments)){
                $img = wp_get_attachment_image($attachments[0]->ID, $size, false, $attr);
                if ($wrapInLink) {
                    $postThumbID = $attachments[0]->ID;
                    $src = wp_get_attachment_image_src($postThumbID, 'full');
                    $src = $src[0];
                }                
            } else {
                $img = false;
            }
        }

        if ($img) {
            if ($wrapInLink) {
                $img = '<a href="'.$src.'">' . $img . '</a>';
            }

            return $img;
        }

        return false;
    }

    function outputExcerpt(){
        $metaInfoDefinedManually = $this->outputField(get_the_ID(), 'excerpt');
        if ($metaInfoDefinedManually) {
            return $metaInfoDefinedManually;
        } else {
        //     $year = $this->convertDateToYear($this->outputField(get_the_ID(), 'production_date'));
        //     $duration = $this->convertSecondsToMinutes($this->outputField(get_the_ID(), 'duration'));
        //     $directors = $this->outputField(get_the_ID(), 'director');
            
        //     if ($year) {
        //         echo $year . ' m.';

        //         if ($duration || $directors) {
        //             echo ' / ';
        //         }
        //     }                                

        //     if ($duration) {
        //         echo $duration . ' min.';

        //         if ($directors) {
        //             echo ' / ';
        //         }                                    
        //     }

        //     if ($directors) {
        //         echo $directors;
        //     }
        // }       

            $output = '';
            foreach ($this->fields as $key => $val) {
                if ($val['showInExcerpt'] == true) {
                    $content = $this->outputField(get_the_ID(), $key);
                    if ($content) {

                        if ($key == 'production_date') {
                            //$content = $this->convertDateToYear($content) . ' m.';
                        } else if ($key == 'duration') {
                            $content = $this->convertSecondsToMinutes($content) . ' min.';
                        }



                        $output .= $content . ' / ';
                    }
                }
            }

            if ($output) {
                $output = substr($output, 0, -2);
            }

            return $output;
        }
    }

    function outputMetaContent(){
       // foreach ($this->fields as $key => $val) {
       //     if ($val['showInFrontEnd'] == true) {
       //      echo $val['label'] . ' ' . $this->outputField(get_the_ID(), $key);
       //      echo ' / ';
       //     }
       // }
        ?>
       <ul class="meta-content-list">
        <?php //print_r($this->fields);exit; ?>
       <?php foreach ($this->fields as $key => $val) {
           // if ($val['showInFrontEnd'] == true) {
           if ($val['showInMetaContent'] == true) {
            $content = $this->outputField(get_the_ID(), $key);
            if ($content) { ?>
                <li>
                    <span class="label"><?php echo $val['label']; ?>:</span>
                    <span class="value"><?php echo $content; ?></span>
                </li>
            <?php }
           }
       } ?>
       </ul>
    <?php }


    ///////////////////////////////////////////////////////////////////////
    // Shortcodes
    ///////////////////////////////////////////////////////////////////////

    function shortcodeSearchFrom($atts, $content = null) {
        $output = $this->outputSearchForm();
        return do_shortcode($output); 
    }


    function shortcodeFilmsList($atts, $content = null) {

        // wp_enqueue_style('jquery-ui', $this->pluginUrl . '/vendors/jquery-ui.custom/css/smoothness/jquery-ui.custom.min.css');  
        // wp_enqueue_script('jquery-ui', $this->pluginUrl . '/vendors/jquery-ui.custom/js/jquery-ui.custom.min.js',array('jquery'), '1.0', false);  
        
        if (!$_GET) $_GET = array();
        $_GET['production_status'] = 'produced';        

        // print_r($_GET['do']);exit;

        if (!isset($_GET['do']) && $this->options[$this->optionSlugSeparateHomeFilmsByType] && $this->options[$this->optionSlugHomeFilmsWhichYear]) {
            $_GET['filterByYears'] = $this->options[$this->optionSlugHomeFilmsWhichYear];
        }

        $the_query = $this->prepareQuery($_GET);
        // print_r($the_query);exit;
        ob_start();
        ?>

        <div class="lithfilm-output-list-major-wrap">
            <?php echo $this->outputFilmsList($the_query); ?>
        </div>

        <?php $output = ob_get_clean();

        return do_shortcode($output); 
    }

    function shortcodeFilmsProducibleList($atts, $content = null) {

        if (!$_GET) $_GET = array();
        $_GET['production_status'] = 'producible';

        $the_query = $this->prepareQuery($_GET);
        // print_r($the_query);exit;

        ob_start();
        ?>

        <div class="lithfilm-output-list-wrap">
            <?php echo $this->outputFilmsList($the_query); ?>
        </div>

        <?php $output = ob_get_clean();

        return do_shortcode($output); 

    }

    /**
     * Shortcode for craft DB home page
     */
    function shortcodeSingleFilm($atts, $content = null) {

        return do_shortcode($this->outputSingleFilm()); 
    }



    ///////////////////////////////////////////////////////////////////////
    // Searching related methods
    ///////////////////////////////////////////////////////////////////////


    /**
     * handles search form submission and data querying
     * return WP query object
     */
    function prepareQuery($_GET, $queryType = 'list'){ 

        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        // echo $paged;exit;
        $args = array(
            'post_type' => $this->cptSlug,
            'posts_per_page' => -1,
            //'paged' => $paged,
            //'offset' => ($paged - 1) * 20,
            'lang' => pll_current_language(),
            // 'lang' => 'en',
        );

        if(isset($_GET['show_amount'])){
            $args['posts_per_page'] = $_GET['show_amount'];
        }

/*        if(isset($_GET['orderby']) && $_GET['orderby'] == 'title'){
            
        } else if(isset($_GET['orderby']) && $_GET['orderby'] != 'title' && isset($this->fields[$_GET['orderby']])){
            $key = $_GET['orderby'];
            if($this->fields[$key]['metaType'] == 'meta'){
                $args['orderby'] = 'meta_value';
                $args['meta_key'] = $this->mfp.$_GET['orderby'];
            } else if($this->fields[$key]['metaType'] == 'taxonomy'){
                // to do

            } 
        } else { // default ordering
            $args['orderby'] = 'title';
        }*/

        //////////////////////////////////////////////
        // specify ordering


        $args['orderby'] = 'title';

        if(isset($_GET['production_date_from']) && $_GET['production_date_from'] == date('Y')) {
            // current year filter
            // order by title
        } else if(isset($_GET['production_date_to']) && $_GET['production_date_to'] != '' || isset($_GET['production_date_from']) && $_GET['production_date_from'] != date('Y')) {
            // year period filter
            $args['orderby'] = 'meta_value_num title';
            $args['meta_key'] = $this->mfp.'production_date';
        }


        //EOF
        //////////////////////////////////////////////


        if(isset($_GET['order']) && strtoupper($_GET['order'] == 'DESC')){
            $args['order'] = 'DESC';
        } else {
            $args['order'] = 'ASC';
        }

        foreach ($this->fields as $key => $val) {              
         
            // if($val['showInSearchForm'] == true || current_user_can('administrator') || current_user_can('semi_admin')){
            if(1>0){
                if(isset($_GET[$key]) && $_GET[$key] != ''){
                    $args['meta_query']['relation'] = 'AND'; 
                    if($val['searchHandleType'] == 's'){
                        $args['s'] = $_GET['title'];  // todo: this is hardcoded
                    } elseif($val['searchHandleType'] == 'metaQueryLike'){

                        $args['meta_query'][] = array(
                            'key' => $this->mfp.$key,
                            'value' => $_GET[$key],
                            'compare' => 'LIKE'
                        );

                    } elseif($val['searchHandleType'] == 'metaQueryLikeMultipleSelect'){
                        // print_r($_GET[$key]);exit;

                        $metaQueryMultipleSelect = array('relation' => 'OR');
                        foreach ($_GET[$key] as $key2 => $val2) {
                            $metaQueryMultipleSelect[] = array(
                                'key' => $this->mfp.$key,
                                'value' => $val2,
                                'compare' => 'LIKE'
                            );
                        }
                        $args['meta_query'][] = $metaQueryMultipleSelect;

                    } elseif ($val['searchHandleType'] == 'metaQueryEqual'){
                        $args['meta_query'][] = array(
                            'key' => $this->mfp.$key,
                            'value' => $_GET[$key],
                        );
                    } elseif ($val['searchHandleType'] == 'metaQueryInArray'){  // multiple select
                        $args['meta_query'][] = array(
                            'key' => $this->mfp.$key,
                            'value' => $_GET[$key],
                            // 'value' => array('alsenu', 'babtu'),
                            'compare' => 'IN'  // should be 'IN' probably, but for now it isnt used
                        );
                    } /*elseif ($val['searchHandleType'] == 'metaQueryBetween'){  // date
                        echo 5; exit;
                        if(isset($_GET[$key.'_from']) && !empty($_GET[$key.'_from'])){       
                            $args['meta_query'][] = array(
                              'key' => $this->mfp.$key,
                              'value' => $_GET[$key.'_from'],
                              'compare' => '>='
                            );
                        }

                        if(isset($_GET[$key.'_to']) && !empty($_GET[$key.'_to'])){
                            $args['meta_query'][] = array(
                              'key' => $this->mfp.$key,
                              'value' => $_GET[$key.'_to'],
                              'compare' => '<='
                            );
                        }
                    }*/ elseif ($val['searchHandleType'] == 'taxonomy' || $val['searchHandleType'] == 'taxonomyNotHierarchical'){
                        // print_r($_GET[$key]);

                        if (is_array($_GET[$key])) {
                            $terms = array();
                            foreach ($_GET[$key] as $key2 => $val2) {
                                if ($val2 != '') {
                                    $terms[] = $val2;
                                }
                            }
                        } else {
                            $terms[] = $_GET[$key];
                        }

                        if (!empty($terms)){
                            $args['tax_query'][] = array(
                                'taxonomy' => $val['taxonomySlug'],
                                'field' => 'slug',
                                'terms' => $_GET[$key],
                                'operator' => 'AND'
                            );
                        }
                    } 
                } else {
                    if ($val['searchHandleType'] == 'metaQueryBetween' || $val['searchHandleType'] == 'metaQueryTimeBetween'){
                        if(isset($_GET[$key.'_from']) && !empty($_GET[$key.'_from'])){ 
                            $value =  $_GET[$key.'_from'];

                            /*if ($val['searchHandleType'] == 'metaQueryBetween') {
                                // currently made assuming that searching is always made for date field in date format
                                $value = $this->convertDateToTimeStamp($value);
                            } else */if ($val['searchHandleType'] == 'metaQueryTimeBetween') {
                                // currently made assuming that searching is always made by minutes
                                $value = $value * 60;
                            }

                            $args['meta_query'][] = array(
                              'key' => $this->mfp.$key,
                              'value' => $value,
                              'compare' => '>=',
                              'type' => 'numeric'
                            );
                        }

                        if(isset($_GET[$key.'_to']) && !empty($_GET[$key.'_to'])){
                            $value =  $_GET[$key.'_to'];
                            
                            /*if ($val['searchHandleType'] == 'metaQueryBetween') {
                                // currently made assuming that searching is always made for date field in date format
                                $value = $this->convertDateToTimeStamp($value);                          
                            } else */if ($val['searchHandleType'] == 'metaQueryTimeBetween') {
                                // currently made assuming that searching is always made by minutes
                                $value = $value * 60;
                            }

                            $args['meta_query'][] = array(
                              'key' => $this->mfp.$key,
                              'value' => $value,
                              'compare' => '<=',
                              'type' => 'numeric'
                            );
                        }
                    }
                }

            }
        }

        if (isset($_GET['filterByYears']) && $_GET['filterByYears'] != '') {
            // home landing page, need to filter by year here
            $yearsArray = explode(',', $_GET['filterByYears']);
            if (!empty($yearsArray)) {
                $args['meta_query'][] = array(
                  'key' => $this->mfp.'production_date',
                  'value' => $yearsArray,
                  'compare' => 'IN',
                );
            }
        }        



        // print_r($args);exit;
        // unset($args['meta_query']);
        // unset($args['s']);

        $the_query = new WP_Query( $args );
        // print_r($the_query);exit;
        wp_reset_query();
        wp_reset_postdata();
        
        return $the_query;
    }
 
    /**
     * Method used for ajax to fill autocomplete values for titiel field
     */
    function lithfilmsSearchAutocompleteResponse() {

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
                'lang' => $_GET['lang'],
                // 'lang' => 'lt',
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

 
    /**
     * Method used for ajax to handle search form submit on craft list
     */
    // function filterCraftListResponse() {
    //     $the_query = $this->prepareQuery($_GET);
    //     $output = $this->outputCraftList($the_query);
    //     echo $output;
    //     exit;
    // }
 
    /**
     * Method used for ajax to handle search form submit on craft map
     */
    // function filterCraftsMapResponse() {
    //     $the_query = $this->prepareQuery($_GET, 'map');
    //     $locations = $this->outputCraftsMapLocations($the_query);
    //     echo $locations;
    //     exit;
    // }

    /**
     * Returns array of used taxonomies
     *
     **/
    // function getRegisteredTaxonomies(){
    //     $fields = array();
    //     foreach($this->fields as $key => $val){
    //         if($val['metaType'] == 'taxonomy'){
    //             $fields[$key] = $val;
    //         }
    //     }
    //     return $fields;
    // }


    ///////////////////////////////////////////////////////////////////////
    // Admin settings
    ///////////////////////////////////////////////////////////////////////

    function additionalAdminMenuPages() {
        //add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
        add_submenu_page('edit.php?post_type='.$this->cptSlug, 'Nustatymai', __('Settings'),  'manage_options', $this->settingsPageSlug, array(&$this, 'adminPageSettings'));
        add_submenu_page('edit.php?post_type='.$this->cptSlug, 'Importavimas', __('Importavimas'),  'administrator', 'import', array(&$this, 'adminPageIpmport'));
   
    }


    // function defaultOptions() {
    //     $options = array(
    //         'user_confirmation_email_subject' => 'Registracijos patvirtinimas',
    //         'user_confirmation_email_message' => '',
    //         );
    //     return $options;
    // }

    function initOptions() {
        // $options = get_option($this->optionsSlug);
        // if ($options === false) {
        //     $options = $this->defaultOptions();
        // }
        // $this->options = $options;
        // update_option($this->optionsSlug, $options);
        //print_r($options);

        // $this->options = get_option($this->optionsSlug);

        // register_setting( $option_group, $option_name, $sanitize_callback
        register_setting($this->optionsSlug, $this->optionsSlug);  
    }


    function adminPageSettings(){ ?>

        <div class="wrap lithfilm-settings-wrap">

            <div id="icon-users" class="icon32"><br/></div>
            <h2><?php echo __('Lithuanian films nustatymai', '');?></h2>

            <?php if (isset( $_GET['settings-updated'])) {
                    //echo $this->flashHtml(__('Settings updated'));
            } ?>

            <form action="options.php" method="post">

                <?php
                // http://wordpress.stackexchange.com/questions/26607/add-section-add-settings-section-to-a-custom-page-add-submenu-page

                // settings_fields( $option_group ) 
                //@options_group This should match the group name used in register_setting().
                //Output nonce, action, and option_page fields for a settings page. 
                //Please note that this function must be called inside of the form tag for the options page.
                settings_fields($this->optionsSlug);
                

                // first section
                $generalSectionSlug = 'lithfilmSettingsSection_general';
                add_settings_section($generalSectionSlug, 'Bendri nustatymai', array(&$this, 'bla'), $this->settingsPageSlug);
                
                add_settings_field($this->optionSlugSeparateHomeFilmsByType, 'Titulinio puslapio filmus skirstyti pagal rūšis?', array(&$this, 'settingsInputCheckboxCallback'), $this->settingsPageSlug, $generalSectionSlug, array('id' => $this->optionSlugSeparateHomeFilmsByType));
                add_settings_field($this->optionSlugHomeFilmsWhichYear, 'Jei titulinio puslapio filmus skirstote, pagal rūšis, galite įrašyti kurių metų filmus rodyti tituliniame puslapyje. Galite įrašyti kelis metus atskirdami juos kableliais. Jei neįrašysite, bus rodomi visų metų filmai', array(&$this, 'settingsInputTextCallback'), $this->settingsPageSlug, $generalSectionSlug, array('id' => $this->optionSlugHomeFilmsWhichYear, 'separator' => '<br/><br/><br/>'));


                // dynamic sections for every field
                foreach ($this->fields as $key => $val) {

                    //add_settings_section( $id, $title, $callback, $page );
                    //@ $page (string) (required) The menu page on which to display this section. Should match $menu_slug from Function Reference/add theme page
                    //add_settings_section('main_settings', 'Main settings', array(&$this, 'main_settings_header_html'), 'lkc_newsletter');
                    add_settings_section('lithfilmSettingsSection_'.$key, 'Laukelis "' . $val['label'] . '"', array(&$this, 'bla'), $this->settingsPageSlug);
                    
                    // add_settings_field( $id, $title, $callback, $page, $section, $args)
                    add_settings_field('showInExcerpt_' . $key, 'Rodyti ištraukoje?', array(&$this, 'settingsInputCheckboxCallback'), $this->settingsPageSlug, 'lithfilmSettingsSection_'.$key, array('id' => 'showInExcerpt_' . $key));
                    add_settings_field('showInMetaContent_' . $key, 'Rodyti pagrindiniame turinyje šalia aprašymo?', array(&$this, 'settingsInputCheckboxCallback'), $this->settingsPageSlug, 'lithfilmSettingsSection_'.$key, array('id' => 'showInMetaContent_' . $key));
                    add_settings_field('showInSearchForm_' . $key, 'Rodyti paieškos formoje?', array(&$this, 'settingsInputCheckboxCallback'), $this->settingsPageSlug, 'lithfilmSettingsSection_'.$key, array('id' => 'showInSearchForm_' . $key));
                }



                    //print out all added sections with add_settings_section()
                do_settings_sections($this->settingsPageSlug);

                ?>

                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Save') ?>" />
                    <!-- <input name="" type="submit" class="button-secondary" value="<?php _e('Restore defaults', 'lkc-newsletter'); ?>" /> -->
                </p>

            </form>


        </div>
        <?php
    }

    function bla(){
    }

    function settingsInputTextCallback($args){       
        echo '<input type="text" name="'.$this->optionsSlug.'['.$args['id'].']" id="'.$args['id'].'" value="'.$this->options[$args['id']].'" />';    

        if ($args['separator']) {
            echo $args['separator'];
        }
    }

    function settingsInputCheckboxCallback($args){   
        echo '<input type="checkbox" name="'.$this->optionsSlug.'['.$args['id'].']" id="'.$args['id'].'" value="1" ' . checked(1, $this->options[$args['id']], false ) . ' />';    
   
        if ($args['separator']) {
            echo $args['separator'];
        }
    }

    ///////////////////////////////////////////////////////////////////////
    // Importing legacy content
    ///////////////////////////////////////////////////////////////////////


    function registerImportHooks() {
        add_action('init', array(&$this, 'create_films'));
        add_action('init', array(&$this, 'create_films_taxonomies'));
        // add_action('wp_loaded', array(&$this, 'doImport'));
    }

    function create_films() {
        $labels = array(
        'name' => _x('LF legacy', 'post type general name'),
        'singular_name' => _x('Film', 'post type singular name'),
        'add_new' => _x('Add New', 'films'),
        'add_new_item' => __('Add New Film'),
        'edit_item' => __('Edit Film'),
        'new_item' => __('New Film'),
        'all_items' => __('All Films'),
        'view_item' => __('View Film'),
        'search_items' => __('Search Films'),
        'not_found' =>  __('No films found'),
        'not_found_in_trash' => __('No films found in Trash'), 
        'parent_item_colon' => '',
        'menu_name' => 'LF Legacy'
      );
      $films_args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true, 
        'show_in_menu' => true, 
        'show_in_nav_menus' => true,
        'query_var' => true,
        'rewrite' => true,
        //'rewrite' => array( 'slug' => 'films'),
        'capability_type' => 'post',
        'has_archive' => true, 
        'hierarchical' => false,
        'menu_position' => 5,
        'menu_icon' => ('http://lithuanianfilms.com/img/Lithuanian-Films-icon.png'),
        'supports' => array('title', 'editor', 'thumbnail'),
      ); 
        register_post_type('films',$films_args);
    }

    function create_films_taxonomies() {

        $labels = array(
            'name' => _x( 'Film categories', 'taxonomy general name' ),
            'singular_name' => _x( 'Film category', 'taxonomy singular name' ),
            'search_items' =>  __( 'Search Film categories' ),
            'all_items' => __( 'All Film categories' ),
            'parent_item' => __( 'Parent Film category' ),
            'parent_item_colon' => __( 'Parent Film category:' ),
            'edit_item' => __( 'Edit Film category' ),
            'update_item' => __( 'Update Film category' ),
            'add_new_item' => __( 'Add New Film category' ),
            'new_item_name' => __( 'New Film category' ),
        );  
        register_taxonomy( 'year_meta', array( 'films' ), array(
            'name' => _x( 'Year', 'taxonomy general name' ),
            'label' => ('Year'),
            'hierarchical' => false,
            'show_ui' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'year' ),
            //'rewrite' => true,
        ));
        register_taxonomy( 'film-categories', array( 'films' ), array(
            'name' => _x( 'Film categories', 'taxonomy general name' ),
            'labels' => $labels, /* NOTICE: Here is where the $labels variable is used */
            'hierarchical' => true,
            'show_ui' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'categories' ),
            //'rewrite' => true,
        ));
        register_taxonomy( 'film-director', array( 'films' ), array(
            'hierarchical' => false,
            'label' => ('Directors'),
            'show_ui' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'director' ),
            //'rewrite' => true,
        ));
        register_taxonomy( 'film-producer', array( 'films' ), array(
            'hierarchical' => false,
            'label' => ('Producers'),
            'show_ui' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'producer' ),
            //'rewrite' => true,
        ));
        register_taxonomy( 'film-production', array( 'films' ), array(
            'hierarchical' => false,
            'label' => ('Production Companies'),
            'show_ui' => true,
            'show_in_nav_menus' => false,
            'show_tagcloud' => true,
            'query_var' => true,
            'rewrite' => array( 'slug' => 'production' ),
            //'rewrite' => true,
        ));
    }

    function adminPageIpmport() {
        // $this->importPosts();


        echo 'Jei matote si pranesima, vadinasi importavimas pavyko.';
    }

    function updateIfShortMeter() {
        $args = array(
            'post_type' => $this->cptSlug,
            'posts_per_page' => -1,
            // 'p' => 8036, // to test duration
            // 'p' => 860, // to test credits
            // 'offset' => 100,
        );

        $the_query = new WP_Query($args);

        while ($the_query->have_posts()){   
            $the_query->the_post();

            $durationInSec = $this->outputField(get_the_ID(), 'duration');
            $typeByDuration = $this->checkIfFullMeter($durationInSec) ? 'full_meter' : 'short_meter';
            print_r($this->convertSecondsToTime($durationInSec) . ' ' . $typeByDuration . ' <br>');
            // update_post_meta(get_the_ID(), $this->mfp . 'duration_type', $typeByDuration);
        }
    }


    // function importTaxonomies($lang = 'lt') {
    //     global $polylang;
    //     foreach ($this->fields as $key => $val) {
    //         if(isset($val['legacyTaxonomy']) && $val['legacyTaxonomy'] != 'film-categories') {
    //         // if(isset($val['legacyTaxonomy']) && $val['legacyTaxonomy'] != 'film-director' && $val['legacyTaxonomy'] != 'film-producer' && $val['legacyTaxonomy'] != 'film-production') {

    //             $existingTerms = get_terms($val['legacyTaxonomy'], array('hide_empty' => false));
    //             foreach ($existingTerms as $key2 => $val2) {
    //                 $result = wp_insert_term($val2->name, $val['taxonomySlug']);
    //                 // if (is_array($result)){
    //                 //     $term_id = $result['term_id'];
    //                 //     $polylang->set_term_language($term_id, $lang);
    //                 // } else {
    //                 //     print_r($result);exit;
    //                 // }
    //             }
    //         }
    //     }
    // }


    function extractTaxonomiesFromString($wholeString, $stringTitle, $delimiter) {

        $pPos = strpos($wholeString, '<p>');
        if ($pPos !== false) {
            $wholeString = substr($wholeString, 0, $pPos);
        }

        $wholeString = str_replace('and others.', '', $wholeString);
        $wholeString = str_replace('and others', '', $wholeString);    
        $wholeString = str_replace('Directors', '', $wholeString);    
        $wholeString = str_replace('Director', '', $wholeString);    
        $wholeString = str_replace('directors', '', $wholeString);    
        $wholeString = str_replace('director', '', $wholeString);    


        $cameraPos = strpos($wholeString, $stringTitle);
        if ($cameraPos !== false) {
            $operatorStart = $cameraPos + strlen($stringTitle) + 1;
            $creditsCutted = substr($wholeString, $operatorStart);
            $operatorEnd = strpos($creditsCutted, $delimiter);

            if ($operatorEnd) {
                $operator = substr($creditsCutted, 0, $operatorEnd);
            } else {
                $operator = $creditsCutted;
            }

            $operator = str_replace($delimiter, '', $operator);
        

            $operatorsArray = explode(',', $operator);

            return $operatorsArray;
            // print_r($operator);exit;
        }
        return false;
    }

    // function translate($word) {
        


    //     $serviceUrl = 'https://www.googleapis.com/language/translate/v2?key=INSERT-YOUR-KEY&q='.$word.'&source=lt&target=en';
    //     $curl = curl_init($serviceUrl);
    //     curl_setopt($curl, CURLOPT_POST, true);
        
    //     // curl_setopt($curl, CURLOPT_POSTFIELDS, "newsletterId=" . $newsletterId . "&receiverEmail=" . $receiverEmail. "&userpasswordToken=" . $userpasswordToken);
    //     $curl_response = curl_exec($curl);
    //     print_r($curl_response);exit;
    //     curl_close($curl);

    // }


    function importPosts() {


        // antra karta importavus patikrinti:
            // DEVIL’S STONE  ar trukme gerai
            // HOW WE PLAYED THE REVOLUTION   time 68 min  nepagauna
        // AN ORDINARY DEVIL  ar LAIKAS GERAI
// patikinti titrus ar veikia

// galima butu camera man ir kt isextractint
// Script Giedrė Žickytė / Camera Audrius Kemežys, Eitvydas Doškus, Rimvydas Leipus / Sound Algimantas A. Apanavičius, Vytis Puronas / Music Viktoras Diawara, Vytautas Bikus / Editing Giedrė Žickytė, Samuel Lajus
// <p>Festivals European Film Forum Scanorama 2011. Vilnius International Film Festival Kino pavasaris 2012(Lithuania)</p>
// Script Algirdas Tarvydas / Camera Algirdas Tarvydas / Music Algimantas Apanavičius / Editing Vaclovas Nevčesauskas


        // $polylang->set_term_language($term_id, $lang);

        // steps moving plugin live
            // create pages
            // define static pages ids
            // create menu
        // admin settings nustatyti



        // steps before running import script:
            // import all posts from old website in to new using wordpress import function
            // upload old site 'uploads dir' to new site 'uploads' dir, under folder named 'lithfilm_temp_to_import'
            // in current uploads month and day folder create dir 'lithfilm-imported'

            // importruoti is LF filmus ir sumapinti categories  - dar nepadaryta






        // fields:

        // taxonomy:
            // flat:
                // director+
                // producer+
                // production companies+
                // year -> insert as meta
            // hierarchical
                // categories

        // meta:
            // tape_meta
            // credits
            // in_the_production
            // _thumbnail_id

        // possible tape formats:
            // 5 min 43 sec / DVCPRO HD, 16/9 / colour       3 minutes
            // 30 min. / mini DV / colour                       A PLACE WE CALL HOME
            // 14 min. / HD / colour                            A STORY OF A DEPORTEE EXILED BY HER OWN FATHER
            // 4 min 26 sec / DVD / colour                         ACTIVATE THE RESULTS
            // 40 min. / HD / colour                          AMONG THE GRASSES
            // 75 min, HDV, colour                              AFGHAN FREEDOM
            // 27 and 12 min. / DVD, mini DV / colour           ANIMATION LECTURES (2 PARTS)
            // 40 min. / HD 1080p / SD PAL 19:9/ colour         BLOOD ROUTE
            // 25' video / color                                BOUNDARY ZONE
            // 50 min. / DVPAL, 16:9 / colour                   CANVAS, NOT WHITE OR ZONE
               // [0] => Animated TV series
               //  [1] => 12 min. x 10 series
               //  [2] => 2D digital animation
               //  [3] => color


        $langs = array('lt', 'en');
        $insertedPostsMap = array();
        foreach ($langs as $lang) {
            
        

                    $args = array(
                        'post_type' => 'films',
                        'posts_per_page' => 50,
                        // 'p' => 8036, // to test duration
                        // 'p' => 860, // to test credits
                        'offset' => 100,
                        // 'offset' => ($paged - 1) * 20,
                        // 'numberposts' => -1,
                    );

                    $the_query = new WP_Query($args);

                    while ($the_query->have_posts()){   
                        $the_query->the_post();

                        $old_post_id = get_the_ID();

                        $film = array(
                            'post_title' => get_the_title(),
                            'post_status' => 'publish',
                            'post_date' => get_post_field('post_date', get_the_ID()),
                            'post_date_gmt' => get_post_field('post_date_gmt', get_the_ID()),
                            'post_type' => $this->cptSlug,
                        );

                        // print_r($the_query);exit;


                        // get image name and remove image from post_content
                        // <img src="http://lf.srv7.321.lt/wp-content/uploads/2012/05/image_00117.jpg" alt="" width="280" height="187" />
                        $content = get_the_content();

                        preg_match('/<img[^>]+./', $content, $img);
                        $img = $img[0];

                        preg_match( '@src="([^"]+)"@', $img, $src);
                        $src = $src[1];

                        $info = pathinfo($src);
                        $imgName = $info['basename'];

                        $content = preg_replace('/<img[^>]+./','', $content);
                        $film['post_content'] = $content;

                        // prepare vars for copying
                        
                        $post_id = wp_insert_post($film, false);
                           

                        if ($post_id) {

                            if ($lang == 'lt') {
                                $insertedPostsMap[$old_post_id][$lang]['new_post_id'] = $post_id;
                            } else {
                                // print_r($insertedPostsMap);exit;
                                $polylang->save_translations('post', $post_id, array('lt' => $insertedPostsMap[$old_post_id]['lt']['new_post_id'], 'en' => $post_id));

                            }

                            global $polylang;
                            $polylang-> set_post_language($post_id, $lang);


                            // attach image
                            if ($lang == 'lt'){

                                // old site 'uploads' dir should be stored in new site 'uploads' dir, under folder named 'lithfilm_temp_to_import'
                                $wp_upload_dir = wp_upload_dir();
                                $oldImgTempDir = $wp_upload_dir['basedir'] . '/lithfilm_temp_to_import/';
                                $oldImgTempUrl = $oldImgTempDir . str_replace('http://lf.srv7.321.lt/wp-content/', '', $src);

                                $imgNewUrl = $wp_upload_dir['url'] . '/lithfilm-imported/' . basename($imgName);
                                $imgNewDir = $wp_upload_dir['path'] . '/lithfilm-imported/' . basename($imgName);

                                if (@fclose(@fopen($oldImgTempUrl, "r"))) { //make sure the file actually exists
                                    copy($oldImgTempUrl, $imgNewDir);

                                    $wp_filetype = wp_check_filetype(basename($imgName), null);
                                    $attachment = array(
                                        // 'guid' => $wp_upload_dir['url'] . '/' . basename($imgName), 
                                        'guid' => $imgNewUrl, 
                                        'post_mime_type' => $wp_filetype['type'],
                                        'post_title' => preg_replace('/\.[^.]+$/', '', basename($imgName)),
                                        'post_content' => '',
                                        'post_status' => 'inherit'
                                    );

                                    $attachment_id = wp_insert_attachment($attachment, $imgNewDir, $post_id);
                                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                                    $attach_data = wp_generate_attachment_metadata($attachment_id, $imgNewDir);
                                    wp_update_attachment_metadata($attachment_id, $attach_data);

                                    set_post_thumbnail($post_id, $attachment_id);
                                    $insertedPostsMap[$old_post_id][$lang]['new_post_attachment_id'] = $attachment_id;
                                }
                            } else {
                                set_post_thumbnail($post_id, $insertedPostsMap[$old_post_id]['lt']['new_post_attachment_id']);
                            }
                            // EOF 

                            $oldMeta = get_post_custom($post->ID);
                            $metaFields = array();

                            foreach ($this->fields as $key => $val) {

                                if(isset($val['legacyTaxonomy'])) {    

                                    if ($val['legacyTaxonomy'] == 'film-categories') {

                                        $mapLt = array(
                                            // old id => new id
                                            
                                            // Animations
                                            326 => 42,
                                            
                                            // Documentaries
                                            224 => 41, 

                                            // Feature (Vaidybinis)
                                            140 => 40,
                                        );

                                        $mapEn = array(
                                            // old id => new id
                                            
                                            // Animations
                                            326 => 128,
                                            
                                            // Documentaries
                                            224 => 129, 

                                            // Feature (Vaidybinis)
                                            140 => 130,
                                        );

                                        if ($lang == 'lt') {
                                            $map = $mapLt;
                                        } else if ($lang == 'en') {
                                            $map = $mapEn;
                                        }

                                        $categories = wp_get_post_terms($old_post_id, $val['legacyTaxonomy']);
                                        $mappedIds = array();

                                        // print_r($categories);
                                        // print_r($map);exit;

                                        foreach ($categories as $key2 => $val2) {
                                            if (isset($map[$val2->term_id])){
                                                $mappedIds[] = $map[$val2->term_id];
                                            }
                                        }

                                        if (!empty($mappedIds)) {
                                            wp_set_object_terms($post_id, $mappedIds, $val['taxonomySlug']);
                                        }

                                    } else {
                                        $postTerms = wp_get_post_terms($old_post_id, $val['legacyTaxonomy'], array('fields' => 'names'));
                                        wp_set_post_terms($post_id, $postTerms, $val['taxonomySlug']);
                                    }
                                }

                            }

                            // year
                            $yearTerms = wp_get_post_terms($old_post_id, 'year_meta', array('fields' => 'names'));
                            update_post_meta($post_id, $this->mfp . 'production_date', $yearTerms[0]);
                            $metaFields[] = $this->mfp . 'production_date';

                            // production status
                            $isInProduction = $oldMeta['in_the_production'][0];

                            if ($isInProduction == 'yes') {
                                $productionStatus = 'producible';
                            } else {
                                $productionStatus = 'produced';
                            }

                            update_post_meta($post_id, $this->mfp . 'production_status', $productionStatus);
                            $metaFields[] = $this->mfp . 'production_status';

                            // colored


                            // duration and format_and_subtitles and is_colored from tape meta field
                            $tape = $oldMeta['tape_meta'][0];

                                        // possible formats:
                                        // 5 min 43 sec / DVCPRO HD, 16/9 / colour       3 minutes
                                        // 30 min. / mini DV / colour                       A PLACE WE CALL HOME
                                        // 14 min. / HD / colour                            A STORY OF A DEPORTEE EXILED BY HER OWN FATHER
                                        // 4 min 26 sec / DVD / colour                         ACTIVATE THE RESULTS
                                        // 40 min. / HD / colour                          AMONG THE GRASSES
                                        // 75 min, HDV, colour                              AFGHAN FREEDOM
                                        // 27 and 12 min. / DVD, mini DV / colour           ANIMATION LECTURES (2 PARTS)
                                        // 40 min. / HD 1080p / SD PAL 19:9/ colour         BLOOD ROUTE
                                        // 25' video / color                                BOUNDARY ZONE
                                        // 50 min. / DVPAL, 16:9 / colour                   CANVAS, NOT WHITE OR ZONE

                            if ($tape) {

                                $minutes = false;
                                $seconds = false;

                                $minPos = strpos($tape, 'min');
                                $posBack = $minPos > 3 ? 4 : 3;

                                if ($minPos){
                                    $minutes = intval(str_replace('/', '', substr($tape, $minPos - $posBack, 3)));
                                }

                                if ($minutes == 0) {
                                    // probably one digit number
                                    $posBack = $posBack - 1;
                                    $minutes = intval(str_replace('/', '', substr($tape, $minPos - $posBack, 3)));
                                }

                                $secPos = strpos($tape, 'sec');
                                if ($secPos){
                                    $seconds = intval(substr($tape, ($secPos - 3), 2));
                                }

                                if ($minutes || $seconds) {
                                    $durationsInSec = 0;
                                    if ($minutes) {
                                        $durationsInSec = $durationsInSec + $minutes * 60;
                                    }
                                    if ($seconds) {
                                        $durationsInSec = $durationsInSec + $seconds;
                                    }

                                    update_post_meta($post_id, $this->mfp . 'duration', $durationsInSec);
                                    $metaFields[] = $this->mfp . 'duration';

                                    $typeByDuration = $this->checkIfFullMeter($durationInSec) ? 'full_meter' : 'short_meter';
                                    update_post_meta($post_id, $this->mfp . 'duration_type', $typeByDuration);
                                    $metaFields[] = $this->mfp . 'duration_type';
                                }


                                $posibleFormatTags = array('35 mm,', '35 mm ', 'super 16 mm,', 'super 16 mm ', 'DVD,', 'DVD ', '16:9,', '16:9 ', 'Full HDV,', 'Full HDV ', 'HDV,', 'HDV ', 'HD,', 'HD ', 'DVCAM,', 'DVCAM ', 'DV CAM,', 'DV CAM ', 'DVPAL,', 'DVPAL ', 'DCP,', 'DCP ', 'MPEG-4,', 'MPEG-4 ', 'Betacam SP,', 'Betacam SP ', 'DIGIBETA,', 'DIGIBETA ', "25' video,", "25' video ", 'RED cam,', 'RED cam ', 'BR,', 'BR ');

                                foreach ($posibleFormatTags as $a => $b) {
                                    if (strpos($tape, $b)) {
                                        wp_set_post_terms($post_id, array(substr($b, 0, -1)), $this->fields['format_and_subtitles']['taxonomySlug']);
                                    }
                                }

                                // colors:
                                $colorOptionsToSave = array();

                                $coloredVariations = array('color', 'colour');
                                foreach ($coloredVariations as $a => $b) {
                                    if (strpos($tape, $b)) {
                                        $colorOptionsToSave[] = 'colored';
                                    }
                                } 
                                $bwVariations = array('b/w', 'b\w', 'bw');
                                foreach ($bwVariations as $a => $b) {
                                    if (strpos($tape, $b)) {
                                        $colorOptionsToSave[] = 'bw';
                                    }
                                }     

                                if(!empty($colorOptionsToSave)){
                                    update_post_meta($post_id, $this->mfp . 'is_colored', $colorOptionsToSave);
                                    $metaFields[] = $this->mfp . 'is_colored';
                                }

                            }


                            $credits = $oldMeta['credits'][0];

                            if ($credits) {

                                $creditsParts = array(
                                    array('taxonomySlug' => 'lithfilm-operator', 'stringTitle' => 'Camera'),
                                    array('taxonomySlug' => 'lithfilm-scenarist', 'stringTitle' => 'Script'),
                                    array('taxonomySlug' => 'lithfilm-compozitor', 'stringTitle' => 'Music'),
                                    array('taxonomySlug' => 'lithfilm-actor', 'stringTitle' => 'Cast'),
                                    array('taxonomySlug' => 'lithfilm-artist', 'stringTitle' => 'Art'),
                                );

                                foreach ($creditsParts as $c => $d) {
                                    $terms = $this->extractTaxonomiesFromString($credits, $d['stringTitle'], '/');
                                    if (is_array($terms)) {

                                        // print_r($d['stringTitle']);
                                        // print_r(':<br>');
                                        // print_r($terms);
                                        // print_r('<br>');
                                        // print_r('<br>');
                                        // print_r('<br>');
                                        // print_r('<br>');
                                        wp_set_post_terms($post_id, $terms, $d['taxonomySlug']);
                                    }
                                }
                                
                            }


                            // these fields will not be used, just importing them in case will be needed somehow
                            update_post_meta($post_id, $this->mfp . 'old_tape', $oldMeta['tape_meta'][0]);
                            $metaFields[] = $this->mfp . 'tape'; // maybe not needed cause not shown in admin
                            update_post_meta($post_id, $this->mfp . 'old_credits', $oldMeta['credits'][0]);
                            $metaFields[] = $this->mfp . 'credits'; // maybe not needed cause not shown in admin
                            update_post_meta($post_id, $this->mfp . 'old_thumbnail_id', $oldMeta['_thumbnail_id'][0]);
                            $metaFields[] = $this->mfp . 'old_thumbnail_id'; // maybe not needed cause not shown in admin

                            // additional field to easily separate imported films from added via WP admin
                            update_post_meta($post_id, $this->importedMetaField, 1);
                            $metaFields[] = $this->importedMetaField;

                            // needed for WP Alchemy to fill meta fields in admin
                            update_post_meta($post_id, $this->mfp . 'meta_fields', $metaFields);
                        }
                    }

                    wp_reset_query();
                    wp_reset_postdata();
                }
    }

}

global $g_lithuanianFilms;
$g_lithuanianFilms = new LithuanianFilms();
$g_lithuanianFilms->init();
?>
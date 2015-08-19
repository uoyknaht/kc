<?php 

class FilmRegisterData {

    function populateDynamicData() {
        $this->populateDropdownOptions();
        return $this->fields;
    }

    function getDropdownOptionsFromTaxonomy($taxonomy){
    	$taxonomyObject = get_terms($taxonomy, array('hide_empty' => 0));
    	$options = array('' => pll__('Pasirinkite'));
    	foreach ($taxonomyObject as $key => $val) {
    		$options[$val->slug] = $val->name; 
    	}
    	return $options;
    }

    function populateDropdownOptions(){
        
        foreach ($this->fields as $key => $val) {
            if($val['metaType'] == 'taxonomy'){
                $this->fields[$key]['dropdownOptions'] = $this->getDropdownOptionsFromTaxonomy($val['taxonomySlug']);
            }
        }
        $this->fields['country']['dropdownOptions'] = $this->countryList;
        $this->fields['country']['dropdownOptions'][0] = pll__('Pasirinkite');
        $this->fields['license_territory']['dropdownOptions'] = $this->countryList;
        $this->fields['license_territory']['dropdownOptions'][0] = pll__('Pasirinkite');
        $this->fields['film_original_language']['dropdownOptions'] = $this->countryList22;
        $this->fields['film_original_language']['dropdownOptions'][0] = pll__('Pasirinkite');
        $this->fields['film_subtitle_language']['dropdownOptions'] = $this->countryList22;
        $this->fields['film_subtitle_language']['dropdownOptions'][0] = pll__('Pasirinkite');
    }

    /**
     * Get fields that will be shown in frontend, in search results
     *
     **/
    function getListTableFields(){
        $fields = array();
        foreach($this->fields as $key => $val){
            if(isset($val['inListTable']) && $val['inListTable'] == true){
                $fields[$key] = $val;
            }
        }
        return $fields;
    }

    /**
     * Get field value of existing film
     * F.e. get identity code of film with ID 20 $this->getFieldData(20, 'identity_code');
     */
	function getFieldData($post_id, $key){
        if($this->fields[$key]['metaType'] == 'main'){
            return get_the_title($post_id);
        } else if($this->fields[$key]['metaType'] == 'meta'){
            return get_post_meta($post_id, '_lkc_film_'.$key, true);
        } else if($this->fields[$key]['metaType'] == 'taxonomy'){
            return $this->getStringifiedTermsFromObject($post_id, $this->fields[$key]['taxonomySlug']);
        } 
	}


    /**
     * Returns array of used taxonomies for films
     *
     **/
    function getFilmTaxonomies(){
        $fields = array();
        foreach($this->fields as $key => $val){
            if($val['metaType'] == 'taxonomy'){
                $fields[$key] = $val;
            }
        }
        return $fields;
    }


    /** 
     *
     */
    function getStringifiedTermsFromObject($postID, $taxonomy){
      $terms = get_the_terms($postID, $taxonomy);
      $i = 0; 
      $output = '';
      if(!empty($terms)){
        foreach($terms as $term){
          $i++;
          $output .= $term->name; 
          if($i != count($terms)) $output .= ', ';
        }
      }
      return $output;
    }


    /**
     * CPT film fields (meta and main(like title))
     *
     *
     *
     * inListTable - if show field as table column heading in frontend film list, by which films could be sortable
     * legacyField - field alias in old database
     * absoleteField - field was in old database, but not used anymore in this database
     * legacyFieldInTableLicenses - field is from licenses table
     */

    var $fields = array(
        'identity_code' => array(
          'metaType' => 'meta', 
          'label' => 'Identifikavimo kodas', 
          'showInFrontEnd' => true, 
          'inputType' => 'text',
          'disabledForEdit' => true,
          'searchInFrontEnd' => false,
          'searchHandleType' => 'metaQueryEqual',
          'searchInputHtmlFunction' => 'sihf_text',
          'legacyField' => 'kod',
          'inListTable' => true,
          'backEndExplanation' => 'Užpildoma automatiškai pirmą kartą išsaugojus filmą, kuomet yra pažymėta varnelė "Filmo informacija pilnai užpildyta. Publikuoti filmą"',
        ),
        // 'post_title' => array(
        'title' => array(
          'metaType' => 'main', 
          'label' => 'Pavadinimas', 
          'showInFrontEnd' => true, 
          'inputType' => 'text',
          'searchInFrontEnd' => true,
          'searchHandleType' => 's',
          'searchInputHtmlFunction' => 'sihf_text',
          'legacyField' => 'pav_lt',
          'inListTable' => true,
        ),
        // 'post_title' => array(
        'title_orig' => array(
          'metaType' => 'meta', 
          'label' => 'Pavadinimas originalo k.', 
          'showInFrontEnd' => false, 
          'inputType' => 'text',
          'searchInFrontEnd' => false,
          'searchHandleType' => 's',
          'searchInputHtmlFunction' => 'sihf_text',
          'legacyField' => 'pav',
        ),
        'produce_date' => array(
          'metaType' => 'meta', 
          'label' => 'Pagaminimo data', 
          'showInFrontEnd' => true, 
          'inputType' => 'date',
          'searchInFrontEnd' => true,
          'searchHandleType' => 'between',
          'searchInputHtmlFunction' => 'sihf_text_between',
          'class' => 'input-type-date',
          'legacyField' => 'data',
          'inListTable' => true,
        ),
        'duration' => array(
          'metaType' => 'meta', 
          'label' => 'Trukmė (min.)', 
          'showInFrontEnd' => true, 
          'inputType' => 'text',
          'searchInFrontEnd' => true,
          'searchHandleType' => 'between',
          'searchInputHtmlFunction' => 'sihf_text_between',
          'legacyField' => 'trukme',
        ),
        'is_full_meter' => array(
          'metaType' => 'meta', 
          'label' => 'Tipas pagal trukmę', 
          'showInFrontEnd' => false, 
          'showInFrontEndNever' => true, 
          'inputType' => 'radio',
          'radioOptions' => array(
            1 => 'Ilgametražis',
            0 => 'Trumpametražis'
          ),
          'disabledForEdit' => true,
          'searchInFrontEnd' => false,
          'searchHandleType' => 'metaQueryEqual',
          'searchInputHtmlFunction' => 'sihf_radio',
          'backEndExplanation' => 'Filmas automatiškai išsaugomas kaip ilgametražis jei jo trukmė ilgesnė nei 60 min.'
        ),        
        'country' => array(
          'metaType' => 'meta', 
          'label' => 'Valstybė', 
          'showInFrontEnd' => true,
          'inputType' => 'select',
          'multipleSelect' => true,
          'dropdownOptions' => array(),
          'searchInFrontEnd' => true,
          'searchHandleType' => 'metaQueryInArray',
          'searchInputHtmlFunction' => 'sihf_select',
          'legacyField' => 'kalba_orig',
        ),
        'film_original_language' => array(
          'metaType' => 'meta', 
          'label' => 'Filmo originalo kalba', 
          'showInFrontEnd' => false,
          'inputType' => 'select',
          'multipleSelect' => true,
          'dropdownOptions' => array(),
          'searchInFrontEnd' => false,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_select',
          'legacyField' => 'salis', // kalba_orig, kalba_titrai, kalba_dublis
        ),
        'film_subtitle_language' => array(
          'metaType' => 'meta', 
          'label' => 'Filmas subtitruotas į', 
          'showInFrontEnd' => false,
          'inputType' => 'select',
          'multipleSelect' => true,
          'dropdownOptions' => array(),
          'searchInFrontEnd' => false,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_select',
          'legacyField' => 'kalba_titrai',
        ),
        'type' => array(
          'metaType' => 'taxonomy', 
          'label' => 'Rūšis', 
          'showInFrontEnd' => true,
          'taxonomySlug' => 'film-type',
          'inputType' => 'select',
          'dropdownOptions' => array(),
          'searchInFrontEnd' => true,
          'searchHandleType' => 'taxonomy',
          'searchInputHtmlFunction' => 'sihf_select',
          'legacyField' => 'rusis',
          'inListTable' => true,
        ),
        'genre' => array(
          'metaType' => 'taxonomy', 
          'label' => 'Žanras', 
          'showInFrontEnd' => true,
          'taxonomySlug' => 'film-genre',
          'inputType' => 'select',
          'dropdownOptions' => array(),
          'searchInFrontEnd' => true,
          'searchHandleType' => 'taxonomy',
          'searchInputHtmlFunction' => 'sihf_select',
          'legacyField' => 'zanras',
          'inListTable' => true,
        ),
        'author_scenario' => array(
          'metaType' => 'meta', 
          'label' => 'Scenarijaus autorius', 
          'showInFrontEnd' => true,
          'inputType' => 'text',
          'searchInFrontEnd' => true,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
          'legacyField' => 'autorius',
        ),
        'director' => array(
          'metaType' => 'meta', 
          'label' => 'Režisierius', 
          'showInFrontEnd' => true,
          'inputType' => 'text',
          'searchInFrontEnd' => true,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
          'legacyField' => 'rezisierius',
        ),
        'operator' => array(
          'metaType' => 'meta', 
          'label' => 'Operatorius', 
          'showInFrontEnd' => true,
          'inputType' => 'text',
          'searchInFrontEnd' => true,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
          'legacyField' => 'operatorius',
        ),
        'compositor' => array(
          'metaType' => 'meta', 
          'label' => 'Kompozitorius', 
          'showInFrontEnd' => true,
          'inputType' => 'text',
          'searchInFrontEnd' => true,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
          'legacyField' => 'atlikejai', // turejo buti muzika
        ),
        'artist' => array(
          'metaType' => 'meta', 
          'label' => 'Dailininkas', 
          'showInFrontEnd' => true,
          'inputType' => 'text',
          'searchInFrontEnd' => true,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
          'legacyField' => 'dailininkas',
        ),
        'property_rights_owner' => array(
          'metaType' => 'meta', 
          'label' => 'Autorių turtinių teisių turėtojas', 
          'showInFrontEnd' => true, 
          'inputType' => 'text',
          'searchInFrontEnd' => false,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
          'legacyField' => 'savininkas',
        ),
        'license_contract_date' => array(
          'metaType' => 'meta', 
          'label' => 'Licencinės sutarties data', 
          'showInFrontEnd' => false, 
          'inputType' => 'date',
          'searchInFrontEnd' => false,
          'searchHandleType' => 'between',
          'searchInputHtmlFunction' => 'sihf_text_between',
          'class' => 'input-type-date',
          'legacyField' => 'laikas',
          'legacyFieldInTableLicenses' => true,
        ),
        'licensiar' => array(
          'metaType' => 'meta', 
          'label' => 'Licenciatas', 
          'showInFrontEnd' => false, 
          'inputType' => 'text',
          'searchInFrontEnd' => false,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
          'legacyField' => 'gavejas',  
          'legacyFieldInTableLicenses' => true,
        ),
        'license_type' => array(
          'metaType' => 'taxonomy', 
          'label' => 'Licencijos rūšis', 
          'taxonomySlug' => 'film-license-type',
          'showInFrontEnd' => false, 
          'inputType' => 'select',
          'dropdownOptions' => array(),
          'searchInFrontEnd' => false,
          'searchHandleType' => 'taxonomy',
          'searchInputHtmlFunction' => 'sihf_select',
          'legacyField' => 'isimtine',
        ),
        'license_valid_from' => array(
          'metaType' => 'meta', 
          'label' => 'Suteiktų teisių galiojimo terminas (nuo)', 
          'labelFirst' => 'Suteiktų teisių galiojimo terminas', 
          'showInFrontEnd' => true, 
          'inputType' => 'date',
          'searchInFrontEnd' => false,
          'searchHandleType' => 'between_related_with_other_field',
          'searchInputHtmlFunction' => 'sihf_text_between_related_with_other_field',
          'searchRelatedFieldKey' => 'license_valid_to',
          'searchRelatedFieldFirst' => true,
          'class' => 'input-type-date',
          'legacyField' => 'data_nuo',
          'legacyFieldInTableLicenses' => true,
        ),
        'license_valid_to' => array(
          'metaType' => 'meta', 
          'label' => 'Suteiktų teisių galiojimo terminas (iki)', 
          'showInFrontEnd' => true, 
          'inputType' => 'date',
          'searchInFrontEnd' => false,
          'searchHandleType' => 'between_related_with_other_field',
          'searchInputHtmlFunction' => 'sihf_text_between_related_with_other_field',
          'searchRelatedFieldKey' => 'license_valid_from',
          'searchRelatedFieldFirst' => false,
          'class' => 'input-type-date',
          'legacyField' => 'data_iki',
          'legacyFieldInTableLicenses' => true,
        ),
        'license_territory' => array(
          'metaType' => 'meta', 
          'label' => 'Suteiktų teisių galiojimo teritorija', 
          'showInFrontEnd' => false, 
          'searchInFrontEnd' => false,
          'inputType' => 'select',
          'multipleSelect' => true,
          'dropdownOptions' => array(),
          'searchHandleType' => 'metaQueryEqual',
          'searchInputHtmlFunction' => 'sihf_select',
        ),
        'rights_given' => array(
          'metaType' => 'taxonomy', 
          'label' => 'Suteiktos teisės', 
          'showInFrontEnd' => true, 
          'taxonomySlug' => 'film-right',
          'searchInFrontEnd' => false,
          'inputType' => 'select',
          'dropdownOptions' => array(),
          'searchHandleType' => 'taxonomy',
          'searchInputHtmlFunction' => 'sihf_select',
          'legacyField' => 'foo',
        ),
        'first_record_producer' => array(
          'metaType' => 'meta', 
          'label' => 'Filmo pirmojo įrašo gamintojas', 
          'showInFrontEnd' => true, 
          'inputType' => 'text',
          'searchInFrontEnd' => false,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
        ),
        'original_source_storing_place' => array(
          'metaType' => 'meta', 
          'label' => 'Nacionalinio filmo originalios filmo medžiagos saugojimo vieta', 
          'showInFrontEnd' => true, 
          'inputType' => 'text',
          'searchInFrontEnd' => false,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
          'legacyField' => 'saugykla',
        ),
        'shower' => array(
          'metaType' => 'meta', 
          'label' => 'Filmo rodytojas', 
          'showInFrontEnd' => false, 
          'inputType' => 'text',
          'searchInFrontEnd' => false,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
        ),
        'distributor' => array(
          'metaType' => 'meta', 
          'label' => 'Filmo platintojas', 
          'showInFrontEnd' => true, 
          'inputType' => 'text',
          'searchInFrontEnd' => false,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
          'legacyField' => 'studija', // not sure 100% that field is mapped correctly
        ),
        'application_provider' => array(
          'metaType' => 'meta', 
          'label' => 'Paraiškos teikėjas', 
          'showInFrontEnd' => false, 
          'inputType' => 'text',
          'searchInFrontEnd' => false,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
          'backEndExplanation' => 'Šio laukelio turinys atsiras eksportuojamos pažymos "Išrašas išduotas:" laukelyje. Įrašykite pavadinimą, kodą, buveinės adresą.'
        ),
        'index' => array(
          'metaType' => 'taxonomy', 
          'label' => 'Indeksas', 
          'showInFrontEnd' => true,
          'taxonomySlug' => 'film-index',
          'inputType' => 'select',
          'dropdownOptions' => array(),
          'searchInFrontEnd' => true,
          'searchHandleType' => 'taxonomy',
          'searchInputHtmlFunction' => 'sihf_select',
          'legacyField' => 'indeksas',
        ),

        'ready' => array(
          'metaType' => 'meta', 
          'label' => 'Filmo informacija pilnai užpildyta. Publikuoti filmą', 
          'showInFrontEnd' => false, 
          'showInFrontEndNever' => true, 
          'inputType' => 'checkbox',
          'checkboxOptions' => array(
            1 => 'Taip'
          ),
          'searchInFrontEnd' => false,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
          'backEndExplanation' => 'Dėmesio! Pirmą kartą pažymėjus šią varnelę ir išsaugojus filmą, filmui bus suteiktas unikalus indeksas, 
                                    priskirta registracijos data. Jei varnelė yra pažymėta ir filmas nėra pripažintas negaliojančiu ar išregistruotas,
                                     filmas bus matomas visiems svetainės lankytojams.'
        ),
        'register_date' => array(
          'metaType' => 'meta', 
          'label' => 'Filmo įregistravimo data', 
          'showInFrontEnd' => true, 
          'inputType' => 'date',
          'disabledForEdit' => true,
          'searchInFrontEnd' => false,
          'searchHandleType' => 'between',
          'searchInputHtmlFunction' => 'sihf_text_between',
          'class' => 'input-type-date',
          'legacyField' => 'laikas',
          'backEndExplanation' => 'Užpildoma automatiškai pirmą kartą išsaugojus filmą, kuomet yra pažymėta varnelė "Filmo informacija pilnai užpildyta. Publikuoti filmą"',
          // 'backEndExplanation' => 'Užpildoma automatiškai kuomet pažymite, jog filmo informacija pilnai užpildyta',
        ),
        'data_enter_date' => array(
          'metaType' => 'meta', 
          'label' => 'Duomenų įrašymo data', 
          'showInFrontEnd' => true, 
          'inputType' => 'date',
          'disabledForEdit' => true,
          'searchInFrontEnd' => false,
          'searchHandleType' => 'between',
          'searchInputHtmlFunction' => 'sihf_text_between',
          'class' => 'input-type-date',
          'legacyField' => 'laikas',
          'backEndExplanation' => 'Užpildoma automatiškai pirmą kartą išsaugojus filmą (nesvarbu ar pilnai užpildytą, ar ne)',
          ),
        'data_change_date' => array(
          'metaType' => 'meta', 
          'label' => 'Duomenų keitimo data', 
          'showInFrontEnd' => true, 
          'showInFrontEndOnlyIfNotEmpty' => true, 
          'inputType' => 'date',
          'disabledForEdit' => true,
          'searchInFrontEnd' => false,
          'searchHandleType' => 'between',
          'searchInputHtmlFunction' => 'sihf_text_between',
          'class' => 'input-type-date',
          'backEndExplanation' => 'Užpildoma automatiškai kiekvieną kartą išsaugojus filmą',
        ),

        'invalid' => array(
          'metaType' => 'meta', 
          'label' => 'Filmas pripažintas negaliojančiu', 
          'showInFrontEnd' => false,
          'showInFrontEndNever' => true,  
          'inputType' => 'checkbox',
          'checkboxOptions' => array(
            1 => 'Taip'
          ),
          'searchInFrontEnd' => false,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
        ),

        'data_declared_invalid_date' => array(
          'metaType' => 'meta', 
          'label' => 'Registracijos pripažinimo negaliojančia data', 
          'showInFrontEnd' => true, 
          'showInFrontEndOnlyIfNotEmpty' => true, 
          'inputType' => 'date',
          'disabledForEdit' => true,
          'searchInFrontEnd' => false,
          'searchHandleType' => 'between',
          'searchInputHtmlFunction' => 'sihf_text_between',
          'class' => 'input-type-date',
          'backEndExplanation' => 'Užpildoma automatiškai išsaugojus filmą su pažymėta varnele "Filmas pripažintas negaliojančiu". Atžymėjus varnelę, data panaikinama',
        ),

        'unregistered' => array(
          'metaType' => 'meta', 
          'label' => 'Filmas išregistruotas', 
          'showInFrontEnd' => false, 
          'showInFrontEndNever' => true, 
          'inputType' => 'checkbox',
          'checkboxOptions' => array(
            1 => 'Taip'
          ),
          'searchInFrontEnd' => false,
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
        ),

        'unregister_date' => array(
          'metaType' => 'meta', 
          'label' => 'Išregistravimo data', 
          'showInFrontEnd' => true, 
          'showInFrontEndOnlyIfNotEmpty' => true, 
          'searchInFrontEnd' => false,
          'inputType' => 'date',
          'disabledForEdit' => true,
          'searchHandleType' => 'between',
          'searchInputHtmlFunction' => 'sihf_text_between',
          'class' => 'input-type-date',
          'backEndExplanation' => 'Užpildoma automatiškai išsaugojus filmą su pažymėta varnele "Filmas išregistruotas". Atžymėjus varnelę, data panaikinama',
        ),

        'is_national' => array(
          'metaType' => 'meta', 
          'label' => 'Ar filmas laikomas nacionaliniu?', 
          'showInFrontEnd' => false, 
          'showInFrontEndNever' => true, 
          'inputType' => 'radio',
          'radioOptions' => array(
            1 => 'Taip',
            0 => 'Ne'
          ),
          'disabledForEdit' => true,
          'searchInFrontEnd' => false,
          'searchHandleType' => 'metaQueryEqual',
          'searchInputHtmlFunction' => 'sihf_checkbox',
          'backEndExplanation' => 'Ar filmas nacionalinis nustatoma automatiškai pagal šiuos laukus: filmo gamintojo viena iš valstybių yra Lietuva; filmo originalo kalba yra lietuvių arba filmas subtitruotas į lietuvių kalbą.'
        ),
        

        // field to be shown on search only. When searching uses 'shower' and 'first_record_producer' fields
        // don't know how to execure search, disabling for now
        // 'applier' => array(
        //   'metaType' => 'meta', 
        //   'label' => 'Paraiškos teikėjas', 
        //   'showInFrontEnd' => false, 
        //   'showInFrontEndNever' => true, 
        //   'inputType' => 'foo',
        //   'searchInFrontEnd' => false,
        //   'searchHandleType' => 'metaQueryLike',
        //   'searchInputHtmlFunction' => 'sihf_text',
        // ),


/*
        // fields, that were in old database, but not needed in the new one  (absoleteField)
        // not all listed here probably

        'actors' => array(
          'metaType' => 'meta', 
          'label' => 'Aktoriai', 
          'showInFrontEnd' => false, 
          'searchInFrontEnd' => false,
          'inputType' => 'text',
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
          // 'class' => 'input-type-date',
          'legacyField' => 'atlikejai',
          'absoleteField' => true,
        ),
        'studio' => array(
          'metaType' => 'meta', 
          'label' => 'Studija', 
          'showInFrontEnd' => false, 
          'searchInFrontEnd' => false,
          'inputType' => 'text',
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
          'legacyField' => 'studija',
          'absoleteField' => true,
        ),
        'language_original' => array(
          'metaType' => 'meta', 
          'label' => 'Kalba originalo', 
          'showInFrontEnd' => false, 
          'searchInFrontEnd' => false,
          'inputType' => 'text',
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
          'legacyField' => 'kalba_orig',
          'absoleteField' => true,
        ),
        'language_titers' => array(
          'metaType' => 'meta', 
          'label' => 'Kalba titrų', 
          'showInFrontEnd' => false, 
          'searchInFrontEnd' => false,
          'inputType' => 'text',
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
          'legacyField' => 'kalba_titrai',
          'absoleteField' => true,
        ),
        'language_duplicate' => array(
          'metaType' => 'meta', 
          'label' => 'Kalba dublio', 
          'showInFrontEnd' => false, 
          'searchInFrontEnd' => false,
          'inputType' => 'text',
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
          'legacyField' => 'kalba_dublis',
          'absoleteField' => true,
        ),
        'format' => array(
          'metaType' => 'taxonomy', 
          'label' => 'Formatas', 
          'showInFrontEnd' => false, 
          'taxonomySlug' => 'film-format',
          'searchInFrontEnd' => false,
          'inputType' => 'select',
          'dropdownOptions' => array(),
          'searchHandleType' => 'taxonomy',
          'searchInputHtmlFunction' => 'sihf_select',
          'legacyField' => 'formatas',
          'absoleteField' => true,
        ),
        'colored' => array(
          'metaType' => 'meta', 
          'label' => 'Ar spalvotas', 
          'showInFrontEnd' => false, 
          'inputType' => 'select',
          'dropdownOptions' => array(),
          'searchInFrontEnd' => false,
          'searchHandleType' => 'metaQueryEqual',
          'searchInputHtmlFunction' => 'sihf_select',
          'absoleteField' => true,
        ),
        'number_of_copies' => array(
          'metaType' => 'meta', 
          'label' => 'Kopijų skaičius', 
          'showInFrontEnd' => false, 
          'searchInFrontEnd' => false,
          'inputType' => 'text',
          'searchHandleType' => 'metaQueryLike',
          'searchInputHtmlFunction' => 'sihf_text',
          'legacyField' => 'kopijos',
          'absoleteField' => true,
        ),
*/
        // also:

        // saugykla
        // anotacija
        // kalba_anot


        // 'status' => array('metaType' => 'meta', 'label' => 'Patvirtintas', 'showInForm' => false),
    );


	var $countryList = array(
		0 => '',
		'ab' =>'Abchazija',
		'af' =>'Afganistanas',
		'ie' =>'Airija',
		'al' =>'Albanija',
		'dz' =>'Alžyras',
		'as' =>'Amerikos Samoa',
		'ad' =>'Andora',
		'ai' =>'Angilija',
		'ao' =>'Angola',
		'aq' =>'Antarktida',
		'ag' =>'Antigva ir Barbuda',
		'ar' =>'Argentina',
		'am' =>'Armėnija',
		'aw' =>'Aruba',
		'au' =>'Australija',
		'at' =>'Austrija',
		'az' =>'Azerbaidžanas',
		'bs' =>'Bahamos',
		'bh' =>'Bahreinas',
		'by' =>'Baltarusija',
		'bd' =>'Bangladešas',
		'bb' =>'Barbadosas',
		'be' =>'Belgija',
		'bz' =>'Belizas',
		'bj' =>'Beninas',
		'bm' =>'Bermuda',
		'gw' =>'Bisau Gvinėja',
		'bo' =>'Bolivija',
		'ba' =>'Bosnija ir Hercegovina',
		'bw' =>'Botsvana',
		'br' =>'Brazilija',
		'bn' =>'Brunėjus',
		'bg' =>'Bulgarija',
		'bf' =>'Burkina Fasas',
		'bi' =>'Burundis',
		'bt' =>'Butanas',
		'cf' =>'Centrinės Afrikos Respublika',
		'td' =>'Čadas',
		'cz' =>'Čekija',
		'cl' =>'Čilė',
		'dk' =>'Danija',
		'vg' =>'Didžiosios Britanijos Mergelių salos',
		'dm' =>'Dominika',
		'do' =>'Dominikos Respublika',
		'ci' =>'Dramblio Kaulo Krantas',
		'je' =>'Džersis',
		'dj' =>'Džibutis',
		'eg' =>'Egiptas',
		'ec' =>'Ekvadoras',
		'er' =>'Eritrėja',
		'ee' =>'Estija',
		'et' =>'Etiopija',
		'fk' =>'Falklando salos',
		'fo' =>'Farerų salos',
		'fj' =>'Fidžis',
		'ph' =>'Filipinai',
		'ga' =>'Gabonas',
		'gy' =>'Gajana',
		'gm' =>'Gambija',
		'gh' =>'Gana',
		'gg' =>'Gernsis',
		'gi' =>'Gibraltaras',
		'gr' =>'Graikija',
		'gd' =>'Grenada',
		'gl' =>'Grenlandija',
		'ge' =>'Gruzija',
		'gu' =>'Guamas',
		'gp' =>'Gvadelupa',
		'gt' =>'Gvatemala',
		'gn' =>'Gvinėja',
		'ht' =>'Haitis',
		'hm' =>'Herdo ir Makdonaldo salos',
		'hn' =>'Hondūras',
		'hk' =>'Honkongas',
		'in' =>'Indija',
		'io' =>'Indijos vandenyno britų salos',
		'id' =>'Indonezija',
		'iq' =>'Irakas',
		'ir' =>'Iranas',
		'is' =>'Islandija',
		'es' =>'Ispanija',
		'it' =>'Italija',
		'il' =>'Izraelis',
		'jm' =>'Jamaika',
		'jp' =>'Japonija',
		'ye' =>'Jemenas',
		'jo' =>'Jordanija',
		'ae' =>'Jungtiniai Arabų Emyratai',
		'gb' =>'Jungtinė Karalystė',
		'us' =>'JAV',
		'ky' =>'Kaimanų salos',
		'cx' =>'Kalėdų sala',
		'kh' =>'Kambodža',
		'cm' =>'Kamerūnas',
		'ca' =>'Kanada',
		'qa' =>'Kataras',
		'kz' =>'Kazachija',
		'ke' =>'Kenija',
		'cn' =>'Kinija',
		'cy' =>'Kipras',
		'kg' =>'Kirgizija',
		'ki' =>'Kiribatis',
		'cc' =>'Kokosų salos',
		'co' =>'Kolumbija',
		'km' =>'Komorai',
		'cd' =>'Kongo Demokratinė Respublika',
		'cg' =>'Kongas',
		'ks' =>'Kosovas',
		'cr' =>'Kosta Rika',
		'hr' =>'Kroatija',
		'cu' =>'Kuba',
		'ck' =>'Kuko salos',
		'kw' =>'Kuveitas',
		'la' =>'Laosas',
		'lv' =>'Latvija',
		'pl' =>'Lenkija',
		'ls' =>'Lesotas',
		'lb' =>'Libanas',
		'lr' =>'Liberija',
		'ly' =>'Libija',
		'li' =>'Lichtenšteinas',
		'lt' =>'Lietuva',
		'lu' =>'Liuksemburgas',
		'yt' =>'Majotas',
		'mo' =>'Makao',
		'mg' =>'Madagaskaras',
		'mk' =>'Makedonija',
		'my' =>'Malaizija',
		'mw' =>'Malavis',
		'mv' =>'Maldyvai',
		'ml' =>'Malis',
		'mt' =>'Malta',
		'mp' =>'Marianos šiaurinės salos',
		'ma' =>'Marokas',
		'mq' =>'Martinika',
		'mh' =>'Maršalo salos',
		'mu' =>'Mauricijus',
		'mr' =>'Mauritanija',
		'mx' =>'Meksika',
		'im' =>'Meno sala',
		'vi' =>'Mergelių salos (JAV)',
		'mm' =>'Mianmaras',
		'fm' =>'Mikronezijos Federacinės Valstijos',
		'md' =>'Moldavija',
		'mc' =>'Monakas',
		'mn' =>'Mongolija',
		'ms' =>'Montseratas',
		'mz' =>'Mozambikas',
		'na' =>'Namibija',
		'nc' =>'Naujoji Kaledonija',
		'nz' =>'Naujoji Zelandija',
		'nr' =>'Nauru',
		'np' =>'Nepalas',
		'nl' =>'Nyderlandai',
		'an' =>'Nyderlandų Antilai',
		'ng' =>'Nigerija',
		'ne' =>'Nigeris',
		'ni' =>'Nikaragva',
		'nu' =>'Niujė',
		'nf' =>'Norfolkas',
		'no' =>'Norvegija',
		'om' =>'Omanas',
		'pk' =>'Pakistanas',
		'pw' =>'Palau',
		'ps' =>'Palestina',
		'pa' =>'Panama',
		'pg' =>'Papua Naujoji Gvinėja',
		'py' =>'Paragvajus',
		'pe' =>'Peru',
		'za' =>'Pietų Afrikos Respublika',
		'gs' =>'Pietų Džordžija ir Pietų Sandvičo salos',
		'kr' =>'Pietų Korėja',
		'pn' =>'Pitkerno salos',
		'pt' =>'Portugalija',
		'gf' =>'Prancūzijos Gviana',
		'pf' =>'Prancūzijos Polinezija',
		'fr' =>'Prancūzija',
		'tf' =>'Prancūzijos Pietų ir Antarkties sritys',
		'pr' =>'Puerto Rikas',
		'gq' =>'Pusiaujo Gvinėja',
		're' =>'Reunionas',
		'rw' =>'Ruanda',
		'ro' =>'Rumunija',
		'ru' =>'Rusija',
		'tp' =>'Rytų Timoras',
		'sb' =>'Saliamono salos',
		'sv' =>'Salvadoras',
		'ws' =>'Samoa',
		'sm' =>'San Marinas',
		'st' =>'San Tomė ir Prinsipė',
		'sa' =>'Saudo Arabija',
		'sc' =>'Seišeliai',
		'pm' =>'Sen Pjeras ir Mikelonas',
		'sn' =>'Senegalas',
		'kn' =>'Sent Kitsas ir Nevis',
		'lc' =>'Sent Lusija',
		'vc' =>'Sent Vinsentas ir Grenadinai',
		'sl' =>'Siera Leonė',
		'sg' =>'Singapūras',
		'sy' =>'Sirija',
		'sk' =>'Slovakija',
		'si' =>'Slovėnija',
		'so' =>'Somalis',
		'sd' =>'Sudanas',
		'fi' =>'Suomija',
		'sr' =>'Surinamas',
		'sj' =>'Svalbardas',
		'sz' =>'Svazilandas',
		'kp' =>'Šiaurės Korėja',
		'lk' =>'Šri Lanka',
		'sh' =>'Šventoji Elena',
		'se' =>'Švedija',
		'ch' =>'Šveicarija',
		'tj' =>'Tadžikija',
		'th' =>'Tailandas',
		'tw' =>'Taivanas',
		'tz' =>'Tanzanija',
		'tc' =>'Terksas ir Kaikosas',
		'tg' =>'Togas',
		'tk' =>'Tokelau',
		'to' =>'Tonga',
		'tt' =>'Trinidadas ir Tobagas',
		'tn' =>'Tunisas',
		'tr' =>'Turkija',
		'tm' =>'Turkmėnija',
		'tv' =>'Tuvalu',
		'ug' =>'Uganda',
		'ua' =>'Ukraina',
		'uy' =>'Urugvajus',
		'uz' =>'Uzbekija',
		'eh' =>'Vakarų Sachara',
		'vu' =>'Vanuatu',
		'va' =>'Vatikanas',
		've' =>'Venesuela',
		'hu' =>'Vengrija',
		'vn' =>'Vietnamas',
		'de' =>'Vokietija',
		'wf' =>'Volisas ir Futuna',
		'zm' =>'Zambija',
		'zw' =>'Zimbabvė',
		'cv' =>'Žaliasis Kyšulys'
	);
	
	var $countryList22 = array(

0 => '',
'aa' => 'Afarų kalba',
'ab' => 'Abchazų kalba',
'ae' => 'Avestos kalba',
'af' => 'Afrikanų kalba',
'am' => 'Amherų kalba',
'an' => 'Aragoničių kalba',
'ar' => 'Arabų kalba',
'as' => 'Asamų kalba',
'av' => 'Avarų kalba',
'ay' => 'Aimarų kalba',
'az' => 'Azerbaidžaniečių kalba',
'ba' => 'Baškirų kalba',
'be' => 'Baltarusių kalba',
'bg' => 'Bulgarų kalba',
'bh' => 'Biharų kalba',
'bi' => 'Bislamų kalba',
'bm' => 'Bambarų kalba',
'bn' => 'Bengalų kalba',
'bo' => 'Tibetiečių kalba',
'br' => 'Bretonų kalba',
'bs' => 'Bosnių kalba',
'ca' => 'Katalonų kalba',
'ce' => 'Čečėnų kalba',
'ch' => 'Čamorų kalba',
'co' => 'Korsikiečių kalba',
'cr' => 'Kri kalba',
'cs' => 'Čekų kalba',
'cu' => 'Bažnytinė slavų kalba',
'cv' => 'Čiuvašų kalba',
'cy' => 'Valų kalba',
'da' => 'Danų kalba',
'de' => 'Vokiečių kalba',
'dv' => 'Maldyviečių kalba',
'dz' => 'Botijų kalba',
'ee' => 'Evų kalba',
'el' => 'Graikų kalba',
'en' => ' Anglų kalba',
'eo' => 'Esperanto kalba',
'es' => 'Ispanų kalba',
'et' => 'Estų kalba',
'eu' => 'Baskų kalba',
'fa' => 'Persų kalba',
'ff' => 'Fulų kalba',
'fi' => 'Suomių kalba',
'fj' => 'Fidžių kalba',
'fo' => 'Fererų kalba',
'fr' => 'Prancūzų kalba',
'fy' => 'Fryzų kalba',
'ga' => 'Airių kalba',
'gd' => 'Škotų gėlių kalba',
'gl' => 'Galisų kalba',
'gn' => 'Gvaranių kalba',
'gu' => 'Gudžaratų kalba',
'gv' => 'Menksiečių kalba',
'ha' => 'Hausų kalba',
'he' => 'Hebrajų kalba',
'hi' => 'Hindi kalba',
'ho' => 'Hiri motu kalba',
'hr' => 'Kroatų kalba',
'ht' => 'Haičio kleorų kalba',
'hu' => 'Vnegrų kalba',
'hy' => 'Armėnų kalba',
'hz' => 'Herero kalba',
'ia' => 'Interlingua kalba',
'id' => 'Indoneziečių kalba',
'ie' => 'Interlingue kalba',
'ig' => 'Igbų kalba',
'ii' => 'Ji kalba',
'ik' => 'Inupiakų kalba',
'io' => 'Ido kalba',
'is' => 'Islandų kalba',
'it' => 'Italų kalba',
'iu' => 'Inuktituto kalba',
'ja' => 'Japonų kalba',
'jv' => 'Javiečių kalba',
'ka' => 'Gruzinų kalba',
'kg' => 'Kongo kalba',
'ki' => 'Kikujų kalba',
'kj' => 'Kvanjamų kalba',
'kk' => 'Kazachų kalba',
'kl' => 'Grenlandų kalba',
'km' => 'Kmerų kalba',
'kn' => 'Kanadų kalba',
'ko' => 'Korėjiečių kalba',
'kr' => 'Kanurių kalba',
'ks' => 'Kašmyriečių kalba',
'ku' => 'Kurdų kalba',
'kv' => 'Komių kalba',
'kw' => 'Kornų kalba',
'ky' => 'Kirgizų kalba',
'la' => 'Lotynų kalba',
'lb' => 'Liuksembugiečių kalba',
'lg' => 'Lugandų kalba',
'li' => 'Limburgiečių kalba',
'ln' => 'Lingalų kalba',
'lo' => 'Lao kalba',
'lt' => 'Lietuvių kalba',
'lu' => 'Lubų kalba',
'lv' => 'Latvių kalba',
'mg' => 'Malagasių kalba',
'mh' => 'Maršaliečių kalba',
'mi' => 'Maorių kalba',
'mk' => 'Makedonų kalba',
'ml' => 'Malajalių kalba',
'mn' => 'Mongolų kalba',
'mr' => 'Maratų kalba',
'ms' => 'Malajų kalba',
'mt' => 'Maltiečių kalba',
'my' => 'Mjanmų kalba',
'na' => 'Nauriečių kalba',
'nb' => 'Norvegų kalba',
'nd' => 'Šiaurės ndebelų kalba',
'ne' => 'Nepalų kalba',
'ng' => 'Ndongų kalba',
'nl' => 'Olandų kalba',
'nn' => 'Naujoji norvegų kalba',
'no' => 'Norvegų kalba',
'nr' => 'Pietų ndebelų kalba',
'nv' => 'Nacachų kalba',
'ny' => 'Čičevų kalba',
'oc' => 'Oksitanų kalba',
'oj' => 'Odžibvių kalba',
'om' => 'Oromų kalba',
'or' => 'Orijų kalba',
'os' => 'Osetinų kalba',
'pa' => 'Pendžabų kalba',
'pi' => 'Pali kalba',
'pl' => 'Lenkų kalba',
'ps' => 'Puštūnų kalba',
'pt' => 'Portugalų kalba',
'qu' => 'Kečujų kalba',
'rm' => 'Retoromanų kalba',
'rn' => 'Kirundžių kalba',
'ro' => 'Rumunų kalba',
'ru' => 'Rusų kalba',
'rw' => 'Kinjaruanda kalba',
'sa' => 'Sanskritas kalba',
'sc' => 'Skardų kalba',
'sd' => 'Sindžų kalba',
'se' => 'Šiaurės samių kalba',
'sg' => 'Sangų kalba',
'si' => 'Sinhalų kalba',
'sh' => 'Serbų kroatų kalba',
'sk' => 'Slovakų kalba',
'sl' => 'Slovėnų kalba',
'sm' => 'Samoa kalba',
'sn' => 'Šonų kalba',
'so' => 'Somalių kalba',
'sq' => 'Albanų kalba',
'sr' => 'Serbų kalba',
'ss' => 'Svazių kalba',
'st' => 'Pietų sotų kalba',
'su' => 'Sundų kalba',
'sv' => 'Švedų kalba',
'sw' => 'Suahilių kalba',
'ta' => 'Tamilių kalba',
'te' => 'Telugų kalba',
'tg' => 'Tadžikų kalba',
'th' => 'Tajų kalba',
'ti' => 'Tigrinijų kalba',
'tk' => 'Turkmėnų kalba',
'tl' => 'Tegalų kalba',
'tn' => 'Tsvanų kalba',
'to' => 'Tongos kalba',
'tr' => 'Turkų kalba',
'ts' => 'Tsongų kalba',
'tt' => 'Totorių kalba',
'tw' => 'Tvi kalba',
'ty' => 'Taitiečių kalba',
'ug' => 'Uigurų kalba',
'uk' => 'Ukrainiečių kalba',
'ur' => 'Urdu kalba',
'uz' => 'Uzbegų kalba',
've' => 'Vendų kalba',
'ir' => 'Vietnamiečių kalba',
'vo' => 'Volapiuko kalba',
'wa' => 'Valonų kalba',
'wo' => 'Volofų kalba',
'xh' => 'Kosų kalba',
'yi' => 'Jidiš kalba',
'yo' => 'Jorubų kalba',
'za' => 'Džiuangų kalba',
'zh' => 'Kinų kalba',
'zu' => 'Zulų kalba'
);
	
}
?>
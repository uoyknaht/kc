<div class="kcsite-meta-control post-type-<?php echo get_post_type(); ?>">
	
	<?php 
	global $g_filmRegisterFields;
	// print_r($mb);exit;
	// print_r($g_filmRegisterFields);exit;

	foreach ($g_filmRegisterFields as $key => $val) {
		if($val['metaType'] == 'meta'){
			if(!isset($val['absoleteField'])){
				if(isset($val['disabledForEdit']) && $val['disabledForEdit'] == true) {
					$disabledAttr = 'disabled="disabled"';
					$disabledClass = 'disabled';
				} else {
					$disabledAttr = '';
					$disabledClass = '';
				}

				if(isset($val['multipleSelect']) && $val['multipleSelect'] == true){
					$mb->the_field($key, WPALCHEMY_FIELD_HINT_SELECT_MULTI); 
				} else {
					$mb->the_field($key); 
				} ?>
				<p class="kcsite-posts-metabox-<?php echo $key; ?>">
					<label><?php echo $val['label'];?></label><br/>

					<?php 
					switch ($val['inputType']) {
						case 'text':
							?> 
							<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" class="input-type-<?php echo $val['inputType'];?> regular-text <?php echo $disabledClass;?>" <?php echo $disabledAttr;?> />
							<?php
							break;

						case 'date':
							?> 
							<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" class="input-type-<?php echo $val['inputType'];?> <?php echo $disabledClass;?>" <?php echo $disabledAttr;?> />
							<?php
							break;

						case 'select':

                            global $post;
                            $isImportedFilm = get_post_meta($post->ID, '_lkc_film_imported', true);

							/*if($key == 'country' && isset($mb->meta['country_hardcoded'])){ ?>*/
                            /*if($isImportedFilm && $key != 'license_territory') { ?>*/
                            $theValue = $mb->get_the_value();
                            // echo $theValue;
                            if (is_array($theValue) || $theValue == '' || array_key_exists(strtolower($theValue), $val['dropdownOptions'])) {
                            // if (1<0) { 
                            ?>
                            <select name="<?php $mb->the_name(); ?>" class="input-type-<?php echo $val['inputType'];?> chosen <?php echo $disabledClass;?>" <?php if(isset($val['multipleSelect']) && $val['multipleSelect'] == true) echo 'multiple="multiple"';?> <?php echo $disabledAttr;?>>
                                <!-- <option value="">---</option> -->
                                <?php 
                                $i = 0;
                                foreach ($val['dropdownOptions'] as $key2 => $val2) {
                                    if($i == 0) {
                                        $selectedState = '';
                                    } else {
                                        $selectedState = $mb->get_the_select_state($key2);
                                        //$selectedState = $mb->get_the_select_state($key2, strtolower($theValue));

                                        // hack. Imported values are uppercase so does not get seelcted in dropdown. Converting.

                                        $selected = false;
                                        if (is_array($theValue)) {
                                            // if (in_array($v, $the_value)) return TRUE;
                                            if (in_array($key2, array_map('strtolower', $theValue))) $selected =  TRUE;
                                        }
                                        elseif($key2 == strtolower($theValue))
                                        {
                                            $selected =  TRUE;
                                        }

                                        if ($selected) {
                                             $selectedState = 'selected="selected"';
                                         } else {
                                             $selectedState = '';
                                         }


                                    }
                                    $i++;
                                 ?>
                                    <option value="<?php echo $key2?>"<?php echo $selectedState; ?>><?php echo $val2;?></option>
                                <?php } ?>
                            </select>
                            <?php } else { ?>
                                <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" class="input-type-<?php echo $val['inputType'];?> regular-text <?php echo $disabledClass;?>" <?php echo $disabledAttr;?> />
                                <div class="metabox-explanation" style="color: #fa6c6c;">Importuojant filmą nepavyko tiksliai nustatyti laukelio reikšmių,kad galėtų būtų rodoma parinkčių juosta. Todėl reikšmė pateikiama paprastame tekstiniame laukelyje.</div>

                            <?php }
							break;

                        case 'radio':
                            echo $disabled;

                            foreach ($val['radioOptions'] as $key2 => $val2){ ?>
                                <?php $mb->the_field($key); ?>
                                <input type="radio" name="<?php $mb->the_name(); ?>" value="<?php echo $key2; ?>" class="<?php echo $disabledClass;?>" <?php $mb->the_radio_state($key2); ?> <?php echo $disabledAttr;?> /> <?php echo $val2; ?> &nbsp;&nbsp;
                            <?php }

                            break;

                        case 'checkbox':
                            foreach ($val['checkboxOptions'] as $key2 => $val2){ ?>
                                <?php $mb->the_field($key); ?>
                                <input type="checkbox" name="<?php $mb->the_name(); ?>" value="<?php echo $key2; ?>" class="<?php echo $disabledClass;?>" <?php if ($mb->get_the_value()) echo 'checked="checked"'; ?>/> <?php echo $val2; ?>
                            <?php }
                            break;

                        // in case there multiple checkboxes, would need to think how to change input name for every checkbox
						/*case 'checkboxMultiple': 
                            foreach ($val['checkboxOptions'] as $key2 => $val2){ ?>
                                <?php $mb->the_field($key); ?>
                                <input type="checkbox" name="<?php $mb->the_name(); ?>" value="<?php echo $key2; ?>" class="<?php echo $disabledClass;?>" <?php if ($mb->get_the_value()) echo 'checked="checked"'; ?>/> <?php echo $val2; ?>
                            <?php }
							break;*/
						
						default:
							# code...
							break;
					}

					?>
					
					<?php if(isset($val['backEndExplanation'])){ ?>
						<div class="metabox-explanation"><?php echo $val['backEndExplanation']; ?></div>
					<?php } ?>
				</p>	
		<?php }
		}
	} ?>

    <hr />

    <p>Eksportavimas:</p>
    <p>
        <button type="submit" name="filmExportType" value="filmo-indekso-pazyma" class="button button-primary button-large">Indekso pažyma</button>
        <button type="submit" name="filmExportType" value="iregistruoto-filmo-israsas" class="button button-primary button-large">Įregistruoto filmo išrašas</button>
    </p>

</div>








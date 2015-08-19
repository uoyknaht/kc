<div class="kcsite-meta-control post-type-<?php echo get_post_type(); ?>">
	
	<?php 
	global $g_lithuanianFilms;
	// print_r($mb);exit;
	// print_r($g_filmRegisterFields);exit;
?>


<!--
<?php  ?>
<div class="my_meta_control">

    <label><?php _e('Spalvos:', 'kcsite');?></label><br/>

    <?php while($metabox->have_fields_and_one('product_color')): ?>
    <p>
        <input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" class="product-color" />
        <span class="color-holder help-inline" style="background-color:<?php $mb->the_value(); ?>;"></span>
    </p>
    <?php endwhile; ?>

</div>
<?php  ?>


<?php while($mb->have_fields_and_multi('docs')): ?>
<?php $mb->the_group_open(); ?>
 
    <?php $mb->the_field('title'); ?>
    <label>Title and URL</label>
    <p><input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/></p>
    <a href="#" class="dodelete button">Remove Document</a>
 
<?php $mb->the_group_close(); ?>
<?php endwhile; ?>
 
<p style="margin-bottom:15px; padding-top:5px;"><a href="#" class="docopy-docs button">Add Document</a></p>
-->


<?php





	foreach ($g_lithuanianFilms->fields as $key => $val) {
		if($val['metaType'] == 'meta'){  ?>
        
            <p class="kcsite-posts-metabox-<?php echo $key; ?>">
            <label><?php echo $val['label'];?></label><br/>

            <?php
            if ($val['metaFieldMultiple'] == true) {
                $i = 0;
                while($mb->have_fields_and_multi($key)) {
                    $i++;
                    $mb->the_group_open(); 
                    $g_lithuanianFilms->generateMetaFieldsForAdmin ($key, $val, $mb);

                    if ($i > 1) { ?>
                        <a href="#" class="dodelete button">Pašalinti</a>                
                    <?php } ?>
                    <?php
                    $mb->the_group_close();
                } ?>
                <div style="margin-bottom:15px; margin-top: 5px;"><a href="#" class="docopy-<?php echo $key; ?> button">Pridėti dar vieną</a></div> 
            <?php } else {
                $g_lithuanianFilms->generateMetaFieldsForAdmin ($key, $val, $mb);
            }

            if(isset($val['backEndExplanation'])){ ?>
                <div class="metabox-explanation"><?php echo $val['backEndExplanation']; ?></div>
            <?php }  ?>
            </p>
            <?php  
        }
	} ?>

<?php /*
    <hr />

    <p>Eksportavimas:</p>
    <p>
        <button type="submit" name="filmExportType" value="filmo-indekso-pazyma" class="button button-primary button-large">Indekso pažyma</button>
        <button type="submit" name="filmExportType" value="iregistruoto-filmo-israsas" class="button button-primary button-large">Įregistruoto filmo išrašas</button>
    </p>
*/ ?>
</div>








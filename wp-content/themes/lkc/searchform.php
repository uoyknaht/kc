<?php
/**
 * The template for displaying search forms in Twenty Eleven
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?>

<form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="search-form clearfix">
	<input type="text" name="s" onfocus="if(this.value=='<?php pll_e('Įveskite žodį ar frazę'); ?>') this.value='';" onblur="if(this.value=='') this.value='<?php pll_e('Įveskite žodį ar frazę'); ?>';" value="<?php pll_e('Įveskite žodį ar frazę'); ?>" class="search-form-text" /> 	
	<input type="submit" name="submit" class="search-form-submit" style="overflow: hidden" value="<?php pll_e('Rasti'); ?>" />
	<i class="icon-glass"></i><br><br>
<a style="color: #FFF; display: inline-block; text-transform: uppercase; font-size: 8.5pt; margin-top: 4px;" href="/vartotoju-sistema/">Vartotojų sistema</a>
</form>
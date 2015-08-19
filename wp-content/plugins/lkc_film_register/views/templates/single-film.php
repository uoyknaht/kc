<?php
/**
 * The Template for displaying single film
 *
 */

get_header(); 

global $g_filmRegisterFields;

global $lkc_film_metabox;
$lkc_film_metabox->the_meta();

global $kcsite_post_metabox;
$kcsite_post_metabox->the_meta();

global $FilmRegister;
?>

<div class="content-single single-film">

	<div class="page-content">

		<?php 
		global $post;
		the_post();
		//get_template_part('content', 'single'); 	
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class('single-article clearfix'); ?>>
		 	<header class="entry-header">
				<h1 class="fancy-title"><?php the_title();?></h1>
			</header>
			<hr class="double-hr entry-header-hr"/>

			<div class="entry-content">

                <?php if(!$FilmRegister->visibleInFrontend(get_the_ID())){ ?>
                    <div class="alert alert-error"><?php pll_e('Filmas nepaskelbtas, negaliojantis arba išregistruotas'); ?></div>
                <?php } ?>

                <?php if($FilmRegister->visibleInFrontend(get_the_ID()) || current_user_can('administrator') || current_user_can('semi_admin')){ ?>

                    <p class="ar"> <a href="#" class="js-print-btn" data-div="single-film-table-print-wrap"> <i class="icon-print"></i></a> </p>

                    <div id="single-film-table-print-wrap">
                        <table class="single-film-table film-print-table">
                            <?php foreach ($g_filmRegisterFields as $key => $val) {
                                if($val['showInFrontEnd'] == true || current_user_can('administrator') || current_user_can('semi_admin')){ 
                                    if(!isset($val['showInFrontEndNever'])){
                                        $output = $FilmRegister->outputField($key);
                                        if(!isset($val['showInFrontEndOnlyIfNotEmpty']) || (isset($val['showInFrontEndOnlyIfNotEmpty']) && $output != '')  ){ ?>
                                            <tr>
                                                <td class="first"><?php echo $val['label']; ?></td>
                                                <td class="second"><?php echo $FilmRegister->outputField($key); ?></td>
                                            </tr>
                                    <?php }
                                    }
                                }
                            } ?>
                        </table>
                    </div>
                <?php } ?>


				<?php //the_content(); ?>

				<?php get_template_part('template-parts/entry-meta'); ?>

				<?php echo $FilmRegister->refererBtn(); ?>

			</div>

		</article><!-- #post-<?php the_ID(); ?> -->


	</div><!-- .page-content -->
</div>

<script type="text/javascript">
jQuery(document).ready(function(){
    // jQuery('.js-print-btn').click(function(){
    //     var $this = jQuery(this),
    //         $id = $this.data('div'),
    //         $css = new String ('<link href="<?php echo $FilmRegister->pluginUrl;?>/assets/css/print.css" rel="stylesheet" type="text/css">'),
    //      $content = document.getElementById($id),
    //         $html = '<html><head>'+$css+'</head><body>'+$content.innerHTML+'</body></html>';

    //     var WinPrint = window.open('', '', 'letf=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
    //     WinPrint.document.write($html);
    //     WinPrint.document.close();
    //     WinPrint.focus();
    //     WinPrint.print();
    //     // WinPrint.close();

    //     return false;
    // });
    
    jQuery('.js-print-btn').click(function(){
        handlePrintBtnClick(jQuery(this), '<?php echo $FilmRegister->pluginUrl;?>/assets/css/print.css');
    });
});

</script>

<?php get_footer(); ?>
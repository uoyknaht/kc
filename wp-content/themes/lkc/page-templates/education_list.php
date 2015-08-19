<?php
/*
Template Name: Edukacija - resursai
*/

get_header(); ?>

<div class="content-page page-template-education-list">

    <?php if(current_user_can('see_education_resource')){ ?>

        <div class="page-content">
            <?php 
            the_post();
            get_template_part('content', 'page');   
            ?>
        </div><!-- .page-content -->

    <?php } else { ?>

        <div class="alert alert-error"><?php pll_e('Jūs neturite teisės matyti šios informacijos');?></div>

    <?php } ?>

</div>

<?php get_footer(); ?>

<?php 
// old:

/*
get_header(); ?>

<div class="content-page page-template-education-list">

    <?php 

    //$education = new EducationMain();

    if(current_user_can('see_education_resource')){ ?>

        <div class="page-content">
            <?php 
            the_post();
            get_template_part('content', 'page');   
            ?>
        </div><!-- .page-content -->


        <?php 
        global $post;
        global $more;

        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

        $args = array('post_type' => 'education-resource', 'orderby' => 'date', 'order' => 'DESC', 'paged' => $paged, 'posts_per_page' => get_option('posts_per_page'));            
        $the_query = new WP_Query( $args );
        ?>                  

        <?php   
        $i=0;   
        while ($the_query->have_posts()){   
            $the_query->the_post();
            $more = 0;
            get_template_part('content', get_post_format());        
        }       
        ?>

        <?php 
        kcsite_nice_pagination($the_query->max_num_pages);

        wp_reset_query();
        wp_reset_postdata();
        ?>


    <?php } else { ?>
        <div class="alert alert-error"><?php pll_e('Jūs neturite teisės matyti šios informacijos');?></div>
    <?php } ?>

</div>

<?php get_footer(); ?>

*/
?>
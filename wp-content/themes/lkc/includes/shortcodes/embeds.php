<?php

    add_shortcode('slideshare', 'kcsite_slideshare_handler');
    
    function kcsite_slideshare_handler($atts, $content = null) {
        extract(shortcode_atts(array(
            //"width" => 520,
            "width" => 500, //match youtube and vimeo width
        ), $atts));
        
         if (isset($atts)) {

            $args   = str_replace('&#038;','&',$atts['id']);
            $args   = str_replace('&amp;','&',$args);
            $r      = wp_parse_args('id='.$args);

            $height = round($width / 1.32) + 34;

            return '<iframe src="http://www.slideshare.net/slideshow/embed_code/'.$r['id'].'" width="'.$width.'" height="'.$height.'" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe><br/><br/>';
        }

        return false;      

/*        $height = round($width / 1.32) + 34;
        $output = '<iframe src="http://www.slideshare.net/slideshow/embed_code/'.$id.'" width="'.$width.'" height="'.$height.'" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe><br/><br/>';
        return do_shortcode($output);  */     
    }


/*    function subscribe_form(){
        return 'aaa';
    }
    add_shortcode('lkc_newsletter_subscribe_form', 'subscribe_form');*/





?>
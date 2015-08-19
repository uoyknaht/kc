<?php
/**
 * The template for displaying attachment page
 *
 */

// http://wordpress.stackexchange.com/questions/27119/how-can-i-remove-image-taxonomy-pages-from-my-theme-and-from-google
    
if(!is_attachment()) return; 

global $post;
if(empty($post)) $post = get_queried_object();

$link = get_permalink($post->post_parent);

wp_redirect($link, 301);

exit(); 
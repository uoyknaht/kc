<?php

//$roles = get_option($wpdb->prefix . 'user_roles'); 
//remove_role('semi_admin');


// add_role('educator', 'Educator', array(
//     'switch_themes' => false,
//     'edit_themes' => false,
//     'activate_plugins' => false,
//     'edit_plugins' => false,
//     'edit_users' => false,
//     'edit_files' => false,
//     'manage_options' => false,
//     'moderate_comments' => false,
//     'manage_categories' => false,
//     'manage_links' => false,
//     'upload_files' => false,
//     'import' => false,
//     'unfiltered_html' => false,
//     'edit_posts' => false,
//     'edit_others_posts' => false,
//     'edit_published_posts' => false,
//     'publish_posts' => false,
//     'edit_pages' => false,
//     'read' => false,
//     'level_true0' => false,
//     'level_9' => false,
//     'level_8' => false,
//     'level_7' => false,
//     'level_6' => false,
//     'level_5' => false,
//     'level_4' => false,
//     'level_3' => false,
//     'level_2' => false,
//     'level_true' => false,
//     'level_0' => false,
//     'edit_others_pages' => false,
//     'edit_published_pages' => false,
//     'publish_pages' => false,
//     'delete_pages' => false,
//     'delete_others_pages' => false,
//     'delete_published_pages' => false,
//     'delete_posts' => false,
//     'delete_others_posts' => false,
//     'delete_published_posts' => false,
//     'delete_private_posts' => false,
//     'edit_private_posts' => false,
//     'read_private_posts' => false,
//     'delete_private_pages' => false,
//     'edit_private_pages' => false,
//     'read_private_pages' => false,
//     'delete_users' => false,
//     'create_users' => false,
//     'unfiltered_upload' => false,
//     'edit_dashboard' => false,
//     'update_plugins' => false,
//     'delete_plugins' => false,
//     'install_plugins' => false,
//     'update_themes' => false,
//     'install_themes' => false,
//     'update_core' => false,
//     'list_users' => false,
//     'remove_users' => false,
//     'add_users' => false,
//     'promote_users' => false,
//     'edit_theme_options' => false,
//     'delete_themes' => false,
//     'export' => false,
// ));

// add_role('semi_admin', 'Semi admin', array(
//     'switch_themes' => false,
//     'edit_themes' => false,
//     'activate_plugins' => false,
//     'edit_plugins' => false,
//     'edit_users' => false,
//     'edit_files' => true,
//     'manage_options' => true,
//     'moderate_comments' => true,
//     'manage_categories' => true,
//     'manage_links' => true,
//     'upload_files' => true,
//     'import' => true,
//     'unfiltered_html' => true,
//     'edit_posts' => true,
//     'edit_others_posts' => true,
//     'edit_published_posts' => true,
//     'publish_posts' => true,
//     'edit_pages' => true,
//     'read' => true,
//     'level_true0' => true,
//     'level_9' => true,
//     'level_8' => true,
//     'level_7' => true,
//     'level_6' => true,
//     'level_5' => true,
//     'level_4' => true,
//     'level_3' => true,
//     'level_2' => true,
//     'level_true' => true,
//     'level_0' => true,
//     'edit_others_pages' => true,
//     'edit_published_pages' => true,
//     'publish_pages' => true,
//     'delete_pages' => true,
//     'delete_others_pages' => true,
//     'delete_published_pages' => true,
//     'delete_posts' => true,
//     'delete_others_posts' => true,
//     'delete_published_posts' => true,
//     'delete_private_posts' => true,
//     'edit_private_posts' => true,
//     'read_private_posts' => true,
//     'delete_private_pages' => true,
//     'edit_private_pages' => true,
//     'read_private_pages' => true,
//     'delete_users' => false,
//     'create_users' => false,
//     'unfiltered_upload' => true,
//     'edit_dashboard' => false,
//     'update_plugins' => false,
//     'delete_plugins' => false,
//     'install_plugins' => false,
//     'update_themes' => false,
//     'install_themes' => false,
//     'update_core' => false,
//     'list_users' => true,
//     'remove_users' => false,
//     'add_users' => true,
//     'promote_users' => true,
//     'edit_theme_options' => true,
//     'delete_themes' => false,
//     'export' => true,
// ));



// add_action( 'admin_init', 'add_theme_caps');
function add_theme_caps() {

    // education
    // $role = get_role( 'semi_admin' );
    // $role->add_cap( 'see_education_resource' );
    // $role = get_role( 'administrator' );
    // $role->add_cap( 'see_education_resource' );

    // polls plugin
    //$role->add_cap( 'manage_polls' ); 

    // tribe events calendar plugin
    // $role->add_cap( 'edit_tribe_event' );
    // $role->add_cap( 'read_tribe_event' );
    // $role->add_cap( 'delete_tribe_event' );
    // $role->add_cap( 'delete_tribe_events');
    // $role->add_cap( 'edit_tribe_events' );
    // $role->add_cap( 'edit_others_tribe_events' );
    // $role->add_cap( 'delete_others_tribe_events' );
    // $role->add_cap( 'publish_tribe_events' );
    // $role->add_cap( 'edit_published_tribe_events' );
    // $role->add_cap( 'delete_published_tribe_events' );
    // $role->add_cap( 'delete_private_tribe_events' );
    // $role->add_cap( 'edit_private_tribe_events' );
    // $role->add_cap( 'read_private_tribe_events' );
    // $role->add_cap( 'edit_tribe_venue' );
    // $role->add_cap( 'read_tribe_venue' );
    // $role->add_cap( 'delete_tribe_venue' );
    // $role->add_cap( 'delete_tribe_venues');
    // $role->add_cap( 'edit_tribe_venues' );
    // $role->add_cap( 'edit_others_tribe_venues' );
    // $role->add_cap( 'delete_others_tribe_venues' );
    // $role->add_cap( 'publish_tribe_venues' );
    // $role->add_cap( 'edit_published_tribe_venues' );
    // $role->add_cap( 'delete_published_tribe_venues' );
    // $role->add_cap( 'delete_private_tribe_venues' );
    // $role->add_cap( 'edit_private_tribe_venues' );
    // $role->add_cap( 'read_private_tribe_venues' );
    // $role->add_cap( 'edit_tribe_organizer' );
    // $role->add_cap( 'read_tribe_organizer' );
    // $role->add_cap( 'delete_tribe_organizer' );
    // $role->add_cap( 'delete_tribe_organizers');
    // $role->add_cap( 'edit_tribe_organizers' );
    // $role->add_cap( 'edit_others_tribe_organizers' );
    // $role->add_cap( 'delete_others_tribe_organizers' );
    // $role->add_cap( 'publish_tribe_organizers' );
    // $role->add_cap( 'edit_published_tribe_organizers' );
    // $role->add_cap( 'delete_published_tribe_organizers' );
    // $role->add_cap( 'delete_private_tribe_organizers' );
    // $role->add_cap( 'edit_private_tribe_organizers' );
    // $role->add_cap( 'read_private_tribe_organizers' );

    // alo easy newsletter plugin
    // $role->add_cap('manage_newsletter_options');
    // $role->add_cap('manage_newsletter_subscribers');      
    // $role->add_cap('publish_newsletters');
    // $role->add_cap('edit_newsletters');
    // $role->add_cap('edit_others_newsletters');
    // $role->add_cap('delete_newsletters');
    // $role->add_cap('delete_others_newsletters');
    // $role->add_cap('read_private_newsletters');
    
    // quiz plugin
    // $role->add_cap('wpsqt-manage');
}

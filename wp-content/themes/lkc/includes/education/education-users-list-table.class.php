<?php

// http://wordpress.org/extend/plugins/custom-list-table-example/
// http://mac-blog.org.ua/wordpress-custom-database-table-example-full/

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class EducationUsersListTable extends WP_List_Table {
    
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'user',     //singular name of the listed records
            'plural'    => 'users',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
    
    function column_default($item, $column_name){
        switch($column_name){
            case 'user_email':
            // case 'user_login':            
            case 'first_name':            
            case 'last_name':            
            case 'organization':            
            case 'user_registered':            
            case 'status':            
                return $item->$column_name;
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    
    function column_cb($item){
        return '<input type="checkbox" name="ID[]" value="'.$item->ID.'" />';
    }    
    function column_user_email($item){
        $email = empty($item->user_email) ? 'Redaguoti' : $item->user_email;
        return '<a href="edit.php?post_type=education-resource&page=education-resource-users&do=edit&ID='.$item->ID.'">'.$email.'</a>';
    }     
    function column_status($item){
        $text = $item->status == Education::STATUS_USER_ENABLED ? 'Taip' : 'Ne';
        return $text;
    }   
    
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            // 'user_login'     => __('Username', 'lkc-newsletter'),
            'user_email'     => __('Email'),
            'first_name'     => __('Vardas', 'lkc-newsletter'),
            'last_name'     => __('Pavardė', 'lkc-newsletter'),
            'organization'     => __('Organizacija', 'lkc-newsletter'),
            'user_registered'     => __('Registracijos data', 'lkc-newsletter'),
            'status'     => __('Patvirtintas', 'lkc-newsletter'),
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            // 'user_login'     => array('user_login',false),     //true means it's already sorted
            'user_email'     => array('user_email',false),     //true means it's already sorted
            'first_name'     => array('first_name',false),     //true means it's already sorted
            'last_name'     => array('last_name',false),     //true means it's already sorted
            'organization'     => array('organizationt_name',false),     //true means it's already sorted
            'user_registered'     => array('user_registered',false),     //true means it's already sorted
            'status' => array('status',false),     //true means it's already sorted
        );
        return $sortable_columns;
    }
    
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }
    
    function process_bulk_action() {
        if ('delete' === $this->current_action()) {
            require_once('education.class.php');
            $education = new Education();
            $ids = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : array();
            $education->deleteUsers($ids);
        }
    }
    
    function prepare_items() {
        global $wpdb; 

        $per_page = 20;
        $current_page = $this->get_pagenum();
        $offset = ($current_page-1) * $per_page;
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
             
        $this->_column_headers = array($columns, $hidden, $sortable);    
        
        $this->process_bulk_action();      


        $args = array( 
            'role' => 'educator', 
            'fields' => 'all_with_meta',
            'number' => $per_page,
            'offset' => $offset, 
            'orderby' => 'user_registered', 
            'order' => 'desc', 
            // 'search' => $_REQUEST["s"],
            // 'search' => preg_replace( "^$\**(.*)\**$?#", "*$1*", $_REQUEST["s"]), // http://wordpress.stackexchange.com/questions/60347/searching-users-with-wp-list-table-plug-in
            'search' => '*'.$_REQUEST["s"].'*',
            'search_columns' => array(
                'ID',
                'user_email',
                'first_name',
                'last_name',
                'organization',
                'user_registered',
            )
        );

        if ($args['search'] !== '')
            $args['search'] = '*' . $args['search'] . '*';

        if (isset($_REQUEST['orderby']))
            $args['orderby'] = $_REQUEST['orderby'];

        if (isset( $_REQUEST['order']))
            $args['order'] = $_REQUEST['order'];

        $the_query = new WP_User_Query($args);
        $data = $the_query->get_results();

        $this->items = $data;

        $this->set_pagination_args( array(
            'total_items' => $the_query->get_total(),
            'per_page' => $per_page,
        ) );
    }


    
}
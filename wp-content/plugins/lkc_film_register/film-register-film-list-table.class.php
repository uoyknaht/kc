<?php

// http://wordpress.org/extend/plugins/custom-list-table-example/
// http://mac-blog.org.ua/wordpress-custom-database-table-example-full/

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class FilmRegisterFilmListTable extends WP_List_Table {
    
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
        // print_r($item);
        switch($column_name){
            case 'title':
            case 'date':                      
            default:
                return $item->$column_name;
        }
    }
    
    function column_cb($item){
        return '<input type="checkbox" name="ID[]" value="'.$item->ID.'" />';
    }    
    function column_title($item){

        $editHref = home_url().'/wp-admin/post.php?post='.$item->ID.'&action=edit';
       
        //Build row actions
        $actions = array(
            'edit'      => '<a href="'.$editHref.'">'.__('Edit').'</a>',
            'delete'    => "<a class='submitdelete' title='" . esc_attr( __( 'Move this item to the Trash' ) ) . "' href='" . get_delete_post_link( $item->ID ) . "'>" . __( 'Trash' ) . "</a>",                        
        );
        return '<a class="row-title" href="'.home_url().'/wp-admin/post.php?post='.$item->ID.'&action=edit">'.$item->post_title.'</a>' . $this->row_actions($actions);
    }   

    /*
     * from wp core
     */ 
    function column_date($post){
        if ( '0000-00-00 00:00:00' == $post->post_date ) {
            $t_time = $h_time = __( 'Unpublished' );
            $time_diff = 0;
        } else {
            $t_time = get_the_time( __( 'Y/m/d g:i:s A' ) );
            $m_time = $post->post_date;
            $time = get_post_time( 'G', true, $post );

            $time_diff = time() - $time;

            if ( $time_diff > 0 && $time_diff < DAY_IN_SECONDS )
                $h_time = sprintf( __( '%s ago' ), human_time_diff( $time ) );
            else
                $h_time = mysql2date( __( 'Y/m/d' ), $m_time );
        }


        if ( 'excerpt' == $mode )
            echo apply_filters( 'post_date_column_time', $t_time, $post, $column_name, $mode );
        else
            echo '<abbr title="' . $t_time . '">' . apply_filters( 'post_date_column_time', $h_time, $post, $column_name, $mode ) . '</abbr>';
        echo '<br />';
        if ( 'publish' == $post->post_status ) {
            _e( 'Published' );
        } elseif ( 'future' == $post->post_status ) {
            if ( $time_diff > 0 )
                echo '<strong class="attention">' . __( 'Missed schedule' ) . '</strong>';
            else
                _e( 'Scheduled' );
        } else {
            _e( 'Last Modified' );
        }

    }     





    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'title'     => __('Title'),
            'date'     => __('Date'),
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('title',false),     //true means it's already sorted
            'date'     => array('date',false),     //true means it's already sorted
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
            require_once('education.main.class.php');
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
            'post_type' => 'film',
            'number' => $per_page,
            'offset' => $offset, 
            'orderby' => 'date', 
            'order' => 'desc', 
            // 'search' => $_REQUEST["s"],
            // 'search' => preg_replace( "^$\**(.*)\**$?#", "*$1*", $_REQUEST["s"]), // http://wordpress.stackexchange.com/questions/60347/searching-users-with-wp-list-table-plug-in
            'search' => '*'.$_REQUEST["s"].'*',
            'search_columns' => array(
                'ID',
                'title',
            )
        );

        if ($args['search'] !== '')
            $args['search'] = '*' . $args['search'] . '*';

        if (isset($_REQUEST['orderby']))
            $args['orderby'] = $_REQUEST['orderby'];

        if (isset( $_REQUEST['order']))
            $args['order'] = $_REQUEST['order'];

        // if(isset($_GET['orderby']) && $_GET['orderby'] != 'title'){
        //     $args['orderby'] = 'meta_value';
        //     $args['meta_key'] = '_lkc_film_' . $_GET['orderby'];
        // }
        // if(isset($_GET['order']) && $_GET['order'] == 'DESC'){
        //     $args['order'] = 'DESC';
        // }




        $the_query = new WP_Query($args);
        $data = $the_query->posts;

        $this->items = $data;

        $this->set_pagination_args( array(
            'total_items' => $the_query->post_count,
            'per_page' => $per_page,
        ) );
    }


    
}
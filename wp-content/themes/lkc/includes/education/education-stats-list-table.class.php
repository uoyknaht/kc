<?php

// http://wordpress.org/extend/plugins/custom-list-table-example/
// http://mac-blog.org.ua/wordpress-custom-database-table-example-full/

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class EducationStatsListTable extends WP_List_Table {
    
    function __construct(){
        global $status, $page;

        require_once('education.class.php');
        $this->education = new Education(); 	
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'stat',     //singular name of the listed records
            'plural'    => 'stats',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
    
    function column_default($item, $column_name){
        switch($column_name){
            case 'abra kadabra':                                 
                return print_r($item,true); //Show the whole array for troubleshooting purposes
            default:
                return $item->$column_name;
        }
    }
    
    function column_cb($item){
        return '<input type="checkbox" name="ID[]" value="'.$item->ID.'" />';
    }  

    function column_activity($item){
        $activity = $item->type == 'file' ? 'Atsisiuntė failą' : 'Peržiūrėjo filmą';
        return $activity;
    } 

    function column_resource_parent_title($item){
        $content = '<a href="post.php?post='.$item->resource_parent_id.'&action=edit">'.$item->resource_parent_title.'</a>';
        return $content;
    }     
    
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'created'        => 'Data ir laikas',
            'user_email'     => __('Email'),
            'first_name'     => __('Vardas'),
            'last_name'     => __('Pavardė'),
            'activity'     => __('Veiksmas', 'lkc-newsletter'),
            'resource_parent_title'     => __('Resurso įrašas', 'lkc-newsletter'),
            'resource_title'     => __('Resursas', 'lkc-newsletter'),
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'created'     => array('created',false), 
            'user_email'     => array('user_email',false), 
            'first_name'     => array('first_name',false),
            'last_name'     => array('last_name',false), 
            // 'activity'     => array('activity',false), 
            // 'resource_title'     => array('resource_title',false), 
            // 'resource_parent_title'     => array('resource_parent_title',false), 
        );
        return $sortable_columns;
    }
    
    function get_bulk_actions() {
        $actions = array(
            // 'delete'    => 'Delete'
        );
        return $actions;
    }
    
    function process_bulk_action() {
        if ('delete' === $this->current_action()) {
            // require_once('education.main.class.php');
            // $education = new Education();
            // $ids = isset($_REQUEST['ID']) ? $_REQUEST['ID'] : array();
            // $education->deleteUsers($ids);
        }
    }
    



    function prepare_items() {

    	// ----------------- function addStatsQuery for template redirect hook goes here ---------------------

/*    	// handle filtering. Semi hack - using redirect to add query paramaters in POST FORM
    	$filters = array('_UserEmail', 'm', '_resourceParentID', '_resourceID');
    	$redirect = false;
    	$queryString = $_SERVER['QUERY_STRING'];
    	foreach ($filters as $key => $val) {
	    	if(isset($_POST[$val])){
    			$redirect = true;
		    	if(!empty($_POST[$val])){
		    		$queryString = add_query_arg(array($val => $_REQUEST[$val]), $queryString);
		    	} else {
		    		$queryString = remove_query_arg($val, $queryString);
		    	}
    		}
    	}
    	if($redirect){
    		// print_r($_POST);exit;
    		// print_r($queryString);exit;
	    	wp_redirect('edit.php?' . $queryString);
	    	exit;
    	}*/

        global $wpdb; 

        $per_page = 20;
        $current_page = $this->get_pagenum();
        $offset = ($current_page-1) * $per_page;
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
             
        $this->_column_headers = array($columns, $hidden, $sortable);    
        
        $this->process_bulk_action();    

        // $total_items = $wpdb->get_var("SELECT COUNT(ID) FROM {$this->education->table}");
        // $query = "SELECT * FROM {$this->education->table} s inner JOIN wp_users u ON s.user_id = u.ID";
       
        // SELECT statement is skipped, is added later
        $query = "FROM {$this->education->table} s inner JOIN wp_users u ON s.user_id = u.ID";
        
        // start setting query params
        $queryParams = array();

        if(isset($_REQUEST["_UserEmail"]) && !empty($_REQUEST["_UserEmail"])){
           	$query .= ' AND u.user_email = %s';
           	$queryParams[] = $_REQUEST['_UserEmail'];
        }
        if(isset($_REQUEST["m"]) && !empty($_REQUEST["m"])){
           	$query .= ' AND s.created LIKE %s';
           	$queryParams[] = $_REQUEST['m'].'%';
        }

        if(isset($_REQUEST["_resourceParentID"]) && !empty($_REQUEST["_resourceParentID"])){
        	$childrenAttachments = $this->getPostResources($_REQUEST["_resourceParentID"]);

           	$in = mysql_real_escape_string(implode(',', $childrenAttachments));
           	$query .= " AND s.resource_id IN ({$in})";
           	// cannot get to work IN(%s) as quotes appear and sql statement not works
           	// $query .= " AND s.resource_id IN (%s)";
           	// $queryParams[] = $in;
        }

        if(isset($_REQUEST["_resourceID"]) && !empty($_REQUEST["_resourceID"]) && isset($childrenAttachments) && in_array($_REQUEST["_resourceID"], $childrenAttachments)){
           	$query .= " AND s.resource_id = %s";
           	$queryParams[] = $_REQUEST["_resourceID"];
        } 

        // set ordering
        $orderby = 's.created';
        if (isset($_REQUEST['orderby'])){

        	$statsFields = array('created');
        	$userFields = array('user_email');

        	if(in_array($_REQUEST['orderby'], $statsFields)){
           		$orderby = 's.' . $_REQUEST['orderby'];
        	} elseif (in_array($_REQUEST['orderby'], $userFields)) {
        		$orderby = 'u.' . $_REQUEST['orderby'];
        	} 
        }
        // $queryParams[] =  $orderby;
        
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'desc';
        // $queryParams[] =  $order;


        // for total items we need number of records without limit or offset, only filtered
        // for normal query, we add limit and offset params
        $queryForTotalItems = "SELECT COUNT(*) " . $query;
        $queryForTotalItemsParams = $queryParams;
        $query = "SELECT * " . $query;

        $query .= " ORDER BY $orderby $order LIMIT %d OFFSET %d";
        $queryParams[] =  $per_page;
        $queryParams[] =  $offset;

        $data = $wpdb->get_results($wpdb->prepare($query, $queryParams));
       
        $total_items = $wpdb->get_results($wpdb->prepare($queryForTotalItems, $queryForTotalItemsParams), ARRAY_N );
        $total_items = $total_items[0][0];


        // print_r($wpdb->prepare($query, $queryParams)); exit;



        foreach ($data as $key => $val) {
           $user = get_userdata($val->user_id);
           $data[$key]->user_email = $user->user_email;
           $data[$key]->first_name = $user->user_firstname;
           $data[$key]->last_name = $user->user_lastname ;
           $post = get_post($val->resource_id);
           $data[$key]->resource_title = $post->post_title;

           $parent = get_post($post->post_parent);
           $data[$key]->resource_parent_title = $parent->post_title;
           $data[$key]->resource_parent_id = $parent->ID;
        }

        $this->items = $data;

        $this->set_pagination_args( array(
            'total_items' => $total_items,
            'per_page' => $per_page,
        ) );
    }

	/**
	 * Generate the table navigation above or below the table
	 *
	 * @since 3.1.0
	 * @access protected
	 *
	 * COPIED FROM CORE TO ADD CUSTOM FILTERING!
	 */
	/*function display_tablenav( $which ) {
		if ( 'top' == $which )
			wp_nonce_field( 'bulk-' . $this->_args['plural'] );
		?>
		<div class="tablenav <?php echo esc_attr( $which ); ?>">

			<div class="alignleft actions">
				<?php $this->bulk_actions(); ?>
				<?php $this->months_dropdown(); ?>
				<?php $this->userEmailDropdownFilter(); ?>
				<?php submit_button( __( 'Filter' ), 'button', false, false, array( 'id' => 'post-query-submit' ) ); ?>

				<?php //$this->search_box( 'search', 'search_id' ); ?>
			</div>
			<?php
			$this->extra_tablenav( $which );
			$this->pagination( $which );
			?>
			<br class="clear" />
		</div>
	<?php
	} */ 


	/**
	 * Retunrs IDS of resource files, that belong to post, which id is passed as $post_id
	 */	
	function getPostResources($post_id){

		$the_query = new WP_Query(array(
			'post_type' => 'attachment', 
			'post_status' => 'inherit', 
			'numberposts' => -1,
			// 'meta_query' => array(
			// 	'key' => '_education_file',
			// 	'value' => 1
			// ),
			'post_parent' => $post_id
		));
		$data = $the_query->posts;
		$ids = array();
		foreach ($data as $key => $val) {
			$ids[] = $val->ID;
		}
		// print_r($ids);exit;
		return $ids;
	}


}
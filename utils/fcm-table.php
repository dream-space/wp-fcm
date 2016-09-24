<?php 

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Fcm_Table_Users_List extends WP_List_Table {

	function __construct(){
		global $status, $page;				
		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'delete_id',     //singular name of the listed records
			'plural'    => 'delete_ids',    //plural name of the listed records
			'ajax'      => false            //does this table support ajax?
		) );
		$this->admin_header();
		$this->define_script();
	}
	
	// define javaScript
	function define_script(){
		echo "<script> 
				var oArg = new Object();
				function dialogRegId(msg) {
					prompt('Copy to clipboard: Ctrl+C, Enter', msg);
				}
			</script>";
	}
	
		
	function admin_header() {
		$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
		if( 'wp-fcm-users' != $page ) return; 
		
		echo '<style type="text/css">';
		echo '.wp-list-table .column-regid { width: 10%; }';
		echo '.wp-list-table .column-serial { width: 30%; }';
		echo '.wp-list-table .column-device_name { width: 20%; }';
		echo '.wp-list-table .column-os_version { width: 10%; }';
		echo '.wp-list-table .column-created_at { width: 15%; }';
		echo '</style>';
	}

	// return row view
	function column_default($item, $column_name){
		switch($column_name){
		case 'id':
		case 'regid':
			$link_details = "<a href=\"javascript:dialogRegId('$item[$column_name]');\">Show Reg Id</a>";
			return $link_details;
		case 'serial':
			return $item[$column_name];
		case 'device_name':
			return $item[$column_name];
		case 'os_version':
			return $item[$column_name];
		case 'created_at':
			return date("Y-m-d H:m:s", $item[$column_name]);		
		default:
			return print_r($item,true); //Show the whole array for troubleshooting purposes
		}
	}

	function column_title($item){		
		//Return the title contents
		return sprintf('%2$s',
			$item['id'],
			$item['regid'],
			$item['serial'],
			$item['device_name'],
			$item['os_version'],
			$item['created_at']
		);
	}

	function get_columns(){
		$columns = array(
			'regid'  		=> 'Device Reg Id',
			'serial'  		=> 'Serial Number',
			'device_name'  	=> 'Device Name',
			'os_version' 	=> 'Version',
			'created_at' 	=> 'Created at'
		);
		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'push_send_date' => array('push_send_date',false)
		);
		return $sortable_columns;
	}

	function prepare_items($search) {
		$item_per_page 	= 20;
		$paged 			= 0;
		$orderby 		= 'created_at';
		$order 			= 'desc';
		
		if(isset($_REQUEST['paged'])){
			$paged = max(0, intval($_REQUEST['paged']) - 1) * $item_per_page;
		}
		if((isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns())))){
			$orderby = $_REQUEST['orderby'];
		}
		if((isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc')))){
			$order = $_REQUEST['order'];
		}
				
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$current_page = $this->get_pagenum();		
		
		$total_items = fcm_data_count_users($search);
		$this->items = fcm_data_get_users($orderby, $order, $item_per_page, $paged, $search);
				
		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $item_per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items/$item_per_page)   //WE have to calculate the total number of pages
		) );
	}
}

class Fcm_Table_Logs_List extends WP_List_Table {

	function __construct(){
		global $status, $page;				
		
		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'delete_id',     //singular name of the listed records
			'plural'    => 'delete_ids',    //plural name of the listed records
			'ajax'      => false            //does this table support ajax?
		) );
		$this->admin_header() ;
	}
	
	function admin_header() {
		$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
		if( 'wp-fcm-history' != $page ) return; 
		
		echo '<style type="text/css">';
		echo '.wp-list-table .column-title { width: 15%; }';
		echo '.wp-list-table .column-content { width: 40%; }';
		echo '.wp-list-table .column-target { width: 10%; }';
		echo '.wp-list-table .column-event { width: 15%; }';
		echo '.wp-list-table .column-success { width: 10%; }';
		echo '.wp-list-table .column-failure { width: 10%; }';
		echo '.wp-list-table .column-created_at { width: 20%; }';
		echo '</style>';
	}

	function column_default($item, $column_name){
		switch($column_name){
		case 'id':
		case 'title':
			return $item[$column_name];
		case 'content':
			return $item[$column_name];
		case 'target':
			return $item[$column_name];
		case 'event':
			return $item[$column_name];		
		case 'success':
			return $item[$column_name];						
		case 'failure':
			return $item[$column_name];
		case 'created_at':
			return date("Y-m-d H:m:s", $item[$column_name]);	
		default:
			return print_r($item,true); //Show the whole array for troubleshooting purposes
		}
	}

	function column_title($item){		
		//Build row actions
		$actions = array(
			'edit'      => sprintf('<a href="?page=%s&action=%s&delete_id=%s">Edit</a>',$_REQUEST['page'],'edit',$item['id']),
			'delete'    => sprintf('<a href="?page=%s&action=%s&delete_id=%s">Delete</a>',$_REQUEST['page'],'delete',$item['id']),
		);
		
		//Return the title contents
		return sprintf('%2$s',
			$item['id'],
			$item['title'],
			$item['content'],
			$item['target'],
			$item['event'],
			$item['success'],
			$item['failure'],
			$item['created_at'],
		$this->row_actions($actions)
		);
	}

	function column_cb($item){
		return sprintf(
		'<input type="checkbox" name="%1$s[]" value="%2$s" />',
		$this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
		$item['id']               //The value of the checkbox should be the record's id
		);
	}

	function get_columns(){
		$columns = array(
			'cb'    		=> '<input type="checkbox" />', //Render a checkbox instead of text
			'title'  		=> 'Title',
			'content'  		=> 'Content',
			'target' 		=> 'Target',
			'event' 		=> 'Event',
			'success' 		=> 'Success',
			'failure' 		=> 'Failure',
			'created_at' 	=> 'Created at'
		);
		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'push_send_date' => array('push_send_date',false)
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
		//Detect when a bulk action is being triggered...
		
		if( 'delete'===$this->current_action() ) {			
			global $wpdb;								
			$array_data=$_REQUEST['delete_id'];
			$fcm_logs = $wpdb->prefix . 'fcm_logs';	
			foreach($array_data as $key => $id_value){ 				
                $wpdb->query( "DELETE FROM $fcm_logs WHERE id = ".$id_value);				
			}			
			$current_page_url=$_REQUEST['_wp_http_referer'];
            echo "<script>window.location.href='" . $current_page_url . "';</script>";
			//header('Location: '.$current_page_url);
		}
	}

	function prepare_items($search) {
		$item_per_page 	= 20;
		$paged 			= 0;
		$orderby 		= 'created_at';
		$order 			= 'desc';
		
		if(isset($_REQUEST['paged'])){
			$paged = max(0, intval($_REQUEST['paged']) - 1) * $item_per_page;
		}
		if((isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns())))){
			$orderby = $_REQUEST['orderby'];
		}
		if((isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc')))){
			$order = $_REQUEST['order'];
		}
				
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$current_page = $this->get_pagenum();		
		
		$total_items = fcm_data_count_logs($search);
		$this->items = fcm_data_get_logs($orderby, $order, $item_per_page, $paged, $search);
		
		$this->process_bulk_action();		
		$this->set_pagination_args( array(
			'total_items' => $total_items,                  //WE have to calculate the total number of items
			'per_page'    => $item_per_page,                     //WE have to determine how many items to show on a page
			'total_pages' => ceil($total_items/$item_per_page)   //WE have to calculate the total number of pages
		) );
	}
}
?>
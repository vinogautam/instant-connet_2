<?php
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
 
class EndoserTable extends WP_List_Table {
    
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'movie',     //singular name of the listed records
            'plural'    => 'movies',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
	
	function column_default($item, $column_name){
		global $wpdb;
		
		switch($column_name){
            case 'user_email':
                return $item[$column_name];
			case 'user_login':
                return $item[$column_name];
			case 'user_registered':
                return date('Y/m/d', strtotime($item[$column_name]));
			case 'invitation':
				return get_user_meta($item['ID'], 'invitation_sent', true) 
				? get_user_meta($item['ID'], 'invitation_sent', true) 
				: "-";
			case 'converted':
				$res = 'Endorsement - '.(get_user_meta($item['ID'], "tracked_invitation", true));
				$res .= '<br>Facebook - '.(get_user_meta($item['ID'], "tracked_fb_invitation", true));
				$res .= '<br>Twitter - '.(get_user_meta($item['ID'], "tracked_tw_invitation", true));
				return $res;
			case 'converted_new':
				$res = 'Endorsement - '.(get_user_meta($item['ID'], "tracked_counter", true));
				$res .= '<br>Facebook - '.(get_user_meta($item['ID'], "tracked_fb_counter", true));
				$res .= '<br>Twitter - '.(get_user_meta($item['ID'], "tracked_tw_counter", true));
				return $res;
			case 'endorser_letter':
                $re = get_user_meta($item['ID'], 'endorser_letter', true);
				$result = $wpdb->get_row("select name from ". $wpdb->prefix . "mailtemplates where id=".$re);
				$re = get_user_meta($item['ID'], 'endorsement_letter', true);
				$result2 = $wpdb->get_row("select name from ". $wpdb->prefix . "mailtemplates where id=".$re);
				$ret = "Endorser : ". ($result->name ? $result->name : 'Default') ;
				$ret .= "<br>Endorsement : ". ($result2->name ? $result2->name : 'Default') ;
				return $ret;
			case 'resend_welcome_email':
                return '<a href="admin.php?page=ntmEndorsements&tab=endorsers&resend_welcome_email='.$item['ID'].'">Resend Link</a>';
			case 'resend_gift':
                $return = '';
				$get_results = $wpdb->get_results("select * from ".$wpdb->prefix . "endorsements where endorser_id=".$item['ID']." and track_status is not null and gift_status is null");
				if(count($get_results))
					$return = '<a data-name="'.get_user_meta($item['ID'], 'first_name', true).' '.get_user_meta($item['ID'], 'last_name', true).'" data-type="new" data-id="'.$item['ID'].'" class="inline" href="#modalpopupnew">Send Gift</a>';
				
				$get_results = $wpdb->get_results("select * from ".$wpdb->prefix . "endorsements where endorser_id=".$item['ID']." and track_status is not null and gift_status = 1");
				if(count($get_results))
				{
					$return = $return ? $return.'<br>' : '';
					$return .= '<br><a data-name="'.get_user_meta($item['ID'], 'first_name', true).' '.get_user_meta($item['ID'], 'last_name', true).'" data-type="old" data-id="'.$item['ID'].'" class="inline" href="#modalpopupold">Resend Gift</a>';
				}
				return $return;
            default:
                return 0;//print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    
    function column_title($item){
        
        	
		//Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&tab=%s&edit=%s">Edit</a>',$_REQUEST['page'],'add_endorsers',$item['ID']),
            'delete'    => sprintf('<a href="?page=%s&tab=endorsers&delete=%s">Delete</a>',$_REQUEST['page'],$item['ID']),
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ get_user_meta($item['ID'], 'first_name', true).' '.get_user_meta($item['ID'], 'last_name', true),
            /*$2%s*/ $item['ID'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }
    
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['ID']                //The value of the checkbox should be the record's id
        );
    }
    
    
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'     => 'Username',
            'user_email'    => 'Email',
			'endorser_letter'	=> 'Letter Template',
			//'endorsement_letter' => 'Endorsement Letter',
			'invitation'    => 'No of invitation sent',
			'converted' => 'Total Coversion',
			'converted_new' => 'New Coversion',
			'user_registered' => 'Registered Date',
			'resend_welcome_email' => 'Resend Auto Link',
			'resend_gift' => 'Send/Resend Gift'
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'title'     => array('user_login',false),    //true means it's already sorted
            'user_email'    => array('user_email',false),
            'user_registered'  => array('user_registered',false)
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
        
        global $wpdb;
		
		if( 'delete'===$this->current_action()) {
		$del_val = $_REQUEST['movie'];
		print_r($del_val);
		foreach($del_val as $val) {
			wp_delete_user($val);
		}}
       
    }
    
    function prepare_items() {
        global $wpdb; 
        $per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();
        
		function objectToArray($d) 
		{
		if (is_object($d)) {
			$d = get_object_vars($d);
		}
 
		if (is_array($d)) {
			return array_map(__FUNCTION__, $d);
		}
		else {
			return $d;
		}
		}
		$data = objectToArray(get_users(array('role'=>'endorser')));
        $newdat = array();
		foreach($data as $v){
			if(!get_user_meta($v['data']['ID'], 'imcomplete_profile', true))
			$newdat[] = $v['data'];
		}
		$data = $newdat;
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'user_login'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
        
        
        $current_page = $this->get_pagenum();
        
        
        $total_items = count($data);
        
        
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        $this->items = $data;
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
    
}


class LetterTable extends WP_List_Table {
    
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'movie',     //singular name of the listed records
            'plural'    => 'movies',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
	
	function column_default($item, $column_name){
		switch($column_name){
            case 'subject':
                return $item[$column_name];
			case 'type':
                return $item[$column_name] == 'Endorsement' ? 'Endorsement - ' . get_the_title($item['page']) : 'Endorser';
			case 'created':
                return date('Y/m/d', strtotime($item[$column_name]));
			case 'resend_welcome_email':
                return '<a href="admin.php?page=ntmEndorsements&tab=endorsers&resend_welcome_email='.$item['ID'].'">Resend Link</a>';
            default:
                return 0;//print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    
    function column_title($item){
        
        	
		//Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&tab=%s&edit=%s">Edit</a>',$_REQUEST['page'],'add_template',$item['id']),
            'delete'    => sprintf('<a href="?page=%s&tab=%s&delete=%s">Delete</a>',$_REQUEST['page'],$_REQUEST['tab'],$item['id']),
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['name'],
            /*$2%s*/ $item['id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }
    
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }
    
    
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'     => 'Name',
            'subject'    => 'Subject',
			'type'    => 'Letter Type',
			'created' => 'Registered Date'
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'name'     => array('name',false)
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
        
        global $wpdb;
		
		if( 'delete'===$this->current_action()) {
		$del_val = $_REQUEST['movie'];
		foreach($del_val as $val) {
			$wpdb->delete($wpdb->prefix . "mailtemplates", array( 'id' => $val ) );
		}}
       
    }
    
    function prepare_items() {
        global $wpdb; 
        $per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();
        
		function objectToArray($d) 
		{
		if (is_object($d)) {
			$d = get_object_vars($d);
		}
 
		if (is_array($d)) {
			return array_map(__FUNCTION__, $d);
		}
		else {
			return $d;
		}
		}
		$data = objectToArray($wpdb->get_results("select * from ".$wpdb->prefix . "mailtemplates"));
        $newdat = array();
		foreach($data as $v){
			$newdat[] = $v;
		}
		$data = $newdat;
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'user_login'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
        
        
        $current_page = $this->get_pagenum();
        
        
        $total_items = count($data);
        
        
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        $this->items = $data;
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
    
}

class EndorsementTable extends WP_List_Table {
    
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'movie',     //singular name of the listed records
            'plural'    => 'movies',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
	
	function column_default($item, $column_name){
		switch($column_name){
            case 'email':
                return $item[$column_name];
			case 'track_link':
                return base64_encode(base64_encode($item['id'].'#&$#'.$item['endorser_id'].'#&$#'.$item['tracker_id']));
			case 'track_status':
                return $item[$column_name] ? "Yes" : "No";
			case 'gift_status':
                return $item[$column_name] ? "Yes" : "No";
			case 'endorser_id':
                return get_user_meta($item[$column_name], 'first_name', true).' '.get_user_meta($item[$column_name], 'last_name', true);
			case 'created':
                return date('Y/m/d', strtotime($item[$column_name]));
			case 'post_data':
                echo '<pre>';
				print_r(unserialize($item[$column_name]));
				echo '</pre>';
				return;
            default:
                return 0;//print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    
    function column_title($item){
        
        	
		//Build row actions
        $actions = array(
            //'edit'      => sprintf('<a href="?page=%s&tab=%s&edit=%s">Edit</a>',$_REQUEST['page'],'add_template',$item['id']),
            'delete'    => sprintf('<a href="?page=%s&tab=%s&delete=%s">Delete</a>',$_REQUEST['page'],$_REQUEST['tab'],$item['id']),
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item['name'],
            /*$2%s*/ $item['id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }
    
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }
    
    
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'     => 'Endorsement Name',
            'email'    => 'Endorsement Email',
			'endorser_id'    => 'Endorser',
			'track_link'    => 'Track link',
			'track_status'    => 'Track status',
			'gift_status'    => 'Gift status',
			'post_data'    => 'Post data',
			'created' => 'Registered Date'
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'name'     => array('name',false)
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
        
        global $wpdb;
		
		if( 'delete'===$this->current_action()) {
		$del_val = $_REQUEST['movie'];
		foreach($del_val as $val) {
			$wpdb->delete($wpdb->prefix . "endorsements", array( 'id' => $val ) );
		}}
       
    }
    
    function prepare_items() {
        global $wpdb; 
        $per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();
        
		function objectToArray($d) 
		{
		if (is_object($d)) {
			$d = get_object_vars($d);
		}
 
		if (is_array($d)) {
			return array_map(__FUNCTION__, $d);
		}
		else {
			return $d;
		}
		}
		$data = objectToArray($wpdb->get_results("select * from ".$wpdb->prefix . "endorsements"));
        $newdat = array();
		foreach($data as $v){
			$newdat[] = $v;
		}
		$data = $newdat;
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'user_login'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
        
        
        $current_page = $this->get_pagenum();
        
        
        $total_items = count($data);
        
        
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        $this->items = $data;
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
    
}

class GiftTable extends WP_List_Table {
    
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'movie',     //singular name of the listed records
            'plural'    => 'movies',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
        
    }
	
	function column_default($item, $column_name){
		switch($column_name){
            case 'email':
                $user_info = get_userdata($item['endorser_id']);
				return $user_info->user_email;
			case 'gift_sent':
                return $item[$column_name] ? "Yes" : "No";
			case 'endorser_id':
                return get_user_meta($item[$column_name], 'first_name', true).' '.get_user_meta($item[$column_name], 'last_name', true);
			case 'created':
                return date('Y/m/d H:i', strtotime($item[$column_name]));
			case 'post_data':
                echo '<pre>';
				print_r(unserialize($item[$column_name]));
				echo '</pre>';
				return;
            case 'amout':
                return '$ '.$item[$column_name];
			default:
                return $item[$column_name];//print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    
    function column_title($item){
        
        	
		//Build row actions
        $actions = array(
            //'edit'      => sprintf('<a href="?page=%s&tab=%s&edit=%s">Edit</a>',$_REQUEST['page'],'add_template',$item['id']),
            //'delete'    => sprintf('<a href="?page=%s&tab=%s&delete=%s">Delete</a>',$_REQUEST['page'],$_REQUEST['tab'],$item['id']),
        );
        
        //Return the title contents
        return sprintf('%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ get_user_meta($item['endorser_id'], 'first_name', true).' '.get_user_meta($item['endorser_id'], 'last_name', true),
            /*$2%s*/ $item['endorser_id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }
    
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
        );
    }
    
    
    function get_columns(){
        $columns = array(
            'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'     => 'Endorser Name',
            'email'    => 'Endorser Email',
			'amout'    => 'Gift Amount',
			'giftbitref_id'    => 'Gift Ref Id',
			'gift_sent'    => 'Gift status',
			'created' => 'Gift Date'
        );
        return $columns;
    }
    
    function get_sortable_columns() {
        $sortable_columns = array(
            'name'     => array('name',false)
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
        
        global $wpdb;
		
		if( 'delete'===$this->current_action()) {
		$del_val = $_REQUEST['movie'];
		foreach($del_val as $val) {
			$wpdb->delete($wpdb->prefix . "gift_transaction", array( 'id' => $val ) );
		}}
       
    }
    
    function prepare_items() {
        global $wpdb; 
        $per_page = 5;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();
        
		function objectToArray($d) 
		{
		if (is_object($d)) {
			$d = get_object_vars($d);
		}
 
		if (is_array($d)) {
			return array_map(__FUNCTION__, $d);
		}
		else {
			return $d;
		}
		}
		$data = objectToArray($wpdb->get_results("select * from ".$wpdb->prefix . "gift_transaction"));
        $newdat = array();
		foreach($data as $v){
			$newdat[] = $v;
		}
		$data = $newdat;
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'created'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
        
        
        $current_page = $this->get_pagenum();
        
        
        $total_items = count($data);
        
        
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        
        $this->items = $data;
        
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }
    
}
<?php
class IC_agent_api{

	function __construct() {


	    header('Access-Control-Allow-Origin: *');
	    header('Access-Control-Allow-Methods: GET, POST');
	    header("Access-Control-Allow-Headers: X-Requested-With");

		add_action( 'wp_ajax_ic_agent_login', array( &$this, 'ic_agent_login') );
		add_action( 'wp_ajax_nopriv_ic_agent_login', array( &$this, 'ic_agent_login') );

		add_action( 'wp_ajax_ic_add_endorser', array( &$this, 'ic_add_endorser') );
		add_action( 'wp_ajax_nopriv_ic_add_endorser', array( &$this, 'ic_add_endorser') );

		add_action( 'wp_ajax_ic_update_endorser', array( &$this, 'ic_update_endorser') );
		add_action( 'wp_ajax_nopriv_ic_update_endorser', array( &$this, 'ic_update_endorser') );

		add_action( 'wp_ajax_ic_endorser_list', array( &$this, 'ic_endorser_list') );
		add_action( 'wp_ajax_nopriv_ic_endorser_list', array( &$this, 'ic_endorser_list') );

		add_action( 'wp_ajax_ic_add_create_email_template', array( &$this, 'ic_add_create_email_template') );
		add_action( 'wp_ajax_nopriv_ic_add_create_email_template', array( &$this, 'ic_add_create_email_template') );

		add_action( 'wp_ajax_ic_update_email_template', array( &$this, 'ic_update_email_template') );
		add_action( 'wp_ajax_nopriv_ic_update_email_template', array( &$this, 'ic_update_email_template') );

		add_action( 'wp_ajax_ic_endorser_letter_list', array( &$this, 'ic_endorser_letter_list') );
		add_action( 'wp_ajax_nopriv_ic_endorser_letter_list', array( &$this, 'ic_endorser_letter_list') );

		add_action( 'wp_ajax_ic_endorsement_letter_list', array( &$this, 'ic_endorsement_letter_list') );
		add_action( 'wp_ajax_nopriv_ic_endorsement_letter_list', array( &$this, 'ic_endorsement_letter_list') );

		add_action( 'wp_ajax_ic_delete_endorser', array( &$this, 'ic_delete_endorser') );
		add_action( 'wp_ajax_nopriv_ic_delete_endorser', array( &$this, 'ic_delete_endorser') );

		add_action( 'wp_ajax_ic_delete_letter', array( &$this, 'ic_delete_letter') );
		add_action( 'wp_ajax_nopriv_ic_delete_letter', array( &$this, 'ic_delete_letter') );
	}


	function ic_agent_login(){
		$_POST = (array) json_decode(file_get_contents('php://input'));
		$user = wp_signon( $creds, false );

		if ( is_wp_error($user) ) {
			$response = array('status' => 'Error', 'msg' => 'Invalid Credentials');
		}
		else{
			$response = array('status' => 'Success', 'data' => $user->data, 'msg' => 'Logged in successfully');
		}
		echo json_encode($response);
		die(0);
	}

	function ic_add_endorser(){
		//global $ntm_mail;

		$user = (array) json_decode(file_get_contents('php://input'));
		$user['role'] = 'endorser';
		$user['user_login'] = strtolower($user['first_name'].'_'.$user['last_name']);
		
		$user_id = username_exists( $user['user_login'] );
		if ( !$user_id and email_exists($user['user_email']) == false ) {
			$user['user_pass'] = wp_generate_password( $length=12, $include_standard_special_chars=false );
			$user_id = wp_insert_user( $user ) ;
			if (  is_wp_error( $user_id ) ) {
				$response = array('status' => 'Error', 'msg' => 'Something went wrong. Try Again!!!.');
			}
			else
			{
				update_user_meta($user_id, 'endorser_letter', $user['endorser_letter']);
				update_user_meta($user_id, 'endorsement_letter', $user['endorsement_letter']);
				//$ntm_mail->send_welcome_mail($user['user_email'], $user_id, $user['user_login'].'#'.$user['user_pass']);
				//$ntm_mail->send_notification_mail($user_id);

				$response = array('status' => 'Success', 'data' => $user_id, 'msg' => 'Endorser created successfully');
			}
		} else {
			$response = array('status' => 'Error', 'msg' => 'User already exists.  Password inherited.');
		}

		echo json_encode($response);
		die(0);
	}

	function ic_update_endorser() {
		$user = (array) json_decode(file_get_contents('php://input'));

		update_user_meta($user['id'], 'endorser_letter', $user['endorser_letter']);
		update_user_meta($user['id'], 'endorsement_letter', $user['endorsement_letter']);
		update_user_meta($user['id'], 'first_name', $user['first_name']);
		update_user_meta($user['id'], 'last_name', $user['last_name']);

		$response = array('status' => 'Success', 'msg' => 'Endorser updated successfully');
		echo json_encode($response);
		die(0);
	}

	function ic_endorser_list(){
		global $wpdb;

		$data = objectToArray(get_users(array('role'=>'endorser')));
        $newdat = array();
		foreach($data as $v){
			$item = $v['data'];
			if(!get_user_meta($item['ID'], 'imcomplete_profile', true)){
				$item['invitation'] = get_user_meta($item['ID'], 'invitation_sent', true) 
				? get_user_meta($item['ID'], 'invitation_sent', true) 
				: "-";
				$item['converted'] = array('email' => get_user_meta($item['ID'], "tracked_invitation", true), 'fb' => get_user_meta($item['ID'], "tracked_fb_invitation", true), 'tw' => get_user_meta($item['ID'], "tracked_tw_invitation", true));

				$item['converted_new'] = array('email' => get_user_meta($item['ID'], "tracked_counter", true), 'fb' => get_user_meta($item['ID'], "tracked_fb_counter", true), 'tw' => get_user_meta($item['ID'], "tracked_tw_counter", true));

				$re = get_user_meta($item['ID'], 'endorser_letter', true);
				$result = $wpdb->get_row("select name from ". $wpdb->prefix . "mailtemplates where id=".$re);
				$re = get_user_meta($item['ID'], 'endorsement_letter', true);
				$result2 = $wpdb->get_row("select name from ". $wpdb->prefix . "mailtemplates where id=".$re);

				$item['endorser_letter'] = ($result->name ? $result->name : 'Default') ;
				$item['endorsement_letter'] = ($result2->name ? $result2->name : 'Default') ;

				$newdat[] = $item;
			}
		}
		$response = array('status' => 'Success', 'data' => $newdat);
		echo json_encode($response);
		die(0);
		
	}

	function ic_add_create_email_template() {
		global $wpdb;


		$letter = (array) json_decode(file_get_contents('php://input'));

		$letter['created'] = date("Y-m-d H:i:s");
		$res = $wpdb->insert($wpdb->prefix . "mailtemplates", $letter);

		if (  is_wp_error( $res ) ) {
			$response = array('status' => 'Error', 'msg' => 'Something went wrong. Try Again!!!.');
		} else {
			$response = array('status' => 'Success', 'data' => $res, 'msg' => 'Letter template created successfully');
		}

		echo json_encode($response);
		die(0);
	}

	function ic_endorser_letter_list() {
		global $wpdb;

		$response = array('status' => 'Success', 'data' => $wpdb->get_results('select * from '.$wpdb->prefix . "mailtemplates where type = 'Endorser")); 

		echo json_encode($response);
		die(0);
	}

	function ic_endorsement_letter_list() {
		global $wpdb;

		$response = array('status' => 'Success', 'data' => $wpdb->get_results('select * from '.$wpdb->prefix . "mailtemplates where type = 'Endorsement")); 

		echo json_encode($response);
		die(0);
	}

	function ic_update_email_template(){
		global $wpdb;


		$letter = (array) json_decode(file_get_contents('php://input'));
		$res = $wpdb->update($wpdb->prefix . "mailtemplates", $letter, array('id' => $letter['id']));

		if (  is_wp_error( $res ) ) {
			$response = array('status' => 'Error', 'msg' => 'Something went wrong. Try Again!!!.');
		} else {
			$response = array('status' => 'Success', 'data' => $res, 'msg' => 'Letter template updated successfully');
		}

		echo json_encode($response);
		die(0);
	}

	function ic_delete_endorser(){
		wpmu_delete_user($_GET['id']);

		$response = array('status' => 'Success', 'msg' => 'Endorser deleted successfully');
		echo json_encode($response);
		die(0);
	}

	function ic_delete_letter(){
		global $wpdb;

		$wpdb->delete($wpdb->prefix . "mailtemplates", array( 'id' => $_GET['id'] ) );

		$response = array('status' => 'Success', 'msg' => 'Mail Letter template deleted successfully');
		echo json_encode($response);
		die(0);
	}
}
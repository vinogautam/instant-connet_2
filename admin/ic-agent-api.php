<?php
class IC_agent_api{

	function __construct() {
		add_action( 'wp_ajax_ic_agent_login', array( &$this, 'ic_agent_login') );
		add_action( 'wp_ajax_nopriv_ic_agent_login', array( &$this, 'ic_agent_login') );

		add_action( 'wp_ajax_ic_add_endorser', array( &$this, 'ic_add_endorser') );
		add_action( 'wp_ajax_nopriv_ic_add_endorser', array( &$this, 'ic_add_endorser') );

		add_action( 'wp_ajax_ic_endorser_list', array( &$this, 'ic_endorser_list') );
		add_action( 'wp_ajax_nopriv_ic_endorser_list', array( &$this, 'ic_endorser_list') );
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
	}

	function ic_endorser_list(){
		global $wpdb;

		$_POST = (array) json_decode(file_get_contents('php://input'));

		
	}
}
<?php
/* Plugin Name: Instant Connect FinancialInsiders
 * Author: Vinodhagan Thangarajan
 * Author URI: https://github.com/financialinsiders
 * Plugin URI: https://github.com/financialinsiders/Instant-Connect
 * Description: Instant Connect plugin for Neil Thomas
 * Version: 2.0
 */
 
 $dir = pathinfo(__FILE__);
 define('IC_PLUGIN_URL', plugin_dir_url( __FILE__ ));
 define('IC_PLUGIN_DIR',$dir['dirname']);

 define("API_KEY", "45701592");
 define("API_SECRET", "487828bda5af8ace8ca1c55ced44411f57750f77");
 
 include 'includes.php';
 include 'pusher/pusher.php';
 
 global $endorsements, $ntmadmin, $ntm_mail;
 $endorsements = new Instant_Connect();
 $ntm_mail = new IC_mail_template();
 
 Class Instant_Connect
 {
	function Instant_Connect()
	{
		register_activation_hook(__FILE__, array( &$this, 'Endorsement_install'));
		//register_uninstall_hook(__FILE__, array( &$this, 'Endorsement_uninstall'));
		
		function codex_custom_init() {
			$args = array(
			  'public' => true,
			  'label'  => 'Meeting'
			);
			register_post_type( 'meeting', $args );
		}
		
		add_action( 'init', 'codex_custom_init' );
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_date_picker' ));
		add_filter( 'page_template', array( &$this, 'wpa3396_page_template' ));

		$ntmadmin = new IC_admin();
		new IC_Metabox();
		new IC_ajax();
		new IC_front();
	}
	
	function wpa3396_page_template( $page_template )
	{
		if ( is_page( 'meeting' ) ) {
			$page_template = dirname( __FILE__ ) . '/meeting_template_new.php';
		}
		return $page_template;
	}
	
	function Endorsement_install()
	{
		global $wpdb;
		
		$mailtemplates = $wpdb->prefix . "meeting";
		
		if($wpdb->get_var('SHOW TABLES LIKE ' . $mailtemplates) != $mailtemplates){
			$sql_one = "CREATE TABLE " . $mailtemplates . "(
			  id int(11) NOT NULL AUTO_INCREMENT,
			   created datetime NOT NULL,
			   title tinytext NOT NULL,
			   session_id text NOT NULL,
			   token text NOT NULL,
			   description text NOT NULL,
			   agent_id text NOT NULL,
			   meeting_date datetime NOT NULL,
			  PRIMARY KEY  (id) ) ENGINE=InnoDB";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql_one);
		}
		
		$mailtemplates = $wpdb->prefix . "meeting_participants";
		
		if($wpdb->get_var('SHOW TABLES LIKE ' . $mailtemplates) != $mailtemplates){
			$sql_one = "CREATE TABLE " . $mailtemplates . "(
			  id int(11) NOT NULL AUTO_INCREMENT,
			   meeting_id int(11),
			   name tinytext NOT NULL,
			   email tinytext NOT NULL,
			   meeting_date datetime NOT NULL,
			   status int(1),
			   video int(1),
			   whiteboard int(1),
			   lead int(11),
			   endorser int(11),
			   gift_status int(1),
			  PRIMARY KEY  (id) ) ENGINE=InnoDB";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql_one);
		}
		
		$mailtemplates = $wpdb->prefix . "meeting_presentations";
		
		if($wpdb->get_var('SHOW TABLES LIKE ' . $mailtemplates) != $mailtemplates){
			$sql_one = "CREATE TABLE " . $mailtemplates . "(
			  id int(11) NOT NULL AUTO_INCREMENT,
			   name text,
			   file text,
			   url text,
			   default_presentation int(1),
			   status int(11),
			  PRIMARY KEY  (id) ) ENGINE=InnoDB";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql_one);
		}
	}
	
	function Endorsement_uninstall()
	{
		
	}
	
	function Endorsement_menu()
	{
		
	}
	
	function Endorsement_frontend()
	{
		$ntm_front_end = new NTM_Frontend();
		
		return $ntm_front_end->frontend();
	}
	
	function Endorsement_load_js_and_css()
	{
		
	}
	
	function enqueue_date_picker(){
                wp_enqueue_script(
				'field-date-js', 
				'Field_Date.js', 
				array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),
				time(),
				true
			);	

			wp_enqueue_style( 'jquery-ui-datepicker' );
	}
 }
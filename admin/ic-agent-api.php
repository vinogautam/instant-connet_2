<?php
use OpenTok\OpenTok;
class IC_agent_api{

	function __construct() {

	    header('Access-Control-Allow-Origin: *');
	    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
	    header("Access-Control-Allow-Headers: X-Requested-With");

	    
	    $functions = array('ic_all_letter_list', 'ic_agent_login', 'ic_site_pages', 'ic_add_endorser', 'ic_send_invitation',
	    	'ic_send_endorsement_invitation', 'ic_update_endorser', 'ic_endorser_list', 'ic_add_create_email_template',
	    	'ic_update_email_template', 'ic_endorser_letter_list', 'ic_endorsement_letter_list',
	    	'ic_delete_endorser', 'ic_delete_letter', 'ic_new_lead', 'ic_new_lead_nomail', 'ic_update_lead', 
	    	'ic_noti_to_agent', 'ic_noti_to_user', 'ic_resend_gift', 'ic_send_gift', 'ic_get_sites', 
	    	'ic_add_points', 'ic_get_points', 'ic_update_fb_id', 'ic_get_fb_id', 'ic_instant_meeting', 
	    	'ic_appointment_meeting', 'ic_update_meeting_date', 'ic_update_meeting_eventid', 'ic_new_lead_nomail',
	    	'ic_update_lead', 'ic_get_active_meeting_list', 'ic_generate_token', 'ic_update_active_time', 
	    	'ic_update_meeting_data', 'ic_get_endorser_info', 'ic_auto_login', 'ic_new_campaign', 
	    	'ic_update_campaign', 'ic_delete_campaign', 'ic_delete_campaign_letter', 'ic_campaigns', 
	    	'ic_new_video', 'ic_video_list', 'ic_delete_video', 'ic_test_template', 'ic_get_default_campaign',
	    	'ic_set_default_campaign', 'ic_get_template_style', 'ic_strategy', 'ic_update_video', 'ic_video_by_id',
	    	'ic_video_message', 'ic_video_message_delete', 'ic_video_message_update', 'ic_message_by_type',
	    	'test_email', 'ic_agent_endorsement_settings', 'ic_agent_save_endorsement_settings',
	    	'ic_agent_billing_transaction', 'ic_cron_agent_billing', 'ic_agent_update', 'ic_get_agent_details',
	    	'ic_upgrade_membership', 'ic_endorsement_settings', 'ic_endorser_login', 'ic_timekit_add_gmail', 'ic_video_message_by_id'
	    );
		
		foreach ($functions as $key => $value) {
			add_action( 'wp_ajax_'.$value, array( &$this, $value) );
			add_action( 'wp_ajax_nopriv_'.$value, array( &$this, $value) );
		}
	    
	}

	function ic_timekit_add_gmail() {
		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));
		if(update_user_meta($_POST['agent_id'], 'timekits_gmail_email', $_POST['timekit_gmail_email'])) {
                   update_user_meta($_POST['agent_id'], 'timekits_time_zone', $_POST['timekit_time_zone']);			
                   $response = array('status' => 'Success');
			
		   } else {
			 $response = array('status' => 'Failed to update');
		   }
		   echo json_encode($response);
                   die(0);
	}


	function ic_agent_update(){
		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		update_user_meta($_GET['user_id'], 'timekit_resource_id', $_POST['timekit_resource_id']);
		update_user_meta($_GET['user_id'], 'timekit_calendar_id', $_POST['timekit_calendar_id']);

		die(0);
	}

	function ic_get_agent_details(){
		$res =  array();
		$res['timekit_resource_id'] = get_user_meta($_GET['user_id'], 'timekit_resource_id', true);
		$res['timekit_calendar_id'] = get_user_meta($_GET['user_id'], 'timekit_calendar_id', true);

		echo json_encode($res);

		die(0);
	}

	function ic_agent_billing_transaction(){
		global $wpdb;

		$results = $wpdb->get_results("select * from agent_billing where user_id='".$_GET['user_id']."'");

		$response = array('status' => 'Success');
		echo json_encode($response);
		die(0);
	}

	function ic_cron_agent_billing(){
		global $wpdb;

		$users = get_users(array('userrole' => 'agent'));
		foreach ($users as $key => $value) {
			$results = $wpdb->get_row("select sum(points) as points from ".$wpdb->prefix . "points_transaction where created like '".date("Y-m-")."%' and user_id='".$value->ID."'");

			$amount = $results->points;
			$res = $wpdb->insert("agent_billing", array(
				'particulars' => 'Bill for the month of '.date("F"),
				'amount' => $amount,
				'credit' => 1,
				'agent_id' => $value->ID,
				'created' => date('Y-m-d H:i:s')
			));

			/* Debit the billing amount from agent cc will add here*/

		}
		
		die(0);
	}

	function ic_agent_save_endorsement_settings(){

		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		update_user_meta($_GET['agent_id'], 'endorsement_settings', $_POST);
		$res = get_user_meta($_GET['agent_id'], 'endorsement_settings', true);
		echo json_encode($res);
		die(0);
	}

	function ic_agent_endorsement_settings(){

		$res = get_user_meta($_GET['agent_id'], 'endorsement_settings', true);
		echo json_encode($res);
		die(0);
	}

	function test_email(){
		global $ntm_mail;
		$ntm_mail->send_welcome_mail('dhanavel237vino@gmail.com', $_GET['id'], 'sdfsf#dfsdf');
		die(0);
	}

	function ic_strategy(){
		$args = array(
			'posts_per_page'   => -1,
			'post_type' => 'strategy'
		);
		function strategy_format($a){
		return array('id' => $a->ID, 'title' => $a->post_title, 'link' => stripslashes(get_post_meta($a->ID, 'strategy_link', true)));
		}
		$strategy = array_map("strategy_format", get_posts($args));

		echo json_encode($strategy);
		die(0);
	}

	function ic_get_template_style(){
		$mail_template_css = get_option('mail_template_css');

		echo json_encode(array('css' => stripslashes(strip_tags($mail_template_css))));
		die(0);
	}

	function ic_test_template(){
		global $ntm_mail;
		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		$_POST['name'] = isset($_POST['name']) ? $_POST['name'] : 'Test Campaign Template';

		$template = '<!DOCTYPE html>
			<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
			<head>
			    <meta charset="utf-8"> <!-- utf-8 works for most cases -->
			    <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn\'t be necessary -->
			    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
			    <meta name="x-apple-disable-message-reformatting">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
			    <title></title> <!-- The title tag shows in email notifications, like Android 4.4. --><style>';

		$template .= stripslashes(strip_tags(get_option('mail_template_css')));

		$template .= '</style>
			    <!-- Progressive Enhancements : END -->

			    <!-- What it does: Makes background images in 72ppi Outlook render at correct size. -->
			    <!--[if gte mso 9]>
			    <xml>
			        <o:OfficeDocumentSettings>
			            <o:AllowPNG/>
			            <o:PixelsPerInch>96</o:PixelsPerInch>
			        </o:OfficeDocumentSettings>
			    </xml>
			    <![endif]-->

			</head>
			<body width="100%" style="margin: 0; mso-line-height-rule: exactly;">';

		$template .= stripslashes($_POST['template']);

		$template .= '</body></html>';

		$res = $ntm_mail->send_mail('neil.personalconsult@gmail.com', $_POST['name'], $template, '', '');
		$res = $ntm_mail->send_mail('Neil@financialinsiders.ca', $_POST['name'], $template, '', '');
		$res = $ntm_mail->send_mail('dhanavel237vino@gmail.com', $_POST['name'], $template, '', '');

		$response = array('status' => 'Success', 'res' => $res);
		echo json_encode($response);
		die(0);
	}

	function ic_get_default_campaign(){
		$response = array('status' => 'Success', 'res' => get_user_meta($_GET['user_id'], 'default_campaign', true));
		echo json_encode($response);
		die(0);
	}

	function ic_set_default_campaign(){
		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		$camps = get_user_meta($_POST['user_id'], 'default_campaign', true);

		$camps = $camps ? $camps : [];

		$camps[$_POST['template']] = $_POST['temp_id'];

		update_user_meta($_POST['user_id'], 'default_campaign', $camps);

		$response = array('status' => 'Success', 'res' => get_user_meta($_POST['user_id'], 'default_campaign', true));
		echo json_encode($response);
		die(0);
	}

	function ic_video_message(){
		global $wpdb;

		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		$_POST['created'] = date("Y-m-d H:i:s");

		$res = $wpdb->insert($wpdb->prefix . "video_message", $_POST);
		if($res){
		$response = array('status' => 'Success', 'id' => $wpdb->insert_id);
		} else {
			$response = array('status' => 'Error', 'msg' => 'Try again later!!');
		}
		echo json_encode($response);
		die(0);
	}

	function ic_video_message_delete(){
		global $wpdb;

		$results = $wpdb->delete($wpdb->prefix . "video_message", array('id' => $_GET['id']));

		$response = array('status' => 'Success');
		echo json_encode($response);
		die(0);
	}

	function ic_message_by_type(){
		global $wpdb;

		$results = $wpdb->get_results("select * from ". $wpdb->prefix . "video_message where message_type='".$_GET['type']."'");

		$response = array('status' => 'Success');
		echo json_encode($response);
		die(0);
	}
	
	function ic_video_message_by_id(){
		global $wpdb;

		$results = $wpdb->get_results("select * from ". $wpdb->prefix . "video_message where video_id='".$_GET['video_id']."'");
		$data = (array) $results;
		$response = array('status' => 'Success', 'data' => $data);
		echo json_encode($response);
		die(0);
	}

	function ic_message_with_video(){
		global $wpdb;

		$results = $wpdb->get_results("select * from ". $wpdb->prefix . "video_message v left join ".$wpdb->prefix . "video_library l on v.video_id = l.id");

		$response = array('status' => 'Success');
		echo json_encode($response);
		die(0);
	}

	function ic_video_message_update($perform = '', $data = array()){
		global $wpdb;

		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		if($perform){
			$vmsg = $data;
		} else {
			$perform = $_GET['perform'];
			$vmsg = $_POST;
		}

		if($perform == 'add'){
			$res = $wpdb->insert($wpdb->prefix . "video_message", $vmsg);
		} elseif($perform == 'edit') {
			$video_id = $vmsg['video_id'];
			unset($vmsg['video_id']);
			$res = $wpdb->update($wpdb->prefix . "video_message", $vmsg, array('video_id' => $video_id));
		} elseif($perform == 'delete') {
			$video_id = $vmsg['video_id'];
			$res = $wpdb->delete($wpdb->prefix . "video_message", array("video_id" => $video_id));
		}
		
		if(isset($_GET['perform'])){
			if($res){
			$response = array('status' => 'Success');
			} else {
				$response = array('status' => 'Error', 'msg' => 'Try again later!!');
			}
			echo json_encode($response);
			die(0);
		}
	}

	function ic_new_video(){
		global $wpdb;

		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		$_POST['created'] = date("Y-m-d H:i:s");

		$video_message = isset($_POST['video_message']) ? (array)$_POST['video_message'] : array();
		unset($_POST['video_message']);
		$res = $wpdb->insert($wpdb->prefix . "video_library", $_POST);
		if($res){
			$video_message['video_id'] = $wpdb->insert_id;
			if(isset($video_message['status_message'])){
				$this->ic_video_message_update('add', $video_message);
			}
			$response = array('status' => 'Success', 'id' => $video_message['video_id']);
		} else {
			$response = array('status' => 'Error', 'msg' => 'Try again later!!');
		}
		echo json_encode($response);
		die(0);
	}

	function ic_update_video(){
		global $wpdb;

		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));
		$video_message = isset($_POST['video_message']) ? (array)$_POST['video_message'] : array();
		unset($_POST['video_message']);
		$res = $wpdb->update($wpdb->prefix . "video_library", $_POST, array('id'=>$_GET['id']));
		if($res){
			if(isset($video_message['status_message'])){
				$video_message['video_id'] = $_GET['id'];
				$this->ic_video_message_update('edit', $video_message);
			}
			$response = array('status' => 'Success');
		} else {
			$response = array('status' => 'Error', 'msg' => 'Try again later!!');
		}
		echo json_encode($response);
		die(0);
	}

	function ic_delete_video(){
		global $wpdb;

		$results = $wpdb->delete($wpdb->prefix . "video_library", array("id" => $_GET['id']));

		$response = array('status' => 'Success');
		echo json_encode($response);
		die(0);
	}

	function ic_video_by_id(){
		global $wpdb;

		$results = $wpdb->get_row("select * from ". $wpdb->prefix . "video_library where id=".$_GET['id']);

		$data = (array) $results;
		$data['messages'] = $wpdb->get_results("select * from ". $wpdb->prefix . "video_message where video_id=".$data['id']);

		$response = array('status' => 'Success', 'data' => $data);
		echo json_encode($response);
		die(0);
	}

	function ic_video_list(){
		global $wpdb;

		$results = $wpdb->get_results("select * from ". $wpdb->prefix . "video_library where agent_id=".$_GET['agent_id']);

		$data = [];

		foreach ($results as $key => $value) {
			$val = (array) $value;
			$val['messages'] = $wpdb->get_results("select * from ". $wpdb->prefix . "video_message where video_id=".$val['id']);
			$data[] = $val;
		}

		$response = array('status' => 'Success', 'data' => $data);
		echo json_encode($response);
		die(0);
	}

	function ic_new_campaign(){
		global $wpdb;

		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		$res = $wpdb->insert($wpdb->prefix . "campaigns", array(
					'title' => $_POST['title'],
					'type' => $_POST['type'],
					//'is_default' => $_POST['is_default'],
					'is_main_site' => is_main_site(),
					'strategy' => $_POST['strategy']
				));
		
		if($res) {
			$id = $wpdb->insert_id;
			foreach ($_POST['templates'] as $key => $value) {
				$value = (array) $value;
				$wpdb->insert($wpdb->prefix . "campaign_templates", array(
					'campaign_id' => $id,
					'name' => addslashes($value['name']),
					'template' => addslashes(nl2br($value['template'])),
					'media' => $value['media'] ? $value['media'] : '',
					'subject' => addslashes($value['subject']),
					'preheader_text' => addslashes($value['preheader_text'])
				));
			}


			if(is_main_site()){
				print_r($_POST['sites']);
				foreach ($_POST['sites'] as $key1 => $value1) {
					$res = $wpdb->insert("wp_".$value1."_campaigns", array(
						'title' => $_POST['title'],
						'type' => $_POST['type'],
						'strategy' => $_POST['strategy']
					));

					$id1 = $wpdb->insert_id;
					foreach ($_POST['templates'] as $key => $value) {
						$value = (array) $value;
						$wpdb->insert("wp_".$value1."_campaign_templates", array(
							'campaign_id' => $id1,
							'name' => addslashes($value['name']),
							'template' => addslashes(nl2br($value['template'])),
							'media' => $value['media'] ? $value['media'] : '',
							'subject' => addslashes($value['subject']),
							'preheader_text' => addslashes($value['preheader_text'])
						));
					}
				}
			}


			$response = array('status' => 'Success', 'id' => $id);
		} else {
			$response = array('status' => 'Error', 'msg' => 'Try again later!!');
		}
		
		echo json_encode($response);
		die(0);
	}

	function ic_update_campaign(){
		global $wpdb;

		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		$res = $wpdb->update($wpdb->prefix . "campaigns", array(
					'title' => $_POST['title'],
					'type' => $_POST['type'],
					'strategy' => $_POST['strategy']
				), array('id' => $_POST['id']));
		
		///if($res) {
			$id = $_POST['id'];
			foreach ($_POST['templates'] as $key => $value) {
				$value = (array) $value;
				if(isset($value['id'])){
					$wpdb->update($wpdb->prefix . "campaign_templates", array(
						'name' => addslashes($value['name']),
						'template' => addslashes(nl2br($value['template'])),
						'media' => $value['media'] ? $value['media'] : '',
						'subject' => addslashes($value['subject']),
						'preheader_text' => addslashes($value['preheader_text'])
					), array('id' => $value['id']));
				} else {
					$wpdb->insert($wpdb->prefix . "campaign_templates", array(
						'campaign_id' => $id,
						'name' => addslashes($value['name']),
						'template' => addslashes(nl2br($value['template'])),
						'media' => $value['media'] ? $value['media'] : '',
						'subject' => addslashes($value['subject']),
						'preheader_text' => addslashes($value['preheader_text'])
					));
				}
			}

			$response = array('status' => 'Success', 'msg' => 'Campaign updated successfully');
		/*} else {
			$response = array('status' => 'Error', 'msg' => 'Try again later!!');
		}*/
		
		echo json_encode($response);
		die(0);
	}

	function ic_delete_campaign(){
		global $wpdb;

		$wpdb->delete($wpdb->prefix . "campaigns", array( 'id' => $_GET['id'] ) );
		$wpdb->delete($wpdb->prefix . "campaign_templates", array( 'campaign_id' => $_GET['id'] ) );

		$response = array('status' => 'Success', 'msg' => 'Campaign Letter template deleted successfully');
		echo json_encode($response);
		die(0);
	}

	function ic_delete_campaign_letter(){
		global $wpdb;

		$wpdb->delete($wpdb->prefix . "campaign_templates", array( 'id' => $_GET['id'] ) );

		$response = array('status' => 'Success', 'msg' => 'Campaign Letter template deleted successfully');
		echo json_encode($response);
		die(0);
	}

	function ic_campaigns(){
		global $wpdb;

		$campaigns = [];
		
		/*if(!is_main_site()){
			$results = $wpdb->get_results("select * from wp_campaigns");
			foreach ($results as $key => $value) {
				$value = (array) $value;

				$templates = $wpdb->get_results("select * from wp_campaign_templates where campaign_id=".$value['id']);
				$value['templates'] = [];
				foreach ($templates as $key => $value2) {
					$value2->preheader_text = stripslashes(stripslashes($value2->preheader_text));
					$value2->subject = stripslashes(stripslashes($value2->subject));
					$value2->template = str_replace("<br />", "", stripslashes(stripslashes($value2->template)));
					$value['templates'][$value2->name] = $value2;
				}
				$campaigns[] = $value;
			}
		}*/

		$results = $wpdb->get_results("select * from ".$wpdb->prefix . "campaigns");
		foreach ($results as $key => $value) {
			$value = (array) $value;

			$templates = $wpdb->get_results("select * from ".$wpdb->prefix . "campaign_templates where campaign_id=".$value['id']);
			$value['templates'] = [];
			foreach ($templates as $key => $value2) {
				$value2->preheader_text = stripslashes(stripslashes($value2->preheader_text));
				$value2->subject = stripslashes(stripslashes($value2->subject));
				$value2->template = str_replace("<br />", "", stripslashes(stripslashes($value2->template)));
				$value['templates'][$value2->name] = $value2;
			}

			$campaigns[] = $value;
		}


		$response = array('status' => 'Success', 'data' => $campaigns);
		echo json_encode($response);
		die(0);
	}

	function ic_endorser_reset_password(){
		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		wp_set_password( $_POST['password'], $_GET['id'] );

		$response = array('status' => 'Success');
		echo json_encode($response);
		die(0);
	}

	function ic_endorser_login(){
		global $wpdb, $ntm_mail;

		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		$creds = array();
		$creds['user_login'] = $_POST['email'];
		$creds['user_password'] = $_POST['password'];
		$creds['remember'] = true;
		$current_user = wp_signon( $creds, false );

		$points = $wpdb->get_row("select sum(points) as points from ".$wpdb->prefix . "points_transaction where endorser_id=".$current_user->ID);

		$campaign = get_user_meta($current_user->ID, 'campaign', true);
		$templates = $wpdb->get_row("select * from wp_campaign_templates where name = 'Endorser Letter' and campaign_id=".$campaign);

		$subject = 'Endorser Invitation';
		$content = str_replace("<br />", "", stripslashes(stripslashes($templates->template)));

		$mailtemplate = '<html><head><style>'.stripslashes(strip_tags(get_option('mail_template_css'))).'</style></head><body>'.$content.'</body></html>';

		if(!is_wp_error($current_user)) {
			$blog_id = get_active_blog_for_user( $current_user->ID )->blog_id;
			$agent_id = get_blog_option($blog_id, 'agent_id');

			$campaign = get_user_meta($current_user->ID, 'campaign', true);
			$dcampaign = $wpdb->get_row("select * from wp_campaigns where id=".$campaign);
			$pagelink = get_post_meta($dcampaign->strategy, 'strategy_link', true);

			$templates = $wpdb->get_row("select * from wp_campaign_templates where name = 'Endorser Letter' and campaign_id=".$campaign);

			$video = $templates->media ? $templates->media : get_user_meta($current_user->ID, 'video', true) ;

			$data = array(
					'endorser' => $current_user,
					'points' => $points->points ? $points->points : 0,
					'fb_ref_link' => $pagelink.'?ref='.base64_encode(base64_encode($current_user->ID.'#&$#fb')).'&video='.$video,
					'li_ref_link' => $pagelink.'?ref='.base64_encode(base64_encode($current_user->ID.'#&$#li')).'&video='.$video,
					'tw_ref_link' => $pagelink.'?ref='.base64_encode(base64_encode($current_user->ID.'#&$#tw')).'&video='.$video,
					'mailtemplate' => $mailtemplate,
					'blog_id' => $blog_id,
					'agent_id' => $agent_id,
					'twitter_text' => get_option('twitter_text'),
					'fb_text' => $dcampaign->facebook,
					'tw_text' => $dcampaign->twitter,
					'li_text' => $dcampaign->linkedin,
					'agent_avatar' => get_avatar_url($agent_id)
				);
			$response = array('status' => 'Success', 'data' => $data);
		} else {
			$response = array('status' => 'Error', 'msg' => 'Invalid link!!');
		}

		echo json_encode($response);
		die(0);
		exit;
	}

	function ic_auto_login(){
		global $wpdb, $ntm_mail;

		$autologin = explode("#", base64_decode(base64_decode($_GET['autologin'])));
		$creds = array();
		$creds['user_login'] = $autologin[0];
		$creds['user_password'] = $autologin[1];
		$creds['remember'] = true;
		$current_user = wp_signon( $creds, false );

		$points = $wpdb->get_row("select sum(points) as points from ".$wpdb->prefix . "points_transaction where endorser_id=".$current_user->ID);

		/*$endorser_letter = get_user_meta($current_user->ID, 'endorsement_letter', true);
		if($endorser_letter)
		{
			$res = $wpdb->get_row("select * from ".$wpdb->prefix . "mailtemplates where id=".$endorser_letter);
			$mailtemplate = $res->content;
		}
		else
		{
			$mailtemplate 	 	= 	$ntm_mail->get_invitation_mail ();
			$mailtemplate = $mailtemplate['content'];
		}*/

		$campaign = get_user_meta($current_user->ID, 'campaign', true);
		$templates = $wpdb->get_row("select * from wp_campaign_templates where name = 'Endorser Letter' and campaign_id=".$campaign);

		$subject = 'Endorser Invitation';
		$content = str_replace("<br />", "", stripslashes(stripslashes($templates->template)));

		$mailtemplate = '<html><head><style>'.stripslashes(strip_tags(get_option('mail_template_css'))).'</style></head><body>'.$content.'</body></html>';

		if(!is_wp_error($current_user)) {
			$blog_id = get_active_blog_for_user( $current_user->ID )->blog_id;
			$agent_id = get_blog_option($blog_id, 'agent_id');

			$campaign = get_user_meta($current_user->ID, 'campaign', true);
			$dcampaign = $wpdb->get_row("select * from wp_campaigns where id=".$campaign);
			$pagelink = get_post_meta($dcampaign->strategy, 'strategy_link', true);

			$templates = $wpdb->get_row("select * from wp_campaign_templates where name = 'Endorser Letter' and campaign_id=".$campaign);

			$video = $templates->media ? $templates->media : get_user_meta($current_user->ID, 'video', true) ;

			$data = array(
					'endorser' => $current_user,
					'points' => $points->points ? $points->points : 0,
					'fb_ref_link' => $pagelink.'?ref='.base64_encode(base64_encode($current_user->ID.'#&$#fb')).'&video='.$video,
					'li_ref_link' => $pagelink.'?ref='.base64_encode(base64_encode($current_user->ID.'#&$#li')).'&video='.$video,
					'tw_ref_link' => $pagelink.'?ref='.base64_encode(base64_encode($current_user->ID.'#&$#tw')).'&video='.$video,
					'mailtemplate' => $mailtemplate,
					'blog_id' => $blog_id,
					'agent_id' => $agent_id,
					'twitter_text' => get_option('twitter_text'),
					'fb_text' => $dcampaign->facebook,
					'tw_text' => $dcampaign->twitter,
					'li_text' => $dcampaign->linkedin,
					'agent_avatar' => get_avatar_url($agent_id)
				);
			$response = array('status' => 'Success', 'data' => $data);
		} else {
			$response = array('status' => 'Error', 'msg' => 'Invalid link!!');
		}

		echo json_encode($response);
		die(0);
		exit;
	}

	function ic_get_endorser_info(){
		global $wpdb;

		$endorser_id = $_GET['id'];
		$blog_id = get_active_blog_for_user( $endorser_id )->blog_id;
		$endorser = get_userdata($endorser_id);
		$agent_id = get_blog_option($blog_id, 'agent_id');

		$response = $wpdb->get_row("select sum(points) as points from ".$wpdb->prefix . "points_transaction where endorser_id=".$endorser_id);

		$res = array(
			'name' => get_user_meta($endorser_id, 'first_name', true). ' '. get_user_meta($endorser_id, 'last_name', true),
			'email' => $endorser->user_email,
			'points' => $response->points,
			'site_id' => $blog_id,
			'agent_id' => $agent_id
		);
		echo json_encode($res);
		die(0);
		exit;
	}

	function ic_update_meeting_data() {
		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		$event = (array) $_POST['event'];
		$meeting_id = $event['what'];

		$wpdb->update($wpdb->prefix . "meeting", 
			array(
				'event_id' => $event['id'],
				'meeting_date' => explode('+', $event['start'])[0]
			), 
			array('id' => $meeting_id));

		if($_POST['state'] == 'cancelled') {
			$wpdb->update($wpdb->prefix . "meeting_participants", 
			array(
				'status' => 4
			), 
			array(
				'id' => $meeting_id,
				'email' => $_POST['customers']['email']
			));
		}

		die(0);
		exit;
	}

	function ic_update_active_time() {
		global $wpdb;

		$wpdb->update($wpdb->prefix . "meeting", 
						array('active_time' => strtotime("now")),
						array("id" => $_GET['id'])
			);

		die(0);
		exit;
	}

	function ic_generate_token() {
		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		$token = opentok_token(isset($_POST['sessionId'])?$_POST['sessionId']:'');
		
		echo json_encode($token);
		die(0);
		exit;
	}

	function ic_get_active_meeting_list() {
		global $wpdb;

		$response = $wpdb->get_results("select * from ".$wpdb->prefix . "meeting a left join ".$wpdb->prefix . "meeting_participants b on a.id = b.meeting_id where a.meeting_date > '".date("Y-m-d H:i:s")."'");

		echo json_encode($response);
		die(0);
	}

	function ic_new_lead_nomail() {
		global $wpdb;

		$lead = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));
		
		$resuts = $wpdb->get_results('select * from wp_leads where email = "'. $lead['email'].'"');
		if(count($resuts)){
			$wpdb->update("wp_leads", $lead, array('email' => $lead['email']));
			$lead_id = $resuts[0]->id;
			$msg = 'Lead already exist, data updated';
		} else {
			$wpdb->insert("wp_leads", $lead);
			$lead_id = $wpdb->insert_id;
			$msg = 'Lead created successfully';
		}

		if($lead_id) {
			$response = array('status' => 'Success', 'id' => $lead_id, 'msg' => $msg);
		} else {
			$response = array('status' => 'Error', 'msg' => 'Try again later!!');
		}
		
		echo json_encode($response);
		die(0);
	}

	function ic_update_lead() {
		global $wpdb;

		$lead = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		$wpdb->update("wp_leads", $lead, array('id' => $_GET['email']));
		$lead_id = $wpdb->insert_id;

		if($lead_id) {
			$response = array('status' => 'Success', 'msg' => 'Lead updated successfully');
		} else {
			$response = array('status' => 'Error', 'msg' => 'Try again later!!');
		}
		
		echo json_encode($response);
		die(0);
	}

	function ic_update_meeting_date()
	{
		global $wpdb;
		
		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		$wpdb->update($wpdb->prefix . "meeting", array('meeting_date' => $_POST['meeting_date']), array('id' => $_POST['id']));

		die(0);
		exit;
	}

	function ic_update_meeting_eventid()
	{
		global $wpdb;
		
		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		$wpdb->update($wpdb->prefix . "meeting", array('event_id' => $_POST['event_id']), array('id' => $_POST['id']));

		die(0);
		exit;
	}

	function ic_appointment_meeting()
	{
		global $wpdb;
		
		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		$meetingId = time();
		
		$opentok = opentok_token();
		
		$wpdb->insert($wpdb->prefix . "meeting", array('agent_id' => $_POST['agent_id'], 'created' => date("Y-m-d H:i:s"), 'session_id' => $opentok['sessionId'], 'token' => $opentok['token']));
		$meeting_id = $wpdb->insert_id;
		
		$opentok['id'] = $meeting_id;
		$status = $_GET['st'] ? 3 : 2;

		$d = (array)$_POST['participants'];

		$d['meeting_id'] = $meeting_id;
		$d['meeting_date'] = date("Y-m-d H:i:s");
		$d['status'] = $status;

		$wpdb->insert($wpdb->prefix . "meeting_participants", $d);
		
		echo json_encode(array('meeting_id' => $meeting_id, 'pid' => $wpdb->insert_id));
		
		die(0);
		exit;
	}
	
	function ic_instant_meeting()
	{
		global $wpdb;
		
		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		$meetingId = time();
		
		$meeting = $wpdb->get_results("select * from ".$wpdb->prefix . "meeting where (".strtotime("now")." - active_time) < 15 ");

		if(count($meeting)){
			$nm = 'existing';
			$meeting_id = $meeting[0]->id;
		} else {
			$opentok = opentok_token();
			$wpdb->insert($wpdb->prefix . "meeting", array('agent_id' => $_POST['agent_id'], 'meeting_date' => date("Y-m-d H:i:s"), 'created' => date("Y-m-d H:i:s"), 'session_id' => $opentok['sessionId'], 'token' => $opentok['token']));
			$nm = 'new';
			$meeting_id = $wpdb->insert_id;
		}
		
		$opentok['id'] = $meeting_id;
		$status = $_GET['st'] ? 3 : 2;

		foreach($_POST['participants'] as $d){
			$d = (array)$d;
			$d['meeting_id'] = $meeting_id;
			$d['meeting_date'] = date("Y-m-d H:i:s");
			$d['status'] = $status;

			$wpdb->insert($wpdb->prefix . "meeting_participants", $d);
		}
		
		$finonce = time().rand(11111,99999);
		setcookie('finonce', $finonce);

		echo json_encode(array('meeting_id' => $meeting_id, 'finonce' => $finonce, 'pid' => $wpdb->insert_id, 'status' => $nm));
		
		die(0);
		exit;
	}

	function ic_update_fb_id(){
		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		update_user_meta($_POST['user_id'], 'firebase_id', $_POST['firebase_id']);

		$response = array('status' => 'Success');
		echo json_encode($response);
		die(0);
	}

	function ic_get_fb_id(){
		$firebase_id = get_user_meta($_GET['user_id'], 'firebase_id', true);

		$response = array('status' => 'Success', 'firebase_id' => $firebase_id);
		echo json_encode($response);
		die(0);
	}

	function ic_get_points(){
		global $wpdb;

		$response = $wpdb->get_row("select sum(points) as points from ".$wpdb->prefix . "points_transaction where endorser_id=".$_GET['endorser_id']);
		$response = array('status' => 'Success', 'total_points' => $response->points ? $response->points : 0);
		echo json_encode($response);
		die(0);
	}

	function ic_add_points(){
		global $wpdb;

		$_POST = (array) json_decode(file_get_contents('php://input'));

		if($_POST['type'] == 'fbShare' || $_POST['type'] == 'liShare'){
			$blog_id = get_active_blog_for_user( $_POST['endorser_id'] )->blog_id;
			$agent_id = get_blog_option($blog_id, 'agent_id');
			$settings = get_user_meta($agent_id, 'endorsement_settings', true);
			$points = $_POST['type'] == 'fbShare' ? $settings['fb_point_value'] : $settings['linked_point_value'] ;

			$data = array(
							'endorser_id' =>$_POST['endorser_id'],
							'points' => $points,
							'type' => $_POST['type'],
							'notes' => $_POST['notes'],
							'created'	=> date("Y-m-d H:i:s")
							);
			$wpdb->insert($wpdb->prefix . "points_transaction", $data);
		}
		
		$response = $wpdb->get_row("select sum(points) as points from ".$wpdb->prefix . "points_transaction where endorser_id=".$_POST['endorser_id']);
		$response = array('status' => 'Success', 'total_points' => $response->points ? $response->points : 0);
		echo json_encode($response);
		die(0);
	}

	function ic_site_pages() {

		echo json_encode(array('data' => get_pages()));
		die(0);
	}

	function ic_send_endorsement_invitation() {
		global $wpdb, $ntm_mail;
		$_POST = (array) json_decode(file_get_contents('php://input'));

		
		$contact_list = $_POST['contacts'];
		$notes = $_POST['template'];

		$campaign = get_user_meta($_POST['id'], 'campaign', true);
		$templates = $wpdb->get_row("select * from wp_campaign_templates where name = 'Endorser Letter' and campaign_id=".$campaign);

		$subject = 'Endorser Invitation';
		$content = str_ireplace("<br />", "", stripslashes(stripslashes($templates->template)));
		$content = str_ireplace("[NOTES]", $notes, $content);
		$endorse_letter = $content;

		foreach($contact_list as $res)
		{

			$res = (array)$res;
			
			$info = array(
				"name" => $res['name'], 
				"created" => date("Y-m-d H:i:s"), 
				"email" => $res['email'],
				"endorser_id" => $_POST['id'],
				"tracker_id" => wp_generate_password( $length=12, $include_standard_special_chars=false )
			);
			$wpdb->insert($wpdb->prefix . "endorsements", $info);
			$ntm_mail->send_invitation_mail($info, $_POST['id'], $wpdb->insert_id, $endorse_letter);
		}

		$blog_id = get_active_blog_for_user( $_POST['id'] )->blog_id;
		$agent_id = get_blog_option($blog_id, 'agent_id');
		$points = get_user_meta($agent_id, 'endorsement_settings', true)['email_point_value'];
		$note_points = get_user_meta($agent_id, 'endorsement_settings', true)['note_point_value'];

		if(strlen($notes) > 100){
			$points += $note_points;
		}

		$data = array(
						'endorser_id' => $_POST['id'],
						'points' => $points,
						'type' => 'email_invitation',
						'created'	=> date("Y-m-d H:i:s")
						);
		$wpdb->insert($wpdb->prefix . "points_transaction", $data);
		
		update_user_meta($_POST['id'], "invitation_sent", (get_user_meta($_POST['id'], "invitation_sent", true) + count($contact_list)));


		die(0);
	}

	function ic_send_invitation() {
		global $wpdb;
		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		foreach ($_POST['mail_list'] as $mail){

			$subject = $_POST['subject'];
			$message = $_POST['message'];

			NTM_mail_template::send_gift_mail($mail['mail'], $subject, $message, '', '');
		}


		die(0);
	}

	function ic_get_sites(){
		$data = [];
		foreach(get_sites() as $sites ) {
			$data[] = array('site_id' => $sites->blog_id, 'domain' => $sites->domain.$sites->path, 'domainname' => str_replace('/', '', $sites->path));
		}
		echo json_encode($data);
		die(0);
	}
	
	function ic_send_gift(){
		global $wpdb;

		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));
		$data = array(
						'endorser_id' =>$_POST['endorser_id'],
						'amout' => $_POST['gift_amount'],
						'fb_count'	=> get_user_meta($_POST['id'], "tracked_fb_counter", true),
						'twitter_count'	=> get_user_meta($_POST['id'], "tracked_tw_counter", true),
						"agent_id" => $current_user->ID,
						'created'	=> date("Y-m-d H:i:s")
						);
		$wpdb->insert($wpdb->prefix . "gift_transaction", $data);
		$gift_id = $wpdb->insert_id;
		$get_results = $wpdb->get_results("select * from ".$wpdb->prefix . "endorsements where endorser_id=".$_POST['endorser_id']." and track_status is not null and gift_status is null");
		
		foreach($get_results as $res)
		{
			$wpdb->insert($wpdb->prefix . "giftendorsements", array(
																"gift_id" => $gift_id, 
																"endorser_id" => $_POST['endorser_id'], 
																"endorsement_id" => $res->id
																)
							);
			$wpdb->update($wpdb->prefix . "endorsements", array('gift_status' => 1), array('id' => $res->id));
		}
		
		update_user_meta($_POST['endorser_id'], "tracked_fb_counter", 0);
		update_user_meta($_POST['endorser_id'], "tracked_tw_counter", 0);
		update_user_meta($_POST['endorser_id'], "tracked_counter", 0);
		NTM_mail_template::send_gift_mail('get_gift_mail', $_POST['endorser_id'], $gift_id);

		$response = array('status' => 'Success', 'msg' => 'Gift send');
		echo json_encode($response);
		die(0);
	}

	function ic_resend_gift(){
		global $wpdb;

		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		$data = array(
						'endorser_id' =>$_POST['endorser_id'],
						'amout' => $_POST['gift_amount'],
						'agent_id' => get_current_user_id(),
						'created'	=> date("Y-m-d H:i:s")
						);
		$wpdb->insert($wpdb->prefix . "gift_transaction", $data);
		$gift_id = $wpdb->insert_id;
		foreach($_POST['endorsement'] as $res)
		{
			$wpdb->insert($wpdb->prefix . "giftendorsements", array(
																"gift_id" => $gift_id, 
																"endorser_id" => $_POST['endorser_id'], 
																"endorsement_id" => $res
																)
							);
			$wpdb->update($wpdb->prefix . "endorsements", array('gift_status' => 2), array('id' => $res->id));
		}
		NTM_mail_template::send_gift_mail('get_regift_mail', $_POST['endorser_id'], $gift_id);

		$response = array('status' => 'Success', 'msg' => 'Gift Resend');
		echo json_encode($response);
		die(0);
	}

	function ic_upgrade_membership(){
		global $wpdb;
		$membership = (array) $wpdb->get_row("select * from wp_pmpro_memberships_orders where user_id=".$_GET['id']);
		$membership['user_id'] = $_POST['id'];
		$membership['membership_id'] = 2;
		$membership['timestamp'] = date("Y-m-d H:i:s");
		
		$membership_level = $wpdb->get_row("select * from wp_pmpro_memberships_levels where user_id=2");

		$fa_lead_options = get_option('fa_lead_settings');
		Stripe::setApiKey($fa_lead_options['api_key']);
		Stripe::setAPIVersion("2015-07-13");
		$invoice_item = Stripe_InvoiceItem::create( array(
			'customer'    => $_POST['id'], // the customer to apply the fee to
			'amount'      => $membership_level->billing_amount * 100,
			'currency'    => 'usd',
			'description' => 'One-time setup fee' // our fee description
		) );
	 
		$invoice = Stripe_Invoice::create( array(
			'customer'    => $_POST['id'], // the customer to apply the fee to
		) );

		//Stripe Integration here
		$wpdb->insert("wp_pmpro_membership_orders", $membership);
		$wpdb->update("wp_pmpro_memberships_users", array('user_id' => $_GET['id']));
		
		$response = array('status' => 'Success');
		echo json_encode($response);
		die(0);
	}

	function ic_agent_login(){
		global $wpdb;
		$creds = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));
		$user = wp_signon( $creds, false );
		$userBlogs = get_blogs_of_user((int)$user->data->ID);
		$timekitGmail = get_user_meta((int)$user->data->ID, 'timekits_gmail_email', true);
$timekitTimeZone = get_user_meta((int)$user->data->ID, 'timekits_time_zone', true);
		$siteUrl = reset($userBlogs);
		if ( is_wp_error($user) ) {
			$response = array('status' => 'Error', 'msg' => 'Invalid Credentials');
		}
		else{
			$data = (array) $user->data;
			$membership = $wpdb->get_row("select * from wp_pmpro_memberships_users where user_id=".$user->data->ID);
			$data['membership'] = isset($membership->membership_id) ? $membership->membership_id : 0;
			$data['timekit_gmail'] = $timekitGmail;
                        $data['timekit_time_zone'] = $timekitTimeZone;
			$response = array('status' => 'Success', 'data' => $data, 'msg' => 'Logged in successfully', 'site_url' => $siteUrl->siteurl);
		}
		echo json_encode($response);
		die(0);
	}

	function ic_add_endorser(){
		global $ntm_mail;

		$user = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));
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
				update_user_meta($user_id, 'first_name', $user['first_name']);
				update_user_meta($user_id, 'last_name', $user['last_name']);
				update_user_meta($user_id, 'phone', $user['phone']);
				update_user_meta($user_id, 'video', $user['video']);
				update_user_meta($user_id, 'endorser_letter', $user['endorser_letter']);
				update_user_meta($user_id, 'endorsement_letter', $user['endorsement_letter']);
				update_user_meta($user_id, 'campaign', $user['campaign']);
				update_user_meta($user_id, 'social_campaign', $user['social_campaign']);
				if( isset($user['video']) ){
					$ntm_mail->send_welcome_mail($user['user_email'], $user_id, $user['user_login'].'#'.$user['user_pass'], $user['video']);
				} else {
					$ntm_mail->send_welcome_mail($user['user_email'], $user_id, $user['user_login'].'#'.$user['user_pass']);
				}
				$ntm_mail->send_notification_mail($user_id);

				$response = array('status' => 'Success', 'data' => $user_id, 'msg' => 'Endorser created successfully');
			}
		} else {
			$response = array('status' => 'Error', 'msg' => 'User already exists.  Password inherited.');
		}

		echo json_encode($response);
		die(0);
	}

	function ic_update_endorser() {
		$user = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		update_user_meta($user['id'], 'endorser_letter', $user['endorser_letter']);
		update_user_meta($user['id'], 'endorsement_letter', $user['endorsement_letter']);
		update_user_meta($user['id'], 'phone', $user['phone']);
		update_user_meta($user['id'], 'first_name', $user['first_name']);
		update_user_meta($user['id'], 'last_name', $user['last_name']);
		update_user_meta($user['id'], 'campaign', $user['campaign']);
		update_user_meta($user['id'], 'video', $user['video']);
		update_user_meta($user['id'], 'social_campaign', $user['social_campaign']);
		$response = array('status' => 'Success', 'msg' => 'Endorser updated successfully');
		echo json_encode($response);
		die(0);
	}

	function objectToArray($d){
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

	function ic_endorser_list(){
		global $wpdb;
		$data = (array)get_users(array('role'=>'endorser'));
		
        $newdat = array();
		foreach($data as $v){
			$v = (array)$v;
			$item = (array)$v['data'];
			$item['id'] = $item['ID'];
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
				$campaign = get_user_meta($item['ID'], 'campaign', true);
				$result3 = $wpdb->get_row("select title from ". $wpdb->prefix . "campaigns where id=".$campaign);
				$social_campaign = get_user_meta($item['ID'], 'social_campaign', true);
				$result4 = $wpdb->get_row("select title from ". $wpdb->prefix . "campaigns where id=".$social_campaign);

				$item['first_name'] = get_user_meta($item['ID'], 'first_name', true);
				$item['last_name'] = get_user_meta($item['ID'], 'last_name', true);
				$item['phone'] = get_user_meta($item['ID'], 'phone', true);
				$item['video'] = get_user_meta($item['ID'], 'video', true);
				$item['campaign'] = $campaign;
				$item['social_campaign'] = $social_campaign;

				$item['endorser_letter_name'] = ($result->name ? $result->name : 'Default') ;
				$item['endorsement_letter_name'] = ($result2->name ? $result2->name : 'Default') ;
				$item['campaign_name'] = ($result3->title ? $result3->title : 'Default') ;
				$item['social_ampaign_name'] = ($result4->title ? $result4->title : 'Default') ;

				$newdat[] = $item;
			}
		}
		$response = array('status' => 'Success', 'data' => $newdat);
		echo json_encode($response);
		die(0);
		
	}

	function ic_add_create_email_template() {
		global $wpdb;


		$letter = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

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

	function ic_all_letter_list() {
		global $wpdb;

		$response = $wpdb->get_results('select * from '.$wpdb->prefix . "mailtemplates");
		$newres = [];
		foreach($response as $res){
			$res = (array)$res;
			$res['pagename'] = get_the_title($res['page']);
			$newres[] = $res;
		}

		echo json_encode(array('status' => 'Success', 'data' => $newres));
		die(0);
	}

	function ic_endorser_letter_list() {
		global $wpdb;

		$response = array('status' => 'Success', 'data' => $wpdb->get_results('select * from '.$wpdb->prefix . "mailtemplates where type = 'Endorser'")); 

		echo json_encode($response);
		die(0);
	}

	function ic_endorsement_letter_list() {
		global $wpdb;

		$response = array('status' => 'Success', 'data' => $wpdb->get_results('select * from '.$wpdb->prefix . "mailtemplates where type = 'Endorsement'")); 

		echo json_encode($response);
		die(0);
	}

	function ic_update_email_template(){
		global $wpdb;


		$letter = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));
		$id = $letter['id'];
		unset($letter['pagename']);
		unset($letter['id']);
		$res = $wpdb->update($wpdb->prefix . "mailtemplates", $letter, array('id' => $id));

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

	function ic_noti_to_user(){

		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));
		$subject = 'Notification - You have new message from agent';
		$message = 'Agent waiting for your reply Please join the chat.';
		
		if(NTM_mail_template::send_mail($_POST['email'], $subject, $message))
			$response = array('status' => 'Success', 'msg' => 'Notifications send to user');
		else
			$response = array('status' => 'Error', 'msg' => 'Notifications failed to send');
		echo json_encode($response);
		die(0);
	}

	function ic_noti_to_agent(){
		
		$_POST = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));
		$blog_id = get_current_blog_id();
		$agent_id = get_blog_option($blog_id, 'agent_id');
		$user_info = get_userdata($agent_id);
		$subject = 'Notification - You have new message from '.$_POST['email'];
		$message = 'User waiting for your repy.';

		if(NTM_mail_template::send_mail($user_info->user_email, $subject, $message))
			$response = array('status' => 'Success', 'msg' => 'Notifications send to agent');
		else
			$response = array('status' => 'Error', 'msg' => 'Notifications failed to send');
		echo json_encode($response);
		echo json_encode($response);
		die(0);
	}

	function ic_new_lead() {
		global $wpdb;

		$lead = count($_POST) ? $_POST : (array) json_decode(file_get_contents('php://input'));

		$wpdb->insert("wp_leads", $lead);
		$lead_id = $wpdb->insert_id;

		if($lead_id) {
			$message = 'Thanks for signing up FinancialInsiders. <a href="'.site_url().'?action=update_lead_status&id='.base64_encode(base64_encode($lead_id)).'">Click here to confirm your registration</a>';
		
			NTM_mail_template::send_mail($lead['email'], 'Registered with FinancialInsiders successfly.', $message);

			$response = array('status' => 'Success', 'msg' => 'Lead created successfully');
		} else {
			$response = array('status' => 'Error', 'msg' => 'Try again later!!');
		}
		
		echo json_encode($response);
		die(0);
	}

	function ic_get_endorsement() {
		global $wpdb;

		$res = $wpdb->get_results("select * from ".$wpdb->prefix . "endorsements");

		$newres = [];
		foreach ($res as $key => $value) {
			$value = (array)$value;
			$value['track_link'] = base64_encode(base64_encode($value['id'].'#&$#'.$value['endorser_id'].'#&$#'.$value['tracker_id']));
			$value['track_status'] = $value['track_status'] ? "Yes" : "No";
			$value['gift_status'] = $value['gift_status'] ? "Yes" : "No";
			$value['endorser_id'] = get_user_meta($value['endorser_id'], 'first_name', true).' '.get_user_meta($value['endorser_id'], 'last_name', true);
			$value['created'] = date('Y/m/d', strtotime($value['created']));
			$newres[] = $value;
		}

		$response = array('status' => 'Success', 'data' => $res);
		echo json_encode($response);
		die(0);
	}
}

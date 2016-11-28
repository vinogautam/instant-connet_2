<?php
use OpenTok\OpenTok;
class IC_ajax{
    
    function __construct() {
        add_action( 'wp_ajax_join_chat', array( &$this, 'join_chat') );
		add_action( 'wp_ajax_nopriv_join_chat', array( &$this, 'join_chat') );
		add_action( 'wp_ajax_waiting_participants', array( &$this, 'waiting_participants') );
		add_action( 'wp_ajax_new_participants', array( &$this, 'new_participants') );
		add_action( 'wp_ajax_get_status', array( &$this, 'agent_status') );
		add_action( 'wp_ajax_nopriv_agent_status', array( &$this, 'agent_status') );
		add_action( 'wp_ajax_create_new_meeting', array( &$this, 'create_new_meeting') );
		add_action( 'wp_ajax_update_agent_status', array( &$this, 'update_agent_status'));
		add_action( 'wp_ajax_check_meeing', array( &$this, 'check_meeing') );
		add_action( 'wp_ajax_nopriv_check_meeing', array( &$this, 'check_meeing') );
		add_action( 'wp_ajax_update_user_offline', array( &$this, 'update_user_offline'));
		add_action( 'wp_ajax_presentation_file', array( &$this, 'presentation_file'));
		add_action( 'wp_ajax_save_settings', array( &$this, 'save_settings'));
		add_action( 'wp_ajax_delete_presentation_file', array( &$this, 'delete_presentation_file'));
		add_action( 'wp_ajax_save_ppt', array( &$this, 'save_ppt'));
		add_action( 'wp_ajax_nopriv_save_ppt', array( &$this, 'save_ppt'));
		add_action( 'wp_ajax_new_user_to_meeting', array( &$this, 'new_user_to_meeting'));
		add_action( 'wp_ajax_usercontrol', array( &$this, 'usercontrol'));
		add_action( 'wp_ajax_addnew_video', array( &$this, 'addnew_video'));
		add_action( 'wp_ajax_delete_video', array( &$this, 'delete_video'));
		add_action( 'wp_ajax_delete_presentation', array( &$this, 'delete_presentation'));
		add_action( 'wp_ajax_nopriv_delete_presentation', array( &$this, 'delete_presentation'));
		add_action( 'wp_ajax_send_ic_gift', array( &$this, 'send_ic_gift') );
		add_action( 'wp_ajax_nopriv_send_ic_gift', array( &$this, 'send_ic_gift') );
		add_action( 'wp_ajax_send_add_chat_points', array( &$this, 'add_chat_points') );
		add_action( 'wp_ajax_nopriv_send_add_chat_points', array( &$this, 'add_chat_points') );
		add_action( 'wp_ajax_heartbeat', array( &$this, 'heartbeat'), 100);
    }
	
    function heartbeat()
    {
    	global $current_user;
		update_user_meta($current_user->ID, 'user_logintime', date("Y-m-d H:i:s"));
		echo "string";
		die(0);
		exit;
    }

	function add_chat_points()
    {
    	global $wpdb, $ntm_mail, $endorsements;

    	$results = $wpdb->get_row("select * from ".$wpdb->prefix . "meeting_participants where id=".$_GET['id']);

    	$points = 25;
		$type = 'Instant connect success meeting';
		$new_balance = $endorsements->get_endorser_points($results->endorser) + $points;
		$data = array('points' => $points, 'credit' => 1, 'endorser_id' => $results->endorser, 'new_balance' => $new_balance, 'transaction_on' => date("Y-m-d H:i:s"), 'type' => $type);
		$endorsements->add_points($data);
    }

    function send_ic_gift()
    {
    	global $wpdb, $ntm_mail, $endorsements;

    	$this->fa_lead_options = get_option('fa_lead_settings');

    	$results = $wpdb->get_row("select * from ".$wpdb->prefix . "meeting_participants where id=".$_GET['id']);

    	$data = array(
							'endorser_id' =>$results->endorser,
							'amout' => $this->fa_lead_options['init_gift'],
							'agent_id' => get_blog_option(get_current_blog_id(), 'agent_id'),
							'created'	=> date("Y-m-d H:i:s")
							);
		$wpdb->insert($wpdb->prefix . "gift_transaction", $data);
		$gift_id = $wpdb->insert_id;
		
		$wpdb->update($wpdb->prefix . "meeting_participants", array('gift_status' => 1), array('id' => $_GET['id']));

		$points = 125;
		$type = 'Instant connect success meeting';
		$new_balance = $endorsements->get_endorser_points($results->endorser) + $points;
		$data = array('points' => $points, 'credit' => 1, 'endorser_id' => $results->endorser, 'new_balance' => $new_balance, 'transaction_on' => date("Y-m-d H:i:s"), 'type' => $type);
		$endorsements->add_points($data);

		$ntm_mail->send_gift_mail('get_gift_mail', $results->endorser, $gift_id);
    }

    function delete_video()
    {
    	$option = get_option('youtube_videos');
    	$option = is_array($option) ? $option : [];

		$new_option = array();

		foreach($option as $k=>$n)
		{
			if($k != $_GET['ind'])
			{
				$new_option[] = $n;
			}
		}

		update_option('youtube_videos', $new_option);
		echo json_encode($new_option);
    	die(0);
		exit;
    }

    function delete_presentation()
    {
    	$option = get_option('ic_presentations');
    	$option = is_array($option) ? $option : [];

		$new_option = array();

		foreach($option as $k=>$n)
		{
			if($k != $_GET['ind'])
			{
				$new_option[] = $n;
			}
		}

		update_option('ic_presentations', $new_option);

		echo json_encode($new_option);
    	die(0);
		exit;
    }

    function addnew_video()
    {
    	$_POST = (array) json_decode(file_get_contents('php://input'));
    	$option = get_option('youtube_videos');

		if(isset($_POST['url']))
		{	
			$option = is_array($option) ? $option : [];
			$option[] = $_POST;
			update_option('youtube_videos', $option);
		}
    	die(0);
		exit;
    }

	function usercontrol()
	{
		global $wpdb;
		$_POST = (array) json_decode(file_get_contents('php://input'));
		$wpdb->update($wpdb->prefix . "meeting_participants", 
						array($_POST['type'] => $_POST['status']),
						array("id" => $_POST['pid'])
			);
		$joined_user = $wpdb->get_results("select * from ".$wpdb->prefix . "meeting_participants where (status = 3 or status = 2) and meeting_id=".$_POST['mid']);
		$participants = $wpdb->get_results("select * from ".$wpdb->prefix . "meeting_participants where (status = 0 or status = 1)");
		
		echo json_encode(array('joined_user' => $joined_user, 'participants' => $participants));
		
		die(0);
		exit;
	}
	
	function new_user_to_meeting()
	{
		global $wpdb;
		$_POST = (array) json_decode(file_get_contents('php://input'));
		$wpdb->update($wpdb->prefix . "meeting_participants", 
						array(	'meeting_id' => $_POST['mid'], 
								'meeting_date' => date("Y-m-d H:i:s"),
								'status' => $_POST['status']
							),
						array("id" => $_POST['pid'])
			);


		if($_POST['status'])
		{
			setcookie("instant_connect_waiting_id", $_POST['pid'], time()-3600, "/");
		}

		$joined_user = $wpdb->get_results("select * from ".$wpdb->prefix . "meeting_participants where (status = 3 or status = 2) and meeting_id=".$_POST['mid']);
		$participants = $wpdb->get_results("select * from ".$wpdb->prefix . "meeting_participants where (status = 0 or status = 1)");
		
		echo json_encode(array('joined_user' => $joined_user, 'participants' => $participants));
		
		die(0);
		exit;
	}
	
	function resize_image($file, $w, $h, $crop=FALSE) {
		list($width, $height) = getimagesize($file);
	    $r = $width / $height;
	    if ($crop) {
	        if ($width > $height) {
	            $width = ceil($width-($width*abs($r-$w/$h)));
	        } else {
	            $height = ceil($height-($height*abs($r-$w/$h)));
	        }
	        $newwidth = $w;
	        $newheight = $h;
	    } else {
	        if ($w/$h > $r) {
	            $newwidth = $h*$r;
	            $newheight = $h;
	        } else {
	            $newheight = $w/$r;
	            $newwidth = $w;
	        }
	    }
	    $src = imagecreatefromjpeg($file);
	    $dst = imagecreatetruecolor($newwidth, $newheight);
	    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

	    imagejpeg($dst,$file);
	}

	function save_ppt()
	{
		$data = $_POST['data'];
		$file = explode("?", $data)[1];
		file_put_contents(IC_PLUGIN_DIR."zip_files/$file.zip", file_get_contents($data));

		$zip = new ZipArchive;
		$res = $zip->open(IC_PLUGIN_DIR."zip_files/$file.zip");
		if ($res === TRUE) {
		  $zip->extractTo(IC_PLUGIN_DIR."extract/$file/");
		  $zip->close();
			$files = array();
			$i = 0;
			if (is_dir(IC_PLUGIN_DIR."extract/$file/")){
					  if ($dh = opendir(IC_PLUGIN_DIR."extract/$file/")){
						while (($filee = readdir($dh)) !== false){
						  if(str_replace(".", "", $filee))
						  {
						  	$files[] = $filee;
							//resize_image(IC_PLUGIN_DIR."/extract/$file/".$filee, 1600, 1200);
							$w = 1600; $h = 1200;
							list($width, $height) = getimagesize(IC_PLUGIN_DIR."extract/$file/".$filee);
						    $r = $width / $height;
						    if ($crop) {
						        if ($width > $height) {
						            $width = ceil($width-($width*abs($r-$w/$h)));
						        } else {
						            $height = ceil($height-($height*abs($r-$w/$h)));
						        }
						        $newwidth = $w;
						        $newheight = $h;
						    } else {
						        if ($w/$h > $r) {
						            $newwidth = $h*$r;
						            $newheight = $h;
						        } else {
						            $newheight = $w/$r;
						            $newwidth = $w;
						        }
						    }
						    $src = imagecreatefromjpeg(IC_PLUGIN_DIR."extract/$file/".$filee);
						    $dst = imagecreatetruecolor($newwidth, $newheight);
						    imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

						    imagejpeg($dst,IC_PLUGIN_DIR."extract/$file/".$filee);
						  }
						  $i++;
						}
						closedir($dh);
					  }
			}
			
			$option = get_option('ic_presentations');
			$option = is_array($option) ? $option : [];
			$option[] = array('folder' => $file, 'files' => $files, 'name' => $_GET['name']);
			update_option('ic_presentations', $option);

			echo json_encode(array('folder' => $file, 'files' => $files));
		} else {
		  echo 'error';
		}
		
		die(0);
		exit;
	}
	
	function delete_presentation_file()
	{
		global $wpdb;
		
		$wpdb->delete($wpdb->prefix . "meeting_presentations", array( 'id' => $_GET['id'] ));
		$presentations = $wpdb->get_results("select * from ". $wpdb->prefix . "meeting_presentations");
		echo json_encode($presentations);
		
		die(0);
		exit;
	}
	
	function save_settings()
	{
		update_option('bbb', (array)json_decode(file_get_contents('php://input')));
		
		die(0);
		exit;
	}
	
	function presentation_file()
	{
		global $wpdb;
		
		if(isset($_FILES['file']))
		{
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}

			$uploadedfile = $_FILES['file'];

			$upload_overrides = array( 'test_form' => false );

			$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

			if ( $movefile && !isset( $movefile['error'] ) ) {
				
				unset($movefile['type']);
				
				$movefile['default_presentation'] = $_GET['default'];
				$movefile['name'] = $uploadedfile['name'];
				
				if($_GET['default'])
				{
					$wpdb->update($wpdb->prefix . "meeting_presentations", array('default_presentation' => 0), array('default_presentation' => 1));
				}
				
				$wpdb->insert($wpdb->prefix . "meeting_presentations", $movefile);
				
				$presentations = $wpdb->get_results("select * from ". $wpdb->prefix . "meeting_presentations");
				
				echo json_encode($presentations);
				
			} else {
				echo $movefile['error'];
			}
		}
		else
		{
			$presentations = $wpdb->get_results("select * from ". $wpdb->prefix . "meeting_presentations");
				
			echo json_encode($presentations);
		}
		
		die(0);
		exit;
	}
	
	function update_user_offline()
	{
		global $wpdb;
		
		$id = $_GET['id'];
		$ree = $wpdb->get_row("select * from ".$wpdb->prefix . "meeting_participants where id = ".$id);
		if($ree->status == 1)
		{
			$wpdb->update($wpdb->prefix . "meeting_participants", 
						array('status' => 0),
						array("id" => $id)
			);
		}
		elseif($ree->status != 0)
		{
			$wpdb->update($wpdb->prefix . "meeting_participants", 
						array('status' => 4),
						array("id" => $id)
			);
		}
		
		if(isset($_GET['meetingroom']))
		{
			$joined_user = $wpdb->get_results("select * from ".$wpdb->prefix . "meeting_participants where (status = 3 or status = 2) and meeting_id=".$_GET['meetingroom']);
			$participants = $wpdb->get_results("select * from ".$wpdb->prefix . "meeting_participants where (status = 0 or status = 1)");
			
			echo json_encode(array('joined_user' => $joined_user, 'participants' => $participants));
		}
		else
		{
			$results = $wpdb->get_results("select * from ".$wpdb->prefix . "meeting_participants where (status = 0 or status = 1)");
		
			echo json_encode($results);
		}
		
		die(0);
		exit;
	}
	
	function check_meeing() {

			global $wpdb;
			
			$meeting_id = $_GET['meeting_id'];
			$participants = $_GET['participants'];
			
			$meeting = $wpdb->get_row("select * from ".$wpdb->prefix . "meeting where id=".$meeting_id);
			
			$results = $wpdb->get_row("select * from ".$wpdb->prefix . "meeting_participants where (status = 2 or status = 3) and meeting_id=".$meeting_id." and id=".$participants);
			
			if(count($results))
			{
				echo json_encode(array("sessionId" => $meeting->session_id, "token" => $meeting->token, "status" => $results->status, "name" => $results->name, "email" => $results->email, "pid" => $participants, "mid" => $meeting_id));
				//setcookie("instant_connect_waiting_id", "", time()-3600, "/");
			}	
			
			die(0);
			exit;
	}
	
	function update_agent_status() {

			global $current_user;
			if(isset($_GET['chatmode']))
				update_user_meta($current_user->ID, 'user_current_status', $_GET['status']);
			else
				update_user_meta($current_user->ID, 'agent_communication_mode', $_GET['status']);

			update_user_meta($current_user->ID, 'user_logintime', date("Y-m-d H:i:s"));
			
			echo 'done';
			die(0);
			exit;
	}
	
	function create_new_meeting()
	{
		global $wpdb, $current_user;
		
		$meetingId = time();
		
		$opentok = opentok_token();
		
		$wpdb->insert($wpdb->prefix . "meeting", array('agent_id' => $current_user->ID, 'meeting_date' => date("Y-m-d H:i:s"), 'created' => date("Y-m-d H:i:s"), 'session_id' => $opentok['sessionId'], 'token' => $opentok['token']));
		$meeting_id = $wpdb->insert_id;
		
		$opentok['id'] = $meeting_id;
		$status = $_GET['st'] ? 3 : 2;
		foreach(json_decode(file_get_contents('php://input'))->data as $d)
		{
			$wpdb->update($wpdb->prefix . "meeting_participants", 
						array(	'meeting_id' => $meeting_id, 
								'meeting_date' => date("Y-m-d H:i:s"),
								'status' => $status
							),
						array("id" => $d)
			);
		}
		
		echo json_encode($opentok);
		
		die(0);
		exit;
	}
	
	function agent_status()
	{
		$general = get_option("general");
		$arr = array(1 => 'Online', 2 => 'Offline', 3 => 'Meeting', 4 => 'Away');
		

		$lst_login_time = get_user_meta($general['agent'], 'user_logintime', true);
		if(strtotime("now") - strtotime($lst_login_time) > 60)
		{
			update_user_meta($general['agent'], 'user_current_status', 2);
		}

		$user_current_status = get_user_meta($general['agent'], 'user_current_status', true);
		echo $arr[$user_current_status];
		die(0);
		exit;
	}

	function agent_mode()
	{
		$general = get_option("general");
		

		$lst_login_time = get_user_meta($general['agent'], 'user_logintime', true);
		if(strtotime("now") - strtotime($lst_login_time) > 60)
		{
			update_user_meta($general['agent'], 'agent_communication_mode', 1);
		}

		$user_current_status = get_user_meta($general['agent'], 'agent_communication_mode', true);
		echo $user_current_status;
		die(0);
		exit;
	}
	
	function waiting_participants()
	{
		global $wpdb;
		
		$results = $wpdb->get_results("select * from ".$wpdb->prefix . "meeting_participants where (status = 0 or status = 1)");
		
		echo json_encode($results);
		die(0);
		exit;
	}
	
	function new_participants()
	{
		global $wpdb;
		
		$results = $wpdb->get_row("select * from ".$wpdb->prefix . "meeting_participants where id = ".$_GET['id']);
		
		echo json_encode($results);
		die(0);
		exit;
	}
	
	function join_chat()
	{
		/*$option = get_option('pusher');
		
		$app_id = $option['id'];
		$app_key = $option['app'];
		$app_secret = $option['secret'];

		$pusher = new Pusher(
		  $app_key,
		  $app_secret,
		  $app_id,
		  array('encrypted' => true)
		);

		$pusher->trigger('test_channel', 'my_event', $_POST);*/
		
		//print_r($_POST['meeting']);

		if(isset($_POST['meeting']))
		{
			$meeting = $_POST['meeting'];
		}
		else
		{
			$meeting = array('name' => $_GET['name'], 'email' => $_GET['email'], 'status' => 1);
		}

		if(isset($_COOKIE['endorsement_track_link']) && isset($_COOKIE['endorsement_tracked']))
		{
			$track_link = explode("#&$#", base64_decode(base64_decode($_COOKIE['endorsement_track_link'])));
			$meeting['endorser'] = $track_link[1];
		}

		global $wpdb;
		
		$wpdb->insert($wpdb->prefix . "meeting_participants", $meeting);
		
		
		setcookie("instant_connect_waiting_id", $wpdb->insert_id, time() + 3600, "/");
		
		if(isset($_GET['meeting']))
			wp_redirect(site_url());
		else
			echo $wpdb->insert_id;

		die(0);
		exit;
	}
}
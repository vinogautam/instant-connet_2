<?php 
class IC_mail_template{
	
	function reset_mail_template ( $mail) {

		$new_value	=	'';

		switch ($mail) {

			case 'welcome_mail':

				return $this->set_welcome_mail ( $new_value,'', true );
				
			case 'notification_mail':

				return $this->set_notification_mail ( $new_value,'', true );

			case 'invitation_mail':

				return $this->set_invitation_mail( $new_value,'', true );
				
			case 'gift_mail':

				return $this->set_gift_mail ( $new_value,'', true );
				
			case 'regift_mail':

				return $this->set_regift_mail ( $new_value,'', true );
				
			case 'manualgift_mail':

				return $this->set_manualgift_mail ( $new_value,'', true );
			
			case 'endorser_invitation_mail':

				return $this->set_endorser_invitation_mail ( $new_value,'', true );
			
			default:

				return 1;

		}

	}

	public function get_endorser_invitation_mail ( ) {

		$default	=	__("<p>Hello [ENDORSER],</p><p> Invitation to join &nbsp;&nbsp;[SITE], cick auto link and join as endorser.<br> [AUTO_LOGIN_LINK]</p>
							<p>Thank you and welcome to [SITE].</p>
							<p>Referred Agent [AGENT]<p>
							<p>[AGENT_EMAIL]<p>", '');
		
		$content = get_option('endorser_invitation_mail');
		
		$subject = get_option('endorser_invitation_mail_subject');
		
		if($content)
		$return = array('content' => $content, 'subject' => $subject);
		else
		$return = array('content' => $default, 'subject' => 'Welcome Email');
		
		return $return;
	}

	

	public function set_endorser_invitation_mail ( $new_value, $subject , $default ) {

		if($default) {

			$new_value	=	__("<p>Hello [ENDORSER],</p><p> Invitation to join &nbsp;&nbsp;[SITE], cick auto link and join as endorser.<br> [AUTO_LOGIN_LINK]</p>
							<p>Thank you and welcome to [SITE].</p>
							<p>Referred Agent [AGENT]<p>
							<p>[AGENT_EMAIL]<p>", '');
							
			$subject = 'Welcome Email';

		}

		update_option('endorser_invitation_mail', $new_value);
		
		update_option('endorser_invitation_mail_subject', $subject);

		return array('content' => $new_value, 'subject' => $subject);

	}

	public function get_welcome_mail ( ) {

		$default	=	__("<p>Hello [ENDORSER],</p><p>You have successfully joined with&nbsp;&nbsp;[SITE]. [AUTO_LOGIN_LINK]</p>
							<p>Thank you and welcome to [SITE].</p>
							<p>Referred Agent [AGENT]<p>
							<p>[AGENT_EMAIL]<p>", '');
		
		$content = get_option('welcome_mail');
		
		$subject = get_option('welcome_mail_subject');
		
		if($content)
		$return = array('content' => $content, 'subject' => $subject);
		else
		$return = array('content' => $default, 'subject' => 'Welcome Email');
		
		return $return;
	}

	

	public function set_welcome_mail ( $new_value, $subject , $default ) {

		if($default) {

			$new_value	=	__("<p>Hello [ENDORSER],</p><p>You have successfully joined with&nbsp;&nbsp;[SITE]. [AUTO_LOGIN_LINK]</p>
							<p>Thank you and welcome to [SITE].</p>
							<p>Referred Agent [AGENT]<p>
							<p>[AGENT_EMAIL]<p>", '');
							
			$subject = 'Welcome Email';

		}

		update_option('welcome_mail', $new_value);
		
		update_option('welcome_mail_subject', $subject);

		return array('content' => $new_value, 'subject' => $subject);

	}

	public function get_notification_mail ( ) {

		$default	=	__("<p>Hello admin,</p><p>New endorser joined in our website.
		Referred Agent: [AGENT]<br>
		Referred Email: [AGENT_EMAIL]</p>
<p>Thank you and welcome to [blogname].</p>", '');
		
		$content = get_option('notification_mail');
		
		$subject = get_option('notification_mail_subject');
		
		if($content)
		$return = array('content' => $content, 'subject' => $subject);
		else
		$return = array('content' => $default, 'subject' => 'New Endorser Joined');
		
		return $return;
	}

	

	public function set_notification_mail ( $new_value, $subject , $default ) {

		if($default) {

			$new_value	=	__("<p>Hello admin,</p><p>New endorser joined in our website.
		Referred Agent: [AGENT]<br>
		Referred Email: [AGENT_EMAIL]</p>
<p>Thank you and welcome to [blogname].</p>", '');
			
			$subject = 'New Endorser Joined';
		}

		update_option('notification_mail', $new_value);
		
		update_option('notification_mail_subject', $subject);

		return array('content' => $new_value, 'subject' => $subject);

	}

	public function get_invitation_mail ( ) {


		$default	=	__("Hi [ENDORSEMENT], I am [ENDORSER], I want to introduce you to Terry Thomas.
I started working with Terry because I was struggling with my financial plan for quite some time.
I’m happy I now have my financial plan in place.
I thought of you, because we had spoken briefly about the need for a financial plan, so I asked
Terry about meeting with you. Terry provides free online consultations if you’re interested. He
won’t pressure you, and the consultation is without obligation. Online means he doesn’t need to
come to your home and you don’t need to go to him.
Terry is an expert at helping people resolve their financial planning needs. He’s been studying
and mentoring people for 20+ years, but most of all Terry enjoys helping people meet their
financial planning needs. He is the real deal.
To learn more about Terry, or register for a free consultation, click here ___________ [TRACK_LINK]. His number is 604-288-1420. I recommend you register, or give him a call right away,
because his schedule fills up fast.
Let me know if you have any questions,", '');

		$content = get_option('invitation_mail');
		
		$subject = get_option('invitation_mail_subject');
		
		if($content)
		$return = array('content' => $content, 'subject' => $subject);
		else
		$return = array('content' => $default, 'subject' => 'You are invited as Endorsement');
		
		return $return;

	}

	

	public function set_invitation_mail ( $new_value, $subject, $default ) {

		if($default) {

			$new_value	=	__("Hi [ENDORSEMENT], I am [ENDORSER], I want to introduce you to Terry Thomas.
I started working with Terry because I was struggling with my financial plan for quite some time.
I’m happy I now have my financial plan in place.
I thought of you, because we had spoken briefly about the need for a financial plan, so I asked
Terry about meeting with you. Terry provides free online consultations if you’re interested. He
won’t pressure you, and the consultation is without obligation. Online means he doesn’t need to
come to your home and you don’t need to go to him.
Terry is an expert at helping people resolve their financial planning needs. He’s been studying
and mentoring people for 20+ years, but most of all Terry enjoys helping people meet their
financial planning needs. He is the real deal.
To learn more about Terry, or register for a free consultation, click here ___________ [TRACK_LINK]. His number is 604-288-1420. I recommend you register, or give him a call right away,
because his schedule fills up fast.
Let me know if you have any questions,", '');
		
			$subject = 'You are invited as Endorsement';
		}

		update_option('invitation_mail', $new_value);
		
		update_option('invitation_mail_subject', $subject);

		return array('content' => $new_value, 'subject' => $subject);

	}
	
	public function get_gift_mail ( ) {


		$default	=	__("Hi [ENDORSER], Your covertion process is intiated. Please click below link and select your vendor. 
		Then you will get your gift voucher. [SELECT_VENDOR_LINK].", '');

		$content = get_option('gift_mail');
		
		$subject = get_option('gift_mail_subject');
		
		if($content)
		$return = array('content' => $content, 'subject' => $subject);
		else
		$return = array('content' => $default, 'subject' => 'Gift coversion Initiated');
		
		return $return;

	}

	

	public function set_gift_mail ( $new_value, $subject, $default ) {

		if($default) {

			$new_value	=	__("Hi [ENDORSER], Your covertion process is intiated. Please click below link and select your vendor. 
			Then you will get your gift voucher. [SELECT_VENDOR_LINK].", '');
		
			$subject = 'Gift coversion Initiated';
		}

		update_option('gift_mail', $new_value);
		
		update_option('gift_mail_subject', $subject);

		return array('content' => $new_value, 'subject' => $subject);

	}
	
	public function get_regift_mail ( ) {


		$default	=	__("Hi [ENDORSER], Your agent resend gift for your converted endorsement. Please click below link and select your vendor. 
			Then you will get your gift voucher. [SELECT_VENDOR_LINK].", '');

		$content = get_option('regift_mail');
		
		$subject = get_option('regift_mail_subject');
		
		if($content)
		$return = array('content' => $content, 'subject' => $subject);
		else
		$return = array('content' => $default, 'subject' => 'Get Bonus Gift');
		
		return $return;

	}

	

	public function set_regift_mail ( $new_value, $subject, $default ) {

		if($default) {

			$new_value	=	__("Hi [ENDORSER], Your agent resend gift for your converted endorsement. Please click below link and select your vendor. 
			Then you will get your gift voucher. [SELECT_VENDOR_LINK].", '');
		
			$subject = 'Get Bonus Gift';
		}

		update_option('regift_mail', $new_value);
		
		update_option('regift_mail_subject', $subject);

		return array('content' => $new_value, 'subject' => $subject);

	}
	
	public function get_manualgift_mail ( ) {


		$default	=	__("Hi [ENDORSER], Your agent resend gift for your converted endorsement. Please click below link and select your vendor. 
			Then you will get your gift voucher. [SELECT_VENDOR_LINK].", '');

		$content = get_option('manualgift_mail');
		
		$subject = get_option('manualgift_mail_subject');
		
		if($content)
		$return = array('content' => $content, 'subject' => $subject);
		else
		$return = array('content' => $default, 'subject' => 'Get Bonus Gift');
		
		return $return;

	}

	

	public function set_manualgift_mail ( $new_value, $subject, $default ) {

		if($default) {

			$new_value	=	__("Hi [ENDORSER], Your agent resend gift for your converted endorsement. Please click below link and select your vendor. 
			Then you will get your gift voucher. [SELECT_VENDOR_LINK].", '');
		
			$subject = 'Get Bonus Gift';
		}

		update_option('manualgift_mail', $new_value);
		
		update_option('manualgift_mail_subject', $subject);

		return array('content' => $new_value, 'subject' => $subject);

	}
	
	public function get_mail_template($content){
		
		$value = '<table cellpadding="5" width="750" cellspacing="0" border="0" style="background-color:#FFF;">
				<td align="left" colspan="4" style="margin-right:30px; padding-left:10px;">'.$content.'</td>
				</tr>
                <tr><td colspan="4" align="center" style="background-color:#EAEAEA; border-top: solid 3px #1E1F22; padding-top:10px; color:#000;">Poweredby Financial Insiders</td></tr>
</table>';

		return $value;
	}
	
	
	public function send_welcome_mail($email, $user_id, $autologin, $data = array()){
					
		global $current_user, $wpdb;
		
		$user_info = get_userdata($user_id);
      	
		$username = $user_info->user_login;
		
		$data = count($data) ? $data : $this->get_welcome_mail();
		
		//print_r(get_permalink(get_option('ENDORSEMENT_FRONT_END')).'?autologin='.base64_encode(base64_encode($autologin)));
		$endorser_letter = get_user_meta($user_id, 'endorser_letter', true);
		if($endorser_letter)
		{
			$res = $wpdb->get_row("select * from ".$wpdb->prefix . "mailtemplates where id=".$endorser_letter);
			$subject = isset($res->subject) ? $res->subject : $data['subject'];
			$content = isset($res->content) ? $res->content : $data['content'];
		}
		else
		{
			$subject = $data['subject'];
			$content = $data['content'];
		}
		
		$content 	=	str_ireplace('[ENDORSER]', get_user_meta($user_id, 'first_name', true).' '.get_user_meta($user_id, 'last_name', true), $content);
		$content 	=	str_ireplace('[AUTO_LOGIN_LINK]', get_permalink(get_option('ENDORSEMENT_FRONT_END')).'?autologin='.base64_encode(base64_encode($autologin)), $content);
		$content 	=	str_ireplace('[AGENT]', $current_user->user_login, $content);
		$content 	=	str_ireplace('[AGENT_EMAIL]', $current_user->user_email, $content);				
		$content	= 	str_ireplace('[SITE]', get_option('blogname'), $content);
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
				
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				
		$headers .= "From: ".get_option('blogname')." < ".get_option('admin_email')."> \r\n";
				
		$message	=	$this->get_mail_template($content);
					
		if($this->mandrip_mail($email, $subject , $message, $headers))
		return true;
		else
		return false;
	}
	
	public function send_notification_mail($user_id){
					
		global $current_user;
		
		$user_info = get_userdata($user_id);
		
		$data = $this->get_notification_mail();
		
		$subject = $data['subject'];
		
		$content = $data['content'];
		
		$content 	=	str_ireplace('[ENDORSER]', get_user_meta($user_id, 'first_name', true).' '.get_user_meta($user_id, 'last_name', true), $content);
		$content 	=	str_ireplace('[ENDORSER_EMAIL]', $user_info->user_email, $content);
		$content 	=	str_ireplace('[AGENT]', $current_user->user_login, $content);
		$content 	=	str_ireplace('[AGENT_EMAIL]', $current_user->user_email, $content);				
		$content	= 	str_ireplace('[SITE]', get_option('blogname'), $content);
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
				
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				
		$headers .= "From: ".get_option('blogname')." < ".get_option('admin_email')."> \r\n";
				
		$message	=	$this->get_mail_template($content);
					
		if($this->mandrip_mail(get_option('admin_email'), $subject, $message, $headers))
		return true;
		else
		return false;
		
	}
	
	public function send_invitation_mail($info, $endorser, $id, $content){
					
		global $current_user, $wpdb;
		
		$data = $this->get_invitation_mail();
		
		$endorser_letter = get_user_meta($endorser, 'endorsement_letter', true);
		if($endorser_letter)
		{
			$res = $wpdb->get_row("select * from ".$wpdb->prefix . "mailtemplates where id=".$endorser_letter);
			$subject = isset($res->subject) ? $res->subject : $data['subject'];
			//$content = isset($res->content) ? $res->content : $data['content'];
			$pagelink = isset($res->page) ? $res->page : get_option('ENDORSEMENT_FRONT_END');
		}
		else
		{
			$subject = $data['subject'];
			//$content = $data['content'];
			$pagelink = get_option('ENDORSEMENT_FRONT_END');
		}
		
		$content 	=	str_ireplace('[ENDORSER]', get_user_meta($endorser, 'first_name', true).' '.get_user_meta($endorser, 'last_name', true), $content);
		$content 	=	str_ireplace('[ENDORSEMENT]', $info['name'], $content);
		$content 	=	str_ireplace('[TRACK_LINK]', get_permalink($pagelink).'?ref='.base64_encode(base64_encode($id.'#&$#'.$endorser.'#&$#'.$info['tracker_id'])), $content);
		$content	= 	str_ireplace('[SITE]', get_option('blogname'), $content);
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
				
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				
		$headers .= "From: ".get_option('blogname')." < ".get_option('admin_email')."> \r\n";
				
		$message	=	$this->get_mail_template($content);
		
		$arr = array('name' => get_user_meta($endorser, 'first_name', true).' '.get_user_meta($endorser, 'last_name', true),
					'email' => get_userdata($endorser)->user_email);
		
		if($this->mandrip_mail($info['email'], $subject , $message, $headers, $arr))
		return true;
		else
		return false;
	}
	
	public function send_gift_mail($template, $user_id, $gift){
					
		global $current_user, $wpdb;
		
		$user_info = get_userdata($user_id);
      	
		$username = $user_info->user_login;
		
		$data = $this->$template();
		
		$subject = $data['subject'];
		$content = $data['content'];
		
		$content 	=	str_ireplace('[ENDORSER]', get_user_meta($user_id, 'first_name', true).' '.get_user_meta($user_id, 'last_name', true), $content);
		$content 	=	str_ireplace('[SELECT_VENDOR_LINK]', get_permalink(get_option('ENDORSEMENT_FRONT_END')).'?gift='.base64_encode(base64_encode($gift)), $content);
		$content 	=	str_ireplace('[AGENT]', $current_user->user_login, $content);
		$content 	=	str_ireplace('[AGENT_EMAIL]', $current_user->user_email, $content);				
		$content	= 	str_ireplace('[SITE]', get_option('blogname'), $content);
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
				
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
				
		$headers .= "From: ".get_option('blogname')." < ".get_option('admin_email')."> \r\n";
				
		$message	=	$this->get_mail_template($content);
					
		if($this->mandrip_mail($user_info->user_email, $subject , $message, $headers))
		return true;
		else
		return false;
	}
	
	function mandrip_mail($to, $subject , $message, $headers, $arr=array())
	{
		
		//global $ntm_mail;
		//return $ntm_mail::send_mail($to, $subject , $message, '', '', $arr);

		$option = get_option('sendgrid');
		
		$sendgrid = new SendGrid($option['api']);
		$email = new SendGrid\Email();


		if(count($arr))
		{
			$email->setFrom($arr['email']);
			$email->setFromName($arr['name']);
		}
		else
		{
			$email->setFrom('neil@financialinsiders.ca');
			$email->setFromName('FinancialInsiders.ca');
		}
		
		$email->addTo($to);
		$email->setHtml($message);

		                             

		$email->setSubject($subject);
		//$email->Body    = $message;

		try {
			//$sendgrid->send($email);
		} catch(\SendGrid\Exception $e) {
			echo $e->getCode();
			foreach($e->getErrors() as $er) {
				echo $er;
			}
		}

		//if(!$mail->Send()) {
		   /* echo 'Message could not be sent.';
		   echo 'Mailer Error: ' . $mail->ErrorInfo;
		   exit; */
		  // return false;
		//}

		//echo 'Message has been sent';
		return true;
	}
	
}
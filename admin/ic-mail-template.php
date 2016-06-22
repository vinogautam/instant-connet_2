<?php 
function mail_template()
{	
	global $ntm_mail;
	
	$templates = array (

			'welcome_mail' 		=>	 "New Endorser Welcome Email template",
			
			'notification_mail' 		=>	 "New Endorser Notification to Admin Email template",

			'invitation_mail'		=> 	 "Endorsement Invitation Email template"

		);
	
	$key = array_keys($templates);
	$value = array_values($templates);
	
	if(isset($_POST['update_mail_template']))
	{
		$ntm_mail->set_welcome_mail ($_POST['welcome_mail'],$_POST['welcome_mail_subject'],false);
		
		$ntm_mail->set_notification_mail ($_POST['notification_mail'],$_POST['notification_mail_subject'],false);
		
		$ntm_mail->set_invitation_mail ($_POST['invitation_mail'],$_POST['invitation_mail_subject'],false);
		
	}
	elseif(isset($_GET['reset']))
	{
		$ntm_mail->reset_mail_template ($_GET['reset']);
	}
	
		$get_mail = array();
		
		$get_mail[] 	 	= 	$ntm_mail->set_welcome_mail ();
		
		$get_mail[] 	 	= 	$ntm_mail->set_notification_mail ();
		
		$get_mail[] 	 	= 	$ntm_mail->set_invitation_mail ();
		
	?>
    <link rel="stylesheet" type="text/css" href="<?php _e(CSS);?>ckeditor.css" media="all" />
    <script type='text/javascript' src='<?php _e(JS);?>ckeditor/ckeditor.js'></script>
    
    <div id="poststuff" class="wrap">
    <h2>Mail template</h2>
    <?php if(isset($message)){?>
    <div id="message" class="updated"><p><?php echo $message;?></p></div>
    <?php }?>
		<div class="postbox">
            <div class="inside group">
            	<form name="myform" method="post" >
                <table id="country" class="form-table">
                    <?php for($i=0;$i<count($templates);$i++){?>
                    <tr>
                        <td colspan="2" style="border-top: 1px #ddd solid; background: #eee"><strong><?php _e($value[$i]);?></strong><small><a href="edit.php?post_type=master&page=mail_template&reset=jp_<?php _e($key[$i]);?>">Reset</a></small></td>
                    </tr>
                    <tr>
                        <th><label>Subject</label></th>
                        <th><input size="100" name="<?php _e($key[$i]._subject);?>" value="<?php _e($get_mail[$i]['subject']);?>" type="text" /></th>
                    </tr>
                    <tr>
                        <th><label>Content</label></th>
                        <th><textarea cols="80" id="editor<?php _e($i);?>" name="<?php _e($key[$i]);?>" rows="10"><?php _e($get_mail[$i]['content']);?></textarea></th>
                    </tr>
                    <?php }?>
				</table>
                <script>
					<?php for($i=0;$i<count($templates);$i++){?>
					CKEDITOR.replace( 'editor<?php _e($i);?>' );
					<?php }?>
				</script>
                <p class="submit">
                	<input type="hidden" name="role" value="job-seeker">
                    <input name="update_mail_template" class="button-primary seeker_btn" value="<?php _e('Save Changes'); ?>" type="submit" />
                </p>
                </form>
            </div>
        </div>
    </div>    
		
<?php }

<?php
class IC_front{
	
	function __construct() {
		add_action( 'wp_footer', array( &$this, 'instant_connect_chat_icon'), 100 );
	}
	
	function instant_connect_chat_icon()
	{
		global $wpdb;
		$option = get_option('chat_position');
		$general = get_option('general');
		
		$is_waiting = isset($_COOKIE['instant_connect_waiting_id']) ? $_COOKIE['instant_connect_waiting_id'] : 0;
		
		if($is_waiting)
		{
			$wuser = $wpdb->get_row("select * from ".$wpdb->prefix . "meeting_participants where (status = 1 or status = 2) and id = ".$is_waiting);
			if(!count($wuser))
			{
				$is_waiting = 0;
				setcookie("instant_connect_waiting_id", "", time()-3600, "/");
			}
		}
		
		
					$user_info = get_userdata($general['agent']);
					$arr = array(1 => 'Online', 2 => 'Offline', 3 => 'Meeting', 4 => 'Away');
					$user_current_status = get_user_meta($general['agent'], 'user_current_status', true);
		?>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular.min.js" type="text/javascript" charset="utf-8"></script>
		<script src='https://cdn.firebase.com/js/client/2.2.1/firebase.js'></script>
		<style>
		<?php if($option == 1){?>
		.chat_icon{width:70px; height:70px; border-radius:50%; background:#5C93C1; text-align:center; position:fixed; top:30px; left:30px; cursor:pointer;}
		.chat_icon i{font-size:35px; color:#fff; display:inline-block; padding:15px;}
		.instant_connect_form{transition:all 350ms ease 0s; left:-400px; position:fixed; background:#DBDBDB; top:120px; width:300px; padding:20px;}
		.instant_connect_form.join_chat{left:40px; }
		.instant_connect_form label, .instant_connect_form input{display:block; width:100%;}
		<?php }elseif($option == 2){?>
		.chat_icon{width:70px; height:70px; border-radius:50%; background:#5C93C1; text-align:center; position:fixed; right:30px; top:30px; cursor:pointer;}
		.chat_icon i{font-size:35px; color:#fff; display:inline-block; padding:15px;}
		.instant_connect_form{transition:all 350ms ease 0s; right:-400px; position:fixed; background:#DBDBDB; top:120px; width:300px; padding:20px;}
		.instant_connect_form.join_chat{right:40px; }
		.instant_connect_form label, .instant_connect_form input{display:block; width:100%;}
		<?php }elseif($option == 3){?>
		.chat_icon{width:70px; height:70px; border-radius:50%; background:#5C93C1; text-align:center; position:fixed; left:30px; bottom:30px; cursor:pointer;}
		.chat_icon i{font-size:35px; color:#fff; display:inline-block; padding:15px;}
		.instant_connect_form{transition:all 350ms ease 0s; left:-400px; position:fixed; background:#DBDBDB; bottom:120px; width:300px; padding:20px;}
		.instant_connect_form.join_chat{left:40px; }
		.instant_connect_form label, .instant_connect_form input{display:block; width:100%;}
		<?php }elseif($option == 4){?>
		.chat_icon{width:70px; height:70px; border-radius:50%; background:#5C93C1; text-align:center; position:fixed; right:30px; bottom:30px; cursor:pointer;}
		.chat_icon i{font-size:35px; color:#fff; display:inline-block; padding:15px;}
		.instant_connect_form{transition:all 350ms ease 0s; right:-400px; position:fixed; background:#DBDBDB; bottom:120px; width:300px; padding:20px;}
		.instant_connect_form.join_chat{right:40px; }
		.instant_connect_form label, .instant_connect_form input{display:block; width:100%;}
		<?php }else{?>
		.chat_icon{width:70px; height:70px; border-radius:50%; background:#5C93C1; text-align:center; position:fixed; right:30px; bottom:30px; cursor:pointer;}
		.chat_icon i{font-size:35px; color:#fff; display:inline-block; padding:15px;}
		.instant_connect_form{transition:all 350ms ease 0s; right:-400px; position:fixed; background:#DBDBDB; bottom:120px; width:300px; padding:20px;}
		.instant_connect_form.join_chat{right:40px; }
		.instant_connect_form label, .instant_connect_form input{display:block; width:100%;}
		<?php }?>
		.submit_btn{position:relative;}
		.submit_btn img{display:none; position:absolute; top:13px;}
		.instant_connect_form.submitted{opacity:0.7; cursor_pointer:none;}
		.instant_connect_form.submitted .submit_btn img{display:inline-block}
		</style>
		<div class="chat_icon hide_when_start">
			<i class="fa fa-comments"></i>
		</div>
		<div class="instant_connect_form hide_when_start" >
			<form  id="instant_connect_form" onSubmit="return false;">
				<?php if($is_waiting == 0){?>
				<p>
					<label>Name</label>
					<input type="text" name="meeting[name]" id="nameInput">
				</p>
				<?php if($user_current_status != 1){?>
				<p>
					<label>Email</label>
					<input type="text" name="meeting[email]" id="emailInput">
				</p>
				<?php }?>
				<input type="hidden" name="action" value="join_chat">
				<input type="hidden" name="meeting[status]" value="1">
				<div class="submit_btn">
					<input type="submit" id="instant_connect_formsubmit" name="Submit" value="Join Live Chat">
					<img src="<?= IC_PLUGIN_URL; ?>294.gif">
				</div>
				<?php }else{?>
					<div style="display: inline-block; overflow: hidden; border-radius: 50%; border: 5px solid rgb(204, 204, 204); width: 150px; height: 150px;">
						<?php echo get_avatar( $user_info->user_email, 150 ); ?>
					</div>
					<div id="agent_status"><?php _e($arr[$user_current_status]); ?></div>
					<br>
					<p>Hello <?php echo $wuser->name;?>, Please wait for a meeting.</p>
				<?php }?>
			</form>
			
		</div>
		<div ng-app="demo" ng-controller="ActionController" class="text_chat_container" style="display:none;position: absolute; top: 30px; right:0;width: 300px; background: rgb(255, 255, 255) none repeat scroll 0% 0%;border:1px solid #000;">
						<iframe ng-src="{{'<?= str_replace("http://financialinsiders.ca/", "https://financialinsiders.ca/", site_url()); ?>/meeting/?id='+meeting.mid+'&only_video'}}"></iframe>
						<div id="messagesDiv" style="height:250px;overflow:auto;">
							<p ng-repeat="c in chat track by $index" on-finish-render ng-class="{align_right: c.email != data.email}" ng-if="c.msg">
								<img ng-if="c.email == data.email" ng-src="http://www.gravatar.com/avatar/{{c.hash}}/?s=30"> 
								{{c.msg}}
								<img ng-if="c.email != data.email" ng-src="http://www.gravatar.com/avatar/{{c.hash}}/?s=30"> 
								<hr>
							</p>
						</div>
						<p ng-show="noti">{{noti.name}} is typing...<p>
						<form>
							<input size="43" type="hidden" ng-model="data.name" placeholder="Name">
							<input size="43" type="hidden" ng-model="data.email" placeholder="Email">
							<textarea rows="2" cols="33" ng-model="data.msg" ng-keyup="send_noti()" placeholder="Message" ng-enter="add();"></textarea>
							<div style="text-align:center;">
								<button ng-click="add();">Post</button>
							</div>
						</form>
			</div>
			
		<script src='https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/core.js'></script>
		<script src='https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/md5-min.js'></script>
		<script>
			
			
			var count = 0,textchatref,scope;
			jQuery(document).ready(function(){
				
					<?php if(isset($wuser) && $wuser->status==2){?>
					jQuery.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=check_meeing&participants=<?php echo $is_waiting;?>&meeting_id=<?= $wuser->meeting_id?>', function(res){
								if(!res) return;
								res = JSON.parse(res);
								console.log(res);
								if(res.status == 3)
									window.location.assign("<?= wp_nonce_url(str_replace("http://financialinsiders.ca", "https://financialinsiders.ca", site_url("/meeting/")),'finonce','finonce');?>&id="+res.mid+"&pid="+res.pid);
								else if(res.status == 2)
								{	
									jQuery(".text_chat_container").show();
									jQuery(".hide_when_start").hide();
									scope = angular.element(jQuery(".text_chat_container")).scope();
									scope.$apply(function(){
										scope.start_chating(res);
									});
									
								}
							});
					<?php }?>

					jQuery(".chat_icon").click(function(){
						jQuery(".instant_connect_form").toggleClass("join_chat");
					});
					
					//http://45.58.38.227/bigbluebutton/api/join?meetingID=Demo+Meeting&fullName=&password=mp&checksum=54dd35ccbc2e1377ab5667ae7d84d6ed815af332
					
					<?php if($is_waiting){?>
					
					var myDataRef = new Firebase('https://vinogautam.firebaseio.com/pusher/new_user');
					myDataRef.update({ count:<?php echo $is_waiting;?>});
					
					var statusRef = new Firebase('https://vinogautam.firebaseio.com/pusher/status_change');
					statusRef.on('value', function(snapshot) {
						jQuery.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=get_status', function(res){
							jQuery("#agent_status").text(res);
							jQuery(".instant_connect_form").addClass("join_chat");
						});
					});
					jQuery(".instant_connect_form").addClass("join_chat");
					
					var meetingRef = new Firebase('https://vinogautam.firebaseio.com/pusher/new_meeting');
					var meetingstatus = 0;
					meetingRef.on('value', function(snapshot) {
						mid = snapshot.val().id;
						if(typeof mid != "number")
							mid = mid.split("-")[0];
						
						meetingstatus++;
						if(meetingstatus != 1)
						{
							jQuery.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=check_meeing&participants=<?php echo $is_waiting;?>&meeting_id='+mid, function(res){
								if(!res) return;
								res = JSON.parse(res);
								console.log(res);
								if(res.status == 3)
									window.location.assign("<?= wp_nonce_url(str_replace("http://financialinsiders.ca", "https://financialinsiders.ca", site_url("/meeting/")),'finonce','finonce');?>&id="+res.mid+"&pid="+res.pid);
								else if(res.status == 2)
								{	
									jQuery(".text_chat_container").show();
									jQuery(".hide_when_start").hide();
									scope = angular.element(jQuery(".text_chat_container")).scope();
									scope.$apply(function(){
										scope.start_chating(res);
									});
									
								}
							});
						}
						
					});
					
					var online_status = new Firebase('https://vinogautam.firebaseio.com/pusher/online_status');
					cccnt = 1;
					setInterval(function(){
						online_status.update({ count:"<?php echo $is_waiting;?>-"+cccnt++});
					}, 5000);
					
					<?php }else{?>
					
					//var myDataRef = new Firebase('https://vinogautam.firebaseio.com/pusher/new_user');
					jQuery("#instant_connect_formsubmit").click(function(){
						jQuery(".instant_connect_form").addClass("submitted");
						jQuery.post('<?php echo site_url();?>/wp-admin/admin-ajax.php',jQuery("#instant_connect_form").serialize(), function(res){
							//var name = jQuery('#nameInput').val();
							//var email = jQuery('#emailInput').val();
							//myDataRef.push({name: name, email: email});
							console.log(res);
							//myDataRef.update({ count:res});
							setTimeout(function(){location.reload();}, 3000);
						});
					});
					
					<?php }?>
			});
			
			angular.module('demo', [])
			.directive('ngEnter', function() {
			return function(scope, element, attrs) {
				element.bind("keydown keypress", function(event) {
					if(event.which === 13) {
							scope.$apply(function(){
									scope.$eval(attrs.ngEnter);
							});
							
							event.preventDefault();
					}
				});
			};
		})
		.directive('onFinishRender', function ($timeout) {
    return {
        restrict: 'A',
        link: function (scope, element, attr) {
            if (scope.$last === true) {
                $timeout(function () {
                    //scope.$emit(attr.onFinishRender);
					jQuery("#messagesDiv").scrollTop(jQuery("#messagesDiv")[0].scrollHeight);
                });
            }
        }
    }
})
			.controller('ActionController', ['$scope', '$timeout', '$http', function($scope, token, $timeout, $http) {
					
					$scope.chat = [];
					$scope.data = {name:"", email:"", msg:""};
					$scope.meeting = {};
					$scope.start_chating = function(res){
						console.log("start cghat");
						textchatref = new Firebase('https://vinogautam.firebaseio.com/opentok/'+res.sessionId);
							$scope.data.name = res.name;
							$scope.data.email = res.email;
							$scope.meeting = res;
								textchatref.on('child_added', function(snapshot) {
									//angular.forEach(snapshot.val(), function(v,k){
										v = snapshot.val();
										if(v.noti === undefined)
										{
											hn = v.email ? v.email : v.name;
											v.hash = CryptoJS.MD5(hn).toString();
											if(!$scope.$$phase) {
											$scope.$apply(function(){
												$scope.chat.push(v);
											});
											}
											else
											{
												$scope.chat.push(v);
											}
										}
										else if(v.noti.indexOf("switchtomeeting") != -1 && v.noti.split("switchtomeeting_")[1] == $scope.meeting.pid)
										{
											window.location.assign("<?= wp_nonce_url(str_replace("http://financialinsiders.ca", "https://financialinsiders.ca", site_url("/meeting/")),'finonce','finonce');?>&id="+$scope.meeting.mid+"&pid="+$scope.meeting.pid);
										}
									//});
								});
					};
					
					$scope.add = function(){
						console.log($scope.data);
						textchatref.push($scope.data);
						$scope.data.msg = '';
					};
			}]);
		</script>
		<?php
	}
}
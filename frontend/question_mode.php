<?php
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
		$com_mode = get_user_meta($general['agent'], 'agent_communication_mode', true);
		?>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular.min.js" type="text/javascript" charset="utf-8"></script>
		<script src='https://cdn.firebase.com/js/client/2.2.1/firebase.js'></script>
		<script type="text/javascript" src="//wurfl.io/wurfl.js"></script>
		<script src='https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/core.js'></script>
		<script src='https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/md5-min.js'></script>
		<style>
		.instant_connect_form{transition:all 350ms ease 0s; right:100px; position:fixed; background:#DBDBDB; bottom:120px; width:300px; padding:20px;}
		.instant_connect_form.join_chat{right:40px; }
		.instant_connect_form label, .instant_connect_form input{display:block; width:100%;}
		.submit_btn{position:relative;}
		.submit_btn img{display:none; position:absolute; top:13px;}
		.instant_connect_form.submitted{opacity:0.7; cursor_pointer:none;}
		.instant_connect_form.submitted .submit_btn img{display:inline-block}
		</style>
		<div ng-app="demo" ng-controller="ActionController" class="instant_connect_form hide_when_start" >
			<form  id="instant_connect_form" onSubmit="return false;">
				<div id="question_mode" <?= $com_mode != 1 ? 'style="display:none;"' : '';?>>
					<div style="display: inline-block; overflow: hidden; border-radius: 50%; border: 5px solid rgb(204, 204, 204); width: 50px; height: 50px;border-radius: 50%;">
						<?php echo get_avatar( $user_info->user_email, 50 ); ?>
					</div>
					<p style="font-size: 14px;"><?= $user_info->username; ?>(Agent) is available to take your questions please put your name and email.</p>
					
				</div>
				<div ng-if="chat.length" id="messagesDiv" style="height:250px;overflow:auto;">
					<p ng-repeat="c in chat track by $index" on-finish-render ng-class="{align_right: c.email != data.email}" ng-if="c.msg">
						<img ng-if="c.email == data.email" ng-src="http://www.gravatar.com/avatar/{{c.hash}}/?s=30"> 
						{{c.msg}}
						<img ng-if="c.email != data.email" ng-src="http://www.gravatar.com/avatar/{{c.hash}}/?s=30"> 
						<hr>
					</p>
				</div>
				
				<div ng-hide="getinput || chat.length == 1" class="submit_btn">
					<p>
						<textarea placeholder="Question" ng-model="data.msg"></textarea>
					</p>
					<input type="submit" ng-click="add();" name="Submit" value="Submit">
					<img src="<?= IC_PLUGIN_URL; ?>294.gif">
				</div>
				<div ng-show="getinput" class="submit_btn">
					<p>
						<input placeholder="Name" ng-model="data.name">
					</p>
					<p>
						<input placeholder="Email" ng-model="data.email">
					</p>
					<input type="submit" ng-click="update_user();" name="Submit" value="Submit">
					<img src="<?= IC_PLUGIN_URL; ?>294.gif">
				</div>

			</form>
			
		</div>

		<script type="text/javascript">
			var textchatref;

			angular.module('demo', []).controller('ActionController', ['$scope', '$timeout', '$http', function($scope, $timeout, $http) {
					
					$scope.chat = [];
					$scope.data = {name:"", email:"", msg:""};
					$scope.meeting = {};

					$scope.getinput = false;

					$scope.start_chating = function(res){


						textchatref = new Firebase('https://vinogautam.firebaseio.com/pusher/individual_chat/'+res+'/');
						textchatref.push($scope.data);
						$scope.data.msg = '';
						textchatref.on('child_added', function(snapshot) {
							v = snapshot.val();
							console.log(v);
							if(typeof v.msg != "undefined")
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
						});
					};
					
					$scope.participant = 0;
					$scope.video_container = false;
					$scope.add = function(){
						if($scope.chat.length)
						{
							textchatref.push($scope.data);
							$scope.data.msg = '';
						}
						else
						{
							data = {action:'join_chat', meeting: {mode:1, is_mobile: WURFL.is_mobile, question: $scope.data.msg, complete_device_name: WURFL.complete_device_name, form_factor:WURFL.form_factor, status: 1}};
							jQuery.post('<?php echo site_url();?>/wp-admin/admin-ajax.php', data, function(res){
									$scope.start_chating(res);
									$scope.participant = res;
									$timeout(function(){
										if($scope.chat.length == 1)
										$scope.getinput = true;
									}, 600);

									var online_status = new Firebase('https://vinogautam.firebaseio.com/pusher/online_status');
									cccnt = 1;
									setInterval(function(){
										online_status.update({ count:res+"-"+cccnt++});
									}, 5000);
									var myDataRef = new Firebase('https://vinogautam.firebaseio.com/pusher/new_user');
									myDataRef.update({ count:res});


									var meetingRef = new Firebase('https://vinogautam.firebaseio.com/pusher/new_meeting');
									var meetingstatus = 0;
									meetingRef.on('value', function(snapshot) {
										mid = snapshot.val().id;
										if(typeof mid != "number")
											mid = mid.split("-")[0];
										
										meetingstatus++;
										if(meetingstatus != 1)
										{
											jQuery.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=check_meeing&participants='+res+'&meeting_id='+mid, function(res1){
												if(!res1) return;
												res1 = JSON.parse(res1);
												console.log(res1);
												if(res1.status == 2)
												{	
													$scope.$apply(function(){
														$scope.video_container = true;
													});
												}
											});
										}
										
									});
								}
							);
						}
					};
					
					$scope.update_user = function(){
						jQuery.post('<?php echo site_url();?>/wp-admin/admin-ajax.php', {action:'update_participant_data', id: $scope.participant, data: {name: $scope.data.name, email: $scope.data.email}}, function(res){
							$scope.getinput = false;

						});
					};
			}]);
		</script>
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
		</div>
		<style class="qc_style">
		.agent-details1{width:400px; height:250px; background:#fff; position:fixed; bottom:70px; right:20px; border-radius:10px; box-shadow: 0px 0px 6px 0px rgba(0,0,0,0.50); display:none;}
		.cus-pho1{width:100px; height:100px; margin:auto; position: absolute; top: -7px; left:-24px;}
		.agent-chat{background:rgb(121,3,3); color:#fff; margin-top:0; border-radius:0 10px 0 0; padding-left:100px; height:84px;}
		.agent-chat h4{ font-size:16px; font-weight:600; margin:0; padding-top:17px; padding-bottom: 10px;text-align: left;color: #fff;}
		.agent-chat p{  padding: 0 0 10px; text-align:left; font-size:11px; color:#fff; margin-top:4px;line-height: 15px;} 
		.cus-pho1 img { border-radius:50% !important;width:100%; height:100%; display:block;  border:5px solid #f7f7f7;}
		.form-control1{width:100%; text-align:left; border-top:1px solid #790303; position:absolute; bottom:0; background:#fff; border-radius: 0 0 10px 10px; height:91px;}
		.form-control1 textarea{border:none;  padding:10px 32px 4px; margin-top:7px; height:60px; resize:none;font-size: 1.4rem; width:290px; background: transparent none repeat scroll 0 0; }
		.go{ background:none; border:none; width:50px;padding:8px 0; position:absolute; right:0; bottom:40px; color:#790303; font-size:16px;}
		.go1{ background:none; border:none; width:20px;padding:8px 0; position:absolute; left:7px; bottom:10px; color:#ccc; font-size:16px;}
		.chat-icon{ position:fixed; bottom:5px; right:5px; background:#790303; border-radius:50% !important; width:50px; height:50px; cursor:pointer;
		 border:2px solid #fff;}
		.chat-icon i{padding:10px 11px; opacity:0; pointer-events:none; transition:all ease 0.5s; z-index:0;position: absolute;left: 0;top: -2px;}
		.chat-icon.showclose .close,.chat-icon.showchat .chat{ opacity:1; pointer-events:auto; z-index:1;}
		.fa-comments,.fa-times{color:#fff; font-size:30px;}
		.fa-times{ padding:10px 13px !important;}
		.form-control2{ margin-right:10px; width:270px; text-align:center; border:1px solid #ccc; padding:10px; float:right; background:#f1f1f1}
		.wiat-box{float:left; width:180px;}
		.wiat-box input{border:1px solid #ccc; padding:8px 2px; margin-bottom:0px; width:180px; border-radius:2px;}
		.form-control2 p{font-size:12px; color:#888;}
		.submit{ float:none; width:30px; height:69px; border-radius:0 2px 2px 0; background:#790303; border:none; color:#fff;}
		textarea:focus{ outline:none; }
		.connecting{ position:absolute; bottom:60px; right:10px;}
		.connecting span{font-size:14px; font-weight:600; color:#790303;}
		.bowerd{ position:absolute; right:5px; bottom:4px; color:#888; font-size:11px; text-decoration:none;} 
		.bowerd img{ width:10px;}
		.hide{ display:none !important;}

		.agent-details2{width:360px; height:100%; background:#fff; position:fixed; right:0px; box-shadow: 0px 0px 6px 0px rgba(0,0,0,0.50);top: 0;z-index: 1000000;}
		.cus-pho2{width:80px; height:80px; position: absolute; top:8px; left:5px;}
		.agent-chat1{ color:#444; margin-top:10px; padding-bottom: 10px; padding-left:100px; border-bottom: 1px solid #790303;}
		.agent-chat1 h4{ padding-bottom: 12px; font-size:16px; font-weight:600; margin:0; padding-top:17px;text-align: left;}
		.agent-chat1 p{  padding: 0 0 0px; text-align:left; font-size:12px; color:#aaa; text-shadow:0 0px 0.5px; margin-top:4px;line-height: 15px;} 
		.cus-pho2 img { border-radius:50% !important;width:100%; height:100%; display:block;  border:2px solid #f7f7f7;}
		.close-chat{ color: #790303; font-size: 16px; position: absolute; right: 25px; top: 30px;}
		textarea:focus{ outline:none;}
		.messages{ text-align:right}
		.messages .del{ font-size:10px; color:#888; margin-top:10px; display: inline-block; margin-right:20px; font-weight:600;}
		.messages p { margin:0;}
		.messages p span{ text-align:right; max-width: 100%;
		    padding: 1.0rem 1.25rem;
		    white-space: pre-wrap;
		    border-radius: 5px !important;
		    word-break: break-word;
			margin:3px;
			font-size: 1.4rem;
		}
		.msg-bar.msg-last{ background: #a60000 none repeat scroll 0 0;
		    border-bottom-left-radius: 20px !important;
		    border-top-left-radius: 20px !important;
		    margin-bottom: 0;
		    margin-right: 10px; display:inline-block; color:#fff;}
		p:first-of-type .msg-bar.msg-last {
		    border-top-right-radius: 20px !important;}
		p:last-of-type .msg-bar.msg-last {
		    border-bottom-right-radius: 20px !important;
		}
		.chat-mathed{ position:relative;}
		.messages1{ text-align:left;}
		.messages1 .chat-persion{font-size:10px; color:#888; font-weight:600; margin-top:10px; margin-left:20px; display: inline-block;}
		.messages1 p{ margin:0;}
		.messages1 p span{ text-align:left; max-width: 100%;
		    padding: 1.0rem 1.25rem;
		    white-space: pre-wrap;
		    border-radius: 5px;
		    word-break: break-word;
			margin:3px; font-size: 1.4rem;}
			.msg-bar-resive.msg-last-resive{ background:  #f8f8f8 none repeat scroll 0 0;
		    border-bottom-right-radius: 20px !important;
		    border-top-right-radius: 20px !important;
		    margin-bottom: 0;
		    margin-right: 10px; display:inline-block;}
		p:first-of-type .msg-bar-resive.msg-last-resive {
		    border-top-left-radius: 20px !important;}
		p:last-of-type .msg-bar-resive.msg-last-resive {
		    border-bottom-left-radius: 20px !important;
		}
		.messages1 p img{ width:40px; height:40px; border-radius:50%; float:left; margin:10px;}
		.chat-mothed{ height:400px; overflow:auto;}

		.wiat-box{ width:100%;position: relative;}
		.wiat-box input{border:1px solid #ccc; padding:8px 5px; margin-bottom:0px; width:165px; border-radius:2px;height: 35px;font-size: 14px;}
		.form-control2 p{font-size:12px; color:#888;  margin-top: 0;}
		.submit{ float:none; width:36px; height:34px; border-radius:0 2px 2px 0; background:#790303; border:none; color:#fff;}
		.form-control2{ margin-right:20px; float:right; padding: 10px 20px; border:1px solid #ccc; margin-top:7px;}
		.submit .fa-angle-right{ font-weight:600; font-size:16px;}
		</style>
		<?php
		if(get_the_ID())
		$instant_connect_settings = get_post_meta(get_the_ID(), 'instant_connect_settings', true);
		?>
		<div ng-app="demo" ng-controller="ActionController" >
			<div ng-hide="chat.length" class="agent-details1">
				<div class="cus-pho1">
					<?php echo get_avatar( $user_info->user_email, 100 ); ?>
				</div>
				<div class="agent-chat">
					<h4>Agent Name</h4>
					<p>Text placeholder </p>
				</div>
				
				<?php if(isset($instant_connect_settings) && isset($instant_connect_settings['message'])){?>
				<p><?= $instant_connect_settings['message']?></p>
				<?php }?>

				<div class="connecting"><span>Conneting...</span></div>
				<div class="form-control1">
					<form>
						<textarea ng-enter="add();" ng-model="data.msg" id="msg" placeholder="Type a message here" rows="3"></textarea>
						<button class="go1"><i class="fa fa-paperclip" aria-hidden="true"></i></button>
						<button ng-click="add();" class="go hide"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
						<a href="#" class="bowerd">We're <img src="<?= IC_PLUGIN_URL; ?>img/bower.png"> by Agent</a>
					</form>
				</div>
			</div>
			<div ng-show="chat.length && showchat" class="agent-details2">
				<div class="cus-pho2">
					<?php echo get_avatar( $user_info->user_email, 100 ); ?>
				</div>
				<div class="agent-chat1">
					<h4>Agent Name</h4>
					<p>Text placeholder </p>
				</div>
				<div class="close-chat">
					<i ng-click="showchat = false;" class="fa fa-times close" aria-hidden="true"></i>
				</div>
				<div class="chat-mothed" id="messagesDiv">
					<div ng-repeat="ch in chat2 track by $index" on-finish-render>
						<div class="messages" ng-if="$index%2 == 0">
						<p ng-repeat="c in ch.msg track by $index" ><span class="msg-bar msg-last">{{c.msg}}</span></p>
						<span class="del">Delivered {{ch.time | date:'h:mm a'}}</span>
						</div>
						<div class="messages1" ng-if="$index%2 != 0">
							<span class="chat-persion">Agent Name {{ch.time | date:'h:mm a'}}</span>
							<p ng-repeat="c in ch.msg track by $index"><span class="msg-bar-resive msg-last-resive">{{c.msg}}</span></p>
						</div>
					</div>
					<div class="form-control2" ng-show="getinput">
						<p>Get notify when agent is online </p>
						<div class="wiat-box">
							<input type="text" ng-model="data.name" placeholder="Enter your email">
						
							<button ng-click="update_user();"  class="submit"><i class="fa fa-angle-right" aria-hidden="true"></i></button>
						</div>
					</div>
				</div>
				
				<div class="form-control1">
					<form>
						<textarea ng-enter="add();" ng-model="data.msg" class="msg" placeholder="Type a message here" rows="2"></textarea>
						<button class="go1"><i class="fa fa-paperclip" aria-hidden="true"></i></button>
						<button ng-click="add();"  class="go"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
						<a href="#" class="bowerd">We're <img src="<?= IC_PLUGIN_URL; ?>img/bower.png"> by Agent</a>
					</form>
				</div>
			</div>
			<div class="chat-icon showchat" ng-click="showchat=true;">
				<i class="fa fa-comments chat" aria-hidden="true"></i>
				<i class="fa fa-times close" aria-hidden="true"></i>
			</div>
		</div>
		

		<script type="text/javascript">
			var textchatref;
			<?php if(isset($instant_connect_settings) && isset($instant_connect_settings['timeout'])){?>
			var angtimeout = <?= $instant_connect_settings['timeout']?>;
			<?php }else{?>
			var angtimeout = 60;
			<?php }?>

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
						                    }, 500);
						                }
						            }
						        }
						})
			.controller('ActionController', ['$scope', '$timeout', '$http', function($scope, $timeout, $http) {
					
					<?php if(isset($instant_connect_settings) && isset($instant_connect_settings['autopopup'])){?>
						$(".chat-icon").hide();
					$(window).scroll(function() {
					   if($(window).scrollTop() + $(window).height() == $(document).height()) {
					       if(!$(".chat-icon").hasClass("chattriggered"))
					       {
					       		$(".chat-icon").show();
					       		$(".chat-icon").addClass("chattriggered");
					       		$(".chat-icon").trigger("click");
					       }
					   }
					});

					setTimeout(function(){
						if(!$(".chat-icon").hasClass("chattriggered"))
					       {
					       		$(".chat-icon").show();
					       		$(".chat-icon").addClass("chattriggered");
					       		$(".chat-icon").trigger("click");
					       }
					}, 30000);
					<?php }?>

					$(".chat-icon").click(function(){
					    $(".agent-details1").toggle(300);
						$(this).toggleClass("showchat").toggleClass("showclose");
					});
					$( "#msg" ).keyup(function() {
					 	if($(this).val())
					 	{
							$(".go").removeClass("hide");
						}
					 	else
					 	{
							$(".go").addClass("hide");
						}
					});

					$scope.showchat = true;
					$scope.chat = [];
					$scope.chat2 = [];
					$scope.data = {name:"", email:"", msg:""};
					$scope.meeting = {};

					$scope.getinput = false;

					$scope.insert_chat_byid = function(msg){

						var a = {id: "", time: "", msg:[]};
						if($scope.chat2.length == 0)
						{
							$scope.chat2.push({id: msg.id, time: msg.time, msg:[msg]});
						}
						else if($scope.chat2[$scope.chat2.length-1].id == msg.id)
						{
							$scope.chat2[$scope.chat2.length-1].id = msg.id;
							$scope.chat2[$scope.chat2.length-1].time = msg.time;
							$scope.chat2[$scope.chat2.length-1].msg.push(msg);
						}
						else
						{
							$scope.chat2.push({id: msg.id, time: msg.time, msg:[msg]});
						}
					};

					$scope.start_chating = function(res, bl){

						console.log(res);
						textchatref = new Firebase('https://vinogautam.firebaseio.com/pusher/individual_chat/'+res+'/');
						
						$scope.data.time = new Date().getTime();

						if(bl === undefined)
						{
							textchatref.push($scope.data);
							<?php if(isset($instant_connect_settings)){?>
							$timeout(function(){
								textchatref.push({id:'admin', msg:'<?= $user_current_status == 1 ? $instant_connect_settings['onmessage'] : $instant_connect_settings['offmessage'] ;?>'});
							}, 5000);
							<?php }?>
						}

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
									$scope.insert_chat_byid(v);
								});
								}
								else
								{
									$scope.chat.push(v);
									$scope.insert_chat_byid(v);
								}
							}
						});
					};
					
					<?php if($is_waiting){?>
						$scope.start_chating(<?= $is_waiting?>, 1);
						$scope.data.id = <?= $is_waiting?>;
						var online_status = new Firebase('https://vinogautam.firebaseio.com/pusher/online_status');
									cccnt = 1;
									setInterval(function(){
										online_status.update({ count:<?= $is_waiting?>+"-"+cccnt++});
									}, 5000);
					<?php }?>
					$scope.participant = 0;
					$scope.video_container = false;
					$scope.add = function(){
						if($scope.chat.length)
						{
							$scope.data.time = new Date().getTime();
							textchatref.push($scope.data);
							$scope.data.msg = '';
						}
						else
						{
							data = {action:'join_chat', meeting: {mode:1, is_mobile: WURFL.is_mobile, question: $scope.data.msg, complete_device_name: WURFL.complete_device_name, form_factor:WURFL.form_factor, status: 1}};
							jQuery.post('<?php echo site_url();?>/wp-admin/admin-ajax.php', data, function(res){
									$scope.data.id = res;
									$scope.start_chating(res);
									$scope.participant = res;
									$timeout(function(){
										if($scope.chat.length == 1)
										$scope.getinput = true;
									}, angtimeout*1000);
									
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
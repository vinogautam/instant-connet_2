<?php
class IC_admin{
    
    function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_pages' ) );
		add_action('admin_footer', array( $this, 'admin_common_lobby'));
    }
	
	function admin_common_lobby() {
		global $current_user;
		
		$general = get_option('general');
		$general['agent'] = $current_user->ID;
		update_option('general', $general);
		
		$bbb = json_encode(get_option('bbb'));
		//print_r($bbb);
		$status = get_user_meta($current_user->ID, 'user_current_status', true);
		$arr = array(1 => 'Online', 2 => 'Offline', 3 => 'Meeting', 4 => 'Away');

		$mode = get_user_meta($current_user->ID, 'agent_communication_mode', true);
		$modearr = array(1 => 'Question', 2 => 'Chat', 3 => 'IC');

		update_user_meta($current_user->ID, 'user_logintime', date("Y-m-d H:i:s"));
		?>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
		<style>
		.chat_icon{width:70px; height:70px; border-radius:50%; background:#5C93C1; text-align:center; position:fixed; right:30px; bottom:30px; cursor:pointer;}
		.chat_icon i{font-size:35px; color:#fff; display:inline-block; padding:15px;}
		.instant_connect_form{transition:all 350ms ease 0s; right:-400px; position:fixed; background:#fff; top:0; bottom:0; width:300px; z-index: 100000; border:2px solid #ccc;}
		.instant_connect_form.join_chat{right:0; }
		.instant_connect_form label, .instant_connect_form input[type='text']{display:block; width:100%;}
		.instant_connect_form h3{text-align:center; position:relative;}
		.instant_connect_form h3 i{position:absolute; top:2px;}
		.instant_connect_form h3 i.fa-cog, .instant_connect_form h3 i.fa-arrow-left{left:20px;}
		.instant_connect_form h3 i.fa-times{right:20px;}
		.user_current_status_div{text-align:center;}
		.user_current_status_div img{vertical-align:middle; border-radius:50%;}
		.cp{cursor:pointer;}
		.instant_connect_form ul{padding:0 20px;}
		.instant_connect_form li.selected{background:#0073AA;color:#fff; }
		.instant_connect_form li .fa-circle	{color:#08E946}
		.instant_connect_form li {text-align:left; padding:5px;}
		.instant_connect_form li img{border-radius:0;}
		.notification_new_user {background: #ccc none repeat scroll 0 0;bottom: 20px;padding: 20px;position: fixed;right: 350px;text-align: center;width: 200px;transition:all 350ms ease 0s; opacity:0;}
		.notification_new_user.new_noti{opacity:1;}
		.lobby_tab span{display:inline-block;}
		.lobby_tab span.active{border-bottom:2px solid;}
		.settings_tab{position:absolute; transition:all 350ms ease 0s; right:400px; position:fixed; background:#fff; top:0; bottom:0;  z-index: 100000; border:2px solid #ccc;opacity:0;width:0; display:none;}
		.settings_tab.settings{right:0;opacity:1;width:300px;}
		#loading_progress{display:none;}
		.chat_container{
		    bottom: 0;
		    position: fixed;
		    right: 0;
		    z-index: 10000;
		}
		.chat_container .chat_box{
			float: right;
			width: 250px;
			height: 350px;
		    background: #d9d9d9;
		    margin-right: 30px;
		    position: relative;
		}
		.chat_container .chat_box .chat_header{
		    background: #272634 none repeat scroll 0 0;
		    color: #fff;
		    padding: 5px 10px;
		    height: 20px;
		}
		.chat_container .chat_box .chat_header b{vertical-align: top;}
		.chat_container .chat_box .chat_header a{
		    cursor: pointer;
		    float: right;
		    padding: 0 5px;
		    vertical-align: top;
		}
		.chat_container .chat_box .chat_conversation_box {
		    height: 250px;
		    overflow: auto;
		    margin: 10px 0;
		}
		.chat_container .chat_box .chat_input{
		    bottom: 0;
		    padding: 5px;
		    position: absolute;
		    width: 100%;
		}
		.chat_container .chat_box .chat_input input{
		    padding: 10px 0;
		    width: 100%;
		}
		.chat_container .chat_box .chat_conversation_box .own_msg,.chat_container .chat_box .chat_conversation_box p{margin: 0;}
		.chat_container .chat_box .chat_conversation_box .own_msg,.chat_container .chat_box .chat_conversation_box .opponent_msg{margin-top: 15px;}
		.chat_container .chat_box .chat_conversation_box .own_msg p{text-align: right;}
		.chat_container .chat_box .chat_conversation_box .opponent_msg p{text-align: left;}
		.chat_container .chat_box .chat_conversation_box .own_msg div div{    background: #fff;
		    display: inline-block;
		    margin: 0 10px;padding: 0 5px;
		    width: 75%;}
		.chat_container .chat_box .chat_conversation_box .own_msg div img{border-radius: 50%;
		    float: right;
		    margin-right: 10px;}
		.chat_container .chat_box .chat_conversation_box .opponent_msg div div{    background: #fff;
		    display: inline-block;
		    margin: 0 10px;padding: 0 5px;
		    width: 75%;
		float: right;}
		.chat_container .chat_box .chat_conversation_box .opponent_msg div img{border-radius: 50%;margin-left: 10px;float: left;}
		.chat_container .chat_box .chat_conversation_box .opponent_msg+.opponent_msg{margin:0;}
		.chat_container .chat_box .chat_conversation_box .own_msg+.own_msg{margin:0;}
		.chat_container .chat_box .chat_conversation_box .opponent_msg+.opponent_msg img{visibility: hidden;}
		.chat_container .chat_box .chat_conversation_box .own_msg+.own_msg img{visibility: hidden;}
		.chat_container .chat_box.boxminimize{height: 30px;}
		</style>
		<div class="chat_icon">
			<i class="fa fa-comments"></i>
		</div>
		
		<audio id="notification_audio" controls style="display:none;">
		  <source src="<?php _e(IC_PLUGIN_URL);?>notification.mp3" type="audio/mpeg">
		  Your browser does not support the audio tag.
		</audio> 
		
		<div ng-app="instant_connect" ng-controller="ICCtrl">
		
			<div ng-class="{new_noti:!(recent.id === undefined)}" class="notification_new_user">
					
					{{recent.name}} Just Connected
					<p>
						<input class="button button-primary" ng-click="selected(recent.id)" type="submit" value="Connect" name="submit">
					</p>
			</div>
			
			
			
			<div class="instant_connect_form" >
				
				<div ng-init="settings = false; " ng-class="{settings:settings}" class="settings_tab">
					<h3><i class="fa fa-arrow-left cp" ng-click="settings = false;"></i>Instant Connect Settings</h3>
					<form id="bbb_settings" ng-submit="settings_submit();" method="post">
						<p>
							<label>BBB Server</label>
							<input ng-model="bbb.server" value="<?= $bbb['server']; ?>">
						</p>
						<p>
							<label>BBB Salt</label>
							<input ng-model="bbb.salt" value="<?= $bbb['salt']; ?>">
						</p>
						<?php submit_button();?>
					</form>
				</div>
				<form id="instant_connect_form" onSubmit="return false;">
					<h3><i class="fa fa-cog cp" ng-click="settings = true;"></i>Instant Connect Lobby<i class="fa fa-times cp"></i></h3>
					<hr>
					<div class="user_current_status_div">
						<?php echo get_avatar( $current_user->user_email, 50 ).'<br>'; echo $current_user->user_login; ?><br>
						<select name="user_current_status" id="user_current_status">
							<?php foreach($arr as $st=>$lb){ $sel = $st == $status ? 'selected' : ''; ?>
							<option <?php _e($sel);?> value="<?php _e($st);?>"><?php _e($lb);?></option>
							<?php }?>
						</select>
						<br>Communication mode<br>
						<select name="user_current_status" id="user_mode_status">
							<?php foreach($modearr as $st=>$lb){ $sel = $st == $mode ? 'selected' : ''; ?>
							<option <?php _e($sel);?> value="<?php _e($st);?>"><?php _e($lb);?></option>
							<?php }?>
						</select>
					<div>
					<hr>
					<div class="lobby_tab">
						<span ng-class="{active:tab == 1}" ng-click="tab = 1;">Online Users</span>
						<span ng-class="{active:tab == 2}" ng-click="tab = 2;">Conversation</span>
					</div>
					<div ng-if="tab == 1" class="">
						<ul>
							<li ng-repeat="part in participants" ng-if="part.mode == 3 && part.status == '1'" ng-click="selected(part.id)" ng-class="{selected:check_selected(part.id)}" ng-init="part.diff = part.diff === undefined ? 0 : part.diff; autotimer(part);">
								<span class="fa fa-circle"></span>
								<img ng-src="{{get_avatar(part)}}">
								#{{part.id}} {{part.name}} 
							</li>
						</ul>
						<p ng-if="selected_participants.length">
							<input id="submit" class="button button-primary" ng-click="create_meeting(0)" type="submit" value="Join Chat" name="submit">
							<input id="submit" class="button button-primary" ng-click="create_meeting(1)" type="submit" value="Join Meeting" name="submit">
						</p>
					</div>
					<div ng-if="tab == 2" class="">
						<ul>
							<li ng-repeat="part in participants" ng-if="part.mode != 3 && part.status == '1'" ng-click="new_chat(part.id)" ng-init="part.diff = part.diff === undefined ? 0 : part.diff; autotimer(part);">
								<span class="fa fa-circle"></span>
								<img ng-src="{{get_avatar(part)}}">
								#{{part.id}} {{part.name}} {{part.msg}}
							</li>
						</ul>
					</div>
					<!--<div ng-if="tab == 2" class="">
						<div ng-show="schedule">
							<p>Name : {{schedule['name']}}</p>
							<p>Email : {{schedule['email']}}</p>
							<p>Appointment on : <input type="text"></p>
							<p>
								<button ng-click="$parent.schedule = false;">Back</button>
								<input class="button button-primary" ng-click="schedule_appointment(1)" type="submit" value="Confirm Appointment" name="submit">
							</p>
						</div>
						<ul ng-hide="schedule">
							<li ng-repeat="part in participants" ng-show="part.status == '0'">
								<span class="fa fa-circle"></span>
								<img ng-src="{{get_avatar(part)}}">
								#{{part.id}} {{part.name}} 
								<button ng-click="$parent.$parent.schedule = part">Schedule Appointment</button>
							</li>
						</ul>
					</div>-->
				</form>
			</div>


			
			<!-- ng-show="$index < 2 || $index == current_chat.length-1" -->
			<div ng-cloak class="chat_container">
			      <div class="chat_box" ng-repeat="cc in current_chat" ng-init="$parent.minmax[cc]=false;" ng-class="{boxminimize:minmax[cc]==true}">
			        <div class="chat_header">
			          <img style="float: left;" class="img-circle" width="20" ng-src="{{getAvatarbyId(cc)}}" alt="">
			          <b ng-if="part.mode == 2">{{part.name}}({{part.email}})</b>
			          <a ng-show="minmax[cc]==true" ng-click="$parent.minmax[cc]=false;"><i class="fa fa-plus"></i></a>
			          <a ng-show="minmax[cc]==false" ng-click="$parent.minmax[cc]=true;"><i class="fa fa-minus"></i></a>
			          <a ng-click="remove_chat(cc)"><i class="fa fa-close"></i></a>
			          <a ng-click="create_meeting_from_qc(cc)"><i class="fa fa-video-camera"></i></a>
			        </div>
			        <div ng-show="minmax[cc]==false" id="chat_box_{{cc}}" class="chat_conversation_box">
			        	<b ng-if="part.mode == 1">"{{part.question}}"</b>
			          <div ng-repeat="msg in all_chat_data[cc]" on-finish-render="{{cc}}" ng-class="{own_msg: msg.id == 'agent', opponent_msg: msg.id != 'agent'}">
			            <div class="clearfix" ng-if="msg.msg && msg.id == 'agent'">
			              <div><p>{{msg.msg}}</p></div>
			              <img ng-src="http://identicon.org/?t=agent&s=20">
			            </div>
			            <div class="clearfix" ng-if="msg.msg && msg.id != 'agent'">
			              <img ng-src="{{getAvatarbyId(msg.id)}}">
			              <div><p>{{msg.msg}}</p></div>
			            </div>
			          </div>
			        </div>
			        <div ng-show="minmax[cc]==false" class="chat_input">
			          <input type="text" ng-model="multi_chat[part.id]" ng-enter="add(part.id);">
			        </div>
			      </div>
			</div>

		</div>
		<script src='https://ajax.googleapis.com/ajax/libs/angularjs/1.5.0-rc.1/angular.min.js'></script>
			<script src='https://cdn.firebase.com/js/client/2.2.4/firebase.js'></script>
			<script src='https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/core.js'></script>
			<script src='https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/md5-min.js'></script>
			  <script>
				    var app = angular.module("instant_connect", []);

				    app.directive('ngEnter', function() {
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
						                      jQuery("#chat_box_"+attr.onFinishRender).scrollTop(jQuery("#chat_box_"+attr.onFinishRender)[0].scrollHeight);
						                    });
						                }
						            }
						        }
						});
					    app.controller("ICCtrl", function($scope, $http, $timeout, $interval) {
								var myDataRef = new Firebase('https://vinogautam.firebaseio.com/pusher/new_user');
								var statusRef = new Firebase('https://vinogautam.firebaseio.com/pusher/status_change');
								var modeRef = new Firebase('https://vinogautam.firebaseio.com/pusher/mode_change');
								var meetingRef = new Firebase('https://vinogautam.firebaseio.com/pusher/new_meeting');
								var online_status = new Firebase('https://vinogautam.firebaseio.com/pusher/online_status');
								var refresh_user_list = new Firebase('https://vinogautam.firebaseio.com/pusher/refresh_user_list');

								/*Question mode and chat mode upates start here*/
								$scope.minmax = {};
								$scope.current_chat = [];
								var all_chat_listeners = [];
						        $scope.all_chat_data = [];
						        $scope.multi_chat = {};
						        $scope.new_chat = function(id)
						        {
						          if($scope.current_chat.indexOf(id) != -1) return;

									$scope.current_chat.push(id);

						          if(all_chat_listeners[id] === undefined)
						          {
						            $scope.all_chat_data[id] = [];
						            all_chat_listeners[id] = new Firebase('https://vinogautam.firebaseio.com/pusher/individual_chat/'+id+'/');
						            
						            all_chat_listeners[id].on('child_added', function(snapshot) {
						                if(!$scope.$$phase) {
						                  $scope.$apply(function(){
						                    $scope.all_chat_data[id].push(snapshot.val());
						                  });
						                }
						                else
						                {
						                  $scope.all_chat_data[id].push(snapshot.val());
						                }
						            });
						          }
								};

								$scope.add = function(id){
						            all_chat_listeners[id].push({id: 'agent', msg: $scope.multi_chat[id], time:new Date().getTime(), name: '<?= $current_user->user_login; ?>'});
						            $scope.multi_chat[id] = '';
								};
								/*Question mode and chat mode upates end here*/

								$scope.getAvatarbyId = function(id){
							          return "http://identicon.org/?t="+id+"&s=20";
								};

								var refresh_user_list_status = 0;
								refresh_user_list.on('value', function(snapshot) {
									refresh_user_list_status++;
									if(refresh_user_list_status != 1)
									{
										$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=waiting_participants').then(function(res){
											$scope.participants = res['data'];
										});
									}
									console.log(refresh_user_list_status);
								});

								var status_count = 0;
								var mode_count = 0; 
								$scope.tab = 1;
								
								$scope.participants = [];
								$scope.recent = {};
								$scope.bbb = <?= $bbb;?>;
								$scope.selected_participants = [];
								
								$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=waiting_participants').then(function(res){
									$scope.participants = res['data'];
								});
								
								$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=presentation_file').then(function(res){
									$scope.presentations = res['data'];
								});
								
								$scope.tmp_check = false;
								
								myDataRef.on('value', function(snapshot) {
									if($scope.tmp_check)
									{
										$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=new_participants&id='+snapshot.val().count).then(function(res){
											
											$scope.recent = res['data'];
											$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=waiting_participants').then(function(res){
												$scope.participants = res['data'];
												jQuery("#notification_audio").trigger('play');
											});
											
											$timeout(function(){
												$scope.recent = {};
											}, 5000);
										});
									}
									$scope.tmp_check = true;
								});
								
								statusRef.once('value', function(snapshot) {
									status_count = parseInt(snapshot.val().count);
								});

								modeRef.once('value', function(snapshot) {
									mode_count = parseInt(snapshot.val().count);
								});
								
								$scope.settings_submit = function(){
									$http.post('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=save_settings', $scope.bbb).then(function(res){
										$scope.settings = false;
									});
								};
								
								$scope.selected = function(id)
								{
									ind = jQuery.inArray(id, $scope.selected_participants);
									if(ind == -1)
										$scope.selected_participants.push(id);
									else
										$scope.selected_participants.splice(ind, 1);
								};
								
								$scope.check_selected = function(id)
								{
									return jQuery.inArray(id, $scope.selected_participants) == -1 ? false : true;
								};
								
								$scope.get_avatar = function(row)
								{
									str = row.email ? row.email : row.name;
									hash = CryptoJS.MD5(str).toString();
									return 'http://2.gravatar.com/avatar/'+hash+'?s=20&d=mm&r=g';
								}
								
								jQuery("#user_current_status").change(function()
								{
									$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=update_agent_status&status='+jQuery(this).val()).then(function(res){
										statusRef.update({ count:status_count++});
									});
								});
								
								jQuery("#user_mode_status").change(function()
								{
									$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=update_agent_status&chatmode&status='+jQuery(this).val()).then(function(res){
										modeRef.update({ count:mode_count++});
									});
								});

								setInterval(function(){
									$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=update_agent_status&status='+jQuery("#user_current_status").val()).then(function(res){
									});
								}, 30000);

								$scope.delete_file = function(e, id)
								{
									$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=delete_presentation_file&id='+id).then(function(res){
										$scope.presentations = res['data'];
									});
								};
								
								$scope.create_meeting = function(st)
								{
									angular.forEach($scope.selected_participants, function(v,k){
										$interval.cancel(intervals[v]);
									});

									$http.post('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=create_new_meeting&st='+st, 
									{data: $scope.selected_participants}).then(function(res){
										
										console.log(res['data']);
										
										meetingRef.update({ id:res['data']['id']});
										$scope.selected_participants = [];
										$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=waiting_participants').then(function(res){
											$scope.participants = res['data'];
										});

										refresh_user_list.update({ st:"new_meeting_created"+res['data']['id']});

										window.open("<?= str_replace("http://financialinsiders.ca", "https://financialinsiders.ca", site_url()); ?>/meeting/?id="+res['data']['id']+"&admin", '_blank');
									});
								};
								
								$scope.create_meeting_from_qc = function(id)
								{
									$interval.cancel(id);

									$http.post('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=create_new_meeting&st=2', 
									{data: [id]}).then(function(res){
										
										meetingRef.update({ id:res['data']['id']});
										$scope.selected_participants = [];
										$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=waiting_participants').then(function(res){
											$scope.participants = res['data'];
										});

										refresh_user_list.update({ st:"new_meeting_created"+res['data']['id']});

										window.open("<?= str_replace("http://financialinsiders.ca", "https://financialinsiders.ca", site_url()); ?>/meeting/?id="+res['data']['id']+"&admin", '_blank');
									});
								};

								var intervals = [];
								var interval_diff = [];
								
								$scope.autotimer = function(part)
								{
									if(typeof intervals[part.id] != "undefined") return;
									intervals[part.id] = $interval(function(){
										if(interval_diff[part.id] > 3)
										{
											$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=update_user_offline&id='+part.id).then(function(res){
												$scope.participants = res['data'];
												refresh_user_list.update({ st:"user_offline_updated_"+part.id});
											});
											$interval.cancel(intervals[part.id]);
										}
										else
											interval_diff[part.id]++;
									}, 5000);
									interval_diff[part.id] = 0;
								}
								
								var online_statusstatus = 0;
								online_status.on('value', function(snapshot) {
									online_statusstatus++;
									if(online_statusstatus != 1)
									{
										vall = snapshot.val().count.split("-");
										interval_diff[vall[0]] = 0;
									}
									
								});
								
								jQuery(document).on("change", ".presentation_file", function(e){
									jQuery("#loading_progress").show();
									f = e.target.files[0];
									formData = new FormData();
									formData.append('file', f);
									formData.append('filename', f.name);
									
									jQuery.ajax({
										url : '<?php echo site_url();?>/wp-admin/admin-ajax.php?action=presentation_file&default='+($scope.tab == 3 ? 1 : 0),
										type: "POST",
										data : formData,
										processData: false,
										contentType: false,
										success:function(response, textStatus, jqXHR){
											jQuery("#presentation_file").val('');
											jQuery("#loading_progress").hide();
											$scope.$apply(function(){
												$scope.presentations = JSON.parse(response);
												$scope.tab = 2;
											});
										},
										error: function(jqXHR, textStatus, errorThrown){
											
										}
									});
								});
						});
			  </script>
		<script>
			jQuery(document).ready(function(){
				jQuery(".chat_icon, #instant_connect_form .fa-times").click(function(){
					jQuery(".instant_connect_form").toggleClass("join_chat");
				});
			});
		</script>
		<?php
	}

    function add_plugin_pages() {
        
        if(is_multisite() && is_super_admin() || current_user_can('manage_options')) {
             
            add_menu_page( 'Instant Connect', 'Instant Connect', 'manage_options', 'instant_connect', array( $this, 'settingsPage' ));
            add_submenu_page( 'instant_connect', 'Endorsements', 'Endorser Waiting for approval',  9, 'waiting_endorsers', array( &$this, 'waiting_endorsers'));
			
			//add_submenu_page( 'instant_connect', 'Endorsements', 'Settings',  9, 'ic_settings', array( &$this, 'settingsPage'));		
        
        }
   
    } 

    public function waiting_endorsers(){
    	?>
    	<div class="wrap">
            <h2>Waiting for approval</h2>           
            <?php 
				$endosersTable = new WaitingEndoserTable();
				$endosersTable->prepare_items();
				$endosersTable->display();
			?>
        </div>
    	<?php
    };
    
    //our admin tabs navigation
    public function adminTabs($tabs, $default, $page){
        
        if ( isset ( $_GET['tab'] ) ) $current = $_GET['tab']; else $current = $default;
        
        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
    
        foreach( $tabs as $tab => $name ){
            $class = ( $tab == $current ) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='?page=$page&tab=$tab'>$name</a>";
    
        }
        
        echo '</h2>';
    }	
    /**
     * admin page callback
     */
    public function settingsPage()
    {   global $pagenow, $current_user, $ntm_mail;
		if ( isset ( $_GET['tab'] ) ) $current = $_GET['tab']; else $current = 'send_auto_meeting_link';
		
		$tabs = array('send_auto_meeting_link' => 'Auto Meeting Link', 'youtube' => 'Youtube link', 'presentations' => 'Presentations');
		$current_page = $tabs[$current];
		$current_tab = $current.'_page';
		
		if($current != 'add_endorsers_cloudsponge')
		//$error = $this->post_actions();
		
		?>
        <div class="wrap">
            <h2><?php echo $current_page;?></h2>           
            <?php 
				if(isset($error)) echo $error;
				$this->adminTabs($tabs, 'general', 'instant_connect');
				$this->$current_tab();
			?>
        </div>
        <?php
        
    }
	
    public function send_auto_meeting_link_page()
    {
    	if(isset($_POST['send_auto_meeting_link_save']))
		{
			$message = '<h4>Hi '.$_POST['name'].'</h4>';
			$message .= '<p><a href="'.site_url().'/wp-admin/admin-ajax.php?meeting&action=join_chat&name='.$_POST['name'].'&email='.$_POST['email'].'">click here to join Financial Insiders meeting</a></p>';
			NTM_mail_template::send_mail($_POST['email'], 'Financial Insiders auto meeting link.', $message);
		}

    	?>
    	<form method="post">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="blogname">Name</label></th>
						<td><input type="text" class="regular-text"  id="blogname" name="name"></td>
					</tr>
					<tr>
						<th scope="row"><label for="blogname">Email</label></th>
						<td><input type="text" class="regular-text"  id="blogname" name="email"></td>
					</tr>
				</tbody>
			</table>
			<?php submit_button('Save ', 'primary', 'send_auto_meeting_link_save');?>
		</form>
    	<?php
    }

	public function youtube_page()
    {
    	$option = get_option('youtube_videos');

		if(isset($_POST['general-save']))
		{	
			$option = is_array($option) ? $option : [];
			$option[] = $_POST['general'];
			update_option('youtube_videos', $option);
		}
		
		$option = get_option('youtube_videos');
		$option = is_array($option) ? $option : [];
		?>
		<form method="post">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="blogname">Title</label></th>
						<td><input type="text" class="regular-text"  id="blogname" name="general[name]"></td>
					</tr>
					<tr>
						<th scope="row"><label for="blogname">Youtube Link</label></th>
						<td><input type="text" class="regular-text"  id="blogname" name="general[url]"></td>
					</tr>
				</tbody>
			</table>
			<?php submit_button('Save ', 'primary', 'general-save');?>
		</form>
		<table class="form-table">
			<thead>
				<tr>
					<th>#</th>
					<th>Title</th>
					<th>Link</th>
					<th>Video</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($option as $k=>$opt){?>
				<tr>
					<th><?= $k+1; ?></th>
					<th><?= $opt['name']; ?></th>
					<th><?= $opt['url']; ?></th>
					<th><iframe width="150" height="100" src="<?= str_replace("watch?v=", "embed/", $opt['url']);?>"></iframe></th>
				</tr>
				<?php }?>
			</tbody>
		</table>
		<?php
	}

	public function presentations_page()
    {
    	$option = get_option('ic_presentations');
		$option = is_array($option) ? $option : [];
		?>
		<form method="post">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="blogname">Title</label></th>
						<td><input id="convert_ppt" type="file" ></td>
					</tr>
				</tbody>
			</table>
			<?php //submit_button('Save ', 'primary', 'ic_presentations-save');?>
		</form>
		<table class="form-table">
			<thead>
				<tr>
					<th width="10">#</th>
					<th width="90">Name</th>
					<th width="90">Image</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach($option as $k=>$opt){?>
				<tr>
					<th><?= $k+1; ?></th>
					<th><?= $opt['name']; ?></th>
					<?php foreach($opt['files'] as $f){ ?>
					<th><img width='100' height='100' src='<?= IC_PLUGIN_URL.'/extract/'.$opt['folder'].'/'.$f;?>'></th>
					<?php }?>
				</tr>
				<?php }?>
			</tbody>
		</table>
		<script>
		$ = jQuery;
		$(document).on("change", "#convert_ppt", function(e) {
					handleFileSelect(e, true);
				});
				
				var formdata = !!window.FormData;

				function handleFileSelect(evt, manual) {
					evt.stopPropagation();
					evt.preventDefault();
					var files;
					files = evt.target.files;
					
					for (var i = 0, f; f = files[i]; i++) {
						if (f.type !== "") {
							var filename = f.name;
							var formData = formdata ? new FormData() : null;
							formData.append('File', files[i]);
							formData.append('OutputFormat', 'jpg');
							formData.append('StoreFile', 'true');
							formData.append('ApiKey', '938074523');
							formData.append('JpgQuality', 100);
							formData.append('AlternativeParser', 'false');

							file_convert_to_jpg(formData, filename);
						} else {
							progress_status(random_id, 0, "Invalid File Format...");
						}
					}

				}

				function file_convert_to_jpg(formData, filename) {
					$.ajax({
						url: "https://do.convertapi.com/PowerPoint2Image",
						type: "POST",
						data: formData,
						processData: false,
						contentType: false,
						success: function(response, textStatus, request) {
							$.post("<?php echo site_url();?>/wp-admin/admin-ajax.php?action=save_ppt&name="+filename, {data:request.getResponseHeader('FileUrl')}, function(data){
								if(data != 'error')
								{	
									window.location.reload();
								}
							});
						},
						error: function(jqXHR) {
							alert("Error in file conversion");
						}
					});
				}
		</script>
		<?php
	}
	
	public function pusher_page()
    {
		if(isset($_POST['pusher-save']))
			update_option('pusher', $_POST['pusher']);
		
		$option = get_option('pusher');
		?>
		<form method="post">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="blogname">Pusher App Id</label></th>
						<td><input type="text" class="regular-text" value="<?php echo $option['id'];?>" id="blogname" name="pusher[id]"></td>
					</tr>
					<tr>
						<th scope="row"><label for="blogname">Pusher Api key</label></th>
						<td><input type="text" class="regular-text" value="<?php echo $option['api'];?>" id="blogname" name="pusher[api]"></td>
					</tr>
					<tr>
						<th scope="row"><label for="blogname">Pusher Api Secret</label></th>
						<td><input type="text" class="regular-text" value="<?php echo $option['secret'];?>" id="blogname" name="pusher[secret]"></td>
					</tr>
				</tbody>
			</table>
			<?php submit_button('Save ', 'primary', 'pusher-save');?>
		</form>
		<?php
	}
	
	public function chat_icon_page()
    {
		
		
		if(isset($_POST['chat_icon-save']))
			update_option('chat_position', $_POST['chat_position']);
		
		$option = get_option('chat_position');
		?>
		<form method="post">
			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="blogname">Icon Position</label></th>
						<td>
							<select name="chat_position">
								<option <?php echo $option == 1 ? 'selected' : ''; ?> value="1">TopLeft</option>
								<option <?php echo $option == 2 ? 'selected' : ''; ?> value="2">TopRight</option>
								<option <?php echo $option == 3 ? 'selected' : ''; ?> value="3">BottomLeft</option>
								<option <?php echo $option == 4 ? 'selected' : ''; ?> value="4">BottomRight</option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
			<?php submit_button('Save ', 'primary', 'chat_icon-save');?>
		</form>
		<?php
	}
    
    
} //end class endorsements
     
   

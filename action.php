<script type="text/javascript" charset="utf-8">
		
		  var player;
		  var scope;
		  var myDataRef = new Firebase('https://vinogautam.firebaseio.com/pusher/new_user');
		  var meetingRef = new Firebase('https://vinogautam.firebaseio.com/pusher/new_meeting');
		  var allowtoleave = false;	
		  function onYouTubeIframeAPIReady() {
			scope = angular.element($("body")).scope();
			player = new YT.Player( 'youtube-player', {
			  events: { 'onStateChange': onPlayerStateChange }
			});
			console.log(player);
		  }
		  
		  function onPlayerStateChange(event) {
				switch(event.data) {
				  case 0:
					console.log('video ended');
					break;
				  case 1:
					console.log('video playing from '+player.getCurrentTime());
					scope.video_noti('start', player.getCurrentTime());
					break;
				  case 2:
					console.log('video paused at '+player.getCurrentTime());
					scope.video_noti('pause', player.getCurrentTime());
				}
			}
		function setCookie(cname, cvalue, exdays) {
			var d = new Date();
			d.setTime(d.getTime() + (exdays*24*60*60*1000));
			var expires = "expires="+d.toUTCString();
			document.cookie = cname + "=" + cvalue + "; " + expires;
		}

		function getCookie(cname) {
			var name = cname + "=";
			var ca = document.cookie.split(';');
			for(var i = 0; i < ca.length; i++) {
				var c = ca[i];
				while (c.charAt(0) == ' ') {
					c = c.substring(1);
				}
				if (c.indexOf(name) == 0) {
					return c.substring(name.length, c.length);
				}
			}
			return "";
		}
		
		angular.module('demo', ['opentok', 'opentok-whiteboard'])
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
		.filter('to_trusted', ['$sce', function($sce){
        return function(text) {
            return $sce.trustAsHtml(text);
        };
    }])
            .controller('MyCtrl', ['$scope', 'OTSession', 'apiKey', 'sessionId', 'token', '$timeout', '$http', '$interval', function($scope, OTSession, apiKey, sessionId, token, $timeout, $http, $interval) {
                


                $scope.urlify = function(text) {
				    var urlRegex = /(https?:\/\/[^\s]+)/g;
				    return text.replace(urlRegex, function(url) {
				        return '<a target="_blank" href="' + url + '">' + url + '</a>';
				    });
				}

                $scope.chat = [];
                $scope.offine_user = [];
				$scope.data = {active_menu:"presentation", active_presentation:{files:"", folder:""}, active_slide:"", active_video:"", video_status:false};
				$scope.noti = false;
				$scope.presentation = true;
				$scope.is_admin = <?= isset($_GET['admin']) ? 1 : 0;?>;
				
				<?php 
				$option = get_option('ic_presentations');
				$option = is_array($option) ? $option : []; 
				?>

				$scope.presentation_files = <?= json_encode($option)?>;
				
				<?php 
				$option = get_option('youtube_videos');
				$option = is_array($option) ? $option : []; 
				?>

				$scope.youtube_list = <?= json_encode($option)?>;

				<?php if(isset($_GET['admin'])){?>
				
				<?php global $wpdb; $results = $wpdb->get_results("select * from ".$wpdb->prefix . "meeting_participants where meeting_id=".$meeting_id);?>
				$scope.show_video = 1;
				$scope.show_whiteboard = 1;
				$scope.data2 = {name:"admin", email:"<?= bloginfo('admin_email');?>", msg:""};
				$scope.joined_user = <?= json_encode($results); ?>;
				console.log($scope.joined_user);
				$scope.participants = [];
				$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=waiting_participants').then(function(res){
					$scope.participants = res['data'];
				});
				
				$scope.join_new_user_to_meeting = function(pid, status){
					$http.post('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=new_user_to_meeting',
					{mid:<?= $meeting_id?>, pid:pid, status:status}
					).then(function(res){
						$scope.joined_user = res['data']['joined_user'];
						$scope.participants = res['data']['participants'];
						meetingRef.update({ id:"<?= $meeting_id.'-'?>"+new Date().getTime()});
					});
				};
				
				$scope.tmp_check = false;
				$scope.selected_participants = [];
				myDataRef.on('value', function(snapshot) {
					if($scope.tmp_check)
					{
						$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=new_participants&id='+snapshot.val().count).then(function(res){
							$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=waiting_participants').then(function(res){
								$scope.participants = res['data'];
							});
						});
					}
					$scope.tmp_check = true;
				});
				
				var intervals = [];
								
				$scope.autotimer = function(part)
				{
					intervals[part.id] = $interval(function(){
						if(part.diff > 2)
						{
							$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=update_user_offline&id='+part.id).then(function(res){
								$scope.participants = res['data'];
							});
							$interval.cancel(intervals[part.id]);
						}
						else
							part.diff++;
					}, 5000);
				}
				
				$scope.addnew_video = function(){
					if($scope.newvideo)
					{
						$scope.youtube_list.push($scope.newvideo);
						$http.post('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=addnew_video', $scope.newvideo).then(function(){

						});
					}
				};
				
				
				$scope.deletevideo = function(e, ind){
					e.stopPropagation();
					$scope.youtube_list.splice(ind,1);
					setCookie('youtube_list', JSON.stringify($scope.youtube_list));
				};
				
				<?php }else{?>
				
				window.addEventListener("beforeunload", function (e) {
				  console.log(allowtoleave);
				  if(!allowtoleave)
				  {
				  	  var confirmationMessage = "\o/";

					  $scope.send_noti("attempttoleave_"+<?= $_GET['pid'];?>);

					  (e || window.event).returnValue = confirmationMessage; 
					  return confirmationMessage; 
				  }
				                             
				});

				<?php global $wpdb; $results = $wpdb->get_row("select * from ".$wpdb->prefix . "meeting_participants where id=".$_GET['pid']);?>
				$scope.data2 = {name:"<?= $results->name;?>", email:"<?= $results->email;?>", msg:""};
				$scope.show_video = 0;
				$scope.show_whiteboard = 0;
				<?php }?>
				
				$scope.change_video = function(p, admin){
					$scope.data.active_video = p;
					player.loadVideoById(p.split("?v=")[1], 0, "default");
					player.stopVideo();
					$scope.video = true;
					$scope.presentation = false;
					$scope.users=false;
					$scope.data.active_menu = 'video';
					if(admin === undefined)
						$scope.signal({type: 'video_change', video: p}, true);
				};
				
				$scope.check_user_is_offine = function(id){
					if(typeof $scope.offine_user[id] != "undefined")
					{
						$scope.left_user = id;
						$("#userleftmodal").modal("show");
						$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=update_user_offline&meetingroom=<?= $_GET['id'] ?>&id='+id).then(function(res){
							$scope.participants = res['data'];
						});
					}
				};

				var statusRef = new Firebase('https://vinogautam.firebaseio.com/opentok/<?= $sessionId?>');
				statusRef.on('child_added', function(snapshot) {
					//angular.forEach(snapshot.val(), function(v,k){
						v = snapshot.val();
						console.log(v);
						if(typeof v.msg != "undefined")
						{
							hn = v.email ? v.email : v.name;
							v.hash = CryptoJS.MD5(hn).toString();
							if(!$scope.$$phase) {
								$scope.$apply(function(){
									$scope.chat.push(v);
									$scope.visible = true;
								});
							}
							else
							{
								$scope.chat.push(v);
								$scope.visible = true;
							}
						}
					//});
				});
				
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
									new_data = JSON.parse(data);
									new_data.name = filename;
									$scope.$apply(function(){
										$scope.presentation_files.push(new_data);
										$scope.selected_file(new_data.folder, new_data.files);
									});
								}
							});
						},
						error: function(jqXHR) {
							alert("Error in file conversion");
						}
					});
				}
				
				$scope.selected_file = function(folder, files, admin)
				{
					
					$scope.presentation=true;
					$scope.users=false;
					$scope.video=false;
					$scope.data.active_menu = 'presentation';
					$scope.data.active_presentation.folder = folder;
					$scope.data.active_presentation.files = files;
					player.stopVideo();
					$timeout(function(){
						$(".slider1").slick('unslick');
						$(".slider2").slick('unslick');
						$(".slider1").empty();
						$(".slider2").empty();
						angular.forEach(files, function(v,k){
							$('.slider1').append("<div><img width='700' height='400' src='<?= IC_PLUGIN_URL;?>/extract/"+folder+"/"+v+"'></div>");
							$('.slider2').append("<div><img width='100' height='150' src='<?= IC_PLUGIN_URL;?>/extract/"+folder+"/"+v+"'></div>");
						});
						
						if(admin === undefined)
							$scope.signal({type: 'presentation_change', folder: folder, files: files}, true);
						$scope.clear();
						$scope.construct_slider();
					}, 1000);
				}
				
				$scope.gravatar = function(email){
					encrypt = CryptoJS.MD5(email).toString();
					return "https://www.gravatar.com/avatar/"+encrypt+"?s=40";
				};
				
				$scope.switchtomeeting = function(id)
				{
					$http.post('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=new_user_to_meeting',
					{mid:<?= $meeting_id?>, pid:id, status:"3"}
					).then(function(res){
						$scope.joined_user = res['data']['joined_user'];
						$scope.participants = res['data']['participants'];
						$scope.send_noti("switchtomeeting_"+id);
					});
					
				};
				
				$scope.exit_user_page = function(){
					if($scope.exit_user == "all")
					{
						$scope.send_noti("exitalluser_"+$scope.selected_page);
						$timeout(function(){
							//window.location.assign("<?= site_url();?>");
							window.close();
						}, 500);
					}
					else
					{
						$http.post('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=new_user_to_meeting',
						{mid:<?= $meeting_id?>, pid:$scope.exit_user, status:"4"}
						).then(function(res){
							$scope.joined_user = res['data']['joined_user'];
							$scope.participants = res['data']['participants'];
							$scope.send_noti("exituser_"+$scope.exit_user+"_"+$scope.selected_page);
							$scope.selected_page = "";
						});
					}
					$("#myModal").modal("hide");
					
				};

				$scope.usercontrol = function(id, type, status)
				{
					$http.post('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=usercontrol',
					{mid:<?= $meeting_id?>, pid:id, type:type, status:status}
					).then(function(res){
						$scope.joined_user = res['data']['joined_user'];
						$scope.participants = res['data']['participants'];
						$scope.send_noti(type+"_"+status+"_"+id);
					});
					
				};
				
				$scope.send_noti = function(noti)
				{
					OTSession.session.signal( 
								{  type: 'user-notifications',
								   data: noti
								}, 
								function(error) {
									if (error) {
									  console.log("signal error ("
												   + error.code
												   + "): " + error.message);
									} else {
									  console.log("signal sent.");
									}
								});
				};
				
				$scope.add = function(){
					statusRef.push($scope.data2);
					$scope.data2.msg = '';
				};
				
				$scope.video_noti = function(st, tm){
					if($scope.is_admin)
					{
						$scope.data.video_status = st;
						OTSession.session.signal( 
								{  type: 'youtube-player',
								   data: {st:st, tm:tm}
								}, 
								function(error) {
									if (error) {
									  console.log("signal error ("
												   + error.code
												   + "): " + error.message);
									} else {
									  console.log("signal sent.");
									}
								});
					}
				};
				
				$('#stop').on('click', function() {
					$('#popup-youtube-player')[0].contentWindow.postMessage('{"event":"command","func":"' + 'pauseVideo' + '","args":""}', '*');
				});
				
				$scope.connected = false;
				OTSession.init(apiKey, sessionId, token, function (err) {
					if (!err) {
						$scope.$apply(function () {
							$scope.connected = true;
						});
					}
				});
				$scope.streams = OTSession.streams;
				$scope.pvideo = 0;
				$scope.$on("otAccessAllowed", function(){
					$scope.$apply(function () {$scope.pvideo = 1;});
				});

				$scope.signal = function(data, isobj){
					
					data = isobj === undefined ? {type: data} : data;
					
					player.stopVideo();
					OTSession.session.signal( 
							{  
								type: 'admin-signal' ,
								data: data
							}, 
							function(error) {
								if (error) {
								  console.log("signal error ("
											   + error.code
											   + "): " + error.message);
								} else {
								  console.log("signal sent.");
								}
							}
					);
				};
				
				$scope.construct_slider = function(){
				
					$('.slider1').slick({
					  slidesToShow: 1,
					  slidesToScroll: 1,
					  arrows: false,
					  fade: true,
					  <?php if(isset($_GET['admin'])){?>
					  asNavFor: '.slider2'
					  <?php }else{?>
					  swipe:false
					  <?php }?>
					});
					<?php if(isset($_GET['admin'])){?>
					$('.slider2').slick({
					  slidesToShow: 3,
					  slidesToScroll: 1,
					  asNavFor: '.slider1',
					  dots: true,
					  centerMode: true,
					  focusOnSelect: true
					});
					<?php }?>
				
				};
				
				$scope.construct_slider();
				
				OTSession.session.on({
                    sessionConnected: function() {
						if($scope.is_admin)
						{
								$scope.$apply(function(){$scope.slide_menu = true;});
								$('.slider1').on('afterChange', function(event, slick, currentSlide){
								  console.log(currentSlide);
								  $scope.$apply(function(){$scope.data.active_slide = currentSlide;});
								  OTSession.session.signal( 
									{  type: 'presentationControl',
									   data: {slide:currentSlide}
									}, 
									function(error) {
										if (error) {
										  console.log("signal error ("
													   + error.code
													   + "): " + error.message);
										} else {
										  console.log("signal sent.");
										}
									});
									$scope.clear();
									OTSession.session.signal( 
									{  type: 'otWhiteboard_clear'
									}, 
									function(error) {
										if (error) {
										  console.log("signal error ("
													   + error.code
													   + "): " + error.message);
										} else {
										  console.log("signal sent.");
										}
									});
								});
						}
						else
						{
							OTSession.session.signal( 
									{  type: 'joined_meeting',
									   data: {}
									}, 
									function(error) {
										
									});
						}
                    }
				});
				
				OTSession.session.on('signal:user-notifications', function (event) {
					console.log(event);
					<?php if(!isset($_GET['admin'])){?>
					if(typeof event.data != "undefined" && event.data.indexOf("video") != -1 && parseInt(event.data.split("_")[2]) == <?= $_GET['pid'];?>)
					{
						console.log(event.data.split("_"));
						$scope.$apply(function(){
							$scope.show_video = parseInt(event.data.split("_")[1]);
							if($scope.show_video == 0)
								$scope.pvideo = 0;
						});
					}
					else if(typeof event.data != "undefined" && event.data.indexOf("whiteboard") != -1 && parseInt(event.data.split("_")[2]) == <?= $_GET['pid'];?>)
					{
						console.log(event.data.split("_"));
						$scope.$apply(function(){
							$scope.show_whiteboard = parseInt(event.data.split("_")[1]);
						});
					}
					else if(typeof event.data != "undefined" && event.data.indexOf("exituser") != -1 && parseInt(event.data.split("_")[1]) == <?= $_GET['pid'];?>)
					{
						console.log(event.data.split("_"));
						allowtoleave = true;
						window.location.assign(event.data.split("_")[2]);
					}
					else if(typeof event.data != "undefined" && event.data.indexOf("exitalluser") != -1)
					{
						allowtoleave = true;
						window.location.assign(event.data.split("_")[1]);
					}
					else if(typeof event.data != "undefined" && event.data.indexOf("acktocheckuserison") != -1 && parseInt(event.data.split("_")[1]) == <?= $_GET['pid'];?>)
					{
						$scope.send_noti("imhere_"+event.data.split("_")[1]);
					}
					else if(typeof event.data != "undefined" && event.data.indexOf("maximize_") != -1)
					{
						$scope.$apply(function(){
							$scope.maximize = event.data.split("_")[1] == "true" ? true : false;
						});
					}
					<?php }else{?>
					if(typeof event.data != "undefined" && event.data.indexOf("attempttoleave_") != -1)
					{
						$timeout(function(){$scope.send_noti("acktocheckuserison_"+event.data.split("_")[1]);}, 10000);
						$scope.offine_user[event.data.split("_")[1]] = 1;
						$timeout(function(){
							$scope.check_user_is_offine(event.data.split("_")[1]);
						}, 20000);
					}
					else if(typeof event.data != "undefined" && event.data.indexOf("imhere") != -1)
					{
						delete $scope.offine_user[event.data.split("_")[1]];
					}
					<?php }?>
					
				});


				if($scope.is_admin)
				{
					OTSession.session.on('signal:joined_meeting', function (event) {
						$scope.data.maximize = $scope.maximize;
						OTSession.session.signal( 
									{  type: 'active_datas',
									   data: $scope.data
									}, 
									function(error) {
										
									});
					});
				}
				else{
					OTSession.session.on('signal:active_datas', function (event) {
						console.log(event);
						if(event.data.active_menu == "video")
						{
							$scope.$apply(function(){
								if(event.data.active_video)
								$scope.change_video(event.data.active_video, 1);
								$scope.presentation = false;
								$scope.video = true;
								$timeout(function(){
									if(event.data.video_status == 'start')
										player.playVideo();
									if(event.data.video_status == 'pause')
										player.pauseVideo();
								}, 500);
							});
						}
						else if(event.data.active_menu == "presentation")
						{
							$scope.$apply(function(){
								$scope.presentation = true;
								$scope.video = false;
								if(event.data.active_presentation.folder && event.data.active_presentation.files)
									$scope.selected_file(event.data.active_presentation.folder, event.data.active_presentation.files, 1);
								$timeout(function(){
									if(event.data.active_slide)
										$('.slider1').slick('slickGoTo', event.data.active_slide);
								}, 1000);
							});
						}

						$scope.$apply(function(){
							$scope.maximize = event.data.maximize;
						});

					});
					
					OTSession.session.on('signal:presentationControl', function (event) {
						console.log(event);
						//var currentSlide = $('.slider1').slick('slickCurrentSlide');
						$('.slider1').slick('slickGoTo', event.data.slide);
					});
					
					OTSession.session.on('signal:presentationControl', function (event) {
						console.log(event);
						//var currentSlide = $('.slider1').slick('slickCurrentSlide');
						$('.slider1').slick('slickGoTo', event.data.slide);
					});
					OTSession.session.on('signal:youtube-player', function (event) {
						console.log(event);
						player.seekTo(event.data.tm, true);
						if(event.data.st == 'start')
							player.playVideo();
						else
							player.pauseVideo();
					});

					OTSession.session.on('signal:admin-signal', function (event) {
						console.log(event);
						if(event.data.type == 'video_change')
						{
							$scope.$apply(function(){
								$scope.change_video(event.data.video, 1);
							});
						}
						else if(event.data.type == 'presentation_change')
						{
							$scope.$apply(function(){
								$scope.selected_file(event.data.folder, event.data.files, 1);
							});
						}
						else if(event.data.type == 'presentation')
						{
							$scope.$apply(function(){
								$scope.presentation = true;
								$scope.video = false;
							});
							player.pauseVideo();
						}
						else if(event.data.type == 'video')
						{
							$scope.$apply(function(){
								$scope.presentation = false;
								$scope.video = true;
							});
							player.stopVideo();
						}
						
					});
				}
				
				
				
			}])
			.value({
                apiKey: '<?= API_KEY;?>',
                sessionId: '<?= $sessionId?>',
                token: '<?= $token?>'
            })
			.directive('ngEnter', function () {
				return function (scope, element, attrs) {
					element.bind("keydown keypress", function (event) {
						if(event.which === 13) {
							scope.$apply(function (){
								scope.$eval(attrs.ngEnter);
							});
			 
							event.preventDefault();
						}
					});
				};
			});
        </script>
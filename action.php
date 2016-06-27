<script type="text/javascript" charset="utf-8">
		
		  var player;
		  var scope;
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
					scope.video_noti('start');
					break;
				  case 2:
					console.log('video paused at '+player.getCurrentTime());
					scope.video_noti('pause');
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
            .controller('MyCtrl', ['$scope', 'OTSession', 'apiKey', 'sessionId', 'token', '$timeout', '$http', function($scope, OTSession, apiKey, sessionId, token, $timeout, $http) {
                var myDataRef = new Firebase('https://vinogautam.firebaseio.com/pusher/new_user');
				$scope.chat = [];
				$scope.noti = false;
				$scope.presentation = true;
				
				$scope.youtube_list = getCookie('youtube_list') ? JSON.parse(getCookie('youtube_list')) : [];
				$scope.presentation_files = getCookie('presentation') ? JSON.parse(getCookie('presentation')) : [];
				<?php if(isset($_GET['admin'])){?>
				$scope.participants = [];
				$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=waiting_participants').then(function(res){
					$scope.participants = res['data'];
				});
				$scope.tmp_check = false;
				myDataRef.on('value', function(snapshot) {
					if($scope.tmp_check)
					{
						$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=new_participants&id='+snapshot.val().count).then(function(res){
							$scope.participants.push($scope.recent);
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
						setCookie('youtube_list', JSON.stringify($scope.youtube_list));
						$scope.newvideo = "";
					}
				};
				
				
				$scope.deletevideo = function(e, ind){
					e.stopPropagation();
					$scope.youtube_list.splice(ind,1);
					setCookie('youtube_list', JSON.stringify($scope.youtube_list));
				};
				
				<?php }?>
				
				$scope.change_video = function(p, admin){
					player.loadVideoById(p.split("?v=")[1], 0, "default");
					player.stopVideo();
					$scope.video = true;
					$scope.presentation = false;
					$scope.users=false;
					if(admin === undefined)
						$scope.signal({type: 'video_change', video: p}, true);
				};
				
				var statusRef = new Firebase('https://vinogautam.firebaseio.com/opentok/<?= $sessionId?>');
				statusRef.on('child_added', function(snapshot) {
					//angular.forEach(snapshot.val(), function(v,k){
						v = snapshot.val();
						if(v.noti)
						{
							$scope.noti = v;
							console.log("fdgdfg tert");
							$timeout(function(){
								$scope.noti = false;
							}, 3000);
						}
						else
						{
							v.hash = CryptoJS.MD5(v.email).toString();
							$scope.chat.push(v);
							$timeout(function(){
								jQuery("#messagesDiv").scrollTop(jQuery("#messagesDiv")[0].scrollHeight);
							}, 100);
							$scope.visible = true;
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
							$.post("<?php echo site_url();?>/wp-admin/admin-ajax.php?action=save_ppt", {data:request.getResponseHeader('FileUrl')}, function(data){
								if(data != 'error')
								{	
									old_data = getCookie('presentation') ? JSON.parse(getCookie('presentation')) : [];
									new_data = JSON.parse(data);
									new_data.name = filename;
									old_data.push(new_data);
									setCookie('presentation', JSON.stringify(old_data), 365);
									$scope.$apply(function(){
										$scope.presentation_files = old_data;
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
				
				$scope.send_noti = function()
				{
					console.log("noti send");
					statusRef.push({name: $scope.data.name, email: $scope.data.email, noti:true, ts: new Date().getTime()});
				};
				
				$scope.add = function(){
					statusRef.push($scope.data);
					$scope.data.msg = '';
				};
				
				$scope.video_noti = function(st){
					OTSession.session.signal( 
							{  type: 'youtube-player',
							   data: st
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
				
				<?php if(isset($_GET['admin'])){?>
				OTSession.session.on({
                    sessionConnected: function() {
						$scope.$apply(function(){$scope.slide_menu = true;});
						$('.slider1').on('afterChange', function(event, slick, currentSlide){
						  console.log(currentSlide);
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
				});
				<?php }else{?>
				OTSession.session.on('signal:presentationControl', function (event) {
					console.log(event);
					//var currentSlide = $('.slider1').slick('slickCurrentSlide');
					$('.slider1').slick('slickGoTo', event.data.slide);
				});
				OTSession.session.on('signal:youtube-player', function (event) {
					console.log(event);
					if(event.data == 'start')
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
				<?php }?>
			}])
			.value({
                apiKey: '45609232',
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
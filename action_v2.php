<script type="text/javascript" charset="utf-8">
var player;
var scope;
var myDataRef = new Firebase('https://vinogautam.firebaseio.com/pusher/new_user');
var meetingRef = new Firebase('https://vinogautam.firebaseio.com/pusher/new_meeting');
var online_status = new Firebase('https://vinogautam.firebaseio.com/pusher/online_status');
var refresh_user_list = new Firebase('https://vinogautam.firebaseio.com/pusher/refresh_user_list');

					
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
		//scope.video_noti('start', player.getCurrentTime());
		break;
	  case 2:
		console.log('video paused at '+player.getCurrentTime());
		//scope.video_noti('pause', player.getCurrentTime());
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

angular.module('instantconnect', ['opentok', 'opentok-whiteboard'])
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
		jQuery(".chat-mothed").scrollTop(jQuery(".chat-mothed")[0].scrollHeight);
    }, 1000);
}
}
}
})
.filter('to_trusted', ['$sce', function($sce){
	return function(text) {
		return $sce.trustAsHtml(text);
	};
}])
.filter('trustAsResourceUrl', ['$sce', function($sce) {
    return function(val) {
        return $sce.trustAsResourceUrl(val);
    };
}])
.controller('MyCtrl', ['$scope', 'OTSession', 'apiKey', 'sessionId', 'token', '$timeout', '$http', '$interval', '$filter', '$rootScope', function($scope, OTSession, apiKey, sessionId, token, $timeout, $http, $interval, $filter, $rootScope) {

	$scope.tabs = [];
	$scope.current_tab = -1;

	$scope.add_tab = function(type, name, data)
	{
		$scope.tabs.push({type:type, name:name, data:data});
		$scope.current_tab = $scope.tabs.length-1;

		$scope.initiatescripts();
	};

	$scope.tab_type_length = function(type, id)
	{
		var len = 0;
		angular.forEach($scope.tabs, function(v,k){
			if(v.type == type)
			{
				if(id === undefined)
				{
					len++;
				}
				else if(v.data.id == id)
				{
					len++;
				}
			}
		});
		return len;
	};

	$scope.short_text = function(txt, len){

        var tmp = document.createElement("DIV");
        tmp.innerHTML = txt;
        txt = tmp.textContent || tmp.innerText || "";

        if(txt === undefined) return;

        if(txt.length > len)
        {
            ind = txt.indexOf(" ", len);
            if(ind == -1 || ind - len > 10)
                return txt.substr(0, len+10)+'...';
            else
                return txt.substr(0, ind)+'...';
        }
        else
            return txt;
    };

	$scope.randomid = function()
	{
		return new Date().getTime()+''+(Math.floor(Math.random()*90000) + 10000);
	};

	$scope.initiatescripts = function()
	{
		$timeout(function(){
			$.AdminLTE.layout.fix();

			//White board pencil tool
	        if($('.pencil').length)
	        {
	        	$('.pencil').toolbar({
		              content: '#toolbar-options',
		              position: 'top',
		              adjustment: 28,
		              event: 'click',
		             hideOnClick: true,	
		              style: 'dark'

		        });

		        $('.pencil').on('toolbarItemClick',
		              function( event, buttonClicked ) {
		                  buttonClickedID = buttonClicked.id;
		                    console.log("BUTTON: " + buttonClickedID);
		                    switch (buttonClickedID) {
		                        case 'pencil-tool':
		                            $(".pencil-tool-fa").removeClass("fa-eraser");
		                            $(".pencil-tool-fa").addClass("fa-pencil");
		                            
		                            break;
		                        case 'eraser-tool':
		                             $(".pencil-tool-fa").removeClass("fa-pencil");
		                             $(".pencil-tool-fa").addClass("fa-eraser");
		                             
		                            break;
		                    }

		                    $("toolbar-options").addClass("hidden");
		              }
		        );
	        }
	        
	        if($(".range-slider").length)
	        {
	        	$(".range-slider img").click(function(){
		            $(".range").toggle();
		        });
	        }
	        
	        if($(".tab-inner-div").length)
	        {
	        	$(".tab-inner-div").height($(".meeting-pane").height()-40);
	        }

		}, 100);
	};

	$scope.set_tab = function(id)
	{
		if($scope.current_tab != -1 && ($scope.tabs[$scope.current_tab].type == 'presentation' || $scope.tabs[$scope.current_tab].type == 'whiteboard'))
		{
			$scope.broadcast();
			$timeout(function(){
				$scope.current_tab = id;
				$scope.initiatescripts();
			},500);
		}
		else
		{
			$scope.current_tab = id;
			$scope.initiatescripts();
		}
	};

	$scope.remove_tab = function(id)
	{
		if($scope.current_tab != -1 && ($scope.tabs[$scope.current_tab].type == 'presentation' || $scope.tabs[$scope.current_tab].type == 'whiteboard'))
		{
			$scope.broadcast();

			$timeout(function(){
				$scope.tabs.splice(id,1);
				$scope.current_tab = -1;
				$scope.initiatescripts();
			}, 500);
		}
		else
		{
			$scope.tabs.splice(id,1);
			$scope.current_tab = -1;
			$scope.initiatescripts();
		}
	};

	$scope.parseInt = function(id)
	{
		return parseInt(id);
	};

	$scope.broadcast = function()
	{
		$rootScope.$broadcast('get_image_data', {ind:$scope.current_tab, tab:$scope.tabs[$scope.current_tab]});
	};

	$rootScope.$on('Presentation_changed', function(event, data){
		if($scope.tabs[$scope.current_tab].type == 'presentation')
        {    
        	if(!$scope.$$phase) {
        		$scope.$apply(function(){
	        		$scope.tabs[$scope.current_tab].slide_image[$scope.tabs[$scope.current_tab].currentpresentationindex] = data;
	        	});
        	}
        	else
        	{
        		$scope.tabs[$scope.current_tab].slide_image[$scope.tabs[$scope.current_tab].currentpresentationindex] = data;
        	}
        }
	});

	$rootScope.$on('Whiteboard_changed', function(event, data){
		if(data.tab.type == 'whiteboard')
        {
        	if(!$scope.$$phase) {
        		$scope.$apply(function(){
	        		$scope.tabs[data.ind].slide_image = data.image;
	        	});
        	}
        	else
        	{
        		$scope.tabs[data.ind].slide_image = data.image;
        	}
        }
	});

	

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

	$scope.currentPage = 0;
    $scope.vsearch = {name:''};
    $scope.psearch = {name:''};
    $scope.reset = function()
    {
    	$scope.currentPage = 0;
    	$scope.vsearch = {name:''};
    	$scope.psearch = {name:''};
    };
		
	$scope.addnew_video = function(){
		if($scope.newvideo.url.split("/embed/").length == 2)
            $scope.newvideo.url = "https://www.youtube.com/embed/"+$scope.newvideo.url.split("/embed/")[1];
        else if($scope.newvideo.url.split("?v=").length == 2)
            $scope.newvideo.url = "https://www.youtube.com/embed/"+$scope.newvideo.url.split("?v=")[1];
        else
            return;

		if($scope.newvideo)
		{
			$http.post('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=addnew_video', $scope.newvideo).then(function(res){
				$scope.newvideo.id = $scope.randomid();
				$scope.youtube_list.push($scope.newvideo);
				$scope.newvideo = {};
			});

			$scope.reset();
		}
	};

	$scope.numberOfPages=function(arr, search){
        return Math.ceil($filter('filter')($scope[arr], $scope[search]).length/5);                
    };

    $scope.numberOfPagesArray=function(arr, search){
        return new Array($scope.numberOfPages(arr, search));                
    };

    $scope.connected = false;
	OTSession.init(apiKey, sessionId, token, function (err) {
		if (!err) {
			$scope.$apply(function () {
				$scope.connected = true;
			});
		}
	});
	$scope.streams = OTSession.streams;
	$scope.screenshare = OTSession.screenshare;

	$scope.initiate_screen_sharing = function(){
		OTSession.initiate_screenshring();
	};

	$scope.trigger_draw_image = function()
	{
		$timeout(function(){
			$(".presentation-thumbs ul li:eq("+$scope.parseInt($scope.tabs[$scope.current_tab].currentpresentationindex)+")").trigger("click");
		}, 500);
	};

	$scope.trigger_draw_whiteboard_image = function()
	{
		$timeout(function(){
			$(".draw_whiteboard").trigger("click");
		}, 500);
	};

	$scope.thumb_position = function()
	{
		height = jQuery(".presentation-thumbs ul")[0].scrollHeight/$scope.tabs[$scope.current_tab].data.files.length;
		jQuery(".presentation-thumbs ul").scrollTop(height*$scope.parseInt($scope.tabs[$scope.current_tab].currentpresentationindex));
	};

    $scope.reset_value = function()
    {
    	if($scope.tabs[$scope.current_tab].slide_image[$scope.tabs[$scope.current_tab].currentpresentationindex] === undefined)
        {    
        	return [];
        }
        else
        {
        	return $scope.tabs[$scope.current_tab].slide_image[$scope.tabs[$scope.current_tab].currentpresentationindex];
        }
    };

	$scope.getvideobyID = function(url)
	{
		if(url.split("/embed/").length == 2)
            return url.split("/embed/")[1];
        else if(url.split("?v=").length == 2)
            return url.split("?v=")[1];
        else
            return;
	};

	$scope.deletevideo = function(e, ind){
		e.stopPropagation();
		$scope.youtube_list.splice(ind,1);
		$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=delete_video&ind='+ind).then(function(){

		});
	};

	$scope.deletepresentation = function(e, ind){
		e.stopPropagation();
		$scope.presentation_files.splice(ind,1);
		$http.get('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=delete_presentation&ind='+ind).then(function(){

		});
	};

	/*Chat section starts here*/
	$scope.chat = [];

	var statusRef = new Firebase('https://vinogautam.firebaseio.com/opentok/<?= $sessionId?>');
	statusRef.on('child_added', function(snapshot) {
		v = snapshot.val();
		if(typeof v.msg != "undefined")
		{
			hn = v.email ? v.email : v.name;
			if(!$scope.$$phase) {
				$scope.$apply(function(){
					$scope.insert_chat_byid(v);
					$scope.visible = true;
				});
			}
			else
			{
				$scope.insert_chat_byid(v);
				$scope.visible = true;
			}
		}
	});

	$scope.insert_chat_byid = function(msg){

		var a = {id: "", time: "", msg:[]};
		if($scope.chat.length == 0)
		{
			$scope.chat.push({id: msg.id, time: msg.time, msg:[msg]});
		}
		else if($scope.chat[$scope.chat.length-1].id == msg.id)
		{
			$scope.chat[$scope.chat.length-1].id = msg.id;
			$scope.chat[$scope.chat.length-1].time = msg.time;
			$scope.chat[$scope.chat.length-1].msg.push(msg);
		}
		else
		{
			$scope.chat.push({id: msg.id, time: msg.time, msg:[msg]});
		}
	};
	$id = new Date().getTime()+''+Math.round(Math.random()*100000);
	$scope.data2 = {id:$id, name: 'user'+$id, email: 'user'+$id+'@gmai.com', msg:''};

	
	$timeout(function(){
		$(".chat-mothed").height($(window).height()-200);
	}, 3000);
	$scope.add = function(){
		if(!$scope.data2.msg)
			return;
		$scope.data2.time = new Date().getTime();
		statusRef.push($scope.data2);
		$scope.data2.msg = '';
		
	};

	$scope.send_noti = function(data)
	{
		data.time = new Date().getTime();
		OTSession.session.signal( 
		{  type: 'user-notifications',
		   data: data
		}, 
		function(error) {
			if (error) {
			  console.log("signal error ("+ error.code + "): " + error.message);
			} else {
			  console.log("signal sent.");
			}
		});
	};

	$scope.typinguser = {};
	OTSession.session.on('signal:user-notifications', function (event) {
		if(event.data.type == 'usertyping')
		{
			$scope.$apply(function(){
				if(event.data.data.id != $scope.data2.id)
				{
					if($scope.typinguser[event.data.data.id] === undefined)
						$scope.typinguser[event.data.data.id] = {id:event.data.data.id, time:event.data.time, name:event.data.data.name};
					else
						$scope.typinguser[event.data.data.id].time = event.data.time;
				}
			});
			jQuery(".control-sidebar").addClass("control-sidebar-open");
			$timeout(function () {
		        jQuery(".chat-mothed").scrollTop(jQuery(".chat-mothed")[0].scrollHeight);
		    }, 1000);
		}
		else if(event.data.type == 'show_video')
		{
			$scope.$apply(function(){
				$scope.show_video = event.data.data;
			});
		}
	});


	$interval(function(){
			angular.forEach($scope.typinguser, function(v,k){
				console.log(v);
				if(new Date().getTime() - v.time > 3000)
					delete $scope.typinguser[k];
			});
	}, 3000);
	$(".close-icon").click(function(){
		jQuery(".control-sidebar").removeClass("control-sidebar-open");
	});
	$scope.size = function(obj)
	{
		return Object.size(obj);
	};

	$scope.show_video = false;
	/*Chat end here*/


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
		$(".upload-preload .upload_percentage").text("0%");
		$(".upload-preload .progress-bar").width("0%");
		$(".upload-preload .file-name").text(filename);
		$(".upload-preload").removeClass("hide");

		$.ajax({
			url: "https://do.convertapi.com/PowerPoint2Image",
			type: "POST",
			data: formData,
			processData: false,
			contentType: false,
			success: function(response, textStatus, request) {

				$(".upload-preload .upload_percentage").text("50%");
				$(".upload-preload .progress-bar").width("50%");

				$http.post("<?php echo site_url();?>/wp-admin/admin-ajax.php?action=save_ppt&name="+filename, {data:request.getResponseHeader('FileUrl')}).then(function(data){
					if(data['data'] != 'error')
					{	
						new_data = data['data'];
						new_data.name = filename;
						new_data.id = $scope.randomid();
						$scope.presentation_files.push(new_data);
						$scope.add_tab('presentation', new_data.name, new_data);

						$(".upload-preload .upload_percentage").text("100%");
						$(".upload-preload .progress-bar").width("100%");
						
						$timeout(function(){
							$(".upload-preload").addClass("hide");
						}, 500);
					}
				});
			},
			error: function(jqXHR) {
				alert("Error in file conversion");
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
})
.filter('startFrom', function() {
    return function(input, start) {
        start = +start; //parse to int
        return input.slice(start);
    }
})
.filter('unique', function() {
   return function(collection, keyname) {
      var output = [], 
          keys = [];

      angular.forEach(collection, function(item) {
          var key = item[keyname];
          if(keys.indexOf(key) === -1) {
              keys.push(key);
              output.push(item);
          }
      });

      return output;
   };
});

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};
</script>
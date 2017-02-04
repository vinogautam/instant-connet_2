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
		              event: 'hover',
		             
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
	        
	        if($("#youtube-player").length)
	        {
	        	$("#youtube-player").height($(".meeting-pane").height()-40);
	        }

		}, 100);
	};

	$scope.set_tab = function(id)
	{
		$scope.current_tab = id;
		$scope.initiatescripts();
	};

	$scope.parseInt = function(id)
	{
		return parseInt(id);
	};

	$rootScope.$on('Whiteboard_changed', function(event, data){
		if($scope.tabs[$scope.current_tab].type == 'presentation')
        {    
        	$scope.$apply(function(){
        		$scope.tabs[$scope.current_tab].slide_image[$scope.tabs[$scope.current_tab].currentpresentationindex] = data;
        	});
        }
	})

	$scope.remove_tab = function(id)
	{
		$scope.tabs.splice(id,1);
		$scope.current_tab = -1;
	};

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
			console.log($scope.newvideo);

			$http.post('<?php echo site_url();?>/wp-admin/admin-ajax.php?action=addnew_video', $scope.newvideo).then(function(res){
				$scope.youtube_list = res['data'];
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

	$scope.getvideobyID = function(url)
	{
		if(url.split("/embed/").length == 2)
            return url.split("/embed/")[1];
        else if(url.split("?v=").length == 2)
            return url.split("?v=")[1];
        else
            return;
	};

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

				$.post("<?php echo site_url();?>/wp-admin/admin-ajax.php?action=save_ppt&name="+filename, {data:request.getResponseHeader('FileUrl')}, function(data){
					if(data != 'error')
					{	
						new_data = JSON.parse(data);
						new_data.name = filename;
						$scope.$apply(function(){
							$scope.presentation_files.push(new_data);
							$scope.selected_file(new_data.folder, new_data.files);
						});

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
});
</script>
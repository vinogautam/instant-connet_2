 <?php
/*
Template Name: Meeting Template
*/
?>
<?php
$sessionId = $_GET['sessionId']; 
$token = $_GET['token'];
 ?>
<!DOCTYPE html>
<html ng-app="demo">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>OpenTok-Angular Demo</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="<?= plugin_dir_url(__FILE__); ?>css/slick.css">
		<link rel="stylesheet" type="text/css" href="<?= plugin_dir_url(__FILE__); ?>css/slick-theme.css">
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" href="<?= plugin_dir_url(__FILE__); ?>css/opentok-whiteboard.css" type="text/css" media="screen" charset="utf-8">
		
		<style type="text/css" media="screen">
            .slick-prev::before, .slick-next::before {
				color: black;
			}
			.slick-slide{text-align:center;}
			.slick-slide img{display:inline-block;max-width:100%;}
			.OT_publisher, .OT_subscriber {
				height: 200px !important;
				position: relative !important;
				width: 100% !important;
				left:0 !important;
			}
			.OT_widget-container{border-radius:5%;}
			header{background-color:#790303;width:100%; height:40px;margin-bottom:15px;position:relative;}
			header .fa{font-size:16px;width:16px;height:16px;position:absolute;margin:auto;top:0;bottom:0;color:#fff;}
			header .fa.fa-bars{left:1%;}
			header .fa.fa-times{right:1%;}
			.overall_container{height:500px;}
			.side_menu li.selected{background-color:#790303;border:1px solid #790303;}
			.side_menu{position:absolute;top:40px;bottom:0;left:0;background-color:#890101;z-index:99;}
			.side_menu ul{
				color: #fff;
				list-style: outside none none;
				margin: 0;
				padding: 0;
			}
			.side_menu ul input{color:#000;}
			.side_menu ul li{padding:15px;position:relative;}
			ot-whiteboard {
                display: block;
                width: 100%;
                height:400px;
                position: absolute;
                left: 0;
                right: 0;
				z-index:11;
            }
			.slider1{margin-bottom:20px;z-index:10;}
			.sub_menu{position:absolute;left:100%;display:none;top:0;background-color:#A13535;width:300px;padding:10px 0 ;}
			.side_menu ul li:hover .sub_menu{display:block;}
			.sub_menu h3{margin:0;font-size:16px;background-color:#790303;padding: 16px 5px;}
			.sub_menu input[type='text']{background:none;border:none;border-bottom:1px solid #fff;width:100%;}
			.sub_menu ul{margin:20px 0;}
			.client_view .OT_panel{display:none;}
			.side_menu button{color:#790303;}
         </style>
		 <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">
    </head>
    <body ng-controller="MyCtrl" class="<?= isset($_GET['admin']) ? 'admin_view' : 'client_view'; ?>">
        
		<div class="container-fluid">
			<div class="row">
				<header>
					<i class="fa fa-bars"></i>
					<i class="fa fa-times"></i>
				</header>
			</div>
			<?php if(isset($_GET['admin'])){?>
			<div class="side_menu" ng-show="slide_menu">
				<ul>
					<li>Fi</li>
					<li ng-class="{selected:presentation}">
						<i class="fa fa-desktop"  ng-click="presentation=true;users=false;video=false;signal('presentation');"></i>
						<div class="sub_menu">
							<h3>Presentations</h3>
							<span><input ng-model="psearch"></span>
							<ul>
								<li ng-repeat="p in presentation_files | filter:psearch track by $index" ng-click="selected_file(p.folder, p.files)">{{p.name}}</li>
							</ul>
							<div class="menu_bottom">
								<input id="convert_ppt" type="file" >
							</div>
						</div>
					</li>
					<li ng-class="{selected:video}" >
						<i class="fa fa-youtube-play" ng-click="presentation=false;users=false;video=true;signal('video');"></i>
						<div class="sub_menu">
							<h3>Youtube</h3>
							<span><input ng-model="vsearch.name"></span>
							<ul>
								<li ng-repeat="p in youtube_list | filter:vsearch track by $index " ng-click="change_video(p.url)">{{p.name}} <i ng-click="deletevideo($event, $index)" class="fa fa-trash"></i></li>
							</ul>
							<span><input ng-model="newvideo.name" placeholder="title"><input ng-model="newvideo.url" placeholder="url"><button ng-click="addnew_video()">Add</button></span>
						</div>
					</li>
					<li ng-class="{selected:users}">
						<i class="fa fa-user" ng-click="presentation=false;users=true;video=false;"></i>
					</li>
					
				</ul>
			</div>
			<?php }?>
			<div class="row">
				<div class="col-sm-12 col-md-3 col-lg-3" ng-cloak>
					<div class="video_container" >
						<ot-layout props="{animate:true}">
							<ot-subscriber ng-repeat="stream in streams" 
								stream="stream" 
								props="{style: {nameDisplayMode: 'off'}}">
							</ot-subscriber>
							<ot-publisher id="publisher" 
								props="{style: {nameDisplayMode: 'off'}, resolution: '500x300', frameRate: 30}">
							</ot-publisher>
						</ot-layout>
					</div>
					<div class="text_chat_container" ng-class="{visible:visible}">
						<div id="messagesDiv" style="height:250px;overflow:auto;">
							<p ng-repeat="c in chat" ng-class="{align_right: c.email != data.email}">
								<img ng-if="c.email == data.email" ng-src="http://www.gravatar.com/avatar/{{c.hash}}/?s=30"> 
								{{c.msg}}
								<img ng-if="c.email != data.email" ng-src="http://www.gravatar.com/avatar/{{c.hash}}/?s=30"> 
							</p>
						</div>
						<p ng-show="noti">{{noti.name}} is typing...<p>
						<form>
							<input size="43" type="text" ng-model="data.name" placeholder="Name">
							<input size="43" type="text" ng-model="data.email" placeholder="Email">
							<textarea rows="2" cols="33" ng-model="data.msg" ng-keyup="send_noti()" placeholder="Message" ng-enter="add();"></textarea>
							<button ng-click="add();">Post</button>
						</form>
					</div>
				</div>
				<div class="col-sm-12 col-md-9 col-lg-9">
					<div class="row">
						<div class="col-sm-12 col-md-12 col-lg-12 overall_container">
							<div class="presentation_container" ng-show="presentation">
								<ot-whiteboard width="1280" height="720"></ot-whiteboard>
								<section class=" slider1">
									<?php for($i=1;$i<=6;$i++){?>
									<div>
									  <img src="http://placehold.it/700x400?text=<?= $i?>">
									</div>
									<?php }?>
								</section>
							</div>
							<?php if(isset($_GET['admin'])){?>
							  <section class=" slider2" ng-show="presentation">
								<?php for($i=1;$i<=6;$i++){?>
								<div>
								  <img src="http://placehold.it/100x150?text=<?= $i?>">
								</div>
								<?php }?>
							  </section>
							<?php }?>
							
							<iframe ng-show="video" <?php if(!isset($_GET['admin'])){?>style="pointer-events:none;"<?php }?> id="youtube-player" width="640" height="360" src="//www.youtube.com/embed/geTgZcHrXTc?enablejsapi=1&version=3&playerapiid=ytplayer" frameborder="0" allowfullscreen="true" allowscriptaccess="always"></iframe>
							
							<div ng-show="users">
								<ul>
									<li ng-repeat="part in participants" ng-click="selected(part.id)" ng-class="{selected:check_selected(part.id)}" ng-init="part.diff = part.diff === undefined ? 0 : part.diff; autotimer(part);">
										
										<img ng-src="{{get_avatar(part)}}">
										#{{part.id}} {{part.name}} 
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
        <script src="https://code.jquery.com/jquery-2.2.0.min.js" type="text/javascript" charset="utf-8"></script>
        
		
		<script src="<?= plugin_dir_url(__FILE__); ?>js/slick.js" type="text/javascript" charset="utf-8"></script>
		  
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/paper.js/0.9.25/paper-core.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="//static.opentok.com/v2.6/js/opentok.js" type="text/javascript" charset="utf-8"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" type="text/javascript" charset="utf-8"></script>
		<script src="<?= plugin_dir_url(__FILE__); ?>js/opentok-layout.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= plugin_dir_url(__FILE__); ?>js/opentok-angular.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= plugin_dir_url(__FILE__); ?>js/opentok-whiteboard.js" type="text/javascript" charset="utf-8"></script>
        <script src="https://www.youtube.com/iframe_api"></script>
		<script src='https://cdn.firebase.com/js/client/2.2.1/firebase.js'></script>
		<script src='https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/core.js'></script>
		<script src='https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/md5-min.js'></script>
        <?php include 'action.php';?>
    </body>
</html>
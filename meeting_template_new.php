<?php

if(isset($_GET['only_video']))
{
  include 'only_video.php';
  exit;
}

$meeting_id = $_GET['id'];

global $wpdb; $results = $meeting = $wpdb->get_row("select * from ".$wpdb->prefix . "meeting where id=".$meeting_id);
$sessionId = $meeting->session_id; 
$token = $meeting->token;
if (!isset($_GET['admin']) && (!isset($_GET['finonce']) || !wp_verify_nonce($_GET['finonce'], 'finonce'))) {
  die("Invalid meeting url");
}
 ?>
 <!DOCTYPE html>
<!--
Instant Connect UI
-->
<html ng-app="demo">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Financial Insiders Meeting Room</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.6 -->
  <link rel="stylesheet" href="<?= plugin_dir_url(__FILE__); ?>bootstrap/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= plugin_dir_url(__FILE__); ?>dist/css/instantconnect.min.css">
  
  <link rel="stylesheet" href="<?= plugin_dir_url(__FILE__); ?>dist/css/skins/skin-red.min.css">

  <link rel="stylesheet" type="text/css" href="<?= plugin_dir_url(__FILE__); ?>css/slick.css">
  <link rel="stylesheet" type="text/css" href="<?= plugin_dir_url(__FILE__); ?>css/slick-theme.css">
  <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
  <link rel="stylesheet" href="<?= plugin_dir_url(__FILE__); ?>css/opentok-whiteboard.css" type="text/css" media="screen" charset="utf-8">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <style type="text/css" media="screen">
            .slick-prev::before, .slick-next::before {
        color: black;
      }
      .slick-slide{text-align:center;}
      .slick-slide img{display:inline-block;max-width:100%;}
      .video1 .OT_publisher, .video1 .OT_subscriber {
        height: 100% !important;
        position: relative !important;
        width: 100% !important;
        left:0 !important;
      }
      .video2 .OT_publisher, .video2 .OT_subscriber {
        height: 50% !important;
        left: 0 !important;
          margin: 0 1% !important;
          position: relative !important;
          top: 0 !important;
          width: 45% !important;
      }
      .video_container{position: relative;height: 200px;margin-left: 40px;}
      .video_container.maximize{position: fixed;width: 100%;top:0;left:0;height: 100%;z-index:1000;margin-left: 0;}
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
      .client_view .presentation_container{pointer-events:none;}
      .client_view .show_whitebord_1.presentation_container{pointer-events:auto;}
      .client_view .OT_panel{display:none;}
      .client_view .show_whitebord_1 .OT_panel{display:block;}
      .side_menu button{color:#790303;}
      .align_right{text-align: right;}
      .preloader_overlay{position: fixed;width: 100%;height: 100%;background: #000;opacity: 0.5;z-index: 9999999;}
      .preloader_image{position: fixed;width: 150px;height: 150px;left: 0;right: 0;margin: auto;top:0;bottom: 0;z-index: 99999999;}
         </style>
</head>

<body class="hold-transition skin-red sidebar-collapse sidebar-mini instant-connect <?= isset($_GET['admin']) ? 'admin_view' : 'client_view'; ?>" ng-controller="MyCtrl" >

<div class="preloader hide preloader_overlay"></div>
<img class="preloader hide preloader_image" src="<?= plugin_dir_url(__FILE__); ?>8.gif">

<div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Select a page to redirect user</h4>
            </div>
            <div class="modal-body">
              <select ng-model="selected_page"  ng-init="selected_page=''">
            <option value="">Select Page</option>
            <?php foreach (get_pages() as $key => $value) {?>
            <option value="<?= get_permalink($value->ID);?>"><?= $value->post_title;?></option>
            <?php }?>
          </select>
            </div>
            <div class="modal-footer">
              <button ng-show="exit_user != 'all'" type="button" class="btn btn-primary" ng-click="exit_user_page(4)" data-dismiss="modal">End Session</button>
              <button ng-show="exit_user != 'all'" type="button" class="btn btn-primary" ng-click="exit_user_page(2)" data-dismiss="modal">Only Redirect</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>

        </div>
      </div>

      <div id="userleftmodal" class="modal fade" role="dialog">
        <div class="modal-dialog">

          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">User {{left_user}} left the meeting</h4>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>

        </div>
      </div>

      <audio id="notification_audio" controls style="display:none;">
      <source src="<?php _e(IC_PLUGIN_URL);?>notification.mp3" type="audio/mpeg">
      Your browser does not support the audio tag.
    </audio>

<div class="wrapper">

  <?php if(isset($_GET['admin'])){?>
  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->
    <a href="index2.html" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><img src="<?= plugin_dir_url(__FILE__); ?>dist/img/fi-logo-mini.png"/></span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg">Meeting Room</span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->
          <li class="dropdown">
            <a ng-click="initiate_screen_sharing();">Share screen</a>
          </li>
          <li class="dropdown">
            <a><i class="fa fa-sign-out" ng-click="exit_user = 'all'" data-toggle="modal" data-target="#myModal"></i></a>
          </li>
          <li class="dropdown">
            <a>
            <i ng-hide="show_video" ng-click="show_video = 1;" class="fa fa-times1">Enable Admin video</i>
            <i ng-show="show_video" ng-click="show_video = 0;" class="fa fa-times1">Disable Admin video</i>
            </a>
          </li>
          <li class="dropdown messages-menu">
            <!-- Menu toggle button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-users"></i>
              <span class="label label-success">4</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 4 messages</li>
              <li>
                <!-- inner menu: contains the messages -->
                <ul class="menu">
                  <li><!-- start message -->
                    <a href="#">
                      <div class="pull-left">
                        <!-- User Image -->
                        <img src="<?= plugin_dir_url(__FILE__); ?>dist/img/user2-160x160.jpg" class="img-circle" alt="User Image">
                      </div>
                      <!-- Message title and timestamp -->
                      <h4>
                        Support Team
                        <small><i class="fa fa-clock-o"></i> 5 mins</small>
                      </h4>
                      <!-- The message -->
                      <p>Why not buy a new awesome theme?</p>
                    </a>
                  </li>
                  <!-- end message -->
                </ul>
                <!-- /.menu -->
              </li>
              <li class="footer"><a href="#">See All Messages</a></li>
            </ul>
          </li>
          <!-- /.messages-menu -->

          <!-- Notifications Menu -->
          <li class="dropdown notifications-menu">
            <!-- Menu toggle button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-bell-o"></i>
              <span class="label label-warning">10</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 10 notifications</li>
              <li>
                <!-- Inner Menu: contains the notifications -->
                <ul class="menu">
                  <li><!-- start notification -->
                    <a href="#">
                      <i class="fa fa-users text-aqua"></i> 5 new members joined today
                    </a>
                  </li>
                  <!-- end notification -->
                </ul>
              </li>
              <li class="footer"><a href="#">View all</a></li>
            </ul>
          </li>
          <!-- Tasks Menu -->
          <li class="dropdown tasks-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-flag-o"></i>
              <span class="label label-danger">9</span>
            </a>
            <ul class="dropdown-menu">
              <li class="header">You have 9 tasks</li>
              <li>
                <!-- Inner menu: contains the tasks -->
                <ul class="menu">
                  <li><!-- Task item -->
                    <a href="#">
                      <!-- Task title and progress text -->
                      <h3>
                        Design some buttons
                        <small class="pull-right">20%</small>
                      </h3>
                      <!-- The progress bar -->
                      <div class="progress xs">
                        <!-- Change the css width attribute to simulate progress -->
                        <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                          <span class="sr-only">20% Complete</span>
                        </div>
                      </div>
                    </a>
                  </li>
                  <!-- end task item -->
                </ul>
              </li>
              <li class="footer">
                <a href="#">View all tasks</a>
              </li>
            </ul>
          </li>
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              <img src="<?= plugin_dir_url(__FILE__); ?>dist/img/neil-avatar.jpg" class="user-image" alt="User Image">
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs">Neil Thomas</span>
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                <img src="<?= plugin_dir_url(__FILE__); ?>dist/img/neil-avatar.jpg" class="img-circle" alt="User Image">

                <p>
                  Alexander Pierce - Web Developer
                  <small>Member since Nov. 2012</small>
                </p>
              </li>
              <!-- Menu Body -->
              <li class="user-body">
                <div class="row">
                  <div class="col-xs-4 text-center">
                    <a href="#">Followers</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Sales</a>
                  </div>
                  <div class="col-xs-4 text-center">
                    <a href="#">Friends</a>
                  </div>
                </div>
                <!-- /.row -->
              </li>
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="#" class="btn btn-default btn-flat">Profile</a>
                </div>
                <div class="pull-right">
                  <a href="#" class="btn btn-default btn-flat">Sign out</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li>
            <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!-- Left side column. contains the logo and sidebar -->
  
  <aside class="main-sidebar instant-connect-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

      <!-- Sidebar user panel (optional) -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="<?= plugin_dir_url(__FILE__); ?>dist/img/neil-avatar.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>Neil Thomas</p>
          <!-- Status -->
          <a href="#"><i class="fa fa-circle text-success"></i>In meeting</a>

        </div>
      </div>

     

      <!-- Sidebar Menu -->
      <ul class="sidebar-menu">
        <li class="header">Meeting Room Controls</li>
        <!-- Optionally, you can add icons to the links -->
        <li ng-click="presentation=true;users=false;video=false;signal('presentation');data.active_menu='presentation'"><a href="#" data-toggle="modal" data-target="#presentationsModal"><i class="fa ion-easel"></i> <span>Presentations</span></a></li>
        <li ng-click="presentation=false;users=false;video=true;signal('video');data.active_menu='video'"><a href="#" data-toggle="modal" data-target="#youtubeModal"><i class="fa fa-youtube-play"></i> <span>Videos</span></a></li>
        
        <li class="treeview">
          <a href="#"><i class="fa fi-logo"></i> <span class="folio">Financial Folios</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
              <li><a href="#">Financial Planning</a></li>
              <li><a href="#">Insurance Planning</a></li>
              <li><a href="#">Retirement Planning</a></li>
              <li><a href="#">Investment Planning</a></li>
          </ul>
        </li>
        <li><a href="#"><i class="fa fa-question-circle"></i> <span>Help</span></a></li>
      </ul>
      <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
  </aside>
  <?php }?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
  


    <!-- Main content -->
    <section class="content">

      <div class="col-sm-12 col-md-3 col-lg-3" ng-cloak>
          <div style="background:#000;" ng-show="streams.length || show_video" class="video_container {{pvideo}}" ng-class="{video1:streams.length+pvideo == 1, video2:streams.length+pvideo > 1, maximize:maximize}">
            <ot-layout props="{animate:true}">
              <ot-subscriber ng-repeat="stream in streams" 
                stream="stream" 
                props="{style: {nameDisplayMode: 'off'}}">
              </ot-subscriber>
              <ot-publisher ng-if="show_video" id="publisher" 
                props="{style: {nameDisplayMode: 'off'}, resolution: '500x300', frameRate: 30}">
              </ot-publisher>
            </ot-layout>
            <?php if(isset($_GET['admin'])){?>
            <i ng-click="maximize=!maximize;send_noti('maximize_'+maximize)" class="fa fa-arrows-alt" style="position: absolute; right: 5px; bottom: 5px; font-size: 20px; color: rgb(255, 255, 255);"></i>
            <?php }?>
          </div>
          <div style="background:#000;" ng-show="screenshare.length" class="maximize">
            <ot-layout props="{animate:true}">
              <ot-subscriber ng-repeat="screenshare in streams" 
                stream="stream" 
                props="{style: {nameDisplayMode: 'off'}}">
              </ot-subscriber>
            </ot-layout>
          </div>
          <div style="margin-left: 40px;" class="text_chat_container" ng-class="{visible:visible}">
            <div id="messagesDiv" style="height:250px;overflow:auto;">
              <p ng-repeat="c in chat" on-finish-render ng-class="{align_right: c.email != data2.email}" ng-if="c.msg">
                <img ng-if="c.email == data2.email" ng-src="http://www.gravatar.com/avatar/{{c.hash}}/?s=30"> 
                <span ng-bind-html="urlify(c.msg) | to_trusted"></span>
                <img ng-if="c.email != data2.email" ng-src="http://www.gravatar.com/avatar/{{c.hash}}/?s=30"> 
                <hr>
              </p>
            </div>
            <p ng-show="noti">{{noti.name}} is typing...<p>
            <form>
              <input size="43" type="hidden" ng-model="data2.name" placeholder="Name">
              <input size="43" type="hidden" ng-model="data2.email" placeholder="Email">
              <textarea rows="2" cols="33" ng-model="data2.msg" placeholder="Message" ng-enter="add();"></textarea>
              <button ng-click="add();">Post</button>
            </form>
          </div>
        </div>
        <div class="col-sm-12 col-md-9 col-lg-9">
          <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12 overall_container">
              <div class="presentation_container show_whitebord_{{show_whiteboard}}" ng-show="presentation" >
                <ot-whiteboard  width="1280" height="720"></ot-whiteboard>
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
              
              
            </div>
          </div>
        </div>


    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
      Anything you want
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; 2016 <a href="#">Company</a>.</strong> All rights reserved.
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
      <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
      <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
      <!-- Home tab content -->
      <div class="tab-pane active" id="control-sidebar-home-tab">
        <h3 class="control-sidebar-heading">Recent Activity</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript::;">
              <i class="menu-icon fa fa-birthday-cake bg-red"></i>

              <div class="menu-info">
                <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                <p>Will be 23 on April 24th</p>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

        <h3 class="control-sidebar-heading">Tasks Progress</h3>
        <ul class="control-sidebar-menu">
          <li>
            <a href="javascript::;">
              <h4 class="control-sidebar-subheading">
                Custom Template Design
                <span class="pull-right-container">
                  <span class="label label-danger pull-right">70%</span>
                </span>
              </h4>

              <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
              </div>
            </a>
          </li>
        </ul>
        <!-- /.control-sidebar-menu -->

      </div>
      <!-- /.tab-pane -->
      <!-- Stats tab content -->
      <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
      <!-- /.tab-pane -->
      <!-- Settings tab content -->
      <div class="tab-pane" id="control-sidebar-settings-tab">
        <form method="post">
          <h3 class="control-sidebar-heading">General Settings</h3>

          <div class="form-group">
            <label class="control-sidebar-subheading">
              Report panel usage
              <input type="checkbox" class="pull-right" checked>
            </label>

            <p>
              Some information about this general settings option
            </p>
          </div>
          <!-- /.form-group -->
        </form>
      </div>
      <!-- /.tab-pane -->
    </div>
  </aside>
  <!-- /.control-sidebar -->
  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
</div>


<!-- ./wrapper -->
<!--MODAL WINDOWS -->

<!-- Presentation Library Modal -->
<div id="presentationsModal" class="ICModalWindow modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa ion-easel"></i> Presentation Library</h4>
      </div>
      <div class="modal-body">
              <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Please select your presentation</h3>

              <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 200px;">
                  <input type="text" name="table_search" class="form-control pull-right" placeholder="Search">

                  <div class="input-group-btn">
                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                
                <tr ng-repeat="p in presentation_files | filter:psearch track by $index">
                  <td><img src="<?= plugin_dir_url(__FILE__); ?>dist/img/ppt-thumb.jpg" width="60" /></td>
                  <td>{{p.name}}</td>
                
                  <td>
                    <a class="btn btn-app modal-app-btn" data-toggle="tooltip" data-placement="bottom" data-animation="delay 2" title="Remove"><i class="fa fa-trash"></i></a>
                    <a ng-click="selected_file(p.folder, p.files)" class="btn btn-app modal-app-btn" data-toggle="tooltip" data-placement="bottom" title="Open" data-dismiss="modal"><i class="fa fa-angle-double-right"></i></a>
                  </td>
                </tr>

                
                
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>

      <div class="box-footer clearfix">
              <ul class="pagination pagination-sm no-margin pull-right">
                <li><a href="#">«</a></li>
                <li><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">»</a></li>
              </ul>
            </div>
          


            
      </div>
      <div class="hide upload-preload row no-margin">

      <div class="col-xs-2 file-loader"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>
      <div class="col-xs-10"><span class="file-name">Presentation 1.ppt</span> <span class="converting">Converting...</span> <span class="label label-danger pull-right">70%</span>
      <div class="progress progress-xxs">
                <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
      </div>
      </div>
      </div>
      <div class="modal-footer">
          
          <div class="col-xs-4 no-pad">
          <input style="opacity: 0;" id="convert_ppt" type="file" >
          <button style="position: absolute;pointer-events: none;top:0;" type="button" class="btn btn-red" data-dismiss="modal" data-toggle="tooltip" data-placement="top" title="Microsoft PowerPoint files accepted only">Upload Presentation</button>
          </div>
          <div class="col-xs-4 col-xs-push-4 close-btn"><button type="button" class="btn btn-default no-margin-right" data-dismiss="modal">Close</button></div>
        
      </div>
    </div>

  </div>
</div>
<!-- End Presentation Modal -->


<!-- Youtube Video Library Modal -->
<div id="youtubeModal" class="ICModalWindow modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa fa-youtube-play"></i> Video Library</h4>
      </div>
      <div class="modal-body">
              <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Please select your YouTube Video</h3>

              <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 200px;">
                  <input ng-model="vsearch.name" type="text" name="table_search" class="form-control pull-right" placeholder="Search">

                  <div class="input-group-btn">
                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                
                <tr ng-repeat="p in youtube_list | filter:vsearch track by $index ">
                  <td><img src="<?= plugin_dir_url(__FILE__); ?>dist/img/ppt-thumb.jpg" width="60" /></td>
                  <td>{{p.name}}</td>
                
                  <td>
                    <a ng-click="deletevideo($event, $index)" class="btn btn-app modal-app-btn" data-toggle="tooltip" data-placement="bottom" data-animation="delay 2" title="Remove"><i class="fa fa-trash"></i></a>
                    <a ng-click="change_video(p.url)" class="btn btn-app modal-app-btn" data-toggle="tooltip" data-placement="bottom" title="Play Video" data-dismiss="modal"><i class="fa fa-youtube-play"></i></a>
                  </td>
                </tr>

                
                
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>

      <div class="box-footer clearfix">
              <ul class="pagination pagination-sm no-margin pull-right">
                <li><a href="#">«</a></li>
                <li><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">»</a></li>
              </ul>
            </div>
          


            
      </div>
      

      <div class="add-video-container">
        <div class="video-close">&times;</div>
        <form class="form-horizontal ic-video-form">
        <div class="form-group">
                  <label for="videoURL" class="col-sm-3 control-label">Video URL:</label>

                  <div class="col-sm-9">
                    <input ng-model="newvideo.url" type="text" class="form-control" id="videoURL" placeholder="Video URL">
                  </div>
                </div>

        <div class="form-group">
                  <label for="videoName" class="col-sm-3 control-label">Video Name:</label>

                  <div class="col-sm-9">
                    <input ng-model="newvideo.name" type="text" class="form-control" id="videoName" placeholder="Video Name">
                  </div>
                </div>

        </form>
        
               
                <button ng-click="addnew_video()" class="btn btn-red pull-right">Add Video</button>
              
      
      </div>

      <div class="modal-footer">
          
          <div class="col-xs-4 no-pad"><button type="button" class="btn btn-red add-video-btn">Add Video</button></div>
          <div class="col-xs-4 col-xs-push-4 close-btn"><button type="button" class="btn btn-default no-margin-right" data-dismiss="modal">Close</button></div>
        
      </div>
    </div>

  </div>
</div>
<!-- End Presentation Modal -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 2.2.3 -->
<script src="<?= plugin_dir_url(__FILE__); ?>plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?= plugin_dir_url(__FILE__); ?>bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= plugin_dir_url(__FILE__); ?>dist/js/app.min.js"></script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
<script type="text/javascript">
  var extensionId = 'nedllccjjngfnljgploibnpkikgmmfkc';
  var ffWhitelistVersion;
</script>
<script src="<?= plugin_dir_url(__FILE__); ?>js/slick.js" type="text/javascript" charset="utf-8"></script> 
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular.min.js" type="text/javascript" charset="utf-8"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/paper.js/0.9.25/paper-core.min.js" type="text/javascript" charset="utf-8"></script>
<script src="//static.opentok.com/v2/js/opentok.js" type="text/javascript" charset="utf-8"></script>
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

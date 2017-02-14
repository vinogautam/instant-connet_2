<?php

if(isset($_GET['only_video']))
{
  include 'only_video.php';
  exit;
}

if(isset($_GET['dev']))
{
  $ot = opentok_token();
  $sessionId = $ot['sessionId']; 
  $token = $ot['token'];
}
else
{
  $meeting_id = $_GET['id'];

  global $wpdb; $results = $meeting = $wpdb->get_row("select * from ".$wpdb->prefix . "meeting where id=".$meeting_id);
  $sessionId = $meeting->session_id; 
  $token = $meeting->token;
  if (!isset($_GET['admin']) && (!isset($_GET['finonce']) || !wp_verify_nonce($_GET['finonce'], 'finonce'))) {
    die("Invalid meeting url");
  }
}
?>
<!DOCTYPE html>
<!--
Instant Connect UI
-->
<html ng-app="instantconnect">
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
  <!-- Toolbar styles -->
  <link rel="stylesheet" href="<?= plugin_dir_url(__FILE__); ?>dist/v2/css/jquery.toolbar.css">

  <!-- Theme style -->
  <link rel="stylesheet" href="<?= plugin_dir_url(__FILE__); ?>dist/v2/css/instantconnect.min.css">
  
  <link rel="stylesheet" href="<?= plugin_dir_url(__FILE__); ?>dist/v2/css/skins/skin-red.min.css">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->
  <link rel="stylesheet" href="<?= plugin_dir_url(__FILE__); ?>css/opentok-whiteboard.css" type="text/css" media="screen" charset="utf-8">
  <style type="text/css">
    .meet-icon li{background: url(<?= plugin_dir_url(__FILE__); ?>dist/v2/img/meet-icons.jpg);}
    

    /*Need to add this styes in less*/
    ot-whiteboard {display: block;width: 100%;height:100%;position: absolute;left: 0;right: 0;z-index:11;}
    .meet-icon{ padding: 0; text-align: center; margin-top: 12%; }
    .meet-icon li, .meet-icon li a{ list-style-type: none;  width:142px; height: 142px;  margin:12px;
      background-repeat: no-repeat; display: inline-block;}
    .meet-icon .presen-img{ background-position: -32px -33px;  }
    .meet-icon .presen-img:hover{ background-position: -32px -180px;  }
    .meet-icon .screen-share{background-position: -185px -33px;}
    .meet-icon .screen-share:hover{background-position: -185px -180px;}
    .meet-icon .whith-board{background-position: -339px -33px; border-bottom:1px solid #ededed;}
    .meet-icon .whith-board:hover{background-position: -339px -180px;}
    .meet-icon .youtube{background-position: -491px -33px; border-top:1px solid #ededed;}
    .meet-icon .youtube:hover{background-position: -491px -180px;}

    .pane-footer{z-index:9999;}
    .absolute_center{position:absolute;left:0;right:0;top:0;bottom:0;margin:auto;}
    .img_whm100{width:auto;height:auto;max-width:100%;max-height:100%;}
    .wh100{width:100%;height:100%;}
    .w100{width:100%;}
    .h100{height:100%;}
    .instant-connect .meeting-panel-container .meeting-pane .presentation-thumbs ul li.active img{outline:2px solid #790303}
    .instant-connect .meeting-panel-container .meeting-pane .presentation-thumbs ul li.active p span{background:#790303;color:#fff;padding:1px 20px 4px;border-radius:20px}
    .tabnavigation{position: absolute;right: 0;}
    .tabnavigation i{margin: 0 5px;}
    /*End here*/
  </style>


</head>

<body class="hold-transition skin-red sidebar-collapse sidebar-mini instant-connect <?= isset($_GET['admin']) ? 'admin_view' : 'client_view'; ?>" ng-controller="MyCtrl">
<div class="wrapper">

  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->
    <a href="index2.html" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><img src="<?= plugin_dir_url(__FILE__); ?>dist/v2/img/fi-logo-mini.png"/></span>
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
                        <img src="<?= plugin_dir_url(__FILE__); ?>dist/v2/img/user2-160x160.jpg" class="img-circle" alt="User Image">
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
          <img src="<?= plugin_dir_url(__FILE__); ?>dist/v2/img/neil-avatar.jpg" class="img-circle" alt="User Image">
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
        <li><a href="#" data-toggle="modal" data-target="#presentationsModal"><i class="fa ion-easel"></i> <span>Presentations</span></a></li>
        <li><a href="#" data-toggle="modal" data-target="#youtubeModal"><i class="fa fa-youtube-play"></i> <span>Videos</span></a></li>
        
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

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    


    <!-- Main content -->
    <section class="content">

      <!-- Start Video and group chat -->
     <div class="row">
     <div class="col-xs-12 col-md-3 col-sm-4 video-chat">
        
        <div class="video-container agent">
            <div class="video-agent">
            <img src="<?= plugin_dir_url(__FILE__); ?>dist/v2/img/agent-video-mock-up.jpg" class="img-responsive"/>

                <div class="agent-name">Agent Name <span class="designations">C.F.C</span></div>
            </div>
         
        </div> 

        <div class="client-videos-container">
           
              <!--CLIENT VIDEO -->
              <div class="col-xs-4 video-container">
                  <div class="video">
                  </div>
              </div>
              <!-- END CLIENT VIDEO -->

              <!-- CLIENT VIDEO -->
              <div class="col-xs-4 video-container">
                  <div class="video">
                  </div>
              </div>
              <!-- END CLIENT VIDEO -->


              <!-- CLIENT VIDEO -->
              <div class="col-xs-4 video-container">
                  <div class="video">
                  </div>
              </div>
              <!-- END CLIENT VIDEO -->

              <!-- CLIENT VIDEO -->
              <div class="col-xs-4 video-container">
                  <div class="video">
                  </div>
              </div>
              <!-- END CLIENT VIDEO -->



              
          
        </div>
      <!-- Group Chat -- >
       <!-- Construct the box with style you want. Here we are using box-danger -->
<!-- Then add the class direct-chat and choose the direct-chat-* contexual class -->
<!-- The contextual class should match the box, so we are using direct-chat-danger -->

<div class="chat-container">
<div class="direct-chat">
 
  <div class="box-body">
    <!-- Conversations are loaded here -->
    <div class="direct-chat-messages">
      <!-- Message. Default to the left -->
      <div class="direct-chat-msg">
        <div class="direct-chat-info clearfix">
          <span class="direct-chat-name pull-left">Alexander Pierce</span>
          <span class="direct-chat-timestamp pull-right">23 Jan 2:00 pm</span>
        </div><!-- /.direct-chat-info -->
        <img class="direct-chat-img" src="<?= plugin_dir_url(__FILE__); ?>dist/v2/img/user1-128x128.jpg" alt="message user image"><!-- /.direct-chat-img -->
        <div class="direct-chat-text">
          Is this template really for free? That's unbelievable!
        </div><!-- /.direct-chat-text -->
      </div><!-- /.direct-chat-msg -->

      <!-- Message to the right -->
      <div class="direct-chat-msg right">
        <div class="direct-chat-info clearfix">
          <span class="direct-chat-name pull-right">Sarah Bullock</span>
          <span class="direct-chat-timestamp pull-left">23 Jan 2:05 pm</span>
        </div><!-- /.direct-chat-info -->
        <img class="direct-chat-img" src="<?= plugin_dir_url(__FILE__); ?>dist/v2/img/user3-128x128.jpg" alt="message user image"><!-- /.direct-chat-img -->
        <div class="direct-chat-text">
          You better believe it!
        </div><!-- /.direct-chat-text -->
      </div><!-- /.direct-chat-msg -->

      <!-- Message. Default to the left -->
      <div class="direct-chat-msg">
        <div class="direct-chat-info clearfix">
          <span class="direct-chat-name pull-left">Alexander Pierce</span>
          <span class="direct-chat-timestamp pull-right">23 Jan 2:00 pm</span>
        </div><!-- /.direct-chat-info -->
        <img class="direct-chat-img" src="<?= plugin_dir_url(__FILE__); ?>dist/v2/img/user1-128x128.jpg" alt="message user image"><!-- /.direct-chat-img -->
        <div class="direct-chat-text">
          Is this template really for free? That's unbelievable!
        </div><!-- /.direct-chat-text -->
      </div><!-- /.direct-chat-msg -->

      <!-- Message to the right -->
      <div class="direct-chat-msg right">
        <div class="direct-chat-info clearfix">
          <span class="direct-chat-name pull-right">Sarah Bullock</span>
          <span class="direct-chat-timestamp pull-left">23 Jan 2:05 pm</span>
        </div><!-- /.direct-chat-info -->
        <img class="direct-chat-img" src="<?= plugin_dir_url(__FILE__); ?>dist/v2/img/user3-128x128.jpg" alt="message user image"><!-- /.direct-chat-img -->
        <div class="direct-chat-text">
          You better believe it!
        </div><!-- /.direct-chat-text -->
      </div><!-- /.direct-chat-msg -->
      <!-- Message. Default to the left -->
      <div class="direct-chat-msg">
        <div class="direct-chat-info clearfix">
          <span class="direct-chat-name pull-left">Alexander Pierce</span>
          <span class="direct-chat-timestamp pull-right">23 Jan 2:00 pm</span>
        </div><!-- /.direct-chat-info -->
        <img class="direct-chat-img" src="<?= plugin_dir_url(__FILE__); ?>dist/v2/img/user1-128x128.jpg" alt="message user image"><!-- /.direct-chat-img -->
        <div class="direct-chat-text">
          Is this template really for free? That's unbelievable!
        </div><!-- /.direct-chat-text -->
      </div><!-- /.direct-chat-msg -->

      <!-- Message to the right -->
      <div class="direct-chat-msg right">
        <div class="direct-chat-info clearfix">
          <span class="direct-chat-name pull-right">Sarah Bullock</span>
          <span class="direct-chat-timestamp pull-left">23 Jan 2:05 pm</span>
        </div><!-- /.direct-chat-info -->
        <img class="direct-chat-img" src="<?= plugin_dir_url(__FILE__); ?>dist/v2/img/user3-128x128.jpg" alt="message user image"><!-- /.direct-chat-img -->
        <div class="direct-chat-text">
          You better believe it!
        </div><!-- /.direct-chat-text -->
      </div><!-- /.direct-chat-msg -->
    </div><!--/.direct-chat-messages-->



  
  </div><!-- /.box-body -->
  
    <div class="input-group">
      <input type="text" name="message" placeholder="Type Message ..." class="form-control">
      <span class="input-group-btn">
        <button type="button" class="btn btn-danger btn-flat">Send</button>
      </span>
    </div>
  <!-- /.box-footer-->
</div><!--/.direct-chat -->
</div>
      

      </div>
<!-- End Video and Group Chat -->

<!--  MEETING ROOM WINDOWS -->
<div class="col-xs-12 col-sm-9 meeting-panel-container" ng-init="tabindex=0">
    <div class="meeting-panel row">
        
        <div class="col-xs-12 panel-header no-pad">
        <div ng-click="set_tab(-1);" class="home-label">Start</div>
        <ul>
            <li ng-repeat="tab in tabs track by $index" ng-class="{active:current_tab == $index}" ng-show="$index >= tabindex && $index <= tabindex+4"><a ng-click="set_tab($index);">{{short_text(tab.name, 10)}} <span ng-click="$event.stopPropagation();remove_tab($index);" class="close-window">&times;</span></a></li>
        </ul>
        <span ng-if="tabs.length > 4" class="tabnavigation">
          <i ng-show="tabindex" ng-click="$parent.tabindex = $parent.tabindex-1" class="fa fa-arrow-left"></i>
          <i ng-show="tabindex < tabs.length-5" ng-click="$parent.tabindex = $parent.tabindex+1" class="fa fa-arrow-right"></i>
        </span>
        </div>
 

    <div class="tab-content">

      <div class="tab-pane active" ng-if="current_tab == -1" >
        <div class="col-xs-12 no-pad meeting-pane">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
              <ul class="meet-icon">
                <li class="presen-img"><a href="#" data-toggle="modal" data-target="#presentationsModal"></a></li>
                <li class="screen-share">
                  <a ng-hide="tab_type_length('screenshare');" ng-click="add_tab('screenshare', 'Screen Share');" href="#"></a>
                  <a ng-show="tab_type_length('screenshare');" href="#"></a>
                </li>
                <li class="whith-board">
                  <a ng-click="add_tab('whiteboard', 'WhiteBoard');" href="#"></a>
                </li>
                <li class="youtube"><a href="#" data-toggle="modal" data-target="#youtubeModal"></a></li>
              </ul>
            </div>
        </div>
      </div>
      <div class="tab-pane" ng-repeat="tab in tabs track by $index" ng-if="current_tab != -1 && current_tab == $index" ng-class="{active:current_tab == $index}" ng-init="tab.index = $index">
         <?php include 'elements/tab_content.php';?>
      </div>

    </div>

    </div>
   </div>



<!-- END MEETING ROOM WINDOWS -->




    </section>
    <!-- /.content -->
  </div>
  <!-- END MEETING ROOM -->
    <!-- /.content-wrapper -->
 <footer class="main-footer">
    
    <strong>Powered by Agent Online</strong>
  </footer>
  </div>





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
<?php include 'elements/video_modal.php';?>
<?php include 'elements/presentation_modal.php';?>
<!-- REQUIRED JS SCRIPTS -->

<script type="text/javascript">
  var extensionId = 'pfobnffdhkcjnpmcgjmfdgbgcpaijbpd';
  var ffWhitelistVersion;
</script>
<!-- jQuery 2.2.3 -->
<script src="<?= plugin_dir_url(__FILE__); ?>plugins/jQuery/jquery-2.2.3.min.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular.min.js" type="text/javascript" charset="utf-8"></script>

<!-- TOOL TIP -->
<script src="<?= plugin_dir_url(__FILE__); ?>dist/v2/js/jquery.toolbar.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?= plugin_dir_url(__FILE__); ?>/bootstrap/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= plugin_dir_url(__FILE__); ?>dist/v2/js/app.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/paper.js/0.9.25/paper-core.min.js" type="text/javascript" charset="utf-8"></script>
<script src="//static.opentok.com/v2/js/opentok.js" type="text/javascript" charset="utf-8"></script>
<script src="<?= plugin_dir_url(__FILE__); ?>js/opentok-layout.js" type="text/javascript" charset="utf-8"></script>
<script src="<?= plugin_dir_url(__FILE__); ?>js/opentok-angular.js" type="text/javascript" charset="utf-8"></script>
<script src="<?= plugin_dir_url(__FILE__); ?>js/opentok-whiteboard.js" type="text/javascript" charset="utf-8"></script>

<script src='https://cdn.firebase.com/js/client/2.2.1/firebase.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/core.js'></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/components/md5-min.js'></script>

<?php include 'action_v2.php';?>
<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. Slimscroll is required when using the
     fixed layout. -->
</body>
</html>

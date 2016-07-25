<?php
$meeting_id = $_GET['id'];

global $wpdb; $results = $meeting = $wpdb->get_row("select * from ".$wpdb->prefix . "meeting where id=".$meeting_id);
$sessionId = $meeting->session_id; 
$token = $meeting->token;
?>
<!DOCTYPE html>
<html ng-app="demo">
    <head>
        <meta http-equiv="Content-type" content="text/html; charset=utf-8">
        <title>OpenTok-Angular Demo</title>
         <style type="text/css" media="screen">
             ot-publisher,ot-subscriber,ot-layout {
                 display: block;
                 overflow: hidden;
             }
             ot-layout {
                 position: absolute;
                 top: 0;
                 left: 0;
                 right: 0;
                 bottom: 0;
             }
         </style>
    </head>
    <body>
        <div ng-controller="MyCtrl">
            <ot-layout props="{animate:true}">
                <ot-subscriber ng-repeat="stream in streams" 
                    stream="stream" 
                    props="{style: {nameDisplayMode: 'off'}}">
                </ot-subscriber>
            </ot-layout>
        </div>
        
        <script src="//static.opentok.com/v2/js/opentok.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="https://code.jquery.com/jquery-1.12.3.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.5.5/angular.min.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= plugin_dir_url(__FILE__); ?>js/opentok-layout.js" type="text/javascript" charset="utf-8"></script>
        <script src="<?= plugin_dir_url(__FILE__); ?>js/opentok-angular.js" type="text/javascript" charset="utf-8"></script>

        <script type="text/javascript" charset="utf-8">
            angular.module('demo', ['opentok'])
            .controller('MyCtrl', ['$scope', 'OTSession', 'apiKey', 'sessionId', 'token', function($scope, OTSession, apiKey, sessionId, token) {
                OTSession.init(apiKey, sessionId, token);
                $scope.streams = OTSession.streams;
            }]).value({
                apiKey: '<?= API_KEY;?>',
                sessionId: '<?= $sessionId?>',
                token: '<?= $token?>'
            });
        </script>
    </body>
</html>
<!-- WHITEBOARD WINDOW -->
<div ng-if="tab.type == 'whiteboard'" class="col-xs-12 no-pad meeting-pane whiteboardtab" ng-init="tab.slide_image = tab.slide_image === undefined ? [] : tab.slide_image;trigger_draw_whiteboard_image();">
  <div class="hide clear_whiteboard" ng-click="clear((is_admin || full_control));"></div>
 <div class="hide draw_whiteboard" ng-click="draw_image(tab.slide_image, (is_admin || full_control));"></div>
 <div class="col-sm-12 no-pad wh100 tab-inner-div">
    <ot-whiteboard  width="700" height="420"></ot-whiteboard>
 </div>

 <div class="pane-footer col-xs-12" ng-show="is_admin || full_control || whiteboard_control">
   
   <div class="col-sm-10 col-lg-12 col-md-10 col-xs-12 whiteboard-tools no-pad">
   <div class="col-sm-3 no-pad" ng-show="is_admin || full_control">
      <div ng-click="remove_tab(tab.index);" class="clos-pre">Close Whiteboard</div>
   </div>


   <div class="col-sm-9 col-xs-12 tool no-pad pull-right">
   <ul>
      <div id="toolbar-options" class="hidden">
            <a ng-click="erasing=false;" href="#" id="pencil-tool"><i class="fa fa-pencil"></i></a>
            <a ng-click="erasing=true;" href="#" id="eraser-tool"><i class="fa fa-eraser"></i></a>   
      </div>
      <li class="pencil"><div class="pen"><i class="pencil-tool-fa fa fa-pencil"></i></div>
       
      </li> 
      <li class="position-change">
        <ul>
           <li><a ng-click="clear();" href="#" id="eraser-tool"><i class="fa fa-trash"></i></a></li>
           <li ng-click="undo()"><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/tarn-left.png"></li>
           <li ng-click="redo()"><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/tarn--right.png"></li>
        </ul>
      </li>
      <li class="range-slider"><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/bar.png">
      <div class="range"><input ng-change="send_noti({type:'lineWidth', val: lineWidth});" ng-model="lineWidth" type="range" min="0" max="10" orient="vertical" /></div>
     </li>
     <li class="color-picker">
       <ul>
          <li ng-class="{active: color=='yellow'}" ng-click="color='yellow';send_noti({type:'color', val: color});" class="yellow"></li>
          <li ng-class="{active: color=='black'}" ng-click="color='black';send_noti({type:'color', val: color});" class="block"></li>
          <li ng-class="{active: color=='white'}" ng-click="color='white';send_noti({type:'color', val: color});" class="wight"></li>
          <li ng-class="{active: color=='red'}" ng-click="color='red';send_noti({type:'color', val: color});" class="red"></li>
          <li ng-class="{active: color=='blue'}" ng-click="color='blue';send_noti({type:'color', val: color});" class="blue"></li>
      </ul>
     </li>
    
   </ul>
 </div>
</div>

 </div>

</div>
<!-- END WHITEBOARD WINDOW -->

<!-- PRESENTATION WINDOW -->
<div ng-if="tab.type == 'presentation'" class="col-xs-12 no-pad meeting-pane presentation-room thumbs-active" ng-init="tab.currentpresentationindex=tab.currentpresentationindex===undefined ? '0' : tab.currentpresentationindex;tab.hidethumbs= tab.hidethumbs===undefined ? false : tab.hidethumbs;tab.slide_image = tab.slide_image === undefined ? {} : tab.slide_image;trigger_draw_image();">
    <div class="hide clear_whiteboard" ng-click="clear((is_admin || full_control));"></div>
    <div ng-class="{'col-xs-12':tab.hidethumbs || (!is_admin && !full_control), 'col-xs-10': !tab.hidethumbs && (is_admin || full_control)}" class="col-xs-10 presentation-room presentation-room-inner tab-inner-div no-pad h100">
      <ot-whiteboard  width="700" height="420"></ot-whiteboard>
      <img ng-src="{{'<?= IC_PLUGIN_URL; ?>/extract/'+tab.data.folder+'/file-page'+ (parseInt(tab.currentpresentationindex) + 1)+'.jpg'}}" class="img-responsive absolute_center img_whm100">
    </div>

    <div ng-hide="tab.hidethumbs || (!is_admin && !full_control)" class="col-xs-2 presentation-thumbs no-pad">

        <ul>
          <li ng-repeat="img in tab.data.files | orderBy:'':false" ng-class="{active:tab.currentpresentationindex==''+$index+''}" ng-click="tab.currentpresentationindex=''+$index+'';clear();draw_image(reset_value(), (is_admin || full_control));send_noti({type:'currentpresentationindex', current_tab:current_tab, ind: tab.currentpresentationindex});"><img ng-src="{{'<?= IC_PLUGIN_URL; ?>/extract/'+tab.data.folder+'/'+img}}" class="img-responsive"><p><span>Page {{$index+1}}</span></p></li>
           </ul> 
    </div>
  <div class="pane-footer col-xs-12" ng-show="is_admin || full_control || whiteboard_control">
     <div class="col-sm-12 col-xs-12 whiteboard-tools no-pad">
     <div class="col-sm-3 no-pad" ng-show="is_admin || full_control"> 
     <div ng-click="remove_tab(tab.index);" class="clos-pre">Close Presentation</div>
     </div>

     <div class="col-sm-4 no-pad pagination" ng-show="is_admin || full_control">
     <div ng-hide="parseInt(tab.currentpresentationindex)==0" class="per" ng-click="tab.currentpresentationindex=''+(parseInt(tab.currentpresentationindex)-1)+'';clear();draw_image(reset_value(), (is_admin || full_control));thumb_position();send_noti({type:'currentpresentationindex', current_tab:current_tab, ind: tab.currentpresentationindex});"><i class="fa fa-arrow-left" aria-hidden="true"></i> PREV</div>
     <div class="page-number">
      <select ng-change="clear();draw_image(reset_value(), (is_admin || full_control));thumb_position();send_noti({type:'currentpresentationindex', current_tab:current_tab, ind: tab.currentpresentationindex});" ng-model="tab.currentpresentationindex">
        <option ng-repeat="img in tab.data.files" value="{{$index}}">Page {{$index+1}} of {{tab.data.files.length}}</option>
      </select>
     </div>
     <div ng-hide="parseInt(tab.currentpresentationindex)==tab.data.files.length-1" class="next" ng-click="tab.currentpresentationindex=''+(parseInt(tab.currentpresentationindex)+1)+'';clear();draw_image(reset_value(), (is_admin || full_control));thumb_position();send_noti({type:'currentpresentationindex', current_tab:current_tab, ind: tab.currentpresentationindex});">NEXT <i class="fa fa-arrow-right" aria-hidden="true"></i></div>
     </div>

     <div class="col-sm-5 no-pad">
     <div class="tool">
     <ul>
      <div id="toolbar-options" class="hidden">
            <a ng-click="erasing=false;" href="#" id="pencil-tool"><i class="fa fa-pencil"></i></a>
            <a ng-click="erasing=true;" href="#" id="eraser-tool"><i class="fa fa-eraser"></i></a>   
      </div>
      <li class="pencil"><div class="pen"><i class="pencil-tool-fa fa fa-pencil"></i></div>
       
      </li>
   
      <li class="position-change">
        <ul>
           <li><a ng-click="clear();" href="#" id="eraser-tool"><i class="fa fa-trash"></i></a></li>
           <li ng-click="undo()"><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/tarn-left.png"></li>
           <li ng-click="redo()"><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/tarn--right.png"></li>
        </ul>
      </li>
      <li class="range-slider"><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/bar.png">
      <div class="range"><input ng-model="lineWidth" type="range" min="0" max="10" orient="vertical" /></div>
     </li>
     <li class="color-picker">
       <ul>
          <li ng-click="color='yellow';" class="yellow"></li>
          <li ng-click="color='black';" class="block"></li>
          <li ng-click="color='white';" class="wight"></li>
          <li ng-click="color='red';" class="red"></li>
          <li ng-click="color='blue';" class="blue"></li>
      </ul>
     </li>
      <li ng-click="tab.hidethumbs=!tab.hidethumbs;" class="tip-option">
        <img ng-if="tab.hidethumbs" src="<?= IC_PLUGIN_URL; ?>dist/v2/img/theme.png" data-toggle="tooltip" data-placement="top" title="Show Thumbs">
        <img ng-if="!tab.hidethumbs" src="<?= IC_PLUGIN_URL; ?>dist/v2/img/theme.png" data-toggle="tooltip" data-placement="top" title="Hide Thumbs">
      </li>
   </ul>
   </div>
 </div>
 </div>



  </div>
</div> 
<!-- END PRESENTATION WINDOW -->

<!-- SCREEN SHARE WINDOW -->
<div ng-if="tab.type == 'screenshare'" class="col-xs-12 no-pad meeting-pane">
    <div class="col-sm-12 col-xs-12 no-pad wh100 tab-inner-div" ng-init="initiate_screen_sharing();">
        <ot-layout props="{animate:true}">
          <ot-subscriber ng-repeat="screenshare in streams" 
            stream="stream" 
            props="{style: {nameDisplayMode: 'off'}}">
          </ot-subscriber>
        </ot-layout>
    </div>
     <div class="pane-footer whiteboard-tools col-xs-12">
        <div class="col-sm-3 no-pad"> 
          <div ng-click="remove_tab(tab.index);" class="clos-pre">Close Screen share</div>
        </div>
     </div>
</div>
   
<!-- END SCREENSHARE WINDOW -->

<!-- YOUTUBE VIDEO WINDOW -->
<div ng-if="tab.type == 'youtube'" class="col-xs-12 no-pad  meeting-pane ">
    <div class="col-sm-12 col-xs-12 no-pad wh100 tab-inner-div">
      <script src="https://www.youtube.com/iframe_api"></script>
      <iframe class="wh100" <?php if(!isset($_GET['admin'])){?>style="pointer-events:none;"<?php }?> id="youtube-player" width="640" height="360" ng-src="{{'//www.youtube.com/embed/'+getvideobyID(tab.data.url)+'?enablejsapi=1&version=3&playerapiid=ytplayer' | trustAsResourceUrl}}" frameborder="0" allowfullscreen="true" allowscriptaccess="always"></iframe>
    </div>
     <div class="pane-footer whiteboard-tools col-xs-12" ng-show="is_admin || full_control">
        <div class="col-sm-3 no-pad"> 
          <div ng-click="remove_tab(tab.index);" class="clos-pre">Close Youtube Video</div>
        </div>
     </div>
</div>

<!-- END YOUTUBE WINDOW -->
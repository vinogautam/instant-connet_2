<!-- WHITEBOARD WINDOW -->
<div ng-if="tab.type == 'whiteboard'" class="col-xs-12 no-pad meeting-pane">
 
 <div class="col-sm-12 no-pad wh100">
    <ot-whiteboard  width="700" height="420"></ot-whiteboard>
 </div>

 <div class="pane-footer col-xs-12">
   
   <div class="col-sm-10 col-lg-12 col-md-10 col-xs-12 whiteboard-tools no-pad">
   <div class="col-sm-3 no-pad">
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
           <li ng-click="undo()"><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/tarn-left.png"></li>
           <li ng-click="redo()"><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/tarn--right.png"></li>
        </ul>
      </li>
      <li class="range-slider"><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/bar.png">
      <div class="range"><input ng-model="lineWidth" type="range" orient="vertical" /></div>
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
    
   </ul>
 </div>
</div>

 </div>

</div>
<!-- END WHITEBOARD WINDOW -->

<!-- PRESENTATION WINDOW -->
<div ng-if="tab.type == 'presentation'" class="col-xs-12 no-pad meeting-pane presentation-room thumbs-active" ng-init="tab.currentpresentationindex=tab.currentpresentationindex===undefined ? '0' : tab.currentpresentationindex;tab.hidethumbs= tab.hidethumbs===undefined ? false : tab.hidethumbs;tab.slide_image = tab.slide_image === undefined ? {} : tab.slide_image;">
    <div ng-class="{'col-xs-12':tab.hidethumbs, 'col-xs-10': !tab.hidethumbs}" class="col-xs-10 presentation-room no-pad h100">
      <ot-whiteboard  width="700" height="420"></ot-whiteboard>
      <img ng-src="{{'<?= IC_PLUGIN_URL; ?>/extract/'+tab.data.folder+'/'+tab.data.files[tab.currentpresentationindex]}}" class="img-responsive absolute_center img_whm100">
    </div>

    <div ng-hide="hidethumbs" class="col-xs-2 presentation-thumbs no-pad">

        <ul>
          <li ng-repeat="img in tab.data.files" ng-class="{active:tab.currentpresentationindex==''+$index+''}" ng-click="tab.currentpresentationindex=''+$index+''"><img ng-src="{{'<?= IC_PLUGIN_URL; ?>/extract/'+tab.data.folder+'/'+img}}" class="img-responsive"><p><span>Page {{$index+1}}</span></p></li>
           </ul> 
    </div>
  <div class="pane-footer col-xs-12">
     <div class="col-sm-12 col-xs-12 whiteboard-tools no-pad">
     <div class="col-sm-3 no-pad"> 
     <div ng-click="remove_tab(tab.index);" class="clos-pre">Close Presentation</div>
     </div>

     <div class="col-sm-4 no-pad pagination">
     <div ng-hide="parseInt(tab.currentpresentationindex)==0" class="per" ng-click="tab.currentpresentationindex=''+(parseInt(tab.currentpresentationindex)-1)+'';"><i class="fa fa-arrow-left" aria-hidden="true"></i> PREV</div>
     <div class="page-number">
      <select ng-model="tab.currentpresentationindex">
        <option ng-repeat="img in tab.data.files" value="{{$index}}">Page {{$index+1}} of {{tab.data.files.length}}</option>
      </select>
     </div>
     <div ng-hide="parseInt(tab.currentpresentationindex)==tab.data.files.length-1" class="next" ng-click="tab.currentpresentationindex=''+(parseInt(tab.currentpresentationindex)+1)+'';">NEXT <i class="fa fa-arrow-right" aria-hidden="true"></i></div>
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
           <li ng-click="undo()"><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/tarn-left.png"></li>
           <li ng-click="redo()"><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/tarn--right.png"></li>
        </ul>
      </li>
      <li class="range-slider"><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/bar.png">
      <div class="range"><input ng-model="lineWidth" type="range" orient="vertical" /></div>
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
 Screen Share
   <div class="pane-footer col-xs-12">test</div>
</div>
   
<!-- END SCREENSHARE WINDOW -->

<!-- YOUTUBE VIDEO WINDOW -->
<div ng-if="tab.type == 'youtube'" class="col-xs-12 no-pad  meeting-pane ">
    <div class="col-sm-12 col-xs-12 no-pad wh100">
      <script src="https://www.youtube.com/iframe_api"></script>
      <iframe class="wh100" <?php if(!isset($_GET['admin'])){?>style="pointer-events:none;"<?php }?> id="youtube-player" width="640" height="360" ng-src="{{'//www.youtube.com/embed/'+getvideobyID(tab.data.url)+'?enablejsapi=1&version=3&playerapiid=ytplayer' | trustAsResourceUrl}}" frameborder="0" allowfullscreen="true" allowscriptaccess="always"></iframe>
    </div>
     <div class="pane-footer whiteboard-tools col-xs-12">
        <div class="col-sm-3 no-pad"> 
          <div ng-click="remove_tab(tab.index);" class="clos-pre">Close Youtube Video</div>
        </div>
     </div>
</div>

<!-- END YOUTUBE WINDOW -->
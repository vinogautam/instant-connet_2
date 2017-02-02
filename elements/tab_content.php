<!-- WHITEBOARD WINDOW -->
<div ng-if="tab.type == 'whiteboard'" class="col-xs-12 no-pad meeting-pane">
 
 <div class="col-sm-12 no-pad">
    <ot-whiteboard  width="700" height="420"></ot-whiteboard>
 </div>

 <div class="pane-footer col-xs-12">
   
   <div class="col-sm-10 col-lg-12 col-md-10 col-xs-12 whiteboard-tools no-pad">
   <div class="col-sm-3 no-pad">
      <div class="clos-pre">Close Whiteboard</div>
   </div>


   <div class="col-sm-9 col-xs-12 tool no-pad pull-right">
   <ul>
      <div id="toolbar-options" class="hidden">
            <a href="#" id="pencil-tool"><i class="fa fa-pencil"></i></a>
            <a href="#" id="eraser-tool"><i class="fa fa-eraser"></i></a>   
      </div>
      <li class="pencil"><div class="pen"><i class="pencil-tool-fa fa fa-pencil"></i></div>
       
      </li>
   
      <li class="position-change">
        <ul>
           <li><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/tarn-left.png"></li>
           <li><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/tarn--right.png"></li>
        </ul>
      </li>
      <li class="range-slider"><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/bar.png">
      <div class="range"><input type="range" orient="vertical" /></div>
     </li>
     <li class="color-picker">
       <ul>
          <li class="yellow"></li>
          <li class="block"></li>
          <li class="wight"></li>
          <li class="red"></li>
          <li class="blue"></li>
      </ul>
     </li>
    
   </ul>
 </div>
</div>

 </div>

</div>
<!-- END WHITEBOARD WINDOW -->

<!-- PRESENTATION WINDOW -->
<div ng-if="tab.type == 'presentation'" class="col-xs-12 no-pad meeting-pane presentation-room thumbs-active" ng-init="currentpresentationindex=0">
    <div class="col-xs-10 presentation-room no-pad">
    <img ng-src="{{'<?= IC_PLUGIN_URL; ?>/extract/'+tab.data.folder+'/'+tab.data.files[currentpresentationindex]}}" class="img-responsive">
    </div>

    <div class="col-xs-2 presentation-thumbs no-pad">

        <ul>
          <li ng-repeat="img in tab.data.files" ng-click="$parent.currentpresentationindex=$index"><img ng-src="{{'<?= IC_PLUGIN_URL; ?>/extract/'+tab.data.folder+'/'+img}}" class="img-responsive"><p><span>Page {{$index+1}}</span></p></li>
           </ul> 
    </div>
  <div class="pane-footer col-xs-12">
     <div class="col-sm-12 col-xs-12 whiteboard-tools no-pad">
     <div class="col-sm-3 no-pad"> 
     <div class="clos-pre">Close Presentation</div>
     </div>

     <div class="col-sm-4 no-pad pagination">
     <div class="per"><i class="fa fa-arrow-left" aria-hidden="true"></i> PREV</div>
     <div class="page-number"><select><option>Page 1 of 10</option><option>Page 10 of 20</option></select></div>
     <div class="next">NEXT <i class="fa fa-arrow-right" aria-hidden="true"></i></div>
     </div>

     <div class="col-sm-5 no-pad">
     <div class="tool">
     <ul>
        <div id="toolbar-options" class="hidden">
              <a href="#" id="pencil-tool"><i class="fa fa-pencil"></i></a>
              <a href="#" id="eraser-tool"><i class="fa fa-eraser"></i></a>   
        </div>
        <li class="pencil"><div class="pen"><i class="pencil-tool-fa fa fa-pencil"></i></div>
     
        <li class="position-change">
          <ul>
             <li><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/tarn-left.png"></li>
             <li><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/tarn--right.png"></li>
          </ul>
        </li>
        <li class="range-slider"><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/bar.png">
        <div class="range"><input type="range" orient="vertical" /></div>
       </li>
       <li class="color-picker">
         <ul>
            <li class="yellow"></li>
            <li class="block"></li>
            <li class="wight"></li>
            <li class="red"></li>
            <li class="blue"></li>
        </ul>
       </li>
       <li class="tip-option"><img src="<?= IC_PLUGIN_URL; ?>dist/v2/img/theme.png" data-toggle="tooltip" data-placement="top" title="Hide Thumbs"></li>
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
<div ng-if="tab.type == 'youtube'" class="col-xs-12 no-pad meeting-pane">
    Youtube
     <div class="pane-footer col-xs-12">test</div>
</div>

<!-- END YOUTUBE WINDOW -->
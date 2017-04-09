<!-- Presentation Library Modal -->
<div id="presentationsModal" class="ICModalWindow modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><i class="fa fa-line-chart" aria-hidden="true"></i> Presentation Library</h4>
      </div>
      <div class="modal-body">
              <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Please select your presentation</h3>

              <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 200px;">
                  <input type="text" ng-change="currentPage=0;" ng-model="psearch.name" name="table_search" class="form-control pull-right" placeholder="Search">

                  <div class="input-group-btn">
                    <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.box-header -->
            <div class="hide">
              <span ng-repeat="p in presentation_files track by $index" ng-init="p.id = p.id === undefined ? randomid() : p.id;"></span>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover">
                
                <tr ng-repeat="p in presentation_files | filter:psearch | startFrom:currentPage*5 | limitTo:5  track by $index">
                  <td><img width='100' ng-src="{{'<?= IC_PLUGIN_URL;?>/extract/'+p.folder+'/'+p.files[0]}}"></td>
                  <td>{{p.name}}</td>
                
                  <td>
                    <a ng-click="deletepresentation($event, $index)" class="btn btn-app modal-app-btn" data-toggle="tooltip" data-placement="bottom" data-animation="delay 2" title="Remove"><i class="fa fa-trash"></i></a>
                    <a ng-hide="tab_type_length('presentation', p.id);" ng-click="add_tab('presentation', p.name, p)" class="btn btn-app modal-app-btn" data-toggle="tooltip" data-placement="bottom" title="Open" data-dismiss="modal"><i class="fa fa-angle-double-right"></i></a>
                    <a ng-show="tab_type_length('presentation', p.id);" class="btn btn-app modal-app-btn" data-toggle="tooltip" data-placement="bottom" title="Already added in tab" ><i class="fa fa-angle-double-right"></i></a>
                  </td>
                </tr>
                
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>

      <div class="box-footer clearfix" ng-show="(presentation_files | filter:psearch).length > 5">
        <ul class="pagination pagination-sm no-margin pull-right">
          <li ><a ng-style="{opacity:currentPage == 0 ? 0.5 : 1, 'pointer-events':currentPage == 0 ? 'none' : 'auto'}" ng-click="currentPage=currentPage-1" href="#">«</a></li>
          <li ng-repeat="pp in numberOfPagesArray('presentation_files', 'psearch') track by $index" ng-disabled="currentPage == $index" ng-click="$parent.currentPage=$index"><a href="#">{{$index+1}}</a></li>
          <li ng-style="{opacity:currentPage >= (presentation_files | filter:psearch ).length/5 - 1 ? 0.5 : 1, 'pointer-events':currentPage >= (presentation_files | filter:psearch).length/5 - 1 ? 'none' : 'auto'}" ng-click="currentPage=currentPage+1"><a href="#">»</a></li>
        </ul>
      </div>
          


            
      </div>
      <div class="hide upload-preload row no-margin">

      <div class="col-xs-2 file-loader"><i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i></div>
      <div class="col-xs-10"><span class="file-name">Presentation 1.ppt</span> <span class="converting">Converting...</span> <span class="label label-danger pull-right upload_percentage">70%</span>
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
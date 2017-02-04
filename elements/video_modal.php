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
                
                <tr ng-repeat="p in youtube_list | filter:vsearch | startFrom:currentPage*5 | limitTo:5  track by $index ">
                  <td ng-click="add_tab('youtube', p.name, p)" data-dismiss="modal"><img ng-src="{{'https://img.youtube.com/vi/'+getvideobyID(p.url)+'/hqdefault.jpg'}}" width="60" /></td>
                  <td ng-click="add_tab('youtube', p.name, p)" data-dismiss="modal">{{p.name}}</td>
                
                  <td>
                    <a ng-click="deletevideo($event, $index)" class="btn btn-app modal-app-btn" data-toggle="tooltip" data-placement="bottom" data-animation="delay 2" title="Remove"><i class="fa fa-trash"></i></a>
                    <a ng-click="add_tab('youtube', p.name, p)" class="btn btn-app modal-app-btn" data-toggle="tooltip" data-placement="bottom" title="Play Video" data-dismiss="modal"><i class="fa fa-youtube-play"></i></a>
                  </td>
                </tr>
                
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>

      <div class="box-footer clearfix" ng-show="(youtube_list | filter:vsearch).length > 5">
        <ul class="pagination pagination-sm no-margin pull-right">
          <li ><a ng-style="{opacity:currentPage == 0 ? 0.5 : 1, 'pointer-events':currentPage == 0 ? 'none' : 'auto'}" ng-click="currentPage=currentPage-1" href="#">«</a></li>
          <li ng-repeat="pp in numberOfPagesArray('youtube_list', 'vsearch') track by $index" ng-disabled="currentPage == $index" ng-click="$parent.currentPage=$index"><a href="#">{{$index+1}}</a></li>
          <li ng-style="{opacity:currentPage >= youtube_list.length/5 - 1 ? 0.5 : 1, 'pointer-events':currentPage >= youtube_list.length/5 - 1 ? 'none' : 'auto'}" ng-click="currentPage=currentPage+1"><a href="#">»</a></li>
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
        
               
                <button ng-click="addnew_video();" class="btn btn-red pull-right">Add Video</button>
              
      
      </div>

      <div class="modal-footer">
          
          <div class="col-xs-4 no-pad"><button type="button" class="btn btn-red add-video-btn">Add Video</button></div>
          <div class="col-xs-4 col-xs-push-4 close-btn"><button type="button" class="btn btn-default no-margin-right" data-dismiss="modal">Close</button></div>
        
      </div>
    </div>

  </div>
</div>
<!-- End Youtube Modal -->
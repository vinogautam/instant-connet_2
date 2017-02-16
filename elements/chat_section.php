<aside class="control-sidebar control-sidebar-dark">
  <div class="meeting-msg">
    <h5><i class="fa fa-comments"></i> <span>Meeting Message</span></h5>
    <div class="close-icon"><i class="fa fa-times close" aria-hidden="true"></i></div>
    </div>
      <div class="chat-mothed">
        <div ng-repeat="ch in chat track by $index" on-finish-render ng-class="{meeting_users:ch.id != data2.id}">
            <div class="messages1" ng-if="ch.id != data2.id">
              <span class="chat-persion">{{ch.msg[0].name}}</span>
              <p ng-repeat="c in ch.msg track by $index"><span class="msg-bar-resive msg-last-resive">{{c.msg}}</span></p>
              <span class="del chat-persion">Delivered {{ch.time | date:'h:mm a'}}</span>
              <span class="ct-user-icon1"><img ng-src="{{'//identicon.org/?t='+ch.msg[0].email+'&s=35'}}"></span>
            </div>
            
            <div class="messages" ng-if="ch.id == data2.id">
              <span class="chat-persion">Agent Name </span>
              <p ng-repeat="c in ch.msg track by $index"><span class="msg-bar msg-last">{{c.msg}}</span></p>
              <span class="del">Delivered 11:29 PM</span>
              <span class="ct-user-icon"><img ng-src="{{'//identicon.org/?t='+ch.id+'&s=35'}}"></span>
            </div>
        </div>
     </div>
     <div class="chat-content">
     <div class="form-control1">
<form>
<textarea ng-model="data2.msg" ng-enter="add();" id="msg" placeholder="Type a message here" rows="2"></textarea>
<button class="go1"><i class="fa fa-paperclip" aria-hidden="true"></i></button>
<button ng-click="add();" class="go"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>

</form>
</div>
</div>




  </aside>
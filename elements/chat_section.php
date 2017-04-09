<aside class="control-sidebar control-sidebar-dark">
  <div class="meeting-msg">
    <h5><i class="fa fa-comments"></i> <span>Chat</span></h5>
    <div class="chat-close-icon"><div class="close-svg"></div></div>
    </div>
      <div class="chat-mothed">
        <div ng-repeat="ch in chat track by $index" on-finish-render ng-class="{meeting_users:ch.id != data2.id}">
            <div class="messages1" ng-if="ch.id != data2.id">
              <span class="chat-persion">{{ch.msg[0].name}}</span>
              <p ng-repeat="c in ch.msg track by $index"><span class="msg-bar-resive msg-last-resive">{{c.msg}}</span></p>
              <span class="del chat-persion">Delivered {{ch.time | date:'h:mm a'}}</span>
              <span ng-if="ch.msg[0].name == 'Agent'" class="ct-user-icon1"><img ng-src="{{'//identicon.org/?t='+ch.msg[0].name+'&s=35'}}"></span>
            </div>
            
            <div class="messages" ng-if="ch.id == data2.id">
              <span class="chat-persion">{{ch.msg[0].name}}</span>
              <p ng-repeat="c in ch.msg track by $index"><span class="msg-bar msg-last">{{c.msg}}</span></p>
              <span class="del">Delivered {{ch.time | date:'h:mm a'}}</span>
              <span ng-if="ch.msg[0].name == 'Agent'" class="ct-user-icon"><img ng-src="{{'//identicon.org/?t='+ch.msg[0].name+'&s=35'}}"></span>
            </div>
        </div>
        <div class="usertypingnoti" ng-show="size(typinguser)">
            <span ng-repeat="item in typinguser">{{item.name}}</span> is typing...
        </div>
     </div>
     <div class="chat-content">
     <div class="form-chat-meeting-room">
<form>
<textarea ng-change="send_noti({type:'usertyping', data:data2})" ng-model="data2.msg" ng-enter="add();" id="msg" placeholder="Type a message here" rows="2"></textarea>
<button class="go1"><i class="fa fa-paperclip" aria-hidden="true"></i></button>
<button ng-click="add();" class="go"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>

</form>
</div>
</div>




  </aside>
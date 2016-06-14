<?php
namespace subscriber;
use User;
use Notifications;
use modules\news\models\News as News;
use Carbon\Carbon; 

class UserSubscriber
{	

  public function saveNewsRelatedUser($objectType, $objectId, $userId, $senderId, $type){
    $news = new News();
    $news->object_type = $objectType;
    $news->object_id = $objectId;
    $news->user_id = $userId;
    $news->sender_id =$senderId;
    $news->news_type = $type;
    $news->save();
  }

  public function onUserFollowed($followingUserId, $followedUserId)
  {
    //note = notification_user

      $followedUser  = User::find($followedUserId);
      $followingUser = User::find($followingUserId);
      foreach ($followedUser->notifications as $notification) {
        $info = $notification->render();
        if ($info[0] == "follow" && $notification->read_at == null) {
          //checa para agrupar caso ja haja e nao seja lida
            $noteNotificationId = $notification->notificationId;
            $noteUserId = $notification->id;
            $note = $notification; //relativo a notification_user
        }
      }
        if (isset($noteNotificationId)) { //update
          $notificationFromTable = DB::table("notifications")->where("id","=", $noteNotificationId)->get();
          if (NotificationsController::isNotificationByUser($followingUserId, 
                                    $notificationFromTable[0]->sender_id, 
                                    $notificationFromTable[0]->data) == false) {

              $newData = $notificationFromTable[0]->data . ":" . $loggedUser->id;

              DB::table("notifications")->where("id", "=", $noteNotificationId)
                            ->update(array("data"       => $newData, 
                                     "created_at" => Carbon::now('America/Sao_Paulo')));

              $note->created_at = Carbon::now('America/Sao_Paulo');
              $note->save();  
          }
      }
      else //novo
        \Notification::create('follow', $followingUser, $followedUser, [$followedUser], null);
      
  }



  public function subscribe($events){

    $events->listen('user.followed' ,'subscriber\UserSubscriber@onUserFollowed');
    

	}

}
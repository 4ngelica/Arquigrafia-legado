<?php
namespace subscriber;
use User;
use News;
use Notifications;

class UserSubscriber
{	//editedProfile

	public function onNewProfilePicture($user)
	{
		\Log::info('NewProfilePicture'.$user->name);
		$news = new News();
		$news->object_type = 'User';
		$news->object_id   = $user->id;
		$news->user_id     = $user->id;
		$news->sender_id   = $user->id;
		$news->news_type   = 'new_profile_picture';
		$news->save();
	}

	public function onUpdateProfile($user)
	{
		\Log::info('edited_profile'.$user->name);
		$news = new News();
		$news->object_type = 'User';
		$news->object_id   = $user->id;
		$news->user_id     = $user->id;
		$news->sender_id   = $user->id;
		$news->news_type   = 'edited_profile';
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
		$events->listen('user.newProfilePicture','subscriber\UserSubscriber@onNewProfilePicture');
		$events->listen('user.updateProfile'    ,'subscriber\UserSubscriber@onUpdateProfile');
		$events->listen('user.followed'         ,'subscriber\UserSubscriber@onUserFollowed');




	}

}
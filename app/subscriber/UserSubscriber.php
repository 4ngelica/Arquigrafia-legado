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

	public function onUserFollowed($following_user_id, $followed_user_id)
	{
		//note = notification_user

  		$followed_user  = User::find($followed_user_id);
  		$following_user = User::find($following_user_id);
  		foreach ($followed_user->notifications as $notification) {
    		$info = $notification->render();
    		if ($info[0] == "follow" && $notification->read_at == null) {
    			//checa para agrupar caso ja haja e nao seja lida
      			$note_notification_id = $notification->notification_id;
      			$note_user_id = $notification->id;
      			$note = $notification; //relativo a notification_user
    		}
  		}
      	if (isset($note_notification_id)) { //update
        	$notification_from_table = DB::table("notifications")->where("id","=", $note_id)->get();
        	if (NotificationsController::isNotificationByUser($following_user_id, 
        													  $notification_from_table[0]->sender_id, 
        													  $notification_from_table[0]->data) == false) {

          		$new_data = $notification_from_table[0]->data . ":" . $logged_user->id;

          		DB::table("notifications")->where("id", "=", $note_id)
          								  ->update(array("data"       => $new_data, 
          								  				 "created_at" => Carbon::now('America/Sao_Paulo')));

          		$note->created_at = Carbon::now('America/Sao_Paulo');
          		$note->save();  
        	}
  		}
  		else //novo
  			\Notification::create('follow', $following_user, $followed_user, [$followed_user], null);
    	
	}

	public function subscribe($events){
		$events->listen('user.newProfilePicture','subscriber\UserSubscriber@onNewProfilePicture');
		$events->listen('user.updateProfile'    ,'subscriber\UserSubscriber@onUpdateProfile');
		$events->listen('user.followed'         ,'subscriber\UserSubscriber@onUserFollowed');




	}

}
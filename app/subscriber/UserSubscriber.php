<?php
namespace subscriber;
use User;
use News;
<<<<<<< HEAD
use Notifications;

=======
use Carbon\Carbon; 
>>>>>>> 9fa9b04... subscriber user and photos
class UserSubscriber
{	
	public function onNewProfilePicture($user)
<<<<<<< HEAD
	{
		\Log::info('NewProfilePicture'.$user->name);
		$news = new News();
		$news->object_type = 'User';
		$news->object_id   = $user->id;
		$news->user_id     = $user->id;
		$news->sender_id   = $user->id;
		$news->news_type   = 'new_profile_picture';
		$news->save();
=======
	{	\Log::info('NewProfilePicture'.$user->name);
		foreach ($user->followers as $users) {
           foreach ($users->news as $note) {
              if($note->news_type == 'new_profile_picture' && $note->sender_id == $user->id) {
                $curr_note = $note;
              }
           }
           if(isset($curr_note)) {
              if($note->sender_id == $user->id) {
                $date = $curr_note->created_at;
                if($date->diffInDays(Carbon::now('America/Sao_Paulo')) > 7) {                 
                  Static::saveNewsRelatedUser('User', null, $user,'new_profile_picture');
                }
             }
           }else {
                 Static::saveNewsRelatedUser('User', null, $user,'new_profile_picture');
           }
        }  
>>>>>>> 9fa9b04... subscriber user and photos
	}

	public function onUpdateProfile($user)
	{
		\Log::info('edited_profile'.$user->name);
		foreach ($user->followers as $users) {
        	foreach ($users->news as $note) {
            	if($note->news_type == 'edited_profile' && $note->sender_id == $user->id) {
            		  $curr_note = $note;
            	}
          	}
          	if(isset($curr_note)) {
            	if($note->sender_id == $user->id) {
              		$date = $curr_note->created_at;
              		if($date->diffInDays(Carbon::now('America/Sao_Paulo')) > 7) {                 
                		Static::saveNewsRelatedUser('User', null, $user,'edited_profile');                 		
              		}
            	}
          }
          else {               
              Static::saveNewsRelatedUser('User', null, $user,'edited_profile');
            }
        }

	}

  public function onGetFacebookPicture($user)
  {
      foreach ($user->followers as $users) {
          foreach ($users->news as $note) {
            if($note->news_type == 'new_profile_picture') {
              $curr_note = $note;
            }
          }
          if(isset($curr_note)) {
            if($note->sender_id == $user->id) {
              $date = $note->created_at;
              if($date->diffInDays(Carbon::now('America/Sao_Paulo')) > 7) {                   
                  Static::saveNewsRelatedUser('User', $user->id, $users->id, $user->id,'new_profile_picture');    
              }
            }
          } else {
              Static::saveNewsRelatedUser('User', $user->id, $users->id, $user->id,'new_profile_picture');
             
          }
      }
  }

<<<<<<< HEAD
	public function saveNewsRelatedUser($objectType, $photo, $user, $type){
		$news = new News();
<<<<<<< HEAD
		$news->object_type = 'User';
		$news->object_id   = $user->id;
		$news->user_id     = $user->id;
		$news->sender_id   = $user->id;
		$news->news_type   = 'edited_profile';
=======
		$news->object_type = $objectType;
		$news->object_id = $user->id;
		$news->user_id = $user->id;
		$news->sender_id =$user->id;
		$news->news_type = $type;
>>>>>>> 9fa9b04... subscriber user and photos
		$news->save();
	}
=======
  public function saveNewsRelatedUser($objectType, $objectId, $userId, $senderId, $type){
    $news = new News();
    $news->object_type = $objectType;
    $news->object_id = $objectId;
    $news->user_id = $userId;
    $news->sender_id =$senderId;
    $news->news_type = $type;
    $news->save();
  }
>>>>>>> cfb2f2f... add in subscriber to users

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
<<<<<<< HEAD
		$events->listen('user.updateProfile'    ,'subscriber\UserSubscriber@onUpdateProfile');
		$events->listen('user.followed'         ,'subscriber\UserSubscriber@onUserFollowed');



=======
		$events->listen('user.updateProfile','subscriber\UserSubscriber@onUpdateProfile');
    $events->listen('user.newProfileFacebookPicture','subscriber\UserSubscriber@onGetFacebookPicture');
>>>>>>> cfb2f2f... add in subscriber to users

	}

}
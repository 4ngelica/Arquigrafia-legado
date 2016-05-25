<?php
namespace subscriber;
use User;
use News;
use Carbon\Carbon; 
class UserSubscriber
{	
	public function onNewProfilePicture($user)
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


	public function saveNewsRelatedUser($objectType, $photo, $user, $type){
		$news = new News();
		$news->object_type = $objectType;
		$news->object_id = $user->id;
		$news->user_id = $user->id;
		$news->sender_id =$user->id;
		$news->news_type = $type;
		$news->save();
	}

	public function subscribe($events){
		$events->listen('user.newProfilePicture','subscriber\UserSubscriber@onNewProfilePicture');
		$events->listen('user.updateProfile','subscriber\UserSubscriber@onUpdateProfile');





	}

}
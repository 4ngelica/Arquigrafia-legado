<?php
namespace subscriber;
use User;
use News;
class UserSubscriber
{	//editedProfile

	public function onNewProfilePicture($user)
	{
		\Log::info('NewProfilePicture'.$user->name);
		$news = new News();
		$news->object_type = 'User';
		$news->object_id = $user->id;
		$news->user_id = $user->id;
		$news->sender_id = $user->id;
		$news->news_type = 'new_profile_picture';
		$news->save();
	}

	public function onUpdateProfile($user)
	{
		\Log::info('edited_profile'.$user->name);
		$news = new News();
		$news->object_type = 'User';
		$news->object_id = $user->id;
		$news->user_id = $user->id;
		$news->sender_id = $user->id;
		$news->news_type = 'edited_profile';
		$news->save();
	}

	public function subscribe($events){
		$events->listen('user.newProfilePicture','subscriber\UserSubscriber@onNewProfilePicture');
		$events->listen('user.updateProfile','subscriber\UserSubscriber@onUpdateProfile');





	}

}
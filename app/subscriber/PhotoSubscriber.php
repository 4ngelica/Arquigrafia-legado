<?php
namespace subscriber;
use User;
use News;
use Auth;
use Carbon\Carbon; 

class PhotoSubscriber
{
	public function onNewPhoto($photo)
	{ \Log::info('onNewPhoto ='.$photo->name);

		foreach(Auth::user()->followers as $users) {
                foreach ($users->news as $note) {
                  if($note->news_type == 'new_photo' && $note->sender_id == Auth::user()->id) {
                    $curr_note = $note;
                  }
                }
                if(isset($curr_note)) {
                    $date = $curr_note->created_at;
                    if($date->diffInDays(Carbon::now('America/Sao_Paulo')) > 0) {						
						Static::saveOnNews('Photo', $photo->id,$users->id,Auth::user()->id,'new_photo');
                        \Log::info("if-newfoto");                       
                    }else {
                      $curr_note->object_id = $photo->id;
                      $curr_note->save();
                      \Log::info("else-newfoto");
                    }
                }else {
                	Static::saveOnNews('Photo', $photo->id,$users->id,Auth::user()->id,'new_photo');
                    \Log::info("elseIsset-newfoto");
                }
        }
		/*$news = new News();
	 News::create(array('object_type' => 'Photo',
'object_id' => $photo->id,
'user_id' => $users->id,
'sender_id' => Auth::user()->id,
'news_type' => 'new_photo'));
} */
	}

	public function onUpdatePhoto($user, $photo)
	{  \Log::info('onNewPhoto'.$user->name.'onNew'.$photo->name);
		\Log::info(Carbon::now('America/Sao_Paulo'));
		foreach ($user->followers as $users) {
          	foreach ($users->news as $note) {
            	if($note->news_type == 'edited_photo' && $note->sender_id == $user->id) {
              		$curr_note = $note;
            	}
          	}
          	if(isset($curr_note)) {
            	if($note->sender_id == $user->id) {
              		$date = $curr_note->created_at;
              		if($date->diffInDays(Carbon::now('America/Sao_Paulo')) > 7)              			
              			Static::saveOnNews('Photo', $photo->id,$user->id,$user->id,'edited_photo');
            	}
          	}else {
          		Static::saveOnNews('Photo', $photo->id,$user->id,$user->id,'edited_photo');
          	}
        }
	}	

	public function saveOnNews($objectType, $objectId,$userId,$senderId,$type)
  {
		$news = new News();
		$news->object_type = $objectType;
		$news->object_id = $objectId;
		$news->user_id = $userId;
		$news->sender_id = $senderId;
		$news->news_type = $type;
		$news->save();
	}

	public function subscribe($events){
		$events->listen('photo.newPhoto','subscriber\PhotoSubscriber@onNewPhoto');
		$events->listen('photo.updatePhoto','subscriber\PhotoSubscriber@onUpdatePhoto');
	}
}

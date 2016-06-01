<?php
namespace modules\collaborative\subscriber;
use Users;
use modules\collaborative\models\Comment;
use modules\collaborative\controllers\CommentsCntroller;

class CommentSubscriber {

	public function onCommentCreate($user, $photo){
        //notification
		if ($user->id != $photo->user_id) { 
            $user_note = User::find($photo->user_id);
            foreach ($user_note->notifications as $notification) {
                $info = $notification->render();
                if ($info[0] == "comment_posted" && $info[2] == $photo->id && 
                									$notification->read_at == null) {
                    $note_id = $notification->notification_id;
                    $note_user_id = $notification->id;
                    $note = $notification;
                }
            }
            if (isset($note_id)) {
                $note_from_table = \DB::table("notifications")->where("id","=", $note_id)->get();
                if (\NotificationsController::isNotificationByUser($user->id, 
                												   $note_from_table[0]->sender_id, 
                												   $note_from_table[0]->data) == false) {
                    $new_data = $note_from_table[0]->data . ":" . $user->id;
                    \DB::table("notifications")->where("id", "=", $note_id)
                    						   ->update(array("data" => $new_data, 
                    						   				  "created_at" => Carbon::now('America/Sao_Paulo')));
                    $note->created_at = Carbon::now('America/Sao_Paulo');
                    $note->save();  
                }
            }
            else Notification::create('comment_posted', $user, $comment, [$user_note], null);
        }
	}

	public function subscribe($events){
		$events->listen('comment.create', 'modules\collaborative\subscriber\CommentSubscriber@onCommentCreate');
	}
}
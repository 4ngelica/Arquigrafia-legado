<?php
namespace modules\collaborative\subscriber;
use Users;
use Notification;
use modules\collaborative\models\Like;
use modules\gamification\models\Badge;

class LikeSubscriber {

	public function onPhotoLiked($user, $photo) {
		if ($user->id != $photo->user_id) {
			Notification::create('photo_liked', $user, $photo, [$user_note], null);
		}

		if ( ($badge = Badge::getDestaqueDaSemana($photo)) ) {
    		Notification::create('badge_earned', $user, $badge, [$photo->user], null);
    	}
    }

	public function onPhotoDisliked($user, $photo){
	    try {
	      $like = Like::fromUser($user)->withLikable($photo)->first();
	      $like->delete();
	    } catch (Exception $e) {
	      //
	    }
	}

	public function onCommentLiked($user, $comment) {
	    if ($user->id != $comment->user_id) {
	        $user_note = User::find($comment->user_id);
	        Notification::create('comment_liked', $user, $comment, [$user_note], null);
	    }
	}

	public function onCommentDisliked($user, $comment) {
	    try {
	      $like = Like::fromUser($user)->withLikable($comment)->first();
	      $like->delete();
	    } catch (Exception $e) {
	      //
	    }
	}

	public function subscribe($events){
		$events->listen('photo.like'     , 'modules\collaborative\subscriber\LikeSubscriber@onPhotoLiked');
		$events->listen('photo.dislike'  , 'modules\collaborative\subscriber\LikeSubscriber@onPhotoDisliked');
		$events->listen('comment.like'   , 'modules\collaborative\subscriber\LikeSubscriber@onCommentLiked');
		$events->listen('comment.dislike', 'modules\collaborative\subscriber\LikeSubscriber@onCommentDisliked');
	}
}
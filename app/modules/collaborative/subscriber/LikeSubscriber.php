<?php
namespace modules\collaborative\subscriber;
use Users;
use Likes;

class LikeSubscriber {

	public function onPhotoLiked($id) {
		
	    
	}

	public function onPhotoDisliked($user, $photo){
		$this->logLikeDislike($user, $photo, "a foto", "Descurtiu", "user");
	    try {
	      $like = Like::fromUser($user)->withLikable($photo)->first();
	      $like->delete();
	    } catch (Exception $e) {
	      //
	    }
	}

	public function onCommentLiked($user, $comment) {
		
	    $this->logLikeDislikeComment($user, $comment, "o comentário", "Curtiu", "user");
    
	    if ($user->id != $comment->user_id) {
	        $user_note = User::find($comment->user_id);
	        Notification::create('comment_liked', $user, $comment, [$user_note], null);
	    }
	}

	public function onCommentDisliked($user, $comment) {
		$this->logLikeDislikeComment($user, $comment, "o comentário", "Descurtiu", "user");
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
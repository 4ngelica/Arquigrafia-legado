<?php
namespace modules\collaborative\controllers;

use modules\collaborative\models\Comment;
use modules\collaborative\models\Like;
use modules\gamification\models\Badge;
use lib\date\Date;
use lib\utils\ActionUser;
use modules\news\models\News;
use Input;
use Photo;
use Auth;
use User;
use Notification;
use Carbon\Carbon;


class CommentsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 * @return Response
	 */
	public function index()
	{ 
		$comment = Comment::all();
    return $comment;
	}

	
	public function comment($id)
  { 
    	$input = Input::all();
    	$rules = ['text' => 'required'];
    	$validator = \Validator::make($input, $rules);
    	if ($validator->fails()) {
      	 $messages = $validator->messages();
      	 return \Redirect::to("/photos/{$id}")->withErrors($messages);
    	} else { 
      	$comment = ['text' => $input["text"], 'user_id' => Auth::user()->id];
      	$comment = new Comment($comment);        
      	$photo = Photo::find($id);
      	$photo->comments()->save($comment);
        
        $user = Auth::user();
        $source_page = \Request::header('referer');
        ActionUser::printComment($user->id, $source_page, "Inseriu", $comment->id, $id, "user");
      
        /*Envio de notificação*/
        
        \Event::fire('comment.create', array($user, $photo));
        
 
        $this->checkCommentCount(5,'test');
        return \Redirect::to("/photos/{$id}");
      }
  }


// need to be modified
  private function checkCommentCount($number_comment, $badge_name){
      $user = Auth::user();
      if(($user->badges()->where('name', $badge_name)->first()) != null){
        return;
      }
      if (($user->comments->count()) == $number_comment){
        $badge=Badge::where('name', $badge_name)->first();
        $user->badges()->attach($badge);
      }
  }

  public function commentLike($id) {
    $comment = Comment::find($id);
    $user = Auth::user();

    \Event::fire('comment.liked', array($user, $comment));
    
    $this->logLikeDislikeComment($user, $comment, "o comentário", "Curtiu", "user");

    $like = Like::getFirstOrCreate($comment, $user);
    if (is_null($comment)) {
      return \Response::json('fail');
    }
    return \Response::json([ 
      'url' => \URL::to('/comments/' . $comment->id . '/' . 'dislike'),
      'likes_count' => $comment->likes->count()]);
  }

  public function commentDislike($id) {
    $comment = Comment::find($id);
    $user = Auth::user();

    \Event::fire('comment.disliked', array($user, $comment));

    $source_page = \Request::header('referer');
    
    if (is_null($comment)) {
      return \Response::json('fail');
    }
    return \Response::json([ 
      'url' => \URL::to('/comments/' . $comment->id . '/' . 'like'),
      'likes_count' => $comment->likes->count()]);
  }

  private function logLikeDislikeComment($user, $likable, $photo_or_comment, $like_or_dislike, $user_or_visitor) {
    $source_page = \Request::header('referer');
    ActionUser::printLikeDislike($user->id, $likable->id, $source_page, $photo_or_comment, $like_or_dislike, $user_or_visitor);
  }



}

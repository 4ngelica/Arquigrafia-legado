<?php
namespace modules\collaborative\controllers;

use modules\collaborative\models\Comment;
use modules\collaborative\models\Like;
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
        

        /*News feed*/
        foreach ($user->followers as $users) {
            foreach ($users->news as $news) {  
              if ($news->news_type == 'commented_photo' && Comment::find($news->object_id)->photo_id == $id) {
                  $last_news = $news;
                  $primary = 'commented_photo';
              }else if ($news->news_type == 'evaluated_photo' || $news->news_type == 'liked_photo') {
                if ($news->object_id == $id) {
                  $last_news = $news;
                  $primary = 'other';
                }
              }
            }
            if (isset($last_news)) {
                $last_update = $last_news->updated_at;
                if($last_update->diffInDays(Carbon::now('America/Sao_Paulo')) < 7) {
                    if ($news->sender_id == $user->id) {
                        $already_sent = true;
                    }else if ($news->data != null) {
                        $data = explode(":", $news->data);
                        for($i = 1; $i < count($data); $i++) {
                            if($data[$i] == $user->id) {
                                $already_sent = true;
                            }
                        }
                    }
                    if (!isset($already_sent)) {
                        $data = $last_news->data . ":" . $user->id;
                        $last_news->data = $data;
                        $last_news->save();
                    }
                    if ($primary == 'other') {
                        if ($last_news->secondary_type == null) {
                            $last_news->secondary_type = 'commented_photo';
                        }else if ($last_news->tertiary_type == null) {
                            $last_news->tertiary_type = 'commented_photo';
                        }
                        $last_news->save();
                    }
                } else {
                    News::create(array('object_type' => 'Comment', 
                               'object_id' => $comment->id, 
                               'user_id' => $users->id, 
                               'sender_id' => $user->id, 
                               'news_type' => 'commented_photo'));
                }
            }else {
                News::create(array('object_type' => 'Comment', 
                             'object_id' => $comment->id, 
                             'user_id' => $users->id, 
                             'sender_id' => $user->id, 
                             'news_type' => 'commented_photo'));
            }
        }  
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
    $this->logLikeDislikeComment($user, $comment, "o comentário", "Descurtiu", "user");
    $source_page = \Request::header('referer');
    ActionUser::printLikeDislike($user->id, $likable->id, $source_page, $photo_or_comment, $like_or_dislike, $user_or_visitor);
  }



}

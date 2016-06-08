<?php namespace modules\collaborative\controllers;
use modules\collaborative\models\Like;
use modules\collaborative\models\Comment;
use lib\utils\ActionUser;
use modules\news\models\News as News;
use modules\gamification\models\Badge;
use Notification;
use Carbon\Carbon;

class LikesController extends \BaseController {

  public function index(){
    $photo = \Photo::find(143);
    $user = \Auth::user();
    $pp = Like::fromUser($user)->withLikable($photo)->first();
    $pp->delete();
    dd($pp);
  }
  public function photoLike($id) {

    $photo = \Photo::find($id);
    $user = \Auth::user();
    if (is_null($photo)) {
      return \Response::json('fail');
    }

    \Event::fire('photo.like', array($user, $photo));

    $this->logLikeDislike($user, $photo, "a foto", "Curtiu", "user");
    if ($user->id != $photo->user_id) {
      $user_note = \User::find($photo->user_id);      
    }
    $like = Like::getFirstOrCreate($photo, $user);
    
    return \Response::json([ 
      'url' => \URL::to('/dislike/' . $photo->id),
      'likes_count' => $photo->likes->count()
    ]);
  }

  public function photoDislike($id) {
    $photo = \Photo::find($id);
    $user = \Auth::user();

    //\Event::fire('photo.dislike', array($user, $photo));

    $this->logLikeDislike($user, $photo, "a foto", "Descurtiu", "user");
    /* */
    try {
      $like = Like::fromUser($user)->withLikable($photo)->first();
      $like->delete();
    } catch (Exception $e) {
      //
    }
    /* */
    if (is_null($photo)) {
      return \Response::json('fail');
    }
    return \Response::json([ 
      'url' => \URL::to('/like/' . $photo->id),
      'likes_count' => $photo->likes->count()]);
  }

  private function logLikeDislike($user, $likable, $photo_or_comment, $like_or_dislike, $user_or_visitor) {
    $source_page = \Request::header('referer');
    ActionUser::printLikeDislike($user->id, $likable->id, $source_page, $photo_or_comment, $like_or_dislike, $user_or_visitor);
  }
}
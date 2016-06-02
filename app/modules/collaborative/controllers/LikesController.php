<?php namespace modules\collaborative\controllers;
use modules\collaborative\models\Like;
use modules\collaborative\models\Comment;
use lib\utils\ActionUser;
use modules\news\models\News as News;
use modules\gamification\models\Badge;
use Notification;
use Carbon\Carbon;

class LikesController extends \BaseController {

  public function photoLike($id) {

    $photo = \Photo::find($id);
    $user = \Auth::user();
    if (is_null($photo)) {
      return \Response::json('fail');
    }
    $this->logLikeDislike($user, $photo, "a foto", "Curtiu", "user");
    if ($user->id != $photo->user_id) {
      $user_note = \User::find($photo->user_id);      
      \Notification::create('photo_liked', $user, $photo, [$user_note], null);
    }
    $like = Like::getFirstOrCreate($photo, $user);
    if ( ($badge = Badge::getDestaqueDaSemana($photo)) ) {
      \Notification::create('badge_earned', $user, $badge, [$photo->user], null);
    }
    return \Response::json([ 
      'url' => \URL::to('/dislike/' . $photo->id),
      'likes_count' => $photo->likes->count()
    ]);
  }

  public function photoDislike($id) {
    $photo = \Photo::find($id);
    $user = \Auth::user();

   // \Event::fire('photo.dislike', array($user, $photo));

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
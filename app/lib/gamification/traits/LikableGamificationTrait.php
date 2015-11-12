<?php namespace lib\gamification\traits;

use lib\gamification\models\Score;
use lib\gamification\models\Like;

trait LikableGamificationTrait { 
  public function likes()
  {
    return $this->morphMany('lib\gamification\models\Like', 'likable');
  }

  public function hasUserLike($user) {
    if (\Auth::check()) {
      $like = Like::fromUser($user)->withLikable($this)->first();
      if ( ! is_null($like) ) {
        return true;
      }
    }
    return false;
  }

  public function countLikesAfterDate($date) {
    $last_week = \Carbon\Carbon::today()->subWeek();
    return $this->likes()
      ->where('created_at', '>=', (string) $last_week)->count();
  }
}
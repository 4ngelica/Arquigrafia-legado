<?php namespace lib\gamification\traits;

use lib\gamification\models\Score;

trait UserGamificationTrait {

  public function score() {
    return $this->hasOne('lib\gamification\models\Score');
  }

  public function scopeWithPoints($query, $columns, $following) {
    return $query->select(\DB::raw('users.' . $columns . ', scores.points'))
    ->leftJoin('scores', 'users.id', '=', 'scores.user_id')
    ->groupBy('users.id')
    ->orderBy('points', 'desc');
  }

  public function scopeWithUploads($query, $columns, $following) {
    $p = \DB::raw('(select * from photos where deleted_at is null and institution_id is null) as p');
    return $query->select(\DB::raw('users.' . $columns . ', count(user_id) as uploads'))
    ->leftJoin($p, 'users.id', '=', 'p.user_id')
    ->groupBy('users.id')
    ->orderBy('uploads', 'desc');
  }

  public function scopeWithEvals($query, $columns, $following) {
    $b = \DB::raw('(select * from binomial_evaluation where photo_id'
      . ' in (select id from photos where deleted_at is null)) as b');
    return $query->select(\DB::raw('users.' . $columns . ', count(user_id) / 6 as evals'))
    ->leftJoin($b, 'users.id', '=', 'b.user_id')
    ->groupBy('users.id')
    ->orderBy('evals', 'desc');
  }

  public function scopeSortBy($query, $score_type, $columns = '*', $following = false) {
    $method = 'with' . ucfirst($score_type);
    $query->$method($columns, $following);
    if ( $following ) {
      return $query->whereIn('users.id', function ($sub) use($following) {
        $sub->select('followed_id')->from('friendship')
          ->where('following_id', $following);
      })->orWhere('users.id', $following);
    }
    return $query;
  }

  public function badges()
  {
    return $this->belongsToMany('lib\gamification\models\Badge','user_badges')
      ->withTimestamps()
      ->withPivot('element_type', 'element_id');
  }

}
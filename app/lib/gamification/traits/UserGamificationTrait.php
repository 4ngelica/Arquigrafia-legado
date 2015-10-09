<?php namespace lib\gamification\traits;

trait UserGamificationTrait {

  public function getCustomKey($key) {
    if ( ! array_key_exists($key, $this->relations)) {
      $this->load($key);
    }
    $related = $this->getRelation($key);
    return ($related) ? (int) $related->$key : 0;
  }

  public function uploads() {
    return $this->hasOne('Photo')
      ->selectRaw('user_id, count(*) as uploads')
      ->groupBy('user_id');
  }

  public function getUploadsAttribute()
  {
    return $this->getCustomKey('uploads');
  }

  public function evals() {
    return $this->hasOne('Evaluation')
      ->selectRaw('user_id, count(*) / 6 as evals')
      ->groupBy('user_id');
  }

  public function getEvalsAttribute() {
    return ($this->getCustomKey('evals'));
  }

  public function points() {
    return $this->hasOne('lib\gamification\models\Score')
      ->selectRaw('user_id, points')
      ->groupBy('user_id');
  }

  public function getPointsAttribute() {
    return $this->getCustomKey('points');
  }

}
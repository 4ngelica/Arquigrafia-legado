<?php namespace lib\gamification\models;

use User;

class Leaderboard extends \Eloquent {

  protected $fillable = ['type', 'count', 'user_id'];

  public function user() {
    return $this->belongsTo('User');
  }

  public function scopeFromUser($query, $user) {
    $id = $user instanceof User ? $user->id : $user;
    return $query->where('user_id', $id);
  }

  public function increaseScore() {
    $this->count += 1;
  }

  public function decreaseScore() {
    $this->count -= 1;
  }

}
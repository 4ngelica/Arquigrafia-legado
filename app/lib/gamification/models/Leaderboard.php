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

  public function increaseScore($save = true) {
    $this->count += 1;
    if ($save) {
      $this->save();
    }
  }

  public function decreaseScore($save = true) {
    $this->count -= 1;
    if ($save) {
      $this->save();
    }
  }

  public static function createFromUser($user) {
    static::create([
        'type' => 'uploads',
        'count' => 0,
        'user_id' => $user->id
      ]);
    static::create([
        'type' => 'evaluations',
        'count' => 0,
        'user_id' => $user->id
      ]);
  }

  public static function increaseUserScore($user, $type) {
    $user_uploads = static::fromUser($user)->whereType($type)->first();
    $user_uploads->increaseScore();
  }

  public static function decreaseUserScore($user, $type) {
    $user_uploads = static::fromUser($user)->whereType($type)->first();
    $user_uploads->decreaseScore();
  }

}
<?php namespace lib\gamification\models;

class Score extends \Eloquent {

  protected $fillable = ['points', 'user_id'];

  public $timestamps = false;

  public function user() {
    return $this->belongsTo('User');
  }

  public function scopeOrderByPoints($query) {
    return $query->orderBy('points', 'desc');
  }

}
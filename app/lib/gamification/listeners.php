<?php
  User::created(function($user) {
    if ( $user->points == null ) {
      $score = lib\gamification\models\Score::create([
        'points' => 0,
        'user_id' => $user->id
      ]);
    }
  });
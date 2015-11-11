<?php
  use lib\gamification\models\Leaderboard as Leaderboard;

  User::created (function ($user) {
    Leaderboard::create([
        'type' => 'uploads',
        'count' => 0,
        'user_id' => $user->id
      ]);
    Leaderboard::create([
        'type' => 'evaluations',
        'count' => 0,
        'user_id' => $user->id
      ]);
  });

  Photo::created (function ($photo) {
    if ( ! $photo->hasInstitution() ) {
      $user_uploads = Leaderboard::fromUser($photo->user_id)
        ->whereType('uploads')->first();
      $user_uploads->increaseScore();
    }
  });

  Photo::deleted (function ($photo) {
    if ( ! $photo->hasInstitution() ) {
      $user_uploads = Leaderboard::fromUser($photo->user_id)
        ->whereType('uploads')->first();
      $user_uploads->decreaseScore();
    }
  });

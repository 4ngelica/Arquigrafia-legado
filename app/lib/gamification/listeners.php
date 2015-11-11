<?php
  use lib\gamification\models\Leaderboard as Leaderboard;

  User::created (function ($user) {
    Leaderboard::createFromUser($user);
  });

  Photo::created (function ($photo) {
    if ( ! $photo->hasInstitution() ) {
      Leaderboard::increaseUserScore($photo->user_id, 'uploads');
    }
  });

  Photo::deleted (function ($photo) {
    if ( ! $photo->hasInstitution() ) {
      Leaderboard::decreaseUserScore($photo->user_id, 'uploads');
    }
  });

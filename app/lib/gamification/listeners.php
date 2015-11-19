<?php
  use lib\gamification\models\Leaderboard as Leaderboard;

  User::created (function ($user) {
    Leaderboard::createFromUser($user);
  });

  Evaluation::created (function ($evaluation) {
    $min_id = Binomial::orderBy('id', 'asc')->first();
    if ( $evaluation == $min_id ) {
      Leaderboard::increaseUserScore($evaluation->user_id, 'evaluations');
    }
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
    Leaderboard::decreaseUsersScores($photo->evaluators, 'evaluations');
  });

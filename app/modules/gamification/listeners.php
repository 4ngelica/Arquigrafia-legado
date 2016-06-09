<?php
  use modules\gamification\models\Leaderboard as Leaderboard;
  use modules\evaluations\models\Evaluation as Evaluation;
  use modules\evaluations\models\Binomial as Binomial;

  // User::created (function ($user) {
  //   Leaderboard::createFromUser($user);
  // });

  // Evaluation::created (function ($evaluation) {
  //   $min_id = Binomial::orderBy('id', 'asc')->first();
  //   if ( $evaluation->binomial_id == $min_id->id ) {
  //     Leaderboard::increaseUserScore($evaluation->user_id, 'evaluations');
  //   }
  // });

  // Photo::created (function ($photo) {
  //   if ( ! $photo->hasInstitution() ) {
  //     Leaderboard::increaseUserScore($photo->user_id, 'uploads');
  //   }
  // });

  // Photo::deleted (function ($photo) {
  //   if ( ! $photo->hasInstitution() ) {
  //     Leaderboard::decreaseUserScore($photo->user_id, 'uploads');
  //   }
  //   Leaderboard::decreaseUsersScores($photo->evaluators, 'evaluations');
  // });

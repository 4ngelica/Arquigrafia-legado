<?php
  use modules\news\models\News as News;
  use modules\collaborative\models\Tag as Tag;
  use modules\collaborative\models\Comment as Comment;
  use modules\collaborative\models\Like as Like;
  use modules\gamification\models\Leaderboard as Leaderboard;
  use modules\evaluations\models\Evaluation as Evaluation;
  use modules\evaluations\models\Binomial as Binomial;
  use modules\institutions\models\Institution as Institution;
  use modules\institutions\models\Employee as Employee;
 
  User::updated(function($user){
      if(Input::hasFile('photo')){
    	   	News::eventNewPicture($user,'new_profile_picture'); 
    	}else{
    		  News::eventUpdateProfile($user,'edited_profile'); 
    	}
  });

   /*modul gamifi */
  User::created (function ($user) {
      Leaderboard::createFromUser($user);
  });

  /*modul gamifi  and institut*/
  

//
  Photo::updated(function ($photo) {
    if (!$photo->hasInstitution() ) {
      News::eventUpdatePhoto($photo, 'edited_photo');
    }
  });

  //of gamification
  Photo::deleted (function ($photo) {
    if ( ! $photo->hasInstitution() ) {
      Leaderboard::decreaseUserScore($photo->user_id, 'uploads');
    }
    Leaderboard::decreaseUsersScores($photo->evaluators, 'evaluations');
  });

//of gamification and binomial

  /*modules collaborative*/
  Like::created (function ($likes) { 
      News::eventLikedPhoto($likes,'liked_photo'); 
  });

  Comment::created (function ($comment) { 
      News::eventCommentedPhoto($comment,'commented_photo'); 
  });
<?php
  use modules\collaborative\models\Tag as Tag;
  use modules\collaborative\models\Comment as Comment;
  use modules\collaborative\models\Like as Like;
  use modules\news\models\News as News;
 
  Like::created (function ($likes) { 
    	News::eventLikedPhoto($likes,'liked_photo'); 
  });

  Comment::created (function ($comment) { 
    	News::eventCommentedPhoto($comment,'commented_photo'); 
  });
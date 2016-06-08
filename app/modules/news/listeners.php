<?php

  use modules\news\models\News as News;
 
  User::updated(function($user){
  	
  if(Input::hasFile('photo')){
  		News::eventNewPicture($user,'new_profile_picture'); 
  	}else{
  		News::eventUpdateProfile($user,'edited_profile'); 
  	}
  });

  Photo::created (function ($photo) {
    if (!$photo->hasInstitution() ) {
      News::eventNewPhoto($photo, 'new_photo');
    }
  });


  Photo::updated (function ($photo) {
    if (!$photo->hasInstitution() ) {
      News::eventUpdatePhoto($photo, 'edited_photo');
    }
  });
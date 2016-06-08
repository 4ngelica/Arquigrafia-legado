<?php

  use modules\news\models\News as News;
 
  User::updated(function($user){
  	
  	if(Input::hasFile('photo')){
  		News::eventNewPicture($user,'new_profile_picture'); 
  	}else{
  		News::eventUpdateProfile($user,'edited_profile'); 
  	}

  });
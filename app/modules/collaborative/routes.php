<?php
/* TAGS */
Route::get('/tags/json', 'modules\collaborative\controllers\TagsController@index');
Route::get('/tags/refreshCount', 'modules\collaborative\controllers\TagsController@refreshCount');

/* COMMENTARIOS */
Route::post('/comments/{photo_id}','modules\collaborative\controllers\CommentsController@comment');
Route::resource('/comments','modules\collaborative\controllers\CommentsController');

Route::get('/like/{id}', 'modules\collaborative\controllers\LikesController@photolike');
Route::get('/dislike/{id}', 'modules\collaborative\controllers\LikesController@photodislike');

/*LIKE DISLIKE of COMMENT*/
//Route::post('/photos/{photo_id}/comment','PhotosController@comment');
//Route::get('/comments/{comment_id}/like','lib\gamification\controllers\LikesController@commentlike');
//Route::get('/comments/{comment_id}/dislike','lib\gamification\controllers\LikesController@commentdislike');
 


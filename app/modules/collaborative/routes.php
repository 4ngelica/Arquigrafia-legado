<?php
/* TAGS */
Route::get('/tags/json', 'modules\collaborative\controllers\TagsController@index');
Route::get('/tags/refreshCount', 'modules\collaborative\controllers\TagsController@refreshCount');

/* COMMENTARIOS */
Route::post('/comments/{photo_id}','modules\collaborative\controllers\CommentsController@comment');
Route::get('/comments/{comment_id}/like','modules\collaborative\controllers\CommentsController@commentLike');
Route::get('/comments/{comment_id}/dislike','modules\collaborative\controllers\CommentsController@commentdislike');
Route::resource('/comments','modules\collaborative\controllers\CommentsController');

/* LIKE E DISLIKE */
Route::get('/like/{id}', 'modules\collaborative\controllers\LikesController@photolike');
Route::get('/dislike/{id}', 'modules\collaborative\controllers\LikesController@photodislike');
Route::resource('/likes','modules\collaborative\controllers\LikesController');
/*LIKE DISLIKE of COMMENT*/

 


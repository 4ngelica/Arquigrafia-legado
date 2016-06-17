<?php
/* TAGS */
Route::get('/tags/json', 'modules\collaborative\controllers\TagsController@index');
Route::get('/tags/refreshCount', 'modules\collaborative\controllers\TagsController@refreshCount');

/* COMMENTARIOS */
Route::post('/comments/{photo_id}','modules\collaborative\controllers\CommentsController@comment');
Route::get('/comments/{comment_id}/like','modules\collaborative\controllers\CommentsController@commentLike');
Route::get('/comments/{comment_id}/dislike','modules\collaborative\controllers\CommentsController@commentDislike');
Route::resource('/comments','modules\collaborative\controllers\CommentsController');

/* LIKE E DISLIKE */
Route::get('/like/{id}', 'modules\collaborative\controllers\LikesController@photoLike');
Route::get('/dislike/{id}', 'modules\collaborative\controllers\LikesController@photoDislike');
Route::resource('/likes','modules\collaborative\controllers\LikesController');

/* GRUPOS */
Route::resource('/groups','modules\collaborative\controllers\GroupsController');

/*EVENTS */
Event::subscribe('modules\collaborative\subscriber\LikeSubscriber');
Event::subscribe('modules\collaborative\subscriber\CommentSubscriber');



<?php

Route::get('/users/{id}/chats/test', 'modules\chat\controllers\ThreadsController@test');
Route::resource('users.chats', 'modules\chat\controllers\ThreadsController');
Route::post('/users/{id}/chats/test', 'modules\chat\controllers\MessagesController@sendMessage');
Route::post('/threads/messages', 'modules\chat\controllers\MessagesController@getMessages');
Route::post('/users/{userId}/chats/read', 'modules\chat\controllers\MessagesController@markThreadAsread');
Route::post('/users/searchName', 'modules\chat\controllers\MessagesController@searchUser');
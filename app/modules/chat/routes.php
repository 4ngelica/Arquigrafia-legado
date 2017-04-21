<?php

Route::resource('chats', 'modules\chat\controllers\ThreadsController');
Route::post('/chats/read', 'modules\chat\controllers\ThreadsController@markThreadAsread');
Route::resource('messages', 'modules\chat\controllers\MessagesController');

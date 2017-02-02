<?php

Route::get('/users/{id}/chats/test', 'modules\chat\controllers\ThreadsController@test');
Route::resource('users.chats', 'modules\chat\controllers\ThreadsController');
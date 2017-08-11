<?php

Route::post('/suggestions', 'modules\moderation\controllers\SuggestionsController@store');
Route::post('/suggestions/sent', 'modules\moderation\controllers\SuggestionsController@sendNotification');
Route::get('/users/suggestions', 'modules\moderation\controllers\SuggestionsController@edit');
Route::post('/users/suggestions', 'modules\moderation\controllers\SuggestionsController@update');

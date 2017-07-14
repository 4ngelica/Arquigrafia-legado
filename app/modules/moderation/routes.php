<?php

Route::post('/suggestions', 'modules\moderation\controllers\SuggestionsController@store');
Route::post('/suggestions/sent', 'modules\moderation\controllers\SuggestionsController@sendNotification');
Route::get('/users/{id}/suggestions', 'modules\moderation\controllers\SuggestionsController@edit');
Route::post('/users/{id}/suggestions', 'modules\moderation\controllers\SuggestionsController@update');

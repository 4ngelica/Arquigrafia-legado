<?php

Route::post('/suggestions', 'modules\moderation\controllers\SuggestionsController@store');
Route::get('/suggestions/sent', 'modules\moderation\controllers\SuggestionsController@sendNotification');

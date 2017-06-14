<?php

Route::post('/suggestions', 'modules\moderation\controllers\SuggestionsController@store');
Route::post('/suggestions/sent', 'modules\moderation\controllers\SuggestionsController@sendNotification');

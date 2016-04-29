<?php

/* INSTITUTIONS */
/*Route::get('/photos/{id}/get/field', 'lib\gamification\controllers\QuestionsController@getField');
Route::post('/photos/{id}/set/field', 'lib\gamification\controllers\QuestionsController@setField');
Route::get('/rank/get', 'lib\gamification\controllers\ScoresController@getRankEval');
Route::get('/leaderboard', 'lib\gamification\controllers\ScoresController@getLeaderboard');
Route::get('/badges/{id}', 'lib\gamification\controllers\BadgesController@show'); */
//TODO upload institutional
Route::get('/institutions/{id}', 'modules\institutions\controllers\InstitutionsController@show');
Route::get('/institutions/{id}/edit', 'modules\institutions\controllers\InstitutionsController@edit');
//Route::get('/institutions/upload','modules\institutions\controllers\InstitutionsController@formInstitutional');
Route::get('/institutions/upload','modules\institutions\controllers\InstitutionsController@form');
Route::get('/institutions/{photo_id}/editInstitutional','modules\institutions\controllers\InstitutionsController@editFormInstitutional');
Route::put('/institutions/{photo_id}/update/Institutional','modules\institutions\controllers\InstitutionsController@updateInstitutional');
Route::resource('/institutions','modules\institutions\controllers\InstitutionsController');
/* FOLLOW */
Route::get('/friends/followInstitution/{institution_id}', 'modules\institutions\controllers\InstitutionsController@followInstitution');
Route::get('/friends/unfollowInstitution/{institution_id}', 'modules\institutions\controllers\InstitutionsController@unfollowInstitution');
<?php 

/* DRAFTS */
Route::post('/drafts/delete', 'PhotosController@deleteDraft');
Route::get('/drafts/paginate', 'PhotosController@paginateDrafts');
Route::get('/drafts/{id}', 'PhotosController@getDraft');
Route::get('/drafts', 'PhotosController@listDrafts');
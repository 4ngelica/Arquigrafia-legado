<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/test', function () { 
  //testes
});

/* IMPORTS */
Route::get('/photos/import', 'ImportsController@import');

/* phpinfo() */
Route::get('/info/', function(){ return View::make('i'); });

Route::get('/', 'PagesController@home');
Route::get('/panel', 'PagesController@panel');
Route::get('/project', function() { return View::make('project'); });
Route::get('/faq', function() { return View::make('faq'); });
Route::get('/chancela', function() { return View::make('chancela'); });
Route::get('/termos', function() { return View::make('termos'); });

/* SEARCH */
Route::get('/search', 'PagesController@search');
Route::post('/search', 'PagesController@search');
Route::get('/search/more', 'PagesController@advancedSearch');

/* USERS */
Route::get('/users/account', 'UsersController@account');
Route::get('/users/register/', 'UsersController@emailRegister');
Route::get('/users/verify/{verifyCode}','UsersController@verify');
Route::get('/users/verify/','UsersController@verifyError');
Route::get('/users/login', 'UsersController@loginForm');
Route::post('/users/login', 'UsersController@login');
Route::get('/users/logout', 'UsersController@logout');
Route::get('users/login/fb', 'UsersController@facebook');
Route::get('users/login/fb/callback', 'UsersController@callback');
Route::get('/users/forget', 'UsersController@forgetForm');
Route::post('/users/forget', 'UsersController@forget');
Route::get('/getPicture', 'UsersController@getFacebookPicture');

Route::resource('/users','UsersController');
Route::resource('/users/stoaLogin','UsersController@stoaLogin');

Route::resource('/users/institutionalLogin','UsersController@institutionalLogin');


/* FOLLOW */
Route::get('/friends/follow/{user_id}', 'UsersController@follow');
Route::get('/friends/unfollow/{user_id}', 'UsersController@unfollow');

// AVATAR 
Route::get('/profile/10/showphotoprofile/{profile_id}', 'UsersController@profile');

Route::resource('/profile','ProfileController'); // lixo ?

/* ALBUMS */
Route::resource('/albums','AlbumsController');
Route::get('/albums/photos/add', 'AlbumsController@paginateByUser');
Route::get('/albums/{id}/photos/rm', 'AlbumsController@paginateByAlbum');
Route::get('/albums/{id}/photos/add', 'AlbumsController@paginateByOtherPhotos');
Route::get('/albums/get/list/{id}', 'AlbumsController@getList');
Route::post('/albums/photo/add', 'AlbumsController@addPhotoToAlbums');
Route::delete('/albums/{album_id}/photos/{photo_id}/remove', 'AlbumsController@removePhotoFromAlbum');

/* ALBUMS - ajax */
Route::get('/albums/get/cover/{id}', 'AlbumsController@paginateCoverPhotos');
Route::post('/albums/{id}/update/info', 'AlbumsController@updateInfo');
Route::post('/albums/{id}/detach/photos', 'AlbumsController@detachPhotos');
Route::post('/albums/{id}/attach/photos', 'AlbumsController@attachPhotos');
Route::get('/albums/{id}/paginate/photos', 'AlbumsController@paginateAlbumPhotos');
Route::get('/albums/{id}/paginate/other/photos', 'AlbumsController@paginatePhotosNotInAlbum');


/* COMMENTS */
Route::post('/photos/{photo_id}/comment','PhotosController@comment');
Route::get('/comments/{comment_id}/like','LikesController@commentlike');
Route::get('/comments/{comment_id}/dislike','LikesController@commentdislike');

/* EVALUATIONS */
Route::get('/photos/{photo_id}/saveEvaluation','PhotosController@saveEvaluation');
Route::post('/photos/{photo_id}/saveEvaluation','PhotosController@saveEvaluation');
Route::get('/photos/{photo_id}/evaluate','PhotosController@evaluate');
Route::post('/photos/{photo_id}/evaluate','PhotosController@evaluate');
Route::get('/photos/{photo_id}/viewEvaluation/{user_id}','PhotosController@viewEvaluation');
Route::get('/photos/{photo_id}/showSimilarAverage/', 'PhotosController@showSimilarAverage');

/* PHOTOS */
Route::get('/photos/{id}/like', 'LikesController@photolike');
Route::get('/photos/{id}/dislike', 'LikesController@photodislike');
Route::resource('/groups','GroupsController');
Route::get('/photos/batch','PhotosController@batch');
Route::get('/photos/upload','PhotosController@form');
Route::get('/photos/newUpload','PhotosController@newForm');
Route::get('/photos/{photo_id}/editInstitutional','PhotosController@editFormInstitutional');
Route::get('/photos/download/{photo_id}','PhotosController@download');
Route::get('/photos/savePhotoInstitutional','PhotosController@saveFormInstitutional');
Route::post('/photos/savePhotoInstitutional','PhotosController@saveFormInstitutional');
Route::resource('/photos','PhotosController');
/* TAGS */
Route::get('/tags/json', 'TagsController@index');

/* NOTIFICATIONS */
Route::get('/notifications', 'NotificationsController@show');
Route::get('/markRead/{id}', 'NotificationsController@read');
Route::get('/readAll', 'NotificationsController@readAll');
Route::get('/refreshBubble', 'NotificationsController@howManyUnread');


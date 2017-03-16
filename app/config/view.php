<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| View Storage Paths
	|--------------------------------------------------------------------------
	|
	| Most templating systems load templates from disk. Here you may specify
	| an array of paths that should be checked for your views. Of course
	| the usual Laravel view path has already been registered for you.
	|
	*/

	'paths' => array(
		__DIR__.'/../views',
		//__DIR__.'/../lib/gamification/views',
		__DIR__.'/../modules/draft/views',
		__DIR__.'/../modules/institutions/views',
		__DIR__.'/../modules/collaborative/views',
		__DIR__.'/../modules/evaluations/views',
		__DIR__.'/../modules/gamification/views',
		__DIR__.'/../modules/chat/views'
	),

	/*
	|--------------------------------------------------------------------------
	| Pagination View
	|--------------------------------------------------------------------------
	|
	| This view will be used to render the pagination link output, and can
	| be easily customized here to show any view you like. A clean view
	| compatible with Twitter's Bootstrap is given to you by default.
	|
	*/

	'pagination' => 'pagination::slider-3',

);

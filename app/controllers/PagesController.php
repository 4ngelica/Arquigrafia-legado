<?php

class PagesController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Page Controller
	|--------------------------------------------------------------------------
	*/

	public function home()
	{
    $photos = Photo::where('deleted', '=', '0')->orderByRaw("RAND()")->take(40)->get();
		return View::make('index', ['photos' => $photos]);
	}

}

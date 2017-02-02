<?php

namespace modules\chat\controllers;

use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Thread;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

class ThreadsController extends \BaseController {
	public function test($id){
		$user = \User::find($id);
		return View::make('test', ['output' => $user->name]);
	}

	public function create($userId) {
		return View::make('test', ['output' => 'create']);
	}

	public function destroy($userId) {
		return View::make('test', ['output' => 'destroy']);
	}

	public function index($userId) {
		return View::make('test', ['output' => 'index']);
	}

	public function show($userId, $chatId) {
		return View::make('test', ['output' => 'show']);
	}
}
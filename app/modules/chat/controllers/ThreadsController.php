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
		$thread = new Thread();
		$thread->scopeForUser(\DB::table('users'), 1);
		return View::make('test', ['output' => $thread]);
	}

	public function create($userId) {
		$input = Input::all();
		$thread = new Thread();
		$thread->subject = $input['subject'];
		$thread->save();

		//tratamento de recebimento da lista de participantes, assumindo array

		//usuario inicial
		$participant = new Participant();
		$participant->thread_id = $thread->id;
		$participant->user_id = $userId;
		$participant->last_read = Carbon::now();
		$participant->save();

		$thread->addParticipants($input['participants']);


		return View::make('test', ['output' => 'create']);
	}

	public function destroy($userId) {
		$input = Input::all();
		return View::make('test', ['output' => 'destroy']);
	}

	public function index($userId) {
		$thread = new Thread();
		$thread->scopeForUser(\DB::table('users'), $userId);
		return View::make('test', ['output' => 'index']);
	}

	public function show($userId, $chatId) {
		$thread = Thread::find($chatId);
		return View::make('test', ['output' => $thread]);
	}
}
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
use Artdarek\Pusherer\Facades\Pusherer;

class ThreadsController extends \BaseController {
	public function test($id) {
		// Pegar o chat que estamos
		// Através do chat pegar os participantes
		// Remover quem enviou
		// Enviar vários eventos para cada participante

		// Send notification to Pusher
    // $message = "This is just an example message!";
   // 	Pusherer::trigger('my-channel', 'my-event', array( 'message' => $message ));

		$thread = new Thread();
		$thread->scopeForUser(\DB::table('users'), 1);
		return View::make('test', ['output' => $thread, 'user_id' => $id]);
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

		$participants = explode(',', $input['participants']);
		$thread->addParticipants($participants);


		return View::make('test', ['output' => 'create']);
	}

	public function destroy($userId) {
		$input = Input::all();
		$user = User::find($userId);
		$thread = Thread::find($input['chat_id']);
		$participant = Participant::where('user_id', $userId)->where('thread_id', $thread->id)->first();
		$participant->delete();
		return View::make('test', ['output' => 'destroy']);
	}

	public function index($userId) {
		$thread = new Thread();
		$thread->scopeForUser(\DB::table('users'), $userId);
		return View::make('test', ['output' => 'index']);
	}

	public function show($userId, $chatId) {
		$thread = Thread::find($chatId);
		$messages = $thread->messages();
		$participants = $thread->participants();
		return View::make('test', ['output' => $thread]);
	}
}

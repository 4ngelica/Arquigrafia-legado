<?php

namespace modules\chat\controllers;

use User;
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

		// Getting current user info
		$user = \User::find($id);

		$thread = new Thread();
		$thread->scopeForUser(\DB::table('users'), 1);
		return View::make('chats', ['output' => $thread, 'user_id' => $id, 'user_name' => $user->name]);
	}

	public function create($userId) {
		try {
			$input = Input::all();
			$thread = new Thread();
			$subject = null;
			$thread->save();

			//tratamento de recebimento da lista de participantes, assumindo array

			//usuario inicial
			$participant = new Participant();
			$participant->thread_id = $thread->id;
			$participant->user_id = $userId;
			$participant->last_read = Carbon::now();
			$participant->save();

			$participants = $input['participants'];
			$thread->addParticipants($participants);

			return \Response::json($thread->id);
		} catch (Exception $error) {
			return \Response::json('Erro ao realizar operação.', 500);
		}
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
		$user = User::find($userId);
		$threads = $user->threads()->get();
		$array = array();

		for($i = 0; $i < count($threads); $i++) {
			$participants = $threads[$i]->participants()->get();
			$last_message = $messages = Message::where('thread_id', $threads[$i]->id)->orderBy('id', 'desc')->take(1)->get();
			array_push($array, [$i => ['thread' => $threads[0], 'participants' => $participants], 'last_message' => $last_message]);
		}
		
		return View::make('chats', ['data' => $array, 'user_id' => $userId, 'user_name' => $user->name]);
	}

	public function show($userId, $chatId) {
		$thread = Thread::find($chatId);
		$messages = $thread->messages()->get();
		$participants = $thread->participants()->get();
		return View::make('test', ['output' => $thread]);
	}
}

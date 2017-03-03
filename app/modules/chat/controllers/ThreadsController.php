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

	public function store($userId) {
		try {
			$input = Input::all();
			$thread = new Thread();
			$subject = null;
			$thread->save();

			/*
			 * Creating a list of participants, to create a thread
			 */

			// Creating initial participant
			$participant = new Participant();
			$participant->thread_id = $thread->id;
			$participant->user_id = $userId;
			$participant->last_read = Carbon::now();
			$participant->save();

			// On the input variable, this function receives an array of participants ids
			$participants = $input['participants'];
			// Creating the participants with participants ids array
			$thread->addParticipants($participants);
			// Getting all the participants from the current thread
			$participants = $thread->participants()->get();

			foreach($participants as $participant){
				// If the current participant user_id equals the current userId, continue
				if($participant->user_id == $userId)
					continue;
				// Triggering event with Pusherer
				Pusherer::trigger(strval($participant->user_id), 'new_thread', array( 'thread' => $thread ));
			}

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
			$participants = $threads[$i]->participants()->with(array('user' => function($query) {
				$query->select('id', 'name');
			}))->get();
			$names = $threads[$i]->participantsString($userId);
			$last_message = Message::where('thread_id', $threads[$i]->id)->orderBy('id', 'desc')->take(1)->get()->first();
			array_push($array, ['thread' => $threads[$i], 'participants' => $participants,
									   'names' => $names, 'last_message' => $last_message]);
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

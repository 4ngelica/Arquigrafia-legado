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
use lib\log\EventLogger;

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
		// $user = \User::find($id);

		// $thread = new Thread();
		// $thread->scopeForUser(\DB::table('users'), 1);
		// return View::make('chats', ['output' => $thread, 'user_id' => $id, 'user_name' => $user->name]);
	}

	public function store() {
		$input = Input::all();
		$user = Auth::user();
		try {
			//validacao 50 participantes
			if(count($input['participants']) >= 49)
				throw new Exception('Limite de participantes excedido.');
			//validacao de duplicatas de threads entre 2 usuarios
			if(count($input['participants']) == 1){
				$threads = $user->threads()->get();
				foreach($threads as $try){
					if(count($try->participants()->get()) != 2)
						continue;
					if(
						$try->hasParticipant($input['participants'][0]))
						return \Response::json($try->id);
				}
			}

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
			$participant->user_id = $user->id;
			$participant->last_read = Carbon::now();
			$participant->save();

			// On the input variable, this function receives an array of participants ids
			$participants = $input['participants'];
			// Creating the participants with participants ids array
			$thread->addParticipants($participants);
			// Getting all the participants from the current thread
			$participants = $thread->participants()->get();

			foreach($participants as $participant){
				// Getting the chat name
				$names = $thread->participantsString($participant->user_id);
				// Adding user object to participant
				$participant->user = \User::find($participant->user_id);

				// Triggering event with Pusherer
				Pusherer::trigger(strval($participant->user_id), 'new_thread', array('thread' => $thread, 'participants' => $participants, 'names' => $names));
			}
			EventLogger::printEventLogs(null, 'new_thread', ['thread' => $thread->id, 'participants' => $participants], 'Web');

			return \Response::json($thread->id);
		} catch (Exception $error) {
			return \Response::json('Erro ao realizar operação.', 500);
		}
	}

	public function destroy($id) {
		$user = Auth::user();
		$input = Input::all();
		$thread = Thread::find($id);
		$participant = Participant::where('user_id', $user->id)->where('thread_id', $thread->id)->first();
		$participant->delete();
		if(count($thread->participants()->get()) < 2)
			$thread->delete();
		return View::make('test', ['output' => 'destroy']);
	}

	public function index() {
		$user = Auth::user();
		$threads = $user->threads()->get();
		$array = array();

		for($i = 0; $i < count($threads); $i++) {
			$participants = $threads[$i]->participants()->with(array('user' => function($query) {
				$query->select('id', 'name', 'lastName', 'photo');
			}))->get();
			$names = $threads[$i]->participantsString($user->id);
			$last_message = Message::where('thread_id', $threads[$i]->id)->orderBy('id', 'desc')->take(1)->get()->first();
			array_push($array, ['thread' => $threads[$i], 'participants' => $participants,
									   'names' => $names, 'last_message' => $last_message]);
		}
		return View::make('chats', ['data' => $array, 'user_id' => $user->id, 'user_name' => $user->name]);
	}

	public function show($id) {
		$thread = Thread::find($id);
		$messages = $thread->messages()->get();
		$participants = $thread->participants()->get();
		return View::make('test', ['output' => $thread]);
	}

	public function searchUser(){
		$input = Input::all();
		$text = $input['text'];
		$result = User::where('name', 'LIKE', '%' . $text . '%')->orWhere('lastName',
			'LIKE', '%' . $text . '%')->orderBy('name')->select('id', 'name', 'lastName', 'photo')->get();
		return \Response::json($result);
	}

	public function markThreadAsRead(){
		$user = Auth::user();
		try{
			$input = Input::all();
			$thread = Thread::find($input['thread_id']);
			$thread->markAsRead($user->id);
			return \Response::json(true);
		} catch (Exception $error){
			return \Response::json(false);
		}
	}
}

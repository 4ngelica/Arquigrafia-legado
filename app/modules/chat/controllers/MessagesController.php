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

class MessagesController extends \BaseController {
	public function sendMessage($id) {
		// Saving message
		$input = Input::all();
		$message = new Message();
		$message->thread_id = $input['thread_id'];
		$message->body = $input['message'];
		$message->user_id = $id;
		$message->save();

		// Getting current user data
		$user = \User::find($id);

		// Find thread that we wanna send the message
		$thread = Thread::find($message->thread_id);
		// Getting all participants from that thread
		$participants = $thread->participants()->get();
		// Sending pusherer events
		foreach($participants as $participant) {
			// If the participant is the logged user, don't sender the Pusher event
			if($participant->user_id == $id)
				continue;
			// Sending Pusher event
			Pusherer::trigger(strval($participant->user_id), strval($message->thread_id), array( 'name' => $user->name, 'message' => $message->body ));;
		}

	}

	public function getMessages(){
		$input = Input::all();
		$id = $input['thread_id'];
		$thread = Thread::find($id);
		$messages = $thread->messages()->get();
		return \Response::json($messages);
	}

	private static function sendEvents($userId, $threadId, $message){

	}
}

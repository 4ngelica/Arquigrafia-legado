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
	public function sendMessage($id){
		$input = Input::all();
		$message = new Message();
		$message->thread_id = $input['thread_id'];
		$message->body = $input['message'];
		$message->user_id = $id;
		$message->save();

		//MessagesController::sendEvents($id, $message->thread_id, $message);
		$thread = Thread::find($message->thread_id);
		$participants = $thread->participants()->get();
		foreach($participants as $participant) {
			if($participant->user_id == $id)
				continue;
			Pusherer::trigger('2_channel', 'new_message', array( 'message' => $message ));;
		}

	}

	private static function sendEvents($userId, $threadId, $message){

	}
}
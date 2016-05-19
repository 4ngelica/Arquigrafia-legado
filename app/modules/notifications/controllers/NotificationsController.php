<?php
namespace modules\notifications\controllers;
use modules\nofifications\models\Notification;
use lib\utils\ActionUser;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Auth;
use Session;
use Request;
use View;

class NotificationsController extends \BaseController {
	
	public function show() { 
		if (Auth::check()) {
			$user = Auth::user();
			$source_page = Request::header('referer');
			ActionUser::printNotification($user->id, $source_page, "user");
			$time_and_date_now = Carbon::now('America/Sao_Paulo');
			foreach ($user->notifications as $notification) {
				$time_and_date_note = Carbon::createFromFormat('Y-m-d H:i:s', $notification->updated_at);
				if ($time_and_date_now->diffInMonths($time_and_date_note) > 1 && $notification->read_at != null) $notification->delete();
			}
			$max_notes = $user->notifications->count();	
			return View::make('notifications')->with(['user'=>$user, 'max_notes'=>$max_notes]);
		}
		return Redirect::action('PagesController@home');
	}

    public function read($id) {
		if (Auth::check()) {
			$user = Auth::user();
			$note = $user->notifications()->find($id);
			$note->setRead();
			return $user->notifications()->unread()->count();
		}
	}

	public function readAll() {
		if (Auth::check()) {
			$user = Auth::user();
			foreach ($user->notifications as $note) {
				$note->setRead();
			}
			return $user->notifications()->unread()->count();
		}
	}

	public function howManyUnread() {
		if (Auth::check()) {
			$user = Auth::user();
			return $user->notifications()->unread()->count();
		}
	}

	public static function isNotificationByUser($user_id, $note_sender_id, $note_data){
		if ($user_id == $note_sender_id) return true;
		elseif ($note_data != null) {
			$users = explode(":", $note_data);
			foreach ($users as $user) {
				if ($user == $user_id) return true;
			}
		}
		return false;
	}
}

?>
<?php

use lib\utils\ActionUser;
use Carbon\Carbon;

class NotificationsController extends \BaseController {
	
	public function show() { 
		if (Auth::check()) {
			$user = Auth::user();
			$source_page = Request::header('referer');
			ActionUser::printNotification($user->id, $source_page, "user");
			$time_and_date_now = Carbon::now('America/Sao_Paulo');
			foreach ($user->notifications as $notification) {
				$time_and_date_note = Carbon::createFromFormat('Y-m-d H:i:s', $notification->updated_at);
				if ($time_and_date_now->diffInMonths($time_and_date_note) > 6) $notification->delete();
			}	
			return View::make('notifications')->with('user', $user);
		}
		return Redirect::action('PagesController@home');
	}

    public function read($id) {
		if (Auth::check()) {
			$user = Auth::user();
			$unreadNotes = $user->notifications()->unread()->get();
			foreach ($unreadNotes as $notification) {
				if($notification->id == $id) $notification->setRead();
			}
		}
	}
}

?>
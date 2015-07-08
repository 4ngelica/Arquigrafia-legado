<?php

use lib\utils\ActionUser;

class NotificationsController extends \BaseController {
	
	public function show() { 
		if (Auth::check()) {
			$user = Auth::user();
			$source_page = Request::header('referer');
			ActionUser::printNotification($user->id, $source_page, "user");
			return View::make('notifications')->with('user', $user);
		}
		return Redirect::action('PagesController@home');
	}

	public static function verifyUnread($notification, $unreadNotifications) {
        foreach ($unreadNotifications as $unread) {
            if ($notification->id == $unread->id) return true;
        }
        return false;
    }

    public function read($id) {
		if (Auth::check()) {
			$user = Auth::user();
			foreach ($user->notifications as $notification) {
				//if () $notification->delete();
			}
			$unreadNotes = $user->notifications()->unread()->get();
			foreach ($unreadNotes as $notification) {
				if($notification->id == $id) $notification->setRead();
			}
		}
		return 0;
	}
}

?>
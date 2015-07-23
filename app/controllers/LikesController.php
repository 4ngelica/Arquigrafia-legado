<?php

use lib\utils\ActionUser;
use Carbon\Carbon;

class LikesController extends \BaseController {

  public function photolike($id) {
    $photo = Photo::find($id);
    $user = Auth::user();
    $this->logLikeDislike($user, $photo, "a foto", "Curtiu", "user");
    
    /*Envio de notificação*/
    if ($user->id != $photo->user_id) {
      $user_note = User::find($photo->user_id);
      foreach ($user_note->notifications as $notification) {
        $info = $notification->render();
        if ($info[0] == "photo_liked" && $info[2] == $id) {
          $note_id = $notification->notification_id;
          $note_user_id = $notification->id;
          $note = $notification;
        }
      }
      if (isset($note_id) && $note->read_at == null) {
        $note_from_table = DB::table("notifications")->where("id","=", $note_id)->get();
        if (NotificationsController::isNotificationByUser($user->id, $note_from_table[0]->sender_id, $note_from_table[0]->data) == false) {
          $new_data = $note_from_table[0]->data . ":" . $user->id;
          DB::table("notifications")->where("id", "=", $note_id)->update(array("data" => $new_data, "created_at" => Carbon::now('America/Sao_Paulo')));
          $note->created_at = Carbon::now('America/Sao_Paulo');
          $note->save();  
        }
      }
      else Notification::create('photo_liked', $user, $photo, [$user_note], null);
    }

    return $this->like($photo, $user);
  }

  public function photodislike($id) {
    $photo = Photo::find($id);
    $user = Auth::user();
    $this->logLikeDislike($user, $photo, "a foto", "Descurtiu", "user");
    return $this->dislike($photo, $user);
  }

  public function commentdislike($id) {
    $comment = Comment::find($id);
    $user = Auth::user();
    $source_page = Request::header('referer');
    $this->logLikeDislike($user, $comment, "o comentário", "Descurtiu", "user");
    return $this->dislike($comment, $user);
  }


  public function commentlike($id) {
    $comment = Comment::find($id);
    $user = Auth::user();
    $this->logLikeDislike($user, $comment, "o comentário", "Curtiu", "user");
    $response = $this->like($comment, $user);
    $this->checkLikesCount($comment, 5, 'test'); 

    if ($user->id != $comment->user_id) {
      $user_note = User::find($comment->user_id);
      Notification::create('comment_liked', $user, $comment, [$user_note], null);
    }

    return $response;
  }

  private function like($likable, $user) {
    try {
      $like = Like::getLike($likable, $user);
      $likable_type = strtolower(get_class($likable));
    } catch (Exception $e) {
      //
    }
    return $this->getLikeResponse($likable_type, $likable, 'dislike');
  }
  
  private function dislike($likable, $user) {
    try {
      $like = Like::fromUser($user)->withLikable($likable)->first();
      $likable_type = strtolower(get_class($likable));
      $like->delete();
    } catch (Exception $e) {
      //
    }
    return $this->getLikeResponse($likable_type, $likable, 'like');
  }

  private function getLikeResponse($likable_type, $likable, $like_or_dislike) {
    if (is_null($likable)) {
      return Response::json('fail');
    }
    return Response::json([ 
      'url' => URL::to('/' . $likable_type . 's/' . $likable->id . '/' . $like_or_dislike),
      'likes_count' => $likable->likes->count()]);
  }

  private function checkLikesCount($likable, $number_likes, $badge_name) {
    if ($likable->badge) {
      return ;
    }
    if ($likable->likes->count() == $number_likes) {
      $badge = Badge::where('name', $badge_name)->first();
      $likable->user->badges()->attach($badge);
      $likable->attachBadge($badge);
    }
  }

  private function logLikeDislike($user, $likable, $photo_or_comment, $like_or_dislike, $user_or_visitor) {
    $source_page = Request::header('referer');
    ActionUser::printLikeDislike($user->id, $likable->id, $source_page, $photo_or_comment, $like_or_dislike, $user_or_visitor);
  }
}
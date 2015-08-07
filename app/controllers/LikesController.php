<?php

use lib\utils\ActionUser;

class LikesController extends \BaseController {

  public function photolike($id) {
    $photo = Photo::find($id);
    $user = Auth::user();
    $this->logLikeDislike($user, $photo, "a foto", "Curtiu", "user");
    
    if ($user->id != $photo->user_id) {
      $user_note = User::find($photo->user_id);
      Notification::create('photo_liked', $user, $photo, [$user_note], null);
    }
    $like = Like::getLike($photo, $user);
    Badge::checkBadgeLike($photo, $user);
    if (is_null($photo)) {
      return Response::json('fail');
    }
    return Response::json([ 
      'url' => URL::to('/photos/' . $photo->id . '/' . 'dislike'),
      'likes_count' => $photo->likes->count()]);
  }

  public function photodislike($id) {
    $photo = Photo::find($id);
    $user = Auth::user();
    $this->logLikeDislike($user, $photo, "a foto", "Descurtiu", "user");
    try {
      $like = Like::fromUser($user)->withLikable($photo)->first();
      $likable_type = strtolower(get_class($photo));
      $like->delete();
    } catch (Exception $e) {
      //
    }
    if (is_null($photo)) {
      return Response::json('fail');
    }
    return Response::json([ 
      'url' => URL::to('/photos/' . $photo->id . '/' . 'like'),
      'likes_count' => $photo->likes->count()]);
  }

  public function commentdislike($id) {
    $comment = Comment::find($id);
    $user = Auth::user();
    $source_page = Request::header('referer');
    $this->logLikeDislike($user, $comment, "o comentário", "Descurtiu", "user");
    try {
      $like = Like::fromUser($user)->withLikable($comment)->first();
      $likable_type = strtolower(get_class($comment));
      $like->delete();
    } catch (Exception $e) {
      //
    }
    if (is_null($comment)) {
      return Response::json('fail');
    }
    return Response::json([ 
      'url' => URL::to('/comments/' . $comment->id . '/' . 'like'),
      'likes_count' => $comment->likes->count()]);
  }

  


  public function commentlike($id) {
    $comment = Comment::find($id);
    $user = Auth::user();
    $this->logLikeDislike($user, $comment, "o comentário", "Curtiu", "user");
    
    if ($user->id != $comment->user_id) {
      $user_note = User::find($comment->user_id);
      Notification::create('comment_liked', $user, $comment, [$user_note], null);
    }

    $like = Like::getLike($comment, $user);
    Badge::checkBadgeLike($comment, $user);
    if (is_null($comment)) {
      return Response::json('fail');
    }
    return Response::json([ 
      'url' => URL::to('/comments/' . $comment->id . '/' . 'dislike'),
      'likes_count' => $comment->likes->count()]);
  }


  private function logLikeDislike($user, $likable, $photo_or_comment, $like_or_dislike, $user_or_visitor) {
    $source_page = Request::header('referer');
    ActionUser::printLikeDislike($user->id, $likable->id, $source_page, $photo_or_comment, $like_or_dislike, $user_or_visitor);
  }
}
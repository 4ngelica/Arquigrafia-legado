<?php namespace modules\collaborative\controllers;
use modules\collaborative\models\Like;
use modules\collaborative\models\Comment;
use lib\utils\ActionUser;
use modules\news\models\News as News;
use modules\gamification\models\Badge;
use Notification;
use Carbon\Carbon;

class LikesController extends \BaseController {

  public function photoLike($id) {

    $photo = \Photo::find($id);
    $user = \Auth::user();
    if (is_null($photo)) {
      return \Response::json('fail');
    }

    \Event::fire('photo.like', array($user, $photo));

    $this->logLikeDislike($user, $photo, "a foto", "Curtiu", "user");
    if ($user->id != $photo->user_id) {
      $user_note = \User::find($photo->user_id);
      /*News feed*/
      foreach ($user->followers as $users) {
        foreach ($users->news as $news) {
          if ($news->news_type == 'liked_photo') {
            if ($news->object_id == $id) {
              $last_news = $news;
              $primary = 'liked_photo';
            }
          }
          else if ($news->news_type == 'evaluated_photo') {
            if ($news->object_id == $id) {
              $last_news = $news;
              $primary = 'other';
            }
          }
          else if ($news->news_type == 'commented_photo') {
            $comment = Comment::find($news->object_id);
            if(!is_null($comment)) {
              if ($comment->photo_id == $id) {
                $last_news = $news;
                $primary = 'other';
              }
            }
          }
        }
        if (isset($last_news)) {
          $last_update = $last_news->updated_at;
          if($last_update->diffInDays(Carbon::now('America/Sao_Paulo')) < 7) {
            if ($news->sender_id == $user->id) {
              $already_sent = true;
            }
            else if ($news->data != null) {
              $data = explode(":", $news->data);
              for($i = 1; $i < count($data); $i++) {
                if($data[$i] == $user->id) {
                  $already_sent = true;
                }
              }
            }
            if (!isset($already_sent)) {
              $data = $last_news->data . ":" . $user->id;
              $last_news->data = $data;
              $last_news->save();
            }
            if ($primary == 'other') {
              if ($last_news->secondary_type == null) {
                $last_news->secondary_type = 'liked_photo';
              }
              else if ($last_news->tertiary_type == null) {
                $last_news->tertiary_type = 'liked_photo';
              }
              $last_news->save();
            }
          }
          else {
            News::create(array('object_type' => 'Photo', 
                                'object_id' => $id, 
                                'user_id' => $users->id, 
                                'sender_id' => $user->id, 
                                'news_type' => 'liked_photo'));
          }
        }
        else {
          News::create(array('object_type' => 'Photo', 
                              'object_id' => $id, 
                              'user_id' => $users->id, 
                              'sender_id' => $user->id, 
                              'news_type' => 'liked_photo'));
        }
      }
    }
    $like = Like::getFirstOrCreate($photo, $user);
    
    return \Response::json([ 
      'url' => \URL::to('/dislike/' . $photo->id),
      'likes_count' => $photo->likes->count()
    ]);
  }

  public function photoDislike($id) {
    $photo = \Photo::find($id);
    $user = \Auth::user();

    \Event::fire('photo.dislike', array($user, $photo));

    $this->logLikeDislike($user, $photo, "a foto", "Descurtiu", "user");

    if (is_null($photo)) {
      return \Response::json('fail');
    }
    return \Response::json([ 
      'url' => \URL::to('/like/' . $photo->id),
      'likes_count' => $photo->likes->count()]);
  }




  private function logLikeDislike($user, $likable, $photo_or_comment, $like_or_dislike, $user_or_visitor) {
    $source_page = \Request::header('referer');
    ActionUser::printLikeDislike($user->id, $likable->id, $source_page, $photo_or_comment, $like_or_dislike, $user_or_visitor);
  }
}
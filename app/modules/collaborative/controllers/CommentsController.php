<?php
namespace modules\collaborative\controllers;
use modules\collaborative\models\Comment;
use lib\date\Date;
use lib\utils\ActionUser;
use News;
use Input;
use Photo;
use Auth;
use User;
use Notification;


class CommentsController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{ 
		$comment = Comment::all();
    return $comment;
	}

	
	public function comment($id)
  { 
    	$input = Input::all();
    	$rules = ['text' => 'required'];
    	$validator = \Validator::make($input, $rules);
    	if ($validator->fails()) {
      	 $messages = $validator->messages();
      	 return \Redirect::to("/photos/{$id}")->withErrors($messages);
    	} else { 
      	$comment = ['text' => $input["text"], 'user_id' => Auth::user()->id];
      	$comment = new Comment($comment);        
      	$photo = Photo::find($id);
      	$photo->comments()->save($comment);
        
        $user = Auth::user();
        $source_page = \Request::header('referer');
        ActionUser::printComment($user->id, $source_page, "Inseriu", $comment->id, $id, "user");
      
        /*Envio de notificação*/
        if ($user->id != $photo->user_id) { 
            $user_note = User::find($photo->user_id);
            foreach ($user_note->notifications as $notification) {
                $info = $notification->render();
                if ($info[0] == "comment_posted" && $info[2] == $photo->id && $notification->read_at == null) {
                    $note_id = $notification->notification_id;
                    $note_user_id = $notification->id;
                    $note = $notification;
                }
            }
            if (isset($note_id)) {
                $note_from_table = \DB::table("notifications")->where("id","=", $note_id)->get();
                if (\NotificationsController::isNotificationByUser($user->id, $note_from_table[0]->sender_id, $note_from_table[0]->data) == false) {
                    $new_data = $note_from_table[0]->data . ":" . $user->id;
                    \DB::table("notifications")->where("id", "=", $note_id)->update(array("data" => $new_data, "created_at" => Carbon::now('America/Sao_Paulo')));
                    $note->created_at = Carbon::now('America/Sao_Paulo');
                    $note->save();  
                }
            }
            else Notification::create('comment_posted', $user, $comment, [$user_note], null);
        }

        /*News feed*/
        foreach ($user->followers as $users) {
            foreach ($users->news as $news) {  
              if ($news->news_type == 'commented_photo' && Comment::find($news->object_id)->photo_id == $id) {
                  $last_news = $news;
                  $primary = 'commented_photo';
              }else if ($news->news_type == 'evaluated_photo' || $news->news_type == 'liked_photo') {
                if ($news->object_id == $id) {
                  $last_news = $news;
                  $primary = 'other';
                }
              }
            }
            if (isset($last_news)) {
                $last_update = $last_news->updated_at;
                if($last_update->diffInDays(Carbon::now('America/Sao_Paulo')) < 7) {
                    if ($news->sender_id == $user->id) {
                        $already_sent = true;
                    }else if ($news->data != null) {
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
                            $last_news->secondary_type = 'commented_photo';
                        }else if ($last_news->tertiary_type == null) {
                            $last_news->tertiary_type = 'commented_photo';
                        }
                        $last_news->save();
                    }
                } else {
                    \News::create(array('object_type' => 'Comment', 
                               'object_id' => $comment->id, 
                               'user_id' => $users->id, 
                               'sender_id' => $user->id, 
                               'news_type' => 'commented_photo'));
                }
            }else {
                \News::create(array('object_type' => 'Comment', 
                             'object_id' => $comment->id, 
                             'user_id' => $users->id, 
                             'sender_id' => $user->id, 
                             'news_type' => 'commented_photo'));
            }
        }  
        $this->checkCommentCount(5,'test');
        return \Redirect::to("/photos/{$id}");
      }
  }


// need to be modified
    private function checkCommentCount($number_comment, $badge_name){
      $user = Auth::user();
      if(($user->badges()->where('name', $badge_name)->first()) != null){
        return;
      }
      if (($user->comments->count()) == $number_comment){
        $badge=Badge::where('name', $badge_name)->first();
        $user->badges()->attach($badge);
      }
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}



}

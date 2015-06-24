	<?php
	//add
	use lib\utils\ActionUser;

class LikesController extends \BaseController {

	public function photolike($id) {
	    $photo = Photo::find($id);
	    $user_id = Auth::user()->id;
      	$source_page = Request::header('referer');
      	ActionUser::printLikeDislike($user_id, $photo->id, $source_page, "a foto", "Curtiu", "user");

      	//TESTE DE NOTIFICAÇÕES
      	$user = Auth::user();
      	$user_note = User::find($photo->user_id);
      	Notification::create('photo_liked', $user, $photo, [$user_note], null);

	    return $this->like('photo',$photo);
	}

	 public function photodislike($id) {
	    $photo = Photo::find($id);
	    $user_id = Auth::user()->id;
      	$source_page = Request::header('referer');
      	ActionUser::printLikeDislike($user_id, $photo->id, $source_page, "a foto", "Descurtiu", "user");
	    return $this->dislike('photo', $photo);
	}

	public function commentdislike($id) {
		$comment = Comment::find($id);
		$user_id = Auth::user()->id;
      	$source_page = Request::header('referer');
      	ActionUser::printLikeDislike($user_id, $comment->id, $source_page, "o comentário", "Descurtiu", "user");
		return $this->dislike('comment',$comment);
	}


	public function commentlike($id) {
	    $comment = Comment::find($id);
	    $user_id = Auth::user()->id;
      	$source_page = Request::header('referer');
      	ActionUser::printLikeDislike($user_id, $comment->id, $source_page, "o comentário", "Curtiu", "user");
	    $response = $this->like('comment',$comment);
		$this->checkLikesCount($comment, 5, 'test'); 
		return $response;
	}

	private function like($model, $model_object) {
		$user_id = Auth::id();
		if(is_null($model_object)) {
	    	return 'fail';
		}

	    $like = Like::firstOrCreate(array( 
	        "user_id" => $user_id,
	        $model ."_id" => $model_object->id));
	     
	    return Response::json([ 
	        'url' => URL::to('/'. $model .'s/' . $model_object->id . '/dislike'),
	        'likes_count' => $model_object->likes->count()]);

	}
	private function dislike($model, $model_object) {
	    $user_id = Auth::id();
	    $like = Like::where("user_id", $user_id)
	       	->where($model . "_id", $model_object->id)->first();
	      	if(isset($like)){
	        	$like->delete();
	      	}
	      	return Response::json([ 
	        	'url' => URL::to('/' . $model . 's/' . $model_object->id . '/like'),
	        	'likes_count' => $model_object->likes->count()]);

	}

	private function checkLikesCount($model_object, $number_likes, $badge_name) {
		if ($model_object->badge) {
			return ;
		}
		if ($model_object->likes->count()== $number_likes){
			$badge=Badge::where('name', $badge_name)->first();
			$model_object->user->badges()->attach($badge);
			$model_object->attachBadge($badge);
		}
	}
}
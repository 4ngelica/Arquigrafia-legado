	<?php
	//add
	use lib\utils\ActionUser;

	class LikesController extends \BaseController {

	public function photolike($id)
	    {
	      $photo = Photo::find($id);
	      return $this->like('photo',$photo);
	    }

	 public function photodislike($id)
	 {
	      $photo = Photo::find($id);
	      return $this->dislike('photo', $photo);
	 }

	public function commentdislike($id){
		$comment=Comment::find($id);
		return $this->dislike('comment',$comment);

	}


	public function commentlike($id){
	    $comment=Comment::find($id);
	    return $this->like('comment',$comment);
	 }

	 private function like($model, $model_object){
	 	$user_id = Auth::id();
	      if(is_null($model_object)){
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

	}
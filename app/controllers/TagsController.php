<?php

class TagsController extends \BaseController {

	public function index()
	{
		$tags = Tag::all();
		return $tags;
	}

	public function refreshCount() {
		$photos = Photo::all();
		DB::update('update tags set count = 0');
		foreach ($photos as $photo) {
			$photo_tags = $photo->tags;
			foreach ($photo_tags as $tag) {
				$tag->count = $tag->count + 1;
				$tag->save();
			}
		}
		$deleted = Photo::onlyTrashed()->get();
		foreach ($deleted as $photo) {
			DB::table('tag_assignments')->where('photo_id', '=', $photo->id)->delete();
		}
	}

}

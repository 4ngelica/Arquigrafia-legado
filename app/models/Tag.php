<?php

class Tag extends Eloquent {

	public $timestamps = false;

	protected $fillable = ['name'];

	public function photos() {
		return $this->belongsToMany('Photo', 'tag_assignments', 'tag_id', 'photo_id');
	}

	public function photosTags($tags){
		  $query = Tag::whereIn('name', $tags);
		  $tagsResult = $query->get();  
          $listTags= $tagsResult->lists('id');

			
				
				$photosTagAssignment = DB::table('tag_assignments')
				->select('photo_id')
				->whereIn('tag_id',$listTags)				
				->get(); 

				$listPhotos=$photosTagAssignment->lists('photo_id');

				$photos = Photo::wherein('id',$listPhotos);
			
				return $photos;

		
	}
}
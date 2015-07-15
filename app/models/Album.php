<?php

class Album extends \BaseModel {

	public $timestamps = false;

	protected $fillable = ['creationDate', 'description', 'title', 'cover_id', 'user_id'];

	protected $rules = [
		'title' => 'required'
	];

	public function photos()
	{
		return $this->belongsToMany('Photo', 'album_elements');
	}

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function cover()
	{
		return $this->belongsTo('Photo', 'cover_id');
	}

	public function updateInfo($title, $description, $cover) {
		$this->title = $title;
		$this->description = $description;
		if (isset($cover)) {
			$this->cover()->associate($cover);
		}
	}

	public static function create(array $attr) {
		$album = new Album;
		$album->updateInfo($attr['title'], $attr['description'], $attr['cover']);
		$album->creationDate = date('Y-m-d H:i:s');
		$album->user()->associate($attr['user']);
		$album->save();
		return $album;
	}

	public function delete() {
		$this->detachPhotos();
		parent::delete();
	}

	public function detachPhotos($photos = array()) {
		if ($photos instanceof Photo) {
			$this->photos()->detach($photos->id);
		} else {
			$this->photos()->detach($photos);
		}
	}

	public function attachPhotos($photos = array()) {
		if ($photos instanceof Photo) {
			$this->photos()->attach($photos->id);
		} else {
			$this->photos()->attach($photos);
		}
	}

	public function syncPhotos($photos = array(), $delete = false) {
		if ($photos instanceof Photo) {
			$this->photos()->sync(array($photos->id), $delete);
		} else {
			$this->photos()->sync($photos, $delete);
		}
	}

	public function hasCover() {
		return !is_null($this->cover);
	}

	public function scopeWithUser($query, $user) {
		return $query->where('user_id', $user->id);
	}

	public function scopeExcept($query, $albums) {
		if ($albums instanceof Album) {
			return $query->where('id', '!=', $albums->id);
		}
		//instance of Eloquent\Collection
		return $query->whereNotIn('id', $albums->modelKeys());
	}
}
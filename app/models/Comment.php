<?php

class Comment extends Eloquent {

	protected $fillable = ['text', 'user_id', 'photo_id'];

	public function photo()
	{
		return $this->belongsTo('Photo');
	}

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function likes()
	{
		return $this->morphMany('Like', 'likable');
	}

	public function badge() {
		return $this->belongsTo('Badge');
	}

	public function attachBadge($badge) {
		$this->badge_id = $badge->id;
		$this->save();
	}
}
<?php

class Like extends Eloquent {

	protected $table = "likes";
	protected $fillable = [ 'user_id', 'photo_id','comment_id'];
	
	public function photo()
	{
		return $this->belongsTo('Photo');
	}

	public function user()
	{
		return $this->belongsTo('User');
	}
	public function comment()
	{
		return $this->belongsTo('comment');
	}
}
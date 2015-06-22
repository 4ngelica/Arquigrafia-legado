<?php

class Badge extends Eloquent {

	protected $fillable = ['name', 'image', 'description'];


	public function users()
	{
		return $this->belongsToMany('User','user_badges');
	}

	public function comments()
	{
		return $this->hasMany('Comment');
	}

	public function photos()
	{
		return $this->hasMany('Photo');
	}
}
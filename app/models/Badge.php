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

	public function render()
	{
			$image = "./img/badges/".$this->image;
			print '<img id="badge_image" src="'.$image.'" alt="badge" />';
            print $this->description;
            print'<br>';
            echo $this->name;
	}
}
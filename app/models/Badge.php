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
			print '<h3 id="badge_name">'.$this->name.'</h3>';
            print '<p>'.$this->description.'</p>';
            
	}

	 public function scopeWhereNotRelatedToUser($query, $id)
    {
        $query->whereNotIn('id', function ($query) use ($id)
        {
            $query->select('badge_id')
                ->from('user_badges')
                ->where('user_id', '=', $id);
        });
    }
}
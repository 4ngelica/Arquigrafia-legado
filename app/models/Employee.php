<?php

class Employee extends Eloquent {

	protected $fillable = ['user_id','institution_id'];

	protected $table = 'employees';

	//public $timestamps = false;

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function institution()
	{
		return $this->belongsTo('Institution');
	}

	public function role()
	{
		return $this->belongsTo('Role');
	}


}
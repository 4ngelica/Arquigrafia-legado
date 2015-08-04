<?php

class Institution extends Eloquent {

	protected $fillable = ['name','country'];

	public $timestamps = false;

	public function employees()
	{
		return $this->hasMany('Employee');
	}

	public function institutions()
	{
		return $this->hasMany('Institution');
	}

	public static function  institutionsList()
	{
		return DB::table('institutions')->orderBy('name', 'asc')->lists('name','id');
	}
}
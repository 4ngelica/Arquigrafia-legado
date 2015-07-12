<?php

class BaseModel extends Eloquent {
	
	public $errors;

	public function validate()
	{
		$validator = Validator::make($this->attributes, static::$rules);
		if ($validator->passes()) {
			return true;
		}
		$this->errors = $validator->messages();
		return false;
	}
}
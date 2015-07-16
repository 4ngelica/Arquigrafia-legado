<?php

use Watson\Validating\ValidatingTrait;
use Illuminate\Database\Eloquent\Collection as Collection;

class BaseModel extends Eloquent {
  
  use ValidatingTrait;

  public function equal($object) {
    if ( is_object($object) && get_class($this) == get_class($object) && $this->id == $object->id ) {
      return true;
    }
    return false;
  }

  public function scopeExcept($query, $baseObject) {
  	/* check if it is a model */
  	if (get_class($baseObject) == get_class($this) )
  	{
			return $query->where('id', '!=', $baseObject->id);
		}
		/* check if it is a laravel collection */
		else if ($baseObject instanceof Collection)
		{
			return $query->whereNotIn('id', $baseObject->modelKeys());
		}
		/* check if it is an array of id's */
		else if ( is_array($baseObject) )
		{
			return $query->whereNotIn('id', $baseObject);
		}
		/* ignore $baseObject, otherwise */
		return $query;		
  
  }

  /* only for those models with a relationship with User model */
  public function scopeWithUser($query, $user) {
		return $query->where('user_id', $user->id);
	}

}
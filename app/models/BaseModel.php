<?php

use Watson\Validating\ValidatingTrait;

class BaseModel extends Eloquent {
  
  use ValidatingTrait;

  public function equal($object) {
    if ( is_object($object) && get_class($this) == get_class($object) && $this->id == $object->id ) {
      return true;
    }
    return false;
  }

}
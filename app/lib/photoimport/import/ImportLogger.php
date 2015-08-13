<?php namespace lib\photoimport\import;

use lib\log\SimpleLogger;

class ImportLogger extends SimpleLogger {

  public function __construct() {
    $this->init('ImportLogger', 'imports');
    $this->pushHandler(date('Y-m-d'));
  }

  

}
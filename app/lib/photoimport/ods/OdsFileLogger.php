<?php namespace lib\photoimport\ods;

use lib\log\SimpleLogger;

class OdsFileLogger extends SimpleLogger {

  public function __construct($logger_name, $log_folder, $file_name) {
    $this->init($logger_name, $log_folder, false);
    $this->pushHandler($file_name);
  }

}
<?php namespace lib\photoimport\ods;

class OdsFile extends \SplFileInfo {

  protected $basepath;

  protected $file_name;

  protected $sheet;

  protected $logger;

  public function __construct(OdsFileLogger $logger) {
    $this->logger = $logger;
  }

  public function logPhotoException($exception, $photo) {

  }

}
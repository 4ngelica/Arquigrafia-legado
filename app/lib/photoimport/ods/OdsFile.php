<?php namespace lib\photoimport\ods;

class OdsFile {

  protected $file;

  protected $basepath;

  protected $file_name;

  protected $sheet;

  protected $logger;

  public function __construct(\SplFileInfo $file, OdsFileLogger $logger = null) {
    $this->file = $file;
    $this->basepath = $file->getPath();
    $this->file_name = $file->getFilename();
    $this->logger = $logger ?: new OdsFileLogger;
  }

  public function logPhotoException($exception, $photo) {

  }

  public function logTagException($exception, $tag) {

  }

}
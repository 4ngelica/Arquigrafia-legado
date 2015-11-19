<?php namespace lib\metadata;

class Exiv2Manager {

  public function saveMetadata($filename, $photo) {
    $e = new Exiv2($filename, $photo);
    $e->saveMetadata();
  }

}
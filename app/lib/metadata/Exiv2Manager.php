<?php namespace lib\metadata;

class Exiv2Manager {

  public function getInstance($originalFileExtension, $photo, $imagesPath) {
    return new Exiv2($originalFileExtension, $photo, $imagesPath);
  }

}
<?php namespace lib\photoimport\import;

use Photo;
use Tag;
use lib\photoimport\ods\SheetReader;

class Importer {

  protected $tag;
  protected $photo;
  protected $reader;
  protected $logger;

  public function __construct (Photo $photo, Tag $tag, SheetReader $sr, ImportLogger $logger) {
    $this->photo = $photo;
    $this->tag = $tag;
    $this->reader = $sr;
    $this->logger = $logger;
  } 

  public function importPhoto($basepath, $attributes) {
    return $this->photo->updateOrCreateByTombo($attributes, $basepath);
  }

  public function importTags($raw_tags) {
    $tags = $this->tag->getMany($raw_tags, $tag_count);
    if ( count($tags) != $tag_count ) {

    }

    return $tags;
  }

  public function import($basepath, $raw_photo, $raw_tags) {
    $photo = $this->importPhoto($basepath, $raw_photo);
    $tags = $this->importTags($raw_tags);
    $photo->syncTags($tags);
    return $photo;
  }

  public function importFromFile($file) {
    $photos = array();
    $basepath = $file->getPath();
    try {
      $raw_photos = $this->getContent($file);
    } catch (PHPExcel_Reader_Exception $e) {
      
      return null;
    }
    foreach ($raw_photos as $raw_photo) {
      $raw_tags = $raw_photo['tags'];
      unset( $raw_photo['tags'] );
      $new_photo = $this->import($basepath, $raw_photo, $raw_tags);
      array_push($photos, $new_photo);
    }

    return $photos;
  }

  public function getContent($file) {
    $this->reader->read($file);
  }

}
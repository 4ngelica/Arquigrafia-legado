<?php namespace lib\photoimport;

use Photo;
use Tag;

class Importer {

  protected $tag;
  protected $photo;
  protected $reader;

  public function __construct (Photo $photo, Tag $tag, SheetReader $sr) {
    $this->photo = $photo;
    $this->tag = $tag;
    $this->reader = $sr;
  } 

  public function importPhoto($basepath, $attributes) {
    try {
      $new_photo = $this->photo->createOrFail($attributes, $basepath);
    } catch (Exception $e) {
      return null;
    }
    return $new_photo;
  }

  public function importTags($raw_tags) {
    $tags = array();
    if ( is_array($raw_tags) ) {
      foreach ($raw_tags as $type => $rt) {
        $tags = array_merge( $tags, $this->tag->getMany($rt, $type) );
      }
    } else {
      array_push( $tags, $this->tag->getMany($raw_tags) );
    }
    return $tags;
  }

  public function import($basepath, $raw_photo, $raw_tags) {
    $photo = $this->importPhoto($basepath, $raw_photo);
    $tags = $this->importTags($raw_tags);
  }

  public function importFromFile($file) {
    $photos = array();
    try {
      $basepath = $file->getPath();
      $raw_photos = $this->getContent($file);
    } catch (Exception $e) {
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
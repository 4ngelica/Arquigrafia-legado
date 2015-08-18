<?php

class Tag extends Eloquent {

  public $timestamps = false;

  protected $fillable = ['name'];

  public function photos() {
    return $this->belongsToMany('Photo', 'tag_assignments', 'tag_id', 'photo_id');
  }

  public function photosTags($tags){
    $query = Tag::whereIn('name', $tags);
    $tagsResult = $query->get();
    $listTags= $tagsResult->lists('id');
    $photosTagAssignment = DB::table('tag_assignments')
      ->select('photo_id')
      ->whereIn('tag_id',$listTags)
      ->get();

    $listPhotos=$photosTagAssignment->lists('photo_id');
    $photos = Photo::wherein('id',$listPhotos);

    return $photos;
  }

  public static function getMany($raw_tags, &$tag_count, $tag_type = null) {
    $instance = new static;
    
    $tags = $instance->transform($raw_tags);
    $tag_count = count($tags);
    $found_tags = array();
    foreach ($tags as $tag_name) {
      if ( !empty($tag_name) ) { 
        try {
          $tag = $instance->getOrCreate($tag_name, $tag_type);
          array_push( $found_tags,  $tag);
        } catch (Exception $e) { }
      }
    }
    return $found_tags;
  }

  public static function getOrCreate($name, $type) {
    $tag = $this->firstOrNew(['name' => $name]);
    $tag->type = $type;
    $tag->incrementReferences();
    $this->save();    
  }

  public static function transform($tags) {
    if ( is_array($tags) ) {
      return $tags;
    }
    $tags = explode(',', $tags);
    $tags = array_map('trim', $tags);
    $tags = array_map('mb_strtolower', $tags);
    $tags = array_filter($tags);
    return array_unique($tags);
  }

  public function incrementReferences() {
    if ($this->count == null) {
      $this->count = 0;
    }
    $this->count++;
  }
}
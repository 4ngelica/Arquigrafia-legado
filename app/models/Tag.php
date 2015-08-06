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

  public function getMany($raw_tags, $tag_type = null) {
    if ( empty($raw_tags) ) {
      return null;
    }
    $tags = $this->split($raw_tags);
    $all_tags = array();
    foreach ($tags as $tag_name) {
      if ( empty($tag) ) {
        continue;
      }
      try {
        $tag = $this->getOrCreate($tag_name, $tag_type);
        array_push($all_tags, $tag);
      } catch (PDOException $e) {
        Log::error("Logging exception, error to register tags");
        return null;
      }
    }
    return $all_tags;
  }

  public function getOrCreate($name, $type) {
    $tag = $this->firstOrCreate(['name' => $name]);
    $tag->type = $type;
    $tag->incrementReferences();
    $this->save();    
  }

  public function split($tags) {
    $tags = explode(',', $tags);
    $tags = array_map('trim', $tags);
    $tags = array_map('mb_strtolower', $tags);
    return array_unique($tags);

  }

  public function incrementReferences() {
    if ($this->count == null) {
      $this->count = 0;
    }
    $this->count++;
  }
}
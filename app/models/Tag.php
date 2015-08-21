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

  public static function getOrCreate($name, $type = null) {
    $tag = static::firstOrNew(['name' => $name]);
    $tag->type = $type;
    $tag->incrementReferences();
    $tag->save();
    return $tag;
  }

  public static function transform($raw_tags) {
    $tags = explode(',', $raw_tags);
    $tags = array_map('trim', $tags);
    $tags = array_map('mb_strtolower', $tags);
    $tags = array_filter($tags);
    return array_unique($tags);
  }

  public function incrementReferences() {
    $this->count = $this->count == null ? 1 : $this->count + 1;
  }
}
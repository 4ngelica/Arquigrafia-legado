<?php

class Author extends Eloquent {

  public $timestamps = false;

  protected $fillable = ['name'];

  public function photos() {
    return $this->belongsToMany('Photo', 'photo_author', 'author_id', 'photo_id');
  }

  public static function getOrCreate($name) {
   // $author = static::firstOrNew(['name' => $name]);
    $author = Author::where('name', $name)->first();
    //dd($author);
    if(is_null($author)){
      $author = new Author();
      $author->name = $name;
      $author->approved = 0;
      $author->save();
    }
    return $author;
  }

  public static function formatAuthors($raw_author) {
    $authors = explode(';', $raw_author);
    $authors = array_map('trim', $authors);
    $authors = array_map('mb_strtolower', $authors);
    $authors = array_filter($authors);
    return array_unique($authors);
  }

  public function saveAuthors($authors_list,$photo)
  {     
        $arrayAuthors = $this->formatAuthors($authors_list);
        //dd($arrayAuthors);
        
        foreach ($arrayAuthors as $name_author) {
            $author = $this->getOrCreate($name_author);
            //echo $author->id;
            //echo "<br>";
            //echo $author->name;
            $photo->authors()->attach($author->id);          
            $author->save();
        } 
        
  } 

  public function getAuthorPhoto($photo_id){     
    $allAuthors = DB::table('photo_author')
      ->select('author_id')
      ->where('photo_id',$photo_id)
      ->lists('author_id');
       //dd($allAuthors);
     $authorsList = Author::wherein('id',$allAuthors)->get(); 
     
     return $authorsList;
  }

  public function deleteAuthorPhoto($photo){
      $allAuthors = $this->getAuthorPhoto($photo->id); 
      if(!empty($allAuthors)){
          foreach ($allAuthors as $allAuthor) {            
            $photo->authors()->detach($allAuthor->id);
          }
      }
      
  }
  public function updateAuthors($authors_list,$photo)
  {   //dd($authors_list);
      $this->deleteAuthorPhoto($photo);
      //dd($authors_list);
      //die();
      $this->saveAuthors($authors_list,$photo);      
  }

  
}
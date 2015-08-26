<?php
//add
use lib\utils\ActionUser;
use Carbon\Carbon;
use lib\date\Date;

class PhotosController extends \BaseController {

  protected $date;

  public function __construct(Date $date = null)
  {
    $this->beforeFilter('auth',
      array( 'except' => ['index','show'] ));
    $this->date = $date ?: new Date; 
  }

  public function index()
  {
    $photos = Photo::all();
    return View::make('/photos/index',['photos' => $photos]);
  }

  public function show($id)
  { 
    $photos = Photo::find($id);
    if ( !isset($photos) ) {
      return Redirect::to('/');
    }
    $user = Auth::user();
    $photo_owner = $photos->user;
    $tags = $photos->tags;
    $binomials = Binomial::all()->keyBy('id');
    $average = Evaluation::average($photos->id);
    $evaluations = null;
    $photoliked = null;
    $follow = true;
    $belongInstitution = false;
    $hasInstitution = false; 
    if (Auth::check()) {
      if(Session::has('institutionId')){
        $belongInstitution = Institution::belongInstitution($photos->id,Session::get('institutionId'));
        $hasInstitution = Institution::belongSomeInstitution($photos->id);
      } else{
        $hasInstitution = Institution::belongSomeInstitution($photos->id);
      }
      
      $photoliked = Like::fromUser($user)->withLikable($photos)->first();
      $evaluations =  Evaluation::where("user_id", $user->id)->where("photo_id", $id)->orderBy("binomial_id", "asc")->get();
      if ($user->following->contains($photo_owner->id)) {
        $follow = false;
      }
      $user_id = $user->id;
      $user_or_visitor = "user";
    } else {
        $user_or_visitor = "visitor";
        session_start();
        $user_id = session_id();
    }
    $source_page = Request::header('referer');
    ActionUser::printSelectPhoto($user_id, $id, $source_page, $user_or_visitor);

    $license = Photo::licensePhoto($photos);

    return View::make('/photos/show',
      ['photos' => $photos, 'owner' => $photo_owner, 'follow' => $follow, 'tags' => $tags,
      'commentsCount' => $photos->comments->count(),
      'commentsMessage' => static::createCommentsMessage($photos->comments->count()),
      'average' => $average, 'userEvaluations' => $evaluations, 'binomials' => $binomials,
      'architectureName' => Photo::composeArchitectureName($photos->name),
      'similarPhotos'=>Photo::photosWithSimilarEvaluation($average,$photos->id),
      'photoliked' => $photoliked,
      'license' => $license,
      'belongInstitution' => $belongInstitution,
      'hasInstitution' => $hasInstitution
    ]);
  }

  // upload form
  public function form()
  {

    $pageSource = Request::header('referer');
    if(empty($pageSource)) $pageSource = '';
    $tags = null;
    if ( Session::has('tags') )
    {
      $tags = Session::pull('tags');
      $tags = explode(',', $tags);
    }    
    return View::make('/photos/form')->with(['tags'=>$tags,'pageSource'=>$pageSource, 'user'=>Auth::user()]);

  }





  public function newForm()
  {  
    $user_id = Auth::user()->id;
    $albumsInstitutional = NULL;

    if(Session::has('institutionId')){
      $institution = Institution::find(Session::get('institutionId'));
      $this->album = new Album();
      $albumsInstitutional = $this->album->showAlbumsInstitutional($institution);
    }

    $pageSource = Request::header('referer');
    
    $tagsArea = null;
    $workAuthorInput = null;

    $tagsMaterialArea = null;
    $tagsElementsArea = null;
    $tagsTypologyArea = null;

    if ( Session::has('tagsArea') )
    {  
      $tagsArea = Session::pull('tagsArea');
      $tagsArea = explode(',', $tagsArea); 
    }
    if ( Session::has('workAuthorInput') )
    {  
      $workAuthorInput = Session::pull('workAuthorInput');
      
    }
     if ( Session::has('tagsMaterialArea') )
    {  
      $tagsMaterialArea = Session::pull('tagsMaterialArea');
      $tagsMaterialArea = explode(',', $tagsMaterialArea); 
    }
    if ( Session::has('tagsElementsArea') )
    {  
      $tagsElementsArea = Session::pull('tagsElementsArea');
      $tagsElementsArea = explode(',', $tagsElementsArea); 
    }
    if ( Session::has('tagsTypologyArea') )
    {  
      $tagsTypologyArea = Session::pull('tagsTypologyArea');
      $tagsTypologyArea = explode(',', $tagsTypologyArea); 
    }

    $input['autoOpenModal'] = null;  
    /* */
    return View::make('/photos/newform')->with(['tagsArea'=> $tagsArea,
       'workAuthorInput' => $workAuthorInput,
       'tagsMaterialArea' => $tagsMaterialArea ,
      'tagsElementsArea' => $tagsElementsArea,
      'tagsTypologyArea' => $tagsTypologyArea,
      'pageSource'=>$pageSource, 'user'=>Auth::user(), 
      'institution'=>$institution,
      'albumsInstitutional'=>$albumsInstitutional,
      'autoOpenModal'=>$input['autoOpenModal'] 
      ]);
  }

  public static function formatTags($tagsType){
    $tagsType = array_map('trim', $tagsType);
    $tagsType = array_map('mb_strtolower', $tagsType); 
    $tagsType = array_unique($tagsType);    
    return $tagsType;
  }

  public static function SaveTags($tags,$photo,$typeTags){
    
    try{
          foreach ($tags as $t) {
              $tag = Tag::where('name', $t)->first();
              if(is_null($tag)){
                $tag = new Tag();
                $tag->name = $t;
                $tag->save();
              }

              $photo->tags()->attach($tag->id);
              if($typeTags == 'material'){
                $tag->type = 'Material';
              }elseif ($typeTags == 'elements') {
                $tag->type = 'Elements';
              }elseif($typeTags == 'Typology'){
                $tag->type = 'Typology';
              }else{
                $tag->type = 'General';
              }

              if($tag->count == null)
                  $tag->count = 0;
              $tag->count++;
              $tag->save();    
          }
          $saved = true;

          }catch(PDOException $e){
            Log::error("Logging exception, error to register tags");           
            $saved = false;
          }
      return $saved;  
  }


  public function saveFormInstitutional() {   
    Input::flashExcept('tagsArea','tagsTypologyArea','tagsElementsArea','tagsMaterialArea', 'photo','workAuthor'); //tagsTypology tagsElements tagsMaterial
   // Input::flashExcept('tagsArea', 'photo','workAuthor');

    $input = Input::all();
     
    if (Input::has('tagsArea') && Input::has('tagsTypologyArea') && Input::has('tagsElementsArea') && Input::has('tagsMaterialArea') ){
      $input["tagsArea"] = str_replace(array('\'', '"', '[', ']'), '', $input["tagsArea"]);    
      $input["tagsMaterialArea"] = str_replace(array('\'', '"', '[', ']'), '', $input["tagsMaterialArea"]);
      $input["tagsElementsArea"] = str_replace(array('\'', '"', '[', ']'), '', $input["tagsElementsArea"]);
      $input["tagsTypologyArea"] = str_replace(array('\'', '"', '[', ']'), '', $input["tagsTypologyArea"]); 
    
    }else{
      $input["tagsArea"] = '';
      $input["tagsMaterialArea"] = '';
      $input["tagsElementsArea"] = '';
      $input["tagsTypologyArea"] = ''; 
    } 

    /*if (Input::has('tagsArea')){
      $input["tagsArea"] = str_replace(array('\'', '"', '[', ']'), '', $input["tagsArea"]); 
    }else{
      $input["tagsArea"] = '';
    } */  

    if (Input::has('workAuthor')){
      //dd($input["workAuthor"] );
      $input["workAuthor"] = str_replace(array('\'', '"'), '', $input["workAuthor"]);       
    }    

    if(Session::has('institutionId')){     
      $rules = array(
      'support' => 'required',
      'tombo' => 'required',
      'subject' => 'required',      
      'hygieneDate' => 'date_format:"d/m/Y"',
      'backupDate' => 'date_format:"d/m/Y"',
      'characterization' => 'required',
      
      'photo' => 'max:10240|required|mimes:jpeg,jpg,png,gif',
      'name' => 'required',
      'tagsArea' => 'required',
      'tagsMaterialArea' => 'required',
      'tagsElementsArea' => 'required',
      'tagsTypologyArea' => 'required', 
      'country' => 'required',
      'imageAuthor' => 'required'     
      
      //'photo_workDate' => 'date_format:"d/m/Y"',
      //'photo_imageDate' => 'date_format:"d/m/Y"'
      );


    }else{
      $rules = array(
      'photo' => 'max:10240|required|mimes:jpeg,jpg,png,gif',
      'name' => 'required',
      'tagsArea' => 'required',
      'tagsMaterialArea' => 'required',
      'tagsElementsArea' => 'required',
      'tagsTypologyArea' => 'required',
      'country' => 'required',
      'imageAuthor' => 'required',
      'authorization_checkbox' => 'required'
        );
    }

  $validator = Validator::make($input, $rules);

  if ($validator->fails()) { 
      $messages = $validator->messages();
      
      return Redirect::to('/photos/newUpload')->with(['tagsArea' => $input['tagsArea'], 
        'tagsMaterialArea' => $input['tagsMaterialArea'],'tagsElementsArea' => $input['tagsElementsArea'],
        'tagsTypologyArea' => $input['tagsTypologyArea'],
        'workAuthorInput'=>$input["workAuthor"]
        ])->withErrors($messages); 
      /*return Redirect::to('/photos/newUpload')->with(['tagsArea' => $input['tagsArea'] ,
        'workAuthorInput'=>$input["workAuthor"]        
        ])->withErrors($messages); */

    }else{ 
      
      if(Input::hasFile('photo') and Input::file('photo')->isValid()) {
        $file = Input::file('photo');
          $photo = new Photo();
          $photo->nome_arquivo = $file->getClientOriginalName();

          if(Session::has('institutionId')){
            $photo->support = $input["support"];
            $photo->tombo = $input["tombo"];
            $photo->subject = $input["subject"];
            if ( !empty($input["hygieneDate"]) )
              $photo->hygieneDate = $this->date->formatDate($input["hygieneDate"]);
            if ( !empty($input["backupDate"]) )
              $photo->backupDate = $this->date->formatDate($input["backupDate"]);
            $photo->characterization = $input["characterization"];
            $photo->cataloguingTime = date('Y-m-d H:i:s');
            $photo->UserResponsible = $input["userResponsible"];
          }
          $photo->name = $input["name"];
          if ( !empty($input["description"]) )
               $photo->description = $input["description"];
          if ( !empty($input["workAuthor"]) )
          $photo->workAuthor = $input["workAuthor"];
          if ( !empty($input["workDate"]) )
            $photo->workdate = $input["workDate"];

          $photo->country = $input["country"];
          if ( !empty($input["state"]) )
            $photo->state = $input["state"];
          if ( !empty($input["city"]) )
              $photo->city = $input["city"];
          if ( !empty($input["street"]) )
               $photo->street = $input["street"];
          if ( !empty($input["imageAuthor"]) )
              $photo->imageAuthor = $input["imageAuthor"];
          if ( !empty($input["imageDate"]) )
              $photo->dataCriacao = $input["imageDate"];
          if ( !empty($input["observation"]) )  
              $photo->observation = $input["observation"];

          if ( !empty($input["aditionalImageComments"]) )
              $photo->aditionalImageComments = $input["aditionalImageComments"];
          $photo->allowCommercialUses = $input["allowCommercialUses"];
          $photo->allowModifications = $input["allowModifications"];

          $photo->user_id = Auth::user()->id;
          $photo->dataUpload = date('Y-m-d H:i:s');
          $photo->institution_id = Session::get('institutionId');
          $photo->save();
          
          $ext = $file->getClientOriginalExtension();
          $photo->nome_arquivo = $photo->id.".".$ext;

          $photo->save();
          
          $tagsCopy = $input['tagsArea'];
          $tagsCopyMaterial = $input['tagsMaterialArea'];
          $tagsCopyElements = $input['tagsElementsArea'];
          $tagsCopyTypology = $input['tagsTypologyArea'];

          $tags = explode(',', $input['tagsArea']);
          $tagsMaterial = explode(',', $input['tagsMaterialArea']);
          $tagsElements = explode(',', $input['tagsElementsArea']);
          $tagsTypology = explode(',', $input['tagsTypologyArea']);
      
          if (!empty($tags) && !empty($tagsMaterial)  && !empty($tagsElements) && 
            !empty($tagsTypology) ) { 
          /*if (!empty($tags)) { */
              $tags = static::formatTags($tags);
              $tagsMaterial = static::formatTags($tagsMaterial);
              $tagsElements = static::formatTags($tagsElements);
              $tagsTypology = static::formatTags($tagsTypology);

              $tagsSaved = static::SaveTags($tags,$photo,'general');

              $tagsMaterialSaved = static::SaveTags($tagsMaterial,$photo,'material');
              $tagsElementsSaved = static::SaveTags($tagsElements,$photo,'elements');
              $tagsTypologySaved = static::SaveTags($tagsTypology,$photo,'typology'); 

              if(!$tagsSaved || !$tagsSaved || !$tagsElementsSaved || !$tagsTypologySaved){    
              /*if(!$tagsSaved){ */
                  $photo->forceDelete();
                  $messages = array('tagsArea'=>array('Inserir pelo menos uma tag'),'tagsMaterialArea'=>array('Inserir pelo menos uma tag material'),
                    'tagsElementsArea'=>array('Inserir pelo menos uma tag de elementos'),'tagsTypologyArea'=>array('Inserir pelo menos uma tag tipologia')
                    );
                  //$messages = array('tagsArea'=>array('Inserir pelo menos uma tag') );

                  return Redirect::to('/photos/newUpload')->with(['tagsArea' => $input['tagsArea'], 
                 'tagsMaterialArea' => $input['tagsMaterialArea'],'tagsElementsArea' => $input['tagsElementsArea'],
                 'tagsTypologyArea' => $input['tagsTypologyArea']])->withErrors($messages);

                  //return Redirect::to('/photos/newUpload')->with(['tagsArea' => $input['tagsArea']])->withErrors($messages);
              }

            }

           //add Album
           /* if (Input::has("albums_institution")) {              
                $album = new Album();
                $album->id = $input["albums_institution"];
                $album->attachPhotos($photo->id);               
            }*/
           
          $input['autoOpenModal'] = 'true';  

          $sourcePage = $input["pageSource"]; //get url of the source page through form
          ActionUser::printUploadOrDownloadLog($photo->user_id, $photo->id, $sourcePage, "Upload", "user");
          ActionUser::printTags($photo->user_id, $photo->id, $tagsCopy, $sourcePage, "user", "Inseriu");
          
          $image = Image::make(Input::file('photo'))->encode('jpg', 80); // todas começam com jpg quality 80

          $image->widen(600)->save(public_path().'/arquigrafia-images/'.$photo->id.'_view.jpg');
          $image->heighten(220)->save(public_path().'/arquigrafia-images/'.$photo->id.'_200h.jpg'); // deveria ser 220h, mantem por já haver alguns arquivos assim.
          $image->fit(186, 124)->encode('jpg', 70)->save(public_path().'/arquigrafia-images/'.$photo->id.'_home.jpg');
          $file->move(public_path().'/arquigrafia-images', $photo->id."_original.".strtolower($ext)); // original

          $photo->saveMetadata(strtolower($ext));
          
          $input['photoId'] = $photo->id; //dd($input);
          //return Redirect::to("/photos/{$photo->id}");
          return Redirect::back()->withInput($input);
        

      }else{
         $messages = $validator->messages();
          return Redirect::to('/photos/newUpload')->withErrors($messages);
      }
  
    }

  }
  /**/
  public static function filterTagByType($photo,$tagType){
      $tagsArea = $photo->tags->toJson();
      $jsonTagsArea=json_decode($tagsArea);      
      $arrayTags = array_filter($jsonTagsArea,function($item) use ($tagType){
        return $item->type == $tagType;
      });
      $tagsTypeList = array(); 
      foreach ($arrayTags as $value) {
        array_push($tagsTypeList, $value->name);
      }
      return $tagsTypeList;
  } 

  public static function updateTags($newTags,$photo,$typeTags){
  
  $photo_tags = $photo->tags;
  $allTags = Tag::allTagsPhotoByType($photo->id,$typeTags); 
  //dd($allTags);
  foreach ($allTags as $tag){
  //foreach($photo_tags as $tag){
    $tag->count--;
    $tag->save();                
  }

  foreach ($allTags as $alltag) {
    $photo->tags()->detach($alltag->id);
  }

  try{
      foreach ($newTags as $t) {
              $tag = Tag::where('name', $t)
                    ->where('type', $typeTags)
              ->first();

              if(is_null($tag)){
                $tag = new Tag();
                $tag->name = $t;
                $tag->type = $typeTags;
                $tag->save();
              }

              $photo->tags()->attach($tag->id);

              if($tag->count == null)
                  $tag->count = 0;
              $tag->count++;
              $tag->save(); 

          }

          $saved = true;

          }catch(PDOException $e){
            Log::error("Logging exception, error to register tags");           
            $saved = false;
          }
      return $saved;  
  }

  public static function updateTags2($tags,$photo,$typeTags){
    $tags_id = [];
    $photo_tags = $photo->tags;
    //dd($photo_tags);
   // print_r($photo_tags);
    try{
        //if($typeTags == 'typology'){
               // echo "tb";
              // dd($tags);
       // } 
          foreach ($tags as $t) {
              $tag = Tag::where('name', $t)->first();
                          //->where('type',$typeTags)->first();
              
             // if($typeTags == 'typology'){
               // echo "ok";
                //dd($photo_tags->contains($tag));
                //dd($tag);
             // } 

              if(is_null($tag)){
                  $tag = new Tag();
                  $tag->name = $t;
                  $tag->type = $typeTags;                  
                  $tag->save();
              }

             /* */
             //if($typeTags == 'typology') dd($tag); // dd($photo_tags->contains($tag));
              if(!$photo_tags->contains($tag)){
                  if ($tag->count == null) $tag->count = 0;                   
                  $tag->count++;
                  //if($typeTags == 'typology') dd($tag->id);
                  $photo->tags()->attach($tag->id);
                  $tag->save();            
              }
              array_push($tags_id, $tag->id);  
             // if($typeTags == 'typology') dd($tags_id);              
          }

          foreach($photo_tags as $tag){
             /*if($typeTags == 'general')  { echo "geral<br>"; print_r($tag->id); print_r($tags_id); echo "<br>";}
             if($typeTags == 'material') { echo "mater<br>"; print_r($tag->id); print_r($tags_id); echo "<br>";}
             if($typeTags == 'elements') { echo "elem<br>"; print_r($tag->id); print_r($tags_id); echo "<br>";} 
             if($typeTags == 'typology') { echo "tipo<br>"; print_r($tag->id); print_r($tags_id);}*/

             if (!in_array($tag->id, $tags_id)){
                  $tag->count--;
                  $photo->tags()->detach($tag->id);
                  $tag->save(); 
              }  
            }
          //die();
          $saved = true;

          }catch(PDOException $e){
            Log::error("Logging exception, error to register tags");           
            $saved = false;
          }
      return $saved;  
  }

  /* Edição do formulario institutional*/
  public function editFormInstitutional($id) {
    $photo = Photo::find($id);
    $logged_user = Auth::User();
    //dd($logged_user->id == $photo->user_id);
    if ($logged_user == null) {
      return Redirect::action('PagesController@home');
    }elseif (Session::get('institutionId') == $photo->institution_id) { 
      $institution = null;
    if(Session::has('institutionId')){
      $institution = Institution::find(Session::get('institutionId'));
      //$this->album = new Album();
      //$albumsInstitutional = $this->album->showAlbumsInstitutional($institution);
    }

    if (Session::has('tagsArea'))
    {
      $tagsArea = Session::pull('tagsArea');
      $tagsArea = explode(',', $tagsArea);
    } else {
      $tagsArea = $photo->tags->lists('name');
      $tagsArea = static::filterTagByType($photo,"General");
      
    }

    if ( Session::has('tagsMaterialArea') )
    {  
      $tagsMaterialArea = Session::pull('tagsMaterialArea');
      $tagsMaterialArea = explode(',', $tagsMaterialArea); 
    }else {
      $tagsMaterialArea = static::filterTagByType($photo,"Material");
    }

    if ( Session::has('tagsElementsArea') )
    {  
      $tagsElementsArea = Session::pull('tagsElementsArea');
      $tagsElementsArea = explode(',', $tagsElementsArea); 
    }else {
      //$tagsElementsArea = $photo->tags->lists('name');
      $tagsElementsArea = static::filterTagByType($photo,"Elements");
    }

    if ( Session::has('tagsTypologyArea') )
    {  
      $tagsTypologyArea = Session::pull('tagsTypologyArea');
      $tagsTypologyArea = explode(',', $tagsTypologyArea); 
    }else {
      $tagsTypologyArea = static::filterTagByType($photo,"Typology");
    }

    if ( Session::has('workAuthorInput') )
    {  
      $workAuthorInput = Session::pull('workAuthorInput');      
    }else{
      $workAuthorInput = "";
    }
  
    return View::make('photos.edit-institutional')
      ->with(['photo' => $photo, 'tagsArea' => $tagsArea,
          'tagsMaterialArea' => $tagsMaterialArea,
          'tagsElementsArea' => $tagsElementsArea,
          'tagsTypologyArea' => $tagsTypologyArea,
          'institution'=>$institution,
          'workAuthorInput' => $workAuthorInput,
          'user'=>$logged_user
        ] );

    }
    
    return Redirect::action('PagesController@home');  
  }

  public function updateInstitutional($id){ 
     $photo = Photo::find($id); 
     Input::flashExcept('tagsArea','tagsTypologyArea','tagsElementsArea','tagsMaterialArea', 'photo','workAuthor'); 
     $input = Input::all(); 
     if (Input::has('tagsArea') && Input::has('tagsTypologyArea') && Input::has('tagsElementsArea') && Input::has('tagsMaterialArea') ){
      $input["tagsArea"] = str_replace(array('\'', '"', '[', ']'), '', $input["tagsArea"]);    
      $input["tagsMaterialArea"] = str_replace(array('\'', '"', '[', ']'), '', $input["tagsMaterialArea"]);
      $input["tagsElementsArea"] = str_replace(array('\'', '"', '[', ']'), '', $input["tagsElementsArea"]);
      $input["tagsTypologyArea"] = str_replace(array('\'', '"', '[', ']'), '', $input["tagsTypologyArea"]); 
    
    }else{
      $input["tagsArea"] = '';
      $input["tagsMaterialArea"] = '';
      $input["tagsElementsArea"] = '';
      $input["tagsTypologyArea"] = ''; 
    } 
    if (Input::has('workAuthor')){ 
      $input["workAuthor"] = str_replace(array('\'', '"'), '', $input["workAuthor"]);       
    }else{
      $input["workAuthor"] ='';
    } 

       $rules = array(
      'support' => 'required',
      'tombo' => 'required',
      'subject' => 'required',      
      'hygieneDate' => 'date_format:"d/m/Y"',
      'backupDate' => 'date_format:"d/m/Y"',
      'characterization' => 'required',
      
      'name' => 'required',
      'tagsArea' => 'required',
      'tagsMaterialArea' => 'required',
      'tagsElementsArea' => 'required',
      'tagsTypologyArea' => 'required', 
      'country' => 'required',
      'imageAuthor' => 'required'           
      //'photo_workDate' => 'date_format:"d/m/Y"',
      //'photo_imageDate' => 'date_format:"d/m/Y"'
      );
       $validator = Validator::make($input, $rules);

       if ($validator->fails()) { 
          $messages = $validator->messages();          
          return Redirect::to('/photos/'.$photo->id.'/editInstitutional')->with(['tagsArea' => $input['tagsArea'], 
        'tagsMaterialArea' => $input['tagsMaterialArea'],'tagsElementsArea' => $input['tagsElementsArea'],
        'tagsTypologyArea' => $input['tagsTypologyArea'],
        'workAuthorInput'=>$input["workAuthor"]
        ])->withErrors($messages); 
    }else{ 
        if ( !empty($input["aditionalImageComments"]) )
            $photo->aditionalImageComments = $input["aditionalImageComments"];
        $photo->support = $input["support"];
        $photo->tombo = $input["tombo"];
        $photo->subject = $input["subject"];
        if ( !empty($input["hygieneDate"]) )
              $photo->hygieneDate = $this->date->formatDate($input["hygieneDate"]);
        if ( !empty($input["backupDate"]) )
              $photo->backupDate = $this->date->formatDate($input["backupDate"]);
        $photo->characterization = $input["characterization"];
        $photo->cataloguingTime = date('Y-m-d H:i:s');
        $photo->UserResponsible = $input["userResponsible"];
        $photo->name = $input["name"];
          if ( !empty($input["description"]) )
               $photo->description = $input["description"];
          if ( !empty($input["workAuthor"]) )
               $photo->workAuthor = $input["workAuthor"];
          if ( !empty($input["workDate"]) )
               $photo->workdate = $input["workDate"];

        $photo->country = $input["country"];
          if ( !empty($input["state"]) )
               $photo->state = $input["state"];
          if ( !empty($input["city"]) )
               $photo->city = $input["city"];
          if ( !empty($input["street"]) )
               $photo->street = $input["street"];
          if ( !empty($input["imageAuthor"]) )
               $photo->imageAuthor = $input["imageAuthor"];
          if ( !empty($input["imageDate"]) )
               $photo->dataCriacao = $input["imageDate"];
          if ( !empty($input["observation"]) )  
               $photo->observation = $input["observation"];
          $photo->allowCommercialUses = $input["allowCommercialUses"];
          $photo->allowModifications = $input["allowModifications"];

          $photo->user_id = Auth::user()->id;
          $photo->dataUpload = date('Y-m-d H:i:s');
          $photo->institution_id = Session::get('institutionId');

          if (Input::hasFile('photo') and Input::file('photo')->isValid()) {
              $file = Input::file('photo');
              $ext = $file->getClientOriginalExtension();
              $photo->nome_arquivo = $photo->id.".".$ext;
          }
          $photo->touch();
          $photo->save();
          //tags
          $tagsCopy = $input['tagsArea'];
          $tagsCopyMaterial = $input['tagsMaterialArea'];
          $tagsCopyElements = $input['tagsElementsArea'];
          $tagsCopyTypology = $input['tagsTypologyArea'];

          $tags = explode(',', $input['tagsArea']);
          $tagsMaterial = explode(',', $input['tagsMaterialArea']);
          $tagsElements = explode(',', $input['tagsElementsArea']);
          $tagsTypology = explode(',', $input['tagsTypologyArea']);


          if (!empty($tags) && !empty($tagsMaterial)  && !empty($tagsElements) && 
            !empty($tagsTypology) ) { 
              $tags = static::formatTags($tags);
              $tagsMaterial = static::formatTags($tagsMaterial);
              $tagsElements = static::formatTags($tagsElements);
              $tagsTypology = static::formatTags($tagsTypology);
              
              $tagsSaved = static::updateTags($tags,$photo,'General');
              $tagsMaterialSaved = static::updateTags($tagsMaterial,$photo,'Material');
              $tagsElementsSaved = static::updateTags($tagsElements,$photo,'Elements');
              $tagsTypologySaved = static::updateTags($tagsTypology,$photo,'Typology');

              if(!$tagsSaved || !$tagsSaved || !$tagsElementsSaved || !$tagsTypologySaved){             
                  $photo->forceDelete();
                  $messages = array('tagsArea'=>array('Inserir pelo menos uma tag'),'tagsMaterialArea'=>array('Inserir pelo menos uma tag material'),
                  'tagsElementsArea'=>array('Inserir pelo menos uma tag de elementos'),'tagsTypologyArea'=>array('Inserir pelo menos uma tag tipologia')
                  );
                
                  return Redirect::to('/photos/'.$photo->id.'/editInstitutional')->with(['tagsArea' => $input['tagsArea'], 
                  'tagsMaterialArea' => $input['tagsMaterialArea'],'tagsElementsArea' => $input['tagsElementsArea'],
                  'tagsTypologyArea' => $input['tagsTypologyArea']])->withErrors($messages);
            }
          }
          
          
          if (Input::hasFile('photo') and Input::file('photo')->isValid()) {
              $image = Image::make(Input::file('photo'))->encode('jpg', 80); // todas começam com jpg quality 80
              $image->widen(600)->save(public_path().'/arquigrafia-images/'.$photo->id.'_view.jpg');
              $image->heighten(220)->save(public_path().'/arquigrafia-images/'.$photo->id.'_200h.jpg'); // deveria ser 220h, mantem por já haver alguns arquivos assim.
              $image->fit(186, 124)->encode('jpg', 70)->save(public_path().'/arquigrafia-images/'.$photo->id.'_home.jpg');
              $file->move(public_path().'/arquigrafia-images', $photo->id."_original.".strtolower($ext)); // original
              $photo->saveMetadata(strtolower($ext));
          }
         // $source_page = Request::header('referer');
         // ActionUser::printTags($photo->user_id, $id, $tags_copy, $source_page, "user", "Editou");
          return Redirect::to("/photos/".$photo->id)->with('message', '<strong>Edição de informações da imagem</strong><br>Dados alterados com sucesso');
    }
  }  

  public function store() {


  Input::flashExcept('tags', 'photo');

  $input = Input::all();

  if (Input::has('tags'))
    $input["tags"] = str_replace(array('\'', '"', '[', ']'), '', $input["tags"]);
  else
    $input["tags"] = '';

  //validate for tamnho maximo e tipo de extensao
    $rules = array(
      'photo_name' => 'required',
      'photo_imageAuthor' => 'required',
      'tags' => 'required',
      'photo_country' => 'required',  
      'photo_authorization_checkbox' => 'required',
      'photo' => 'max:10240|required|mimes:jpeg,jpg,png,gif',
      'photo_workDate' => 'date_format:"d/m/Y"',
      'photo_imageDate' => 'date_format:"d/m/Y"'
    );

  $validator = Validator::make($input, $rules);

  if ($validator->fails()) {
      $messages = $validator->messages();
      //dd($messages);

    return Redirect::to('/photos/upload')->with(['tags' => $input['tags']])->withErrors($messages);
    } else {

    if (Input::hasFile('photo') and Input::file('photo')->isValid()) {
      $file = Input::file('photo');
      $photo = new Photo();

      if ( !empty($input["photo_aditionalImageComments"]) )
        $photo->aditionalImageComments = $input["photo_aditionalImageComments"];
      $photo->allowCommercialUses = $input["photo_allowCommercialUses"];
      $photo->allowModifications = $input["photo_allowModifications"];
      $photo->city = $input["photo_city"];
      $photo->country = $input["photo_country"];
      if ( !empty($input["photo_description"]) )
        $photo->description = $input["photo_description"];
      if ( !empty($input["photo_district"]) )
        $photo->district = $input["photo_district"];
      if ( !empty($input["photo_imageAuthor"]) )
        $photo->imageAuthor = $input["photo_imageAuthor"];
      $photo->name = $input["photo_name"];
      $photo->state = $input["photo_state"];
      if ( !empty($input["photo_street"]) )
        $photo->street = $input["photo_street"];
      if ( !empty($input["photo_workAuthor"]) )
        $photo->workAuthor = $input["photo_workAuthor"];
      if ( !empty($input["photo_workDate"]) )
        $photo->workdate = $input["photo_workDate"];
      if ( !empty($input["photo_imageDate"]) )
      $photo->dataCriacao = $input["photo_imageDate"];

      $photo->nome_arquivo = $file->getClientOriginalName();

      $photo->user_id = Auth::user()->id;

      $photo->dataUpload = date('Y-m-d H:i:s');

      $photo->save();

      $ext = $file->getClientOriginalExtension();
      $photo->nome_arquivo = $photo->id.".".$ext;

      $photo->save();

      $tags_copy = $input['tags'];
      $tags = explode(',', $input['tags']);

      if (!empty($tags)) {


        $tags = array_map('trim', $tags);
        //$tags = array_map('strtolower', $tags);

        $tags = array_map('mb_strtolower', $tags); // com suporte para cadeias multibytes
        // tudo em minusculas, para remover redundancias, como Casa/casa/CASA
        $tags = array_unique($tags); //retira tags repetidas, se houver.
        foreach ($tags as $t) {
          $tag = Tag::where('name', $t)->first();

          if (is_null($tag)) {
            $tag = new Tag();
            $tag->name = $t;
            // 10/05/2015 msy begin
            try {
              $tag->save();
            } catch (PDOException $e) {
                Log::error("Logging exception, error to register tags");
                $photo->forceDelete();
                //$messages = array('tags'=>array('invalido'));
                return Redirect::to('/photos/upload')->with(['tags' => $input['tags']]); //->withErrors($messages)
            }
            // 10/05/2015  msy end
          }
          $photo->tags()->attach($tag->id);
          if ($tag->count == null)
            $tag->count = 0;
          $tag->count++;
          $tag->save();
        }
      }

      $source_page = $input["pageSource"]; //get url of the source page through form
      ActionUser::printUploadOrDownloadLog($photo->user_id, $photo->id, $source_page, "Upload", "user");
      ActionUser::printTags($photo->user_id, $photo->id, $tags_copy, $source_page, "user", "Inseriu");

      $image = Image::make(Input::file('photo'))->encode('jpg', 80); // todas começam com jpg quality 80
      $image->widen(600)->save(public_path().'/arquigrafia-images/'.$photo->id.'_view.jpg');
      $image->heighten(220)->save(public_path().'/arquigrafia-images/'.$photo->id.'_200h.jpg'); // deveria ser 220h, mantem por já haver alguns arquivos assim.
      $image->fit(186, 124)->encode('jpg', 70)->save(public_path().'/arquigrafia-images/'.$photo->id.'_home.jpg');
      $file->move(public_path().'/arquigrafia-images', $photo->id."_original.".strtolower($ext)); // original

      $photo->saveMetadata(strtolower($ext));

      return Redirect::to("/photos/{$photo->id}");

    } else {
    $messages = $validator->messages();
      return Redirect::to('/photos/upload')->withErrors($messages);
    }
 }
}


  // ORIGINAL
  public function download($id)
  {
    if (Auth::check()) {
      $photo = Photo::find($id);
      $originalFileExtension = strtolower(substr(strrchr($photo->nome_arquivo, '.'), 1));
      $filename = $id . '_original.' . $originalFileExtension;
      $path = public_path().'/arquigrafia-images/'. $filename;

      if( File::exists($path) ) {

        $user_id = Auth::user()->id;
        $pageSource = Request::header('referer');
        ActionUser::printUploadOrDownloadLog($user_id, $id, $pageSource, "Download", "user");

        header('Content-Description: File Transfer');
        header("Content-Disposition: attachment; filename=\"". $filename ."\"");
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: binary");
        header("Cache-Control: public");
        readfile($path);

        exit;
      }
      return "Arquivo original não encontrado.";
    } else {
      return "Você só pode fazer o download se estiver logado, caso tenha usuário e senha, faça novamente o login.";
    }
  }

  // COMMENT
  public function comment($id)
  {
    $input = Input::all();
    $rules = ['text' => 'required'];
    $validator = Validator::make($input, $rules);
    if ($validator->fails()) {
      $messages = $validator->messages();
      return Redirect::to("/photos/{$id}")->withErrors($messages);
    } else {
      $comment = ['text' => $input["text"], 'user_id' => Auth::user()->id];
      $comment = new Comment($comment);
      $photo = Photo::find($id);
      $photo->comments()->save($comment);

      $user = Auth::user();
      $source_page = Request::header('referer');
      ActionUser::printComment($user->id, $source_page, "Inseriu", $comment->id, $id, "user");
      
      /*Envio de notificação*/
      if ($user->id != $photo->user_id) {
        $user_note = User::find($photo->user_id);
        foreach ($user_note->notifications as $notification) {
        $info = $notification->render();
        if ($info[0] == "comment_posted" && $info[2] == $photo->id && $notification->read_at == null) {
          $note_id = $notification->notification_id;
          $note_user_id = $notification->id;
          $note = $notification;
        }
      }
      if (isset($note_id)) {
        $note_from_table = DB::table("notifications")->where("id","=", $note_id)->get();
        if (NotificationsController::isNotificationByUser($user->id, $note_from_table[0]->sender_id, $note_from_table[0]->data) == false) {
          $new_data = $note_from_table[0]->data . ":" . $user->id;
          DB::table("notifications")->where("id", "=", $note_id)->update(array("data" => $new_data, "created_at" => Carbon::now('America/Sao_Paulo')));
          $note->created_at = Carbon::now('America/Sao_Paulo');
          $note->save();  
        }
      }
      else Notification::create('comment_posted', $user, $comment, [$user_note], null);
      }

      $this->checkCommentCount(5,'test');

      return Redirect::to("/photos/{$id}");
    }

  }

  // EVALUATE
  public function saveEvaluation($id)
  {
    if (Auth::check()) {
      $evaluations =  Evaluation::where("user_id", Auth::id())->where("photo_id", $id)->get();
      $input = Input::all();
      
      if(Input::get('knownArchitecture') == true)
      {
        $knownArchitecture = Input::get('knownArchitecture');
      }else{
        $knownArchitecture = 'no';
      }

      
      $evaluation_string = "";
      $evaluation_names = array("Vertical-Horizontal", "Opaca-Translúcida", "Assimétrica-Simétrica", "Simples-Complexa", "Externa-Interna", "Fechada-Aberta");
      $i = 0;
      $user_id = Auth::user()->id;
      // pegar do banco as possives métricas
      $binomials = Binomial::all();  
      // fazer um loop por cada e salvar como uma avaliação
      if ($evaluations->isEmpty()) {
         $insertion_edition = "Inseriu";
        foreach ($binomials as $binomial) {
          $bid = $binomial->id;
          $newEvaluation = Evaluation::create([
            'photo_id'=> $id,
            'evaluationPosition'=> $input['value-'.$bid],
            'binomial_id'=> $bid,
            'user_id'=> $user_id,
            'knownArchitecture'=>$knownArchitecture
          ]);
          $evaluation_string = $evaluation_string . $evaluation_names[$i++] . ": " . $input['value-'.$bid] . ", ";
        }
      } else { 
          $insertion_edition = "Editou";
        foreach ($evaluations as $evaluation) {
          $bid = $evaluation->binomial_id;
          $evaluation->evaluationPosition = $input['value-'.$bid];
          $evaluation->knownArchitecture = $knownArchitecture;
          $evaluation->save();
          $evaluation_string = $evaluation_string . $evaluation_names[$i++] . ": " . $input['value-'.$bid] . ", ";
        }
      }
      $user_id = Auth::user()->id;
      $source_page = Request::header('referer');
      ActionUser::printEvaluation($user_id, $id, $source_page, "user", $insertion_edition, $evaluation_string);
      return Redirect::to("/photos/{$id}/evaluate")->with('message', '<strong>Avaliação salva com sucesso</strong><br>Abaixo você pode visualizar a média atual de avaliações');
    } else {
      // avaliação sem login
      return Redirect::to("/photos/{$id}")->with('message', '<strong>Erro na avaliação</strong><br>Faça login para poder avaliar');
    }
  }

  // BATCH RESIZE
  public function batch()
  {
    $photos = Photo::all();
    foreach ($photos as $photo) {
      $path = public_path().'/arquigrafia-images/'.$photo->id.'_view.jpg';
      // novo tamanho para home, o micro, para pré carregamento.
	  $new = public_path().'/arquigrafia-images/'.$photo->id.'_micro.jpg';
      if (is_file($path) && !is_file($new)) $image = Image::make($path)->fit(32,20)->save($new);
	  /*
	  $image = Image::make($path)->save(public_path().'/arquigrafia-images/'.$newid.'_view.jpg');
	  $image->heighten(220)->save(public_path().'/arquigrafia-images/'.$newid.'_200h.jpg');
	  $image->fit(186, 124)->encode('jpg', 70)->save(public_path().'/arquigrafia-images/'.$newid.'_home.jpg');
	  */
    }
    return "OK.";
  }
  
  // BATCH REGENERATE
  public function batchRegenerate()
  {
    $photos = Photo::all();
    foreach ($photos as $photo) {
      $path = public_path().'/arquigrafia-images/'.$photo->id.'_view.jpg';
	  $image = Image::make($path);
	  $image->heighten(220)->save(public_path().'/arquigrafia-images/'.$photo->id.'_200h.jpg');
	  $image->fit(186, 124)->encode('jpg', 70)->save(public_path().'/arquigrafia-images/'.$photo->id.'_home.jpg');
	  $image->fit(32,20)->save(public_path().'/arquigrafia-images/'.$photo->id.'_micro.jpg');
    }
    return "OK.";
  }

  public function evaluate($photoId ) { 
    $this->checkEvalCount(5, 'test');
    if(isset($_SERVER['QUERY_STRING'])) parse_str($_SERVER['QUERY_STRING']);
    $user_id = Auth::user()->id;
    $source_page = Request::header('referer');
    if(isset($f)) {
    if($f == "sb") ActionUser::printEvaluationAccess($user_id, $photoId, $source_page, "user", "pelo botão abaixo da imagem");
    elseif($f == "c") ActionUser::printEvaluationAccess($user_id, $photoId, $source_page, "user", "pelo botão abaixo do gráfico");
    elseif($f == "g") ActionUser::printEvaluationAccess($user_id, $photoId, $source_page, "user", "pelo gráfico");
    }
    else ActionUser::printEvaluationAccess($user_id, $photoId, $source_page, "user", "diretamente");
    return static::getEvaluation($photoId, Auth::user()->id, true);
  }

  private function checkEvalCount($number_assessment, $badge_name){
    $user = Auth::user();
    if(($user->badges()->where('name', $badge_name)->first()) != null){
        return;
      }
    if (($user->evaluations->groupBy('photo_id')->count()) == $number_assessment){
        $badge=Badge::where('name', $badge_name)->first();
        $user->badges()->attach($badge);
      }
    }

    private function checkCommentCount($number_comment, $badge_name){
      $user = Auth::user();
      if(($user->badges()->where('name', $badge_name)->first()) != null){
        return;
      }
      if (($user->comments->count()) == $number_comment){
        $badge=Badge::where('name', $badge_name)->first();
        $user->badges()->attach($badge);
      }
    }


  private function getEvaluation($photoId, $userId, $isOwner) {
    $photo = Photo::find($photoId);
    $binomials = Binomial::all()->keyBy('id');

    $average = Evaluation::average($photo->id);
    $evaluations = null;
    $averageAndEvaluations = null;
    $checkedKnowArchitecture = false;
    $user = null;
    $follow = true;
    if ($userId != null) {
      $user = User::find($userId);
      if (Auth::check()) {
        if (Auth::user()->following->contains($user->id))
          $follow = false;
        else
          $follow = true;
      }
      $averageAndEvaluations= Evaluation::averageAndUserEvaluation($photo->id,$userId);
      $evaluations =  Evaluation::where("user_id", $user->id)->where("photo_id", $photo->id)->orderBy("binomial_id", "asc")->get();
      $checkedKnowArchitecture= Evaluation::userKnowsArchitecture($photoId,$userId);
      
    }
    
    return View::make('/photos/evaluate',
      ['photos' => $photo, 'owner' => $user, 'follow' => $follow, 'tags' => $photo->tags, 'commentsCount' => $photo->comments->count(), 'commentsMessage' => static::createCommentsMessage($photo->comments->count()),
      'average' => $average, 'userEvaluations' => $evaluations,'userEvaluationsChart' => $averageAndEvaluations, 'binomials' => $binomials,
      'architectureName' => Photo::composeArchitectureName($photo->name),
      'similarPhotos'=>Photo::photosWithSimilarEvaluation($average,$photo->id),
      'isOwner' => $isOwner,
      'checkedKnowArchitecture' => $checkedKnowArchitecture]);
  }


  public function edit($id) {
    $photo = Photo::find($id);
    $logged_user = Auth::User();
    if ($logged_user == null) {
      return Redirect::action('PagesController@home');
    }
    elseif ($logged_user->id == $photo->user_id) {
    if (Session::has('tags'))
    {
      $tags = Session::pull('tags');
      $tags = explode(',', $tags);
    } else {
      $tags = $photo->tags->lists('name');
    }
    return View::make('photos.edit')
      ->with(['photo' => $photo, 'tags' => $tags] );
    }
    return Redirect::action('PagesController@home');
  }

  public function update($id) {
    $photo = Photo::find($id);
     Input::flashExcept('tags', 'photo');
     $input = Input::all();

    if (Input::has('tags'))
      $input["tags"] = str_replace(array('\'', '"', '[', ']'), '', $input["tags"]);
    else
      $input["tags"] = '';
    //2015-05-09 msy add validate for date image/work end
    $rules = array(
        'photo_name' => 'required',
        'photo_imageAuthor' => 'required',
        'tags' => 'required',
        'photo_country' => 'required',
        'photo_workDate' => 'date_format:"d/m/Y"',
        'photo_imageDate' => 'date_format:"d/m/Y"',
        'photo' => 'max:10240|mimes:jpeg,jpg,png,gif'

    );

  $validator = Validator::make($input, $rules);

  if ($validator->fails()) {
      $messages = $validator->messages();
      return Redirect::to('/photos/' . $photo->id . '/edit')->with('tags', $input['tags'])->withErrors($messages);
    } else {
      if ( !empty($input["photo_aditionalImageComments"]) )
        $photo->aditionalImageComments = $input["photo_aditionalImageComments"];
      $photo->allowCommercialUses = $input["photo_allowCommercialUses"];
      $photo->allowModifications = $input["photo_allowModifications"];
      $photo->city = $input["photo_city"];
      $photo->country = $input["photo_country"];
      $photo->description = $input["photo_description"];
      $photo->district = $input["photo_district"];
      $photo->imageAuthor = $input["photo_imageAuthor"];
      $photo->name = $input["photo_name"];
      $photo->state = $input["photo_state"];
      $photo->street = $input["photo_street"];
      $photo->workAuthor = $input["photo_workAuthor"];
      //2015-05-09 msy add validate for date image/work end
      if ( !empty($input["photo_workDate"])) {
        $photo->workdate = $input["photo_workDate"];
      }else {
        $photo->workdate = null;
      }

      if ( !empty($input["photo_imageDate"]) ){
        $photo->dataCriacao = $input["photo_imageDate"];
      }else {
        $photo->dataCriacao = null;
      }

    //endmsy
      if (Input::hasFile('photo') and Input::file('photo')->isValid()) {
        $file = Input::file('photo');
        $ext = $file->getClientOriginalExtension();
        $photo->nome_arquivo = $photo->id.".".$ext;
      }
      //update o field update_at
      $photo->touch();
      $photo->save();

      $tags_copy = $input['tags'];
      $tags = explode(',', $input['tags']);

      if (!empty($tags)) {
        $tags = array_map('trim', $tags);
        $tags = array_map('mb_strtolower', $tags);

        $tags_id = [];
        $photo_tags = $photo->tags;
        // tudo em minusculas, para remover redundancias, como Casa/casa/CASA
        $tags = array_unique($tags); //retira tags repetidas, se houver.

        foreach ($tags as $t) {

          $tag = Tag::where('name', $t)->first();

          if (is_null($tag)) {
            $tag = new Tag();
            $tag->name = $t;

            try{
              $tag->save();
            }catch(PDOException $e) {
              Log::error("Logging exception, error to edit tags 1");

              $messages = array('tags'=>array('Erro nos tags'));
              return Redirect::to("/photos/{$photo->id}/edit")->with(['tags' => $input['tags']])->withErrors($messages);

            }
          }
          if ( !$photo_tags->contains($tag) )
          {
            if ($tag->count == null) $tag->count = 0;
            $tag->count++;
            $photo->tags()->attach($tag->id);
            try{
              $tag->save();
            }catch(PDOException $e) {
              Log::error("Logging exception, error to edit tags 2");
              $messages = array('tags'=>array('Erro nos tags'));
              return Redirect::to("/photos/{$photo->id}/edit")->with(['tags' => $input['tags']])->withErrors($messages);
            }

          }
          array_push($tags_id, $tag->id);
        }


        foreach($photo_tags as $tag)
        {
          if (!in_array($tag->id, $tags_id))
          {
            $tag->count--;
            $photo->tags()->detach($tag->id);
            try{
              $tag->save();
            }catch(PDOException $e) {
              Log::error("Logging exception, error to edit tags 3");
              $messages = array('tags'=>array('Erro nos tags'));
              return Redirect::to("/photos/{$photo->id}/edit")->with(['tags' => $input['tags']])->withErrors($messages);
            }
          }
        }

      }

      if (Input::hasFile('photo') and Input::file('photo')->isValid()) {
        $image = Image::make(Input::file('photo'))->encode('jpg', 80); // todas começam com jpg quality 80
        $image->widen(600)->save(public_path().'/arquigrafia-images/'.$photo->id.'_view.jpg');
        $image->heighten(220)->save(public_path().'/arquigrafia-images/'.$photo->id.'_200h.jpg'); // deveria ser 220h, mantem por já haver alguns arquivos assim.
        $image->fit(186, 124)->encode('jpg', 70)->save(public_path().'/arquigrafia-images/'.$photo->id.'_home.jpg');
        $file->move(public_path().'/arquigrafia-images', $photo->id."_original.".strtolower($ext)); // original
        $photo->saveMetadata(strtolower($ext));
      }
      $source_page = Request::header('referer');
      ActionUser::printTags($photo->user_id, $id, $tags_copy, $source_page, "user", "Editou");
      return Redirect::to("/photos/{$photo->id}")->with('message', '<strong>Edição de informações da imagem</strong><br>Dados alterados com sucesso');

  }
}

  public function destroy($id) {
    $photo = Photo::find($id);
    $photo->delete();
    return Redirect::to('/users/' . $photo->user_id);
  }

  public function viewEvaluation($photoId, $userId ) {
    return static::getEvaluation($photoId, $userId, false);
  }

  public function showSimilarAverage($photoId) {
    return static::getEvaluation($photoId, null, false);
  }

  public function createCommentsMessage($commentsCount){
    $commentsMessage = '';
    if($commentsCount == 0)
      $commentsMessage = 'Ninguém comentou ainda esta imagem';
    else if($commentsCount == 1)
      $commentsMessage = 'Existe ' . $commentsCount . ' comentário sobre esta imagem';
    else
      $commentsMessage = 'Existem '. $commentsCount . ' comentários sobre esta imagem';
    return $commentsMessage;
  }

}
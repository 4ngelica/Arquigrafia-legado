<?php
//add
use lib\utils\ActionUser;
use lib\gamification\models\Badge;
use modules\institutions\models\Institution as Institution;
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
    $user = null;    
    $user = Auth::user();
    $photo_owner = $photos->user; 
    
    $photo_institution = $photos->institution;     
    
    $tags = $photos->tags;
    $binomials = Binomial::all()->keyBy('id');
    $average = Evaluation::average($photos->id); //dd($average);
    $evaluations = null;
    $photoliked = null;
    $follow = true;
    $followInstitution = true;
    $belongInstitution = false;
    $hasInstitution = false; 
    $institution = null;
    $currentPage = null;
    $urlBack = URL::previous();

    if (Auth::check()) {
      if(Session::has('institutionId')){
        $belongInstitution = Institution::belongInstitution($photos->id,Session::get('institutionId'));
        $hasInstitution = Institution::belongSomeInstitution($photos->id);
        $institution = Institution::find(Session::get('institutionId')); 
      } else{
        $hasInstitution = Institution::belongSomeInstitution($photos->id);
        
        if(!is_null($photo_institution) && $user->followingInstitution->contains($photo_institution->id)){ 
         
           $followInstitution = false;
        }        
      }
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
    $authorsList = $photos->authors->lists('name');
    
    $querySearch = "";
    $typeSearch = "";
    
    if(strpos(URL::previous(),'search') != false){

      if (strpos(URL::previous(),'more') !== false) {
        if(Session::has('last_advanced_search')){
          $lastSearch = Session::get('last_advanced_search');
          $typeSearch = $lastSearch['typeSearch']; 
          $currentPage = $lastSearch['page']; 
        }
      } else {
        if(Session::has('last_search')){
          $lastSearch = Session::get('last_search');
          $querySearch = $lastSearch['query'];
          $typeSearch = $lastSearch['typeSearch']; 
          $currentPage = $lastSearch['page']; 
          $urlBack = "search/";              
        }
      }
    }

    return View::make('/photos/show',
      ['photos' => $photos, 'owner' => $photo_owner, 'follow' => $follow, 'tags' => $tags,
      'commentsCount' => $photos->comments->count(),
      'commentsMessage' => static::createCommentsMessage($photos->comments->count()),
      'average' => $average, 'userEvaluations' => $evaluations, 'binomials' => $binomials,
      'architectureName' => Photo::composeArchitectureName($photos->name),
      'similarPhotos'=>Photo::photosWithSimilarEvaluation($average,$photos->id),
      'license' => $license,
      'belongInstitution' => $belongInstitution,
      'hasInstitution' => $hasInstitution,
      'ownerInstitution' => $photo_institution,
      'institution' => $institution,
      'authorsList' => $authorsList,
      'followInstitution' => $followInstitution,
      'user' => $user,
      'querySearch' => $querySearch,
      'currentPage' => $currentPage,
      'typeSearch' => $typeSearch,
      'urlBack' => $urlBack
    ]);
  }

  // upload form
  public function form()
  {
    if (Session::has('institutionId') ) {
      return Redirect::to('/');
    }
    $pageSource = Request::header('referer');
    if(empty($pageSource)) $pageSource = '';
    $tags = null;
    $work_authors = null;
    $centuryInput =  null;
    $decadeInput = null;
    $centuryImageInput = null;
    $decadeImageInput = null;
    $dates = false;
    $dateImage = false;

    if ( Session::has('tags') )
    {
      $tags = Session::pull('tags');
      $tags = explode(',', $tags);
    }

    if ( Session::has('work_authors') )
    {
      $work_authors = Session::pull('work_authors');
      $work_authors = explode(';', $work_authors);
    }

    if ( Session::has('centuryInput') ) {
       $centuryInput = Session::pull('centuryInput');
      //dd($century);
       $dates = true;
      }
    if ( Session::has('decadeInput') ){
       $decadeInput = Session::pull('decadeInput');
       $dates = true;
     }

     if ( Session::has('centuryImageInput') ) {
       $centuryImageInput = Session::pull('centuryImageInput');
      //dd($century);
       $dateImage = true;
      }
    if ( Session::has('decadeImageInput') ){
       $decadeImageInput = Session::pull('decadeImageInput');
       $dateImage = true;
     }

    $input['autoOpenModal'] = null;   

    return View::make('/photos/form')->with(['tags'=>$tags,'pageSource'=>$pageSource,       
      'user'=>Auth::user(),
      'centuryInput'=> $centuryInput,
      'decadeInput' =>  $decadeInput,
      'centuryImageInput'=> $centuryImageInput,
      'decadeImageInput' =>  $decadeImageInput,
      'autoOpenModal'=>$input['autoOpenModal'],
      'dates' => $dates,
      'dateImage' => $dateImage,
      'work_authors'=>$work_authors   
      ]);

  }

  

  public static function formatTags($tagsType){
    $tagsType = array_map('trim', $tagsType);
    $tagsType = array_map('mb_strtolower', $tagsType); 
    $tagsType = array_unique($tagsType);    
    return $tagsType;
  }

  public static function saveTags($tags,$photo){
    try {
      $saved_tags = [];
      foreach ($tags as $t) {
        $tag = Tag::where('name', $t)
         ->whereIn('type', array('Acervo','Livre'))->first();
        if ( is_null($tag) ) {
          $tag = new Tag();
          $tag->name = $t;
          $tag->type = 'Livre';
        }
        if($tag->count == null) $tag->count = 0;
        $tag->count++;
        $tag->save();
        $saved_tags[] = $tag->id;
      }
      $photo->tags()->sync($saved_tags, false);
      $saved = true;
    } catch(PDOException $e) {
      Log::error("Logging exception, error to register tags");           
      $saved = false;
    }
    return $saved;
  }

  public function paginateDrafts() {
    $institution = Session::get('institutionId');
    $perPage = Input::get('perPage') ?: 50;
    $drafts = Photo::withInstitution($institution)->onlyDrafts()->paginate($perPage);
    $view = View::make('drafts.draft_list')
      ->with([ 'drafts' => $drafts ])->render();
    return Response::json([
        'view' => $view,
        'current_page' => $drafts->getCurrentPage(),
        'last_page' => $drafts->getLastPage(),
        'total_items' => $drafts->getTotal()
    ]);
  }

  public function listDrafts() {
    $institution = Session::get('institutionId');
    if ( is_null($institution) ) {
      return Redirect::to('/');
    }
    $drafts = Photo::with('tags')->withInstitution($institution)
      ->onlyDrafts()->paginate(100);
    return View::make('drafts.show')->with([
      'drafts' => $drafts
    ]);
  }

  public function getDraft($id) { 
    $photo = Photo::onlyDrafts()->find($id);
    if ( is_null($photo) ) {
      return Redirect::to('/');
    }
    $input = array(
      'dates' => true,
      'draft_id' => $id,
      'support' => $photo->support,
      'tombo' => $photo->tombo,
      'subject' => $photo->subject,
      'characterization' => $photo->characterization,
      'photo_name' => $photo->name,
      'description' => $photo->description,
      'country' => $photo->country,
      'state' => $photo->state,
      'city' => $photo->city,
      'street' => $photo->street,
      'observation' => $photo->observation,
      'allowCommercialUses' => $photo->allowCommercialUses,
      'allowModifications' => $photo->allowModifications,
      'hygieneDate' => $this->date->formatDatePortugues($photo->hygieneDate),
      'backupDate' => $this->date->formatDatePortugues($photo->backupDate),
      'imageAuthor' => $photo->imageAuthor,
      'observation' => $photo->observation,
      'authorized' => $photo->authorized,
      'new_album-name' => $photo->albums()->first()
    );
    if ( $photo->tags->count() ) {
      $input['tagsArea'] = implode(',', $photo->tags->lists('name'));
    }
    if ( $photo->authors->count() ) {
     $input['work_authors'] = implode(';', $photo->authors->lists('name')); 
    }
    if ( $photo->workDateType == 'year' ) {
      $input['workDate'] = $photo->workdate;
    } elseif ( $photo->workDateType == 'decade' ) {
      $input['decade_select'] = $photo->workdate;
    } elseif ( $photo->workDateType == 'century' ) {
      $input['century'] = $photo->workdate;
    }
    if ( $photo->imageDateType == 'date' ) {
      $input['image_date'] = $this->date->formatDatePortugues($photo->dataCriacao);
    } elseif ( $photo->imageDateType == 'decade' ) {
      $input['decade_select_image'] = $photo->dataCriacao;
    } elseif ( $photo->imageDateType == 'century' ) {
      $input['century_image'] = $photo->dataCriacao;
    }
    return Redirect::to('/institutions/form/upload')
      ->withInput($input);
  }

  public function deleteDraft() {
    $id = Input::get('draft');
    $last_page = Input::get('last_page');
    $perPage = Input::get('per_page');
    $institution = Session::get('institutionId');
    $draft = Photo::withInstitution($institution)->onlyDrafts()->find($id);
    if ( !is_null($draft) ) {
      $draft->deleted_at = $draft->freshTimestampString();
      $draft->save();
      $drafts = Photo::withInstitution($institution)->onlyDrafts()->paginate($perPage);
      if ( $last_page > $drafts->getLastPage()) {
        return Response::json([
          'refresh' => true,
          'view' => View::make('drafts.draft_list')->with(compact('drafts'))->render(),
          'current_page' => $drafts->getCurrentPage(),
          'last_page' => $drafts->getLastPage(),
          'total_items' => $drafts->getTotal()
        ]);
      } else {
        return Response::json([
            'refresh' => false,
            'total_items' => $drafts->getTotal()
          ]);
      }
    }
    return Response::json(false);
  }

  

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

  public static function updateTags($newTags,$photo){
  
      $photo_tags = $photo->tags;
      $allTags = Tag::allTagsPhoto($photo->id); 
      //dd($allTags);
      foreach ($allTags as $tag){   
        $tag->count--;
        $tag->save();                
      }

      foreach ($allTags as $alltag) {
        $photo->tags()->detach($alltag->id);
      }

      try{    // dd($newTags); 
        foreach ($newTags as $t) {            
            $t = strtolower($t);           
             
            $tag = Tag::where('name', $t)
                     ->whereIn('type', array('Acervo','Livre'))->first();
             //        ->orWhere('type', 'Livre')
                    // ->first();
             //dd($tag);       
            if(is_null($tag)){
                $tag = new Tag();
                $tag->name = $t;
                $tag->type = 'Livre';
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

  

    

  public function store() {
      Input::flashExcept('tags', 'photo','work_authors');
      $input = Input::all();

      if (Input::has('tags'))
        $input["tags"] = str_replace(array('\'', '"', '[', ']'), '', $input["tags"]);
      else
        $input["tags"] = '';

      if (Input::has('work_authors')){
          $input["work_authors"] = str_replace(array('","'), '";"', $input["work_authors"]);    
          $input["work_authors"] = str_replace(array( '"','[', ']'), '', $input["work_authors"]);    
      }else
          $input["work_authors"] = '';
 
  
      $rules = array(
        'photo_name' => 'required',
        'photo_imageAuthor' => 'required',
        'tags' => 'required',
        'photo_country' => 'required',  
        'photo_authorization_checkbox' => 'required',
        'photo' => 'max:10240|required|mimes:jpeg,jpg,png,gif',
        //'photo_workDate' => 'date_format:"d/m/Y"',      
        'photo_imageDate' => 'date_format:d/m/Y|regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/'
      );

      $validator = Validator::make($input, $rules);

      if ($validator->fails()) {
          $messages = $validator->messages();
      
          return Redirect::to('/photos/upload')->with(['tags' => $input['tags'],
          'decadeInput'=>$input["decade_select"],
          'centuryInput'=>$input["century"],
          'decadeImageInput'=>$input["decade_select_image"],
          'centuryImageInput'=>$input["century_image"] ,
          'work_authors'=>$input["work_authors"]     
          ])->withErrors($messages);
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
      //if ( !empty($input["photo_workAuthor"]) )
      //  $photo->workAuthor = $input["photo_workAuthor"];
      
            if(!empty($input["workDate"])){             
               $photo->workdate = $input["workDate"];
               $photo->workDateType = "year";
            }elseif(!empty($input["decade_select"])){             
               $photo->workdate = $input["decade_select"];
               $photo->workDateType = "decade";
            }elseif (!empty($input["century"]) && $input["century"]!="NS") { 
               $photo->workdate = $input["century"];
               $photo->workDateType = "century";
            }else{ 
               $photo->workdate = NULL;
            }

      if(!empty($input["photo_imageDate"])){             
             $photo->dataCriacao = $this->date->formatDate($input["photo_imageDate"]);
             $photo->imageDateType = "date";
       }elseif(!empty($input["decade_select_image"])){             
            $photo->dataCriacao = $input["decade_select_image"];
            $photo->imageDateType = "decade";
       }elseif (!empty($input["century_image"]) && $input["century_image"]!="NS") { 
            $photo->dataCriacao = $input["century_image"];
            $photo->imageDateType = "century";
       }else{ 
            $photo->dataCriacao = NULL;
       }      
      
      $photo->nome_arquivo = $file->getClientOriginalName();

      $photo->user_id = Auth::user()->id;
      $photo->dataUpload = date('Y-m-d H:i:s');
      $photo->save();
      

      if ( !empty($input["new_album-name"]) ) {
        $album = Album::create([
          'title' => $input["new_album-name"],
          'description' => "",
          'user' => Auth::user(),
          'cover' => $photo,
        ]);
        if ( $album->isValid() ) {
            DB::insert('insert into album_elements (album_id, photo_id) values (?, ?)', array($album->id, $photo->id));
        }
      }
      elseif ( !empty($input["photo_album"]) ) {
        DB::insert('insert into album_elements (album_id, photo_id) values (?, ?)', array($input["photo_album"], $photo->id));
      }
      $ext = $file->getClientOriginalExtension();
      $photo->nome_arquivo = $photo->id.".".$ext;

      $photo->save();
     

      $tags_copy = $input['tags'];
      $tags = explode(',', $input['tags']);
          
      if (!empty($tags)) {           
              $tags = static::formatTags($tags);              
              $tagsSaved = static::saveTags($tags,$photo);
              
            if(!$tagsSaved){ 
                  $photo->forceDelete();
                  $messages = array('tags'=>array('Inserir pelo menos uma tag'));                  
                  return Redirect::to('/photos/upload')->with(['tags' => $input['tags']])->withErrors($messages);                  
            }
      }

      $author = new Author();
      if (!empty($input["work_authors"])) {
          $author->saveAuthors($input["work_authors"],$photo);
      }
            
      $input['autoOpenModal'] = 'true';  
      $source_page = $input["pageSource"]; //get url of the source page through form
      ActionUser::printUploadOrDownloadLog($photo->user_id, $photo->id, $source_page, "Upload", "user");
      ActionUser::printTags($photo->user_id, $photo->id, $tags_copy, $source_page, "user", "Inseriu");

      if(array_key_exists('rotate', $input))
          $angle = (float)$input['rotate'];
      else
          $angle = 0;
      $metadata       = Image::make(Input::file('photo'))->exif();
      $public_image   = Image::make(Input::file('photo'))->rotate($angle)->encode('jpg', 80);
      $original_image = Image::make(Input::file('photo'))->rotate($angle);

      $public_image->widen(600)->save(public_path().'/arquigrafia-images/'.$photo->id.'_view.jpg');
      $public_image->heighten(220)->save(public_path().'/arquigrafia-images/'.$photo->id.'_200h.jpg');
      $public_image->fit(186, 124)->encode('jpg', 70)->save(public_path().'/arquigrafia-images/'.$photo->id.'_home.jpg');
      $public_image->fit(32,20)->save(public_path().'/arquigrafia-images/'.$photo->id.'_micro.jpg');
      $original_image->save(storage_path().'/original-images/'.$photo->id."_original.".strtolower($ext));

      $photo->saveMetadata(strtolower($ext), $metadata);
      $input['photoId'] = $photo->id;
      $input['dates'] = true;
      $input['dateImage'] = true;
      //return Redirect::to("/photos/{$photo->id}");
      foreach(Auth::user()->followers as $users) {
        foreach ($users->news as $note) {
          if($note->news_type == 'new_photo' && $note->sender_id == Auth::user()->id) {
              $curr_note = $note;
            }
        }
        if(isset($curr_note)) {
          $date = $curr_note->created_at;
          if($date->diffInDays(Carbon::now('America/Sao_Paulo')) > 7) {
            News::create(array('object_type' => 'Photo', 
                           'object_id' => $photo->id, 
                           'user_id' => $users->id, 
                           'sender_id' => Auth::user()->id, 
                           'news_type' => 'new_photo'));
          }
          else {
            $curr_note->object_id = $photo->id;
            $curr_note->save();
          }
        }
        else {
          News::create(array('object_type' => 'Photo', 
                             'object_id' => $photo->id, 
                             'user_id' => $users->id, 
                             'sender_id' => Auth::user()->id, 
                             'news_type' => 'new_photo'));
        }
      }
      return Redirect::back()->withInput($input);

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
      if(/*$photo->authorized*/true) {
        $originalFileExtension = strtolower(substr(strrchr($photo->nome_arquivo, '.'), 1));
        $filename = $id . '_original.' . $originalFileExtension;
        $path = storage_path().'/original-images/'. $filename;

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

      /*News feed*/
      foreach ($user->followers as $users) {
        foreach ($users->news as $news) {
          if ($news->news_type == 'commented_photo' && Comment::find($news->object_id)->photo_id == $id) {
              $last_news = $news;
              $primary = 'commented_photo';
          }
          else if ($news->news_type == 'evaluated_photo' || $news->news_type == 'liked_photo') {
            if ($news->object_id == $id) {
              $last_news = $news;
              $primary = 'other';
            }
          }
        }
        if (isset($last_news)) {
          $last_update = $last_news->updated_at;
          if($last_update->diffInDays(Carbon::now('America/Sao_Paulo')) < 7) {
            if ($news->sender_id == $user->id) {
              $already_sent = true;
            }
            else if ($news->data != null) {
              $data = explode(":", $news->data);
              for($i = 1; $i < count($data); $i++) {
                if($data[$i] == $user->id) {
                  $already_sent = true;
                }
              }
            }
            if (!isset($already_sent)) {
              $data = $last_news->data . ":" . $user->id;
              $last_news->data = $data;
              $last_news->save();
            }
            if ($primary == 'other') {
              if ($last_news->secondary_type == null) {
                $last_news->secondary_type = 'commented_photo';
              }
              else if ($last_news->tertiary_type == null) {
                $last_news->tertiary_type = 'commented_photo';
              }
              $last_news->save();
            }
          }
          else {
            News::create(array('object_type' => 'Comment', 
                               'object_id' => $comment->id, 
                               'user_id' => $users->id, 
                               'sender_id' => $user->id, 
                               'news_type' => 'commented_photo'));
          }
        }
        else {
          News::create(array('object_type' => 'Comment', 
                             'object_id' => $comment->id, 
                             'user_id' => $users->id, 
                             'sender_id' => $user->id, 
                             'news_type' => 'commented_photo'));
        }
      }     
 
      $this->checkCommentCount(5,'test');
      return Redirect::to("/photos/{$id}");
    }

  }

  // EVALUATE
  //saveEvaluation
  

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

 

// need to be modified
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




  public function edit($id) {
    if (Session::has('institutionId') ) {
      return Redirect::to('/');
    }
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

      if( Session::has('work_authors'))
      {
        $work_authors = Session::pull('work_authors');
        $work_authors = explode(';', $work_authors);
      } else{
        $work_authors = $photo->authors->lists('name');
      }

      $dateYear = "";
      $decadeInput = "";
      $centuryInput = "";
      $decadeImageInput = "";
      $centuryImageInput = "";
            
      if(Session::has('workDate')){     
        $dateYear = Session::pull('workDate');
      }elseif($photo->workDateType == "year"){
        $dateYear = $photo->workdate;
      }/*elseif($photo->workDateType == NULL && $photo->workdate!= "" && DateTime::createFromFormat('Y-m-d', $photo->workdate) == true){
              $date = DateTime::createFromFormat("Y-m-d",$photo->workdate);
              $dateYear = $date->format("Y");
      }*/

      if(Session::has('decadeInput')){ 
         $decadeInput = Session::pull('decadeInput'); 
      }elseif ($photo->workDateType == "decade"){
          $decadeInput = $photo->workdate;
      }

      if(Session::has('centuryInput')){
         $centuryInput = Session::pull('centuryInput');
      }elseif($photo->workDateType == "century") {
         $centuryInput = $photo->workdate;
         //dd($centuryInput);
      }

      if(Session::has('decadeImageInput')){ 
         $decadeImageInput = Session::pull('decadeImageInput'); 
      }elseif ($photo->imageDateType == "decade"){
         $decadeImageInput = $photo->dataCriacao;
      }

      if(Session::has('centuryImageInput')){
         $centuryImageInput = Session::pull('centuryImageInput');
      }elseif($photo->imageDateType == "century") {
         $centuryImageInput = $photo->dataCriacao;
         //dd($centuryInput);
      }
      

      return View::make('photos.edit')
        ->with(['photo' => $photo, 'tags' => $tags,
            'dateYear' => $dateYear,
            'centuryInput'=> $centuryInput,
            'decadeInput' =>  $decadeInput,
            'centuryImageInput'=> $centuryImageInput,
            'decadeImageInput' =>  $decadeImageInput,
            'work_authors' => $work_authors
          ] );
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

      if (Input::has('work_authors')){
        $input["work_authors"] = str_replace(array('","'), '";"', $input["work_authors"]);    
        $input["work_authors"] = str_replace(array( '"','[', ']'), '', $input["work_authors"]);    
      }else  $input["work_authors"] = '';
    
      $workDate = "";
      $decadeInput = "";
      $centuryInput = "";
      $imageDateCreated = "";
      $decadeImageInput = "";
      $centuryImageInput = "";

      if(Input::has('photo_imageDate')){        
        $imageDateCreated = $input["photo_imageDate"];
      }elseif(Input::has('decade_select_image')){ 
         $decadeImageInput = $input["decade_select_image"];
      }elseif(Input::has('century_image')){
         $centuryImageInput = $input["century_image"];
      }

      if(Input::has('workDate')){        
        $workDate = $input["workDate"];
      }elseif(Input::has('decade_select')){ 
         $decadeInput = $input["decade_select"];
      }elseif(Input::has('century')){
         $centuryInput = $input["century"];
      }

      $rules = array(
        'photo_name' => 'required',
        'photo_imageAuthor' => 'required',
        'tags' => 'required',
        'photo_country' => 'required',
        //'photo_workDate' => 'date_format:"d/m/Y"',
        'photo_imageDate' => 'date_format:d/m/Y|regex:/[0-9]{2}\/[0-9]{2}\/[0-9]{4}/',
        'photo' => 'max:10240|mimes:jpeg,jpg,png,gif'

          );

      $validator = Validator::make($input, $rules);

      if ($validator->fails()) {       
        $messages = $validator->messages();
        return Redirect::to('/photos/' . $photo->id . '/edit')->with(['tags' => $input['tags'],
          'decadeInput' => $decadeInput,
          'centuryInput' => $centuryInput,   
          'workDate' => $workDate,
          'decadeImageInput'=>$decadeImageInput,
          'centuryImageInput'=>$centuryImageInput,  
          'imageDateCreated' => $imageDateCreated,
          'work_authors'=>$input["work_authors"] 
          ])->withErrors($messages);

      } else{
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
        //$photo->workAuthor = $input["photo_workAuthor"];     
      
        if(!empty($input["workDate"])){             
            $photo->workdate = $input["workDate"];
            $photo->workDateType = "year";
        }elseif(!empty($input["decade_select"])){             
            $photo->workdate = $input["decade_select"];
            $photo->workDateType = "decade";
        }elseif (!empty($input["century"]) && $input["century"]!="NS") { 
            $photo->workdate = $input["century"];
            $photo->workDateType = "century";
        }else{ 
            $photo->workdate = NULL;
            $photo->workDateType = NULL;
        }

        if(!empty($input["photo_imageDate"])){             
            $photo->dataCriacao = $this->date->formatDate($input["photo_imageDate"]);
            $photo->imageDateType = "date";
        }elseif(!empty($input["decade_select_image"])){             
            $photo->dataCriacao = $input["decade_select_image"];
            $photo->imageDateType = "decade";
        }elseif (!empty($input["century_image"]) && $input["century_image"]!="NS") { 
            $photo->dataCriacao = $input["century_image"];
            $photo->imageDateType = "century";
        }else{ 
            $photo->dataCriacao = NULL;
        }  


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

        if(!empty($tags)) { 
            $tags = static::formatTags($tags);              
            $tagsSaved = static::updateTags($tags,$photo);

            if(!$tagsSaved){
                $photo->forceDelete();
                $messages = array('tags'=>array('Erro nos tags'));
                return Redirect::to("/photos/{$photo->id}/edit")->with([
                    'tags' => $input['tags']])->withErrors($messages);
            }
        }
 
        $author = new Author();
        if (!empty($input["work_authors"])) {
            $author->updateAuthors($input["work_authors"],$photo);
        }else{
            $author->deleteAuthorPhoto($photo);
        }

        if (Input::hasFile('photo') and Input::file('photo')->isValid()) {
          if(array_key_exists('rotate', $input))
              $angle = (float)$input['rotate'];
          else
              $angle = 0;
          $metadata       = Image::make(Input::file('photo'))->exif();
          $public_image   = Image::make(Input::file('photo'))->rotate($angle)->encode('jpg', 80);
          $original_image = Image::make(Input::file('photo'))->rotate($angle);
          $create = true;
        }else {
          list($photo_id, $ext) = explode(".", $photo->nome_arquivo);
          $path                 = storage_path().'/original-images/'.$photo->id.'_original.'.$ext;
          $metadata             = Image::make($path)->exif();

          if (array_key_exists('rotate', $input) and ($input['rotate'] != 0)) {
              $angle = (float)$input['rotate'];
              $public_image   = Image::make($path)->rotate($angle)->encode('jpg', 80);
              $original_image = Image::make($path)->rotate($angle);
              $create = true;
          } else
              $create = false;
        }

        if ($create) {
            $public_image->widen(600)->save(public_path().'/arquigrafia-images/'.$photo->id.'_view.jpg');
            $public_image->heighten(220)->save(public_path().'/arquigrafia-images/'.$photo->id.'_200h.jpg'); 
            $public_image->fit(186, 124)->encode('jpg', 70)->save(public_path().'/arquigrafia-images/'.$photo->id.'_home.jpg');
            $public_image->fit(32,20)->save(public_path().'/arquigrafia-images/'.$photo->id.'_micro.jpg');
            $original_image->save(storage_path().'/original-images/'.$photo->id."_original.".strtolower($ext));
        }
        $photo->saveMetadata(strtolower($ext), $metadata);

        $source_page = Request::header('referer');
        ActionUser::printTags($photo->user_id, $id, $tags_copy, $source_page, "user", "Editou");

        /*News feed*/
        $user = User::find($photo->user_id);
        foreach ($user->followers as $users) {
          foreach ($users->news as $note) {
            if($note->news_type == 'edited_photo' && $note->sender_id == $user->id) {
              $curr_note = $note;
            }
          }
          if(isset($curr_note)) {
            if($note->sender_id == $user->id) {
              $date = $curr_note->created_at;
              if($date->diffInDays(Carbon::now('America/Sao_Paulo')) > 7) {
                News::create(array('object_type' => 'Photo', 
                                   'object_id' => $id, 
                                   'user_id' => $users->id, 
                                   'sender_id' => $user->id, 
                                   'news_type' => 'edited_photo'));
              }
            }
          }
          else {
              News::create(array('object_type' => 'Photo', 
                                 'object_id' => $id, 
                                 'user_id' => $users->id, 
                                 'sender_id' => $user->id, 
                                 'news_type' => 'edited_photo'));
          }
        }
        return Redirect::to("/photos/{$photo->id}")->with('message', '<strong>Edição de informações da imagem</strong><br>Dados alterados com sucesso');
      }
  }

  public function destroy($id) {
    $photo = Photo::find($id);
    foreach($photo->tags as $tag) {
      $tag->count = $tag->count - 1;
      $tag->save();
    }
    DB::table('tag_assignments')->where('photo_id', '=', $photo->id)->delete();
    $all_users = User::all();
    foreach ($all_users as $users) {
      foreach ($users->news as $news) {
        if ($news->news_type == 'liked_photo' || $news->news_type == 'highlight_of_the_week' || $news->news_type == 'edited_photo' || $news->news_type == 'evaluated_photo' || $news->news_type == 'new_institutional_photo' || $news->news_type == 'new_photo') {
          if ($news->object_id == $photo->id) {
            $news->delete();
          }
        }
        if ($news->news_type == 'commented_photo') {
          $object_comment = Comment::find($news->object_id);
          $object_photo = Photo::find($object_comment->photo_id);
          if ($photo->id == $object_photo->id) {
            $news->delete();
          }
        }
      }
      foreach ($users->notifications as $notes) {
        $curr_note = DB::table('notifications')->where('id', $notes->notification_id)->first();
        if (!is_null($curr_note)) {
          if ($curr_note->type == 'photo_liked') {
            if ($curr_note->object_id == $photo->id) {
              DB::table('notifications')->where('id', $notes->notification_id)->delete();
              $notes->delete();
            }
          }
          if ($curr_note->type = 'comment_liked' || $curr_note->type = 'comment_posted') {
            $note_photo = null;
            $note_comment = Comment::find($curr_note->object_id);
            if (!is_null($note_comment)) $note_photo = Photo::find($note_comment->photo_id);
            if(!is_null($note_photo)) {
              if ($photo->id == $note_photo->id) {
                DB::table('notifications')->where('id', $notes->notification_id)->delete();
                $notes->delete();
              }
            }
          }
        }
      }
    }

    $photo->delete();
    return Redirect::to('/users/' . $photo->user_id);
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
  /*public function evaluate($photoId ) { 
    if (Session::has('institutionId') ) {
      return Redirect::to('/');
    }
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
  } */
  /*
  public function viewEvaluation($photoId, $userId ) {
    
    return static::getEvaluation($photoId, $userId, false);
  } */

  /*public function showSimilarAverage($photoId) {
    $isOwner = false;
    if (Auth::check()) $userId = Auth::user()->id;     
    $photo = Photo::find($photoId);     
    if($photo->user_id == $userId ) $isOwner = true;
   
    return static::getEvaluation($photoId, $userId, $isOwner);
  }*/
  /*
  private function getEvaluation($photoId, $userId, $isOwner) {
    $photo = Photo::find($photoId);
    $binomials = Binomial::all()->keyBy('id'); 
    $average = Evaluation::average($photo->id); 
    $evaluations = null;
    $averageAndEvaluations = null;
    $checkedKnowArchitecture = false;
    $checkedAreArchitecture = false;
    $user = null;
    $follow = true;
    //echo $user_id; die();
    if ($userId != null) {
      $user = User::find($userId);
      if (Auth::check()) {
        if (Auth::user()->following->contains($user->id))
          $follow = false;
        else
          $follow = true;
    }

      $averageAndEvaluations= Evaluation::averageAndUserEvaluation($photo->id,$userId);
      $evaluations =  Evaluation::where("user_id",
        $user->id)->where("photo_id", $photo->id)->orderBy("binomial_id", "asc")->get();
      $checkedKnowArchitecture= Evaluation::userKnowsArchitecture($photoId,$userId);
      $checkedAreArchitecture= Evaluation::userAreArchitecture($photoId,$userId);

    }
    
    return View::make('/photos/evaluate',
      [
        'photos' => $photo, 
        'owner' => $user, 
        'follow' => $follow, 
        'tags' => $photo->tags, 
        'commentsCount' => $photo->comments->count(), 
        'commentsMessage' => static::createCommentsMessage($photo->comments->count()),
        'average' => $average, 
        'userEvaluations' => $evaluations,
        'userEvaluationsChart' => $averageAndEvaluations, 
        'binomials' => $binomials,
        'architectureName' => Photo::composeArchitectureName($photo->name),
        'similarPhotos'=>Photo::photosWithSimilarEvaluation($average,$photo->id),
        'isOwner' => $isOwner,
        'checkedKnowArchitecture' => $checkedKnowArchitecture,
        'checkedAreArchitecture' => $checkedAreArchitecture
      ]);
  } */

  //formInstitutional
  //saveFormInstitutional
  //editFormInstitutional
  //updateInstitutional
}

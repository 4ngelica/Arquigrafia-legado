<?php
//add
use lib\utils\ActionUser;
use lib\log\EventLogger;
use Carbon\Carbon;
use lib\date\Date;
use modules\gamification\models\Badge;
use modules\institutions\models\Institution as Institution;
use modules\collaborative\models\Tag as Tag;
use modules\collaborative\models\Comment as Comment;
use modules\collaborative\models\Like as Like;
use modules\evaluations\models\Evaluation as Evaluation;
use modules\evaluations\models\Binomial as Binomial;

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
    $average = Evaluation::average($photos->id);
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
        //dd($hasInstitution);
        if(!is_null($photo_institution) && $user->followingInstitution->contains($photo_institution->id)){ 
         
           $followInstitution = false;
        }        
      }
      $evaluations =  Evaluation::where("user_id", $user->id)->where("photo_id", $id)->orderBy("binomial_id", "asc")->get();
      
      if ($user->following->contains($photo_owner->id)) {
        $follow = false;
      }
    }
    EventLogger::printEventLogs($id, "select_photo", null, "Web");

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
      'commentsMessage' => Comment::createCommentsMessage($photos->comments->count()),
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
      'urlBack' => $urlBack,
      'institutionId' => $photos->institution_id
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
       $dates = true;
      }
    if ( Session::has('decadeInput') ){
       $decadeInput = Session::pull('decadeInput');
       $dates = true;
     }

     if ( Session::has('centuryImageInput') ) {
       $centuryImageInput = Session::pull('centuryImageInput');
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


  public function store() 
  {
      Input::flashExcept('tags', 'photo','work_authors');
      $input = Input::all();

      if (Input::has('tags'))
        $input["tags"] = str_replace(array('\'', '"', '[', ']'), '', $input["tags"]);
      else
        $input["tags"] = '';

      if (Input::has('work_authors')){
          $input["work_authors"] = str_replace(array('","'), '";"', $input["work_authors"]);    
          $input["work_authors"] = str_replace(array( '"','[', ']'), '', $input["work_authors"]);    
      }else $input["work_authors"] = '';
 
  
      $rules = array(
        'photo_name' => 'required',
        'photo_imageAuthor' => 'required',
        'tags' => 'required',
        'photo_country' => 'required',  
        'photo_authorization_checkbox' => 'required',
        'photo' => 'max:10240|required|mimes:jpeg,jpg,png,gif',    
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
            } elseif ( !empty($input["photo_album"]) ) {
                DB::insert('insert into album_elements (album_id, photo_id) values (?, ?)', array($input["photo_album"], $photo->id));
            }
            $ext = $file->getClientOriginalExtension();  
            Photo::fileNamePhoto($photo, $ext);    

            $tags = explode(',', $input['tags']);
          
            if (!empty($tags)) {           
                $tags = Tag::formatTags($tags);              
                $tagsSaved = Tag::saveTags($tags,$photo);
              
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
            //$source_page = $input["pageSource"]; //get url of the source page through form
            $eventContent['tags'] = $input['tags'];
            EventLogger::printEventLogs($photo->id, 'upload', NULL,'Web');
            EventLogger::printEventLogs($photo->id, 'insert_tags', $eventContent,'Web');


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
      if($photo->authorized) {
        $originalFileExtension = strtolower(substr(strrchr($photo->nome_arquivo, '.'), 1));
        $filename = $id . '_original.' . $originalFileExtension;
        $path = storage_path().'/original-images/'. $filename;

        if( File::exists($path) ) {
          EventLogger::printEventLogs($id, 'download', null, 'Web');

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

  // BATCH RESIZE
  public function batch()
  {
    $photos = Photo::all();
    foreach ($photos as $photo) {
      $path = public_path().'/arquigrafia-images/'.$photo->id.'_view.jpg';
      // novo tamanho para home, o micro, para pré carregamento.
    $new = public_path().'/arquigrafia-images/'.$photo->id.'_micro.jpg';
      if (is_file($path) && !is_file($new)) $image = Image::make($path)->fit(32,20)->save($new);
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
      }

      if(Session::has('decadeInput')){ 
         $decadeInput = Session::pull('decadeInput'); 
      }elseif ($photo->workDateType == "decade"){
          $decadeInput = $photo->workdate;
      }

      if(Session::has('centuryInput')){
         $centuryInput = Session::pull('centuryInput');
      }elseif($photo->workDateType == "century") {
         $centuryInput = $photo->workdate;
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
            $tags = Tag::formatTags($tags);              
            $tagsSaved = Tag::updateTags($tags,$photo);

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

        EventLogger::printEventLogs($id, 'editi_photo', null, 'Web');

        $user = User::find($photo->user_id);
        
        return Redirect::to("/photos/{$photo->id}")->with('message', '<strong>Edição de informações da imagem</strong><br>Dados alterados com sucesso');
      }
  }

  public function destroy($id) 
  {
    $photo = Photo::find($id);
    foreach($photo->tags as $tag) {
      $tag->count = $tag->count - 1;
      $tag->save();
    }
    DB::table('tag_assignments')->where('photo_id', '=', $photo->id)->delete();

    $photo->delete();
    return Redirect::to('/users/' . $photo->user_id);
  }
}

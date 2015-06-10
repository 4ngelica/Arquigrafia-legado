<?php
//add
use lib\utils\ActionUser;

class PhotosController extends \BaseController {

	public function __construct() 
  {
    $this->beforeFilter('auth', 
      array( 'except' => ['index','show'] ));
  }

  public function index()
	{
    $photos = Photo::all();
		return View::make('/photos/index',['photos' => $photos]);
	}

	public function show($id)
	{ 
    $photos = Photo::whereid($id)->first();
    if (!isset($photos))
      return Redirect::to('/');
    $user = User::find($photos->user_id);
    $tags = $photos->tags;
    $binomials = Binomial::all()->keyBy('id');
    $average = Evaluation::average($photos->id);
    $evaluations = null;
    if (Auth::check()) {
      $evaluations =  Evaluation::where("user_id", Auth::id())->where("photo_id", $id)->orderBy("binomial_id", "asc")->get();
      if (Auth::user()->following->contains($user->id))
        $follow = false;
      else 
        $follow = true;
    } else {
      $follow = true;
    }    

    return View::make('/photos/show',
      ['photos' => $photos, 'owner' => $user, 'follow' => $follow, 'tags' => $tags, 'commentsCount' => $photos->comments->count(),
      'average' => $average, 'userEvaluations' => $evaluations, 'binomials' => $binomials, 
      'architectureName' => Photo::composeArchitectureName($photos->name),
      'similarPhotos'=>Photo::photosWithSimilarEvaluation($average,$photos->id)
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
    return View::make('/photos/form')->with(['tags', $tags,'pageSource'=>$pageSource]);
    
	}

  public function store() {  
	
	Input::flashExcept('tags', 'photo');
	
	$input = Input::all();

  if (Input::has('tags')) 
    $input["tags"] = str_replace(array('\'', '"', '[', ']'), '', $input["tags"]);    
  else
    $input["tags"] = '';
//2015-05-25 msy add validate for date image/work end
  //validate for tamnho maximo e tipo de extensao
    $rules = array(			
      'photo_name' => 'required',
      'photo_imageAuthor' => 'required',
      'tags' => 'required',
      'photo_country' => 'required',
      'photo_state' => 'required',
	    'photo_city' => 'required',
      'photo_authorization_checkbox' => 'required',
      'photo' => 'max:10240|required|mimes:jpeg,jpg,png,gif',
      'photo_workDate' => 'date_format:"d/m/Y"',
      'photo_imageDate' => 'date_format:"d/m/Y"'
    );
//echo $input["pageSource"]; die();
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
        $photo->workdate = Photo::formatDate($input["photo_workDate"]);
      if ( !empty($input["photo_imageDate"]) )
      $photo->dataCriacao = Photo::formatDate($input["photo_imageDate"]);

      $photo->nome_arquivo = $file->getClientOriginalName();

      $photo->user_id = Auth::user()->id;
      
      $photo->dataUpload = date('Y-m-d H:i:s');

      $photo->save();

      $ext = $file->getClientOriginalExtension();
      $photo->nome_arquivo = $photo->id.".".$ext;
      
      $photo->save();

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

      //add
      $pageSource = $input["pageSource"]; //get url of the source page through form
      ActionUser::userEvents($photo->user_id, $photo->id,'upload',$pageSource, "");

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
      
        /*==================================================================================*/
        /*      Log de Downloads - user_id, photo_id, download_date                         */
        /*==================================================================================*/
        
        $user_id = Auth::user()->id;
        $pageSource = Request::header('referer'); //get url of the source page
        ActionUser::userEvents($user_id,$id,'downloads',$pageSource, "");

        /*================================================================================*/

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

      $user_id = Auth::user()->id;
      $pageSource = Request::header('referer');
      ActionUser::userEvents($user_id, $id,'comments',$pageSource, "insertion");

      return Redirect::to("/photos/{$id}");
    }
    
  }
  
  // EVALUATE
  public function saveEvaluation($id)
  {
    if (Auth::check()) {
      $evaluations =  Evaluation::where("user_id", Auth::id())->where("photo_id", $id)->get();
      $input = Input::all();
      $user_id = Auth::user()->id;
      // pegar do banco as possives métricas
      $binomials = Binomial::all();
      // fazer um loop por cada e salvar como uma avaliação
      if ($evaluations->isEmpty()) {
        foreach ($binomials as $binomial) {
        $bid = $binomial->id;        
          $newEvaluation = Evaluation::create([
            'photo_id'=> $id,
            'evaluationPosition'=> $input['value-'.$bid],
            'binomial_id'=> $bid,
            'user_id'=> $user_id
          ]);
        } 
      } else {
        foreach ($evaluations as $evaluation) {
          $bid = $evaluation->binomial_id;
          $evaluation->evaluationPosition = $input['value-'.$bid];
          $evaluation->save();
      } 

      }
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
      $new = public_path().'/arquigrafia-images/'.$photo->id.'_home.jpg';
      if (is_file($path) && !is_file($new)) $image = Image::make($path)->fit(186, 124)->save($new);
    }
    return "OK.";
  }

  public function evaluate($photoId ) { 
    return static::getEvaluation($photoId, Auth::user()->id, true);    
  }

  private function getEvaluation($photoId, $userId, $isOwner) {    
    $photo = Photo::find($photoId);    
    $binomials = Binomial::all()->keyBy('id');
    //dd($binomials); 
    
    $average = Evaluation::average($photo->id); 
    $evaluations = null;  
    $averageAndEvaluations = null;
    // dd($average);
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
    } //dd($evaluations);

    return View::make('/photos/evaluate',
      ['photos' => $photo, 'owner' => $user, 'follow' => $follow, 'tags' => $photo->tags, 'commentsCount' => $photo->comments->count(),
      'average' => $average, 'userEvaluations' => $evaluations,'userEvaluationsChart' => $averageAndEvaluations, 'binomials' => $binomials,
      'architectureName' => Photo::composeArchitectureName($photo->name),
      'similarPhotos'=>Photo::photosWithSimilarEvaluation($average,$photo->id), 
      'isOwner' => $isOwner]);
  }


  public function edit($id) {
    $photo = Photo::find($id);
    
    
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
        'photo_state' => 'required',
        'photo_city' => 'required',
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
      //2015-05-09 msy add validate for date image/work end
      if ( !empty($input["photo_workDate"])) {
        $photo->workdate = Photo::formatDate($input["photo_workDate"]);
      }else{
        $photo->workdate = null;
      }

      if ( !empty($input["photo_imageDate"]) ){ 
        $photo->dataCriacao = Photo::formatDate($input["photo_imageDate"]);
      }else{
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

  public function getPhotoInfo($id) {
    $photo = Photo::find($id);
    return Response::json(View::make('/photos/get-info')->with('photo', $photo)->render());
  }

}
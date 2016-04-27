<?php
//namespace app\controllers;

//use app\controllers\Controller;
//use app\controllers\Photos\PhotosController\Photo;
//use Photos;
use lib\utils\ActionUser;

class EvaluationsController extends \BaseController {

  // private $fphotos;

   public function __construct()
    {  // dd("dddh");
      //dd(new app/controllers/Photos\PhotosController\Photos);
    //    $this->fphotos = $fphotos;
        //$this->photos = $photos; 
    }
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
    dd("eval");
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{ //$photoId = $id;
    //$userId = Auth::user()->id;
		//return static::getEvaluation($photoId, $userId, false);
	}

    public function evaluate($photoId ) {
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
      }else ActionUser::printEvaluationAccess($user_id, $photoId, $source_page, "user", "diretamente");
        
      return static::getEvaluation($photoId, Auth::user()->id, true);
    }




   public function viewEvaluation($photoId, $userId ) { 
    return static::getEvaluation($photoId, $userId, false);
   }      

   public function showSimilarAverage($photoId) {
      $isOwner = false;
      if (Auth::check()) $userId = Auth::user()->id;     
      $photo = Photo::find($photoId);     
      if($photo->user_id == $userId ) $isOwner = true;
      
      return static::getEvaluation($photoId, $userId, $isOwner);
   }

	 private function getEvaluation($photoId, $userId, $isOwner) {
	   $photo = Photo::find($photoId);
     //dd($photo);
     $binomials = Binomial::all()->keyBy('id'); 
     $average = Evaluation::average($photo->id); 
     $evaluations = null;
     $averageAndEvaluations = null;
     $checkedKnowArchitecture = false;
     $checkedAreArchitecture = false;
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
     }
     if ($userId != null) {
        $averageAndEvaluations= Evaluation::averageAndUserEvaluation($photo->id,$userId);
        $evaluations =  Evaluation::where("user_id",
        $user->id)->where("photo_id", $photo->id)->orderBy("binomial_id", "asc")->get();
        $checkedKnowArchitecture= Evaluation::userKnowsArchitecture($photoId,$userId);
        $checkedAreArchitecture= Evaluation::userAreArchitecture($photoId,$userId);
     }    
     
     //$controller = new PhotosController;
     //'commentsCount' => $photo->comments->count(), 
        //'commentsMessage' => $this->photos->createCommentsMessage($photo->comments->count()),
     //dd($photo->createCommentsMessage(0));
      return View::make('/evaluations/evaluate',
      [
        'photos' => $photo, 
        'owner' => $user, 
        'follow' => $follow, 
        'tags' => $photo->tags,        
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
	}

  /**
   * Show the form for creating a new resource.
   *
   * @return Response
   */
  public function create()
  {
    //
  }

  /** Store a newly created resource in storage.
   * @return Response
   */
  public function store()
  {
    //
  }
  
  //saveEvaluation($id)
  public function saveEvaluation($id)
  {
      if (Auth::check()) {
          $evaluations =  Evaluation::where("user_id", Auth::id())->where("photo_id", $id)->get();
          $input = Input::all();
          if(Input::get('knownArchitecture') == true)
              $knownArchitecture = Input::get('knownArchitecture');
          else $knownArchitecture = 'no';
          
          if(Input::get('areArchitecture') == true) $areArchitecture = Input::get('areArchitecture');
          else  $areArchitecture = 'no';
          
          $i = 0;
          $user_id = Auth::user()->id;
          $evaluation_string = "";
          $evaluation_names = array(
           "Vertical-Horizontal", 
           "Opaca-Translúcida", 
           "Assimétrica-Simétrica", 
           "Simples-Complexa", 
           "Externa-Interna", 
           "Fechada-Aberta"
          );

          // Pegar do banco as possives métricas
          $binomials = Binomial::all();
          // Fazer um loop por cada e salvar como uma avaliação
          if ($evaluations->isEmpty()) {
              $insertion_edition = "Inseriu";
              foreach ($binomials as $binomial) {
                $bid = $binomial->id;
                $newEvaluation = Evaluation::create([
                  'photo_id'=> $id,
                  'evaluationPosition'=> $input['value-'.$bid],
                  'binomial_id'=> $bid,
                  'user_id'=> $user_id,
                  'knownArchitecture'=>$knownArchitecture,
                  'areArchitecture'=>$areArchitecture
                  ]);
                $evaluation_string = $evaluation_string . $evaluation_names[$i++] . ": " . $input['value-'.$bid] . ", ";
              }
              /* News feed */
              $user = Auth::user();
              foreach ($user->followers as $users) {
                foreach ($users->news as $news) {
                  if ($news->news_type == 'evaluated_photo' && $news->object_id == $id) {
                      $last_news = $news;
                      $primary = 'evaluated_photo';
                  }else if ($news->news_type == 'liked_photo' || $news->news_type == 'commented_photo') {
                      if ($news->object_id == $id) {
                          $last_news = $news;
                          $primary = 'other';
                      }else {
                        $comment = Comment::find($news->object_id);
                        if(!is_null($comment)) {
                          if ($comment->photo_id == $id) {
                                $last_news = $news;
                                $primary = 'other';
                           }
                        }
                      }
                  }
                }  //End for of news
                if (isset($last_news)) {
                    $last_update = $last_news->updated_at;
                    if($last_update->diffInDays(Carbon::now('America/Sao_Paulo')) < 7) {
                        if ($news->sender_id == $user->id) {
                            $already_sent = true;
                        }else if ($news->data != null) {
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
                                $last_news->secondary_type = 'evaluated_photo';
                            }else if ($last_news->tertiary_type == null) {
                                $last_news->tertiary_type = 'evaluated_photo';
                            }
                            $last_news->save();
                        }
                    }else {
                          News::create(array('object_type' => 'Photo', 
                            'object_id' => $id, 
                            'user_id' => $users->id, 
                            'sender_id' => $user->id, 
                            'news_type' => 'evaluated_photo'));
                    }  
                }else {
                      News::create(array('object_type' => 'Photo', 
                          'object_id' => $id, 
                          'user_id' => $users->id, 
                          'sender_id' => $user->id, 
                          'news_type' => 'evaluated_photo'));
                }
              }  //End For Followers
          }else { 
              $insertion_edition = "Editou";
              foreach ($evaluations as $evaluation) {
                  $bid = $evaluation->binomial_id;
                  $evaluation->evaluationPosition = $input['value-'.$bid];
                  $evaluation->knownArchitecture = $knownArchitecture;
                  $evaluation->areArchitecture = $areArchitecture;
                  $evaluation->save();
                  $evaluation_string = $evaluation_string . $evaluation_names[$i++] . ": " . $input['value-'.$bid] . ", ";
              }
          } //end if evaluation empty
          $user_id = Auth::user()->id;
          $source_page = Request::header('referer');
          ActionUser::printEvaluation($user_id, $id, $source_page, "user", $insertion_edition, $evaluation_string);
          return Redirect::to("/evaluations/{$id}/evaluate")->with('message', 
              '<strong>Avaliação salva com sucesso</strong><br>Abaixo você pode visualizar a média atual de avaliações');
      } else { // avaliação sem login        
          return Redirect::to("/photos/{$id}")->with('message', 
            '<strong>Erro na avaliação</strong><br>Faça login para poder avaliar');
      }//End if check
  }





/**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return Response
   */
  public function edit($id)
  {
    //
  }


  /**
   * Update the specified resource in storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function update($id)
  {
    //
  }


  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return Response
   */
  public function destroy($id)
  {
    //
  }

}

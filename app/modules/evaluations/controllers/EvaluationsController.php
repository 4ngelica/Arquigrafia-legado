<?php
namespace modules\evaluations\controllers;
use modules\evaluations\models\Evaluation;
use modules\evaluations\models\Binomial;
use modules\news\models\News as News;
use lib\utils\ActionUser;
use Session;  
use Auth;
use Photo;
use Carbon\Carbon;
use View;
use Input;
use User;
use Request;

class EvaluationsController extends \BaseController {


	public function index()
	{ 
    $evaluation = Evaluation::all();
    return $evaluation;
	}


	public function show($id)
	{ 
      return \Redirect::to('/');
	}

  public function evaluate($photoId ) 
  {
      if (Session::has('institutionId') ) {
        return \Redirect::to('/');
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




   public function viewEvaluation($photoId, $userId) 
   { 
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
        $evaluations =  Evaluation::where("user_id",$user->id)
                                  ->where("photo_id", $photo->id)
                                  ->orderBy("binomial_id", "asc")->get();

        $checkedKnowArchitecture= Evaluation::userKnowsArchitecture($photoId,$userId);
        $checkedAreArchitecture= Evaluation::userAreArchitecture($photoId,$userId);
     }    
     
      return View::make('evaluate',
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

  
   /** saveEvaluation($id) */
  public function store($id)
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
          return \Redirect::to("/evaluations/{$id}/evaluate")->with('message', 
              '<strong>Avaliação salva com sucesso</strong><br>Abaixo você pode visualizar a média atual de avaliações');
      } else { // avaliação sem login        
          return \Redirect::to("/photos/{$id}")->with('message', 
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

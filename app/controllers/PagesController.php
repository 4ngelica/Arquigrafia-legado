<?php

use lib\utils\ActionUser;
use lib\date\Date;

class PagesController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Page Controller
	|--------------------------------------------------------------------------
	*/

  protected $date;

  public function __construct(Date $date = null)
  {
    $this->date = $date ?: new Date; 
  }

	public function home()
	{
    $photos = Photo::orderByRaw("RAND()")->take(1000)->get();

    if (Auth::check()) {
      $user_id = Auth::user()->id;
      $user_or_visitor = "user";
    }
    else { 
      $user_or_visitor = "visitor";
      session_start();
      $user_id = session_id();
    }
    if(Session::has('institutionId')){
      $institution = Institution::find(Session::get('institutionId')); 
    }else{
      $institution = null;
    }
    $source_page = Request::header('referer');
    ActionUser::printHomePage($user_id, $source_page, $user_or_visitor);

		return View::make('index', ['photos' => $photos]);
	}
  
  public function panel()
	{
    $photos = Photo::orderByRaw("RAND()")->take(1000)->get();
		return View::make('api.panel', ['photos' => $photos]);
	}

  private static function userPhotosSearch($needle) {
    $query = User::where('id', '>', 0);
    $query->where('name', 'LIKE', '%'. $needle .'%');
    $userList = $query->get();    
    return $userList->lists('id');
  }
   //2015-05-09 msy end
  private static function streetAndCitySearch(&$needle,&$txtcity) {
        Log::info("Logging info txtcity <".$txtcity.">");       

        $allowed = "/[^a-z\\.\/\sçáéíóúãàõ]/i";
        $txtstreet=  preg_replace($allowed,"",$needle);
        $txtstreet = rtrim($txtstreet);      
        $needle = $txtstreet;        
                  
        $query = Photo::orderByRaw("RAND()");         
        $query->where('city', 'LIKE', '%' . $txtcity . '%');
        $query->where('street', 'LIKE', '%' . $txtstreet . '%');
        $query->whereNull('deleted_at');
        $photos = $query->get(); 
        return $photos;  
  }
//msy
  private static function dateSearch(&$needle,&$type){

      if($type=='work'){
        Log::info("Logging information of work date<".$needle.">"); 
         $dateType = 'workdate';

      }elseif ($type=='img') {
        Log::info("Logging information of image date<".$needle.">"); 
         $dateType = 'dataCriacao';

      }elseif ($type=='up') {
        Log::info("Logging information for upload <".$needle.">");
        $dateType = 'dataUpload';
        $date = new DateTime($needle);
        $needle =  $date->format('Y-m-d');  
       Log::info("Logging information for format upload <".$needle.">");
      }
        $query = Photo::orderByRaw("RAND()");         
        $query->where($dateType, 'LIKE', '%' . $needle . '%');
        $query->whereNull('deleted_at');
        $photos = $query->get(); 
        return $photos;   
  }
  //msy
  public static function yearSearch(&$needle,&$dateFilter,&$date){
        
      //  $dateFilter = array('dataCriacao','dataUpload','workdate');
      
        $dateFilter = [
            'di'=>'Data da Imagem',
            'du'=>'Data de Upload',
            'do'=>'Data da Obra'
        ];


        if(!empty($date) ){  //&& !isset($date)
          
            if($date == 'di' ) $dateType = 'dataCriacao';          
            if ($date == 'du' ) $dateType = 'dataUpload';
            if ($date == 'do' ) {$dateType = 'workdate'; }                   
           
            $query = Photo::orderByRaw("RAND()");         
            $query->where($dateType, 'LIKE', '%' . $needle . '%');
            $query->whereNull('deleted_at');
            $photos = $query->get(); 
            return $photos; 

        }else{
          $query = Photo::orderByRaw("RAND()");         
          $query->where('dataCriacao', 'LIKE', '%' . $needle . '%');
          $query->orWhere('dataUpload', 'LIKE', '%' . $needle . '%');
          $query->orWhere('workdate', 'LIKE', '%' . $needle . '%');
          $query->whereNull('deleted_at');
          $photos = $query->get(); 
          return $photos; 
        }

        
  }

  public function searchBinomial($binomial_id, $option, $value = null) {
    $bin = Binomial::find($binomial_id);
    $bi_opt = $option == 1 ? $bin->firstOption : $bin->secondOption;
    $photos = Evaluation::getPhotosByBinomial($bin, $option, $value);
    $value = $option == 1 ? 100 - $value : $value;
    return View::make('/search',
      [
        'tags' => [], 'photos' => $photos, 'query' => '',
        'city' => '', 'dateFilter' => [], 'binomial_option' => $bi_opt,
        'value' => $value
      ]);
  }
	
	public function search()
	{
    if ( Input::has('bin') ) {
      return $this->searchBinomial(
          Input::get('bin'), Input::get('opt'), Input::get('val')
        );
    }
    //2015-05-06 msy begin, add param city
    $needle = Input::get("q");
    $txtcity = Input::get("city"); 
    $type = Input::get("t"); 
    $dateFilter = null;
    $date = Input::get("d"); 

		if ($needle != "") {
      
      $query = Tag::where('name', 'LIKE', '%' . $needle . '%');  
      $tags = $query->get();

      if ($txtcity != "") {  
            //2015-05-09 msy end
            $photos = static::streetAndCitySearch($needle,$txtcity);        
                
       }elseif ((DateTime::createFromFormat('Y-m-d', $needle) !== FALSE || DateTime::createFromFormat('Y-m-d H:i:s', $needle) !== FALSE )&& !empty($type)) {
         $photos = static::dateSearch($needle,$type);

       }elseif (DateTime::createFromFormat('Y', $needle) !== FALSE) {

            $photos = static::yearSearch($needle,$dateFilter,$date);      

       } else {         

          $idUserList = static::userPhotosSearch($needle);
                               
          $query = Photo::where(function($query) use($needle, $idUserList) {
            $query->where('name', 'LIKE', '%'. $needle .'%');  
            $query->orWhere('description', 'LIKE', '%'. $needle .'%');  
            $query->orWhere('imageAuthor', 'LIKE', '%' . $needle . '%');
            $query->orWhere('workAuthor', 'LIKE', '%'. $needle .'%');
            $query->orWhere('country', 'LIKE', '%'. $needle .'%');  
            $query->orWhere('state', 'LIKE', '%'. $needle .'%'); 
            $query->orWhere('city', 'LIKE', '%'. $needle .'%'); 
            if ($idUserList != null && !empty($idUserList)) {
              $query->orWhereIn('user_id', $idUserList);}
          })->orderBy('created_at', 'desc');
          $photos = $query->get();
       } 
      //2015-05-06 msy end
      
      // se houver uma tag exatamente como a busca, pegar todas as fotos dessa tag e juntar no painel
      //$tag = Tag::where('name', '=', $needle)->get();
      $query = Tag::where('name', '=', $needle);  
      $tag = $query->get();

      if ($tag->first()) {
        $byTag = $tag->first()->photos;
        $photos = $photos->merge($byTag);
      }

      if (Auth::check()) {
        $user_id = Auth::user()->id;
        $user_or_visitor = "user";
      }
      else { 
        $user_or_visitor = "visitor";
        session_start();
        $user_id = session_id();
      }
      $source_page = Request::header('referer');
      ActionUser::printSearch($user_id, $source_page, $needle, $user_or_visitor);

      // retorna resultado da busca
      return View::make('/search',['tags' => $tags, 'photos' => $photos, 'query'=>$needle, 'city'=>$txtcity,'dateFilter'=>$dateFilter]);
    } else { 
      // busca vazia
      return View::make('/search',['tags' => [], 'photos' => [], 'query' => "", 'city'=>"",'dateFilter'=>[]]);
    }
	}  

  private static function searchTags($t) {

    $query = Tag::where('name','=', $t);    
    $tagList = $query->get();    
    return $tagList->lists('id');
  }
  
  public function advancedSearch()
	{ 


    $fields = array(
        'name',
        'description',
        'city',
        'state',
        'country',
        'workAuthor',
        'imageAuthor',
        'dataCriacao',
        'dataUpload',
        'workdate',
        'district',
        'street',
        'tags',
        'allowCommercialUses',
        'allowModifications'

    );

    
    foreach($fields as $field) $$field = trim(Input::get($field));
    
    if(empty($name) && empty($description) && empty($city) && empty($state) && empty($country) && empty($workAuthor) 
      && empty($imageAuthor) && empty($dataCriacao) && empty($dataUpload) && empty($workdate) && empty($district)
      && empty($street)&& empty($tags) && empty($allowModifications) && empty($allowCommercialUses)) {
       // busca vazia
       return View::make('/advanced-search',['tags' => [], 'photos' => [], 'query' => ""]);
    } else {
      
      

      $query = Photo::where('id', '>', 0);       
      if ($name != '') $query->where('name', 'LIKE', '%'. $name .'%');
      if ($description != '') $query->where('description', 'LIKE', '%'. $description .'%');  
      if ($city != '') $query->where('city', 'LIKE', '%'. $city .'%');  
      if ($state != '') $query->where('state', 'LIKE', '%'. $state .'%'); 
      if ($country != '') $query->where('country', 'LIKE', '%'. $country .'%'); 
      if ($workAuthor  != '') $query->where('workAuthor', 'LIKE', '%'. $workAuthor .'%');  
      if ($imageAuthor  != '') $query->where('imageAuthor', 'LIKE', '%'. $imageAuthor .'%');
      if ($district  != '') $query->where('district', 'LIKE', '%'. $district .'%');
      if ($street  != '') $query->where('street', 'LIKE', '%'. $street .'%');
      if ($allowModifications  != '') $query->where('allowModifications', '=', $allowModifications);
      if ($allowCommercialUses  != '') $query->where('allowCommercialUses', '=', $allowCommercialUses);

       if ($workdate != ''){ 
          if(DateTime::createFromFormat('Y', $workdate)!== FALSE) {
            $query->where('workdate', 'LIKE', '%' . $workdate . '%');
          }else{
            $query->where('workdate', 'LIKE', '%' . $this->date->formatDate($workdate) . '%');
          }
       }
       if ($dataCriacao != ''){ 
          if(DateTime::createFromFormat('Y', $dataCriacao)!== FALSE) {
            $query->where('dataCriacao', 'LIKE', '%' . $dataCriacao . '%');
          }else{
            $query->where('dataCriacao', 'LIKE', '%' . $this->date->formatDate($dataCriacao) . '%');
          }
       }
       if ($dataUpload != ''){ 
          if(DateTime::createFromFormat('Y', $dataUpload)!== FALSE) {
            $query->where('dataUpload', 'LIKE', '%' . $dataUpload . '%');
          }else{
            $query->where('dataUpload', 'LIKE', '%' . $this->date->formatDate($dataUpload) . '%');
          }
       }
       
      $query->whereNull('deleted_at'); 
      $photos = $query->get();
      
      //Adding search by tags

        if (Input::has('tags')) 
           $tags = str_replace(array('\'', '"', '[', ']'), '', $tags);    
        else
           $tags= '';  

        
        if (!empty($tags)) { 

          $tags_copy = $tags;
          $tags = explode(',', $tags); 

          $tags = array_map('trim', $tags);
          $tags = array_map('mb_strtolower', $tags);
          $tags = array_unique($tags);
          $tagString = implode(",", $tags);
          
          

          $query = Tag::whereIn('name', $tags);
          $tagsResult = $query->get();  

          //commnet
          $arrayPhotoId = array();
          foreach ($tagsResult as $tagResult) {
            $photosRelated = $tagResult->photos;
            foreach ($photosRelated as $photoRel) {
              array_push($arrayPhotoId, $photoRel->id);  
            }
            
          }
          //dd($arrayTemp);

          $photos = $photos->filter(
            function($photo) use ($arrayPhotoId){
              if(in_array($photo->id, $arrayPhotoId)){
                return true;
              }
            });
            
        }
    } //2015-05-21 msy end
    //dd($photos->count());
    if($tags == '') $tags = [];
    if($photos->count()) { // retorna resultado da busca       
      return View::make('/advanced-search',['tags' => $tags, 'photos' => $photos]); //tagsResult
    } else {
      // busca sem resultados
      return View::make('/advanced-search',['tags' => $tags, 'photos' => []]); //tagsResult
    }
    
	}

}

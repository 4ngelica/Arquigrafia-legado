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
        if(Session::has('last_search'))
            Session::forget('last_search');

        if(Session::has('last_advanced_search'))
            Session::forget('last_advanced_search');

        $photos = Photo::orderByRaw("RAND()")->take(350)->get();

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

        return View::make('index', ['photos' => $photos, 'institution' => $institution]);
    }

    public function panel()
    { 
        $photos = Photo::orderByRaw("RAND()")->take(350)->get();
        return View::make('api.panel', ['photos' => $photos]);
    }

    private static function userPhotosSearch($needle) {
        $query = User::where('id', '>', 0);
        $query->where('name', 'LIKE', '%'. $needle .'%');
        $userList = $query->get();    
        return $userList->lists('id');
    }
    
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
    
    public static function yearSearch(&$needle,&$dateFilter,&$date){

        

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
        
        
        $needle = Input::get("q");
        $txtcity = Input::get("city"); 
        $type = Input::get("t"); 
        $dateFilter = null;
        $date = Input::get("d"); 
        $authorFilter = null;

        $url= null;
        $maxPage = 0;
        $photosTotal = 0;        
        $photosPages = null;
        $photosAll = 0;
        
        if($needle == ""){
            Session::forget('last_search');
        } 
       
        if ( Input::has('type') ) {
                $authorFilter= Input::get('type');
        }

        if ($needle != "") { 
            $tags = null;
            $allAuthors =  null;
            $query = Tag::where('name', 'LIKE', '%' . $needle . '%')->where('count', '>', 0);  
            $tags = $query->get();

            
                $allAuthors = DB::table('authors')
                ->join('photo_author', function($join) use ($needle)
                {   $join->on('authors.id', '=', 'photo_author.author_id')
                         ->where('name', 'LIKE', '%' . $needle . '%');
                })->groupBy('authors.id')->get();
            
            
                                     
            if ($txtcity != "") {                  
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
                    $query->orWhere('country', 'LIKE', '%'. $needle .'%');  
                    $query->orWhere('state', 'LIKE', '%'. $needle .'%'); 
                    $query->orWhere('city', 'LIKE', '%'. $needle .'%'); 
                    if ($idUserList != null && !empty($idUserList)) {
                        $query->orWhereIn('user_id', $idUserList);}
                })->orderBy('created_at', 'desc');
                $photos =  $query->get(); 
                
            }       
           
            // se houver uma tag exatamente como a busca, pegar todas as fotos dessa tag e juntar no painel
            $query = Tag::where('name', '=', $needle); 
            $tags = $query->get();
             foreach ($tags as $tag) { 
                $byTag = $tag->photos;                
                $photos = $photos->merge($byTag);
             }   

            if($authorFilter != null){             
                $query = Author::where('name', '=', $needle);
                $author = $query->get();
                if ($author->first()) { 
                    $byAuthor = $author->first()->photos;                
                    $photos = $photos->merge($byAuthor);                
                }     
            }else{
                $queryAuthor = Author::where('name', 'LIKE', '%' . $needle . '%'); 
                $authors = $queryAuthor->get();
                foreach ($authors as $author) { 
                    $byAuthor = $author->photos;                
                    $photos = $photos->merge($byAuthor);                
                }    
            }  

            $query = Institution::where('name', '=', $needle); 
            $institution = $query->get();
            if ($institution->first()) {
                $byInstitution = $institution->first()->photos;                
                $photos = $photos->merge($byInstitution);
            }
          
            $photosAll = $photos->count();

            if (Auth::check()) {
                $user_id = Auth::user()->id;
                $user_or_visitor = "user";
            }else { 
                $user_or_visitor = "visitor";
                session_start();
                $user_id = session_id();
            }
            $source_page = Request::header('referer');
            ActionUser::printSearch($user_id, $source_page, $needle, $user_or_visitor);

            Session::put('last_search',
                ['tags' => $tags, 'photos' => $photos, 'query'=>$needle, 'city'=>$txtcity,
                'dateFilter'=>$dateFilter, 'authors' => $allAuthors,
                'url' => $url,'photosTotal' => $photosTotal , 'maxPage' => $maxPage, 'page' => 1,
                'photosAll' => $photosAll ]);
            
            
            if($photos->count() != 0){           
                $photosPages = Photo::paginatePhotosSearch($photos); 
                $photosTotal = $photosPages->getTotal();
                $maxPage = $photosPages->getLastPage();
                Log::info('simpleSearch');
                $url = URL::to('/search'. '/paginate/other/photos/');
            }else{                
                Session::forget('last_search');
            }
            

            return View::make('/search',['tags' => $tags, 'photos' => $photosPages, 
                'query'=>$needle, 'city'=>$txtcity,'dateFilter'=>$dateFilter,
                'authors' => $allAuthors ,'needle' => $needle,'url' => $url,
                'photosTotal' => $photosTotal , 'maxPage' => $maxPage, 'page' => 1,
                'photosAll' => $photosAll ]);
        }else {
            if(Session::has('last_search'))	
                return View::make('/search', Session::get('last_search'));
            else{// busca vazia

                return View::make('/search',['tags' => [], 'photos' => [], 'query' => "", 'city'=>"",
                    'dateFilter'=>[], 'authors' =>[], 
                    'url'=>null,'photosTotal'=> 1,'maxPage' => 1, 'page' => 1, 'photosAll' => 0 ]);
            }
        }
    }  

    private static function searchTags($t) {
        $query = Tag::where('name','=', $t);    
        $tagList = $query->get();    
        return $tagList->lists('id');
    }

    public function advancedSearch()
    { 
        $fields = Input::only( array(
            'name', 'description', 'city', 'state', 'country', 
            'imageAuthor', 'dataCriacao', 'dataUpload', 'workdate', 'district',
            'street', 'tags', 'allowCommercialUses', 'allowModifications','workAuthor_area'
        ));
        $fields = array_filter(array_map('trim', $fields));

        $url= null;
        $maxPage = 0;
        $photosTotal = 0;        
        $photosPages = null;

        if( count($fields) == 0 ) { // busca vazia 
        
            if(Session::has('last_advanced_search'))
                return View::make('/advanced-search', Session::get('last_advanced_search'));
            else {  // busca vazia
                return View::make('/advanced-search',
                    ['tags' => [], 'photos' => [], 'query' => "", 'binomials' => Binomial::all(),'authorsArea' => [],
                    'url'=> null,'photosTotal'=> 1,'maxPage' => 1, 'page' => 1 ]);
            }
        }
        $binomials = array();
        if ( Input::has('binomial_check') ) {
            foreach (Binomial::all() as $binomial) {
                if ( Input::has('value-' . $binomial->id) ) {
                    $binomials[$binomial->id] = Input::get('value-' . $binomial->id);
                }
            }
        }
        
        //Adding search by tags
        $tags = str_replace(array('\'', '"', '[', ']'), '', $fields['tags']);
        $tags = Tag::transform($tags);

        $authorsArea = str_replace(array('","'), '";"', $fields['workAuthor_area']);    
        $authorsArea = str_replace(array('\'', '"', '[', ']'), '', $authorsArea);
        $authorsArea = Author::transform($authorsArea);

        $photos = Photo::search(array_except($fields, 'tags'), $tags, $binomials,$authorsArea);
        //dd($fields); 

        if( ($count_photos = $photos->count()) == 0 ) {
            $message = 'A busca não retornou resultados.';
        } elseif ( $count_photos == 1 ) {
            $message = 'Verifique abaixo a ' . $count_photos . ' imagem encontrada.';
        } else {
            $message = 'Verifique abaixo as ' . $count_photos . ' imagens encontradas.';
        }
        $tags = $tags == '' ? [] : $tags;
        $photos = $photos->count() == 0 ? [] : $photos;

        Session::put('last_advanced_search', ['tags' => $tags, 'photos' => $photos,
            'binomials' => Binomial::all(), 'authorsArea' => $authorsArea, 'message' => $message,
            'url' => $url,'photosTotal' => $photosTotal , 'maxPage' => $maxPage, 'page' => 1
            ]);

        $photosPages = Photo::paginatePhotosSearchAdvance($photos); 
        if($photosPages != null){
            $photosTotal = $photosPages->getTotal();
            $maxPage = $photosPages->getLastPage();
        } else {
            $photosTotal = 0;
            $maxPage = 0;
        }
        

        $url = URL::to('/search/more'. '/paginate/other/photos/');

        return View::make('/advanced-search',
            ['tags' => $tags, 'photos' => $photosPages, //'photos' => $photos,
            'binomials' => Binomial::all(), 'authorsArea' => $authorsArea, 'message' => $message,
            'url' => $url,'photosTotal' => $photosTotal , 'maxPage' => $maxPage, 'page' => 1 ]); 
    }

    public function paginatePhotosResult() {
       if(Session::has('last_search')){
            $lastSearch = Session::get('last_search');
            $photos = $lastSearch['photos'];
        }
            
        $query = Input::has('q') ? Input::get('q') : ''; 
        
        $pagination = Photo::paginateAllPhotosSearch($photos,$query);
        return $this->paginationResponseSearch($pagination, 'add');
    }

    public function paginatePhotosResultAdvance() {
       if(Session::has('last_advanced_search')){
            $lastSearchAdvance = Session::get('last_advanced_search');
            $photos = $lastSearchAdvance['photos'];
        }
            
        $query = Input::has('q') ? Input::get('q') : ''; 
        
        $pagination = Photo::paginateAllPhotosSearchAdvance($photos,$query);
        return $this->paginationResponseSearch($pagination, 'add');
    }

    private function paginationResponseSearch($photos, $type) {
        $count = $photos->getTotal();
        $page = $photos->getCurrentPage();
        $response = [];
        $response['content'] = View::make('photos.includes.searchResult_include')
            ->with(['photos' => $photos, 'page' => $page, 'type' => $type])
            ->render();
        $response['maxPage'] = $photos->getLastPage();
        $response['empty'] = ($photos->count() == 0);
        $response['count'] = $count;
        return Response::json($response);
    }

    

    
}

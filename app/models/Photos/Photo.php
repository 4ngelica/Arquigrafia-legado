<?php

use modules\draft\traits\DraftingTrait;
use lib\date\Date;
use Illuminate\Database\Eloquent\SoftDeletingTrait;

use modules\gamification\traits\LikableGamificationTrait;
use Illuminate\Database\Eloquent\Collection as Collection;
use modules\institutions\models\Institution;
use modules\collaborative\models\Like as Like;
use modules\evaluations\models\Evaluation as Evaluation;

class Photo extends Eloquent {

	use DraftingTrait;
	use SoftDeletingTrait;
	use LikableGamificationTrait;

	protected $softDelete = true;

	protected $dates = ['deleted_at'];

	protected $fillable = [
		'aditionalImageComments',
		'allowCommercialUses',
		'allowModifications',
		'cataloguingTime',
		'characterization',
		'city',
		'collection',
		'country',
		'dataCriacao',
		'dataUpload',
		'description',
		'district',
		'imageAuthor',
		'name',
		'nome_arquivo',
		'state',
		'street',
		'tombo',
		'user_id',
		'workAuthor',
		'workdate',
	];

	static $allowModificationsList = [
		'YES' => ['Sim', ''],
		'YES_SA' => ['Sim, contanto que os outros compartilhem de forma semelhante', '-sa'],
		'NO' => ['Não', '-nd']
	];

	static $allowCommercialUsesList = [
		'YES' => ['Sim', ''],
		'NO' => ['Não', '-nc']
	];

	private static	$information_questions = [
		'city' => 'Deseja adicionar a cidade da obra?',
		'country' => 'Deseja adicionar o país da obra?',
		'dataCriacao' => 'Qual é a data desta imagem?',
		'description' => 'Deseja adicionar uma descrição para a imagem?',
		'district' => 'Qual é o bairro da obra?',
		'imageAuthor' => 'Quem é o autor desta imagem?',
		'name' => 'Qual é o nome desta obra?',
		'state' => 'Qual é o Estado desta arquitetura?',
		'street' => 'Qual é a rua desta obra?',
		'workAuthor' => 'Quem é o autor da obra?',
		'workdate' => 'Quando foi construída a obra?'
	];

	protected $date;

	public function __construct($attributes = array(), Date $date = null) {
		parent::__construct($attributes);
		$this->date = $date ?: new Date;
	}

	public function user()
	{
		return $this->belongsTo('User');
	}

	public function institution()
	{
		return $this->belongsTo('modules\institutions\models\Institution');
	}

	public function tags()
	{
		return $this->belongsToMany('modules\collaborative\models\Tag', 'tag_assignments');
	}

	public function authors()
	{
		return $this->belongsToMany('Author', 'photo_author');
	}

	public function comments()
	{
		return $this->hasMany('modules\collaborative\models\Comment');
	}

	public function albums()
	{
		return $this->belongsToMany('Album', 'album_elements');
	}

	public function evaluations()
	{
		return $this->hasMany('modules\evaluations\models\Evaluation');
	}

	public function evaluators()
	{
		return $this->belongsToMany('User', 'binomial_evaluation');
	}

	public function saveMetadata($originalFileExtension, $metadata)
	{
		$original_path = storage_path() . '/original-images/';
		$original_path .= $this->id . '_original.' . $originalFileExtension;
		$view_path = public_path() . '/arquigrafia-images/' . $this->id . '_view.jpg';
		$h200_path = public_path() . '/arquigrafia-images/' . $this->id . '_200h.jpg';
		$home_path = public_path() . '/arquigrafia-images/' . $this->id . '_home.jpg';
		$micro_path = public_path() . '/arquigrafia-images/' . $this->id . '_micro.jpg';
		Exiv2::saveMetadata($original_path, $this, $metadata);
		Exiv2::saveMetadata($view_path, $this, $metadata);
		Exiv2::saveMetadata($h200_path, $this, $metadata);
		Exiv2::saveMetadata($home_path, $this, $metadata);
		Exiv2::saveMetadata($micro_path, $this, $metadata);
	}

	public static function paginateUserPhotos($user, $perPage = 24) {
		return static::withUser($user)
			->withoutInstitutions()->paginate($perPage);
	}

	public static function paginateInstitutionPhotos($institution, $perPage = 24) {
		return static::withInstitution($institution)->paginate($perPage);
	}

	public static function paginateAlbumPhotos($album, $perPage = 24) {
		return $album->photos()->paginate($perPage);
	}

	public static function paginateOtherPhotos($user, $photos, $perPage = 24) {
		if ( Session::has('institutionId') ) {
			return static::withInstitution($user)->except($photos)->paginate($perPage);
		} else {
			return static::withUser($user)->withoutInstitutions()
				->except($photos)->paginate($perPage);
		}
	}

	public static function paginateUserPhotosNotInAlbum($user, $album, $q = null, $perPage = 24) {

		return static::notInAlbum($album, $q)->withUser($user)
			->withoutInstitutions()->paginate($perPage);

		
	}

	public static function paginateInstitutionPhotosNotInAlbum($inst, $album, $q = null, $perPage = 24) {
		return static::notInAlbum($album, $q)
			->withInstitution($inst)->paginate($perPage);
	}

	public static function paginateAllPhotosNotInAlbum($album, $q = null, $perPage = 24) {
		return static::notInAlbum($album, $q)->paginate($perPage);
	}

	public static function paginateFromAlbumWithQuery($album, $q, $perPage = 24) {
		return static::inAlbum($album, $q)->paginate($perPage);
	}

	public static function composeArchitectureName($name) {
		$array = explode(" ", $name);
		$architectureName = "";
		if (!is_null($array) && !is_null($array[0])) {
			if (ends_with($array[0], 'a') || ends_with($array[0], 'dade')
				|| ends_with($array[0], 'ção') || ends_with($array[0], 'ase')
				|| ends_with($array[0], 'ede') || ends_with($array[0], 'dral')
				|| ends_with($array[0], 'agem') || $array[0] == "fonte"
				|| $array[0] == "Fonte" || $array[0] == "ponte"
				|| $array[0] == "Ponte")
				$architectureName = 'a ';
			else if (ends_with($array[0], 's'))
				$architectureName = 'a arquitetura de ';
			else
				$architectureName = 'o ';
		}
		return $architectureName = $architectureName .$name;
	}

	public static function getEvaluatedPhotosByUser($user) {
		$evaluations = Evaluation::where("user_id", $user->id)->groupBy('photo_id')->distinct()->get();
		return Photo::whereIn('id', $evaluations->lists('photo_id'))->get();
	}


	public static function getLastUpdatePhotoByUser($user_id) {		
		return $dataUpdate = Photo::where("user_id", $user_id)->orderBy('updated_at','desc')->first();
	}
	public static function getLastUploadPhotoByUser($user_id) {
		return Photo::where("user_id", $user_id)->orderBy('dataUpload','desc')->first();
	}

	public static function photosWithSimilarEvaluation($average,$idPhotoSelected) {
		Log::info("Logging function Similar evaluation");
		$similarPhotos = array();
		$arrayPhotosId = array();
		$arrayPhotosDB = array();
		$i=0;

		if (!empty($average)) {
			foreach ($average as $avg) {
				Log::info("Logging params ".$avg->binomial_id." ".$avg->avgPosition);
				//average of photo by each binomial(media de fotos x binomio)
				$avgPhotosBinomials = DB::table('binomial_evaluation')
				->select('photo_id', DB::raw('avg(evaluationPosition) as avgPosition'))
				->where('binomial_id', $avg->binomial_id)
				->where('photo_id','<>' ,$idPhotoSelected)
				->groupBy('photo_id')->get();

				//clean array for news id photo
				$arrayPhotosId = array();
				$flag=false;
				
				foreach ($avgPhotosBinomials as $avgPhotoBinomial) {
				
					if(abs($avgPhotoBinomial->avgPosition - $avg->avgPosition)<=25){
						$flag=true;
						array_push($arrayPhotosId,$avgPhotoBinomial->photo_id);
					}
				}

				if($flag == false){
					Log::info("Logging break ");
					$similarPhotos = array();
					break;
				}

				if($i==0){
					$similarPhotos = $arrayPhotosId;
				}
				
			$similarPhotos = array_intersect($similarPhotos, $arrayPhotosId);

			$i++;

			}
			//To remove repeted values
			$similarPhotos = array_unique($similarPhotos);

			//To obtain name of similarPhotos
			foreach ($similarPhotos as $similarPhotosId ) {
				//echo $similarPhotosId;
				//echo "<br>";
				$photoObj = Photo::where('id',$similarPhotosId)->whereNull('deleted_at')->first();
				
				if(!empty($photoObj) && !is_null($photoObj)){
					$similarPhotosDB = DB::table('photos')
					->select('id', 'name')
					->where('id',$photoObj->id)				
					->get();
					array_push($arrayPhotosDB,$similarPhotosDB[0]);
				} 			
			}
		}


			return $arrayPhotosDB;
	}

	public static function licensePhoto($photo){
		$license = array();
		if($photo->allowCommercialUses == 'YES'){
			$textAllowCommercial = 'Permite o uso comercial da imagem ';
			if($photo->allowModifications == 'YES'){
				 $license[0] ='by';
				 $license[1] = $textAllowCommercial.'e permite modificações na imagem.';
			}elseif ($photo->allowModifications == 'NO') {
				 $license[0] ='by-nd';
				 $license[1] = $textAllowCommercial.'mas NÃO permite modificações na imagem.';
			}else {
				 $license[0] = 'by-sa';
				 $license[1] = $textAllowCommercial.'e permite modificações contato que os outros compartilhem de forma semelhante.';
			}
		}else{
			$textAllowCommercial = 'NÃO permite o uso comercial da imagem ';
			if($photo->allowModifications == 'YES'){
				$license[0] ='by-nc';
				$license[1] =$textAllowCommercial.'mas permite modificações na imagem.';
			}elseif ($photo->allowModifications == 'NO') {
				$license[0] = 'by-nc-nd';
				$license[1] = $textAllowCommercial.'e NÃO permite modificações na imagem.';
			}else {
				$license[0] = 'by-nc-sa';
				$license[1] = $textAllowCommercial.'mas permite modificações contato que os outros compartilhem de forma semelhante.';
			}
		}

		return $license;

	}

	public function scopeWithoutInstitutions($query) {
		return $query->whereNull('institution_id');
	}

	public function scopeWithInstitution($query, $institution) {
		$id = $institution instanceof Institution ? $institution->id : $institution;
		return $query->where('institution_id', $id);
	}

	public function scopeWithUser($query, $user) {
		$id = $user instanceof User ? $user->id : $user;
		return $query->where('user_id', $id);
	}

	public function scopeExcept($query, $photos) {
		if ($photos instanceof Photo) {
			return $query->where('id', '!=', $photos->id);
		}
		//instance of Eloquent\Collection
		return $query->whereNotIn('id', $photos->modelKeys());
	}

	public function scopeNotInAlbum($query, $album, $q = null) {
		return $query->whereDoesntHave('albums', function($query) use($album) {
			$query->where('album_id', $album->id);
		})->whereMatches($q);
	}

	public function scopeInAlbum($query, $album, $q = null) {
		return $query->whereHas('albums', function($query) use($album) {
			$query->where('album_id', $album->id);
		})->whereMatches($q);	
	}

	public function scopeWhereMatches($query, $needle) {
		if ( empty($needle) ) {
			return $query;
		}
		return $query->where( function($q) use($needle) {
			$q->withTag($needle)->orWhere( function ($q) use($needle) {
				$q->withAttributes($needle);
			});
		});
	}

	public function scopeWithTag($query, $needle) {
		return $query->whereHas('tags', function($q) use($needle) {
			$q->where('name', 'LIKE', '%' . $needle . '%');
		});
	}

	public function scopeWithAttributes($query, $needle) {
		return $query->where('name', 'LIKE', '%'. $needle .'%')
			->orWhere('description', 'LIKE', '%'. $needle .'%')
			->orWhere('imageAuthor', 'LIKE', '%' . $needle . '%')
			->orWhere('country', 'LIKE', '%'. $needle .'%')
			->orWhere('state', 'LIKE', '%'. $needle .'%')
			->orWhere('city', 'LIKE', '%'. $needle .'%');
	}

	public function scopeWithBinomials($query, $binomials) {
		foreach($binomials as $binomial => $avg) {
			$query->whereIn('photos.id', function ($sub_query) use ($binomial, $avg) { 
				$sub_query->select('photo_id')->from('binomial_evaluation')
					->whereRaw('binomial_id = ' . $binomial)
					->groupBy('photo_id')
					->havingRaw('avg(evaluationPosition) >= ' . ($avg - 5))
					->havingRaw('avg(evaluationPosition) <= ' . ($avg + 5));
			});
		}
		return $query;
	}

	public function scopeWithTags($query, $tags) {
		if ( ! empty($tags) ) { 
			$query->whereHas('tags', function($sub_query) use ($tags) {
				$sub_query->whereIn('name', $tags);
			});
		}
		return $query;
	}

	public function scopeWithTagsVarious($query, $tags) {
		if(!empty($tags)) { 
				
				$query->join('tag_assignments','tag_assignments.photo_id','=','photos.id');
				$query->join('tags','tags.id','=','tag_assignments.tag_id');
				$query->where(function($sub_query) use ($tags) {
					foreach ($tags as $tag) {
						$sub_query->orWhere('tags.name', '=', $tag);						
					}
				});	
		}
		return $query;
	}

	public function scopeWithAuthors($query, $authors) {
		if ( ! empty($authors) ) {
			$query->whereHas('authors', function($sub_query) use ($authors) {
				$sub_query->whereIn('name', $authors);
				
				
			});
		}
		return $query;
	}
	public function scopeWithAuthorsVarious($query, $authors) {

			if(!empty($authors)) { 
				
				$query->join('photo_author','photo_author.photo_id','=','photos.id');
				$query->join('authors','authors.id','=','photo_author.author_id');
				$query->where(function($sub_query) use ($authors) {
					foreach ($authors as $author) {
						$sub_query->orWhere('authors.name', 'LIKE', '%' .  $author. '%');
					}
				});	
			}
			
		return $query;
	}

	public function getDataUploadAttribute($value) {
		return $this->date->formatDatePortugues($this->attributes['dataUpload']);
	}



	public function getTranslatedDataCriacaoAttribute($raw_date) {
		return $this->date->translate($this->attributes['dataCriacao']);
	}



	public function getFormatWorkdateAttribute($dateWork,$type) {		
		return  $this->date->formatToWorkDate($dateWork,$type);
	}

	public function getFormatDataCriacaoAttribute($dataCriacao,$type) {		
		return  $this->date->formatToDataCriacao($dataCriacao,$type);
	}

	public static function import($attributes, $basepath) {
		$tombo = $attributes['tombo'];
		list( $image, $image_extension ) = static::getImage( $basepath, $tombo );
		$image_extension = strtolower($image_extension);
		$attributes['nome_arquivo'] = $tombo . '.' . $image_extension;
		$photo = static::updateOrCreateByTombo( $tombo, $attributes );
		$photo->saveImages( $image, $image_extension );
		$photo->saveMetadata($image_extension);
		return $photo;
	}

	public static function getImage($basepath, $tombo) {
		$image = ImageManager::find( $basepath . '/' . $tombo . '.*' );
		$image_extension = ImageManager::getOriginalImageExtension( $image );
		return array( $image, $image_extension );
	}

	public static function updateOrCreateByTombo($tombo, $newValues) {
		return static::updateOrCreateWithTrashed( array( 'tombo' => $tombo ), $newValues );
	}

	public static function updateOrCreateWithTrashed($attributes, $newValues) {
		$photo = static::withTrashed()->where( $attributes )->first();
		$photo = $photo ?: new static;
		$photo->fill( $newValues );
		if ( $photo->trashed() ) {
			$photo->restore();
		} else {
			if ( ! $photo->exists ) {
				$photo->dataUpload = date('Y-m-d H:i:s');
			}
			$photo->save();
		}
		return $photo;
	}

	public function saveImages($image, $extension = 'jpg') {
		try {
			$prefix = public_path() . '/arquigrafia-images/' . $this->id;
			ImageManager::makeAll( $image, $prefix, $extension );
		} catch (Exception $e) {
			$this->delete();
			throw $e;
		}
	}

	public function syncTags(array $tags) {
		$get_ids = function( $tag ) {
			return $tag instanceof Tag ? $tag->id : $tag;
		};
		$tags = array_map($get_ids, $tags);
		$this->tags()->sync($tags);
	}

	public static function search($input, $tags, $binomials, $authorsArea) {

		if(Session::has('CurrPage') && Session::get('CurrPage')!= 1){ 			
		   Session::forget('CurrPage');
		}else{
		   Session::put('CurrPage',1);
		}
		foreach (['workdate', 'dataCriacao', 'dataUpload'] as $date) {
			if ( isset($input[$date])
					&& DateTime::createFromFormat('Y', $input[$date]) == FALSE ) { 				
					$input[$date] = Date::formatedDate($input[$date]);
			}
		}
		$query = static::query();
		$query->select(DB::raw('photos.*'));
		foreach (['allowCommercialUses', 'allowModifications'] as $license) {
			if ( isset($input[$license]) ) {
				$query->where($license, array_pull($input, $license) );
			}
		}
		if(Input::has('workAuthor_area')){  						
			$input = array_except($input, 'workAuthor_area'); 
		}
		foreach ( $input as $column => $value) { 			
			$query->where('photos.'.$column, 'LIKE', '%' . $value . '%');
		}

		$query->withTagsVarious($tags);
		$query->withBinomials($binomials);			
		$query->withAuthorsVarious($authorsArea);

		$query->groupBy('photos.id');
		$resultSet = $query->get();		
    	return $resultSet;
	}




	public function authorTextFormat($authorName){
		
		if(strpos($authorName, ",")){
			$arrayAuthor = explode(',', $authorName);
			$first = true;
			$authorString = ""; 
			foreach ($arrayAuthor as $t ) {
				if($first == true){
					$authorString.= strtoupper($t).", ";				
				}else{
					$arrayText = explode(' ', $t);
					foreach ($arrayText as $a ) {
						if(strlen($a) > 3){
							$authorString.= ucwords($a)." ";
						}else{
							$authorString.= $a." ";
						}
							
					}
				}
				$first = false;	
			}
			
		}else{
			$authorString = ucwords($authorName);
		}
		return $authorString;		
	}

	public function hasInstitution() {
		return ! is_null ($this->institution_id);
	}


	public function scopeWithTagsName($query, $tag) {
		if(!empty($tag)) { 				
				$query->join('tag_assignments','tag_assignments.photo_id','=','photos.id');
				$query->join('tags','tags.id','=','tag_assignments.tag_id');
				$query->where(function($sub_query) use ($tag) {					
						$sub_query->where('tags.name', '=', $tag); 
				});	
		}
		return $query;
	}

	public function scopeWithAuthorName($query, $author) {
		if(!empty($author)) { 				
				$query->join('photo_author','photo_author.photo_id','=','photos.id');
				$query->join('authors','authors.id','=','photo_author.author_id');
				$query->where(function($sub_query) use ($author) {					
						$sub_query->where('authors.name', '=', $author); 
				});	
		}
		return $query;
	}


	//busca Simples
	public function scopeWithAttributesBuilder($query, $needle) {
		$qq = $query->orWhere('photos.name', 'LIKE', '%'. $needle .'%')
			->orWhere('photos.description', 'LIKE', '%'. $needle .'%')
			->orWhere('photos.imageAuthor', 'LIKE', '%' . $needle . '%')			
			->orWhere('photos.country', 'LIKE', '%'. $needle .'%')
			->orWhere('photos.state', 'LIKE', '%'. $needle .'%')
			->orWhere('photos.city', 'LIKE', '%'. $needle .'%');			
		return $qq;	
	}

	public function scopeWithBinomialsxxx($query, $binomials) {
		foreach($binomials as $binomial => $avg) {
			$query->whereIn('photos.id', function ($sub_query) use ($binomial, $avg) { //id //photos.id 
				$sub_query->select('photo_id')->from('binomial_evaluation')
					->whereRaw('binomial_id = ' . $binomial)
					->groupBy('photo_id')
					->havingRaw('avg(evaluationPosition) >= ' . ($avg - 5))
					->havingRaw('avg(evaluationPosition) <= ' . ($avg + 5));
			});
		}
		return $query;
	}

	public static function search2($needle,$perPage = 24 ) { 

		$query = static::query()->select(DB::raw('photos.*'))
		->withAttributesBuilder($needle)->withTagsName($needle)
		->withAuthorName($needle)
		->groupBy('photos.id')->paginate($perPage); 		
    	
    	return $query;
	}

	public static function searchPhotosField($needle,$perPage = 24 ) { 
		$query = static::query()->select(DB::raw('photos.*'))
		->withAttributesBuilder($needle)
		->orderBy('photos.id')
		->groupBy('photos.id')
		->paginate($perPage); 
		//$resultSet = $query->get(); 	
    	//return $resultSet;
    	return $query;
	}

	public static function searchPhotosWithTags($needle,$perPage = 24 ) { 
		$query = static::query()->select(DB::raw('photos.*'))
		->withTagsName($needle)
		->orderBy('photos.id')
		->groupBy('photos.id')
		->paginate($perPage);
    	return $query;
	}

	public static function searchPhotosWithAuthor($needle,$perPage = 24 ) { 
		$query = static::query()->select(DB::raw('photos.*'))
		->withAuthorName($needle)
		->orderBy('photos.id')
		->groupBy('photos.id')
		->paginate($perPage); 		
    	
    	return $query;
	}




	public function scopePhotosVarious($query, $photos, $q = null) { 
		if(!empty($photos)) { 				
				$query->where(function($sub_query) use ($photos) {
					foreach ($photos as $photo) {				
						$sub_query->orwhere('photos.id', '=', $photo->id);						
					} })->whereMatches($q);	
		}
		return $query;
	}


	public static function paginatePhotosSearch($photos, $perPage = 36,$q = null) { 		
		if($photos!= null){
			$qq = static::PhotosVarious($photos, $q)->orderBy('photos.created_at', 'DESC')->paginate($perPage);			
			return $qq;
		}else{
			return null;
		}			
	}

	
	public static function paginateAllPhotosSearch($photos, $q = null, $perPage = 36) { 		
		return static::PhotosVarious($photos,$q)->orderBy('photos.created_at', 'DESC')->paginate($perPage);	
	}

	public static function paginateAllPhotosSearchAdvance($photos, $q = null, $perPage = 36) { 		
		return static::PhotosVarious($photos,$q)->orderBy('photos.created_at', 'DESC')->paginate($perPage);	
		
	}

	public static function paginatePhotosSearchAdvance($photos, $perPage = 36,$q = null) {		
		if($photos!= null)
			return static::PhotosVarious($photos, $q)->orderBy('photos.created_at', 'DESC')->paginate($perPage);	
		else 
			return null;
					
	}

	public static function fileNamePhoto($photo,$ext)
	{	
		if($photo!= null)
			Photo::where('id',$photo->id)->update(['nome_arquivo' => $photo->id.".".$ext ]); 		
	}
	

}

<?php

use lib\date\Date;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Illuminate\Database\Eloquent\Collection as Collection;

class Photo extends Eloquent {

	use SoftDeletingTrait;

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
		return $this->belongsTo('Institution');
	}

	public function badge()
	{
		return $this->belongsTo('Badge');
	}

	public function tags()
	{
		return $this->belongsToMany('Tag', 'tag_assignments');
	}

	public function comments()
	{
		return $this->hasMany('Comment');
	}

	public function likes()
	{
		return $this->morphMany('Like', 'likable');
	}

	public function albums()
	{
		return $this->belongsToMany('Album', 'album_elements');
	}

	public function evaluations()
	{
		return $this->hasMany('Evaluation');
	}

	public function saveMetadata($originalFileExtension)
	{
		$user = $this->user;
		$exiv2 = Exiv2::getInstance($originalFileExtension, $this, public_path() . '/arquigrafia-images/');
		$exiv2->saveMetadata();
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
		//select user_id,id,dataUpload, created_at, updated_at
		//from photos
		//where user_id=1 order by updated_at desc limit 5;
		//return $id;

		return $dataUpdate = Photo::where("user_id", $user_id)->orderBy('updated_at','desc')->first();
		//return Date::dateDiff(date("Y-m-d H:i:s"),$dataUpdate->updated_at);
		//date("Y-m-d H:i:s")

	}
	public static function getLastUploadPhotoByUser($user_id) {
		return Photo::where("user_id", $user_id)->orderBy('dataUpload','desc')->first();
		//return Date::dateDiff(date("Y-m-d H:i:s"),$dataUpload->dataUpload
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
				//dd($avgPhotosBinomials);
				foreach ($avgPhotosBinomials as $avgPhotoBinomial) {
				//Log::info("Logging iterate avgPhotoBinomial pos ".$avgPhotoBinomial->avgPosition." param ".$avg->avgPosition);
					if(abs($avgPhotoBinomial->avgPosition - $avg->avgPosition)<=5){
						$flag=true;
						//echo $avgPhotoBinomial->photo_id;
						//Log::info("Logging push ".$avgPhotoBinomial->photo_id);
						array_push($arrayPhotosId,$avgPhotoBinomial->photo_id);
					}
				}

				//dd($arrayPhotosId);
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

				$similarPhotosDB = DB::table('photos')
				->select('id', 'name')
				->where('id',$similarPhotosId )
				->get();

				array_push($arrayPhotosDB,$similarPhotosDB[0]);

			}
		}


			return $arrayPhotosDB;
	}

	public function attachBadge($badge) {
		$this->badge_id = $badge->id;
		$this->save();
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
			$q->withTags($needle)->orWhere( function ($q) use($needle) {
				$q->withAttributes($needle);
			});
		});
	}

	public function scopeWithTags($query, $needle) {
		return $query->whereHas('tags', function($q) use($needle) {
			$q->where('name', 'LIKE', '%' . $needle . '%');
		});
	}

	public function scopeWithAttributes($query, $needle) {
		return $query->where('name', 'LIKE', '%'. $needle .'%')
			->orWhere('description', 'LIKE', '%'. $needle .'%')
			->orWhere('imageAuthor', 'LIKE', '%' . $needle . '%')
			->orWhere('workAuthor', 'LIKE', '%'. $needle .'%')
			->orWhere('country', 'LIKE', '%'. $needle .'%')
			->orWhere('state', 'LIKE', '%'. $needle .'%')
			->orWhere('city', 'LIKE', '%'. $needle .'%');
	}

	public function getDataUploadAttribute($value) {
		return $this->date->formatDatePortugues($this->attributes['dataUpload']);
	}

	public function setDataCriacaoAttribute($raw_date) {
		$this->attributes['dataCriacao'] = $this->date->formatDate($raw_date);
	}

	public function setWorkDateAttribute($raw_date) {
		$this->attributes['workdate'] = $this->date->formatDate($raw_date);
	}

	public function getTranslatedDataCriacaoAttribute($raw_date) {
		return $this->date->translate($this->attributes['dataCriacao']);
	}

	public function getTranslatedWorkdateAttribute($raw_date) {
		return $this->date->translate($this->attributes['workdate']);
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

	public static function search($input) {
		foreach (['workdate', 'dataCriacao', 'dataUpload'] as $date) {
			if ( isset($input[$date])
					&& DateTime::createFromFormat('Y', $input[$date]) == FALSE ) {
				$input[$date] = $this->date->formatDate($input[$date]);
			}
		}
		$query = static::query();
		foreach (['allowCommercialUses', 'allowModifications'] as $license) {
			if ( isset($input[$license]) ) {
				$query->where($license, array_pull($input, $license) );
			}
		}
		foreach ( $input as $column => $value) {
			$query->where($column, 'LIKE', '%' . $value . '%');
		}
    return $query->get();
	}
}